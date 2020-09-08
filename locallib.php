<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * A plugin that warns teachers when they need to mark the roll.
 *
 * @package   local_rollcheck
 * @copyright Michael Vangelovski
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/local/rollcheck/lib.php');

function check_roll_for_course() {
    global $OUTPUT, $USER, $DB, $PAGE;

    // Initialise the return structure.
    $data = array (
    	'result' => '',
        'idnumber' => '',
        'classcode' => '',
        'starttime' => '',
        'timetowait' => 0,
        'url' => '',
    );

    // Check whether user is logged in.
    if (!isloggedin()) {
        $data['result'] = 'notloggedin';
        return $data;
    }

    // If user is impersonating another, don't show the popup.
    if (\core\session\manager::is_loggedinas()) {
        $data['result'] = 'impersonating';
        return $data;
    }

    // Check config is valid.
    if (local_rollcheck_config_missing()) {
        $data['result'] = 'missingconfig';
        return $data;
    }

    // Only check between reasonable hours. between 8:00am and 16:59pm.
    $currenthour = date('H');
    if ($currenthour < 8 || $currenthour > 16) {
        $data['result'] = 'outofhours';
        return $data;
    }

    // Get the global config.
    $config = get_config('local_rollcheck');

    // Initialise other variables.
    $timetabledata = null;
    $foundperiod = null;
    $idnumber = null;

    // Ensure that user is a staff member in general before continuing.
    if(isset($USER->profile['CampusRoles'])) {
        $profileroles = explode(',', $USER->profile['CampusRoles']);
        $staffroles = array_map('trim', explode(',', $config->staffroles));
        if (!array_intersect($profileroles, $staffroles)) {
            $data['result'] = 'campusrolesnotstaff';
            return $data;
        }
    }

    // Get the user's timetable.
    $dbconn = moodle_database::get_driver_instance($config->timetable_dbtype, 'native', true);
    $dbconn->connect($config->timetable_dbhost, $config->timetable_dbuser, $config->timetable_dbpass, $config->timetable_dbname, '');
    $sql = $config->timetable_sql;
    $params = array($USER->username);
    $timetabledata = $dbconn->get_records_sql($sql, $params);
    $dbconn->dispose();

    // Attempt to find an in-progress period.
    foreach ($timetabledata as $row) {
        $progress = calc_period_progress(strtotime($row->sorttime), strtotime($row->timetabledatetimeto));
        if ($progress > 0 && $progress < 100) {
            $foundperiod = $row;
            break;
        }
    }
    if (!$foundperiod) {
        $data['result'] = 'inprogressperiodnotfound';
        return $data;
    }

    // Only continue if period is a Senior School subject.
    if ($foundperiod->classcampus != 'SEN') {
        $data['result'] = 'periodnotsenior';
        return $data;
    }

    // Convert Synergetic classcode to Moodle course idnumber.
    $externalDB = moodle_database::get_driver_instance($config->coursemap_dbtype, 'native', true);
    $externalDB->connect($config->coursemap_dbhost, $config->coursemap_dbuser, $config->coursemap_dbpass, $config->coursemap_dbname, '');
    $sql = "SELECT  $config->coursemap_mdlcode
              FROM  $config->coursemap_table
             WHERE  $config->coursemap_extcode = ?";
    $extcode = strtolower($config->coursemap_extcode);
    $mdlcode = strtolower($config->coursemap_mdlcode);
    $params = array($foundperiod->classcode);
    $coursemap = $externalDB->get_record_sql($sql, $params);
    if (!$coursemap) {
        $data['result'] = 'couldnotmapidnumber';
        return $data;
    }
    $idnumber = $coursemap->$mdlcode;

    // Load the course.
    $course = $DB->get_record('course', array('idnumber' => $idnumber));
    if (!$course) {
        $data['result'] = "unknowncourse_$idnumber";
        return $data;
    }

    // Check whether the user is an editor in the course.
    $context = CONTEXT_COURSE::instance($course->id);
    if (!has_capability('moodle/course:update', $context)) {
        $data['result'] = 'capabilityfail';
        return $data;
    }

    // Check whether the user has already responded to a roll check for this course today.
    $sql = "SELECT *
              FROM {local_rollcheck_responses}
             WHERE username = ?
               AND courseidnumber = ?
               AND responsetime > ?";
    $params = array(
        $USER->username,
        $course->idnumber,
        strtotime('today'),
    );
    $records = $DB->get_records_sql($sql, $params);
    $remindwaittime = 0;
    foreach ($records as $row) {
    	if ($row->response == 'markroll' || $row->response == 'dismiss') {
            $data['result'] = 'alreadyresponded' . $row->response;
    		return $data;
    	}
    	if ($row->response == 'remind') {
    		// Get the latest remind me response.
    		$t = round((($row->responsetime + ($config->remindtime*60)) - time()) / 60);
    		if ($t > $remindwaittime) {
    			$remindwaittime = $t;
    		}
    	}
    }

    // If they opted to be reminded later, check whether the wait time has past.
	if ($remindwaittime > 0) {
		$data['result'] = 'remindin' . $remindwaittime;
    	return $data;
	}

    // Check how long to wait before showing alert.
    $timetowait = 0;
    $timesincestart = ((time() - strtotime($foundperiod->sorttime))/60);
    if ($timesincestart < $config->graceperiod) {
        $timetowait = round($config->graceperiod - $timesincestart);
    }

    // If period started at least x mins ago, check whether the roll has been marked.
    if (!$timetowait) {
        // Check whether the roll has been marked.
        $dbconn = moodle_database::get_driver_instance($config->timetable_dbtype, 'native', true);
        $dbconn->connect($config->timetable_dbhost, $config->timetable_dbuser, $config->timetable_dbpass, $config->timetable_dbname, '');
        $sql = "exec [cgs].[local_rollcheck_attendance] ?, ?, ?";
        $params = array($USER->username, $foundperiod->classcode, $foundperiod->sorttime);
        $rolldata = $dbconn->get_records_sql($sql, $params);
        $dbconn->dispose();
        // If roll data exists then the roll has been marked.
        if ($rolldata) {
            $data['result'] = 'rollalreadymarked';
    		return $data;
        }
    }

    $data = array (
    	'result' => 'needsmarking',
        'idnumber' => $course->idnumber,
        'classcode' => $foundperiod->classcode,
        'starttime' => $foundperiod->sorttime,
        'timetowait' => $timetowait,
        'url' => isset($config->rollurl) ? $config->rollurl : '',
    );

    return $data;
}

/*
* Calculated period progress.
*/
function calc_period_progress($start, $end) {
    $currtime =  time();
    $progress = 0;
    if ( $currtime >= $start && $currtime <= $end ) {
        $len = $end - $start;
        $done = $currtime - $start;
        $progress = ( $done / $len ) * 100;
    }
    if ( $currtime >= $end ) {
        $progress = 100;
    }
    return $progress;
}
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

// A convenient time in the page loading process to load the plugin.
function local_rollcheck_extend_navigation(global_navigation $nav) {
    global $COURSE, $PAGE;

    $config = get_config('local_rollcheck');

    if (local_rollcheck_config_missing()) {
        return;
    }

    $data = array(
        'alerttext' => $config->alerttext,
        'graceperiod' => $config->graceperiod,
        'remindtime' => $config->remindtime,
        'rollurl' => $config->rollurl,
    );

    $PAGE->requires->js_call_amd(
        'local_rollcheck/control', 
        'init', [
            'data' => $data,
        ]
    );

    $PAGE->requires->css('/local/rollcheck/styles.css');
}

function local_rollcheck_config_missing() {
    // Get the global config.
    $config = get_config('local_rollcheck');

    $requiredfields = array('coursemap_dbtype', 'coursemap_dbhost', 'coursemap_dbuser', 'coursemap_dbpass', 
        'coursemap_dbname', 'coursemap_table', 'coursemap_extcode', 'coursemap_mdlcode', 
        'timetable_dbtype', 'timetable_dbhost', 'timetable_dbuser', 'timetable_dbpass', 
        'timetable_dbname', 'timetable_sql', 'alerttext', 'graceperiod', 'remindtime', );
    foreach ($requiredfields as $field) {
        if (empty($config->$field)) {
            return true;
        }
    }
    return false;
}

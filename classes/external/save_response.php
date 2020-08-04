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
 * @package     local_rollcheck
 * @copyright   2020 Michael Vangelovski
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_rollcheck\external;

defined('MOODLE_INTERNAL') || die();

use external_function_parameters;

require_once($CFG->libdir . "/externallib.php");

trait save_response {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function save_response_parameters() {
        return new external_function_parameters(
            array(
                'response' => new \external_value(PARAM_TEXT, 'Response'),
                'idnumber' => new \external_value(PARAM_RAW, 'Course idnumber'),
                'classcode' => new \external_value(PARAM_RAW, 'External course class code'),
                'starttime' => new \external_value(PARAM_RAW, 'Period start time string'),
            )
        );
    }

    public static function save_response($response, $idnumber, $classcode, $starttime) {
        global $DB, $USER;

        self::validate_parameters(self::save_response_parameters(), compact('response', 'idnumber', 'classcode', 'starttime'));

        // Insert the response.
        $record = new \stdClass();
        $record->username = $USER->username;
        $record->courseidnumber = $idnumber;
        $record->classcode = $classcode;
        $record->response = $response;
        $record->responsetime = time();
        $id = $DB->insert_record('local_rollcheck_responses', $record);

        return array('result' => $id);
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function save_response_returns() {
        return new \external_single_structure(
            array(
                'result' => new \external_value(PARAM_RAW, 'Id of the saved record.'),
            )
        );
    }

}

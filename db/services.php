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
defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_rollcheck_check_roll' => [
        'classname'   => 'local_rollcheck\external\api',
        'methodname'  => 'check_roll',
        'classpath'   => '',
        'description' => 'Checks whether staff member needs to mark the roll',
        'type'        => 'read',
        'loginrequired' => false,
        'ajax' => true,
    ],
    'local_rollcheck_save_response' => [
        'classname'   => 'local_rollcheck\external\api',
        'methodname'  => 'save_response',
        'classpath'   => '',
        'description' => 'Saves the staff members response to a roll check alert.',
        'type'        => 'write',
        'loginrequired' => true,
        'ajax' => true,
    ],
];


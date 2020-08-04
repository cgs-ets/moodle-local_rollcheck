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
 * @package    	local_rollcheck
 * @copyright 	2020 Michael Vangelovski
 * @license    	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
$string['pluginname'] = 'Roll Check';
$string['rollcheck'] = 'Roll Check';

$string['config:missingsettings'] = 'You are missing setting configurations for the "Roll Check" plugin.';

$string['config:mappingsettingsheaderdb'] = 'Course code mapping database settings';
$string['config:coursemap_dbtype'] = 'Mapping Database driver';
$string['config:coursemap_dbtype_desc'] = 'Database driver name, type of the external database engine.';
$string['config:coursemap_dbhost'] = 'Mapping Database host';
$string['config:coursemap_dbhost_desc'] = 'Type database server IP address or host name. Use a system DSN name if using ODBC. Use a PDO DSN if using PDO.';
$string['config:coursemap_dbname'] = 'Mapping Database name';
$string['config:coursemap_dbuser'] = 'Mapping Database user';
$string['config:coursemap_dbpass'] = 'Mapping Database password';
$string['config:coursemap_table'] = 'Mapping table';
$string['config:coursemap_table_desc'] = 'Name of table that contains a mapping between external system course codes and moodle course idnumbers.';
$string['config:coursemap_mdlcode'] = 'Mapping Moodle code field';
$string['config:coursemap_mdlcode_desc'] = 'Name of field in mapping table that contains the Moodle idnumber.';
$string['config:coursemap_extcode'] = 'Mapping External code field';
$string['config:coursemap_extcode_desc'] = 'Name of field in mapping table that contains the external system course code.';


$string['config:timetablesettingsheaderdb'] = 'Timetable database settings';
$string['config:timetable_dbtype'] = 'Timetable Database driver';
$string['config:timetable_dbtype_desc'] = 'Database driver name, type of the external database engine.';
$string['config:timetable_dbhost'] = 'Timetable Database host';
$string['config:timetable_dbhost_desc'] = 'Type database server IP address or host name. Use a system DSN name if using ODBC. Use a PDO DSN if using PDO.';
$string['config:timetable_dbname'] = 'Timetable Database name';
$string['config:timetable_dbuser'] = 'Timetable Database user';
$string['config:timetable_dbpass'] = 'Timetable Database password';
$string['config:timetable_sql'] = 'Timetable SQL';
$string['config:timetable_sql_desc'] = 'SQL used to retrieve the timetable data. Accepts 1 parameter for username. E.g. `select * from timetable where username = ?`';

$string['config:alerttext'] = 'Alert text';
$string['config:graceperiod'] = 'Grace period (minutes)';
$string['config:remindtime'] = 'Remind time (minutes)';
$string['config:rollurl'] = 'Roll URL';
$string['config:staffroles'] = 'Staff CampusRoles (csv)';
$string['config:staffroles_desc'] = 'Used to determine who is a "Staff" user based on the "CampusRoles" custom profile field.';

$string['markrollnow'] = 'Mark roll now';
$string['remindmein'] = 'Remind me in {$a}min';
$string['dismiss'] = 'Dismiss for this period';

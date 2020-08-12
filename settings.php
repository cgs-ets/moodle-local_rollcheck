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
 * Defines the global settings of the plugin
 *
 * @package   local_rollcheck
 * @copyright 2020 Michael Vangelovski
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $settings = new admin_settingpage('local_rollcheck', get_string('pluginname', 'local_rollcheck'));
    $ADMIN->add('localplugins', $settings);

    // Global Enable/Disable.
    $name = 'local_rollcheck/globaldisable';
    $title = get_string('config:globaldisable', 'local_rollcheck');
    $description = get_string('config:globaldisabledesc', 'local_rollcheck');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $settings->add($setting);

    // DB connection used to map Moodle course idnumbers to external system course codes.
    $settings->add(new admin_setting_heading(
        'local_rollcheck_mappingexdbheader', 
        get_string('config:mappingsettingsheaderdb', 'local_rollcheck'), 
        ''
    ));
    // DB type to use for course mapping lookup.
    $name = 'local_rollcheck/coursemap_dbtype';
    $title = get_string('config:coursemap_dbtype', 'local_rollcheck');
    $description = get_string('config:coursemap_dbtype_desc', 'local_rollcheck');
    $default = '';
    $options = array('', "mysqli", "oci", "pdo", "pgsql", "sqlite3", "sqlsrv");
    $options = array_combine($options, $options);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $options);
    $settings->add($setting);

    // DB host to use for course mapping lookup.
    $name = 'local_rollcheck/coursemap_dbhost';
    $title = get_string('config:coursemap_dbhost', 'local_rollcheck');
    $description = get_string('config:coursemap_dbhost_desc', 'local_rollcheck');
    $default = 'localhost';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $settings->add($setting);

    // DB user to use for course mapping lookup.
    $name = 'local_rollcheck/coursemap_dbuser';
    $title = get_string('config:coursemap_dbuser', 'local_rollcheck');
    $description = '';
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $settings->add($setting);

    // DB pass to use for course mapping lookup.
    $name = 'local_rollcheck/coursemap_dbpass';
    $title = get_string('config:coursemap_dbpass', 'local_rollcheck');
    $description = '';
    $default = '';
    $setting = new admin_setting_configpasswordunmask($name, $title, $description, $default);
    $settings->add($setting);

    // DB name to use for course mapping lookup.
    $name = 'local_rollcheck/coursemap_dbname';
    $title = get_string('config:coursemap_dbname', 'local_rollcheck');
    $description = '';
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $settings->add($setting);

    // Name of table that contains a mapping between external system course codes and moodle course idnumbers.
    $name = 'local_rollcheck/coursemap_table';
    $title = get_string('config:coursemap_table', 'local_rollcheck');
    $default = '';
    $description = get_string('config:coursemap_table_desc', 'local_rollcheck');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $settings->add($setting);

    // Field in mapping table that contains the external system course code. 
    $name = 'local_rollcheck/coursemap_extcode';
    $title = get_string('config:coursemap_extcode', 'local_rollcheck');
    $default = '';
    $description = get_string('config:coursemap_extcode_desc', 'local_rollcheck');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $settings->add($setting);

    // Field in mapping table that contains the Moodle idnumber. 
    $name = 'local_rollcheck/coursemap_mdlcode';
    $title = get_string('config:coursemap_mdlcode', 'local_rollcheck');
    $default = '';
    $description = get_string('config:coursemap_mdlcode_desc', 'local_rollcheck');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $settings->add($setting);


    // DB connection used to get timetable data.
    $settings->add(
        new admin_setting_heading('local_rollcheck_timetableexdbheader', 
        get_string('config:timetablesettingsheaderdb', 'local_rollcheck'), 
        ''
    ));
    // DB type
    $name = 'local_rollcheck/timetable_dbtype';
    $title = get_string('config:timetable_dbtype', 'local_rollcheck');
    $description = get_string('config:timetable_dbtype_desc', 'local_rollcheck');
    $default = '';
    $options = array('', "mysqli", "oci", "pdo", "pgsql", "sqlite3", "sqlsrv");
    $options = array_combine($options, $options);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $options);
    $settings->add($setting);

    // DB host
    $name = 'local_rollcheck/timetable_dbhost';
    $title = get_string('config:timetable_dbhost', 'local_rollcheck');
    $description = get_string('config:timetable_dbhost_desc', 'local_rollcheck');
    $default = 'localhost';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $settings->add($setting);

    // DB user
    $name = 'local_rollcheck/timetable_dbuser';
    $title = get_string('config:timetable_dbuser', 'local_rollcheck');
    $description = '';
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $settings->add($setting);

    // DB pass
    $name = 'local_rollcheck/timetable_dbpass';
    $title = get_string('config:timetable_dbpass', 'local_rollcheck');
    $description = '';
    $default = '';
    $setting = new admin_setting_configpasswordunmask($name, $title, $description, $default);
    $settings->add($setting);

    // DB name
    $name = 'local_rollcheck/timetable_dbname';
    $title = get_string('config:timetable_dbname', 'local_rollcheck');
    $description = '';
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $settings->add($setting);

    // SQL. Accepts 1 param for username.
    $name = 'local_rollcheck/timetable_sql';
    $title = get_string('config:timetable_sql', 'local_rollcheck');
    $default = '';
    $description = get_string('config:timetable_sql_desc', 'local_rollcheck');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $settings->add($setting);

    // Alert text
    $name = 'local_rollcheck/alerttext';
    $title = get_string('config:alerttext', 'local_rollcheck');
    $description = '';
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $settings->add($setting);

    // Grace period
    $name = 'local_rollcheck/graceperiod';
    $title = get_string('config:graceperiod', 'local_rollcheck');
    $description = '';
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $settings->add($setting);

    // Remind me time
    $name = 'local_rollcheck/remindtime';
    $title = get_string('config:remindtime', 'local_rollcheck');
    $description = '';
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $settings->add($setting);

    // Roll url
    $name = 'local_rollcheck/rollurl';
    $title = get_string('config:rollurl', 'local_rollcheck');
    $description = '';
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $settings->add($setting);

    // Staff roles
    $name = 'local_rollcheck/staffroles';
    $title = get_string('config:staffroles', 'local_rollcheck');
    $description = get_string('config:staffroles_desc', 'local_rollcheck');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $settings->add($setting);
    
}

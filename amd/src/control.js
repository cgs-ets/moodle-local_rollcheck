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
 * Provides the local_rollcheck/control module
 *
 * @package   local_rollcheck
 * @category  output
 * @copyright 2020 Michael Vangelovski
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module local_rollcheck/control
 */
 define(['jquery', 'core/log', 'core/ajax', 'core/templates'], 
    function ($, Log, Ajax, Templates) {
    'use strict';

    /**
     * Initializes the ui controls.
     */
    function init(data) {
        Log.debug('local_rollcheck/control: initializing controls of the local_rollcheck ui');

        var control = new RollCheckControl(data);
        control.main();
    };

    // Constructor.
    function RollCheckControl(data) {
        var self = this;
        self.data = data;
        self.templates = {
            rollcheck: 'local_rollcheck/rollcheck',
        };
    };

    RollCheckControl.prototype.main = function () {
        var self = this;

        // Check whether the roll has been marked.
        self.checkRoll();
 
    };

    RollCheckControl.prototype.checkRoll = function() {
        var self = this;

        return Ajax.call([{
            methodname: 'local_rollcheck_check_roll',
            args: {},
            done: function (response) {
                Log.debug("local_rollcheck/control: check result: " + response.result);

                if ( response.result != 'needsmarking') {
                    return;
                }

                if (response.timetowait) {
                    // Set a timeout.
                    Log.debug('local_rollcheck/control: Waiting ' + response.timetowait + ' minutes before checking roll again.');
                    setTimeout(function() {
                        self.checkRoll();
                    }, (response.timetowait*60000));
                    return;
                }
 
                // Set up data and show alert.
                Log.debug('local_rollcheck/control: Roll has not been marked. Setting up alert.');
                self.data['classcode'] = response.classcode;
                self.data['idnumber'] = response.idnumber;
                self.data['starttime'] = response.starttime;
                self.data['timetowait'] = response.timetowait;
                self.data['url'] = response.url;
                self.initPopup();
            },
            fail: function (reason) {
                Log.debug("local_rollcheck/control: Failed to check roll.");
                Log.error(reason);
            }
        }]);
    };

    RollCheckControl.prototype.initPopup = function() {
        var self = this;

        // Set up message text.
        self.data['alerttext'] = self.data['alerttext'].replace("{{classcode}}", self.data['classcode']);

        // Render popup.
        $.when(self.render(self.data)).then(function() {
            // Setup events.
            self.region = $('.local_rollcheck').first();

            // Handle option click.
            self.region.on('click', '.option', function(e) {
                e.preventDefault();
                var option = $(this);
                self.doOption(option);
            });

        });
    };

    RollCheckControl.prototype.render = function(data) {
        var self = this;

        // Load the popup via the template.
        return Templates.render(self.templates.rollcheck, data)
            .done(function(html) {
                $('#region-main-box').append(html);
            })
            .fail(function(reason) {
                Log.debug("local_rollcheck/control: Failed to render rollcheck popup.");
                Log.error(reason);
                return;
            });
    };


    RollCheckControl.prototype.doOption = function(option) {
        var self = this;

        // Check if already submiting.
        if (self.region.hasClass('submitting')) {
            return;
        }
        self.region.addClass('submitting');

        Ajax.call([{
            methodname: 'local_rollcheck_save_response',
            args: {
                response: option.data('option'),
                idnumber: self.data['idnumber'],
                classcode: self.data['classcode'],
                starttime: self.data['starttime'],
            },
            done: function (response) {
                if (response.result) {
                    if (option.data('option') == 'markroll') {
                        if (self.data['url']) {
                            var win = window.open(self.data['url'], '_blank');
                            if (win) {
                                win.focus();
                            } else {
                                Log.error("local_rollcheck/control: Browser has blocked popup.");
                            }
                        }
                    }
                    self.region.fadeOut(400);
                }
            },
            fail: function (reason) {
                self.region.remove();
                Log.debug("local_rollcheck/control: Failed to submit option.");
                Log.error(reason);
            }
        }]);
        
    };

    return {
        init: init
    };
 });
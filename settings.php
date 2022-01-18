<?php
// This file is part of Moodle - https://moodle.org/
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
 * Adds admin settings for retake plugin
 *
 * @package     local_retake
 * @category    admin
 * @copyright   2022 Agung Pratama <prrtmgng@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_category('local_retake_settings', new lang_string('pluginname', 'local_retake')));
    $settingspage = new admin_settingpage('managelocalretake', "Manage Retake Settings");

    if ($ADMIN->fulltree) {
        $yesno = array(0 => get_string('no'), 1 => get_string('yes'));

        // enable on all course
        $settingspage->add(new admin_setting_configselect(
            'local_retake/enableonallcourse',
            "Enable on All Course",
            null,
            0, 
            $yesno
        ));

        // enable on specific course
        $settingspage->add(new admin_setting_configtext(
            'local_retake/enableonspecificcourse',
            'Max Retake',
            'Max retake per year',
            1,
            PARAM_INT
        ));
    }

    $ADMIN->add('localplugins', $settingspage);
}
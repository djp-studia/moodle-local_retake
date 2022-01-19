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

defined('MOODLE_INTERNAL') || die;

/**
 * Fungsi untuk menambahkan navigasi course retake pada course setting pada setiap course
 */
function local_retake_extend_navigation_course($navigation, $course, $context) {
    $resetCapability = has_capability('moodle/course:reset', $context);
    $isEnabled = (new \local_retake\Retake($course->id))->isEnabled();

    if($resetCapability){
        $url = new moodle_url('/local/retake/admin.php', array('id' => $course->id));
        $icon = 'i/settings';
        $label = "Retake Settings";
    } else {
        $url = new moodle_url('/local/retake/index.php', array('id' => $course->id));
        $icon = 'i/reload';
        $label = 'Retake Course';
    }

    if(!$resetCapability && !$isEnabled){
        return;
    } else {
        $navigation->add($label, $url, navigation_node::TYPE_SETTING, null, null, new pix_icon($icon, ''));
    }
}

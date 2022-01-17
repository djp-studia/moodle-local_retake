<?php

defined('MOODLE_INTERNAL') || die;

/**
 * Fungsi untuk menambahkan navigasi course retake pada course setting pada setiap course
 */
function local_retake_extend_navigation_course($navigation, $course, $context) {
    $url = new moodle_url('/local/retake/index.php', array('id'=>$course->id));
    $navigation->add('Retake Course', $url, navigation_node::TYPE_SETTING, null, null, new pix_icon('i/reload', ''));
}

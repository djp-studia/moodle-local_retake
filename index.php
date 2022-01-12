<?php

require(__DIR__ . '/../../config.php');

// Get course
$courseid = required_param('course', PARAM_INT);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id);
$url = new moodle_url('/local/retake/index.php', array('course'=>$course->id));
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Retake Course');
$PAGE->set_heading("Retake Course $course->fullname");

echo $OUTPUT->header();

echo "This is plugin content";

echo $OUTPUT->footer();

<?php

require(__DIR__ . '/../../config.php');

// cek apakah user sudah login
require_login();

// cek parameter course id pada URL
$courseid = required_param('id', PARAM_INT);

// get data course berdasarkan parameter course id dan user id
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id);
$url = new moodle_url('/local/retake/index.php', array('id'=>$course->id));
$courseUrl = new moodle_url('/course/view.php', array('id' => $courseid));

// set page content
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Retake Course');
$PAGE->set_heading("Retake Course $course->fullname");

// jika request method POST, lakukan penghapusan di sini, lalu redirect ke halaman course

if(isset($_POST['confirm_retake'])){
    require_once($CFG->dirroot. '/course/lib.php');

    \local_retake\Cleanser::cleanActivityCompletion($courseid, $USER->id);
    \local_retake\Cleanser::cleanScormData($courseid, $USER->id);
    \local_retake\Cleanser::cleanH5PActivityAndAttempts($courseid, $USER->id);
    \local_retake\Cleanser::removeUserEnrollment($courseid, $USER->id);

    // remove course completion cache
    \local_retake\Cleanser::removeCompletionCache($courseid, $USER->id);

    // redirect ke course
    header("Location: $courseUrl");
}


// display content
echo $OUTPUT->header();

// prepare confirmation message
$confirmMessage = $OUTPUT->render_from_template(
    'local_retake/message', array(
        "warning" => get_string('warning', 'local_retake'),
        "warning_detail" => get_string('warning_detail', 'local_retake'),
        "confirmation" => get_string('confirmation', 'local_retake')
    )
);
$yesUrl = new moodle_url('/local/retake/index.php', ['id' => $courseid, 'confirm_retake' => 'true']);
$noUrl = new moodle_url('/course/view.php', ['id' => $courseid]);

echo $OUTPUT->confirm($confirmMessage, $yesUrl, $noUrl);
echo $OUTPUT->footer();

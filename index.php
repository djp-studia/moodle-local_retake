<?php

require(__DIR__ . '/../../config.php');

// cek apakah user sudah login
require_login();

// cek parameter course id pada URL
$courseid = required_param('course', PARAM_INT);

// get data course berdasarkan parameter course id dan user id
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id);
$url = new moodle_url('/local/retake/index.php', array('course'=>$course->id));
$courseUrl = new moodle_url('/course/view.php', array('id' => $courseid));

// set page content
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Retake Course');
$PAGE->set_heading("Retake Course $course->fullname");

// jika request method POST, lakukan penghapusan di sini, lalu redirect ke halaman course

if(isset($_POST['confirm_retake'])){
    require_once($CFG->dirroot. '/course/lib.php');

    \local_retake\Cleanser::cleanActivityCompletion($courseid, $USER->id);

    // rebuild course cache untuk update tampilan di user
    rebuild_course_cache($courseid);

    // redirect ke course
    header("Location: $courseUrl");
}


// display content
echo $OUTPUT->header();
?>

<h3>Retake Confirmation</h3>

<div class="alert alert-danger"><strong>Proses ini tidak bisa dibatalkan</strong>. Proses retake course akan menghapus seluruh data course pegawai seperti progress, nilai, badge, dan jam pelajaran (tahun berjalan).</div>

<p>Apakah Anda yakin untuk mengulang course?</p>

<form action="" method="POST">
    <input type="hidden" name="confirm_retake">
    <a href="<?= new moodle_url('/course/view.php', array('id' => $courseid)) ?>" class="btn btn-secondary">Batal</a>
    <button type="submit" class="btn btn-primary">Ya, Retake Course</button>
</form>

<?php

echo $OUTPUT->footer();

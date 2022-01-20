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

require(__DIR__ . '/../../config.php');
require_once('forms.php');

// cek apakah user sudah login
require_login();

// cek parameter course id pada URL
$courseid = required_param('id', PARAM_INT);
$userid = required_param('user', PARAM_INT);

// retake object
$retake = new \local_retake\Retake($courseid);

// get data course dan user berdasarkan parameter course id dan user id
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$user = $DB->get_record('user', array('id' => $userid), 'firstname', MUST_EXIST);
$context = context_course::instance($course->id);
$adminUrl = new moodle_url('/local/retake/admin.php', array('id'=>$course->id));
$url = new moodle_url('/local/retake/reset.php', array('id'=> $course->id, 'user' => $userid));
$courseUrl = new moodle_url('/course/view.php', array('id' => $courseid));

// required course reset capability
require_capability('moodle/course:reset', $context);

// if method post, lakukan reset di sini
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $retake->deleteRetakeHistoryByUser($userid);

    \core\notification::success("Reset user data success.");
    
    redirect($adminUrl);
}

// set page content
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_pagelayout('admin');
$PAGE->set_title('Retake Course');
$PAGE->set_heading("Retake Course $course->fullname");

// display content
echo $OUTPUT->header();

// set reset confirmation
echo $OUTPUT->confirm("Apakah Anda yakin untuk menghapus riwayat reset course user <strong>$user->firstname</strong>?", $url, $adminUrl);

echo $OUTPUT->footer();
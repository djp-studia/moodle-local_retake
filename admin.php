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

// retake object
$retake = new \local_retake\Retake($courseid);

// get data course berdasarkan parameter course id dan user id
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id);
$url = new moodle_url('/local/retake/admin.php', array('id'=>$course->id));
$courseUrl = new moodle_url('/course/view.php', array('id' => $courseid));

// required course reset capability
require_capability('moodle/course:reset', $context);

// set page content
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_pagelayout('admin');
$PAGE->set_title('Retake Course');
$PAGE->set_heading("Retake Course $course->fullname");

// generate settings form
$mform = new simplehtml_form($url);

// set current value from database
$mform->set_data(array('enable_retake' => $retake->isEnabled()));

// if form is executed
if($formData = $mform->get_data()){
    if($formData->enable_retake == 1){
        $retake->enable($USER->id);
        $label = "Enable";
    } else {
        $retake->disable();
        $label = "Disable";
    }

    \core\notification::success("<strong>$label</strong> retake success");

    redirect($url);
}

// display content
echo $OUTPUT->header();

echo '<h3>Retake Settings</h3>';

$mform->display();

echo '<h3>Retake History</h3>';

// show retake history
?>

<table class="generaltable flexible boxaligncenter">
    <tr>
        <th>Username</th>
        <th>Name</th>
        <th>Total Retake</th>
        <th>Reset</th>
    </tr>
    <?php
        foreach($retake->getHistory() as $row){
            $reset_url = new moodle_url('/local/retake/reset.php', array('user' => $row->user));
            echo "<tr>
                    <td>$row->username</td>
                    <td>$row->firstname</td>
                    <td>$row->total</td>
                    <td><a href='$reset_url' class='btn btn-danger'>Reset</a></td>
                </tr>";
        }
    ?>
</table>

<?php

echo $OUTPUT->footer();
<?php

defined("MOODLE_INTERNAL") || die();

require_once("$CFG->libdir/formslib.php");

class simplehtml_form extends moodleform {
    public function definition() {
        global $CFG;
       
        $mform = $this->_form;
        $mform->addElement('select', 'enable_retake', 'Enable Retake', ['No', 'Yes'], null);
        $this->add_action_buttons(false);
    }
}
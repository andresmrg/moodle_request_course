<?php

require_once("{$CFG->libdir}/formslib.php");
global $CGG;

class managelinks_form extends moodleform {

    function definition() {
        $mform = & $this->_form;
        $options = array();        
        $mform->addElement('html', '<a href="createcourse.php">Add a new course</a></br></br>');
    }

}
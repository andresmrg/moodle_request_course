<?php

require_once("{$CFG->libdir}/formslib.php");

class filter_form extends moodleform {

    function definition() {

        $mform = & $this->_form;

		// add course name
		$mform->addElement('text', 'filter_code', get_string('field_filtercode', 'block_ps_selfstudy'));
        $mform->setType('filter_code', PARAM_NOTAGS);

        $buttonarray=array();
        $buttonarray[] =& $mform->createElement('submit', 'submit_button', get_string('submitbutton','block_ps_selfstudy'));
        $buttonarray[] =& $mform->createElement('cancel', 'cancel_button', get_string('resetbutton','block_ps_selfstudy'));
        $mform->addGroup($buttonarray, 'buttonarray', '', '', false);
      
    }

}

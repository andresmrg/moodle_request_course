<?php

require_once("{$CFG->libdir}/formslib.php");

class requestcourse_form extends moodleform {

    function definition() {

        global $USER, $CFG, $DB;

        if(!isset($_GET['id'])) {
            //$homeurl = new moodle_url($CFG->wwwroot);
            //redirect($homeurl);
        } else {
            $courseid = $_GET['id']; 
        }

        $mform = & $this->_form;

        $course = $DB->get_record('block_ps_selfstudy_course', array ('id'=>@$courseid), $fields='*', $strictness=IGNORE_MISSING);
        $mform->addElement('html','<p>You are requesting a copy of the course <strong>'.@$course->course_name.'</strong></p>');
        
        // group user profile fields
        $mform->addElement('header', 'displayinfo', get_string('group_userfields', 'block_ps_selfstudy'));

        //pass id
        @$mform->addElement('hidden', 'courseid');
        @$mform->setType('courseid', PARAM_NOTAGS);
        @$mform->setDefault('courseid', $courseid);

		//load all users profile fields
        $mform->addElement('text', 'firstname', get_string('firstname', 'block_ps_selfstudy'));
        $mform->setType('firstname', PARAM_NOTAGS);
        $mform->addRule('firstname', null, 'required', null, 'client');
        $mform->setDefault('firstname', $USER->firstname); 

        
        $mform->addElement('text', 'lastname', get_string('lastname', 'block_ps_selfstudy'));
        $mform->setType('lastname', PARAM_NOTAGS);
        $mform->addRule('lastname', null, 'required', null, 'client');
        $mform->setDefault('lastname', $USER->lastname);  

        
        $mform->addElement('text', 'email', get_string("email", "block_ps_selfstudy"));
        $mform->setType('email', PARAM_EMAIL);
        $mform->addRule('email', null, 'required', null, 'client');
        $mform->addRule('email', null, 'email', null, 'client');
        $mform->setDefault('email', $USER->email);  

        // group shipping information
        $mform->addElement('header', 'shipping', get_string('group_shipping', 'block_ps_selfstudy'));

        $mform->addElement('text', 'address', get_string('address', 'block_ps_selfstudy'));
        $mform->setType('address', PARAM_NOTAGS);
        $mform->addRule('address', null, 'required', null, 'client');
        $mform->setDefault('address', $USER->address);

        //get address2
        $address2_id = $DB->get_record('user_info_field', array ('shortname'=>'address2'), $fields='id', $strictness=IGNORE_MISSING);    
        if(!empty($address2_id)) {
            $address2 = $DB->get_record('user_info_data', array ('userid'=>$USER->id,'fieldid'=>$address2_id->id), $fields='data', $strictness=IGNORE_MISSING);
            $mform->addElement('text', 'address2', get_string('address2', 'block_ps_selfstudy'));
            $mform->setType('address2', PARAM_NOTAGS);

            if(!empty($address2)) {
                $mform->setDefault('address2', $address2->data);
            }
        }
        
        $mform->addElement('text', 'city', get_string('city', 'block_ps_selfstudy'));
        $mform->setType('city', PARAM_NOTAGS);
        $mform->addRule('city', null, 'required', null, 'client');
        $mform->setDefault('city', $USER->city);

        //get state
        $state_id = $DB->get_record('user_info_field', array ('shortname'=>'state'), $fields='id', $strictness=IGNORE_MISSING);    
        if(!empty($state_id)) {
            $state = $DB->get_record('user_info_data', array ('userid'=>$USER->id,'fieldid'=>$state_id->id), $fields='data', $strictness=IGNORE_MISSING);
            $mform->addElement('text', 'state', get_string('state', 'block_ps_selfstudy'));
            $mform->setType('state', PARAM_NOTAGS);
            $mform->addRule('state', null, 'required',null,'client');
            if(!empty($state)) {
                $mform->setDefault('state', $state->data);
            }
        }
        
        //get zipcode
        $zip_id = $DB->get_record('user_info_field', array ('shortname'=>'zipcode'), $fields='id', $strictness=IGNORE_MISSING); 
        if(!empty($zip_id)) {
            $zipcode = $DB->get_record('user_info_data', array ('userid'=>$USER->id,'fieldid'=>$zip_id->id), $fields='data', $strictness=IGNORE_MISSING);
            $mform->addElement('text', 'zipcode', get_string('zipcode', 'block_ps_selfstudy'),'maxlength="7" minlength="5"');
            $mform->setType('zipcode', PARAM_NOTAGS);
            $mform->addRule('zipcode', null, 'required',null,'client');

            if(!empty($zipcode)) {
                $mform->setDefault('zipcode', $zipcode->data);
            }
        }

        $choices = get_string_manager()->get_list_of_countries();
        $choices = array('' => get_string('selectacountry') . '...') + $choices;
        $mform->addElement('select', 'country', get_string('country','block_ps_selfstudy'), $choices);
        if (!empty($CFG->country)) {
            $mform->setDefault('country', $CFG->country);
        } 
        if(!empty($USER->country)) {
            $mform->setDefault('country', $USER->country);
        }

    
        $mform->addElement('text', 'phone1', get_string('phone1', 'block_ps_selfstudy'),'maxlength="20" minlength="5"');
        $mform->setType('phone1', PARAM_NOTAGS);
        $mform->addRule('phone1', null, 'required',null,'client');
        $mform->setDefault('phone1', $USER->phone1);
        
        $this->add_action_buttons(true, get_string('submitbutton', 'block_ps_selfstudy'));
    }
}

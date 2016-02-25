<?php

require_once("{$CFG->libdir}/formslib.php");

class editcourse_form extends moodleform {

    function definition() {
        global $DB;

        if(isset($_GET['id'])) {
            $id = $_GET['id'];    
        }
        if(isset($_GET['code'])) {
            $code = $_GET['code'];
        }
        if(isset($_GET['platform'])) {
            $platform = $_GET['platform'];
        }
        if(isset($_GET['name'])) {
            $name = $_GET['name'];
        }
        if(isset($_GET['desc'])) {
            $description = base64_decode($_GET['desc']);            
        }
        if(isset($_GET['hours'])) {
            $hours = $_GET['hours'];
        }
        if(isset($_GET['link'])) {
            $link = base64_decode($_GET['link']);
        }
        if(isset($_GET['type'])) {
            $type = $_GET['type'];
        }
        if(isset($_GET['status'])) {
            $status = $_GET['status'];
        }

        $mform = & $this->_form;
        
        // add group for text areas
        //$mform->addElement('header', 'displayinfo', get_string('text_coursename', 'block_ps_selfstudy'));

        //pass id
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_NOTAGS);
        $mform->setDefault('id', $id);

		// load existing course code
        $mform->addElement('text', 'course_code', get_string('field_coursecode', 'block_ps_selfstudy'));
        $mform->setType('course_code', PARAM_NOTAGS);
        $mform->addRule('course_code', null, 'required', null, 'client');
        $mform->setDefault('course_code', $code); 

        // load existing course code
        $desc_link = $DB->get_record('block_ps_selfstudy_course',array('id'=>$id),$fields="description_link");

        $mform->addElement('text', 'description_link', get_string('field_description_link', 'block_ps_selfstudy'));
        $mform->setType('description_link', PARAM_NOTAGS);
        if(!empty($desc_link)) {
            $mform->setDefault('description_link', $desc_link->description_link); 
        }
        
        $mform->addElement('text', 'course_platform', get_string('field_platform', 'block_ps_selfstudy'));
        $mform->setType('course_platform', PARAM_NOTAGS);
        $mform->addRule('course_platform', null, 'required', null, 'client');
        $mform->setDefault('course_platform', $platform); 

        // load existing course name
        $mform->addElement('text', 'course_name', get_string('field_coursename', 'block_ps_selfstudy'));
        $mform->setType('course_name', PARAM_NOTAGS);
        $mform->addRule('course_name', null, 'required', null, 'client');
        $mform->setDefault('course_name', $name);  

        //textarea
        $mform->addElement('textarea', 'course_description', get_string("field_description", "block_ps_selfstudy"), 'wrap="virtual" rows="15" cols="50"');
        $mform->setType('course_description', PARAM_NOTAGS);
        $mform->addRule('course_description', null, 'required', null, 'client');
        $mform->setDefault('course_description', $description);  

        $mform->addElement('text', 'course_hours', get_string('field_hours', 'block_ps_selfstudy'));
        $mform->setType('course_hours', PARAM_NOTAGS);
        $mform->addRule('course_hours', null, 'required', null, 'client');
        $mform->setDefault('course_hours', $hours);  

        //checkbox for link courses, if it is not checked, it is like if it didn't exist
        $mform->addElement('advcheckbox', 'course_type', get_string('field_checkbox', 'block_ps_selfstudy'), 'Select to create a self-study course with link.', array('group' => 1), array(0, 1));
        $mform->setDefault('course_type', $type); 

        $mform->disabledIf('course_link', 'course_type');
        $mform->addElement('text', 'course_link', get_string('field_link', 'block_ps_selfstudy'));
        $mform->setType('course_link', PARAM_NOTAGS);
        if($link != '0') {
            $mform->setDefault('course_link', $link);  
        }
        
        //checkbox for link courses, if it is not checked, it is like if it didn't exist
        $mform->addElement('advcheckbox', 'course_status', get_string('field_checkbox_hide', 'block_ps_selfstudy'), 'Select to hide the course by default.', array('group' => 2), array(0, 1));
        $mform->setDefault('course_status', $status);
        
        $this->add_action_buttons();
    }

}

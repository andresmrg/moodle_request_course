<?php

class block_request_course extends block_list {

	public function init() {
		$this->title = get_string('pluginname','block_request_course');
	}

	public function get_content() {
		
		global $DB, $OUTPUT;

		if ($this->content !== null) {
	      return $this->content;
	    }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();

        //introduction
        $this->content->items[] = get_string('intro', 'block_request_course');
        
        //get courses only copies type
        //$courses = $DB->get_records('block_request_course_course', array ('course_status'=>'0'), $sort='', $fields='course_code,course_name', $limitfrom=0, $limitnum=0);
        //foreach($courses as $key => $value) {
        	//build the list of courses
        	//$long = "$value->course_code - $value->course_name";
        	//$str = substr("$value->course_code - $value->course_name", 0, 55);
        	//if (strlen($str) > 50)
   			//$str = substr($str, 0, 50) . '...';

        	//$this->content->items[] = '<div><a href="#">'.$str.'</a></div>';
        	//$this->content->items[] = '<div><a href="#">'.$str.'</a></div>';
        //}
        $url1 = new moodle_url('/blocks/request_course/allcourses.php');
        $this->content->footer = html_writer::link($url1, get_string('viewall', 'block_request_course'));

	    return $this->content;
	}   // Here's the closing bracket for the class definition

	public function applicable_formats() {
	  return array(
	           'site-index' => true,
	          'course-view' => true, 
	   'course-view-social' => true,
	                  'mod' => true, 
	             'mod-quiz' => true
	  );
	}

	public function instance_allow_multiple() {
    return false;
    }

    //Allow configurations
    function has_config() {
        return false;
    }

}
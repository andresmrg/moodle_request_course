<?php

class block_ps_selfstudy extends block_list {

	public function init() {
		$this->title = get_string('selfstudy','block_ps_selfstudy');
	}

	public function get_content() {

		global $USER;

	    if ($this->content !== null) {
	      return $this->content;
	    }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        
        $url1 = new moodle_url('/blocks/ps_selfstudy/managecourses.php');
        $url2 = new moodle_url('/blocks/ps_selfstudy/viewrequests.php');
        $url3 = new moodle_url('/blocks/ps_selfstudy/myrequests.php');
        $url4 = new moodle_url('/blocks/ps_selfstudy/viewcompletion.php');

        $context = context_system::instance();
        if (has_capability('block/ps_selfstudy:managecourses', $context, $USER->id))
        $this->content->items[] = html_writer::link($url1, get_string('link_managecourses', 'block_ps_selfstudy'));

    	if (has_capability('block/ps_selfstudy:viewrequests', $context, $USER->id)) 
        $this->content->items[] = html_writer::link($url2, get_string('link_requests', 'block_ps_selfstudy'));

    	if (has_capability('block/ps_selfstudy:viewrequests', $context, $USER->id)) 
        $this->content->items[] = html_writer::link($url4, get_string('link_completion', 'block_ps_selfstudy'));

    	if (has_capability('block/ps_selfstudy:myrequests', $context, $USER->id)) 
        $this->content->items[] = html_writer::link($url3, get_string('link_myrequests', 'block_ps_selfstudy'));
	 
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
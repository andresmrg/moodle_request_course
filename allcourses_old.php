<?php

require_once('../../config.php');
//require_once('course_form.php');
//require_once('allcourses_form.php');

global $DB, $OUTPUT, $PAGE;

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/blocks/request_course/allcourses.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Available Courses');
$PAGE->set_heading('Available Courses');

/*
1. get records from db
2. display records in the box, title, description and a button

*/

//get courses only copies type
$courses = $DB->get_records('block_ps_selfstudy_course', array ('course_status'=>'0'), $sort='', $fields='*', $limitfrom=0, $limitnum=0);

$site = get_site();
echo $OUTPUT->header();
foreach($courses as $key => $value) {

	if($value->course_type == 0) {
		$requesturl = $CFG->wwwroot."/blocks/ps_selfstudy/requestcourse.php?id=".$value->id;
	} else {
		$requesturl = "#";
	}

	echo $OUTPUT->box('
		<div style="border:solid 1px #CCC; padding: 20px; margin-bottom: 10px;">
			<strong>Course Title:</strong> '.$value->course_name.'<br><br>
			<p>'.$value->course_description.'</p>
			<a class="ps_request_button" href="'.$requesturl.'">Request Course</a>
		</div>');
}
echo $OUTPUT->footer();



// form didn't validate or this is the first display
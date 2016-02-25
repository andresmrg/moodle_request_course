<?php
/**
 * Simple file test_custom.php to drop into root of Moodle installation.
 * This is an example of using a sql_table class to format data.
 */
require "../../config.php";
require "$CFG->libdir/tablelib.php";

require_login();
if (isguestuser()) {
    print_error('guestsarenotallowed');
}

global $OUTPUT, $PAGE, $DB, $USER;

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/blocks/ps_selfstudy/myrequests.php');
$PAGE->set_pagelayout('standard');

/**** TABLE LIST OF PHYSICAL REQUESTS ****/
$table = new html_table();
$table->head = array('Course Code','Course Title','Request date','Status','Completion');
$table->data = array();
/**** TABLE LINK TYPE REQUESTS ****/
$table_link = new html_table();
$table_link->head = array('Course Code','Course Title','Link','Request date','Completion');
$table_link->data = array();
/**** TABLE HISTORY ****/
$table_history = new html_table();
$table_history->head = array('Course Code','Course Title','Completion date','Status');
$table_history->data = array();


//get all data from _request table
$request = $DB->get_records('block_ps_selfstudy_request', array('student_id'=>$USER->id), $sort='', $fields='*', $limitfrom=0, $limitnum=0);

//loop the request records to form the requests list and the link list
foreach($request as $value) {
	//get course data
	$course = $DB->get_record('block_ps_selfstudy_course', array('id'=>$value->course_id), $fields='id,course_name,course_code,course_link,course_type');
	
	//format requested date from timestamp
	$timestamp = $value->request_date;
	$date = date("m/d/Y",$timestamp);

	//get status to be strings
	//if status is pending, then doesn't show the link for completion, if not, display shipped and show the completion link
	if($value->request_status == 0) {
		$status = "Pending";
		$completion = '';
	} else {
		$completion = '<a href="success.php?rid='.$value->id.'">Complete</a>';
		$status = "Shipped";
	}

	//if it is course link type, add to second table, if not, add it to the request table
	if($course->course_type == 1) {
		//if the course was completed => skip it.
		if($DB->record_exists('block_ps_selfstudy_complete', array('request_id'=>$value->id))) {
			continue;
		} else {
			//Display blank if doesn't have a link
			$link = $course->course_link;
			if($link == '0')
				$link = "";
			//$link = '<a href="'.$link.'" target="_blank">'.$link.'</a>';
			//create completion button and table
			$completion = '<a href="success.php?rid='.$value->id.'">Complete</a>';
			$row1 = array($course->course_code,$course->course_name,$link,$date,$completion);
    		$table_link->data[] = $row1;
		}
	} else {
		//if the course was completed => skip it.
		if($DB->record_exists('block_ps_selfstudy_complete', array('request_id'=>$value->id))) {
			continue;
		} else {
			$row = array($course->course_code,$course->course_name,$date,$status,$completion);
    		$table->data[] = $row;
		}
	}
			
}

foreach($request as $value) {
//get all data from _complete table
	$completionlist = $DB->get_records('block_ps_selfstudy_complete', array('request_id'=>$value->id), $sort='', $fields='*', $limitfrom=0, $limitnum=0);
		//loop the request table_history
	foreach($completionlist as $values) {
		$courseid = $DB->get_record('block_ps_selfstudy_request', array('id'=>$values->request_id), $fields='course_id');
		$course = $DB->get_record('block_ps_selfstudy_course', array('id'=>$courseid->course_id), $fields='course_name,course_code');
		//format requested date from timestamp
		$timestamp = $values->completion_date;
		$date = date("m/d/Y",$timestamp);
		$status = ucfirst($values->completion_status);

		//add the cells to the request table
		$row = array($course->course_code,$course->course_name,$date,$status);
	    $table_history->data[] = $row;		
	}
}
// Define headers
$PAGE->set_title(get_string('myrequests','block_ps_selfstudy'));
$PAGE->set_heading(get_string('myrequests','block_ps_selfstudy'));

$site = get_site();
echo $OUTPUT->header(); //output header
if(isset($_GET['success'])) {
	$success = $_GET['success'];
	if($success == 'yes') {
		echo '<div class="alert alert-success">'.get_string('ordersubmitted','block_ps_selfstudy').'</div>';	
	} else {
		echo '<div class="alert alert-success">'.get_string('completecourse','block_ps_selfstudy').'</div>';	 
	}	
}
if (has_capability('block/ps_selfstudy:myrequests', $context, $USER->id)) {
	if($table->data) {
	echo get_string('tablerequest','block_ps_selfstudy');
	echo html_writer::table($table);
	} 
	if($table_link->data) {
	echo get_string('tablelink','block_ps_selfstudy');
	echo html_writer::table($table_link);	
	}
	if($table_history->data) {
	echo get_string('tablehistory','block_ps_selfstudy');
	echo html_writer::table($table_history);	
	}
	if(!$table->data && !$table_link->data && !$table_history->data) {
		echo get_string('nopendingrequests','block_ps_selfstudy');
	}
} else {
	print_error('nopermissiontoviewpage', 'error', '');
}
echo $OUTPUT->footer();
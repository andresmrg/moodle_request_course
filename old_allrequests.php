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

global $OUTPUT, $PAGE, $DB;

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/blocks/ps_selfstudy/viewrequests.php');
$PAGE->set_pagelayout('standard');

//create table to hold the data
$table = new html_table();
$table->head = array('Course Code','Title','Name','Email Address', 'Address','Phone #','Request date','Status','Action');
$table->data = array();

//get all data from requesttable
$request = $DB->get_records('block_ps_selfstudy_request', null, $sort='', $fields='*', $limitfrom=0, $limitnum=0);

//loop the request table
foreach($request as $value) {
	$user = $DB->get_record('user', array('id'=>$value->student_id), $fields='firstname,lastname,email,address,department,country,phone1');
	$course = $DB->get_record('block_ps_selfstudy_course', array('id'=>$value->course_id), $fields='course_name,course_code');

	//get firstname and lastname together
	$fullname = "$user->firstname $user->lastname";
	//format requested date from timestamp
	$timestamp = $value->request_date;
	$date = date("m/d/Y",$timestamp);

	if($value->request_status == 0) {
		$status = "Pending";
		$links = '<a href="success.php?id='.$value->id.'&status=shipped">Delivered</a> - <a href="deleterequest.php?id='.$value->id.'">Delete</a>';
	} else if($value->request_status == 1) {
		$status = "Shipped";
		$links = '<a href="deleterequest.php?id='.$value->id.'">Delete</a>';
	} else {
		continue;
	}
	//get zipcode
    $zip_id = $DB->get_record('user_info_field', array ('shortname'=>'zipcode'), $fields='id', $strictness=IGNORE_MISSING);        
    $zipcode = $DB->get_record('user_info_data', array ('userid'=>$value->student_id,'fieldid'=>$zip_id->id), $fields='data', $strictness=IGNORE_MISSING);
	$fulladdress = "$user->address - $user->department - $zipcode->data - $user->country";
	//add the cells to the request table
	$row = array($course->course_code,$course->course_name,$fullname,$user->email,$fulladdress,$user->phone1,$date,$status,$links);
    $table->data[] = $row;		
}

// Define headers
$PAGE->set_title('View All Requests');
$PAGE->set_heading('View All Requests');
//$PAGE->navbar->add('View All Requests', new moodle_url('/blocks/ps_selfstudy/viewrequests.php'));

$site = get_site();
echo $OUTPUT->header(); //output header
if (has_capability('block/ps_selfstudy:viewrequests', $context, $USER->id)) {
	echo html_writer::table($table);
	echo '<a href="viewrequests.php">Click here to view only pending requests</a>';
} else {
	print_error('nopermissiontoviewpage', 'error', '');
}
echo $OUTPUT->footer();
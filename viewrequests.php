<?php
/**
 * Simple file test_custom.php to drop into root of Moodle installation.
 * This is an example of using a sql_table class to format data.
 */
require "../../config.php";
require "$CFG->libdir/tablelib.php";
require_once('filter_form.php');
require "viewrequests_table.php";
global $OUTPUT, $PAGE;

require_login();
if (isguestuser()) {
	print_error('guestsarenotallowed');
}

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/blocks/request_course/viewrequests.php');
$PAGE->set_pagelayout('standard');
$filterform = new filter_form();

$download = optional_param('download', '', PARAM_ALPHA);

$table = new viewrequests_table('uniqueid');
$table->is_downloading($download, 'view_requests', 'Requests');

if($filterform->is_cancelled()) {

	$courseurl = new moodle_url('/blocks/request_course/viewrequests.php');
  	redirect($courseurl);

} else if ($fromform = $filterform->get_data()) {

	$sqlconditions = "course_code = '".$fromform->filter_code."' AND ";
	//WHEN FORM IS SUBMITTED
	if (!$table->is_downloading()) {
	//Define headers
		$PAGE->set_title(get_string('title_viewrequests','block_request_course'));
		$PAGE->set_heading(get_string('title_viewrequests','block_request_course'));
		$site = get_site();
		echo $OUTPUT->header(); //output header
		$filterform->display();
		echo "<hr>";
	}

	if (has_capability('block/request_course:viewrequests', $context, $USER->id)) {



		//sql to get all requests
		$fields = 'r.id,c.course_code,c.course_name,u.firstname,u.lastname,u.email,u.address,u.country,u.city,u.phone1,r.student_id,r.course_id,r.request_date,r.request_status';
		$from = "{block_request_course_request} as r JOIN {course} c ON (c.id=r.course_id) JOIN {user} u ON(u.id=r.student_id)";
		$sqlconditions .= 'r.request_status = 0';
		$table->define_baseurl("$CFG->wwwroot/blocks/request_course/viewrequests.php");
		$link = '<br><a href="viewallrequests.php">'.get_string('clickfulllist','block_request_course').'</a>';
		$table->set_sql($fields, $from, $sqlconditions);
		$table->out(30, true); //print table
		if (!$table->is_downloading()) {
			echo $link;
		}
	} else {
		print_error('nopermissiontoviewpage', 'error', '');
	}
	if (!$table->is_downloading()) {
		echo $OUTPUT->footer();
	}

} else {

	//FIRST TIME
	if (!$table->is_downloading()) {
	//Define headers
		$PAGE->set_title(get_string('title_viewrequests','block_request_course'));
		$PAGE->set_heading(get_string('title_viewrequests','block_request_course'));
		$site = get_site();
		echo $OUTPUT->header(); //output header
		$filterform->display();
		echo "<hr>";
	}

	if (has_capability('block/request_course:viewrequests', $context, $USER->id)) {

		//sql to get all requests
		$fields = 'r.id,c.course_code,c.course_name,u.firstname,u.lastname,u.email,u.address,u.country,u.city,u.phone1,r.student_id,r.course_id,r.request_date,r.request_status';
		$from = "{block_request_course_request} as r JOIN {course} c ON (c.id=r.course_id) JOIN {user} u ON(u.id=r.student_id)";
		$sqlconditions = 'r.request_status = 0';
		$table->define_baseurl("$CFG->wwwroot/blocks/request_course/viewrequests.php");
		$link = '<br><a href="viewallrequests.php">'.get_string('clickfulllist','block_request_course').'</a>';
		$table->set_sql($fields, $from, $sqlconditions);
		$table->out(30, true); //print table
		//print_object($table);
		if (!$table->is_downloading()) {
			echo $link;
		}
	} else {
		print_error('nopermissiontoviewpage', 'error', '');
	}
	if (!$table->is_downloading()) {
		echo $OUTPUT->footer();
	}
}

<?php
/**
 * Simple file test_custom.php to drop into root of Moodle installation.
 * This is an example of using a sql_table class to format data.
 */
require "../../config.php";
require "$CFG->libdir/tablelib.php";
require_once('filter_form.php');
require "allcourses_table.php";
global $OUTPUT, $PAGE;

require_login();
if (isguestuser()) {
	print_error('guestsarenotallowed');
}

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/blocks/request_course/allcourses.php');
$PAGE->set_pagelayout('standard');
$table = new allcourses_table('uniqueid');
$filterform = new filter_form();

// Define headers
$PAGE->set_title('Available courses');
$PAGE->set_heading('Available courses');

if($filterform->is_cancelled()) {

	$courseurl = new moodle_url('/blocks/request_course/allcourses.php');
	redirect($courseurl);

} else if ($fromform = $filterform->get_data()) {

	$url = new moodle_url($CFG->wwwroot.'/blocks/request_course/allcourses.php?code='.$fromform->filter_code);
    redirect($url);

} else {

	//if was filtered, look for the code.
	if(isset($_GET['code'])) {
		$course_code = $_GET['code'];
		$sqlconditions = "idnumber = '".$course_code."' AND visible = 1 AND id != 1";
	} else {
		$sqlconditions = 'visible = 1 AND id != 1';
	}

	$site = get_site();
	echo $OUTPUT->header(); //output header
	$filterform->display();
	echo "<hr>";
	//sql to get all requests
	$fields = '*';
	$from = "{course}";
	//put a fixed height for the rows and expand the course name column 20%
	$table->column_style('fullname','width','20%');
	$table->column_style_all('height','60px');
	$table->set_sql($fields, $from, $sqlconditions);
	$table->define_baseurl("$CFG->wwwroot/blocks/request_course/allcourses.php");
	$table->out(30, true); //print table
	echo $OUTPUT->footer();

}
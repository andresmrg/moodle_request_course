<?php
/**
 * Simple file test_custom.php to drop into root of Moodle installation.
 * This is an example of using a sql_table class to format data.
 */
require "../../config.php";
require "$CFG->libdir/tablelib.php";
require_once('managelinks_form.php');
require "courselist_table.php";
require_once('filter_form.php');
global $OUTPUT, $PAGE;

require_login();
if (isguestuser()) {
    print_error('guestsarenotallowed');
}

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/blocks/ps_selfstudy/managecourses.php');
$PAGE->set_pagelayout('standard');
$link_form = new managelinks_form();
$table = new courselist_table('uniqueid');
$filterform = new filter_form();

// Define headers
$PAGE->set_title(get_string('title_managecourses','block_ps_selfstudy'));
$PAGE->set_heading(get_string('title_managecourses','block_ps_selfstudy'));

if($filterform->is_cancelled()) {

	$courseurl = new moodle_url('/blocks/ps_selfstudy/managecourses.php');
  	redirect($courseurl);

} else if ($fromform = $filterform->get_data()) { 

	$sqlconditions = "course_code = '".$fromform->filter_code."' AND ";

	$site = get_site();
	echo $OUTPUT->header(); //output header
	
	if (has_capability('block/ps_selfstudy:managecourses', $context, $USER->id)) {
		//display message if any
		if(isset($_GET['success'])) {
			$success = $_GET['success'];
			if($success == 'del') {
				echo '<div class="alert alert-success">'.get_string('course_deleted','block_ps_selfstudy').'</div>';	
			} else {
				echo '<div class="alert alert-success">'.get_string('course_edited','block_ps_selfstudy').'</div>';	
			}
		}

		//display filter form
		$filterform->display();
		echo "<hr>";

		$link_form->display(); //output button to create new courses
		// Get the course table.
		//$table->set_sql('*', "{block_ps_selfstudy_course}", '1');

		//sql to get all requests
		$fields = '*';
		$from = "{block_ps_selfstudy_course}";
		$sqlconditions .= '1';
		$table->set_sql($fields, $from, $sqlconditions);

		$table->define_baseurl("$CFG->wwwroot/blocks/ps_selfstudy/managecourses.php");
		$table->out(30, true); //print table
	} else {
		print_error('nopermissiontoviewpage', 'error', '');
	}
	echo $OUTPUT->footer();

} else {
	
	$site = get_site();
	echo $OUTPUT->header(); //output header
	
	if (has_capability('block/ps_selfstudy:managecourses', $context, $USER->id)) {
		//display message if any
		if(isset($_GET['success'])) {
			$success = $_GET['success'];
			if($success == 'del') {
				echo '<div class="alert alert-success">'.get_string('course_deleted','block_ps_selfstudy').'</div>';	
			} else {
				echo '<div class="alert alert-success">'.get_string('course_edited','block_ps_selfstudy').'</div>';	
			}
		}

		//display filter form
		$filterform->display();
		echo "<hr>";

		$link_form->display(); //output button to create new courses
		// Get the course table.
		//$table->set_sql('*', "{block_ps_selfstudy_course}", '1');

		//sql to get all requests
		$fields = '*';
		$from = "{block_ps_selfstudy_course}";
		$sqlconditions = '1';
		$table->set_sql($fields, $from, $sqlconditions);

		$table->define_baseurl("$CFG->wwwroot/blocks/ps_selfstudy/managecourses.php");
		$table->out(30, true); //print table
	} else {
		print_error('nopermissiontoviewpage', 'error', '');
	}
	echo $OUTPUT->footer();
}
echo "
<script>
    function check_confirm()
    {
        var c = confirm('IMPORTANT: If you delete this course, all requests and completion records related to this course will be deleted. Do you want to proceed?');
        if (c) {
            return true;
        }
        else {
            return false;
        }
    }
    ('#linkContainer').on('click', 'a', function() {
    });
</script>
";





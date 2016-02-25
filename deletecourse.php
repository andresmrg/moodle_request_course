<?php

/*
	Delete a course and delete all records related to the course (requests and completion)
*/

require_once('../../config.php');

require_login();
if (isguestuser()) {
	print_error('guestsarenotallowed');
}
global $DB;
$context = context_system::instance();

if(isset($_GET['id'])) {
	$id = $_GET['id'];

	//if the user has the right permissions
	if (has_capability('block/ps_selfstudy:managecourses', $context, $USER->id)) {
      	//Delete course record
		if (!$DB->delete_records('block_ps_selfstudy_course', ['id' => $id])) {
			print_error('inserterror', 'block_ps_selfstudy');
		}

		//delete all requests related to this course if any
		$requestlist = $DB->get_records('block_ps_selfstudy_request', array('course_id'=>$id), $sort='', $fields='id', $limitfrom=0, $limitnum=0);
		if($requestlist) {
			foreach($requestlist as $field) {
				if (!$DB->delete_records('block_ps_selfstudy_request', array('id' => $field->id))) {
					print_error('inserterror', 'block_ps_selfstudy');
				}
				//Delete all completions with this requests if any
				$completionid = $DB->get_record('block_ps_selfstudy_complete', array('request_id'=>$field->id), $fields='id');
				if($completionid) {
					if (!$DB->delete_records('block_ps_selfstudy_complete', array('id' => $completionid->id))) {
						print_error('inserterror', 'block_ps_selfstudy');
					}
				}
			}
		}
		//Redirect to manage course page.
		$url = new moodle_url('/blocks/ps_selfstudy/managecourses.php?success=del');
		redirect($url);
	} else {
		print_error('nopermissiontoviewpage', 'error', '');
	}

} else {
	$url = new moodle_url('/blocks/ps_selfstudy/managecourses.php');
	redirect($url);
	}
<?php

require_once('../../config.php');

require_login();
if (isguestuser()) {
	print_error('guestsarenotallowed');
}

global $DB;
$context = context_system::instance();
//Get course ID
if(isset($_GET['id'])) {

	$id = $_GET['id'];

	if (has_capability('block/ps_selfstudy:viewrequests', $context, $USER->id)) {
      //Delete course record
		if (!$DB->delete_records('block_ps_selfstudy_request', array('id' => $id))) {
			print_error('inserterror', 'block_ps_selfstudy');
		}
		//Delete all completions with this requests if any
		$completionid = $DB->get_record('block_ps_selfstudy_complete', array('request_id'=>$id), $fields='id');
		if($completionid) {
			if (!$DB->delete_records('block_ps_selfstudy_complete', array('id' => $completionid->id))) {
				print_error('inserterror', 'block_ps_selfstudy');
			}
		}
		if(isset($_GET['page'])) {
			$url = new moodle_url('/blocks/ps_selfstudy/viewallrequests.php');
			redirect($url);
		} else {
			$url = new moodle_url('/blocks/ps_selfstudy/viewrequests.php');
			redirect($url);
		}
		
	} else {
		print_error('nopermissiontoviewpage', 'error', '');
	}
} else {
	$url = new moodle_url('/blocks/ps_selfstudy/viewrequests.php');
	redirect($url);
}
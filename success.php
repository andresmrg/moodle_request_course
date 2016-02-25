<?php 

require "../../config.php";

require_login();
if (isguestuser()) {
  print_error('guestsarenotallowed');
}

global $DB, $USER;

//Success when the course has been shipped
if(isset($_GET['id']) && isset($_GET['status']) && isset($_GET['courseid'])) {

	$id = $_GET['id'];
	$status = $_GET['status'];
  $courseid = $_GET['courseid'];

  $course = $DB->get_record('block_ps_selfstudy_course', array('id'=>$courseid), $fields='course_name,course_code');

  //get user data to send email.
  $userid = $DB->get_record('block_ps_selfstudy_request', array('id'=>$id), $fields='student_id');
  $user = $DB->get_record('user', array('id'=>$userid->student_id), $fields='firstname,lastname,email');

  if (!$DB->update_record('block_ps_selfstudy_request', array('id'=>$id,'request_status'=>$status))) {
   print_error('inserterror', 'block_ps_selfstudy');
 }
 include('sendmessage.php');
 $url = new moodle_url('/blocks/ps_selfstudy/viewrequests.php');
 redirect($url);
} else if (isset($_GET['rid'])) {
  //Success when a user mark a course as completed
  $requestid = $_GET['rid'];
  $today = time();

  $completion = new stdClass();
  $completion->request_id = $requestid;
  $completion->completion_status = "completed";
  $completion->completion_date = $today;

  if (!$DB->insert_record('block_ps_selfstudy_complete', $completion)) {
    print_error('cannotsavedata', 'error', '');
  }
  $url = new moodle_url('/blocks/ps_selfstudy/myrequests.php?success=completed');
  redirect($url);
} else if (isset($_GET['id'])) {
  //Success when a user request a link type course
  $courseid = $_GET['id'];
  $today = time();
    $request = new stdClass();
    $request->student_id = $USER->id;
    $request->course_id = $courseid;
    $request->request_date = $today;
    $request->request_status = 2;  

  if (!$DB->insert_record('block_ps_selfstudy_request', $request)) {
      print_error('inserterror', 'block_ps_selfstudy');
  }

  $link = $DB->get_record('block_ps_selfstudy_course', array('id'=>$courseid), $fields='course_link');
  $url = $link->course_link;
  if (!preg_match("~^(ht)tps?://~i", $url)) {
    $url = "http://" . $url;
  }
  $externalurl = new moodle_url($url);
  //$url = new moodle_url('/blocks/ps_selfstudy/myrequests.php?success=yes');
  redirect($externalurl);

} else {
 $url = new moodle_url('/blocks/ps_selfstudy/myrequests.php');
 redirect($url);
}
<?php 

require_once('../../config.php');
require_once("{$CFG->libdir}/moodlelib.php");

global $CFG;
//echo "{$CFG->wwwroot}/blocks/ps_selfstudy/myrequests.php";
//exit();

/*PREPARING MESSAGE*/
//get course name

$subject = "Course Shipment Your order for $course->course_code $course->course_name has been shipped";

$userinfo = new stdClass;
$userinfo->id = $userid->student_id;
$userinfo->email = $user->email;
$userinfo->firstname = $user->firstname;
$userinfo->lastname = $user->lastname;
$userinfo->mailformat = 1;
$from = new stdClass;
$from->email="noreply@ibm.com";
$from->firstname="No ";
$from->lastname="Reply";

$message = "
<p>Hello $userinfo->firstname $userinfo->lastname,</p>

<p>You should receive this order within the next week. Be sure to come back into the EPS system <a href='{$CFG->wwwroot}/blocks/ps_selfstudy/myrequests.php'>$CFG->wwwroot</a> and indicate the date you complete the course so that your training history can be updated.</p>

<p>Thank you.</p>
";

if(!email_to_user($userinfo, $from, $subject, $message,$message, ",", false)) {
	print_error('emailfail', 'error', '');
}
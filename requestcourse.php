<?php

require_once('../../config.php');
require_once('requestcourse_form.php');
require_once("../../user/lib.php");

require_login();
if (isguestuser()) {
  print_error('guestsarenotallowed');
}

global $OUTPUT, $PAGE, $COURSE, $USER;

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/blocks/request_course/requestcourse.php');
$PAGE->set_pagelayout('standard');
$form_page = new requestcourse_form();

// Define headers
$PAGE->set_title(get_string('title_requestcourses','block_request_course'));
$PAGE->set_heading(get_string('title_requestcourses','block_request_course'));
//$PAGE->navbar->add('Request  course', new moodle_url('/blocks/request_course/requestcourse.php'));

if($form_page->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
	$courseurl = new moodle_url('/blocks/request_course/managecourses.php');
  redirect($courseurl);

} else if ($fromform = $form_page->get_data()) {
    // We need to add code to appropriately act on and store the submitted data

    /*
    1. update user profile
    2. save request data into request table
    3. take the user to the list of request and pass the message if the request was made
    successfully
    */
    $profile = new stdClass();
    $profile->id = $USER->id;
    $profile->firstname         = $fromform->firstname;
    $profile->lastname         = $fromform->lastname;
    $profile->email         = $fromform->email;
    $profile->country         = $fromform->country;
    $profile->city         = $fromform->city;
    $profile->address         = $fromform->address;
    $profile->phone1         = $fromform->phone1;

    //update user
    user_update_user($profile, false,true);
    // Reload from db.
    $user = $DB->get_record('user', array('id' => $profile->id), '*', MUST_EXIST);
    // Override old $USER session variable if needed.
    if ($USER->id == $user->id) {
        // Override old $USER session variable if needed.
      foreach ((array)$user as $variable => $value) {
        if ($variable === 'description' or $variable === 'password') {
                // These are not set for security nad perf reasons.
          continue;
        }
        $USER->$variable = $value;
      }
    }

    $today = time();
    $request = new stdClass();
    $request->student_id = $profile->id;
    $request->course_id = $fromform->courseid;
    $request->request_date = $today;  

    //2. store the request data in the request table
    if (!$DB->insert_record('block_request_course_request', $request)) {
      print_error('inserterror', 'block_request_course');
    }


    //get id of the zipcode in the fields table
    $zip_id = $DB->get_record('user_info_field', array('shortname'=>'zipcode'), $fields='id', $strictness=IGNORE_MISSING);
    $zipcodedata = new stdClass();
    $zipcodedata->userid = $USER->id;
    $zipcodedata->fieldid = $zip_id->id;
    $zipcodedata->data = $fromform->zipcode;

    //get id of the address2 in the fields table
    $address2_id = $DB->get_record('user_info_field', array('shortname'=>'address2'), $fields='id', $strictness=IGNORE_MISSING);
    $address2data = new stdClass();
    $address2data->userid = $USER->id;
    $address2data->fieldid = $address2_id->id;
    $address2data->data = $fromform->address2;

    //get id of the state in the fields table
    $state_id = $DB->get_record('user_info_field', array('shortname'=>'state'), $fields='id', $strictness=IGNORE_MISSING);
    $statedata = new stdClass();
    $statedata->userid = $USER->id;
    $statedata->fieldid = $state_id->id;
    $statedata->data = $fromform->state;

  //if there is already a zipcode defined, update it.
    if($DB->record_exists('user_info_data', array('fieldid'=>$zip_id->id,'userid'=>$USER->id))) {
    //get the record id
      $dataid = $DB->get_record('user_info_data', array('fieldid'=>$zip_id->id,'userid'=>$USER->id), $fields='id', $strictness=IGNORE_MISSING);
      if (!$DB->update_record('user_info_data', array('id'=>$dataid->id,'data'=>$fromform->zipcode))) {
        print_error('inserterror', 'block_request_course');
      }
    } else {
        //3. insert a record with the zipcode
      if (!$DB->insert_record('user_info_data', $zipcodedata)) {
        print_error('inserterror', 'block_request_course');
      }
    }


    //if there is already a zipcode defined, update it.
    if($DB->record_exists('user_info_data', array('fieldid'=>$address2_id->id,'userid'=>$USER->id))) {
      //get the record id
        $addressdataid = $DB->get_record('user_info_data', array('fieldid'=>$address2_id->id,'userid'=>$USER->id), $fields='id', $strictness=IGNORE_MISSING);
        if (!$DB->update_record('user_info_data', array('id'=>$addressdataid->id,'data'=>$fromform->address2))) {
          print_error('inserterror', 'block_request_course');
        }
    } else {
        //3. insert a record with the zipcode
        if (!$DB->insert_record('user_info_data', $address2data)) {
          print_error('inserterror', 'block_request_course');
        }
    }

    //if there is already a state defined, update it.
    if($DB->record_exists('user_info_data', array('fieldid'=>$state_id->id,'userid'=>$USER->id))) {
      //get the record id
        $statedataid = $DB->get_record('user_info_data', array('fieldid'=>$state_id->id,'userid'=>$USER->id), $fields='id', $strictness=IGNORE_MISSING);
        if (!$DB->update_record('user_info_data', array('id'=>$statedataid->id,'data'=>$fromform->state))) {
          print_error('inserterror', 'block_request_course');
        }
    } else {
        //3. insert a record with the state
        if (!$DB->insert_record('user_info_data', $statedata)) {
          print_error('inserterror', 'block_request_course');
        }
    }

//echo "<script>alert('Order Submitted');</script>";
    //redirect to my request page
      $url = new moodle_url($CFG->wwwroot.'/blocks/request_course/myrequests.php?success=yes');
      redirect($url);

  } else {
  // form didn't validate or this is the first display
    $site = get_site();
    echo $OUTPUT->header();
    $form_page->display();
    echo $OUTPUT->footer();
  }
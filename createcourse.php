<?php

require_once('../../config.php');
require_once('createcourse_form.php');

require_login();
if (isguestuser()) {
  print_error('guestsarenotallowed');
}

global $OUTPUT, $PAGE;

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/blocks/ps_selfstudy/createcourse.php');
$PAGE->set_pagelayout('standard');
$form_page = new createcourse_form();

$today = time();
$toform['date_created'] = $today;
$form_page->set_data($toform);

// Define headers
$PAGE->set_title(get_string('title_createcourse','block_ps_selfstudy'));

require_login();

if($form_page->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
	$courseurl = new moodle_url('/blocks/ps_selfstudy/managecourses.php');
  redirect($courseurl);

} else if ($fromform = $form_page->get_data()) {
    // We need to add code to appropriately act on and store the submitted data

   	if (!$DB->insert_record('block_ps_selfstudy_course', $fromform)) {
   		print_error('inserterror', 'block_ps_selfstudy');
   	}
   	$courseurl = new moodle_url('/blocks/ps_selfstudy/managecourses.php');
   	redirect($courseurl);

   } else {
    // form didn't validate or this is the first display
   	$site = get_site();
   	echo $OUTPUT->header();
    if (has_capability('block/ps_selfstudy:managecourses', $context, $USER->id)) {
      $form_page->display();
    } else {
      print_error('nopermissiontoviewpage', 'error', '');
    }
    echo $OUTPUT->footer();
  }
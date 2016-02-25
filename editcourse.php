<?php

require_once('../../config.php');
require_once('editcourse_form.php');

require_login();
if (isguestuser()) {
  print_error('guestsarenotallowed');
}

global $OUTPUT, $PAGE;

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/blocks/ps_selfstudy/editcourse.php');
$PAGE->set_pagelayout('standard');
$form_page = new editcourse_form();

// Define headers
$PAGE->set_title(get_string('title_editcourse','block_ps_selfstudy'));
$PAGE->set_heading(get_string('title_editcourse','block_ps_selfstudy'));

if($form_page->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
	$courseurl = new moodle_url('/blocks/ps_selfstudy/managecourses.php');

} else if ($fromform = $form_page->get_data()) {
    // We need to add code to appropriately act on and store the submitted data
  if (!$DB->update_record('block_ps_selfstudy_course', $fromform)) {
   print_error('inserterror', 'block_ps_selfstudy');
 }
 $courseurl = new moodle_url('/blocks/ps_selfstudy/managecourses.php?success=edited');
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

// form didn't validate or this is the first display

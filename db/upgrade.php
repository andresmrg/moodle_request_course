<?php
 
function xmldb_block_request_course_upgrade($oldversion) {
    global $CFG, $DB;
 
    $result = TRUE;

 $dbman = $DB->get_manager();
 if ($oldversion < 2015081905) { 
    return $result;
}
?>
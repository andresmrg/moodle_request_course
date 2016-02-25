<?php


/**
 * Table class to be put in managecourses.php selfstudy manage course page.
 *  for defining some custom column names and proccessing
 */
class completion_table extends table_sql {

    //GLOBAL $CFG;

    /**
     * Constructor
     * @param int $uniqueid all tables have to have a unique id, this is used
     *      as a key when storing table properties like sort order in the session.
     */
    function __construct($uniqueid) {
        parent::__construct($uniqueid);
        // Define the list of columns to show.
        $columns = array('course_code','course_name','empctry','email','firstname','completion_date','completion_status');
        // Define the titles of columns to show in header.
        $headers = array('Course Code','Title','EmpSerial/CC','Email Address','Name','Completion Date','Completion Status');
        
        /*if (!$this->is_downloading()) {
            $columns[] = 'actions';
            $headers[] = 'Action';
        }*/

        global $DB;
        
        //print_object($columns);
        $this->sortable(true,'course_code', SORT_ASC);
        $this->collapsible(false);
        //$this->no_sorting('actions');
        
        $this->define_columns($columns);
        $this->define_headers($headers);
        
        
    }

    /**
     * This function is called for each data row to allow processing of the
     * username value.
     *
     * @param object $values Contains object with all the values of record.
     * @return $string Return username with link to profile or username only
     *     when downloading.
     */
    function col_firstname($values) {
        // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {
            $fullname = "$values->firstname $values->lastname";
            return $fullname;
        } else {
            return '<a href="$CFG->wwwroot/../../../user/profile.php?id='.$values->student_id.'">'.$values->firstname." ".$values->lastname.'</a>';
        }
    }

    function col_empctry($values) {
        global $DB;
        // If the data is being downloaded than we don't want to show HTML.
        $empctry_id = $DB->get_record('user_info_field', array ('shortname'=>'empctry'), $fields='id', $strictness=IGNORE_MISSING); 
        if(!empty($empctry_id)) {
            $empctry = $DB->get_record('user_info_data', array ('userid'=>$values->student_id,'fieldid'=>$empctry_id->id), $fields='data', $strictness=IGNORE_MISSING);
            if(!empty($empctry)) {
                return $empctry->data;
            } else {
                return "";
            }
        }
    }


    function col_completion_status($values) {
        // If the value is 0, show Pending status.
        if($values->completion_status == "completed") {
            return "Completed";    
        }
    }
    function col_completion_date($values) {
        // Show readable date from timestamp.
        $date = $values->completion_date;
        return date("m/d/Y",$date);
    }

    /**
     * This function is called for each data row to allow processing of
     * columns which do not have a *_cols function.
     * @return string return processed value. Return NULL if no change has
     *     been made.
     */
    function other_cols($colname, $value) {
        // For security reasons we don't want to show the password hash.

    }
}
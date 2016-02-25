<?php


/**
 * Table class to be put in managecourses.php selfstudy manage course page.
 *  for defining some custom column names and proccessing
 */
class viewrequests_table extends table_sql {

    //GLOBAL $CFG;

    /**
     * Constructor
     * @param int $uniqueid all tables have to have a unique id, this is used
     *      as a key when storing table properties like sort order in the session.
     */
    function __construct($uniqueid) {
        parent::__construct($uniqueid);
        // Define the list of columns to show.
        $columns = array('course_code','course_name','email','firstname','address','address2','city','state','zipcode','country','phone1','request_date','request_status');
        // Define the titles of columns to show in header.
        $headers = array('Course Code','Title','Email Address','Name','Address 1','Address 2','City','State','Zip','Country','Phone #','Request date','Status');
        
        if (!$this->is_downloading()) {
            $columns[] = 'actions';
            $headers[] = 'Action';
        }

        global $DB;
        
        //print_object($columns);
        $this->sortable(true,'course_code', SORT_ASC);
        $this->collapsible(false);
        $this->no_sorting('actions');
        
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

    function col_address2($values) {
        global $DB;
        
        $address2_id = $DB->get_record('user_info_field', array ('shortname'=>'address2'), $fields='id', $strictness=IGNORE_MISSING);        
        $address2 = $DB->get_record('user_info_data', array ('userid'=>$values->student_id,'fieldid'=>$address2_id->id), $fields='data', $strictness=IGNORE_MISSING);

        return $address2->data;
    }

    function col_zipcode($values) {
        global $DB;
        //display fulladdress
        $zip_id = $DB->get_record('user_info_field', array ('shortname'=>'zipcode'), $fields='id', $strictness=IGNORE_MISSING);        
        $zipcode = $DB->get_record('user_info_data', array ('userid'=>$values->student_id,'fieldid'=>$zip_id->id), $fields='data', $strictness=IGNORE_MISSING);

        return $zipcode->data;
    }

    function col_state($values) {
        global $DB;
        //display fulladdress
        $state_id = $DB->get_record('user_info_field', array ('shortname'=>'state'), $fields='id', $strictness=IGNORE_MISSING);        
        $state = $DB->get_record('user_info_data', array ('userid'=>$values->student_id,'fieldid'=>$state_id->id), $fields='data', $strictness=IGNORE_MISSING);

        return $state->data;
    }

    function col_request_status($values) {
        // If the value is 0, show Pending status.
        if($values->request_status == 0) {
            return "Pending";    
        } else {
            return "Shipped";
        }
    }
    function col_request_date($values) {
        // Show readable date from timestamp.
        $date = $values->request_date;
        return date("m/d/Y",$date);
    }
    function col_actions($values) {
        if (!$this->is_downloading()) {
            if($values->request_status == 0) {
                return '<a href="success.php?id='.$values->id.'&status=1&courseid='.$values->course_id.'">Delivered</a> - <a href="deleterequest.php?id='.$values->id.'">Delete</a>';
            } else {
                return '<a href="deleterequest.php?id='.$values->id.'&page=all">Delete</a>';
            }
        } 
        //- <a href="deletecourse.php?id='.$values->id.'" onclick="return check_confirm()">Delete</a>';
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
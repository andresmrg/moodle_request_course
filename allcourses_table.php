<?php


/**
 * Table class to be put in managecourses.php selfstudy manage course page.
 *  for defining some custom column names and proccessing
 */
class allcourses_table extends table_sql {

    //GLOBAL $CFG;

    /**
     * Constructor
     * @param int $uniqueid all tables have to have a unique id, this is used
     *      as a key when storing table properties like sort order in the session.
     */
    function __construct($uniqueid) {
        parent::__construct($uniqueid);
        // Define the list of columns to show.
        $columns = array('idnumber','shortname','fullname','summary','startdate','actions');
        $this->sortable(true,'course_code', SORT_ASC);
        $this->collapsible(false);
        $this->no_sorting('actions');
        $this->no_sorting('course_description');
        $this->define_columns($columns);
        
        // Define the titles of columns to show in header.
        $headers = array('ID Number','Shortname','Course Full Name', 'Summary','Start Date','Action');
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
    function col_course_code($values) {
        
        global $DB;
        $desc_link = $DB->get_record('course',array('id'=>$values->id), $fields='description_link');
        $url = $desc_link->description_link;

        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }
    
        if(!empty($desc_link) && $desc_link->description_link !== NULL && $desc_link->description_link !== "") {
            return '<a href="'.$url.'" target="_blank" style="text-decoration:underline;">'.$values->course_code.'</a>';
        } else {
            return $values->course_code;
        }
    }

    public function col_course_name($values)
    {
        $value = $values->course_name;
        return '<p style="width: 100%; height: 60px; margin: 0; padding: 0; overflow: auto;">'.$value.'</p>';
    }

    public function col_course_description($values) 
    {  
        $value = $values->course_description;
        return '<p style="width: 100%; height: 60px; margin: 0; padding: 0; overflow: auto;">'.$value.'</p>';
        // if(strlen($value) < 150) {
        //     return $value;
        // } else {
        //     $pos=strpos($values->course_description, ' ', 150);
        //     $value = substr($value,0,$pos).'...'; 
        //     return $value;
        // }        
        //return $value;
    }

    function col_course_type($values) {
        //print_object($values);
        // If the value is 0, show Phisical copy, else, Link course.
        if($values->course_type == 0) {
            return "Physical Copy";    
        } else {
            return "Link Course";
        }
    }
    function col_course_status($values) {
        // If the value is 0, show Active copy, else, Disable.
        if($values->course_status == 0) {
            return "Active";    
        } else {
            return "Disable";
        }
    }
    function col_date_created($values) {
        // Show readable date from timestamp.
        $date = $values->date_created;
        return date("m/d/Y",$date);
    }
    function col_actions($values) {
        global $DB,$CFG;
        // Show readable date from timestamp.
        // if($values->course_type == 0) {
            $requesturl = $CFG->wwwroot."/blocks/request_course/requestcourse.php?id=".$values->id;
        // } else {
        //     $requesturl = $CFG->wwwroot."/blocks/request_course/success.php?id=".$values->id;;
        // }

        return '<a href="'.$requesturl.'">Request access</a>';
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
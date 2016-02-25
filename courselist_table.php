<?php


/**
 * Table class to be put in managecourses.php selfstudy manage course page.
 *  for defining some custom column names and proccessing
 */
class courselist_table extends table_sql {

    //GLOBAL $CFG;

    /**
     * Constructor
     * @param int $uniqueid all tables have to have a unique id, this is used
     *      as a key when storing table properties like sort order in the session.
     */
    function __construct($uniqueid) {
        parent::__construct($uniqueid);
        // Define the list of columns to show.
        $columns = array('course_code','course_platform','course_name','course_description','course_hours', 'course_type','course_status','date_created','actions');
        $this->sortable(true,'course_code', SORT_ASC);
        $this->collapsible(false);
        $this->no_sorting('actions');
        $this->no_sorting('course_description');
        $this->define_columns($columns);
        
        // Define the titles of columns to show in header.
        $headers = array('Course Code','Platform','Course Name', 'Description','Hours', 'Course Type','Status','Date Created','Action');
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
        // If the data is being downloaded than we don't want to show HTML.

        global $DB;
        $desc_link = $DB->get_record('block_ps_selfstudy_course',array('id'=>$values->id), $fields='description_link');
        $url = $desc_link->description_link;
        
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }

        if(!empty($desc_link) && $desc_link->description_link !== NULL && $desc_link->description_link !== "") {
            return '<a href="'.$desc_link->description_link.'" target="_blank">'.$values->course_code.'</a>';
        } else {
            return $values->course_code;
        }

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
        return date("M d, Y",$date);
    }
    function col_actions($values) {
        global $DB;
        // Show readable date from timestamp.
        $str = $values->course_description;
        $description = base64_encode($str);
        
        $link = $DB->get_record('block_ps_selfstudy_course',array('id'=>$values->id), $fields='course_link');
        
        $str2 = $link->course_link;
        $link = base64_encode($str2);

        return '<a href="editcourse.php?id='.$values->id.'&platform='.$values->course_platform.'&code='.$values->course_code.'&name='.$values->course_name.'&hours='.$values->course_hours.'&link='.$link.'&desc='.$description.'&type='.$values->course_type.'&status='.$values->course_status.'">Edit</a> 
        - <a href="deletecourse.php?id='.$values->id.'" onclick="return check_confirm()">Delete</a>';
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
<?php

include_once('Adamamed_DB.php');

class Adamamed_Mailist_Page {

    /* Form related arrays */
    protected $form_details_email = "your-email";
    protected $form_details_name = "your-name";
    protected $form_details_phone ='phone-num';

    function __construct() {      
    }                          
    
    /**
     * drawStats
     * Generate statistics on the registration form
     * 
     * @plugin - the plugin instance
     * @asString - if true, the result HTML will be returned as a string (used for exporting)
     *             if false, the result will be writted to the output stream (displayed in
     *             admin area)
     */                                
    public function drawStats($plugin, $asString = false) {
        $date = date('H:i d-m-Y');        
        $out_str = "<div class='wrap'><h2>ריכוז מיילים מטופס הרשמה</h2><p>&nbsp&nbsp($date)</p>";        
        if (!$asString)
            $out_str .= "<p><button type='button' onclick='javascript:exportAsDoc(\"export_stats=1\");'>ייצוא לקובץ</button></p>";
        $db = new Adamamed_DB();
        $stats = $this->getHelpersStats($db);
        if (!isset($stats) || sizeof($stats) == 0) {
            $out_str .= "<div>לא נמצאו טפסים</div></div>";
            if ($asString)
                return $out_str;
            echo $out_str;
            return "";
        }
        $size = $stats['SIZE'];
        $out_str .= "<h4>סך הכל ".$size." טפסים</h4>";
        $names = array_values($stats[$this->form_details_name]);        
        $emails = array_values($stats[$this->form_details_email]);        
        $phones = array_values($stats[$this->form_details_phone]);        

        $out_str .= "<table cellspacing='1' cellpadding='2' border='1' ><tbody>";
        $out_str .= "<tr><td>#</td><td>שם</td><td>מייל</td><td>מספר טלפון</td></tr>";
        for ($index = 0; $index < $size ; $index++) {
            $out_str .= "<tr'><td>". ($index+1). "</td>" .
            "<td>".$names[$index]."</td>" .
            "<td>".$emails[$index]."</td>" .
            "<td>".$phones[$index]."</td>" .
            "</tr>";
        }

        $out_str .= "</tbody></table>";

        $out_str .= "</div>";
        if ($asString)
            return $out_str;
        echo $out_str;
        return "";
    }

    /**
     * Gets an array where each entry is an array for a single form key with the number
     * of times the answer appeared 
     */
    public function getHelpersStats($db) {
        $all_forms = $db->getRegistrationDetails();
        if (sizeof($all_forms) == 0)
          return;
        $keys = array_keys($all_forms[0]);
        $aggregate = array();
        
        // for each key in the form, create an array of all the received values ($aggregate)
        foreach ($keys as $key) {
          $this_key = array();
          foreach($all_forms as $form) {
            array_push($this_key, $form[$key]);
          }
          //array_push($aggregate, $this_key);
          $aggregate[$key] = $this_key;
        }
        $size = sizeof($aggregate[$this->form_details_name]);
        $aggregate['SIZE'] = $size;
        return $aggregate;
      }
}
  ?>

<?php

include_once('Adamamed_DB.php');

/**
 * Admin Panel Feature
 * Presents a list of all users that have filled in the Details form
 */
class Adamamed_ArrivalList_Page {

    /**
     * drawStats
     * Generate statistics on the registration form
     * 
     * @plugin - the plugin instance
     * @asString - if true, the result HTML will be returned as a string (used for exporting)
     *             if false, the result will be writted to the output stream (displayed in
     *             admin area)
     */                                
    public function drawList($plugin, $asString = false) {
        $date = date('H:i d-m-Y');        
        $out_str = "<div class='wrap'><h2>דף הגעה לכנס</h2><p>מעודכן ל&nbsp&nbsp($date)</p>";                
        if (!$asString) {
            $out_str .= "<p><button type='button' onclick='javascript:exportAsDoc(\"export_stats=1\");'>ייצוא לקובץ Word</button>&nbsp;&nbsp;";
            $out_str .= "<button type='button' onclick='javascript:exportAsDoc(\"export_stats=2\");'>ייצוא לקובץ Excel</button></p>";
        }
        $db = new Adamamed_DB();
        $forms = $this->getForms($db);
        if (!isset($forms) || sizeof($forms) == 0) {
            $out_str .= "<div>לא נמצאו טפסים</div></div>";
            if ($asString)
                return $out_str;
            echo $out_str;
            return "";
        }        
        $out_str .= "<h4>סך הכל ".sizeof($forms)." טפסים</h4>";
        $out_str .= "<table cellspacing='1' cellpadding='2' border='1'><thead>";
        $out_str .= "<th width='200'>שם</th><th width='200'>טלפון</th><th width='200'>ימי הגעה</th><th width='200'>אדם נוסף</th><th width='200'>לינה</th></thead><tbody>";
        foreach ($forms as $person) {

            $out_str .= "<tr><td>".$person['your-name']."</td>";
            $out_str .= "<td>".$this->fixPhoneNumber($person['phone-num'])."</td>";
            $out_str .= "<td>".$person['days']."</td>";
            $out_str .= "<td>".$person['kids']."</td>";
            $out_str .= "<td>".$person['sleep']."</td></tr>";
        }

        $out_str .= "</tbody></table>";

        if ($asString)
            return $out_str;
        echo $out_str;

        return "";
    }

    function getForms($db) {
        $all_forms = $db->getRegistrationDetails();
        return $all_forms;
    }

        /**
     * Reformats the phone number to a readable format with 
     * a dash at the right place
     */
    protected function fixPhoneNumber($number) {
        $chars = str_split($number);
        $normalized = $number;
        if ($chars[0] == '0' && $chars[1] == '5') { // mobile
            if ($chars[3] != '-') {
                // add - char
                $normalized = implode("",array_merge(array_slice($chars, 0, 3), array('-'), array_slice($chars, 3)));
            }
        }
        else if ($chars[0] == '0' && $chars[1] != '0') { // landline
            if ($chars[2] != '-') {
                // add - char
                $normalized = implode("",array_merge(array_slice($chars, 0, 2), array('-'), array_slice($chars, 2)));
            }            
        }
        return $normalized;
    }
  
  }
?>

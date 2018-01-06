<?php

include_once('Adamamed_DB.php');

class Adamamed_HelpersPage {

    /* Form related arrays */
    protected $preference = 'checkbox-297';
    protected $more = 'textarea-212';
    protected $motivation = 'textarea-150';
    protected $form_details_include_fields = array();
    protected $form_details_name = "your-name";
    protected $form_details_email = "your-email";
    protected $form_desc = array();

    function __construct() {      
        array_push($this->form_details_include_fields, $this->motivation);
        array_push($this->form_details_include_fields,$this->preference);
        array_push($this->form_details_include_fields,$this->more);
        $this->form_desc[$this->preference] = "האם יש לך העדפה לצוות מסוים";
        $this->form_desc[$this->motivation] = "מה המוטיבציה שלך להצטרף לצוות העזר";
        $this->form_desc[$this->more] = "רוצה להוסיף משהו";
        //array_push($form_des,array($this->preference => "האם יש לך העדפה לצוות מסוים"));
        //array_push($form_des, array($this->motivation => "מה המוטיבציה שלך להצטרף לצוות העזר"));
        //array_push($form_des,array($this->more => ));      
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
        $out_str = "<div class='wrap'><h2>סטטיסטיקה של עוזרים</h2><p>התפלגות תשובות העוזרים&nbsp&nbsp($date)</p>";        
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
        $out_str .= "<h4>סך הכל ".$stats['SIZE'][0]." טפסים</h4>";
        $names = array_values($stats[$this->form_details_name]);
        $values = array($this->motivation => $stats[$this->motivation], $this->preference=>$stats[$this->preference]);

        $out_str .= "<table cellspacing='1' cellpadding='2' border='1' ><tbody>";
        foreach ($values as $key => $value) {
            $out_str .= "<tr'><td>". $key. "</td>" .
            "<td>".$this->form_desc[$key]."</td>" .
            "<td><table class='ad-table'>";
            $index = 0;
            foreach ($value as $item) {
                $userid = $names[$index];
                $out_str .= "<tr><td class='stats-value'>$userid</td>";
                if (!is_array($item))
                    $out_str .= "<td class='stats-name'>$item</td>";
                else {
                    $out_str .= "<td class='stats-name'>";
                    foreach ($item as $value) 
                        $out_str .= $value . ",&nbsp;&nbsp;";
                    $out_str .= "</td>";
                }
                $index++;   
            }
            $out_str .= "</table></td></tr>";
        }

/*
        foreach ($stats as $key => $value) { 
            $index = 1;
            if ($key == 'SIZE' || !in_array($key,$this->form_details_include_fields))
                continue;
                $out_str .= "<tr><td>". $key. "</td>" .
                    "<td>".$this->form_desc[$key]."</td>" .
                    "<td><table>";
            foreach ($value as $inkey => $invalue) {
                $index++;
                $userid = $names[$index];
                $out_str .= "<tr><td class='stats-value'>$userid</td>";
                $out_str .= "<td class='stats-name'>$inkey</td>";
            }
            $out_str .= "</table></td></tr>";
        }
        */
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
        $all_forms = $db->getHelpers();
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
          array_push($aggregate, $this_key);
        }
        $size = sizeof($aggregate[0]);

        // Count the number of occurences of each value in each key
        $i = 0;
        $stats = array();
        foreach ($aggregate as $key_array) {
            $the_key = $keys[$i++];
            //echo "<p>$the_key = ".print_r($key_array,true)."</p>";
            if (!in_array($the_key,$this->form_details_include_fields) && $the_key != $this->form_details_name && $the_key != $this->form_details_email)  {
                continue;
            }
            //$key_values = array_count_values($key_array);
            $stats[$the_key] = $key_array;
        }        
        $stats['SIZE'] = array($size);
        return $stats;
      }
}
  ?>

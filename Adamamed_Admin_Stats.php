<?php

include_once('Adamamed_DB.php');

class Adamamed_StatsPage {

    /* Form related arrays */
    protected $form_details_open_fields = array('talent','other','studies');
    protected $form_details_general_fields = array('your-name','your-email','phone-num','address','talent','other','studies','accept','SIZE');
    protected $form_details_closed_fields = array('type','days','sleep','market','kids');
    protected $form_details_accept_mail = "accept";
    protected $form_details_name = "your-name";
    protected $form_details_email = "your-email";
    protected $form_desc = array( "type" => "מה הקשר שלך לתחום הרפואה המשלימה",
                                  "days" => "באיזה ימים תהיה",
                                  "sleep" => "העדפות בלינה",
                                  "market" => "מכירה בשוק",
                                  "kids" => "ילדים נילווים",
                                  "talent" => "כשרונות לשיתוף",
                                  "other" => "משתתפים נוספים",
                                  "studies" => "אם אתה מטפל או סטודנט, נשמח אם תציין באיזה תחום",
                                );
    
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
        $out_str = "<div class='wrap'><h2>סטטיסטיקה של נרשמים</h2><p>התפלגות תשובות הנרשמים&nbsp&nbsp($date)</p>";        
        if (!$asString) {
            $out_str .= "<p><button type='button' onclick='javascript:exportAsDoc(\"export_stats=1\");'>ייצוא לקובץ Word</button>&nbsp;&nbsp;";
            $out_str .= "<button type='button' onclick='javascript:exportAsDoc(\"export_stats=2\");'>ייצוא לקובץ Excel</button></p>";
        }
        $db = new Adamamed_DB();
        $stats = $this->getRegistrationDetailsStats($db);
        if (!isset($stats) || sizeof($stats) == 0) {
            $out_str .= "<div>לא נמצאו טפסים</div></div>";
            if ($asString)
                return $out_str;
            echo $out_str;
            return "";
        }
        $out_str .= "<h4>סך הכל ".$stats['SIZE'][0]." טפסים</h4>";
        $out_str .= "<table cellspacing='1' cellpadding='2' border='1'><tbody>";
        foreach ($stats as $key => $value) { 
            if ($key == 'SIZE')
                continue;
            $total = 0;
            foreach ($value as $inkey => $invalue) {
                $total += $invalue;
            }
            $out_str .= "<tr><td>". $key. "</td>" .
                    "<td>".$this->form_desc[$key]."</td>" .
                    "<td><table>";
            foreach ($value as $inkey => $invalue) {
                $percent = (int)(($invalue / $total) * 100+.5);
                $out_str .= "<tr><td class='stats-name'>$inkey</td>";
                $out_str .= "<td class='stats-value'>$invalue מתוך $total</td>";
                $out_str .= "<td class='stats-percent'>$percent%</td></tr>";
            }
            $out_str .= "</table></td></tr>";
        }
        $out_str .= "</tbody></table>";

        $out_str .= "<h2>תשובות נוספות</h2><p>קיבוץ תשובות הנרשמים</p>";
        $openStats = $this->getRegistrationDetailsOpenQuestionsStats($db);
        $out_str .= "<table cellspacing='1' cellpadding='2' border='1'><tbody>";
        foreach ($openStats as $key => $value) {
            if ($key == 'SIZE')
                continue;
            $out_str .= "<tr><td>".$key."</td>";
            $out_str .= "<td>".$this->form_desc[$key]."</td>";
            $out_str .= "<td><table class='ad1-table'>";
            foreach ($value as $inkey => $invalue) {
                if ($invalue == "")
                    continue;
                $out_str .= "<tr>";
                $out_str .= "<td class='stats-text'>$invalue</td>";
                $out_str .= "</tr>";
            }
            $out_str .= "</table></td></tr>";
        }
        $out_str .= "</tbody></table>";

        $out_str .= "<h2>הסכמה לדיוור</h2><p></p>";
        $out_str .= "<table cellspacing='1' cellpadding='2' border='1'><tbody>";
        $acceptance = $this->getRegistrationDetailsAcceptMailStats($db);
        $out_str .= "<tr><td>הסכימו לדיוור</td><td>";
        foreach ($acceptance['accepted'] as $item) {
            $out_str .= $item . "<br>";
        }
        $out_str .= "</td></tr>";
        $out_str .= "<tr><td>לא הסכימו לדיוור</td><td>";
        foreach ($acceptance['declined'] as $item) {
            $out_str .= $item . "<br>";
        }
        $out_str .= "</td></tr>";
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
    public function getRegistrationDetailsStats($db) {
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
          array_push($aggregate, $this_key);
        }
        $size = sizeof($aggregate[0]);

        // Count the number of occurences of each value in each key
        $i = 0;
        $stats = array();
        foreach ($aggregate as $key_array) {
            $the_key = $keys[$i++];
            if (in_array($the_key,$this->form_details_general_fields)) 
                continue;
            $key_values = array_count_values($key_array);
            $stats[$the_key] = $key_values;
        }        
        $stats['SIZE'] = array($size);
        return $stats;
      }
  
    /**
     * Gets an array where each entry is an array for a single form key with the answers
     */
    public function getRegistrationDetailsOpenQuestionsStats($db) {
        $all_forms = $db->getRegistrationDetails($this->form_details_open_fields);
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
        $i = 0;
        $stats = array();
        foreach ($aggregate as $key_array) {
            $the_key = $keys[$i++];
            if (!in_array($the_key,$this->form_details_open_fields)) 
                continue;
            $stats[$the_key] = $key_array;
        }
        return $stats;
      }

      public function getRegistrationDetailsAcceptMailStats($db) {
        $all_forms = $db->getRegistrationDetails($this->form_details_open_fields);
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
  
        $i = 0;
        $stats = array();
        foreach ($aggregate as $key_array) {
            $the_key = $keys[$i++];
            $stats[$the_key] = $key_array;
        }
  
        $responses =$stats[$this->form_details_accept_mail];
        $names = $stats[$this->form_details_name]; 
        $emails = $stats[$this->form_details_email]; 

        $yes = array();
        $no = array();
        for ($index = 0; $index < sizeof($names) ; $index++) {
            $item = $names[$index] . " [" . $emails[$index]. "]";
            if ($responses[$index][0] <> '') 
                array_push($yes, $item);
            else
                array_push($no, $item);
        }
        return array('accepted' => $yes, 'declined' => $no);
      }
  }
?>

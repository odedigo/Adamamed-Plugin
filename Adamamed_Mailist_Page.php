<?php

include_once('Adamamed_DB.php');

/**
 * Admin Panel Feature
 * Presents a list of all users that have filled in the Details form
 */
class Adamamed_Mailist_Page {

    /* Form related attributes */
    protected $form_details_email = "your-email";
    protected $form_details_name = "your-name";
    protected $form_details_phone ='phone-num';

    function __construct() {      
    }                          
    
    /**
     * drawStats
     * Draws the mailing list
     * 
     * @plugin - the plugin instance
     * @asString - if true, the result HTML will be returned as a string (used for exporting)
     *             if false, the result will be writted to the output stream (displayed in
     *             admin area)
     */                                
    public function drawMailist($plugin, $asString = false) {
        $date = date('H:i d-m-Y');        
        $out_str = "<div class='wrap'><h2>ריכוז מיילים מטופס הרשמה</h2><p>&nbsp&nbsp($date)</p>";        
        if (!$asString) {
            $out_str .= "<p><button type='button' onclick='javascript:exportAsDoc(\"export_stats=1\");'>ייצוא לקובץ Word</button>&nbsp;&nbsp;";
            $out_str .= "<button type='button' onclick='javascript:exportAsDoc(\"export_stats=2\");'>ייצוא לקובץ Excel</button></p>";
        }

        // Get all the paid orders from WooCommerce
        $order_details = $this->getWoocommerceOrders();     

        $db = new Adamamed_DB();
        $stats = $this->getMembersDetails($db);
        if (!isset($stats) || sizeof($stats) == 0) {
            $out_str .= "<div>לא נמצאו טפסים</div></div>";
            if ($asString)
                return $out_str;
            echo $out_str;
            return "";
        }
        $size = $stats['SIZE'];
        $out_str .= "<h4>סך הכל ".$size." טפסים מהם ".sizeof($order_details['email'])." שולמו, ו ".$order_details['total']." כרטיסים נקנו</h4>";

        $names = array_values($stats[$this->form_details_name]);        
        $emails = array_values($stats[$this->form_details_email]);        
        $phones = array_values($stats[$this->form_details_phone]);        

        $out_str .= "<table cellspacing='1' cellpadding='2' border='1' ><tbody>";
        $out_str .= "<tr><td>#</td><td>שם</td><td>מייל</td><td>מספר טלפון</td><td>הערות</td><td>תשלום</td><td>כמות</td><td>מוצר</td></tr>";
        $unique_email = array();
        for ($index = 0; $index < $size ; $index++) {
            $out_str .= "<tr'><td>". ($index+1). "</td>" .
            "<td>".$names[$index]."</td>" . 
            "<td>".$emails[$index]."</td>";
            $out_str .="<td>".$this->fixPhoneNumber($phones[$index])."</td>";
            if (in_array($emails[$index],$unique_email))
                $out_str .= "<td class='ad-duplicate'>כפילות</td>";
            else {
                array_push($unique_email, $emails[$index]);
                $out_str .= "<td></td>";
            }        
            $out_str .= "<td>";
            $order_index = $this->findInArray($order_details['email'], $emails[$index]);
            if ($order_index != -1)
                $out_str .= "שולם";
            $out_str .= "</td>";
            $out_str .= "<td>";
            if ($order_index != -1)
                $out_str .= $order_details['quantity'][$order_index];
            $out_str .= "</td>";
            $out_str .= "<td>";
            if ($order_index != -1)
                $out_str .= $order_details['product'][$order_index];
            $out_str .= "</td>";

            $out_str .="</tr>";
        }

        $out_str .= "</tbody></table>";

        $out_str .= "</div>";
        if ($asString)
            return $out_str;
        echo $out_str;
        return "";
    }

    protected function findInArray($hay, $needle) {
        for($index = 0; $index < sizeof($hay); $index++) {
            if ($hay[$index] == $needle)
                return $index;
        }
        return -1;
    }

    /**
     * get the orders from WooCommerce (only paid ones)
     */
    protected function getWoocommerceOrders() {
        $args = array(
            'status' => 'processing',
            'limit' => -1,
        );
        $results = wc_get_orders($args);
        $numOrders = sizeof($results);

        $resultEmails = array();
        $resultQuantity = array();
        $resultProduct = array();
        $total = 0;
        for ($index = 0 ; $index < $numOrders; $index++) {
            $order = new WC_Order($results[$index]);
            array_push($resultEmails, $order->get_billing_email());
            $items = $order->get_items(); 
            foreach ($items as $key => $product ) {
                $order_item = new WC_Order_Item_Product($key);                    
                $quantity =  $order_item->get_quantity();
                $total += $quantity;
                array_push($resultQuantity, $quantity);
                array_push($resultProduct, $product->get_name());
            }
        }        
        return array('email'=>$resultEmails, 'quantity'=>$resultQuantity, 'product'=>$resultProduct, 'total'=>$total);
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

    /**
     * Gets an array of users from the registration form
     */
    public function getMembersDetails($db) {
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
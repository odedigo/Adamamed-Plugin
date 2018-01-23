<?php
/*
    Access to the DB
  
    Developer: Oded Cnaan
    Dec 2017
*/
class Adamamed_DB {

   /**
     * Query MySQL DB for its version
     * @return string|false
     */
    public function getMySqlVersion() {
        global $wpdb;
        $rows = $wpdb->get_results('select version() as mysqlversion');
        if (!empty($rows)) {
             return $rows[0]->mysqlversion;
        }
        return false;
    }
  
  /**
    * Gets an array of relevant values for stats out of the Registration Details form
    * @return array
    */
    public function getRegistrationDetails($fields_to_add_user = null) {
      global $wpdb;
      $table = 'wp_db7_forms';
      $detailsFormId = '3872';
      $rows = $wpdb->get_results('SELECT * FROM '. $table. ' WHERE form_post_id='.$detailsFormId.' ORDER BY form_date ASC');
      $data = array();
      foreach ($rows as $row) {
        $form_data  = unserialize( $row->form_value );
        unset($form_data['cfdb7_status']);
        unset($form_data['mc4wp_checkbox']);  
        if ($fields_to_add_user != null) {
          $id = "<span class='stats-value'>". $form_data['your-name']." - </span>";
          foreach ($fields_to_add_user as $field) {
            if ($form_data[$field] != "")
              $form_data[$field] = $id . $form_data[$field];
          }
        }
        array_push($data, $form_data);
      }
      return $data;
    }  

    /**
     * Gets the number of details forms submitted
     */
    public function getNumberOfDetailsForms() {
      global $wpdb;
      $table = 'wp_db7_forms';
      $detailsFormId = '3872';
      $wpdb->query('SELECT form_post_id FROM '. $table. ' WHERE form_post_id='.$detailsFormId);       
      return $wpdb->num_rows;
    }

    /******************************* HELPER FORM ***********************/

    public function getHelpers() {
      global $wpdb;
      $table = 'wp_db7_forms';
      $detailsFormId = '1302';
      $rows = $wpdb->get_results('SELECT * FROM '. $table. ' WHERE form_post_id='.$detailsFormId);
      $data = array();
      foreach ($rows as $row) {
        $form_data  = unserialize( $row->form_value );
        unset($form_data['cfdb7_status']);
        unset($form_data['mc4wp_checkbox']);  
        array_push($data, $form_data);
      }
      return $data;
    }  

        /**
     * Gets the number of details forms submitted
     */
    public function getNumberOfHelpersForms() {
      global $wpdb;
      $table = 'wp_db7_forms';
      $detailsFormId = '1302';
      $wpdb->query('SELECT form_post_id FROM '. $table. ' WHERE form_post_id='.$detailsFormId);       
      return $wpdb->num_rows;
    }

     /******************************* MANUAL RESULTS ***********************/

    /**
     * Get all manual orders
     */  
    public function getManualOrders() {
      global $wpdb;
      $table = 'wp_manual_orders';
      $rows = $wpdb->get_results("SELECT * FROM ".$table." ORDER BY 'date'");
      return $rows;
    }

    public function deleteManualOrder($id) {
      global $wpdb;
      $table = 'wp_manual_orders';

      $wpdb->delete($table, array('order_id' => $id));
    }

    public function insertManualOrder($data) {
      global $wpdb;
      $table = 'wp_manual_orders';

      $data['comment'] = str_replace('\"',"",$data['comment']);

      $wpdb->insert($table, array( 
        'email' => $wpdb-> _escape($data['email']),
        'quantity' => $wpdb-> _escape($data['quantity']),
        'product' => $wpdb-> _escape($data['product']),
        'date' => $wpdb-> _escape($data['date']),
        'reference' => $wpdb-> _escape($data['reference']),
        'comment' => $wpdb-> _escape($data['comment'])
      ));
      return $wpdb->insert_id;
    }

    public function updateManualOrder($id, $data) {
      global $wpdb;
      $table = 'wp_manual_orders';

      $data['comment'] = str_replace('\"',"",$data['comment']);

      $wpdb->update($table,array( 
        'email' => $wpdb-> _escape($data['email']),
        'quantity' => $wpdb-> _escape($data['quantity']),
        'product' => $wpdb-> _escape($data['product']),
        'date' => $wpdb-> _escape($data['date']),
        'reference' => $wpdb-> _escape($data['reference']),
        'comment' => $wpdb-> _escape($data['comment'])
        ),
        array('order_id' => $id)
      );
    }

}
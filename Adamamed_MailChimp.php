<?php

class Adamamed_MailChimp {

    protected $mailChimp_form_id = 4502;

    public function beforeFormSent($form_tag) {
        $form = WPCF7_Submission::get_instance();
        if ( $form ) {
            $black_list   = array('_wpcf7', '_wpcf7_version', '_wpcf7_locale', '_wpcf7_unit_tag',
                                    '_wpcf7_is_ajax_call','cfdb7_name', '_wpcf7_container_post','_wpcf7cf_hidden_group_fields',
                                    '_wpcf7cf_hidden_groups', '_wpcf7cf_visible_groups', '_wpcf7cf_options');

            $data = $form->get_posted_data();
            $form_id = $form_tag->id();
            if ($form_id != $this->mailChimp_form_id)
                return;

            $form_data   = array();
            foreach ($data as $key => $d) {
                if ( !in_array($key, $black_list )) {
                    $tmpD = $d;
                    if ( ! is_array($d) ){
                        $bl   = array('\"',"\'",'/','\\');
                        $wl   = array('&quot;','&#039;','&#047;', '&#092;');
                        $tmpD = str_replace($bl, $wl, $tmpD );
                    }
                    $form_data[$key] = $tmpD;
                }
            }

            $d = print_r($form_data,true);
            $name = $form_data['your-name'];
            $email = $form_data['your-email'];

            $mail = $form_tag->prop( 'mail' ); // returns array 
            // add content to email body
            $mail['body'] .= 'Data:<br>'.$name." [".$email."]";
            // set mail property with changed value(s)
            $form_tag->set_properties( array( 'mail' => $mail ) );
        }
    }

}
<?php

class Adamamed_MailChimp {

    protected $mailChimp_form_id = 3872;
    protected $fields = array('email'=>'your-email','name'=>'your-name');

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
            $name = $form_data[$this->fields['name']];
            $email = $form_data[$this->fields['email']];

            $all_names = $this->getFirstFamilityName($name);
            if ($all_names == null)
                return;

            $msg = 'Not submitted';
            if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL) === false){
                $msg = $this->sumbitToMailChimp($all_names['fname'],$all_names['lname'],$email);
            }
        }
    }

    protected function sumbitToMailChimp($first_name, $last_name, $email) {
        // MailChimp API credentials
        $apiKey = '4f0e7517c6913ecd164fc0567a08d83c-us9';
        $listID = '96fda3d4c8';

        // MailChimp API URL
        $memberID = md5(strtolower($email));
        $dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
        $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listID . '/members/' . $memberID;

        // member information
        $json = json_encode([
            'email_address' => $email,
            'status'        => 'subscribed',
            'merge_fields'  => [
                'FNAME'     => $first_name,
                'LNAME'     => $last_name
            ]
        ]);

        // send a HTTP POST request with curl
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // store the status message based on response code
        if ($httpCode == 200) {
            $msg = '<p style="color: #34A853">You have successfully subscribed to CodexWorld.</p>';
        } else {
            switch ($httpCode) {
                case 214:
                    $msg = 'You are already subscribed.';
                    break;
                default:
                    $msg = 'Some problem occurred, please try again.';
                    break;
            }
            $msg = '<p style="color: #EA4335">'.$msg.'</p>';
        }
        return $msg;
    }

    protected function getFirstFamilityName($form_name) {
        $names = explode(' ',$form_name);
        $the_names = array();
        if (sizeof($names) == 0) {
            return null;
        }
        else if (sizeof($names) == 1) {
            $the_names['fname'] = $names[0];
            $the_names['lname'] = $names[0]; // use first name as last name
            return $the_names;
        }
        else if (sizeof($names) == 2) {
            $the_names['fname'] = $names[0];
            $the_names['lname'] = $names[1];
            return $the_names;
        }
        $isFirst = true;
        // first name is the first item
        $the_names['fname'] = $names[0];
        $fname = '';
        foreach ($names as $item) {
            if ($isFirst) {
                $isFirst = false;
            }
            else {
                $fname .= $item." ";
            }
        }
        // last name is all the rest
        $the_names['lname'] = $fname;
        return $the_names;    
    }

    protected function addMessageToBody($form_tag, $msg) {
        $mail = $form_tag->prop( 'mail' ); // returns array 
        // add content to email body
        $mail['body'] = 'Message: '.$msg. "\r\n".$mail['body'];
        // set mail property with changed value(s)
        $form_tag->set_properties( array( 'mail' => $mail ) );
    }

}
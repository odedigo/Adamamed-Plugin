<?php


class Adamamed_Google_Contacts {

    /*protected $google_client_id = '740537959806-vjs4e14lij1ts1ntcdn4n7ve0809o5p1.apps.googleusercontent.com';
    protected $google_client_secret = 'PCW0AvshAYCvxCMRgUFX_UMp';
    protected $google_redirect_uri = 'http://www.adamamed.com/wp-admin/admin.php?page=AdContacts';*/

//adamamed-g-contacts
//Client ID	: 740537959806-vjs4e14lij1ts1ntcdn4n7ve0809o5p1.apps.googleusercontent.com

    public function getGoogleContacts($plugin, $asString = false) {
        session_start();

        $date = date('H:i d-m-Y');        
        $out_str = "<div class='wrap'><h2>רשימת אנשי קשר מגוגל</h2><p>ייצוא אנשי הקשר מחשבון הגוגל של רפואה מפרי האדמה&nbsp&nbsp($date)</p>";        
        if (!$asString) {
            $out_str .= "<p><button type='button' onclick='javascript:exportAsDoc(\"export_stats=1\");'>ייצוא לקובץ Word</button>&nbsp;&nbsp;";
            $out_str .= "<button type='button' onclick='javascript:exportAsDoc(\"export_stats=2\");'>ייצוא לקובץ Excel</button></p>";
        }

        $contacts = $this->getContacts();

        if ($asString)
            return $out_str;
        echo $out_str;
        return "";    
    }

    protected function getContacts() {
    }
}
?>
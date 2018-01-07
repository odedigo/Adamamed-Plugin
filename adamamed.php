<?php
/*
   Plugin Name: Adamamed
   Plugin URI: http://wordpress.org/extend/plugins/adamamed/
   Version: 0.1
   Author: Oded
   Description: Adamamed tools
   Text Domain: adamamed
   License: GPLv3
  */

/*
    "WordPress Plugin Template" Copyright (C) 2017 Michael Simpson  (email : michael.d.simpson@gmail.com)

    This following part of this file is part of WordPress Plugin Template for WordPress.

    WordPress Plugin Template is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    WordPress Plugin Template is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Contact Form to Database Extension.
    If not, see http://www.gnu.org/licenses/gpl-3.0.html
*/

$Adamamed_minimalRequiredPhpVersion = '5.0';
include_once('Adamamed_MailChimp.php');

/**
 * Check the PHP version and give a useful error message if the user's version is less than the required version
 * @return boolean true if version check passed. If false, triggers an error which WP will handle, by displaying
 * an error message on the Admin page
 */
function Adamamed_noticePhpVersionWrong() {
    global $Adamamed_minimalRequiredPhpVersion;
    echo '<div class="updated fade">' .
      __('Error: plugin "Adamamed" requires a newer version of PHP to be running.',  'adamamed').
            '<br/>' . __('Minimal version of PHP required: ', 'adamamed') . '<strong>' . $Adamamed_minimalRequiredPhpVersion . '</strong>' .
            '<br/>' . __('Your server\'s PHP version: ', 'adamamed') . '<strong>' . phpversion() . '</strong>' .
         '</div>';
}


function Adamamed_PhpVersionCheck() {
    global $Adamamed_minimalRequiredPhpVersion;
    if (version_compare(phpversion(), $Adamamed_minimalRequiredPhpVersion) < 0) {
        add_action('admin_notices', 'Adamamed_noticePhpVersionWrong');
        return false;
    }
    return true;
}


/**
 * Initialize internationalization (i18n) for this plugin.
 * References:
 *      http://codex.wordpress.org/I18n_for_WordPress_Developers
 *      http://www.wdmac.com/how-to-create-a-po-language-translation#more-631
 * @return void
 */
function Adamamed_i18n_init() {
    $pluginDir = dirname(plugin_basename(__FILE__));
    load_plugin_textdomain('adamamed', false, $pluginDir . '/languages/');
}


//////////////////////////////////
// Run initialization
/////////////////////////////////

// Initialize i18n
add_action('plugins_loadedi','Adamamed_i18n_init');

// Run the version check.
// If it is successful, continue with initialization for this plugin
if (Adamamed_PhpVersionCheck()) {
    // Only load and run the init function if we know PHP version can parse it
    include_once('adamamed_init.php');
    Adamamed_init(__FILE__);
}


/******************************************************************************************************/
/****************** FUNCTIONS *******************/

function Adamamed_showStockQuantity($atts) {
    $a = shortcode_atts( array(
        'id' => 0,
    ), $atts );
    $product = wc_get_product($a['id']);
    if ($a['id'] != 0 && $product != null)
        echo "<span class='stock-quantity'>".wc_get_stock_html( $product )."</span>";
    else
        echo "Product ". $a['id']. " not found";
}
add_shortcode( 'showStockQuantityTag', 'Adamamed_showStockQuantity' );

/* Download */

function downloadFile($data, $fileName, $exportType) {
    if ($exportType == '1')
        $fileName .= ".doc";
    else
        $fileName .= ".xls";
    $prefix = getDownloadPrefix();
    $suffix = getDownloadSuffix();
    $data = $prefix . $data . $suffix;
    ob_clean();
    $fsize = strlen($data);
    header("Pragma: public");
    header("Content-Description: File Transfer");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public");
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    header('Content-Type: application/octet-stream');
    //header('Content-Type: text/html');
    //header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    //header("Content-Type: application/vnd.ms-excel");
    header("Content-Transfer-Encoding: binary"); 
    header("Pragma: no-cache");
    header("Content-Length: " . $fsize);
    print $data;
    ob_flush();
  }

  function getDownloadPrefix() {
    $str = "
    <html xmlns='http://www.w3.org/1999/xhtml' DIR='RTL'>    
    <head>
    <meta http-equiv='Content-Language' content='en-us' />
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
    <title></title>
    </head>
    <body style='background-color: white;color:black;font-family:Arial, Helvetica, sans-serif;font-size:12pt;' >    
    <div dir='rtl' style='text-align: right;'>
    ";
    return $str;
  }

  function getDownloadSuffix() {
    $str = "</div></body></html>";
    return $str;
  }

  /* Contact Form 7 Integration  */
  function cf7_beforeFormSent($form_tag) {
    $mp = new Adamamed_MailChimp();
    $mp->beforeFormSent($form_tag);
  }
  add_action( 'wpcf7_before_send_mail', 'cf7_beforeFormSent');

<?php

include_once('Adamamed_InstallIndicator.php');

class Adamamed_LifeCycle extends Adamamed_InstallIndicator {

    public function install() {

        // Initialize Plugin Options
        $this->initOptions();

        // Initialize DB Tables used by the plugin
        $this->installDatabaseTables();

        // Other Plugin initialization - for the plugin writer to override as needed
        $this->otherInstall();

        // Record the installed version
        $this->saveInstalledVersion();

        // To avoid running install() more then once
        $this->markAsInstalled();
    }

    public function uninstall() {
        $this->otherUninstall();
        $this->unInstallDatabaseTables();
        $this->deleteSavedOptions();
        $this->markAsUnInstalled();
    }

    /**
     * Perform any version-upgrade activities prior to activation (e.g. database changes)
     * @return void
     */
    public function upgrade() {
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=105
     * @return void
     */
    public function activate() {
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=105
     * @return void
     */
    public function deactivate() {
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     * @return void
     */
    protected function initOptions() {
    }

    public function addActionsAndFilters() {
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Called by install() to create any database tables if needed.
     * Best Practice:
     * (1) Prefix all table names with $wpdb->prefix
     * (2) make table names lower case only
     * @return void
     */
    protected function installDatabaseTables() {
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Drop plugin-created tables on uninstall.
     * @return void
     */
    protected function unInstallDatabaseTables() {
    }

    /**
     * Override to add any additional actions to be done at install time
     * See: http://plugin.michael-simpson.com/?page_id=33
     * @return void
     */
    protected function otherInstall() {
    }

    /**
     * Override to add any additional actions to be done at uninstall time
     * See: http://plugin.michael-simpson.com/?page_id=33
     * @return void
     */
    protected function otherUninstall() {
    }

    /**
     * Puts the configuration page in the Plugins menu by default.
     * Override to put it elsewhere or create a set of submenus
     * Override with an empty implementation if you don't want a configuration page
     * @return void
     */
    public function addAdminMenus() {
        //$this->addSettingsSubMenuPageToPluginsMenu();
        //$this->addSettingsSubMenuPageToSettingsMenu();
        $this->addSettingsSubMenuPageToMainMenu();
        //$this->addToolsMenu();
    }


    protected function requireExtraPluginFiles() {
        require_once(ABSPATH . 'wp-includes/pluggable.php');
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }

    /**
     * @return string Slug name for the URL to the Setting page
     * (i.e. the page for setting options)
     */
    protected function getSettingsSlug() {
        return get_class($this) . 'Settings';
    }

    protected function addToolsMenu() {
        /*$pageTitle = "Debug Mode";
        add_management_page($pageTitle,$pageTitle , 'administrator', 'debug_modifier', array(&$this, 'adamamed_debugMode') );*/
    }

    protected function addSettingsSubMenuPageToMainMenu() {
        $this->requireExtraPluginFiles();
        $displayName = $this->getPluginDisplayName();
        $subMenuRegStats = 'טופס הרשמה';
        $subMenuHelpStats = 'צוות עזר';
        $subMenuMailList = "ריכוז מיילים";
        add_menu_page($displayName,
                      $displayName,
                      'administrator',
                      $this->getSettingsSlug(),
                      null,//array(&$this, 'adamamed_mainPage'),
                      null,
                      4);        
        add_submenu_page(  $this->getSettingsSlug(),
                         $subMenuRegStats,
                         $subMenuRegStats,
                         'administrator',
                         $this->getSettingsSlug(),
                         array(&$this, 'adamamed_statsPage'));
        add_submenu_page(  $this->getSettingsSlug(),
                         $subMenuHelpStats,
                         $subMenuHelpStats,
                         'administrator',
                         "HelpersStats",
                         array(&$this, 'adamamed_statsHelperPage'));
        add_submenu_page(  $this->getSettingsSlug(),
                         $subMenuMailList,
                         $subMenuMailList,
                         'administrator',
                         "AdMailList",
                         array(&$this, 'adamamed_statsMailistPage'));
    }

    /**
     * Add a widget to the dashboard.
     *
     * This function is hooked into the 'wp_dashboard_setup' action below.
     */
    public function addAdminDashboardWidget() {        
	    wp_add_dashboard_widget(
                 'adamamed_widget',         // Widget slug.
                 'רפואה מפרי האדמה',         // Title.
                 array(&$this,'drawDashboardWidget') // Display function.
        );	
	    wp_add_dashboard_widget(
            'debug_mod_widget',         // Widget slug.
            'Debug Mode',         // Title.
            array(&$this,'drawDebugModeDashboardWidget'),
            array(&$this,'drawDebugModeDashboardWidget_handle') // Display function.
        //add_action('wp_dashboard_setup', 'drawDebugModeDashboardWidget_handle');
   );	
}

    /**
     * Create the function to output the contents of our Dashboard Widget.
     */
    public function drawDashboardWidget() {
        $db = new Adamamed_DB();
        $countDetails = $db->getNumberOfDetailsForms();
        $countHelpers = $db->getNumberOfHelpersForms();
        echo "נמצאו "."<span class='stats-value'>".$countDetails."</span>"." טפסי הרשמה עם פרטים<br>";
        echo "ו "."<span class='stats-value'>".$countHelpers."</span>"." טפסי הרשמה לצוות עזר";
    }        

    public function drawDebugModeDashboardWidget() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'adamamed'));
        }

        $tools = new Adamamed_ToolsDebugPage();
        $tools->doPage();
    }

    public function drawDebugModeDashboardWidget_handle() {
        $tools = new Adamamed_ToolsDebugPage();
        $tools->doPage();
    }

    protected function addSettingsSubMenuPageToPluginsMenu() {
        $this->requireExtraPluginFiles();
        $displayName = $this->getPluginDisplayName();
        add_submenu_page('plugins.php',
                         $displayName,
                         $displayName,
                         'manage_options',
                         $this->getSettingsSlug(),
                         array(&$this, 'settingsPage'));
    }


    protected function addSettingsSubMenuPageToSettingsMenu() {
        $this->requireExtraPluginFiles();
        $displayName = $this->getPluginDisplayName();
        add_options_page($displayName,
                         $displayName,
                         'manage_options',
                         $this->getSettingsSlug(),
                         array(&$this, 'settingsPage'));
    }

    /**
     * @param  $name string name of a database table
     * @return string input prefixed with the WordPress DB table prefix
     * plus the prefix for this plugin (lower-cased) to avoid table name collisions.
     * The plugin prefix is lower-cases as a best practice that all DB table names are lower case to
     * avoid issues on some platforms
     */
    protected function prefixTableName($name) {
        global $wpdb;
        return $wpdb->prefix .  strtolower($this->prefix($name));
    }


    /**
     * Convenience function for creating AJAX URLs.
     *
     * @param $actionName string the name of the ajax action registered in a call like
     * add_action('wp_ajax_actionName', array(&$this, 'functionName'));
     *     and/or
     * add_action('wp_ajax_nopriv_actionName', array(&$this, 'functionName'));
     *
     * If have an additional parameters to add to the Ajax call, e.g. an "id" parameter,
     * you could call this function and append to the returned string like:
     *    $url = $this->getAjaxUrl('myaction&id=') . urlencode($id);
     * or more complex:
     *    $url = sprintf($this->getAjaxUrl('myaction&id=%s&var2=%s&var3=%s'), urlencode($id), urlencode($var2), urlencode($var3));
     *
     * @return string URL that can be used in a web page to make an Ajax call to $this->functionName
     */
    public function getAjaxUrl($actionName) {
        return admin_url('admin-ajax.php') . '?action=' . $actionName;
    }

}

<?php

class WtiPress {
  
  public $settings;
  public $network;
  
  function __construct() {
    add_action('admin_menu', array($this, 'administration_menu'));
    $this->settings = new Settings();
    if(isset($this->settings->api_key)) {
      // fetch project settings
      $this->network = new Network($this->settings->api_key);
    }
    // Process post requests
    if(!empty($_POST)) {
      add_action('init', array($this,'process_forms'));
    }    
  }
  
  function administration_menu() {
    add_menu_page("WTIpress", "WTIpress", 'manage_options', basename(WTIPRESS_PLUGIN_PATH).'/menu/setup.php',null, WTIPRESS_PLUGIN_URL . '/res/img/icon16.png');        
    add_submenu_page(basename(WTIPRESS_PLUGIN_PATH).'/menu/setup.php', "Setup", "Setup", 'manage_options', basename(WTIPRESS_PLUGIN_PATH).'/menu/setup.php');
    add_submenu_page(basename(WTIPRESS_PLUGIN_PATH).'/menu/setup.php', "Setup", "Translations", 'manage_options', basename(WTIPRESS_PLUGIN_PATH).'/menu/translations.php');
    
  }
  
  function process_forms() {
    $this->settings->api_key = $_POST['wti_api_key'];
    $this->settings->save();
  }
  
}

?>
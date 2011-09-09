<?php

class WtiPress {
  
  private $settings;
  
  function __construct() {
    add_action('admin_menu', array($this, 'administration_menu'));
  }
  
  function administration_menu() {
    add_menu_page("WTIpress", "WTIpress", 'manage_options', basename(WTIPRESS_PLUGIN_PATH).'/menu/languages.php',null, WTIPRESS_PLUGIN_URL . '/res/img/icon16.png');        
  }
}

?>
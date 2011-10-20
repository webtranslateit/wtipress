<?php

class Settings {
  
  public $api_key;
  
  function __construct() {
    $data = get_option('wtipress_settings');
    $this->api_key = $data['api_key'];
  }
  
  function save() {
    $data['api_key'] = $this->api_key;
    update_option('wtipress_settings', $data);
  }
}

?>
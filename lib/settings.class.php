<?php

class Settings {
  
  public $api_key;
  public $project;
  
  function __construct() {
    $data = get_option('wtipress_settings');
    $this->api_key = $data['api_key'];
  }
  
  function save() {
    $data['api_key'] = $this->api_key;
    update_option('wtipress_settings', $data);
  }
  
  function get_project() {
    if (!isset($this->project)) {
      $network = new Network($this->api_key);
      $this->project = $network->project();
    }
    return $this->project;
  }
}

?>
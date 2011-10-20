<?php

class Setting {
  
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
  
  function push_post($post_id) {
    $network = new Network($this->api_key);
    $network->push_post($post_id);
  }
  
  // Install DB
  static function install() {
    global $wpdb;
    
    if(get_option("wtipress_db_version") != WTIPRESS_DB_VERSION) {
      $table_name = $wpdb->prefix . "wtipress_posts";

      $sql = "CREATE TABLE " . $table_name . " (
    	  id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    	  element_type VARCHAR(32) NOT NULL DEFAULT 'post_post',
    	  element_id BIGINT NOT NULL,
    	  language_code VARCHAR(7) NOT NULL,
    	  post_content LONGTEXT NOT NULL,
    	  post_title TEXT NOT NULL,
    	  post_excerpt TEXT NOT NULL,
    	  post_name VARCHAR(200) NOT NULL,
    	  post_content_filtered TEXT NOT NULL,
    	  created_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    	  updated_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    	  last_pushed_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    	  last_pulled_at DATETIME,
    	  wti_file_id BIGINT NOT NULL,
    	  wti_checksum VARCHAR(40),
    	  UNIQUE KEY element_type_id_lang (element_type,element_id,language_code)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);
      add_option("wtipress_db_version", WTIPRESS_DB_VERSION);
    }
  }
  
  function update_db_ckeck() {
    if(get_site_option('wtipress_db_version') != WTIPRESS_DB_VERSION) {
      Setting::install();
      update_option('wtipress_db_version', WTIPRESS_DB_VERSION);
    }
  }
}

?>
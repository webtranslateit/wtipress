<?php

class Setting {
  
  public $api_key;
  public $action_missing_translation;
  public $project_name;
  public $project_id;
  public $language_negociation_format;
  
  function __construct() {
    $data = get_option('wtipress_settings');
    $this->api_key = $data['api_key'];
    $this->project_name = $data['project_name'];
    $this->project_id = $data['project_id'];
    $this->action_missing_translation = $data['action_missing_translation'];
    $this->language_negociation_format = $data['language_negociation_format'];
  }
  
  function save() {
    $data['api_key'] = $this->api_key;
    $data['project_name'] = $this->project_name;
    $data['project_id'] = $this->project_id;
    $data['action_missing_translation'] = $this->action_missing_translation;
    $data['language_negociation_format'] = $this->language_negociation_format;
    update_option('wtipress_settings', $data);
  }
  
  function refresh_settings() {
    $network = new Network($this->api_key);
    $info = $network->get_project_info();
    $this->project_name = $info['project']['name'];
    $this->project_id = $info['project']['id'];
    $this->save();
    // persist source language
    $l = new Language(NULL, $info['project']['source_locale']['code'], $info['project']['source_locale']['name'], true);
    $l->save();
    // persist target languages
    foreach($info['project']['target_locales'] as $target_locale) {
      // except source language
      if ($target_locale['code'] != $info['project']['source_locale']['code']) {
        $l = new Language(NULL, $target_locale['code'], $target_locale['name']);
        $l->save();
      }
    }
  }
  
  function push_post($post_id) {
    $network = new Network($this->api_key);
    $network->push_post($post_id);
  }
  
  function pull_post($post_id) {
    $network = new Network($this->api_key);
    $network->pull_post($post_id);
  }
  
  function add_language($language_code) {
    $network = new Network($this->api_key);
    $network->add_language($language_code);
    $this->refresh_settings();
  }
  
  function remove_language($language_code) {
    $language = Language::get_by_code($language_code);
    if ($language != NULL) {
      $network = new Network($this->api_key);
      $language->delete();
      $network->remove_language($language_code);
      $this->refresh_settings();
    }
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
    	  language_id BIGINT NOT NULL,
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
    	  UNIQUE KEY element_type_id_lang (element_type,element_id,language_id)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);

      $table_name = $wpdb->prefix . "wtipress_languages";

      $sql = "CREATE TABLE " . $table_name . " (
    	  id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    	  code VARCHAR(7) NOT NULL,
    	  name VARCHAR(250) NOT NULL,
    	  source BOOL NOT NULL DEFAULT FALSE
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

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
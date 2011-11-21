<?php

class Language {
  
  public $id;
  public $code;
  public $name;
  public $source;
  
  function __construct($id=NULL, $code, $name, $source=false) {
    $this->id = $id;
    $this->code = $code;
    $this->name = $name;
    $this->source = $source;
  }
  
  static function get_by_code($code) {
    global $wpdb;
    $query = $wpdb->prepare("SELECT * FROM ". $wpdb->prefix . "wtipress_languages WHERE code=%s", array($code));
    $result = $wpdb->get_row($query);
    if ($result) {
      return new Language($result->id, $code, $result->name, $result->source);
    }
    else {
      return NULL;
    }
  }
  
  static function get_source_language() {
    global $wpdb;
    $query = $wpdb->prepare("SELECT * FROM ". $wpdb->prefix . "wtipress_languages WHERE source=%d", array(true));
    $result = $wpdb->get_results($query);
    return $result;
  }
  
  static function get_all($source=false) {
    global $wpdb;
    $query = $wpdb->prepare("SELECT * FROM ". $wpdb->prefix . "wtipress_languages WHERE source=%d", array($source));
    $result = $wpdb->get_results($query);
    return $result;
  }
  
  static function get_all_as_array($source=false) {
    $lang = Language::get_all();
    function get_code($o){
      return $o->code;
    }
    return array_map("get_code", $lang);
  }
  
  function save() {
    global $wpdb;
    if(Language::get_by_code($this->code)) {
      $wpdb->update($wpdb->prefix.'wtipress_languages', array(
        'code' => $this->code,
        'name' => $this->name,
        'source' => $this->source
      ),
      array(
        'code' => $this->code
      )
    );
    }
    else {
      $wpdb->insert($wpdb->prefix.'wtipress_languages', array(
        'code' => $this->code,
        'name' => $this->name,
        'source' => $this->source
        )
      );
    }
  }
  
  function delete() {
    global $wpdb;
    $wpdb->query("DELETE FROM ".$wpdb->prefix."wtipress_languages WHERE id = '".$this->id."'");
    $wpdb->get_results;
  }
  
}

?>
<?php

class Network {
    
  public $api_key;
  
  function __construct($api_key = null) {
    $this->api_key = $api_key;
  }
  
  // call Project API
  function project() {
    $snoopy = new Snoopy();
    $snoopy->curl_path = "/usr/bin/curl";
    if($snoopy->fetch("https://webtranslateit.com/api/projects/" . $this->api_key . ".json")) {
      $p = json_decode($snoopy->results, true);
      $project = new Project($p['project']['name'], $p['project']['source_locale']['name'], $p['project']['target_locales']);
      return $project;
    }
    else {
      print "Snoopy: error while fetching document: ".$snoopy->error."\n";
    }
  }
  
  function http_connection() {
    
  }
  
}
?>

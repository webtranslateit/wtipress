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
    if($snoopy->fetch("https://webtranslateit.com/api/projects/" . $this->api_key . ".yaml")) {
      return spyc_load($snoopy->results);
    }
    else {
      print "Snoopy: error while fetching document: ".$snoopy->error."\n";
      var_dump($snoopy);
    }
  }
  
  function http_connection() {
    
  }
  
}
?>

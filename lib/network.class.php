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
  
  function push_post($post_id) {
    $post = get_post($post_id);
    // create pseudo language file
    $array = array('en' => array('post_title' => $post->post_title, 'post_excerpt' => $post->post_excerpt, 'post_content' => $post->post_content, 'post_name' => $post->post_name, 'post_content_filtered' => $post->post_content_filtered));
    $dumper = new sfYamlDumper();
    $yaml = $dumper->dump($array);
    
    $myFile = wp_upload_dir() . "test.yml";
    $fh = fopen($myFile, 'w') or die("can't open file");
    fwrite($fh, $yaml);
    fclose($fh);
    
    // create entries for each language in wtipress table
    foreach($this->project()->target_locales as $target_locale) {
      $translation = new Translation($post, $locale['code']);
      $translation->save();
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://webtranslateit.com/api/projects/" . $this->api_key . "/files");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array("file" => "@".wp_upload_dir() . "test.yml", 'name' => $post->post_date.'-'.$post->post_name.'.yml')); 
    $response = curl_exec($ch);
    
    // TODO: Delete file
  }
  
  function pull_post($post_id) {
    $post = get_post($post_id);
    // pull post from wti for each language
    
    // update entries for each language in wtipress table
  }
  
}
?>

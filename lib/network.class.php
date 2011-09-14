<?php

class Network {
    
  public $api_key;
  
  function __construct($api_key = null) {
    $this->api_key = $api_key;
  }
  
  // call Project API
  function project() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, "https://webtranslateit.com/api/projects/" . $this->api_key . ".json");
    $response = curl_exec($ch);
    $p = json_decode($response, true);
    $project = new Project($p['project']['name'], $p['project']['source_locale']['code'], $p['project']['target_locales']);
    return $project;
  }
  
  function push_post($post_id) {
    $post = get_post($post_id);
    // create pseudo language file
    $file_path = wp_upload_dir();
    $file_path = $file_path['path']. "/". $post->post_date.'-'.$post->post_name.'.wordpress';
    $array = array('post_title' => $post->post_title, 'post_excerpt' => $post->post_excerpt, 'post_content' => $post->post_content, 'post_name' => $post->post_name, 'post_content_filtered' => $post->post_content_filtered);
    $dumper = new sfYamlDumper();
    $yaml = $dumper->dump($array);
    
    $handle = fopen($file_path, 'w') or die("can't open file");
    fwrite($handle, $yaml);
    fclose($handle);
    
    $translation = Translation::get_translation($post, $this->project()->source_locale);
    if ($translation->wti_file_id != NULL) {
      // already have translations in DB? Update.
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "https://webtranslateit.com/api/projects/" . $this->api_key . "/files/" . $translation->wti_file_id . "/locales/" . $this->project()->source_locale);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
      curl_setopt($ch, CURLOPT_POSTFIELDS, array("file" => "@".$file_path, 'name' => $post->post_date.'-'.$post->post_name.'.wordpress')); 
      $response = curl_exec($ch);
      
      // update entries for each language in wtipress table
      foreach($this->project()->target_locales as $target_locale) {
        $translation = Translation::get_translation($post, $target_locale['code']);
        $translation->last_pushed_at = date("Y-m-d H:i:s", time());
        $translation->updated_at = date("Y-m-d H:i:s", time());
        $translation->save();
      }
    }
    else {
      // create
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "https://webtranslateit.com/api/projects/" . $this->api_key . "/files");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, array("file" => "@".$file_path, 'name' => $post->post_date.'-'.$post->post_name.'.wordpress')); 
      $response = curl_exec($ch);
      // create entries for each language in wtipress table
      foreach($this->project()->target_locales as $target_locale) {
        $translation = new Translation($post, $target_locale['code']);
        $translation->wti_file_id = $response;
        $translation->save();
      }
    }
    // unlink($file_path);
  }
  
  function pull_post($post_id) {
    $post = get_post($post_id);
    // pull post from wti for each language
    
    // update entries for each language in wtipress table
  }
  
}
?>

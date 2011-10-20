<?php
  require_once WTIPRESS_PLUGIN_PATH . '/wtipress.php';

  if(isset($wtipress->settings->api_key)) {
    echo '<div class="updated fade"><p><strong>WTIpress is setup correctly</strong>';
    echo ' and can sync with the project “<a href="https://webtranslateit.com/projects/'.$wtipress->settings->project_id.'">'.$wtipress->settings->project_name.'</a>” on WebTranslateIt.com. Good job!</p></div>';
  }
?>

<div class="wrap">
  <div id="icon-options-general" class="icon32"><br /></div>
  <h2>Setup WTIpress</h2>
    
  <h3>WebTranslateIt.com API key</h3>
  <form id="wti_api_key" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
    <?php wp_nonce_field('wti_api_key') ?>            
    <p>
      <input type="text" name="wti_api_key" value="<?php echo $wtipress->settings->api_key ?>" />
      You will find your API key in your project settings<br /><br />
      <input class="button-primary" name="save" value="Save and Refresh" type="submit" />
    </p>
  </form>
  
  <?php
  if(isset($wtipress->settings->api_key)) {
  ?>
  
  <h3>Languages at play</h3>

  <p>
    Tip: Add languages on your <?php echo '<a href="https://webtranslateit.com/projects/'.$wtipress->settings->project_id.'">project on WebTranslateIt.com</a>.'; ?>
    We detected the following:
  </p>
  
  <h4>Source:</h4>
  <?php
  $source_language = Language::get_source_language();
  echo "<ul><li>" . $source_language[0]->name . "</li></ul>";
  ?>
  
  <h4>Target:</h4>
  <?php
  echo "<ul>";
  foreach(Language::get_all() as $language) {
    echo "<li>".$language->name . "</li>";
  }
  echo "</ul>";
}
?>
</div>

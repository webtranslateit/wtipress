<?php
  require_once WTIPRESS_PLUGIN_PATH . '/wtipress.php';
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
      <input class="button" name="save" value="Save" type="submit" />
    </p>
  </form>
  
  <hr />
  
  <?php
  if(isset($wtipress->settings->api_key)) {
    echo "In sync with project " . $wtipress->settings->get_project()->name;
    echo "<br />";
    echo "Source Locale: " . $wtipress->settings->get_project()->source_locale;
    echo "<br />";
    echo "Target Locales: ";
    if (isset($wtipress->settings->get_project()->target_locales)) {
      foreach($wtipress->settings->get_project()->target_locales as &$value) {
        echo $value["name"]." ";
      }
    }
  }
  ?>
</div>

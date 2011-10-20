<?php

class WtiPress {
  
  public $settings;
  public $network;
  
  function __construct() {
    add_action('admin_menu', array($this, 'administration_menu'));
    $this->settings = new Setting();
    $this->admin_warning();
    $this->version_warning();
    if(isset($this->settings->api_key)) {
      // fetch project settings
      $this->network = new Network($this->settings->api_key);
    }
    // Process post requests
    if(!empty($_POST)) {
      add_action('init', array($this,'process_forms'));
    }
    // Set locale
    $this->set_locale();
    add_filter('the_posts', array($this, 'the_posts'));
    
  }
  
  function set_locale() {
    global $locale;
    if(isset($_GET['lang'])) {
      $locale = $_GET['lang'];
    }
    else {
      $l = Language::get_source_language();
      $locale = $l[0]->code;
    }
  }
  
  // Monkey patch to replace posts by their translations
  function the_posts($posts) {
    global $locale;
    $source_locale = Language::get_source_language();
    if ($source_locale[0]->code == $locale){
      return $posts;
    }
    else { // get post translations
      $language = Language::get_by_code($locale);
      $i = 0;
      foreach($posts as $post) {
        $translation = Translation::get_translation($post, $language);
        if ($translation != NULL) {
          $post->post_content = $translation->post_content;
          $post->post_title = $translation->post_title;
          $post->post_excerpt = $translation->post_excerpt;
          $post->post_name = $translation->post_name;
          $post->post_content_filtered = $translation->post_content_filtered;
        }
        $translated_posts[$i] = $post;
        $i++;
      }
      return $translated_posts;
    }
  }
  
  function administration_menu() {
    add_menu_page("WTIpress", "WTIpress", 'manage_options', basename(WTIPRESS_PLUGIN_PATH).'/menu/setup.php',null, WTIPRESS_PLUGIN_URL . '/res/img/icon16.png');        
    add_submenu_page(basename(WTIPRESS_PLUGIN_PATH).'/menu/setup.php', "Setup", "Setup", 'manage_options', basename(WTIPRESS_PLUGIN_PATH).'/menu/setup.php');
    add_submenu_page(basename(WTIPRESS_PLUGIN_PATH).'/menu/setup.php', "Setup", "Translations", 'manage_options', basename(WTIPRESS_PLUGIN_PATH).'/menu/translations.php');
  }
  
  function admin_warning() {
    if (!isset($this->settings->api_key) && !isset($_POST['wti_api_key'])) {
      echo "<div class='updated fade'><p><strong>WTIpress is almost ready.</strong> You must <a href='admin.php?page=wtipress/menu/setup.php'>enter your WebTranslateIt API key</a> for it to work.</p></div>";
    }
  }
  
  function version_warning() {
    global $wp_version;
    if (version_compare($wp_version, WTIPRESS_MIN_WORDPRESS_VERSION, '<')) {
      echo "<div class='updated fade'><p><strong>WTIpress ".WTIPRESS_VERSION." requires WordPress ".WTIPRESS_MIN_WORDPRESS_VERSION." or higher.</strong> Please <a href='http://codex.wordpress.org/Upgrading_WordPress'>upgrade WordPress</a> to a current version.</p></div>";
    }
    if(version_compare(phpversion(), '5', '<')) {
      echo "<div class='updated fade'><p><strong>WTIpress requires PHP 5 or higher.</strong> Please upgrade PHP to a current version.</p></div>";
    }    
  }
  
  function process_forms() {
    if (isset($_POST['wti_api_key'])) {
      $this->settings->api_key = $_POST['wti_api_key'];
      $this->settings->refresh_settings();
    }
    elseif (isset($_POST['wti_post_id'])) {
      if ($_POST['action'] == 'push') {
        $this->settings->push_post($_POST['wti_post_id']);
      }
      elseif ($_POST['action'] == 'pull') {
        $this->settings->pull_post($_POST['wti_post_id']);
      }
    }
  }
}

?>
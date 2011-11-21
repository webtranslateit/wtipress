<?php

class WtiPress {
  
  public $settings;
  public $network;
  
  function __construct() {
    add_action('admin_menu', array($this, 'administration_menu'));
    $this->settings = new Setting();
    $this->warnings();
    if(isset($this->settings->api_key)) {
      // fetch project settings
      $this->network = new Network($this->settings->api_key);
    }
    // Process post requests
    if(!empty($_POST)) {
      add_action('init', array($this, 'process_forms'));
    }
    // Set locale
    if(!defined('WP_ADMIN')){
      $this->set_locale();
      add_filter('the_posts', array($this, 'the_posts'));
      add_filter('post_link', array($this, 'permalink_filter'), 1, 2);
      add_filter('category_link', array($this, 'category_permalink_filter'), 1, 2);
      add_filter('tag_link', array($this, 'tax_permalink_filter'), 1, 2);
      add_filter('home_url', array($this, 'home_url'), 1, 4) ;
      add_filter('feed_link', array($this, 'feed_link'), 1);
      add_filter('author_link', array($this,'author_link'));
      add_filter('year_link', array($this,'archives_link'));
      add_filter('month_link', array($this,'archives_link'));
      add_filter('day_link', array($this,'archives_link'));
    }
  }
  
  function set_locale() {
    global $locale;
    $languages = Language::get_all_as_array();
    switch($this->settings->language_negociation_format) {
      // language negotiation by ?lang=xx parameter
      case 'param':
        if(isset($_GET['lang'])) {
          $locale = trim($_GET['lang'], '/');
        }
        else {
          $l = Language::get_source_language();
          $locale = $l[0]->code;
        }
        break;
      // language negotiation by /xx/... directory
      case 'directory':
        $s = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '';
        $request = 'http' . $s . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $home = get_option('home');
        if($s) {
          $home = preg_replace('#^http://#', 'https://', $home);
        }
        $url_parts = parse_url($home);
        $blog_path = !empty($url_parts['path']) ? $url_parts['path'] : '';
        $path  = str_replace($home, '', $request);
        $parts = explode('?', $path);
        $path = $parts[0];
        $exp = explode('/', trim($path, '/'));
        if(in_array($exp[0], $languages)) {
          $locale = $exp[0];
          add_filter('option_rewrite_rules', array($this, 'rewrite_rules_filter'));
          
        }
        else {
          $l = Language::get_source_language();
          $locale = $l[0]->code;
        }
        break;
    }
  }
  
  function rewrite_rules_filter($value) {
    global $locale;
    foreach((array)$value as $k => $v) {
      $value[$locale . '/' . $k] = $v;
      unset($value[$k]);
    }
    $value[$locale] = 'index.php';
    return $value;
  }
  
  function permalink_filter($p, $pid) {
    global $locale;
    $p = $this->convert_url($p, $locale);
    if(is_feed()) {
      $p = str_replace("&lang=", "&#038;lang=", $p);
    }
    return $p;
  }
  
  function category_permalink_filter($p, $cat_id) {
    global $locale;
    return $this->convert_url($p, $locale);
  }
  
  function tax_permalink_filter($p, $tag) {
    global $locale;
    return $this->convert_url($p, $locale);
  }
  
  function home_url($url, $path, $orig_scheme, $blog_id) {
    global $locale;
    if(did_action('template_redirect') && rtrim($url,'/') == rtrim(get_option('home'),'/')) {
      $url = $this->convert_url($url, $locale);
    }
    return $url;
  }
  
  function feed_link($out) {
    global $locale;
    return $this->convert_url($out, $locale);
  }
  
  function author_link($url){
    global $locale;
    $url = $this->convert_url($url, $locale);
    return preg_replace('#^http://(.+)//(.+)$#','http://$1/$2', $url);
  }
  
  function archives_link($url){
    global $locale;
    return $this->convert_url($url, $locale);
  }
  
  function convert_url($url, $locale) {
    $source_language = Language::get_source_language();
    if ($locale == $source_language[0]->code) {
      return $url;
    }
    else {
      $abshome = preg_replace('@\?lang=' . $locale . '@i','', get_option('home'));
      switch($this->settings->language_negociation_format) {
        case 'directory':
          if(0 === strpos($url, 'https://')){
            $abshome = preg_replace('#^http://#', 'https://', $abshome);
          }
          if ($abshome == $url) $url .= '/';
          if (0 !== strpos($url, $abshome . '/' . $locale . '/')) {
            // only replace if it is there already
            $url = str_replace($abshome, $abshome . '/' . $locale, $url);
          }
          break;
        case 'param':
          // $url = preg_replace('/[\?&]lang='.$locale.'/', '', $url);
          if (strpos($url, '?')) {
            $url .= "&lang=".$locale;
          }
          else {
            $url .= "?lang=".$locale;
          }          
          break;
      }
    return $url;
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
        if ($translation == NULL || (isset($translation) && $translation->finalized == 'false')) {
          if ($this->settings->action_missing_translation == 'display_source_language') {
            $translated_posts[$i] = $post;
            $i++;
          }
        }
        else {
          $post->post_content = $translation->post_content;
          $post->post_title = $translation->post_title;
          $post->post_excerpt = $translation->post_excerpt;
          // $post->post_name = $translation->post_name; can’t make this translatable yet
          $post->post_content_filtered = $translation->post_content_filtered;
          $translated_posts[$i] = $post;
          $i++;
        }
      }
      return $translated_posts;
    }
  }
  
  function administration_menu() {
    add_menu_page("WTIpress", "WTIpress", 'manage_options', basename(WTIPRESS_PLUGIN_PATH).'/menu/setup.php',null, WTIPRESS_PLUGIN_URL . '/res/img/icon16.png');        
    add_submenu_page(basename(WTIPRESS_PLUGIN_PATH).'/menu/setup.php', "Setup", "Setup", 'manage_options', basename(WTIPRESS_PLUGIN_PATH).'/menu/setup.php');
    add_submenu_page(basename(WTIPRESS_PLUGIN_PATH).'/menu/setup.php', "Setup", "Translations", 'manage_options', basename(WTIPRESS_PLUGIN_PATH).'/menu/translations.php');
  }
      
  function process_forms() {
    if (isset($_POST['wti_api_key'])) {
      $this->settings->api_key = $_POST['wti_api_key'];
      $this->settings->action_missing_translation = $_POST['wti_translation_missing'];
      $this->settings->language_negociation_format = $_POST['language_negociation_format'];
      $this->settings->refresh_settings();
    }
    elseif (isset($_POST['new_language'])) {
      $this->settings->add_language($_POST['new_language']);
    }
    elseif (isset($_POST['remove_language_code'])) {
      $this->settings->remove_language($_POST['remove_language_code']);
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
  
  function warnings() {
    global $wp_version;

    if (version_compare($wp_version, WTIPRESS_MIN_WORDPRESS_VERSION, '<')) {
      add_action('admin_notices', 'wtipress_wordpress_version_warning');
    }

    if(version_compare(phpversion(), '5', '<')) {
      add_action('admin_notices', 'wtipress_php_version_warning');
    }

    if (!isset($this->settings->api_key) && !isset($_POST['wti_api_key'])) {
      add_action('admin_notices', 'wtipress_almost_ready');
    }

    function wtipress_wordpress_version_warning() {
      echo "<div class='updated fade'><p><strong>WTIpress ".WTIPRESS_VERSION." requires WordPress ".WTIPRESS_MIN_WORDPRESS_VERSION." or higher.</strong> Please <a href='http://codex.wordpress.org/Upgrading_WordPress'>upgrade WordPress</a> to a current version.</p></div>";
    }

    function wtipress_php_version_warning() {
      echo "<div class='updated fade'><p><strong>WTIpress requires PHP 5 or higher.</strong> Please upgrade PHP to a current version.</p></div>";
    }

    function wtipress_almost_ready() {
      echo "<div class='updated fade'><p><strong>WTIpress is almost ready.</strong> You must <a href='admin.php?page=wtipress/menu/setup.php'>enter your WebTranslateIt API key</a> for it to work.</p></div>";
    }
  }
}

?>
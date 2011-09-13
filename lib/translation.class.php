<?php
class Translation {
  
  public $post;
  public $language;
  public $post_content;
  public $post_title;
  public $post_excerpt;
  public $post_name;
  public $post_content_filtered;
  public $created_at;
  public $updated_at;
  public $last_pushed_at;
  public $last_pulled_at;
  
  function __construct($post, $language, $post_content=NULL, $post_title=NULL, $post_excerpt=NULL, $post_name=NULL, $post_content_filtered=NULL, $created_at=NULL, $updated_at=NULL, $last_pushed_at=NULL, $last_pulled_at=NULL) {
    $this->post = $post;
    $this->language = $language;
    $this->post_content = $post_content;
    $this->post_title = $post_title;
    $this->post_excerpt = $post_excerpt;
    $this->post_name = $post_name;
    $this->post_content_filtered = $post_content_filtered;
    $this->created_at = $created_at;
    $this->updated_at = $updated_at;
    $this->last_pushed_at = $last_pushed_at;
    $this->last_pulled_at = $last_pulled_at;
  }
  
  static function get_translation($post, $language) {
    $result = $wpdb->get_var($wpdb->prepare("SELECT * FROM ". $wpdb->prefix . "wtipress WHERE element_type=%s AND element_id=%d AND language_code=%s", $post->element_type, $post->element_id, $language));
    return new Translation($post, $language, $result->post_content, $result->post_title, $result->post_excerpt, $result->post_name, $result->post_content_filtered, $result->created_at, $result->updated_at, $result->last_pushed_at, $result->last_pulled_at);
  }
  
  function save() {
    global $wpdb;
    $wpdb->insert($wpdb->prefix.'wtipress', array(
      'post_content' => $this->post_content,
      'post_title' => $this->post_title,
      'post_excerpt' => $this->post_excerpt,
      'post_name' => $this->post_name,
      'post_content_filtered' => $this->post_content_filtered,
      'created_at' => date ("Y-m-d H:i:s", time()),
      'updated_at' => date ("Y-m-d H:i:s", time()),
      'last_pushed_at' => date ("Y-m-d H:i:s", time())
      )
    );
  }
}
?>
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
  public $wti_file_id;
  public $wti_checksum;
  
  function __construct($post, $language, $post_content=NULL, $post_title=NULL, $post_excerpt=NULL, $post_name=NULL, $post_content_filtered=NULL, $created_at=NULL, $updated_at=NULL, $last_pushed_at=NULL, $last_pulled_at=NULL, $wti_file_id=NULL, $wti_checksum=NULL) {
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
    $this->wti_file_id = $wti_file_id;
    $this->wti_checksum = $wti_checksum;
  }
  
  static function get_translation($post, $language) {
    global $wpdb;
    $query = $wpdb->prepare("SELECT * FROM ". $wpdb->prefix . "wtipress_posts WHERE element_type=%s AND element_id=%d AND language_code=%s", array($post->post_type, $post->ID, $language));
    $result = $wpdb->get_row($query);
    return new Translation($post, $language, $result->post_content, $result->post_title, $result->post_excerpt, $result->post_name, $result->post_content_filtered, $result->created_at, $result->updated_at, $result->last_pushed_at, $result->last_pulled_at, $result->wti_file_id, $result->wti_checksum);
  }
  
  function save() {
    global $wpdb;
    if($wpdb->get_var($wpdb->prepare("SELECT * FROM ". $wpdb->prefix . "wtipress_posts WHERE element_type=%s AND element_id=%d AND language_code=%s", $this->post->post_type, $this->post->ID, $this->language))){
      $wpdb->update($wpdb->prefix.'wtipress_posts', array(
        'post_content' => $this->post_content,
        'post_title' => $this->post_title,
        'post_excerpt' => $this->post_excerpt,
        'post_name' => $this->post_name,
        'post_content_filtered' => $this->post_content_filtered,
        'updated_at' => date("Y-m-d H:i:s", time()),
        'last_pushed_at' => date("Y-m-d H:i:s", time()),
        'wti_checksum' => $this->wti_checksum,
      ),
      array(
        'element_id' => $this->post->ID,
        'element_type' => $this->post->post_type,
        'language_code' => $this->language
      )
    );
    }
    else {
      $wpdb->insert($wpdb->prefix.'wtipress_posts', array(
        'element_id' => $this->post->ID,
        'element_type' => $this->post->post_type,
        'language_code' => $this->language,
        'post_content' => $this->post_content,
        'post_title' => $this->post_title,
        'post_excerpt' => $this->post_excerpt,
        'post_name' => $this->post_name,
        'post_content_filtered' => $this->post_content_filtered,
        'created_at' => date("Y-m-d H:i:s", time()),
        'updated_at' => date("Y-m-d H:i:s", time()),
        'last_pushed_at' => date("Y-m-d H:i:s", time()),
        'wti_file_id' => $this->wti_file_id,
        'wti_checksum' => $this->wti_checksum,
        )
      );
    }
  }
}
?>
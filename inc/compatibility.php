<?php

// A few custom functions for backwards compatibility

if (!function_exists('json_decode')){
  include_once WTIPRESS_PLUGIN_PATH . '/lib/json.php';
  function json_decode($data, $bool) {
    if ($bool) {
      $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
    }
    else {
      $json = new Services_JSON();
    }
    return($json->decode($data));
  }
}   

?>
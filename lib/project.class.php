<?php

class Project {
  
  public $name;
  public $source_locale;
  public $target_locales;
  
  function __construct($name, $source_locale, $target_locales) {
    $this->name = $name;
    $this->source_locale = $source_locale;
    $this->target_locales = $target_locales;
  }
}

?>
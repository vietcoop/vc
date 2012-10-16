<?php
/**
 * @file
 */

/**
 * Simple configuration class.
 */
class VCConfig {
  protected $collection;
  protected $key;
  protected $storage;

  public function __construct($key) {
    list($this->collection, $this->key) = explode(':', $key);
    $this->storage = new VCKeyValue($this->collection);
  }

  /**
   * Method to get variable value.
   */
  public function get() {
    $result = $this->storage->get($this->key);
    if (NULL === $result) {
      return $this->fetch();
    }
    return $result;
  }

  /**
   * Fetch configuration from configuration file.
   *
   * @param boolean $save_bundle Save the bundle or not.
   */
  private function fetch($save_bundle = TRUE) {
    $module = $this->collection;
    $file = drupal_get_path('module', $module);
    $file = DRUPAL_ROOT . "/{$file}/config/{$module}.yaml";
    if (file_get_contents($file)) {
      if ($bundle = yaml_parse($file)) {
        if ($save_bundle) {
          $this->saveBundle($bundle);
        }

        if (isset($bundle[$this->key])) {
          return $bundle[$this->key];
        }
      }
    }
    return FALSE;
  }

  /**
   * Method to set variable value.
   */
  public function set($value) {
    $this->storage->set($this->key, $value);
  }

  public function saveBundle($bundle) {
    foreach ($bundle as $key => $value) {
      $this->storage->set($key, $value);
    }
  }

  public function restore() {
    if ($value = $this->fetch(FALSE)) {
      $this->set($value);
    }
  }
}

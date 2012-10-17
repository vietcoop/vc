<?php
/**
 * @file lib/LazyHook.php
 */

class VcLazyHook {
  const COLLECTION = 'vclazy';
  const DUMP_FILE = 'private://vc.lazyhooks.php';

  /**
   * Supported hooks.
   *
   * @var array
   */
  static $hooks = array(
    'entity_info', 'filter_info', 'help', 'menu', 'permission', 'theme',
    'views_api'
  );

  public function buildHooks() {
    $this->clearCode();

    foreach (self::$hooks as $hook) {
      $this->buildHook($hook);
    }
    if ($this->writeCode()) {
      if (function_exists('apc_compile_file')) {
        $file = drupal_realpath(self::DUMP_FILE);
        apc_compile_file($file);
      }
    }
  }

  protected function clearCode() {
    // Clear code
    $kv = new VCKeyValue(COLLECTION);
    $kv->deleteAll();

    // Remove dump file
    drupal_unlink(self::DUMP_FILE);
  }

  protected function buildHook($hook) {
    list($module, $data) = $this->parseData($hook);
    if ($module && $data) {
      if ($code = $this->buildCode($hook, $module, $data)) {
        if (FALSE !== $this->saveCode($hook, $module, $code)) {
        }
      }
    }
  }

  protected function parseData($hook) {
    if (!in_array($hook, self::$hooks)) return FALSE;

    foreach (vc_get_module_apis() as $module => $info) {
      $file = drupal_get_path('module', $module);
      $file = DRUPAL_ROOT . '/' . $file . "/config/{$module}.{$hook}.yaml";
      if (file_exists($file)) {
        if (!$content = yaml_parse_file($file)) cotinue;
        return array($module, $content);
      }
    }
  }

  protected function buildCode($hook, $module, $items) {
    // Include helper functions.
    ctools_include('export');

    $code = array();
    $code = ctools_var_export($items, '  ');
    $code = "  return {$code};";
    $code = "function {$module}_{$hook}() {\n{$code}\n}\n\n";
    $code = "/**\n * Implements hook_permission().\n *\n */\n{$code}";
    return $code;
  }

  protected function saveCode($hook, $module, $code) {
    $kv = new VCKeyValue(COLLECTION);
    return $kv->set("{$module}.{$hook}", $code);
  }

  protected function writeCode() {
    $kv = new VCKeyValue(self::COLLECTION);
    $code = $kv->getAll();

    $code = implode("\n\n", $code);
    $prefix  = "<?php\n";
    $prefix .= "/**\n";
    $prefix .= " * @file vc.lazyhooks.php\n";
    $prefix .= " * Generated by vc.module\n";
    $prefix .= " *\n";
    $prefix .= " */\n\n";

    return file_unmanaged_save_data($prefix . $code, self::DUMP_FILE, FILE_EXISTS_REPLACE);
  }
}

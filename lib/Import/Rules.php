<?php
class Vc_Import_Rules {
  public static function importAll() {
    foreach (vc_get_module_apis() as $module => $info) {
      $dir = DRUPAL_ROOT . '/' . drupal_get_path('module', $module) . '/config/rules/';
      if (is_dir($dir)) {
        foreach (file_scan_directory($dir, '/\.json/') as $filename) {
          if (function_exists('drush_print_r')) {
            drush_print_r("Imporing {$filename->uri}");
          }
          static::import($filename->uri);
        }
      }
    }
  }

  public static function import($filename) {
    if (!file_exists($filename)) {
      $msg = "File is not existing: {$filename}";
      throw new Exception($msg);
    }

    $name = basename($filename, '.json');

    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', 'rules_config');
    $query->propertyCondition('name', $name);
    if ($query = $query->execute()) {
      $old_rule = reset($query);
      $old_rule = reset($old_rule);
      $old_rule = entity_load('rules_config', $old_rule);
      $old_rule = reset($old_rule);
      $old_rule->delete();
    }

    $file = file_get_contents($filename);
    $rule = entity_import('rules_config', $file);
    if ($rule) {
      $rule->save();
    }

    return $rule;
  }
}

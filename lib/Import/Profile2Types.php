<?php
class Vc_Import_Profile2Types {
  public static function importAll() {
    foreach (vc_get_module_apis() as $module => $info) {
      $dir = DRUPAL_ROOT . '/' . drupal_get_path('module', $module) . '/config/profile2_type/';
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

    $type = basename($filename, '.json');

    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', 'profile2_type');
    $query->propertyCondition('type', $type);
    if ($query = $query->execute()) {
      $old_profile_type = reset($query);
      $old_profile_type = reset($old_profile_type);
      $old_profile_type = entity_load('profile2_type', $old_profile_type);
      $old_profile_type = reset($old_profile_type);
      $old_profile_type->delete();
    }

    $file = file_get_contents($filename);
    $rule = entity_import('profile2_type', $file);
    if ($rule) {
      $rule->save();
    }

    return $rule;
  }
}

<?php

class Vc_Import_Entity {
  public static function importAll($entity_type, $extension = 'json') {
    foreach (vc_get_module_apis() as $module => $info) {
      $dir = DRUPAL_ROOT . '/' . drupal_get_path('module', $module) . '/config/'. $entity_type;
      if (!is_dir($dir)) continue;

      foreach (file_scan_directory($dir, "/\.{$extension}/") as $filename) {
        if (function_exists('drush_log')) drush_log(" › Imporing {$filename->uri}");
        static::import($filename->uri, $entity_type, $extension);
      }
    }
  }

  protected function deleteOld($entity_type, $machine_name) {
    $meta = entity_metadata_wrapper($entity_type);

    // Validate
    $info = $meta->entityInfo();
    if (empty($info['exportable'])) return;
    if (empty($info['entity keys']['name'])) return;

    $name_key = $info['entity keys']['name'];
    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', $entity_type);
    $query->propertyCondition($name_key, $machine_name);
    if ($query = $query->execute()) {
      $ids = array_keys($query[$entity_type]);
      foreach (entity_load($entity_type, $ids) as $_entity) {
        $_entity->delete();
      }
    }
  }

  public function import($filename, $entity_type, $extension) {
    if (!file_exists($filename)) {
      $msg = "File is not existing: {$filename}";
      throw new Exception($msg);
    }

    self::deleteOld($entity_type, $machine_name = basename($filename, ".{$extension}"));

    if ($file = file_get_contents($filename)) {
      if ($entity = entity_import($entity_type, $file)) {
        return $entity->save();
      }
    }
  }
}

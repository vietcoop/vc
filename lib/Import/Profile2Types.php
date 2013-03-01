<?php

class Vc_Import_Profile2Types extends Vc_Import_Entity {
  public static function importAll() {
    parent::importAll('profile2_type', $extension = 'json');
  }

  protected function deleteOld($entity_type, $machine_name) {
    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', $entity_type);
    $query->propertyCondition('type', $machine_name);
    if ($query = $query->execute()) {
      $_entity = reset($query);
      $_entity = reset($_entity);
      $_entity = entity_load($entity_type, $_entity);
      $_entity = reset($_entity);
      $_entity->delete();
    }
  }

  public static function import($filename, $entity_type = 'profile2_type', $extension = 'json') {
    if (!file_exists($filename)) {
      $msg = "File is not existing: {$filename}";
      throw new Exception($msg);
    }

    // Remove old
    self::deleteOld($entity_type, $machine_name = basename($filename, '.' . $extension));

    if ($file = file_get_contents($filename)) {
      if ($entity = entity_import($entity_type, $file)) {
        return $entity->save();
      }
    }

    return FALSE;
  }
}

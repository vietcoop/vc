<?php

class Vc_Import_LgentityLevels extends Vc_Import_Entity {
  public static function importAll() {
    parent::importAll($entity_type = 'lgentity_level', $extension = 'json');
  }
}

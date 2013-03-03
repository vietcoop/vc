<?php

class Vc_Drush_Block {
  public static function resetCallback() {
    if (!$module = drush_get_option('module')) return;

    if (module_exists($module)) {
      self::resetBlocksByModule($module);
    }
  }

  public static function resetBlocksByModule($module) {
    block_modules_uninstalled(array($module));
    _block_rehash();
  }
}

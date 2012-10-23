<?php
/**
 * @file lib/Debug/Menu.php
 */

class Vc_Debug_Menu {
  public static function itemInfo($path) {
    $item = menu_get_item($path);
    drush_print_r($item);
  }
}

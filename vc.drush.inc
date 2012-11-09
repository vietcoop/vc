<?php
/**
 * @file vc.drush.inc
 */

/**
 * Define drush command "lm-sql-sync"
 */
function vc_drush_command() {
  $items['vc-cc'] = array(
    'description' => 'Rebuild the lazy-hooks.',
    'callback' => 'Vc_Lazy_Hook::rebuild',
  );

  $items['vc-debug-path'] = array(
    'description' => 'Display info of an menu item',
    'callback' => 'Vc_Debug_Menu::itemInfo',
  );

  return $items;
}

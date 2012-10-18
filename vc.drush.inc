<?php
/**
 * @file vc.drush.inc
 */

/**
 * Define drush command "lm-sql-sync"
 */
function vc_drush_command() {
  $items['vc-lazyhooks-rebuild'] = array(
    'description' => 'Rebuild the lazy-hooks.',
    'callback' => 'Vc_Core_FlushCaches::actionRebuildLazyHooks',
  );

  $items['vc-debug-path'] = array(
    'description' => 'Display info of an menu item',
    'callback' => 'Vc_Debug_Menu::itemInfo',
  );

  return $items;
}

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
    'callback' => 'vc_flush_caches',
  );
  return $items;
}

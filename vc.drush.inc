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

  $items['vc-import-views'] = array(
    'description' => 'Import all views',
    'callback' => 'Vc_Import_Views::importAll',
  );

  $items['vc-import-rules'] = array(
    'description' => 'Import all rules',
    'callback' => 'Vc_Import_Rules::importAll',
  );

  $items['vc-import-profile2-types'] = array(
    'description' => 'Import all profile2 types',
    'callback' => 'Vc_Import_Profile2Types::importAll',
  );

  $items['vc-debug-path'] = array(
    'description' => 'Display info of an menu item',
    'callback' => 'Vc_Debug_Menu::itemInfo',
  );

  $items['vc-test'] = array(
    'description' => 'Test a test',
    'callback' => 'Vc_Drush_Test::callback',
  );

  $items['vc-test-all'] = array(
    'description' => 'Run all tests',
    'callback' => 'Vc_Drush_Test::runAllcallback',
  );

  $items['vc-cron'] = array(
    'description' => 'Run cron jobs',
    'callback' => 'Vc_Drush_Cron::callback',
  );

  return $items;
}

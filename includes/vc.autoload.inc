<?php
/**
 * @file vc.autoload.inc
 */

/**
 * Lazy hooks
 */
require_once dirname(dirname(__FILE__)) . '/lib/Lazy/Hook.php';
$lazy_hook =  drupal_realpath(Vc_Lazy_Hook::dumpFile());
if (file_exists($lazy_hook)) {
  include_once $lazy_hook;
}

/**
 * Register auto loader.
 */
spl_autoload_register('vc_autoload');

/**
 * Autoloader.
 */
if (!function_exists('vc_autoload')) {
  function vc_autoload($class, $sites = array('all')) {
    return _vc_autoload($class, $sites);
  }
}

function _vc_autoload($class, $sites = array('all')) {
  if (strpos($class, 'Drupal\\') === 0) {
    return vc_autoload_namespace($class);
  }

  if (strpos($class, '\\') !== FALSE) {
    // load class like Symfony\Component\Yaml\Parser in /sites/all/libraries
    $class = str_replace('\\', '/', $class);
    foreach ($sites as $site) {
      $file = DRUPAL_ROOT . "/sites/{$site}/libraries/{$class}.php";
      if (is_file($file)) {
        include_once $file;
        return;
      }
    }
  }
  else {
    return vc_autoload_class($class);
  }
}

/**
 * Autoloader for namespace based classes.
 */
function vc_autoload_namespace($class) {
  $suffix = str_replace('\\', '/', $class) . '.php';
  foreach (vc_get_module_apis() as $module => $info) {
    if (strpos($suffix, "Drupal/{$module}/") !== FALSE) {
      $prefix = DRUPAL_ROOT . '/' . drupal_get_path('module', $module);
      $file = "{$prefix}/lib/{$suffix}";
      if (file_exists($file)) {
        include_once $file;
      }
    }
  }
}

/**
 * Autoloader for non-namespace classes.
 */
function vc_autoload_class($class) {
  foreach (vc_get_module_apis() as $module => $info) {
    $_module = str_replace('_', ' ', $module);
    $_module = ucwords($_module);
    $_module = str_replace(' ', '', $_module);
    $path  = DRUPAL_ROOT . '/' . drupal_get_path('module', $module) . '/lib/';

    if ($_module === $class) {
      $path .= "{$class}.php";
    }
    else {
      // Find sub, remove module prefix.
      if (strpos($class, '_') !== FALSE) {
        $sub = explode('_', $class);
        array_shift($sub);
        $sub = implode('/', $sub);
      }
      else {
        $sub = substr($class, strlen($_module));
      }
      $path .= "{$sub}.php";
    }

    if (file_exists($path)) {
      include_once $path;
    }
  }
}

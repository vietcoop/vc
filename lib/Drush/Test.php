<?php

class Vc_Drush_Test {
  public static function callback() {
    $args = drush_get_arguments();
    array_shift($args);

    if (!empty($args)) {
      foreach ($args as $test) {
        static::runTest($test);
      }
    }
    else {
      self::runAllcallback();
    }
  }

  public static function runAllcallback() {
    $args = drush_get_arguments();
    array_shift($args);

    if (!empty($args)) {
      $module = reset($args);
      if (module_exists($module)) {
        return self::runAllModuleTests($module);
      }
      else {
        drush_log(" » Module {$module} does not exists.", 'error');
        return;
      }
    }

    foreach (vc_get_module_apis() as $module => $info) {
      self::runAllModuleTests($module);
    }
  }

  public static function runAllModuleTests($module) {
    $file = drupal_get_path('module', $module);
    $file = DRUPAL_ROOT . '/' . $file . "/config/{$module}.tests.yaml";
    if (file_exists($file)) {
      if (!$tests = yaml_parse_file($file)) continue;
      foreach ($tests as $test) {
        self::runTest($test);
      }
    }
  }

  public static function runTest($class_name) {
    drush_print_r("=================================================");
    drush_print_r("  Running test {$class_name}");
    drush_print_r("=================================================");

    $test = new $class_name();
    if (method_exists($test, 'setUp')) $test->setUp();

    foreach (get_class_methods($test) as $method) {
      if (substr($method, 0, '4') === 'test') {
        drush_print_r('');
        drush_print_r("  Testing {$method}");
        drush_print_r("  -------");
        drush_print_r();

        $test->{$method}();
      }
    }

    if (method_exists($test, 'tearDown')) $test->tearDown();

    drush_print_r('');
  }
}

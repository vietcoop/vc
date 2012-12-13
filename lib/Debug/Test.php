<?php

class Vc_Debug_Test {
  public static function callback() {
    $args = drush_get_arguments();
    array_shift($args);

    foreach ($args as $test) {
      static::runTest($test);
    }
  }

  public static function runTest($class_name) {
    drush_print_r("-------");
    drush_print_r("Running test {$class_name}");
    drush_print_r("-------");

    $test = new $class_name();
    if (method_exists($test, 'setUp')) $test->setUp();

    foreach (get_class_methods($test) as $method) {
      if (substr($method, 0, '4') === 'test') {
        drush_print_r('');
        drush_print_r("Testing {$method}");
        drush_print_r("---");

        $test->{$method}();
      }
    }

    if (method_exists($test, 'setUp')) $test->tearDown();

    drush_print_r('');
  }
}

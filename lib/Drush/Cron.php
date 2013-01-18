<?php

class Vc_Drush_Cron {
  public static function callback() {
    $args = drush_get_arguments();
    array_shift($args);
    if (empty($args)) {
      return self::runAll();
    }

    $module = array_shift($args);
    return self::runModuleCrons($module);
  }

  public static function runAll() {
    foreach (vc_get_module_apis() as $module => $info) {
      self::runModuleCrons($module);
    }
  }

  protected static function runModuleCrons($module) {
    $file = drupal_get_path('module', $module);
    $file = DRUPAL_ROOT . '/' . $file . "/config/{$module}.cron.yaml";
    if (file_exists($file)) {
      drush_print_r(" › Running cron jobs in {$module} module");

      if (!$jobs = yaml_parse_file($file)) continue;
      foreach ($jobs as $name => $job) {
        drush_print_r("   » Running {$module} › {$name}");

        if (!empty($job['lock'])) {
          $lock_name = md5("vc_cron_{$module}_{$name}");
          $lock_timeout = isset($job['lock timeout']) ? $job['lock timeout'] : 60;
          if (!lock_acquire($lock_name, $lock_timeout)) {
            drush_print_r("      | An other process is running…");
            return;
          }
        }

        if (is_callable($job['callback'])) {
          $job['callback']();
        }
        else {
          drush_print_r("      | Callback is not callable: {$job['callback']}");
        }

        if ($job['lock']) {
          lock_release($lock_name);
        }
      }
    }
  }
}

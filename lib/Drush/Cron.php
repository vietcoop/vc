<?php

class Vc_Drush_Cron {
  public static function callback() {
    $args = drush_get_arguments();
    array_shift($args);

    if (empty($args)) {
      return self::runAll();
    }

    $module = array_shift($args);
    $cron_job = array_shift($args);
    return self::runModuleCrons($module, $cron_job);
  }

  public static function runAll() {
    foreach (vc_get_module_apis() as $module => $info) {
      self::runModuleCrons($module);
    }
  }
  
  protected static function runModuleCrons($module, $cron_job = NULL) {    
    $file = drupal_get_path('module', $module);
    $file = DRUPAL_ROOT . '/' . $file . "/config/{$module}.cron.yaml";
    if (file_exists($file)) {
      function_exists('drush_print_r') && drush_print_r(" › Running cron jobs in {$module} module");

      // Get job infos
      if (!$jobs = yaml_parse_file($file)) continue;

      // Only run one job!
      if (!empty($cron_job) && isset($jobs[$cron_job])) {
        $jobs = array($cron_job => $jobs[$cron_job]);
      }

      // Loop to execute the jobs
      foreach ($jobs as $job_name => $info) {
        function_exists('drush_print_r') && drush_print_r("   » Running {$module} › {$job_name}");
        self::runModuleCron($module, $job_name, $info);
      }
    }
  }

  protected static function runModuleCron($module, $job_name, $job_info) {
    if (!empty($job_info['lock'])) {
      $lock_name    = md5("vc_cron_{$module}_{$job_name}");
      $lock_timeout = isset($job_info['lock timeout']) ? $job_info['lock timeout'] : 60;
      if (!lock_acquire($lock_name, $lock_timeout)) {
        function_exists('drush_print_r') && drush_print_r("      | An other process is running…");
        return;
      }
    }

    if (is_callable($job_info['callback'])) {
      call_user_func($job_info['callback']);
    }
    else {
      drush_print_r("      | Callback is not callable: {$job_info['callback']}");
    }

    if ($job_info['lock']) {
      lock_release($lock_name);
    }
  }
}

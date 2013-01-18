<?php

class Vc_Example_Drush_Cron {
  public static function demoJob() {
    drush_print_r(__FILE__ . ':' . __LINE__);
  }
}

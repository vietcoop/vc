<?php
/**
 * @file lib/Core/Cron.php
 */

class VcExample_Core_Cron {
  /**
   * This method will be called on cron run.
   * It starts with action.
   */
  public static function actionA() {
    // …
  }

  /**
   * This method will not be called on cron run.
   * It does nont start with action.
   */
  public static function _actionB() {
    // …
  }
}

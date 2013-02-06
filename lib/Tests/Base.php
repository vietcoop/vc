<?php

class Vc_Tests_Base {
  public $print_status = TRUE;
  public $fail_exit = TRUE;

  public function assertTrue($bool, $msg = NULL, $throw = FALSE, $print_error = TRUE, $backtrace = 0) {
    if ($this->print_status) {
      if (!empty($msg)) {
        $prefix = '     ›';
        drush_log("{$prefix} {$msg}", $bool === TRUE ? 'success' : 'error');
      }
    }

    if (!$bool && $this->fail_exit) {
      if ($print_error) {
        $d = debug_backtrace();
        $d = $d[$backtrace];
        drush_log('-------------------------', 'error');
        drush_log(' Fail assertion',            'error');
        drush_log(' File: ' .   $d['file'],     'error');
        drush_log(' Line: ' .   $d['line'],     'error');
        drush_log(' Class: ' .  $d['class'],    'error');
        drush_log(' Method: ' . $d['function'], 'error');
        drush_log('-------------------------', 'error');
      }

      if ($throw) {
        throw new Exception("Fail assertion", TRUE);
      }
    }
  }

  public function assertEqual($expected, $actual, $msg = NULL) {
    try {
      $msg = !empty($msg) ? $msg : "{$expected} = {$actual}";
      $this->assertTrue($expected === $actual, $msg, $throw = TRUE, $print = TRUE, $backtrace = 1);
    }
    catch (Exception $e) {
      $d = debug_backtrace();
      $d = $d[$backtrace];
      drush_log(' Exprected: ' .   $expected,   'error');
      drush_log(' Actual:    ' .   $actual,     'error');
    }
  }

  public function assert($expression, $msg = NULL) {
    return $this->assertTrue((bool)($expression), $msg);
  }

  public function assertFalse($bool, $msg = NULL) {
    $this->assertTrue(!$bool, $msg);
  }

  public function assertNumber($expression, $msg = NULL) {
    $this->assertTrue(is_numeric($expression), $msg);
  }

  public function assertNull($expression, $msg = NULL) {
    $this->assertTrue(is_null($expression), $msg);
  }

  public function assertNotNull($expression, $msg = NULL) {
    $this->assertTrue(!is_null($expression), $msg);
  }

  public function assertEmpty($expression, $msg = NULL) {
    $this->assertTrue(empty($expression), $msg);
  }

  public function assertNotEmpty($expression, $msg = NULL) {
    $this->assertTrue(!empty($expression), $msg);
  }
}

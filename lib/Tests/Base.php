<?php

class Vc_Tests_Base {
  public $print_status = TRUE;
  public $fail_exit = TRUE;

  public function assert($expression, $msg) {
    return $this->assertTrue((bool)($expression), $msg);
  }

  public function assertTrue($bool, $msg) {
    if ($this->print_status) {
      $prefix = '     ›';
      drush_log("{$prefix} {$msg}", $bool === TRUE ? 'success' : 'error');
    }

    if (!$bool && $this->fail_exit) {
      throw new Exception("Fail assert: {$msg}");
    }
  }

  public function assertFalse($bool, $msg) {
    $this->assertTrue(!$bool, $msg);
  }

  public function assertNumber($expression, $msg) {
    $this->assertTrue(is_numeric($expression), $msg);
  }
}

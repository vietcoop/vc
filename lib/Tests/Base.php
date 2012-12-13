<?php

class Vc_Tests_Base {
  protected function assertTrue($bool, $msg) {
    drush_log("[Assert] {$msg}", $bool ? 'success' : 'error');
  }
}

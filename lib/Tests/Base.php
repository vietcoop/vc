<?php

class Vc_Tests_Base {
  protected function assertTrue($bool, $msg) {
    drush_log("     › {$msg}", $bool ? 'success' : 'error');
  }
}

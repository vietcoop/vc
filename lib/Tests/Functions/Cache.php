<?php

class Vc_Tests_Functions_Cache extends Vc_Tests_Base {
  public function __testDebug() {
    $t1 = vc_cache($reset = TRUE, 'vc_test_callback');
    $t2 = vc_cache('vc_test_callback');
  }

  public function testFunctionCache() {
    $t1 = vc_cache($reset = TRUE, 'vc_test_callback');
    sleep(1);
    $t2 = vc_cache('vc_test_callback');
    sleep(5);
    $t3 = vc_cache('vc_test_callback');

    $this->assertTrue($t1 == $t2, 'Result is cached');
    $this->assertTrue($t1 <  $t3, '@ttl works');
  }

  public function testObjectCache() {
    $object = new Vc_Tests_Object_Cache();
    $t1 = vc_cache($reset = TRUE, $object, 'method');
    sleep(1);
    $t2 = vc_cache($object, 'method');
    sleep(5);
    $t3 = vc_cache($object, 'method');

    $this->assertTrue($t1 == $t2, 'Result is cached');
    $this->assertTrue($t1 <  $t3, '@ttl works');
  }
}

/**
 * @ttl '+ 5 seconds'
 */
function vc_test_callback() {
  return time();
}

class Vc_Tests_Object_Cache {
  /**
   * @ttl + 5 seconds
   */
  public function method() {
    return time();
  }
}

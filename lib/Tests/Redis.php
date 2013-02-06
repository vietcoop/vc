<?php

class Vc_Tests_Redis extends Vc_Tests_Base {
  /**
   * Test the lib for bug this issue https://github.com/nicolasff/phpredis/pull/278
   */
  public function testSRandMember() {
    $redis = VcRedis::getClient();
    $redis->sAdd('key1' , 'member1');
    $redis->sAdd('key1' , 'member2');
    $redis->sAdd('key1' , 'member3');

    //
    $items = $redis->sMembers('key1');
    $this->assertTrue(3 == count($items), 'Added 3 items');

    //
    $real = $redis->sRandMember('key1');
    $this->assertTrue(is_string($real), 'Got a random item');
    $this->assertTrue(in_array($real, $items), 'Picked item is in set.');

    $two_items = $redis->sRandMember('key1', 2);
    $this->assertTrue(2 == count($two_items), 'Found 2 items');

    $redis->del('key1');
  }
}

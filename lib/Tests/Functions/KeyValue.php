<?php

class Vc_Tests_Functions_KeyValue extends Vc_Tests_Base {
  public $collection_name = 'vc_tests';

  /**
   *
   * @var VCKeyValue
   */
  public $collection;

  public function setUp() {
    $this->collection = new VCKeyValue($this->collection_name);
  }

  public function testSetMethod() {
    $this->collection->set('foo', 'bar');
    $check = 'bar' === $this->collection->get('foo');
    $this->assertTrue($check, 'VCKeyValue#set works.');
  }

  public function testSetMultiple() {
    $this->collection->setMultiple(array('foo' => 'baz', 'bar' => 'fu'));
    $check = 'baz' === $this->collection->get('foo');
    $check = $check && ('fu' === $this->collection->get('bar'));
    $this->assertTrue($check, 'VCKeyValue#setMultiple works.');
  }

  public function testDelete() {
    $this->collection->set('foo', 'bar');
    $this->collection->delete('foo');
    $this->assert(!$this->collection->get('foo'), 'VCKeyValue#delete works.');
  }

  public function testDeleteMultiple() {
    $this->collection->setMultiple(array('foo' => 'baz', 'bar' => 'fu'));
    $this->collection->deleteMultiple(array('foo', 'bar'));
    $this->assert(!$this->collection->get('foo'), 'VCKeyValue#deleteMultiple works.');
  }
}

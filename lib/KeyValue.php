<?php
/**
 * @file lib/KeyValue.php
 */

/**
 * KeyValue storage.
 */
class VCKeyValue {
  /**
   * The name of the collection holding key and value pairs.
   *
   * @var string
   */
  protected $collection;

  /**
   * The name of the SQL table to use.
   *
   * @var string
   */
  protected $table;

  public function __construct($collection, $table = 'key_value') {
    $this->table = $table;
    $this->collection = $collection;
  }

  public function getCollectionName() {
    return $this->collection;
  }

  public function get($key) {
    $values = $this->getMultiple(array($key));
    return isset($values[$key]) ? $values[$key] : NULL;
  }

  public function getMultiple(array $keys) {
    $values = array();
    try {
      $result = db_query('SELECT name, value FROM {' . db_escape_table($this->table) . '} WHERE name IN (:keys) AND collection = :collection', array(':keys' => $keys, ':collection' => $this->collection))->fetchAllAssoc('name');
      foreach ($keys as $key) {
        if (isset($result[$key])) {
          $values[$key] = unserialize($result[$key]->value);
        }
      }
    }
    catch (Exception $e) {
      // @todo: Perhaps if the database is never going to be available,
      // key/value requests should return FALSE in order to allow exception
      // handling to occur but for now, keep it an array, always.
    }
    return $values;
  }

  public function getAll() {
    $result = db_query('SELECT name, value FROM {' . db_escape_table($this->table) . '} WHERE collection = :collection', array(':collection' => $this->collection));
    $values = array();

    foreach ($result as $item) {
      if ($item) {
        $values[$item->name] = unserialize($item->value);
      }
    }
    return $values;
  }

  public function set($key, $value) {
    db_merge($this->table)
      ->key(array(
        'name' => $key,
        'collection' => $this->collection,
      ))
      ->fields(array('value' => serialize($value)))
      ->execute();
  }

  public function setMultiple(array $data) {
    foreach ($data as $key => $value) {
      $this->set($key, $value);
    }
  }

  public function setIfNotExists($key, $value) {
    $result = db_merge($this->table)
      ->insertFields(array(
        'collection' => $this->collection,
        'name' => $key,
        'value' => serialize($value),
      ))
      ->condition('collection', $this->collection)
      ->condition('name', $key)
      ->execute();
    return $result == MergeQuery::STATUS_INSERT;
  }

  public function delete($key) {
    $this->deleteMultiple(array($key));
  }

  public function deleteMultiple(array $keys) {
    // Delete in chunks when a large array is passed.
    do {
      db_delete($this->table)
        ->condition('name', array_splice($keys, 0, 1000))
        ->condition('collection', $this->collection)
        ->execute();
    }
    while (count($keys));
  }

  public function deleteAll() {
    $sql = 'DELETE FROM {' . db_escape_table($this->table) . '} WHERE collection = :collection';
    db_query($sql, array(':collection' => $this->collection));
  }
}

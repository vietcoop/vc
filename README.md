### Features

1. Lazy load.
1. Simple KeyValue class.
1. Simple configuration system
1. Lazy hooks:
  1. Lazy for static hooks: permission, menu, theme, views, …
  1. Lazy for dynamic hooks: cron, node_*, …
1. Lazy content
1. Lazy cron
1. Redis wrapper for PhpRedis.
1. Cache wrapper
1. @TODO: Callback wrapper, supports annotations.

### Lazy Load

To use this feature. Your module need implementing hook_vc_api().
Your class named `ModuleNameFoo`, vc will be loaded the file in
`/path/to/module_name/lib/Foo.php`.

  - `ModuleName` -> `/path/to/module_name/lib/ModuleName.php`
  - `ModuleNameFoo` -> `/path/to/module_name/lib/Foo.php`
  - `ModuleName_Foo_Bar` -> `/path/to/module_name/lib/Foo/Bar.php`

This lazy loader also supports namespace:

  - If your class is Drupal\vc_example\TryNameSpace()
  - Your file will be found in /path/to/vc_example/lib/Drupal/vc_example/TryNameSpace.php

### KeyValue class

    <?php
    $kv = new VCKeyValue($collection = 'my_collection');
    $kv->set('foo', 'bar');
    $kv->setMultiple(array('foo' => 'bar', 'baz' => 'foo'));
    $kv->delete('foo');
    $kv->deleteMultiple(array('foo', 'baz');

### Lazy Hooks

#### Lazy for static hooks: permission, menu, theme, views, …

  …

#### Lazy for dynamic hooks: cron, node_*, …

  …

### Lazy content

    <?php
    $path = drupal_get_path('module', 'vc') . '/vc_example/config/lazy_content/example.yaml';
    vc_content($path);

### Lazy cron

Module that implement hook_vc_api() can define a YAML configuration file to list
the cron jobs that it provides. See vc_example/config/vc_example.cron.yaml for
supported parameters.

VC.module provides a drush command to run these cron jobs:

    # Run all cron jobs
    drush vc-cron

    # Run cron jobs in a specific module
    drush vc-cron vc_examples

### Redis wrapper

Configure Redis connection in your Drupal settings.php (They are all optional,
default values will be used).

    <?php
    $conf['redis_client_host'] = '127.0.0.1';
    $conf['redis_client_port'] = '6379';
    $conf['redis_client_base'] = '';
    $conf['redis_client_password'] = NULL;

Usage, support IDE autocomplete:

    <?php
    $redis = VcRedis::getClient();
    $redis->mget($array);

### @TODO: Function Anotations

Supported annotations:

#### 1. Cache: …

##### Function cache:

Cache control:

    /**
     * @ttl '+ 5 seconds'
     */
    function vc_test_callback() {
      return time();
    }

This is direct call:

    function_callback($a1, $a2, $a3);

Call with cache:

    vc_cache('function_callback', $a1, $a2, $a3);

##### Method callback:

Cache control:

class Vc_Tests_Object_Cache {
  /**
   * @ttl + 5 seconds
   */
  public function method() {
    return time();
  }
}

This is direct call:

    $object->method($a1, $a2, $a3);

Call with cache:

    vc_cache($object, 'method', $a1, $a2, $a3);

#### 2. Queue: …

    <?php
    /**
     * @queue($name = 'function_name', $data = 'function arguments')
     */
    function foo() {}

#### 3. String functions: …

    <?php
    /**
     * @filter_xss('%function results', $allowed_tags = array('a', 'strong'))
     * @strip_tags('%function results', $allowed_tags = array('a', 'strong'))
     * @truncate_utf8(…)
     */
    function foo() {}

#### 4. Logging (watchdog)

    <?php
    /**
     * @watchdog($type = '', $message, $variables = array(), $severity = WATCHDOG_NOTICE, $link = NULL).
     */
    function foo($a1, $a2) {
      $my_complex_result = '…';
      return $my_complex_result; // ' Hello  '
    }

### Cache Wrapper

Example cache:

    <?php
    $options = array('bin' => 'cache', 'expire' => strtotime('+15 minutes));
    $callback = 'content_maker_callback';
    $content = vc_cache($options, $callback, $a1 = 'foo', $a2 = 'bar');

### Configuration System

Why not just using variable_get/set/del?

Drupal use variable system, load all variables on every pages, make them available
as a global array. It takes a lot of memory. This is not, it just load the
configuration value when you need.

Example configuration file in yaml format:

    # path/to/module/conf/module_name.yaml
    foo: bar
    baz: [foo, bar]

Example Code:

    <?php
    // return bar
    vc_conf('module_name.foo')->get();

    // override configuration value
    vc_conf('module_name.foo')->set('baz');

    // Restore default configuration value.
    vc_conf('module_name.foo')->restore();

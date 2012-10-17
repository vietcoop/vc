### Features

1. Lazy load.
1. Simple KeyValue class.
1. Simple configuration system
1. Lazy hooks:
  1. Lazy for static hooks: permission, menu, theme, views, …
  1. Lazy for dynamic hooks: cron, node_*, …
1. Redis wrapper for PhpRedis.
1. @TODO: Auto run path/to/module/lib/Cron.php > {ModuleName}Cron->cron*().
1. @TODO: Callback wrapper, supports annotations.

### Lazy Load

To use this feature. Your module need implementing hook_vc_api().
Your class named `ModuleNameFoo`, vc will be loaded the file in
`/path/to/module_name/lib/Foo.php`.

  - `ModuleName` -> `/path/to/module_name/lib/ModuleName.php`
  - `ModuleNameFoo` -> `/path/to/module_name/lib/Foo.php`
  - `ModuleName_Foo_Bar` -> `/path/to/module_name/lib/Foo/Bar.php`

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

    <?php
    /**
     * @cache($cid = __FUNCTION__, $bin = 'cache', $expire = '+ 30 minutes')
     */
    function foo() {}

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

### Configuration System

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
    vc_conf('module_name._name')->restore();

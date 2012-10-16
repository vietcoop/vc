### Features

1. Lazy load.
1. Simple configuration system.
1. Simple KeyValue class.
1. YAML configuration style for hooks: permission, menu, theme, views.
1. @TODO: Auto run path/to/module/lib/Cron.php > {ModuleName}Cron->cron*().
1. @TODO: Callback wrapper, supports annotations.

### Lazy Load

To use this feature. Your module need implementing hook_vc_api().
Your class named `ModuleNameFoo`, vc will be loaded the file in
`/path/to/module/lib/Foo.php`.

### KeyValue class

    <?php
    $kv = new VCKeyValue($collection = 'my_collection');
    $kv->set('foo', 'bar');
    $kv->setMultiple(array('foo' => 'bar', 'baz' => 'foo'));
    $kv->delete('foo');
    $kv->deleteMultiple(array('foo', 'baz');

### Function Callback Wrapper

Support annotations.

Example, this your function is

    <?php

    /**
     * @cache($cid = __FUNCTION__, $bin = 'cache', $expire = '+ 30 minutes')
     * @truncate_utf8().
     * @trim().
     */
    function foo($a1, $a2) {
      $my_complex_result = '…';
      return $my_complex_result; // ' Hello  '
    }

Usage:

    <?php
    $result = vc_wrapper('foo', $a1, $a2);

### Configuration System

Example configuration file in yaml format:

  # module_name.yaml
  foo: bar
  baz: [foo, bar]

Example Code:

  // return bar
  vc_conf('module_name.foo')->get();

  // override configuration value
  vc_conf('module_name.foo')->set('baz');

  // Restore default configuration value.
  vc_conf('module_name._name')->restore();

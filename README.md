### Features

- Lazy load.
- Simple configuration system.
- YAML configuration style for hooks: permission, menu, theme, views.
- Auto run path/to/module/lib/Cron.php > {ModuleName}Cron->cron*().

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

<?php

class Vc_Import_Views {
  public static function importAll() {
    foreach (vc_get_module_apis() as $module => $info) {
      $dir = DRUPAL_ROOT . '/' . drupal_get_path('module', $module) . '/config/views/';
      if (is_dir($dir)) {
        foreach (file_scan_directory($dir, '/\.views\.php$/') as $filename) {
          if (function_exists('drush_print_r')) {
            drush_print_r("Imporing {$filename->uri}");
          }
          static::import($filename->uri);
        }
      }
    }
  }

  public static function import($filename) {
    if (!file_exists($filename)) {
      $msg = "File is not existing: {$filename}";
      throw new Exception($msg);
    }

    $name = basename($filename, '.views.php');
    $name = basename($name, '.php');

    $code = file_get_contents($filename);
    return static::importCode($code, $name = '', TRUE, FALSE);
  }

  public static function importCode($code, $name = '', $name_override = FALSE, $bypass_validation = FALSE) {
    $view = '';
    views_include('view');

    // Be forgiving if someone pastes views code that starts with '<?php'.
    if (substr($code, 0, 5) == '<?php') {
      $code = substr($code, 5);
    }
    ob_start();
    eval($code);
    ob_end_clean();

    if (!is_object($view)) {
      $msg = t('Unable to interpret view code.');
      throw new Exception($msg);
    }

    if (empty($view->api_version) || $view->api_version < 2) {
      $msg = t('That view is not compatible with this version of Views.
        If you have a view from views1 you have to go to a drupal6 installation and import it there.');
      throw new Exception($msg);
    }
    elseif (version_compare($view->api_version, views_api_version(), '>')) {
      $msg = t('That view is created for the version @import_version of views, but you only have @api_version', array(
        '@import_version' => $view->api_version,
        '@api_version' => views_api_version()));
      throw new Exception($msg);
    }

    // View name must be alphanumeric or underscores, no other punctuation.
    if (!empty($name) && preg_match('/[^a-zA-Z0-9_]/', $name)) {
      $msg = t('View name must be alphanumeric or underscores only.');
      throw new Exception($msg);
    }

    if ($name) {
      $view->name = $name;
    }

    $test = views_get_view($view->name);
    if (!$name_override) {
      if ($test && $test->type != t('Default')) {
        $msg = t('A view by that name already exists; please choose a different name');
        throw new Exception($msg);
      }
    }
    else {
      if ($test->vid) {
        $view->vid = $test->vid;
      }
    }

    // Make sure base table gets set properly if it got moved.
    $view->update();

    $view->init_display();

    $broken = FALSE;

    // Bypass the validation of view pluigns/handlers if option is checked.
    if (!$bypass_validation) {
      // Make sure that all plugins and handlers needed by this view actually exist.
      foreach ($view->display as $id => $display) {
        if (empty($display->handler) || !empty($display->handler->broken)) {
          drupal_set_message(t('Display plugin @plugin is not available.', array('@plugin' => $display->display_plugin)), 'error');
          $broken = TRUE;
          continue;
        }

        $plugin = views_get_plugin('style', $display->handler->get_option('style_plugin'));
        if (!$plugin) {
          drupal_set_message(t('Style plugin @plugin is not available.', array('@plugin' => $display->handler->get_option('style_plugin'))), 'error');
          $broken = TRUE;
        }
        elseif ($plugin->uses_row_plugin()) {
          $plugin = views_get_plugin('row', $display->handler->get_option('row_plugin'));
          if (!$plugin) {
            $msg = t('Row plugin @plugin is not available.', array('@plugin' => $display->handler->get_option('row_plugin')));
            throw new Exception($msg);
          }
        }

        foreach (views_object_types() as $type => $info) {
          $handlers = $display->handler->get_handlers($type);
          if ($handlers) {
            foreach ($handlers as $id => $handler) {
              if ($handler->broken()) {
                $msg = t('@type handler @table.@field is not available.', array(
                  '@type' => $info['stitle'],
                  '@table' => $handler->table,
                  '@field' => $handler->field,
                ));
                throw new Exception($msg);
              }
            }
          }
        }
      }
    }

    $view->save();

    return $view;
  }
}

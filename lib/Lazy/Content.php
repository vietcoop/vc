<?php
/**
 * @file lib/Lazy/Content.php
 */

class Vc_Lazy_Content {
  protected static function parseContent($file) {
    if (!is_file($file)) return FALSE;

    $cache_id = 'cache_' . substr(md5($file), 0, 10);
    if ($cache = cache_get($cache_id)) {
      $mtime = filemtime($file);
      if ($mtime < $cache->created) {
        return $cache->data;
      }
    }

    if (is_file($file) && $content = yaml_parse_file($file)) {
      cache_set($cache_id, $content, 'cache', strtotime('+ 1 day'));
      return $content;
    }

    return FALSE;
  }

  public static function build($file) {
    global $language;
    if (!$content = self::parseContent($file)) return FALSE;

    $meta = isset($content['meta']) ? $content['meta'] : NULL;
    unset($content['meta']);

    // Get content per language context
    if (isset($content[$language->language])) {
      $content = $content[$language->language];
    }
    elseif (isset($content[LANGUAGE_NONE])) {
      $content = $content[LANGUAGE_NONE];
    }

    return self::buildContent($content, $meta);
  }

  public static function buildContent($content, $meta) {
    // Meta values
    $preprocess = isset($meta['preprocess']) ? $meta['preprocess'] : NULL;
    $tokens = isset($meta['tokens']) ? $meta['tokens'] : NULL;
    $after_build = isset($meta['after_build']) ? $meta['after_build'] : NULL;

    if (is_array($content) && isset($content['meta'])) {
      $context_meta = isset($content['meta']) ? $content['meta'] : NULL;
      unset($content['meta']);
    }

    // Preprocess
    if (!empty($preprocess)) {
      foreach ($preprocess as $callback) {
        if (is_callable($callback)) {
          $content = $callback($content);
        }
      }
    }

    // Token
    if (!empty($tokens) && $tokens = self::buildMetaTokens($tokens)) {
      self::applyToken($content, $tokens);
    }

    // After build
    if (!empty($after_build)) {
      foreach ($after_build as $callback) {
        if (is_callable($callback)) {
          $content = $callback($content);
        }
      }
    }

    if (!empty($context_meta)) {
      return self::buildContent($content, $context_meta);
    }

    if (is_array($content) && count($content) === 1) {
      $result = reset($content);
      if (is_string($result)) {
        return $result;
      }
    }

    # kpr($content);
    # kpr(drupal_render($content['content']));
    # exit;

    return $content;
  }

  public static function applyToken(&$content, $tokens) {
    if (is_string($content)) {
      $find = array_keys($tokens);
      foreach ($tokens as $token => $value) {
        $content = str_replace("%{$token}%", $value, $content);
      }
    }

    if (is_array($content)) {
      foreach (array_keys($content) as $key) {
        self::applyToken($content[$key], $tokens);
      }
    }
  }

  public static function buildMetaTokens($tokens) {
    $results = array();
    foreach ($tokens as $token => $info) {
      if (empty($info['callback'])) {
        $results[$token] = NULL;
      }

      $callback = $info['callback'];
      if (is_callable($callback)) {
        $arguments = isset($info['arguments']) ? $info['arguments'] : array();
        $results[$token] = call_user_func_array($callback, $arguments);
      }
      else {
        $results[$token] = FALSE;
      }
    }

    return $results;
  }
}

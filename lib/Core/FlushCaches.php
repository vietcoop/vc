<?php
/**
 * @file lib/Core/FlushCaches.php
 */

class Vc_Core_FlushCaches {
  public static function actionRebuildLazyHooks() {
    $lazy = new VcLazyHook();
    $lazy->buildHooks();
  }
}

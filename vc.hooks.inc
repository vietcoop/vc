<?php
/**
 * @file vc.hooks.inc
 *
 * Core hook implementations.
 */

/**
 * Implements hook_cron().
 */
function vc_cron() {
  Vc_Lazy_Hook::execute('cron');
}

/**
 * Implements hook_flush_caches().
 */
function vc_flush_caches() {
  Vc_Lazy_Hook::execute('flush_caches');
}

/**
 * Implements hook_node_view().
 */
function vc_node_view($node, $view_mode, $langcode) {
  Vc_Lazy_Hook::execute('node_view', $node, $view_mode, $langcode);
}

/**
 * Implements hook_node_view().
 */
function vc_node_delete($node) {
  Vc_Lazy_Hook::execute('node_delete', $node);
}

/**
 * Implements hook_node_insert().
 */
function vc_node_insert($node) {
  Vc_Lazy_Hook::execute('node_insert', $node);
}

/**
 * Implements hook_node_presave().
 */
function vc_node_presave($node) {
  Vc_Lazy_Hook::execute('node_presave', $node);
}

/**
 * Implements hook_modules_enabled().
 */
function vc_modules_enabled($modules) {
  Vc_Lazy_Hook::rebuild(TRUE);
}

/**
 * Implements hook_modules_installed().
 */
function vc_modules_installed($modules) {
  Vc_Lazy_Hook::rebuild(TRUE);
}

/**
 * Implements hook_modules_disabled().
 */
function vc_modules_disabled($modules) {
  Vc_Lazy_Hook::rebuild(TRUE);
}

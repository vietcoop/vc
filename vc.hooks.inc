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
  VcLazyHook::execute('cron');
}

/**
 * Implements hook_flush_caches().
 */
function vc_flush_caches() {
  VcLazyHook::execute('flush_caches');
}

/**
 * Implements hook_node_view().
 */
function vc_node_view($node, $view_mode, $langcode) {
  VcLazyHook::execute('node_view', $node, $view_mode, $langcode);
}

/**
 * Implements hook_node_view().
 */
function vc_node_delete($node) {
  VcLazyHook::execute('node_delete', $node);
}

/**
 * Implements hook_node_insert().
 */
function vc_node_insert($node) {
  VcLazyHook::execute('node_insert', $node);
}

/**
 * Implements hook_node_presave().
 */
function vc_node_presave($node) {
  VcLazyHook::execute('node_presave', $node);
}

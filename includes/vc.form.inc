<?php
/**
 * @file vc.form.inc
 *
 * Form helper functions.
 */

/**
 * Wrapper for form validators.
 */
function vc_form_validate($form, &$form_state) {
  if (empty($form['#vc_validate'])) return;

  foreach ($form['#vc_validate'] as $validate) {
    call_user_func($validate, $form, $form_state);
  }
}

/**
 * Wrapper for form submit callbacks.
 */
function vc_form_submit($form, &$form_state) {
  if (empty($form['#vc_submit'])) return;

  foreach ($form['#vc_submit'] as $submit) {
    call_user_func($submit, $form, $form_state);
  }
}

/**
 * Wrapper for form ajax callback.
 */
function vc_form_ajax(&$form, &$form_state) {
  return call_user_func($form['#vc_ajax'], $form, $form_state);
}

/**
 * Wrapper for form element validators.
 */
function vc_form_element_validate($e, $form_state, $form) {
  if (empty($e['#vc_validate'])) return;

  foreach ($e['#vc_validate'] as $validate) {
    call_user_func($validate, $e, $form_state, $form);
  }
}

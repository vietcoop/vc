<?php

/**
 * Place holder to store methods for fast form element validators.
 */
class Vc_Form_Validate_Element {
  public static function isNumber($e, $form_state, $form) {
    if (isset($e['#value']) && !is_numeric($e['#value'])) {
      $msg = t("Invalid number value for !title", array('!title' => filter_xss_admin($e['#title'])));
      form_error($e, $msg);
    }
  }

  public static function isEmail($e, $form_state, $form) {
    if (isset($e['#value']) && !valid_email_address($e['#value'])) {
      $msg = t("Invalid email address for !title", array('!title' => filter_xss_admin($e['#title'])));
      form_error($e, $msg);
    }
  }
}

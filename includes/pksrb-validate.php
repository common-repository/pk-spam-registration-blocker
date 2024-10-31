<?php

// Restrict direct access to the file
if(!defined('ABSPATH')){ exit; }

// Only run if the class doesn't exists
if(!class_exists('PKSRB_VALIDATE')){
class PKSRB_VALIDATE {

// Constructor method
public function __construct(){
  // Load settings from the DB
  add_action('registration_errors', array($this, 'pksrb_validate_reg'), 10, 3);
  // Before Password Reset
  add_action('password_reset', array($this, 'pksrb_validate_reset'), 10, 2);
  // Before User Logs in
  //add_action('wp_authenticate', array($this, 'pksrb_validate_login', 30, 2));
  add_filter('authenticate', array($this, 'pksrb_validate_login'), 100, 3);
}

// Before User Registration
public function pksrb_validate_reg( $errors, $sanitized_user_login, $user_email ) {
  global $pksrb;
  // If spam protection is enabled
  if($pksrb->pksrb_status=='E' && $pksrb->pksrb_regPage=='Y' && !empty($pksrb->pk_rcSiteKey)){
  // If no pk_captcha
  if( isset($_POST['pk_captcha']) && !empty($_POST['pk_captcha']) ){
	$pk_captcha_token = sanitize_text_field(esc_attr( $_POST['pk_captcha'] ));
	// Validate reCaptcha token
	$validate_captcha = $pksrb->validate_captcha_token($pk_captcha_token);
  if(!$validate_captcha){
	$errors->add( 'pksrb_reg_error', sprintf('<strong>%s</strong>: %s',__( 'Error', 'pk-spam-registration-blocker' ),__( 'Invalid reCaptcha token!', 'pk-spam-registration-blocker' ) ) );
  }
  } else {
	$errors->add( 'pksrb_reg_error', sprintf('<strong>%s</strong>: %s',__( 'Error', 'pk-spam-registration-blocker' ),__( 'Invalid Request!', 'pk-spam-registration-blocker' ) ) );
  }
  }
return $errors;
}

// Before User Logs in
public function pksrb_validate_login( $user, $username, $password ) {
  global $pksrb;
if( !is_wp_error( $user ) ) {
  // If spam protection is enabled
  if($pksrb->pksrb_status=='E' && $pksrb->pksrb_logPage=='Y' && !empty($pksrb->pk_rcSiteKey)){
  // If no pk_captcha
  if( isset($_POST['pk_captcha']) && !empty($_POST['pk_captcha']) ){
	$pk_captcha_token = sanitize_text_field(esc_attr( $_POST['pk_captcha'] ));
	// Validate reCaptcha token
	$validate_captcha = $pksrb->validate_captcha_token($pk_captcha_token);
  if(!$validate_captcha){
	$error = new WP_Error();
	$error->add( 'pksrb_log_error', sprintf('<strong>%s</strong>: %s',__( 'Error', 'pk-spam-registration-blocker' ),__( 'Invalid reCaptcha token!', 'pk-spam-registration-blocker' ) ) );
	return $error;
  }
  } else {
	$error = new WP_Error();
	$error->add( 'pksrb_log_error', sprintf('<strong>%s</strong>: %s',__( 'Error', 'pk-spam-registration-blocker' ),__( 'Invalid Request!', 'pk-spam-registration-blocker' ) ) );
	return $error;
  }
  }
}
return $user;
}

// Before Password Reset
public function pksrb_validate_reset($user, $new_pass) {
  global $pksrb;
  // If spam protection is enabled
  if($pksrb->pksrb_status=='E' && $pksrb->pksrb_resPage=='Y' && !empty($pksrb->pk_rcSiteKey)){
  // If no pk_captcha
  if( isset($_POST['pk_captcha']) && !empty($_POST['pk_captcha']) ){
	$pk_captcha_token = sanitize_text_field(esc_attr( $_POST['pk_captcha'] ));
	// Validate reCaptcha token
	$validate_captcha = $pksrb->validate_captcha_token($pk_captcha_token);
  if(!$validate_captcha){
	$error = new WP_Error();
	$error->add( 'pksrb_res_error', sprintf('<strong>%s</strong>: %s',__( 'Error', 'pk-spam-registration-blocker' ),__( 'Invalid reCaptcha token!', 'pk-spam-registration-blocker' ) ) );
	return $error;
  }
  } else {
	$error = new WP_Error();
	$error->add( 'pksrb_res_error', sprintf('<strong>%s</strong>: %s',__( 'Error', 'pk-spam-registration-blocker' ),__( 'Invalid Request!', 'pk-spam-registration-blocker' ) ) );
	return $error;
  }
  }
}

}
  $pksrb_validate = new PKSRB_VALIDATE();
}

?>

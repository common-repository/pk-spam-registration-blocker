<?php
/**
* Plugin Name:       Pk Spam Registration Blocker
* Plugin URI:        https://pkplugins.com/
* Description:       WordPress plugin to block spam user registration. This plugin prevents bot or spam user registrations on WordPress websites. Try this WP plugin to disable auto registration, stop test/fake user registrations on WP site. The plugin uses Google reCAPTCHA v3 to stop fake user registration.
* Version:           1.1
* Requires at least: 5.2
* Requires PHP:      5.5
* Author:            Pradnyankur Nikam
* Author URI:        https://profiles.wordpress.org/phpsword/
* Text Domain:       pk-spam-registration-blocker
* Domain Path:       /languages
* License:           GPL v2 or later
* License URI:       https://www.gnu.org/licenses/gpl-2.0.html
**/

// Restrict direct access to the file
if(!defined('ABSPATH')){ exit; }

// Start: *** Plugin Activation & Deactivation functions ***
// On plugin activation
function pksrb_on_activation(){
  if(!current_user_can('activate_plugins')){ return; }
  // If required, disable/remove custom options on deactivation on the plugin
}
register_activation_hook(__FILE__, 'pksrb_on_activation');

// On plugin deactivation
function pksrb_on_deactivation(){
  if(!current_user_can('activate_plugins')){ return; }
  // If required, disable/remove custom options on deactivation on the plugin
}
register_deactivation_hook(__FILE__, 'pksrb_on_deactivation');
// End: *** Plugin Activation & Deactivation functions ***

// Only run if the class doesn't exists
if(!class_exists('PKSRB')){
class PKSRB {

// Properties
public $pk_rcSiteKey, $pk_rcSecrKey, $pksrb_status, $pksrb_regPage, $pksrb_logPage, $pksrb_resPage, $pksrb_options;

// Constructor method
public function __construct(){
  // Load settings from the DB
  add_action('init', array($this, 'pksrb_init'));
  // Register custom CSS files
  add_action('init', array($this, 'pksrb_regcss'));
  // Register custom JS files
  add_action('init', array($this, 'pksrb_regjs'));
  // Load custom CSS & JS files
  add_action('wp_enqueue_scripts', array($this, 'pksrb_load_css_js'));
  add_action('admin_enqueue_scripts', array($this, 'pksrb_load_css_js'));
  add_action('login_head', array($this, 'pksrb_load_css_js'));
  // Add Google reCaptcha API on specific pages
  add_action('login_head', array($this, 'pksrb_load_rcapi'));
  // Add extra hidden field on the form
  add_action('login_form', array($this, 'pk_captcha_field'));
  add_action('register_form', array($this, 'pk_captcha_field'));
  add_action('lostpassword_form', array($this, 'pk_captcha_field'));
}

// Load settings from the DB
public function pksrb_init(){
  // Get option value from DB
  $this->pksrb_options = get_option('pksrb_option');
  if($this->pksrb_options){
	$this->pk_rcSiteKey = $this->pksrb_options['pk_rcSiteKey'];
	$this->pk_rcSecrKey = $this->pksrb_options['pk_rcSecrKey'];	
	$this->pksrb_status = $this->pksrb_options['pksrb_status'];
	$this->pksrb_regPage = $this->pksrb_options['pksrb_regPage'];
	$this->pksrb_logPage = $this->pksrb_options['pksrb_logPage'];
	$this->pksrb_resPage = $this->pksrb_options['pksrb_resPage']; 
  }
}
  
// Register custom CSS files
public function pksrb_regcss() {
  wp_register_style('pksrb-css', plugin_dir_url( __FILE__ ) . 'css/pksrb.css' ); 
}
  
// Register custom JS files
public function pksrb_regjs() {
  wp_register_script('pksrb-js', plugin_dir_url( __FILE__ ) . 'js/pksrb.js', array('jquery') );
  wp_register_script('pksrb-gcapi-js', "https://www.google.com/recaptcha/api.js?render={$this->pk_rcSiteKey}" );
}

// Load custom CSS & JS files
public function pksrb_load_css_js() {
  wp_enqueue_style('pksrb-css');
  wp_enqueue_script('jquery');
  wp_enqueue_script('pksrb-js');
  wp_localize_script( 'pksrb-js', 'pksrbParam', array('rcSiteKey' => $this->pk_rcSiteKey) );
}
  
// Function to validate Google reCaptcha
public function validate_captcha_token($token){
  // Get user IP address
  $userIP = $_SERVER['REMOTE_ADDR'];
  // Post data to reCaptcha validation server (for validation)
  $apiURL = 'https://www.google.com/recaptcha/api/siteverify';
  // Get response
  $response = wp_remote_get( add_query_arg( array(
  'secret'   => $this->pk_rcSecrKey,
  'response' => $token,
  'remoteip' => $userIP
  ), $apiURL )
  );
  // Get response body
  $response_body = wp_remote_retrieve_body( $response );
  // json decode
  $responseKeys = json_decode($response_body,true);
  if($responseKeys["success"]) {
	return true;
  } else {
	return false;  
  }
}

// Add Google reCaptcha API on specific pages
public function pksrb_load_rcapi(){
// Load reCaptcha API on selected pages
if( $GLOBALS['pagenow'] === 'wp-login.php' ) {
  if( isset($_GET['action']) && $_GET['action']=='register' ){
	if($this->pksrb_status=='E' && $this->pksrb_regPage=='Y' && !empty($this->pk_rcSiteKey)){
	wp_enqueue_script('pksrb-gcapi-js');
	}
  } else if ( isset($_GET['action']) && $_GET['action']=='lostpassword' ){
	if($this->pksrb_status=='E' && $this->pksrb_resPage=='Y' && !empty($this->pk_rcSiteKey)){
	wp_enqueue_script('pksrb-gcapi-js');
	}  
  } else {
	if($this->pksrb_status=='E' && $this->pksrb_logPage=='Y' && !empty($this->pk_rcSiteKey)){
	wp_enqueue_script('pksrb-gcapi-js');
	} 
  }
}
}

// Add extra hidden field on the form
function pk_captcha_field(){
  echo '<p><input type="hidden" id="pk_captcha" name="pk_captcha" value="pk_captcha"></p>';
}

}
  $pksrb = new PKSRB();
}

// If admin area
if(is_admin()){
// Include admin menu	
require_once plugin_dir_path(__FILE__).'admin/admin-menu.php';
// Load dashboard page
require_once plugin_dir_path(__FILE__).'admin/dashboard-page.php';
}

// Include validation file
require_once plugin_dir_path(__FILE__).'includes/pksrb-validate.php';

?>
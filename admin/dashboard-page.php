<?php

// Restrict direct access to the file
if(!defined('ABSPATH')){ exit; }

// Display the plugin dashboard page
function pksrb_display_dashboard_page(){

// exit if it's not an admin
if(!current_user_can('manage_options')){ return; }

global $pksrb;
// Get selected tab
$default_tab = null;
$sel_tab = isset($_GET['tab']) ? sanitize_text_field(esc_attr( $_GET['tab'] )) : $default_tab;

// If form is submitted
if(isset($_POST['pksrb_submitted']) && $_POST['pksrb_submitted']==='yes'){
// Get form values
$pk_rcSiteKey = sanitize_text_field(trim($_POST['pk_rcSiteKey']));
$pk_rcSecrKey = sanitize_text_field(trim($_POST['pk_rcSecrKey']));
if(isset($_POST['pksrb_status']) && in_array($_POST['pksrb_status'], array('E', 'D'))){
$pksrb_status = sanitize_text_field(trim($_POST['pksrb_status']));
} else { $pksrb_status = 'E'; }
if(isset($_POST['pksrb_regPage']) && $_POST['pksrb_regPage']==='Y'){
$pksrb_regPage = 'Y'; } else { $pksrb_regPage = 'N'; }
if(isset($_POST['pksrb_logPage']) && $_POST['pksrb_logPage']==='Y'){
$pksrb_logPage = 'Y'; } else { $pksrb_logPage = 'N'; }
if(isset($_POST['pksrb_resPage']) && $_POST['pksrb_resPage']==='Y'){
$pksrb_resPage = 'Y'; } else { $pksrb_resPage = 'N'; }
// If Google reCaptcha Site Key & Secret Key are provided, only then save to DB
if( !empty($pk_rcSiteKey) && !empty($pk_rcSecrKey) ){
  // Save values to DB
  $pksrb_options['pk_rcSiteKey'] = $pk_rcSiteKey;
  $pksrb_options['pk_rcSecrKey'] = $pk_rcSecrKey;
  $pksrb_options['pksrb_status'] = $pksrb_status;
  $pksrb_options['pksrb_regPage'] = $pksrb_regPage;
  $pksrb_options['pksrb_logPage'] = $pksrb_logPage;
  $pksrb_options['pksrb_resPage'] = $pksrb_resPage;
  $save = update_option( 'pksrb_option', $pksrb_options );
  if($save){
	$success = __( 'Settings saved successfully!', 'pk-spam-registration-blocker' );
  } else {
	$error = __( 'There was an error, please try again later!', 'pk-spam-registration-blocker' );
  }
} else {
  $error = __( 'You must provide Google reCaptcha Site Key & Secret Key!', 'pk-spam-registration-blocker' );
}
}

// Get settings from the DB
$pksrb->pksrb_init();
if($pksrb->pksrb_options){
  $pk_rcSiteKey = $pksrb->pksrb_options['pk_rcSiteKey'];  
  $pk_rcSecrKey = $pksrb->pksrb_options['pk_rcSecrKey'];  
  $pksrb_status = $pksrb->pksrb_options['pksrb_status'];  
  $pksrb_regPage = $pksrb->pksrb_options['pksrb_regPage'];  
  $pksrb_logPage = $pksrb->pksrb_options['pksrb_logPage'];  
  $pksrb_resPage = $pksrb->pksrb_options['pksrb_resPage'];  
}

?>
<div class="wrap">
<h1><?php echo esc_html(get_admin_page_title() . ' ' . __( 'Dashboard Page', 'pk-spam-registration-blocker' )); ?></h1>
<br />

<?php if( isset($success) && !empty($success) ){ ?>
<div class="notice notice-success is-dismissible"><p><?php echo esc_html($success); ?></p></div>
<?php } ?>
<?php if( isset($error) && !empty($error) ){ ?>
<div class="notice notice-warning is-dismissible"><p><?php echo esc_html($error); ?></p></div>
<?php } ?>

<nav class="nav-tab-wrapper">
 <a href="?page=pksrb" class="nav-tab <?php if($sel_tab===null){ echo 'nav-tab-active'; } ?>"><?php echo __( 'Plugin Settings', 'pk-spam-registration-blocker' ); ?></a>
 <a href="?page=pksrb&tab=documentation" class="nav-tab <?php if($sel_tab==='documentation'){ echo 'nav-tab-active'; } ?>"><?php echo __( 'Documentation', 'pk-spam-registration-blocker' ); ?></a>
 <a href="?page=pksrb&tab=more" class="nav-tab <?php if($sel_tab==='more'){ echo 'nav-tab-active'; } ?>"><?php echo __( 'More Info', 'pk-spam-registration-blocker' ); ?></a>
</nav>
<div class="tab-content">
<?php
switch($sel_tab){
case 'documentation':
?>
<h2><?php echo __( 'How to generate Google reCaptcha site key & secret key?', 'pk-spam-registration-blocker' ); ?></h2>
<p><?php echo __( 'In this short documentation, you will learn how to generate Google reCaptcha site key & secret key. The method is very simple, it needs only 5 steps. The steps are as following.', 'pk-spam-registration-blocker' ); ?></p>
<?php
printf('<p><strong>%s</strong>: %s <a href="https://www.google.com/recaptcha/about/" title="%s" target="_blank">%s</a>. %s <a href="https://www.google.com/recaptcha/admin" title="%s" target="_blank">%s</a>.</p>',
__( 'Step 1', 'pk-spam-registration-blocker' ),
__( 'Go to the official', 'pk-spam-registration-blocker' ),
__( 'Google reCAPTCHA website', 'pk-spam-registration-blocker' ),
__( 'Google reCAPTCHA website', 'pk-spam-registration-blocker' ),
__( 'And navigate to', 'pk-spam-registration-blocker' ),
__( 'v3 Admin Console', 'pk-spam-registration-blocker' ),
__( 'v3 Admin Console', 'pk-spam-registration-blocker' )
);
printf('<p><strong>%s</strong>: %s</p>',
__( 'Step 2', 'pk-spam-registration-blocker' ),
__( 'From right hand side, click the plus icon to register your new site.', 'pk-spam-registration-blocker' )
);
printf('<p><strong>%s</strong>: %s</p>',
__( 'Step 3', 'pk-spam-registration-blocker' ),
__( 'Fill out the "Register a new site" form. Enter your domain, for reCaptcha type select reCaptcha v3. Enter your Gmail account ID in owners filed. Accept the recaptcha terms of service. And finally submit the form.', 'pk-spam-registration-blocker' )
);
printf('<p><strong>%s</strong>: %s</p>',
__( 'Step 4', 'pk-spam-registration-blocker' ),
__( 'On next screen you\'ll find site key and secret key.', 'pk-spam-registration-blocker' )
);
printf('<p><strong>%s</strong>: %s</p>',
__( 'Step 5', 'pk-spam-registration-blocker' ),
__( 'Copy generated site key and secret key. And paste inside your site\'s "Pk Spam Registration Blocker Settings" page.', 'pk-spam-registration-blocker' )
);
?>
<?php
break;
case 'more':
?>
<h2><?php echo __( 'More useful WordPress plugins by pkplugin.com', 'pk-spam-registration-blocker' ); ?></h2>
<?php
printf('<p><a href="https://wordpress.org/plugins/phpsword-google-analytics/" title="%s" target="_blank"><strong>%s</strong></a><br />%s</p>',
__( 'Pk Google Analytics', 'pk-spam-registration-blocker' ),
__( 'Pk Google Analytics', 'pk-spam-registration-blocker' ),
__( 'A WordPress plugin to add Google Analytics code easily on your WordPress websites.', 'pk-spam-registration-blocker' )
);
echo '<hr />';
printf('<p><a href="https://wordpress.org/plugins/phpsword-favicon-manager/" title="%s" target="_blank"><strong>%s</strong></a><br />%s</p>',
__( 'Pk Favicon Manager', 'pk-spam-registration-blocker' ),
__( 'Pk Favicon Manager', 'pk-spam-registration-blocker' ),
__( 'A WordPress plugin to add a favicon image to your WordPress website.', 'pk-spam-registration-blocker' )
);
?>
<?php
break;
default:
?>
<h2><?php echo __( 'Pk Spam Registration Blocker Settings', 'pk-spam-registration-blocker' ); ?></h2>
<form method="post" action="">
<table class="form-table" role="presentation">
<tbody>
<tr>
<th scope="row"><label for="pk_rcSiteKey"><?php echo __( 'Google reCaptcha Site Key', 'pk-spam-registration-blocker' ); ?></label></th>
<td><input name="pk_rcSiteKey" type="text" id="pk_rcSiteKey" class="regular-text" value="<?php if(isset($pk_rcSiteKey) && !empty($pk_rcSiteKey)){ echo esc_html($pk_rcSiteKey); } ?>" ></td>
</tr>
<tr>
<th scope="row"><label for="pk_rcSecrKey"><?php echo __( 'Google reCaptcha Secret Key', 'pk-spam-registration-blocker' ); ?></label></th>
<td><input name="pk_rcSecrKey" type="text" id="pk_rcSecrKey" class="regular-text" value="<?php if(isset($pk_rcSecrKey) && !empty($pk_rcSecrKey)){ echo esc_html($pk_rcSecrKey); } ?>" ></td>
</tr>

<tr>
<th scope="row"><label for="pksrb_status"><?php echo __( 'Enable/Disable spam protection on the whole website', 'pk-spam-registration-blocker' ); ?></label></th>
<td>
<select name="pksrb_status" id="pksrb_status">
  <option value="E" <?php if(isset($pksrb_status) && $pksrb_status=='E'){ echo 'selected'; } ?>><?php echo __( 'Enable', 'pk-spam-registration-blocker' ); ?></option>
  <option value="D" <?php if(isset($pksrb_status) && $pksrb_status=='D'){ echo 'selected'; } ?>><?php echo __( 'Disable', 'pk-spam-registration-blocker' ); ?></option>
</td>
</tr>

<tr>
<th scope="row"><?php echo __( 'Enable/Disable spam protection on specific pages of the site', 'pk-spam-registration-blocker' ); ?></th>
<td>
<label for="pksrb_regPage">
<input name="pksrb_regPage" type="checkbox" id="pksrb_regPage" value="Y" <?php if(isset($pksrb_regPage) && $pksrb_regPage=='Y'){ echo 'checked="checked"'; } ?>><?php echo __( 'Register Page', 'pk-spam-registration-blocker' ); ?></label><br /><br />
<label for="pksrb_logPage">
<input name="pksrb_logPage" type="checkbox" id="pksrb_logPage" value="Y" <?php if(isset($pksrb_logPage) && $pksrb_logPage=='Y'){ echo 'checked="checked"'; } ?>><?php echo __( 'Log in Page', 'pk-spam-registration-blocker' ); ?></label><br /><br />
<label for="pksrb_resPage">
<input name="pksrb_resPage" type="checkbox" id="pksrb_resPage" value="Y" <?php if(isset($pksrb_resPage) && $pksrb_resPage=='Y'){ echo 'checked="checked"'; } ?>><?php echo __( 'Reset Password Page', 'pk-spam-registration-blocker' ); ?></label><br /><br />
</td>
</tr>

</tbody>
</table>
<p class="submit">
<input type="hidden" name="pksrb_submitted" value="yes">
<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __( 'Save Settings', 'pk-spam-registration-blocker' ); ?>">
</p>
</form>
<hr />
<p><?php echo __( 'This plugin has been created and managed by', 'pk-spam-registration-blocker' ); ?> <a href="https://pkplugins.com" title="<?php echo __( 'WordPress plugins by pkplugins.com', 'pk-spam-registration-blocker' ); ?>">pkplugins.com</a> <?php echo __( '(developer Pradnyankur Nikam). If you are satisfied with this plugin, please post your rating and valuable feedback about the plugin.', 'pk-spam-registration-blocker' ); ?></p>
<?php
}
?>
</div>
</div>

<?php
}
?>
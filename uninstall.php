<?php

// if uninstall.php is not called by WordPress, die
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
  die;
}
// Option name in the DB
$option_name = 'pksrb_option';
// Delete option
delete_option( $option_name );

?>
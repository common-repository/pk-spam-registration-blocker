<?php

// Restrict direct access to the file
if(!defined('ABSPATH')){ exit; }

// Add top-level administrative menu & submenu
function pksrb_add_toplevel_menu(){
add_menu_page(
__( 'Pk Spam Registration Blocker', 'pk-spam-registration-blocker' ),
__( 'Pk Spam Registration Blocker', 'pk-spam-registration-blocker' ),
'manage_options', 'pksrb', 'pksrb_display_dashboard_page',
'dashicons-shield', null 
);
}
// Load admin menu with "admin_menu" action hook
add_action('admin_menu', 'pksrb_add_toplevel_menu');

?>
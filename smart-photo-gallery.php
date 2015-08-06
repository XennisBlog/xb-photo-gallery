<?php
/*
Plugin Name: Smart Photo Gallery
Plugin URI: 
Description: 
Version: 0.0.1
Author: Xennis
Text Domain: smart-photo-gallery
*/

/**
 * Plugin name
 */
define('SPG_NAME', dirname(plugin_basename( __FILE__ )));
/**
 * Plugin directory 
 */
define('SPG_DIR', WP_PLUGIN_DIR.'/'.SPG_NAME);
require_once(SPG_DIR.'/src/php/helper.php');

/**
 * Register activation hook 
 */
function spg_register_activation() {
	//require_once(SPG_DIR . '/src/php/config/Db.php');
	//SPG_Config_Db::__setup_database_tables();
}
register_activation_hook( __FILE__, 'spg_register_activation' );

/**
 * Admin enqueue scripts and styles.
 */
function spg_admin_enqueue_scripts() {
    wp_enqueue_style(SPG_NAME, plugins_url('/dist/'.SPG_NAME.'.min.css', __FILE__));
    wp_enqueue_script(SPG_NAME, plugins_url('/dist/'.SPG_NAME.'.js', __FILE__), array('jquery-ui-sortable'));
}
add_action('admin_enqueue_scripts', 'spg_admin_enqueue_scripts');

	/*
 * Admin notices hook
 */
add_action('admin_notices', 'spg_admin_notices');
function spg_admin_notices() {
  if ($notices = get_option('spg_deferred_admin_notices')) {
    foreach ($notices as $notice) {
      echo "<div class='updated'><p>$notice</p></div>";
    }
    delete_option('spg_deferred_admin_notices');
  }
}
	
/*
 * Include scripts
 */
require_once(SPG_DIR.'/'.SPG_NAME.'.shortcodes.php');
require_once(SPG_DIR.'/'.SPG_NAME.'.pages.php');
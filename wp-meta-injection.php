<?php
/**
 * @package   WP Meta Injection
 * @author    mmcachran
 * @license   GPL-2.0+
 *
 * Plugin Name:       WP Meta Injection
 * Plugin URI:        
 * Description:       Inject additional meta data on a post-by-post basis
 * Version:           1.0.1
 * Author:            mmcachran
 * Text Domain:       wp_meta_injection
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WP_META_INJECTION_VERSION', '1.0.1' );

// Are we in DEV mode?
if ( ! defined( 'WP_META_INJECTION' ) ) {
	define( 'WP_META_INJECTION', true );
}

// load the plugin
require_once( plugin_dir_path( __FILE__ ) . 'lib/meta-injection.php' );	
add_action( 'plugins_loaded', array( 'WP_Meta_Injection', 'get_instance' ) );

<?php
/**
 *  * Maintenance
 *
 * @package       stutz-medien/utils-maintenance
 * @author        Stutz Medien
 *
 * @wordpress-plugin
 * Plugin Name:   Maintenance
 * Plugin URI:    https://github.com/Stutz-Medien/Maintenance
 * Description:   Lightweight maintenance screen for your WordPress page.
 * Version:       1.3.5
 * Author:        Stutz Medien
 * Author URI:    https://stutz-medien.ch/
 * Domain Path:   /lang
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'UTILS_MAINTENANCE_VERSION', '1.3.5' );

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

function register_class( $class_name ) {
	if ( class_exists( $class_name ) ) {
		$instance = new $class_name();
		if ( method_exists( $instance, 'register' ) ) {
			$instance->register();
		}
	}
}

register_class( 'Utils\\Plugins\\Maintenance' );
register_class( 'Utils\\Plugins\\Form' );

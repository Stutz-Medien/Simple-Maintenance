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
 * Version:       1.0.0
 * Author:        Stutz Medien
 * Author URI:    https://stutz-medien.ch/
 * Text Domain:   acf
 * Domain Path:   /lang
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) :
	require_once __DIR__ . '/vendor/autoload.php';
endif;

if ( class_exists( 'Utils\\Plugins\\Maintenance' ) ) :
	$maintenance = new Utils\Plugins\Maintenance();
	$maintenance->register();
endif;

if ( class_exists( 'Utils\\Plugins\\Form' ) ) :
	$form = new Utils\Plugins\Form();
	$form->register();
endif;
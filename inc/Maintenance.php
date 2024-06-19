<?php
/**
 * WordPress Theme Feature: Maintenance
 *
 * @since   1.0
 * @package stutz-medien/utils-maintenance
 * @link    https://github.com/Stutz-Medien/Simple-Maintenance
 * @license MIT
 */

namespace Utils\Plugins;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Maintenance {
	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register() {
		$enable_settings = get_option( 'enable_settings' );

		if ( $enable_settings ) {
			add_action( 'template_redirect', [ $this, 'enable_maintenance' ] );
		}

		add_action( 'admin_init', [ $this, 'enable_maintenance' ] );
		add_action( 'admin_bar_menu', [ $this, 'add_maintenance_mode_button' ], 999 );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_post_activate_maintenance_mode', [ $this, 'activate_maintenance_mode' ] );
		add_action( 'admin_post_deactivate_maintenance_mode', [ $this, 'deactivate_maintenance_mode' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_styles' ] );
	}

	public function enqueue_admin_styles() {
		wp_enqueue_style( 'maintenance-style', plugin_dir_url( __DIR__ ) . 'assets/dist/css/style.css', [], '1.0.0' );
	}
	/**
	 * Enable maintenance mode
	 *
	 * @return void
	 */
	public function enable_maintenance() {
		if ( ! current_user_can( 'manage_options' ) ) {
			$title = get_option( 'maintenance_title', 'Site Under Maintenance' );
			$text  = get_option( 'maintenance_message', 'We are currently performing scheduled maintenance. Please check back later.' );
			$img   = '<img src="' . plugin_dir_url( __DIR__ ) . 'assets/src/global/logo.svg" alt="Maintenance">';

			$allowed_html = array(
				'h1'    => array(),
				'p'     => array(),
				'style' => array(),
				'img'   => array(
					'src' => array(),
					'alt' => array(),
				),
			);

			$style = '<style>
			body { 
				background: #f1f1f1; 
				font-family: Arial, sans-serif; 
				display: flex; 
				justify-content: center; 
				align-items: center; 
				height: 500px; 
				text-align: center;
				border: none;
				box-shadow: 1px 1px 1px rgba(3, 7, 18, 0.08),
					4px 5px 4px rgba(3, 7, 18, 0.06),
					9px 12px 9px rgba(3, 7, 18, 0.05),
					16px 20px 15px rgba(3, 7, 18, 0.03),
					25px 32px 24px rgba(3, 7, 18, 0.02);
				background-color: #fff;
				border-radius: 8px;
			}
			h1 { 
				color: #4295a2; 
				border: none;
			}
			p { 
				font-size: 18px; 
				color: #555; 
			}
			img { 
				width: 150px; 
				height: 150px; 
			}
			</style>';

			$message = "<h1>$title</h1><p>$text</p>";

			wp_die( wp_kses( $style . $img . $message, $allowed_html ), esc_html( $title ) );
		}
	}

	/**
	 * Add maintenance mode button to admin bar
	 *
	 * @param WP_Admin_Bar $wp_admin_bar instance
	 * @return void
	 */
	public function add_maintenance_mode_button( $wp_admin_bar ) {
		$args = [
			'id'    => 'maintenance_mode',
			'title' => 'Maintenance Mode',
			'href'  => admin_url( 'options-general.php?page=maintenance-options' ),
		];

		$wp_admin_bar->add_node( $args );

		$this->toggle_maintenance_mode( $wp_admin_bar );
	}

	/**
	 * Toggle maintenance mode
	 *
	 * @param WP_Admin_Bar $wp_admin_bar instance
	 * @return void
	 */
	private function toggle_maintenance_mode( $wp_admin_bar ): void {
		$enable_settings = get_option( 'enable_settings' );

		if ( $enable_settings ) {
			$wp_admin_bar->add_node(
				[
					'id'     => 'deactivate_maintenance_mode',
					'title'  => 'Deactivate',
					'href'   => admin_url( 'admin-post.php?action=deactivate_maintenance_mode' ),
					'parent' => 'maintenance_mode',
				]
			);
		} else {
			$wp_admin_bar->add_node(
				[
					'id'     => 'activate_maintenance_mode',
					'title'  => 'Activate',
					'href'   => admin_url( 'admin-post.php?action=activate_maintenance_mode' ),
					'parent' => 'maintenance_mode',
				]
			);
		}
	}

	/**
	 * Activate maintenance mode
	 *
	 * @return void
	 */
	public function activate_maintenance_mode() {
		update_option( 'enable_settings', '1' );
		$referer = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
		wp_safe_redirect( $referer );
		exit;
	}

	/**
	 * Deactivate maintenance mode
	 *
	 * @return void
	 */
	public function deactivate_maintenance_mode() {
		update_option( 'enable_settings', '0' );
		$referer = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
		wp_safe_redirect( $referer );
		exit;
	}

	/**
	 * Register settings
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'maintenance-settings-group',
			'enable_settings',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'none',
			)
		);

		register_setting(
			'maintenance-settings-group',
			'maintenance_title',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'Maintenance Mode',
			)
		);

		register_setting(
			'maintenance-settings-group',
			'maintenance_message',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'Site is under maintenance. Please check back later.',
			)
		);
	}
}

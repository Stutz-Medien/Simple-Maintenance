<?php
/**
 * WordPress Theme Feature: Maintenance
 *
 * @since   1.0
 * @package stutz-medien/utils-mainenance
 * @link    https://github.com/Stutz-Medien/Mainenance
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
			add_action( 'admin_init', [ $this, 'enable_maintenance' ] );
		}

		add_action( 'template_redirect', [ $this, 'enable_maintenance' ] );
		add_action( 'admin_bar_menu', [ $this, 'add_maintenance_mode_button' ], 999 );
		add_action( 'admin_menu', [ $this, 'create_settings_page' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
	}

	/**
	 * Enable maintenance mode
	 *
	 * @return void
	 */
	public function enable_maintenance() {
		if ( ! current_user_can( 'manage_options' ) ) {
			$message = get_option( 'maintenance_message', 'Site is under maintenance. Please check back later.' );

			wp_die( esc_html( $message ), 'Maintenance Mode', 503 );
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
	}

	/**
	 * Create settings page
	 *
	 * @return void
	 */
	public function create_settings_page() {
		add_submenu_page(
			'options-general.php',
			'Maintenance Options',
			'Maintenance',
			'manage_options',
			'maintenance-options',
			array( $this, 'maintenance_options_page' )
		);
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
			'maintenance_message',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'Site is under maintenance. Please check back later.',
			)
		);
	}

	public function maintenance_options_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$this->process_form();

		$this->display_form();
	}

	/**
	 * Process form
	 *
	 * @return void
	 */
	private function process_form() {
		if ( isset( $_POST['hide_settings'] ) ) {
			if ( ! check_admin_referer( 'update-options' ) ) {
				wp_die( 'Nonce verification failed' );
			}

			$enable_settings = isset( $_POST['enable_settings'] ) ? 1 : 0;
			update_option( 'hide_settings', $enable_settings );

			$maintenance_message = isset( $_POST['maintenance_message'] ) ? sanitize_text_field( wp_unslash( $_POST['maintenance_message'] ) ) : '';
			update_option( 'maintenance_message', $maintenance_message );
		}
	}

	/**
	 * Display form
	 *
	 * @return void
	 */
	private function display_form() {
		echo '<div class="wrap">';
		echo '<h1>' . esc_html( get_admin_page_title() ) . '</h1>';
		echo '<form method="post" action="options.php">';
		wp_nonce_field( 'update-options' );

		settings_fields( 'maintenance-settings-group' );

		do_settings_sections( 'maintenance-options' );

		$enable_settings = get_option( 'enable_settings' );

		$maintenance_message = get_option( 'maintenance_message' );

		echo '<table class="form-table">';
		echo '<tr valign="top">';
		echo '<th scope="row">Enable Maintenance Screen</th>';
		echo '<td><input type="checkbox" id="enable_settings" name="enable_settings" value="1" ' . checked( 1, $enable_settings, false ) . ' /></td>';
		echo "<td><input type='text' name='maintenance_message' value='" . esc_attr( $maintenance_message ) . "' /></td>";
		echo '</tr>';
		echo '</table>';

		submit_button( 'Save Settings' );

		echo '</form>';
		echo '</div>';
	}
}

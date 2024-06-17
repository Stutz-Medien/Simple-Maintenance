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
			add_action( 'template_redirect', [ $this, 'enable_maintenance' ] );
		}

		add_action( 'admin_init', [ $this, 'enable_maintenance' ] );
		add_action( 'admin_bar_menu', [ $this, 'add_maintenance_mode_button' ], 999 );
		add_action( 'admin_menu', [ $this, 'create_settings_page' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_post_activate_maintenance_mode', [ $this, 'activate_maintenance_mode' ] );
		add_action( 'admin_post_deactivate_maintenance_mode', [ $this, 'deactivate_maintenance_mode' ] );
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
				box-shadow: none; 
				background-color: #fff;
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

	public function activate_maintenance_mode() {
		update_option( 'enable_settings', '1' );
		$referer = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
		wp_safe_redirect( $referer );
		exit;
	}

	public function deactivate_maintenance_mode() {
		update_option( 'enable_settings', '0' );
		$referer = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
		wp_safe_redirect( $referer );
		exit;
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

			$maintenance_title = isset( $_POST['maintenance_title'] ) ? sanitize_text_field( wp_unslash( $_POST['maintenance_title'] ) ) : '';
			update_option( 'maintenance_title', $maintenance_title );

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

		$maintenance_title   = get_option( 'maintenance_title' );
		$maintenance_message = get_option( 'maintenance_message' );

		echo '<table class="form-table">';
		echo '<tr valign="top">';
		echo '<th scope="row">Enable Maintenance Screen</th>';
		echo '<td><input type="checkbox" id="enable_settings" name="enable_settings" value="1" ' . checked( 1, $enable_settings, false ) . ' /></td>';
		echo '</tr>';
		echo '<tr valign="top">';
		echo '<th scope="row">Maintenance Texts</th>';
		echo '</tr>';
		echo '<tr valign="top">';
		echo "<td><input type='text' name='maintenance_title' value='" . esc_attr( $maintenance_title ) . "' /></td>";
		echo '</tr>';
		echo '<tr valign="top">';
		echo '<td><textarea type="text" id="maintenance_message" name="maintenance_message">' . esc_textarea( $maintenance_message ) . '</textarea></td>';
		echo '</tr>';
		echo '</table>';

		submit_button( 'Save Settings' );

		echo '</form>';
		echo '</div>';
	}
}

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

class Form {
	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_menu', [ $this, 'create_settings_page' ] );
	}

	/**
	 * Create settings page
	 *
	 * @return void
	 */
	public function create_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

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
	 * Register hooks
	 *
	 * @return void
	 */
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

			$maintenance_title_color = isset( $_POST['maintenance_title_color'] ) ? sanitize_text_field( wp_unslash( $_POST['maintenance_title_color'] ) ) : '';
			update_option( 'maintenance_title_color', $maintenance_title_color );

			$maintenance_logo = isset( $_POST['maintenance_logo'] ) ? sanitize_text_field( wp_unslash( $_POST['maintenance_logo'] ) ) : '';
			update_option( 'maintenance_logo', $maintenance_logo );
		}
	}

	/**
	 * Display form
	 *
	 * @return void
	 */
	private function display_form() {
		echo '<div class="wrap maintenance">';
		echo '<div class="maintenance-header">';
		echo '<h1 class="main-heading">' . esc_html( get_admin_page_title() ) . '</h1>';
		echo '<img class="main-logo" src="' . esc_url( plugins_url( 'assets/src/global/logo.svg', __DIR__ ) ) . '" alt="Maintenance" width="100" height="100">';
		echo '</div>';
		echo '<form method="post" action="options.php">';
		wp_nonce_field( 'update-options' );

		settings_fields( 'maintenance-settings-group' );

		do_settings_sections( 'maintenance-options' );

		$enable_settings = get_option( 'enable_settings' );

		$maintenance_logo        = get_option( 'maintenance_logo' );
		$maintenance_title       = get_option( 'maintenance_title' );
		$maintenance_message     = get_option( 'maintenance_message' );
		$maintenance_title_color = get_option( 'maintenance_title_color' );

		echo '<div class="maintenance-inner">';
		echo '<div class="maintenance-field flex-field">';
		echo '<h2 scope="row">Enable Maintenance Screen</h2>';
		echo '<span><input type="checkbox" id="enable_settings" name="enable_settings" value="1" ' . checked( 1, $enable_settings, false ) . ' /></span>';
		echo '</div>';
		echo '<h2 class="maintenance-field-title">Maintenance Logo</h2>';
		echo '<div class="maintenance-field">';
		echo '<img id="maintenance-logo-preview" src="' . esc_url( $maintenance_logo ) . '" alt="Logo" width="150" height="auto">';
		echo '<div class="maintenance-field">';
		echo '<p>Logo URL</p>';
		echo '<div class="select-field">';
		echo '<input type="text" id="maintenance-logo" name="maintenance_logo" value="' . esc_attr( $maintenance_logo ) . '" />';
		echo '<button type="button" class="button" id="upload-logo-button">Select from Library</button>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
		echo '<h2 class="maintenance-field-title">Maintenance Texts</h2>';
		echo '<div class="maintenance-field">';
		echo "<div><p>Title</p><input type='text' name='maintenance_title' value='" . esc_attr( $maintenance_title ) . "' /></div>";
		echo '</div>';
		echo '<div class="maintenance-field">';
		echo "<div><p>Title Color</p><input type='text' name='maintenance_title_color' value='" . esc_attr( $maintenance_title_color ) . "' /></div>";
		echo '</div>';
		echo '<div class="maintenance-field">';
		echo '<div><p>Text</p><textarea type="text" id="maintenance_message" name="maintenance_message">' . esc_textarea( $maintenance_message ) . '</textarea></div>';
		echo '</div>';
		echo '</div>';

		submit_button( 'Save Settings' );

		echo '</form>';
		echo '<p>Coded with ❤️ by <a href="https://stutz-medien.ch" target="_blank">Stutz Medien</a></p>';
		echo '<small>v' . esc_html( UTILS_MAINTENANCE_VERSION ) . '</small>';
		echo '</div>';
	}
}

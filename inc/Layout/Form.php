<?php
/**
 * WordPress Theme Feature: Maintenance
 *
 * @since   1.0
 * @package stutz-medien/utils-mainenance
 * @link    https://github.com/Stutz-Medien/Mainenance
 * @license MIT
 */

namespace Utils\Plugins\Maintenance\Layout;

class Form {
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

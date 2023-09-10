<?php
defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/**
 * This function registers the 'amateurtv_affiliate' setting for the 'amateurtv_header' option group.
 *
 * @since 1.0.0
 */
add_action(
	'admin_init',
	function () {

		// Register the 'amateurtv_affiliate' setting.
		register_setting( 'amateurtv_header', 'amateurtv_affiliate' );
	}
);

/**
 * This function adds an options page to the WordPress admin menu for the 'Amateur TV' plugin.
 *
 * @since 1.0.0
 */
add_action(
	'admin_menu',
	function () {

		// Add an options page to the WordPress admin menu.
		add_options_page(
			__( 'Amateur TV Settings', 'amateur-tv' ), // Page title.
			__( 'Amateur TV', 'amateur-tv' ), // Menu title.
			'manage_options', // Capability required to access the page.
			'amateurtv_header', // Menu slug.
			'amateurtv_settings' // Callback function to render the page content.
		);
	}
);

/**
 * This function loads the translation files for the 'Amateur TV' plugin.
 *
 * @since 1.0.0
 */
add_action(
	'plugins_loaded',
	function () {

		// Load the translation files for the 'Amateur TV' plugin.
		load_plugin_textdomain(
			'amateur-tv', // Text domain.
			false, // Deprecated argument. Should always be false.
			AMATEURTV_DIR . '/languages/' // Path to the languages directory.
		);
	}
);

/**
 * This function generates the HTML for the 'Amateur TV' settings page.
 *
 * @since 1.0.0
 */
function amateurtv_settings() {

	if ( ! current_user_can( 'manage_options' ) ) {
		// If the current user doesn't have the 'manage_options' capability, don't show anything.
		wp_die();
	}

	// Create a nonce for the settings form.
	$nonce_action = 'amateurtv_settings_nonce';
	$nonce_name   = 'amateurtv_settings_nonce';
	wp_nonce_field( $nonce_action, $nonce_name );

	?>
	<div class="wrap">
		<h2><?php _e( 'Amateur TV Settings', 'amateur-tv' ); ?></h2>
		<form action="options.php" method="post">
		<?php

			// Output the nonce as a hidden form field.
			echo '<input type="hidden" name="' . esc_attr( $nonce_name ) . '" value="' . esc_attr( wp_create_nonce( $nonce_action ) ) . '">';

			// Output the settings fields.
			settings_fields( 'amateurtv_header' );
			do_settings_fields( 'amateurtv_header', '' );

		?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label><?php _e( 'Affiliate code', 'amateur-tv' ); ?></label></th>
					<td><input type="text" name="amateurtv_affiliate" value="<?php echo esc_attr( get_option( 'amateurtv_affiliate' ) ); ?>"></td>
				</tr>
			</table>
			<?php

				// Output a submit button with a nonce for extra security.
				submit_button( __( 'Save Changes', 'amateur-tv' ), 'primary', 'submit', true, array( 'id' => 'amateurtv_submit' ) );

			?>
		</form>
	</div>
	<?php
}

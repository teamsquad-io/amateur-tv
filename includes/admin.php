<?php
// Make sure the file is not directly accessible.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you cannot directly access this file.' );
}

/**
  * Registers setting for the AmateurTV header.
  * @since 1.2.0
  * @return void
**/
add_action( 'admin_init', function() {

	register_setting( 'amateurtv_header', 'amateurtv_affiliate' );

});

/**
	* Adds an options page to the WordPress admin menu for the Amateur TV plugin.
	* @since 1.2.0
	* @return void
**/
add_action( 'admin_menu', function() {

	add_options_page( __( 'Amateur TV Settings', 'amateur-tv'), __( 'Amateur TV', 'amateur-tv'), 'manage_options', 'amateurtv_header', 'amateurtv_settings' );

});

/**
	* Load the plugin text domain for localization.
	* @since 1.2.0
	* @return void
**/
add_action( 'plugins_loaded', function() {

	load_plugin_textdomain(
		'amateur-tv',
		false,
		AMATEURTV_DIR . '/languages/'
	);

});

/**
	* Renders the Amateur TV settings page.
	* @return void
**/
function amateurtv_settings() {

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die();
	}

	// Add nonce field to form
	wp_nonce_field( 'amateurtv_settings', 'amateurtv_settings_nonce' );
?>
	<div class="wrap">
		<h2><?php _e( 'Amateur TV Settings', 'amateur-tv' ); ?></h2>
		<form action="options.php" method="post">
			<?php
			// Verify nonce when form is submitted
			if ( isset( $_POST['amateurtv_settings_nonce'] ) && wp_verify_nonce( $_POST['amateurtv_settings_nonce'], 'amateurtv_settings' ) ) {
				// Nonce is valid, save form data
				update_option( 'amateurtv_affiliate', sanitize_text_field( $_POST['amateurtv_affiliate'] ) );
				echo '<div class="updated"><p>' . __( 'Settings saved.', 'amateur-tv' ) . '</p></div>';
			}
			?>
			<?php settings_fields( 'amateurtv_header' ); ?>
			<?php do_settings_fields( 'amateurtv_header', '' ); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label><?php _e( 'Affiliate code', 'amateur-tv');?></label></th>
					<td><input type="text" name="amateurtv_affiliate" value="<?php echo esc_attr( get_option( 'amateurtv_affiliate' ) ); ?>"></td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
<?php
}

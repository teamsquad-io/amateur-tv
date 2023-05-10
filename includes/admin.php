<?php
defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/**
 * This function registers the 'amateurtv_affiliate' setting for the 'amateurtv_header' option group.
 *
 * @since 1.0.0
 */
add_action( 'admin_init', function () {

	// Register the 'amateurtv_affiliate' setting.
	register_setting( 
		'amateurtv_header', 
		'amateurtv_affiliate',
		array( 
			'type' => 'string', 
			'show_in_rest' => true,
		)
	);

	// Register the 'amateurtv_whitelable' setting.
	register_setting(
		'amateurtv_header', 
		'amateurtv_whitelabel', 
		array( 
			'type' => 'string', 
			'show_in_rest' => true,
			'sanitize_callback' => function( $domain ) {
				// remove any protocol information and trailing slash
				return str_replace( array( 'https://', 'http://' ), '', untrailingslashit( sanitize_text_field( $domain ) ) );
			},
		) 
	);

} );

/**
 * This function adds an options page to the WordPress admin menu for the 'Amateur TV' plugin.
 *
 * @since 1.0.0
 */
add_action( 'admin_menu', function () {

	// Add an options page to the WordPress admin menu.
	add_options_page(
		__( 'Amateur TV Settings', 'amateur-tv' ), // Page title.
		__( 'Amateur TV', 'amateur-tv' ), // Menu title.
		'manage_options', // Capability required to access the page.
		'amateurtv_header', // Menu slug.
		'amateurtv_settings' // Callback function to render the page content.
	);

} );

/**
 * This function loads the translation files for the 'Amateur TV' plugin.
 *
 * @since 1.0.0
 */
add_action( 'plugins_loaded', function() {

	// Load the translation files for the 'Amateur TV' plugin.
	load_plugin_textdomain(
		'amateur-tv', // Text domain.
		false, // Deprecated argument. Should always be false.
		AMATEURTV_DIR . '/languages/' // Path to the languages directory.
	);

} );

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
	$nonce_name = 'amateurtv_settings_nonce';
	wp_nonce_field( $nonce_action, $nonce_name );

	include_once AMATEURTV_DIR . '/includes/views/settings.php';
}

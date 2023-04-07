<?php

namespace AmateurTv;

class Admin {

	public function __construct(){
        $this->hooks();
    }

    /**
     * Register all the hooks.
     */
    function hooks(){
        add_action( 'admin_init', array( $this, 'register_options' ) );
		add_action( 'rest_api_init', array( $this, 'register_options' ) );

		add_action( 'plugins_loaded', function() {
			load_plugin_textdomain(
				'amateur-tv',
				false,
				AMATEURTV_DIR . '/languages/'
			);
		} );

		add_action( 'admin_menu', function () {
			add_options_page( __( 'Amateur TV Settings', 'amateur-tv'), __( 'Amateur TV', 'amateur-tv'), 'manage_options', 'amateurtv_header', array( $this, 'settings' ) );
		} );
    }

	function register_options() {
		register_setting( 'amateurtv_header', 'amateurtv_affiliate', array( 'type' => 'string', 'show_in_rest' => true ) );
	}

	function settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}
?>
	<div class="wrap">
		<h2><?php _e( 'Amateur TV Settings', 'amateur-tv');?></h2>
		<form action="options.php" method="post">
			<?php settings_fields( 'amateurtv_header' ); ?>
			<?php do_settings_fields( 'amateurtv_header', '' ); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label>
							<?php _e( 'Affiliate code', 'amateur-tv');?>
						</label>
					</th>
					<td>
						<input type="text" name="amateurtv_affiliate" value="<?php echo esc_attr( get_option( 'amateurtv_affiliate' ) ); ?>" />
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
	}
}

new \AmateurTv\Admin();

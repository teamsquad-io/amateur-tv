	<div class="wrap">
		<h2><?php _e( 'Amateur TV Settings', 'amateur-tv' );?></h2>
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
					<th scope="row"><label><?php _e( 'Affiliate code', 'amateur-tv');?></label></th>
					<td><input type="text" required name="amateurtv_affiliate" value="<?php echo esc_attr( get_option( 'amateurtv_affiliate' ) ); ?>"></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label><?php _e( 'White Label', 'amateur-tv');?></label></th>
					<td><input type="text" name="amateurtv_whitelabel" value="<?php echo esc_attr( get_option( 'amateurtv_whitelabel' ) ); ?>"></td>
				</tr>
            </table>
			<?php

				// Output a submit button with a nonce for extra security.
				submit_button( __( 'Save Changes', 'amateur-tv' ), 'primary', 'submit', true, array( 'id' => 'amateurtv_submit' ) );

			?>
		</form>
	</div>
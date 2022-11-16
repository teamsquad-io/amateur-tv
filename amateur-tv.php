<?php
/**
 * Plugin Name:       Amateur Tv WP site builder for Affiliates
 * Description:       Simple site builder using blocks with content from http://Amateur.tv to increase your profits as an affiliate. Online cams feed and live cam viewer ready to use on your WP site.
 * Requires at least: 6.0
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Amateur TV
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       amateur-tv
 *
 * @package           amateur-tv
 */


add_action( 'admin_init', function () {
	register_setting( 'atv-header', 'atv_affiliate' );
} );

add_action( 'admin_menu', function () {
	add_options_page( __( 'Amateur TV Settings', 'amateur-tv'), __( 'Amateur TV', 'amateur-tv'), 'manage_options', 'atv-header', 'atv_settings' );
} );

add_action( 'plugins_loaded', function() {
		load_plugin_textdomain(
			'amateur-tv',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages/'
		);
} );

function atv_settings() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die();
	}
?>
	<div class="wrap">
		<h2><?php _e( 'Amateur TV Settings', 'amateur-tv');?></h2>
		<form action="options.php" method="post">
			<?php settings_fields( 'atv-header' ); ?>
			<?php do_settings_fields( 'atv-header', '' ); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label>
							<?php _e( 'Affiliate code', 'amateur-tv');?>
						</label>
					</th>
					<td>
						<input type="text" name="atv_affiliate" value="<?php echo esc_attr( get_option( 'atv_affiliate' ) ); ?>" />
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}



/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function create_block_amateur_tv_block_init() {
  register_block_type( __DIR__ . '/config/feed-block.json', array(
    'render_callback' => 'render_feed'
  ));
}
add_action( 'init', 'create_block_amateur_tv_block_init' );


function render_feed($attributes) {
	if ( is_admin()){
		return;
	}
	$lang = explode( '-', get_bloginfo('language') );
	$lang = reset($lang);

  $url = add_query_arg( array( 
	  'a' => get_option( 'atv_affiliate' ),
	  'genre' => implode(',', ($attributes['genre'] ?? array())),
	  'age' => implode(',', ($attributes['age'] ?? array())),
	  'lang' => $attributes['lang'] ?? 'en',
  ), 'https://public-api.amateur.cash/v3/cache/affiliates/promo/json' );

  $cams = null;
  $response = wp_remote_get( $url );
  if ( ( !is_wp_error($response)) && (200 === wp_remote_retrieve_response_code( $response ) ) ) {
	  $responseBody = json_decode($response['body'], true);
	  $cams = $responseBody['body'];
  }
  if ( ! $cams ) {
	  return;
  }

  $template = '<a href="%s" target="_blank" class="atv-cam">
						<img src="%s" width="216" height="115"/>
						%s
	</a>';


$final = '';
  foreach ( $cams as $cam ) {
	  $inner = '';
	  if($attributes['displayLive'] ?? false){
		  $inner .= sprintf( '<span class="atv-live" style="color: %s">%s</span>', $attributes['liveColor'] ?? '', __('Live', 'amateur-tv' ) );
	  }
	  if($attributes['displayGenres'] ?? false){
		  $inner .= sprintf( '<span class="atv-genre" style="color: %s">%s</span>', $attributes['usernameColor'] ?? '', __( $cam['genre'], 'amateur-tv' ) );
	  }
	  if($attributes['displayUsers'] ?? false){
		  $inner .= sprintf( '<span class="atv-viewers dashicons dashicons-visibility" style="color: %s">%s</span>', $attributes['liveColor'] ?? '', $cam['viewers']);
	  }
	  if($attributes['displayTopic'] ?? false){
		  $inner .= sprintf( '<div class="atv-topic" style="color: %s">%s</div>', $attributes['topicColor'] ?? '', $cam['topic'][$lang]);
	  }
	  $inner .= sprintf( '<span class="atv-username" style="color: %s">%s</span>', $attributes['usernameColor'] ?? '', $cam['username'] );
	  $final .= sprintf( $template, $cam['url'], $cam['image'], $inner );
  }

  return sprintf( '<div class="atv-cams-list atv-front" style="background-color: %s">%s</div>', $attributes['bgColor'] ?? '', $final );
}

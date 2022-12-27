<?php
/**
 * Plugin Name: amateur tv
 * Description: Create your own amateur cam affiliate site, thanks to amateur.tv. Online cams feed and live cams viewer ready to use.
 * Requires at least: 6.0
 * Requires PHP: 7.0
 * Version: 1.0.3
 * Author: amateur.cash
 * License: GPL 2.0
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: amateur-tv
 *
 * @package amateur-tv
 */

add_action( 'admin_init', function () {
	register_setting( 'amateurtv_header', 'amateurtv_affiliate' );
} );

add_action( 'admin_menu', function () {
	add_options_page( __( 'Amateur TV Settings', 'amateur-tv'), __( 'Amateur TV', 'amateur-tv'), 'manage_options', 'amateurtv_header', 'amateurtv_settings' );
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



/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function amateurtv_create_block_init() {
  register_block_type( __DIR__ . '/config/feed-block.json', array(
    'render_callback' => 'amateurtv_render_feed'
  ));
  register_block_type( __DIR__ . '/config/iframe-block.json', array(
    'render_callback' => 'amateurtv_render_iframe'
  ));
}
add_action( 'init', 'amateurtv_create_block_init' );

function amateurtv_render_iframe($attributes) {
	if ( is_admin()){
		return;
	}

	$genre = $attributes['genre'] ?? array();
	$age = $attributes['age'] ?? array();

	$args = array(
		'a' => get_option( 'amateurtv_affiliate' ),
		'lang' => $attributes['lang'] ?? 'en',
	);

	if(!empty($genre)){
		$args['genre'] = implode(',', $genre );
	}
	if(!empty($age)){
		$args['age'] = implode(',', $age );
	}

	$url = add_query_arg( $args, 'https://www.amateur.tv/freecam/embed?width=890&height=580&lazyloadvideo=1&a_mute=1' );

	$iframe = sprintf( '<iframe width="890" height="580" src="%s" frameborder="0" class="atv_lazy_load_iframe"></iframe><script src="https://www.amateur.tv/js/IntersectionObserverIframe.js"></script>', $url );

	return $iframe;

}

function amateurtv_render_feed($attributes) {
	if ( is_admin()){
		return;
	}
	$lang = explode( '-', get_bloginfo('language') );
	$lang = reset($lang);

	$genre = $attributes['genre'] ?? array();
	$age = $attributes['age'] ?? array();

	$args = array(
		'a' => get_option( 'amateurtv_affiliate' ),
		'lang' => $attributes['lang'] ?? 'en',
	);

	if(!empty($genre)){
		$args['genre'] = implode(',', $genre );
	}
	if(!empty($age)){
		$args['age'] = implode(',', $age );
	}

  $url = add_query_arg( $args, 'https://public-api.amateur.cash/v3/cache/affiliates/promo/json' );

  $cams = null;
  $response = wp_remote_get( $url );
  if ( ( !is_wp_error($response)) && (200 === wp_remote_retrieve_response_code( $response ) ) ) {
	  $responseBody = json_decode($response['body'], true);
	  $cams = $responseBody['body'] ?? null;
  }
  if ( ! $cams ) {
	  return;
  }

  $template = '<a href="%s" target="%s" class="atv-cam">
						<img src="%s" width="%d" height="%d"/>
						%s
	</a>';


$final = '';
  foreach ( $cams as $cam ) {
	  $inner = '';
	  if($attributes['displayLive'] ?? false){
		  $inner .= sprintf( '<span class="atv-live" style="color: %s; background-color: %s;">%s</span>', $attributes['liveColor'] ?? '', $attributes['labelBgColor'] ?? '', __('Live', 'amateur-tv' ) );
	  }
	  if($attributes['displayGenre'] ?? false){
		  $inner .= sprintf( '<span class="atv-genre" style="color: %s; background-color: %s;">%s</span>', $attributes['usernameColor'] ?? '', $attributes['labelBgColor'] ?? '', __( $cam['genre'], 'amateur-tv' ) );
	  }
	  if($attributes['displayUsers'] ?? false){
		  $inner .= sprintf( '<span class="atv-viewers" style="color: %s; background-color: %s;"><span class="dashicons dashicons-visibility"></span><span>%s</span></span>', $attributes['liveColor'] ?? '', $attributes['labelBgColor'] ?? '', $cam['viewers']);
	  }
	  if($attributes['displayTopic'] ?? false){
		  $inner .= sprintf( '<div class="atv-topic" style="color: %s; background-color: %s;">%s</div>', $attributes['topicColor'] ?? '', $cam['topic'][$lang]);
	  }
	  $inner .= sprintf( '<span class="atv-username" style="color: %s; background-color: %s;">%s</span>', $attributes['usernameColor'] ?? '', $attributes['labelBgColor'] ?? '', $cam['username'] );

	  $url = $cam['url'];
	  $target = '';
	  if($attributes['targetNew'] ?? false){
		  $target = "_blank";
	  }
	  if($attributes['link'] ?? false){
		  $url = strpos( $attributes['link'], 'http' ) === 0 ? $attributes['link'] : site_url( $attributes['link'] );
	  }
	  $final .= sprintf( $template, $url, $target, $cam['image'], $attributes['imageWidth'] ?? 216, $attributes['imageHeight'] ?? 115, $inner );
  }

  $styles = $classes = array();

  if ( ! empty( $attributes['columnGap'] ?? '' ) ) {
	$styles[] = sprintf( 'gap: %dpx', $attributes['columnGap'] );
  }
  if ( ! empty( $attributes['bgColor'] ?? '' ) ) {
	$styles[] = sprintf( 'background-color: %s', $attributes['bgColor'] );
  }
  if ( ! empty( $attributes['fontFamily'] ?? '' ) ) {
	$classes[] = sprintf( 'has-%s-font-family', $attributes['fontFamily'] );
  }
  if ( ! empty( $attributes['fontSize'] ?? '' ) ) {
	$classes[] = sprintf( 'has-%s-font-size', $attributes['fontSize'] );
  }
  if ( ! empty( $attributes['align'] ?? '' ) ) {
	$classes[] = sprintf( 'align%s', $attributes['align'] );
  }
  if ( ! empty( $attributes['style'] ?? '' ) ) {
	$padding = $attributes['style']['spacing']['padding'] ?? '';
	if ( $padding ) {
		foreach ( $attributes['style']['spacing']['padding'] as $on => $amount ) {
			$amount = str_replace( array( ':', '|' ), array( '(--wp--', '--'), $amount ) . ')';
			$styles[] = sprintf( 'padding-%s: %s', $on, $amount );
		}
	}

	$margin = $attributes['style']['spacing']['margin'] ?? '';
	if ( $margin ) {
		foreach ( $attributes['style']['spacing']['margin'] as $on => $amount ) {
			$amount = str_replace( array( ':', '|' ), array( '(--wp--', '--'), $amount ) . ')';
			$styles[] = sprintf( 'margin-%s: %s', $on, $amount );
		}
	}

	if ( ! empty( $attributes['style']['typography']['fontSize'] ?? '' ) ) {
		$styles[] = sprintf( 'font-size: %s', $attributes['style']['typography']['fontSize'] );
	}

	if ( ! empty( $attributes['style']['typography']['fontStyle'] ?? '' ) ) {
		$styles[] = sprintf( 'font-style: %s', $attributes['style']['typography']['fontStyle'] );
	}

	if ( ! empty( $attributes['style']['typography']['fontWeight'] ?? '' ) ) {
		$styles[] = sprintf( 'font-weight: %s', $attributes['style']['typography']['fontWeight'] );
	}
  }

/*
  echo "<pre>";
  print_r($attributes);
  echo "</pre>";
*/
  return sprintf( '<div class="atv-cams-list atv-front %s" style="%s">%s</div>', implode( ' ', $classes ), implode( '; ', $styles ), $final );
}

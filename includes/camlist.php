<?php

namespace AmateurTv;

class CamlistBlock {

	public function __construct(){
		$this->hooks();
	}

	/**
	 * Register all hooks.
	 */
	function hooks() {
		add_action( 'rest_api_init', function() {
			register_rest_route( 'atv/v1', '/feed/', array(
				'methods'  => 'POST',
				'permission_callback' => '__return_true',
				'callback' => array( $this, 'render_single_block_ajax' )
			));
		});

		add_action( 'init', array( $this, 'register_block' ) );

		add_action( 'enqueue_block_assets', function() {
			if ( has_block('amateur-tv/feed') ) {
				wp_enqueue_script( 'amateur-tv/feed', AMATEURTV_URL . "src/frontend.js", array( 'jquery' ), AMATEURTV_VERSION );
				wp_localize_script( 'amateur-tv/feed', 'atvfconfig', array(
					'url' => get_rest_url( null, '/atv/v1/feed/' ),
				));
			}
		} );
	}


	/**
	 * Registers the block using the metadata loaded from the `block.json` file.
	 * Behind the scenes, it registers also all assets so they can be enqueued
	 * through the block editor in the corresponding context.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_block_type/
	 */
	function register_block() {
		register_block_type( AMATEURTV_DIR . '/config/feed-block.json', array(
			'render_callback' => array( $this, 'render_feed' )
		));
	}

	/**
	 * Renders the feed on the front end.
	 */
	function render_feed($attributes) {
		if ( is_admin()){
			return;
		}

		$final = $this->render_single_block( $attributes );

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

		return sprintf( '<div data-refresh="%d" data-attributes="%s" class="atv-cams-list atv-front %s" style="%s">%s</div>', esc_attr( $attributes['autoRefresh'] ), esc_attr(json_encode( $attributes )), implode( ' ', $classes ), implode( '; ', $styles ), $final );
	}

	/**
	 * Renders a single block on the front end through ajax.
	 */
	function render_single_block_ajax( \WP_REST_Request $request ) {
		$final = $this->render_single_block( $request->get_params( 'attributes ')['attributes'] );
		return new \WP_REST_Response( array( 'success' => true, 'html' => $final ) );
	}

	/**
	 * Renders a single block on the front end.
	 */
	function render_single_block( $attributes ) {
		$lang = explode( '-', get_bloginfo('language') );
		$lang = reset($lang);

		$genre = $attributes['genre'] ?? array();
		$age = $attributes['age'] ?? array();
		$camLang = $attributes['camLang'] ?? array();
		$tags = $attributes['tags'] ?? array();
		$order = $attributes['order'] ?? '';

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
		if(!empty($camLang)){
			$args['camLang'] = implode(',', $camLang );
		}
		if(!empty($tags)){
			$args['tags'] = implode(',', $tags );
		}
		if(!empty($age)){
			$args['order'] = $order;
		}

		$url = add_query_arg( $args, $attributes['api'] );

		$cams = null;
		$response = wp_remote_get( $url );
		if ( ( !is_wp_error($response)) && (200 === wp_remote_retrieve_response_code( $response ) ) ) {
			$responseBody = json_decode($response['body'], true);
			$cams = $responseBody['body'] ?? null;
		}
		if ( ! $cams ) {
			return $final;
		}

		$template = '<a href="%s" target="%s" class="atv-cam">
							<img src="%s" width="%d" height="%d" style="max-height: %dpx"/>
							%s
		</a>';


		$final = '';
		$final_cams = $cams;
		if ( $attributes['count'] > 0 ) {
			$final_cams = array_slice( $final_cams, 0, intval( $attributes['count'] ) );
		}
		
		foreach ( $final_cams as $cam ) {
			$inner = '';
			if($attributes['displayLive'] === true || $attributes['displayLive'] === 'true'){
				$inner .= sprintf( '<span class="atv-live atv-padding" style="color: %s; background-color: %s;">%s</span>', $attributes['liveColor'] ?? '', '', __('Live', 'amateur-tv' ) );
			}
			if($attributes['displayGenre'] === true || $attributes['displayGenre'] === 'true'){
				$inner .= sprintf( '<span class="atv-genre atv-padding" style="color: %s; background-color: %s;">%s</span>', $attributes['usernameColor'] ?? '', '', __( $cam['genre'], 'amateur-tv' ) );
			}
			if($attributes['displayUsers'] === true || $attributes['displayUsers'] === 'true'){
				$inner .= sprintf( '<span class="atv-viewers atv-padding" style="color: %s; background-color: %s;"><span class="dashicons dashicons-visibility"></span><span>%s</span></span>', $attributes['liveColor'] ?? '', '', $cam['viewers']);
			}
			if($attributes['displayTopic'] === true || $attributes['displayTopic'] === 'true'){
				$inner .= sprintf( '<div class="atv-topic atv-padding atv-rounded" style="color: %s; background-color: %s;">%s</div>', $attributes['topicColor'] ?? '', $attributes['labelBgColor'] ?? '', $cam['topic'][$lang]);
			}
			$inner .= sprintf( '<span class="atv-username atv-padding atv-rounded" style="color: %s; background-color: %s;">%s</span>', $attributes['usernameColor'] ?? '', $attributes['labelBgColor'] ?? '', $cam['username'] );

			$url = $cam['url'];
			$target = '';
			if($attributes['targetNew'] === true || $attributes['targetNew'] === 'true'){
				$target = "_blank";
			}
			if($attributes['link'] ?? false){
				$url = strpos( $attributes['link'], 'http' ) === 0 ? $attributes['link'] : site_url( $attributes['link'] );
				$url = str_replace(
					array( '{camname}', '{affiliate}' ),
					array( $cam['username'], get_option( 'amateurtv_affiliate' ) ),
					$url
				);
			}
			$final .= sprintf( $template, $url, $target, $cam['image'], $attributes['imageWidth'] ?? 216, $attributes['imageHeight'] ?? 115, $attributes['imageHeight'] ?? 115, $inner );
		}
		return $final;
	}
}

new \AmateurTv\CamlistBlock();
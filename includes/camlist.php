<?php
// Make sure the file is not directly accessible.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you cannot directly access this file.' );
}

/**
	* Initializes a REST API route for the amateur TV feed.
  * @since 1.2.0
  *  @return void
**/
add_action( 'rest_api_init', function() {
	register_rest_route( 'atv/v1', '/feed/', array(
		'methods'  => 'POST',
		'permission_callback' => '__return_true',
		'callback' => 'amateurtv_render_single_block_ajax'
	));
});

/**
  *  Initializes a block for the amateur TV camera list.
  *  @since 1.2.0
  *  @return void
**/
function amateurtv_init_camlist() {
/**
	* Renders the amateur TV camera list.
	* @since 1.2.0
	* @param array $attributes The attributes for the block.
	* @return string The HTML for the block.
**/
	register_block_type( __DIR__ . '/../config/feed-block.json', array(
		'render_callback' => 'amateurtv_render_feed'
	));
}
/**
	* Adds an action to initialize the amateur TV camera list block.
	* @since 1.2.0
	* @return void
**/
add_action( 'init', 'amateurtv_init_camlist' );

/**
	* Adds an action to enqueue the assets for the amateur TV feed block.
	* @since 1.2.0
	* @return void
**/
add_action( 'enqueue_block_assets', function() {
/**
	* Checks if the amateur TV feed block is present in the content.
	* @since 1.2.0
	* @return boolean
**/
	if( has_block( 'amateur-tv/feed' ) ) {
/**
	* Enqueues the amateur TV feed block script.
	* @since 1.2.0
	* @param string $handle The script handle.
	* @param string $src The script source URL.
	* @param array $deps An array of script dependencies.
	* @param string $ver The script version.
	* @param bool $in_footer Whether the script should be placed in the footer.
	* @return void
**/
		wp_enqueue_script( 'amateur-tv/feed', AMATEURTV_URL . 'src/frontend.js', array( 'jquery' ), AMATEURTV_VERSION );
/**
	* Localizes the amateur TV feed block script.
	* @since 1.2.0
	* @param string $handle The script handle.
	* @param string $object_name The name of the object to export.
	* @param array $l10n An array of key-value pairs to be exported to the script.
	* @return void
**/
		wp_localize_script( 'amateur-tv/feed', 'atvfconfig', array(
			'url' => get_rest_url( null, '/atv/v1/feed/' ),
		));
	}
});

/**
	* Renders the amateur TV feed block
	* @param array $attributes The block attributes
	* @return string The rendered HTML
**/
function amateurtv_render_feed( $attributes ) {

	if ( is_admin() ) {
		return;
	}

	$lang = explode( '-', get_bloginfo('language') );
	$lang = reset( $lang );
	$genre = $attributes['genre'] ?? array();
	$age = $attributes['age'] ?? array();

	$final = amateurtv_render_single_block( $attributes );
	$styles = $classes = array();

	if ( !empty( $attributes['columnGap'] ?? '' ) ) {
		$styles[] = sprintf( 'gap: %dpx', $attributes['columnGap'] );
	}

	if ( !empty( $attributes['bgColor'] ?? '' ) ) {
		$styles[] = sprintf( 'background-color: %s', $attributes['bgColor'] );
	}

	if ( !empty( $attributes['fontFamily'] ?? '' ) ) {
		$classes[] = sprintf( 'has-%s-font-family', $attributes['fontFamily'] );
	}

	if ( !empty( $attributes['fontSize'] ?? '' ) ) {
		$classes[] = sprintf( 'has-%s-font-size', $attributes['fontSize'] );
	}

	if ( !empty( $attributes['align'] ?? '' ) ) {
		$classes[] = sprintf( 'align%s', $attributes['align'] );
	}

	if ( !empty( $attributes['style'] ?? '' ) ) {
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

		if ( !empty( $attributes['style']['typography']['fontSize'] ?? '' ) ) {
			$styles[] = sprintf( 'font-size: %s', $attributes['style']['typography']['fontSize'] );
		}

		if ( !empty( $attributes['style']['typography']['fontStyle'] ?? '' ) ) {
			$styles[] = sprintf( 'font-style: %s', $attributes['style']['typography']['fontStyle'] );
		}

		if ( !empty( $attributes['style']['typography']['fontWeight'] ?? '' ) ) {
			$styles[] = sprintf( 'font-weight: %s', $attributes['style']['typography']['fontWeight'] );
		}
	}

/**
	* The rendered HTML of the amateur TV feed block
**/
	return sprintf( '<div data-refresh="%d" data-attributes="%s" class="atv-cams-list atv-front %s" style="%s">%s</div>', esc_attr( $attributes['autoRefresh'] ), esc_attr( json_encode( $attributes ) ), implode( ' ', $classes ), implode( '; ', $styles ), $final );

}

/**
	* Renders a single AmateurTV block with attributes provided by a REST API request.
	* @param WP_REST_Request $request A REST API request.
	* @return WP_REST_Response A REST API response containing a success flag and the rendered HTML.
**/
function amateurtv_render_single_block_ajax( WP_REST_Request $request ) {
	$final = amateurtv_render_single_block( $request->get_params( 'attributes ')['attributes'] );
	return new WP_REST_Response( array( 'success' => true, 'html' => $final ) );
}

/**
	* Render a single Amateur TV block
	* @param array $attributes An array of attributes for the block
	* 'genre': an array of genre(s)
	* 'age': an array of age range(s)
	* 'lang': the language to use for the block (default is 'en')
	* 'count': the maximum number of cams to display (default is 0 for no limit)
	* 'displayLive': whether to display the live status (default is true)
	* 'displayGenre': whether to display the genre (default is true)
	* 'displayUsers': whether to display the number of viewers (default is true)
	* 'displayTopic': whether to display the topic (default is true)
	* 'targetNew': whether to open the links in a new tab (default is true)
	* 'link': the URL to use as the link for the cams
	* 'imageWidth': the width to use for the cam images (default is 216)
	* 'imageHeight': the height to use for the cam images (default is 115)
	* 'liveColor': the color to use for the live status (default is empty string)
	* 'labelBgColor': the background color to use for the labels (default is empty string)
	* 'usernameColor': the color to use for the username (default is empty string)
	* 'topicColor': the color to use for the topic (default is empty string)
	* 'api': the URL of the API endpoint
	* @return string The rendered HTML for the block
**/

function amateurtv_render_single_block( $attributes ) {

	$lang = explode( '-', get_bloginfo('language') );
	$lang = reset($lang);
	$genre = $attributes['genre'] ?? array();
	$age = $attributes['age'] ?? array();
	$args = array(
		'a' => get_option( 'amateurtv_affiliate' ),
		'lang' => $attributes['lang'] ?? 'en',
	);
	if( !empty($genre) ) {
		$args['genre'] = implode( ',', $genre );
	}
	if( !empty($age) ) {
		$args['age'] = implode( ',', $age );
	}
	$url = add_query_arg( $args, $attributes['api'] );
	$cams = null;

	$response = wp_remote_get( $url );
	if ( ( !is_wp_error($response)) && ( 200 === wp_remote_retrieve_response_code( $response ) ) ) {
		$responseBody = json_decode( $response['body'], true );
		$cams = $responseBody['body'] ?? null;
	}
	if ( !$cams ) {
		return $final;
	}

	$template = '<a href="%s" target="%s" class="atv-cam"><img src="%s" width="%d" height="%d" style="max-height: %dpx">%s</a>';
	$final = '';
	$final_cams = $cams;

	if ( $attributes['count'] > 0 ) {
		$final_cams = array_slice( $final_cams, 0, intval( $attributes['count'] ) );
	}

	foreach ( $final_cams as $cam ) {
		$inner = '';
		if( true === $attributes['displayLive'] || 'true' === $attributes['displayLive'] ) {
			$inner .= sprintf( '<span class="atv-live" style="color: %s; background-color: %s;">%s</span>', $attributes['liveColor'] ?? '', $attributes['labelBgColor'] ?? '', __( 'Live', 'amateur-tv' ) );
		}
		if( true === $attributes['displayGenre'] || 'true' === $attributes['displayGenre'] ) {
			$inner .= sprintf( '<span class="atv-genre" style="color: %s; background-color: %s;">%s</span>', $attributes['usernameColor'] ?? '', $attributes['labelBgColor'] ?? '', __(  $cam['genre'], 'amateur-tv' ) );
		}
		if( true === $attributes['displayUsers'] || 'true' === $attributes['displayUsers'] ) {
			$inner .= sprintf( '<span class="atv-viewers" style="color: %s; background-color: %s;"><span class="dashicons dashicons-visibility"></span><span>%s</span></span>', $attributes['liveColor'] ?? '', $attributes['labelBgColor'] ?? '', $cam['viewers'] );
		}
		if( true === $attributes['displayTopic'] || 'true' === $attributes['displayTopic'] ) {
			$inner .= sprintf( '<div class="atv-topic" style="color: %s; background-color: %s;">%s</div>', $attributes['topicColor'] ?? '', $attributes['labelBgColor'] ?? '', $cam['topic'][$lang] );
		}
		$inner .= sprintf( '<span class="atv-username" style="color: %s; background-color: %s;">%s</span>', $attributes['usernameColor'] ?? '', $attributes['labelBgColor'] ?? '', $cam['username'] );

		$url = $cam['url'];
		$target = '';

		if( true === $attributes['targetNew'] || 'true' === $attributes['targetNew'] ) {
			$target = "_blank";
		}

		if( $attributes['link'] ?? false ) {
			$url = strpos( 0 === $attributes['link'], 'http' ) ? $attributes['link'] : site_url( $attributes['link'] );
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

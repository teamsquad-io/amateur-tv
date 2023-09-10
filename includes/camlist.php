<?php
namespace AmateurTv;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

class CamlistBlock {

	/**
	 * The constructor function.
	 * Registers all the hooks that are necessary for this class.
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Hooks up all the actions and filters for this class.
	 */
	function hooks() {
		add_action(
			'rest_api_init',
			function () {
				register_rest_route(
					'atv/v1',
					'/feed/',
					array(
						'methods'             => 'POST',
						'permission_callback' => '__return_true',
						'callback'            => array( $this, 'render_single_block_ajax' ),
					)
				);
			}
		);

		// Register the block.
		add_action( 'init', array( $this, 'register_block' ) );

		// Enqueue the block assets, including the frontend script.
		add_action(
			'enqueue_block_assets',
			function () {
				if ( has_block( 'amateur-tv/feed' ) ) {
					wp_enqueue_script( 'amateur-tv/feed', AMATEURTV_URL . 'src/frontend.js', array( 'jquery' ), AMATEURTV_VERSION );

					// Set up the configuration for the frontend script.
					wp_localize_script(
						'amateur-tv/feed',
						'atvfconfig',
						array(
							'url' => get_rest_url( null, '/atv/v1/feed/' ),
						)
					);
				}
			}
		);
	}

	/**
	 * Registers the Amateur TV feed block using the metadata loaded from the `block.json` file.
	 *
	 * This function registers the block type and sets the render callback to the `render_feed` method in the current class.
	 * This allows WordPress to use this method to render the content of the block when it is displayed on the front-end.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_block_type/
	 */
	function register_block() {
		register_block_type(
			AMATEURTV_DIR . '/config/feed-block.json',
			array(
				'render_callback' => array( $this, 'render_feed' ),
			)
		);
	}

	/**
	 * Renders the feed on the front end.
	 *
	 * @param array $attributes The attributes passed to the block.
	 *
	 * @return string The HTML to render the feed.
	 */
	function render_feed( $attributes ) {

		// If the current request is in the admin panel, return an empty string to prevent rendering.
		if ( is_admin() ) {
			return;
		}

		// Render the feed using the render_single_block method and store the result in the $final variable.
		$final = $this->render_single_block( $attributes );

		// Initialize empty arrays to store the styles and classes to be applied to the feed container.
		$styles  = $classes = array();
		$classes = array();

		// Add styles for the column gap if it's set in the attributes.
		if ( ! empty( $attributes['columnGap'] ?? '' ) ) {
			$styles[] = sprintf( 'gap: %dpx', $attributes['columnGap'] );
		}

		// Add styles for the background color if it's set in the attributes.
		if ( ! empty( $attributes['bgColor'] ?? '' ) ) {
			$styles[] = sprintf( 'background-color: %s', $attributes['bgColor'] );
		}

		// Add classes for the font family if it's set in the attributes.
		if ( ! empty( $attributes['fontFamily'] ?? '' ) ) {
			$classes[] = sprintf( 'has-%s-font-family', $attributes['fontFamily'] );
		}

		// Add classes for the font size if it's set in the attributes.
		if ( ! empty( $attributes['fontSize'] ?? '' ) ) {
			$classes[] = sprintf( 'has-%s-font-size', $attributes['fontSize'] );
		}

		// Add classes for the text alignment if it's set in the attributes.
		if ( ! empty( $attributes['align'] ?? '' ) ) {
			$classes[] = sprintf( 'align%s', $attributes['align'] );
		}

		// If there are custom styles set in the attributes, add them to the styles array.
		if ( ! empty( $attributes['style'] ?? '' ) ) {

			// If there is padding set in the styles, add padding styles to the styles array.
			$padding = $attributes['style']['spacing']['padding'] ?? '';
			if ( $padding ) {
				foreach ( $attributes['style']['spacing']['padding'] as $on => $amount ) {
					$amount   = str_replace( array( ':', '|' ), array( '(--wp--', '--' ), $amount ) . ')';
					$styles[] = sprintf( 'padding-%s: %s', $on, $amount );
				}
			}

			// If there is margin set in the styles, add margin styles to the styles array.
			$margin = $attributes['style']['spacing']['margin'] ?? '';
			if ( $margin ) {
				foreach ( $attributes['style']['spacing']['margin'] as $on => $amount ) {
					$amount   = str_replace( array( ':', '|' ), array( '(--wp--', '--' ), $amount ) . ')';
					$styles[] = sprintf( 'margin-%s: %s', $on, $amount );
				}
			}

			// If there is a custom font size set in the styles, add the font size style to the styles array.
			if ( ! empty( $attributes['style']['typography']['fontSize'] ?? '' ) ) {
				$styles[] = sprintf( 'font-size: %s', $attributes['style']['typography']['fontSize'] );
			}

			// If there is a custom font style set in the styles, add the font style to the styles array.
			if ( ! empty( $attributes['style']['typography']['fontStyle'] ?? '' ) ) {
				$styles[] = sprintf( 'font-style: %s', $attributes['style']['typography']['fontStyle'] );
			}

			// If there is a custom font weight set in the styles, add the font weight to the styles array.
			if ( ! empty( $attributes['style']['typography']['fontWeight'] ?? '' ) ) {
				$styles[] = sprintf( 'font-weight: %s', $attributes['style']['typography']['fontWeight'] );
			}
		}

		return sprintf( '<div data-refresh="%d" data-attributes="%s" class="atv-cams-list atv-front %s" style="%s">%s</div>', esc_attr( $attributes['autoRefresh'] ), esc_attr( json_encode( $attributes ) ), implode( ' ', $classes ), implode( '; ', $styles ), $final );
	}

	/**
	 * Renders a single block on the front end through ajax.
	 *
	 * @param \WP_REST_Request $request The REST request object.
	 * @return \WP_REST_Response The REST response object containing the rendered block.
	 */
	function render_single_block_ajax( \WP_REST_Request $request ) {

		// Get the attributes passed in the request.
		$attributes = $request->get_params( 'attributes' )['attributes'];

		// Render the block and store the result in the $final variable.
		$final = $this->render_single_block( $attributes );

		// Create a new REST response object with the rendered block HTML.
		return new \WP_REST_Response(
			array(
				'success' => true,
				'html'    => $final,
			)
		);
	}

	/**
	 * Renders a single block on the front end.
	 *
	 * @param array $attributes An array of attributes for the block.
	 * @return string The HTML to render the block.
	 */
	function render_single_block( $attributes ) {

		// Get the language code from the blog settings.
		$lang = explode( '-', get_bloginfo( 'language' ) );
		$lang = reset( $lang );

		// Get the filters and options from the attributes.
		$genre   = $attributes['genre'] ?? array();
		$age     = $attributes['age'] ?? array();
		$camLang = $attributes['camLang'] ?? array();
		$tags    = $attributes['tags'] ?? array();
		$order   = $attributes['order'] ?? '';

		// Set up the API query args based on the attributes.
		$args = array(
			'a'    => get_option( 'amateurtv_affiliate' ),
			'lang' => $attributes['lang'] ?? 'en',
		);

		if ( ! empty( $genre ) ) {
			$args['genre'] = implode( ',', $genre );
		}
		if ( ! empty( $age ) ) {
			$args['age'] = implode( ',', $age );
		}
		if ( ! empty( $camLang ) ) {
			$args['camLang'] = implode( ',', $camLang );
		}
		if ( ! empty( $tags ) ) {
			$args['tags'] = implode( ',', $tags );
		}
		if ( ! empty( $age ) ) {
			$args['order'] = $order;
		}

		// Build the API URL.
		$url = add_query_arg( $args, $attributes['api'] );

		// Fetch the data from the API.
		$cams     = null;
		$response = wp_remote_get( $url );
		if ( ( ! is_wp_error( $response ) ) && ( 200 === wp_remote_retrieve_response_code( $response ) ) ) {
			$responseBody = json_decode( $response['body'], true );
			$cams         = $responseBody['body'] ?? null;
		}
		if ( ! $cams ) {
			return $final;
		}

		// Build the template for each cam item.
		$template = '<a href="%s" target="%s" class="atv-cam"><img src="%s" width="%d" height="%d" style="max-height: %dpx;">%s</a>';

		// Build the HTML for each cam item.
		$final      = '';
		$final_cams = $cams;
		if ( $attributes['count'] > 0 ) {
			$final_cams = array_slice( $final_cams, 0, intval( $attributes['count'] ) );
		}

		// Iterate over the cams and generate HTML output for each cam.
		foreach ( $final_cams as $cam ) {

			$inner = '';

			// Add HTML output for the live status of the cam.
			if ( $attributes['displayLive'] === true || $attributes['displayLive'] === 'true' ) {
				$inner .= sprintf( '<span class="atv-live atv-padding" style="color: %s; background-color: %s;">%s</span>', $attributes['liveColor'] ?? '', '', __( 'Live', 'amateur-tv' ) );
			}

			// Add HTML output for the genre of the cam.
			if ( $attributes['displayGenre'] === true || $attributes['displayGenre'] === 'true' ) {
				$inner .= sprintf( '<span class="atv-genre atv-padding" style="color: %s; background-color: %s;">%s</span>', $attributes['usernameColor'] ?? '', '', __( $cam['genre'], 'amateur-tv' ) );
			}

			// Add HTML output for the number of viewers of the cam.
			if ( $attributes['displayUsers'] === true || $attributes['displayUsers'] === 'true' ) {
				$inner .= sprintf( '<span class="atv-viewers atv-padding" style="color: %s; background-color: %s;"><span class="dashicons dashicons-visibility"></span><span>%s</span></span>', $attributes['liveColor'] ?? '', '', $cam['viewers'] );
			}

			// Add HTML output for the topic of the cam.
			if ( $attributes['displayTopic'] === true || $attributes['displayTopic'] === 'true' ) {
				$inner .= sprintf( '<div class="atv-topic atv-padding atv-rounded" style="color: %s; background-color: %s;">%s</div>', $attributes['topicColor'] ?? '', $attributes['labelBgColor'] ?? '', $cam['topic'][ $lang ] );
			}

			// Add HTML output for the username of the cam.
			$inner .= sprintf( '<span class="atv-username atv-padding atv-rounded" style="color: %s; background-color: %s;">%s</span>', $attributes['usernameColor'] ?? '', $attributes['labelBgColor'] ?? '', $cam['username'] );

			// Generate the URL and target for the cam.
			$url    = $cam['url'];
			$target = '';
			if ( $attributes['targetNew'] === true || $attributes['targetNew'] === 'true' ) {
				$target = '_blank';
			}
			if ( $attributes['link'] ?? false ) {
				$url = strpos( $attributes['link'], 'http' ) === 0 ? $attributes['link'] : site_url( $attributes['link'] );
				$url = str_replace(
					array( '{camname}', '{affiliate}' ),
					array( $cam['username'], get_option( 'amateurtv_affiliate' ) ),
					$url
				);
			}
			// Generate the HTML output for the cam
			$final .= sprintf( $template, $url, $target, $cam['image'], $attributes['imageWidth'] ?? 216, $attributes['imageHeight'] ?? 115, $attributes['imageHeight'] ?? 115, $inner );
		}
		return $final;
	}
}

new \AmateurTv\CamlistBlock();

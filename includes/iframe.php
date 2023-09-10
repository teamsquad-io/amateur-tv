<?php
/**
 * The iFrame Module
 *
 * @package amateur-tv
 */

namespace AmateurTv;

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/**
 * IframeBlock class to handle the rendering and registration of the iframe block.
 */
class IframeBlock {

	/**
	 * Initializes the class by setting up all hooks.
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Register all the hooks required for this class.
	 * Hooks `register_block` method to `init` action.
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_action/
	 */
	public function hooks() {
			add_action( 'init', array( $this, 'register_block' ) );
	}

	/**
	 * Registers the block using the metadata loaded from the `block.json` file.
	 * Behind the scenes, it registers also all assets so they can be enqueued
	 * through the block editor in the corresponding context.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_block_type/
	 */
	public function register_block() {
		register_block_type(
			AMATEURTV_DIR . '/config/iframe-block.json',
			array(
				'render_callback' => array( $this, 'render_iframe' ),
			)
		);
	}

	/**
	 * Renders the iframe on the front end.
	 *
	 * @param array $attributes An array of block attributes.
	 * @return string Returns the HTML output of the iframe block.
	 */
	public function render_iframe( $attributes ) {

		// Check if the user is in the WordPress admin area.
		if ( is_admin() ) {
			return;
		}

		// Set default values for attributes that may not be present.
		$genre    = $attributes['genre'] ?? array();
		$age      = $attributes['age'] ?? array();
		$cam_lang = $attributes['camLang'] ?? array();
		$tags     = $attributes['tags'] ?? array();
		$cam_type = $attributes['camType'] ?? 'popular';
		$cam_name = $attributes['camName'] ?? '';
		$height   = $attributes['iframeHeight'] ?? 580;

		// Construct an array of query arguments for the iframe URL.
		$args = array(
			'a' => get_option( 'amateurtv_affiliate' ),
		);
		if ( ! empty( $genre ) ) {
			$args['genre'] = implode( ',', $genre );
		}
		if ( ! empty( $age ) ) {
			$args['age'] = implode( ',', $age );
		}
		if ( ! empty( $cam_lang ) ) {
			$args['camLang'] = implode( ',', $cam_lang );
		}
		if ( ! empty( $tags ) ) {
			$args['tags'] = implode( ',', $tags );
		}

		// Depending on the type of camera, add an appropriate query argument.
		switch ( $cam_type ) {
			case 'camname':
				if ( ! empty( $cam_name ) ) {
					$args['livecam'] = sanitize_title( $cam_name );
				}
				break;
			case 'camparam':
				if ( ! empty( $_GET['livecam'] ) ) {
					$args['livecam'] = sanitize_title( wp_unslash( $_GET['livecam'] ) );
				}
				break;
		}

		// Set up arrays to hold any classes or styles for the iframe container.
		$classes = array();
		$styles  = array();

		// If the "align" attribute is present, add an appropriate class.
		if ( ! empty( $attributes['align'] ?? '' ) ) {
			$classes[] = sprintf( 'align%s', $attributes['align'] );
		}

		// If the "style" attribute is present, parse the padding and margin styles.
		if ( ! empty( $attributes['style'] ?? '' ) ) {
			$padding = $attributes['style']['spacing']['padding'] ?? '';
			if ( $padding ) {
				foreach ( $attributes['style']['spacing']['padding'] as $on => $amount ) {
					$amount   = str_replace( array( ':', '|' ), array( '(--wp--', '--' ), $amount ) . ')';
					$styles[] = sprintf( 'padding-%s: %s', $on, $amount );
				}
			}

			$margin = $attributes['style']['spacing']['margin'] ?? '';
			if ( $margin ) {
				foreach ( $attributes['style']['spacing']['margin'] as $on => $amount ) {
					$amount   = str_replace( array( ':', '|' ), array( '(--wp--', '--' ), $amount ) . ')';
					$styles[] = sprintf( 'margin-%s: %s', $on, $amount );
				}
			}
		}

		// Construct the iframe URL with the query arguments.
		$url = add_query_arg( $args, sprintf( $attributes['iframeUrl'] . '%d', $height ) );

		// Construct the HTML for the iframe.
		$iframe = '<iframe width="100%%" height="%d" src="%s" frameborder="0" class="atv_lazy_load_iframe"></iframe><script src="https://www.amateur.tv/js/IntersectionObserverIframe.js"></script>';

		$html = sprintf( '<div class="atv-front-iframe %s" style="%s">' . $iframe . '</div>', implode( ' ', $classes ), implode( '; ', $styles ), $height, $url );

		return $html;
	}
}

new \AmateurTv\IframeBlock();

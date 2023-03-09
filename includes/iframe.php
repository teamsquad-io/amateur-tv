<?php
// Make sure the file is not directly accessible.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you cannot directly access this file.' );
}

/**
	*	Initializes the amateur TV iframe block and registers it with WordPress.
  *  @since 1.0.0
  *  @return void
**/
function amateurtv_init_iframe() {
/**
  *  Registers the amateur TV iframe block with WordPress.
  *  @since 1.0.0
  *  @param string $block_path The file path of the block's JSON configuration file.
  *  @param array $args An array of block registration arguments.
**/
	register_block_type( __DIR__ . '/../config/iframe-block.json', array(
		'render_callback' => 'amateurtv_render_iframe'
	));
}
add_action( 'init', 'amateurtv_init_iframe' );

/**
	* Renders the output for the amateur TV iframe block.
	* @since 1.0.0
	* @param array $attributes An array of block attributes.
  * @return string The HTML output for the amateur TV iframe block.
**/
function amateurtv_render_iframe( $attributes ) {

	if ( is_admin() ) {
		return;
	}

	$genre = $attributes['genre'] ?? array();
	$age = $attributes['age'] ?? array();
	$camType = $attributes['camType'] ?? 'popular';
	$camName = $attributes['camName'] ?? '';

	$args = array(
		'a' => get_option( 'amateurtv_affiliate' )
	);

	if( !empty($genre) ) {
		$args['genre'] = implode( ',', $genre );
	}

	if( !empty($age) ) {
		$args['age'] = implode( ',', $age );
	}

	switch ( $camType ) {
		case 'camname':
			if ( !empty( $camName ) ) {
				$args['livecam'] = $camName;
			}
			break;
		case 'camparam':
			if ( !empty( $_GET['livecam'] ) ) {
				$args['livecam'] = $_GET['livecam'];
			}
			break;
	}

	$url = add_query_arg( $args, 'https://www.amateur.tv/freecam/embed?width=890&height=580&lazyloadvideo=1&a_mute=1' );

	$iframe = sprintf( '<iframe width="890" height="580" src="%s" frameborder="0" class="atv_lazy_load_iframe"></iframe><script src="https://www.amateur.tv/js/IntersectionObserverIframe.js"></script>', $url );

	return $iframe;

}

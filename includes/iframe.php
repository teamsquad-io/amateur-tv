<?php
// Make sure the file is not directly accessible.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you cannot directly access this file.' );
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function amateurtv_init_iframe() {
    register_block_type( __DIR__ . '/../config/iframe-block.json', array(
      'render_callback' => 'amateurtv_render_iframe'
    ));
  }
  add_action( 'init', 'amateurtv_init_iframe' );
  
  function amateurtv_render_iframe($attributes) {
      if ( is_admin()){
          return;
      }
  
      $genre = $attributes['genre'] ?? array();
      $age = $attributes['age'] ?? array();
      $camType = $attributes['camType'] ?? 'popular';
      $camName = $attributes['camName'] ?? '';
  
      $args = array(
          'a' => get_option( 'amateurtv_affiliate' )
      );
  
      if(!empty($genre)){
          $args['genre'] = implode(',', $genre );
      }
      if(!empty($age)){
          $args['age'] = implode(',', $age );
      }

      switch ( $camType ){
        case 'camname':
            if ( ! empty( $camName ) ){
                $args['livecam'] = $camName;
            }
            break;
        case 'camparam':
            if ( ! empty( $_GET['livecam'] ) ) {
                $args['livecam'] = $_GET['livecam'];
            }
            break;
      }
      
      $url = add_query_arg( $args, 'https://www.amateur.tv/freecam/embed?width=890&height=580&lazyloadvideo=1&a_mute=1' );
  
      $iframe = sprintf( '<iframe width="890" height="580" src="%s" frameborder="0" class="atv_lazy_load_iframe"></iframe><script src="https://www.amateur.tv/js/IntersectionObserverIframe.js"></script>', $url );
  
      return $iframe;
  
  }
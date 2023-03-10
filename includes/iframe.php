<?php

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
      $height = $attributes['iframeHeight'] ?? 580;
  
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

        $classes = $styles = array();

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
        }   
      
        $url = add_query_arg( $args, 'https://www.amateur.tv/freecam/embed?width=890&height=%d&lazyloadvideo=1&a_mute=1' );
    
        $iframe = '<iframe width="100%%" height="%d" src="%s" frameborder="0" class="atv_lazy_load_iframe"></iframe><script src="https://www.amateur.tv/js/IntersectionObserverIframe.js"></script>';
    
        $html = sprintf( '<div class="atv-front-iframe %s" style="%s">' . $iframe . '</div>', implode( ' ', $classes ), implode( '; ', $styles ), $height, $url, $height );

        return $html;
  }
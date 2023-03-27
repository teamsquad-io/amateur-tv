<?php

namespace AmateurTv;

class IframeBlock{

    public function __construct(){
        $this->hooks();
    }

    /**
     * Register all the hooks.
     */
    function hooks(){
        add_action( 'init', array( $this, 'register_block' ) );
    }

    /**
     * Registers the block using the metadata loaded from the `block.json` file.
     * Behind the scenes, it registers also all assets so they can be enqueued
     * through the block editor in the corresponding context.
     *
     * @see https://developer.wordpress.org/reference/functions/register_block_type/
     */
    function register_block() {
        register_block_type( AMATEURTV_DIR . '/config/iframe-block.json', array(
            'render_callback' => array( $this, 'render_iframe' ),
        ));
    }
  
    /**
     * Renders the iframe on the front end.
     */
    function render_iframe($attributes) {
        if ( is_admin()){
            return;
        }
   
        $genre = $attributes['genre'] ?? array();
        $age = $attributes['age'] ?? array();
        $camLang = $attributes['camLang'] ?? array();
        $tags = $attributes['tags'] ?? array();
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
        if(!empty($camLang)){
            $args['camLang'] = implode(',', $camLang );
        }
        if(!empty($tags)){
            $args['tags'] = implode(',', $tags );
        }

        switch ( $camType ){
            case 'camname':
                if ( ! empty( $camName ) ){
                    $args['livecam'] = sanitize_title( $camName );
                }
                break;
            case 'camparam':
                if ( ! empty( $_GET['livecam'] ) ) {
                    $args['livecam'] = sanitize_title( $_GET['livecam'] );
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
      
        $url = add_query_arg( $args, sprintf( $attributes['iframeUrl'] . '%d', $height ) );
    
        $iframe = '<iframe width="100%%" height="%d" src="%s" frameborder="0" class="atv_lazy_load_iframe"></iframe><script src="https://www.amateur.tv/js/IntersectionObserverIframe.js"></script>';
    
        $html = sprintf( '<div class="atv-front-iframe %s" style="%s">' . $iframe . '</div>', implode( ' ', $classes ), implode( '; ', $styles ), $height, $url );

        return $html;
    }
}

new \AmateurTv\IframeBlock();
<?php

namespace AmateurTv;

class CamDetailsBlock{

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
        register_block_type( AMATEURTV_DIR . '/config/details-block.json', array(
            'render_callback' => array( $this, 'render_block' ),
        ));
    }
  
    /**
     * Renders the block on the front end.
     */
    function render_block($attributes, $content) {
        if ( is_admin()){
            return;
        }

        switch ( $attributes['camType'] ) {
            case 'camparam':
                $cam = sanitize_title( $_GET['livecam'] ?? false );
                if ( $cam ) {
                    $url = add_query_arg( 'camname', $cam, $attributes['api'] );
                    $details = null;
                    $response = wp_remote_get( $url );
                    if ( ( !is_wp_error($response)) && (200 === wp_remote_retrieve_response_code( $response ) ) ) {
                        $responseBody = json_decode($response['body'], true);
                        $details = $responseBody['body'] ?? null;
                        if ( $details ) {
                           $content = $this->replace_data( $content, $details );
                        }
                    }
                }
                break;
        }

        return $content;
    }

    /**
     * Replace the values in the placeholders with the values from the API.
     */
    function replace_data( $content, $details ) {
        $domDocument = new \DOMDocument();
        $domContent = $domDocument->loadHTML( $content, LIBXML_HTML_NODEFDTD | LIBXML_NOERROR | LIBXML_NOWARNING );

        $xpath = new \DOMXpath( $domDocument, false );

        // text elements
        foreach ( $xpath->evaluate('//p[contains(@class, "camparam")]') as $p ) {
            foreach ( $p->attributes as $name => $node ) {
                $key = null;
                
                // the element has at least one class corresponding to the key in the API
                if ( 'class' === $name ) {
                    $classes = explode( ' ', $node->value );
                    foreach ( $classes as $class ) {
                        if ( strpos( $class, 'atv-' ) !== false ) {
                            $key = str_replace( 'atv-', '', $class );
                            break;
                        }
                    }
                    if ( $key ) {
                        break;
                    }
                }
            }

            // only if the key in the API is found.
            if ( $key ) {
                $p->nodeValue = esc_html( $details[ $key ] );
            }
        }

        // image elements
        foreach ( $xpath->evaluate('//figure[contains(@class, "camparam")]') as $img ) {
            foreach ( $img->attributes as $name => $node ) {
                $key = null;
                
                // the element has at least one class corresponding to the key in the API
                if ( 'class' === $name ) {
                    $classes = explode( ' ', $node->value );
                    foreach ( $classes as $class ) {
                        if ( strpos( $class, 'atv-' ) !== false ) {
                            $key = str_replace( 'atv-', '', $class );
                            break;
                        }
                    }
                    if ( $key ) {
                        break;
                    }
                }
            }

            // only if the key in the API is found.
            if ( $key ) {
                foreach( $img->childNodes as $child ) {
                    if ( 'img' === $child->nodeName ){
                        foreach ( $child->attributes as $name => $node ) {
                            if ( 'src' === $name ) {
                                $node->nodeValue = esc_attr( $details[ $key ] );
                            }
                        }
                    }
                }
            }
        }

        return $domDocument->saveHTML();

    }
}

new \AmateurTv\CamDetailsBlock();
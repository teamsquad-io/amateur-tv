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
        return $content;
    }
}

new \AmateurTv\CamDetailsBlock();
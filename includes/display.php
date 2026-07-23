<?php

namespace Fachbetrieb;

add_action( 'init', \Fachbetrieb\register_block(...) );

function register_block( ): void {
  register_block_type( FACHBETRIEB_PLUGDIR . '/build' );
}


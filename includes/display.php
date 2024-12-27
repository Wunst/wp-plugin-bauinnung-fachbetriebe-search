<?php
add_action( "init", "fachb_register_block" );

function fachb_register_block() {
  register_block_type( fachb_PLUGDIR . "/build" );
}


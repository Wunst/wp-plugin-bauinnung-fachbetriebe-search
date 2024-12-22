<?php
add_action( "init", "fachb_register_block" );

function fachb_register_block() {
  wp_register_script(
    "bauinnung-kiel-fachb-block-editor",
    fachb_PLUGURL . "/display/index.js",
    array(
      "wp-block-editor",
      "wp-blocks",
      "wp-element"
    ),
    filemtime( fachb_PLUGDIR . "display/index.js" )
  );
  register_block_type(
    fachb_PLUGDIR . "/display",
    array(
      "editor_script" => "bauinnung-kiel-fachb-block-editor"
    )
  );
}


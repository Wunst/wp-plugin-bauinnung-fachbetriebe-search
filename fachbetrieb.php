<?php
/*
 * Plugin Name: Fachbetrieb finden
 * Plugin URI: https://github.com/Wunst/wp-plugin-bauinnung-kiel-fachbetriebe-search
 * Author: Ben Matthies
 * Author URI: https://github.com/Wunst
 * Version: 1.2.0
 * Update URI: false
 * GitHub Plugin URI: Wunst/wp-plugin-bauinnung-kiel-fachbetriebe-search
 * Primary Branch: main
 * Release Asset: true
 * Requires at least: 6.2
 * Requires PHP: 8.2
 */

define( "fachb_PLUGDIR", plugin_dir_path(__FILE__) );
define( "fachb_PLUGURL", plugin_dir_url(__FILE__) );

require_once __DIR__ . '/vendor/autoload.php';

require_once( fachb_PLUGDIR . "includes/db.php" );
require_once( fachb_PLUGDIR . "includes/admin.php" );
require_once( fachb_PLUGDIR . "includes/display.php" );
require_once( fachb_PLUGDIR . "includes/rest.php" );

register_activation_hook( __FILE__, "fachb_install" );


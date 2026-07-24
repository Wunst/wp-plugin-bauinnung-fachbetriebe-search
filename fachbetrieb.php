<?php
/*
 * Plugin Name: Fachbetrieb finden
 * Plugin URI: https://github.com/Wunst/wp-plugin-bauinnung-fachbetriebe-search
 * Author: Ben Matthies
 * Author URI: https://github.com/Wunst
 * Version: 2.0.0
 * Update URI: false
 * GitHub Plugin URI: Wunst/wp-plugin-bauinnung-fachbetriebe-search
 * Primary Branch: main
 * Release Asset: true
 * Requires at least: 6.2
 * Requires PHP: 8.2
 */

define( "FACHBETRIEB_PLUGDIR", plugin_dir_path(__FILE__) );
define( "FACHBETRIEB_PLUGURL", plugin_dir_url(__FILE__) );

require_once __DIR__ . '/vendor/autoload.php';

require_once( FACHBETRIEB_PLUGDIR . "includes/db.php" );
require_once( FACHBETRIEB_PLUGDIR . "includes/admin.php" );
require_once( FACHBETRIEB_PLUGDIR . "includes/display.php" );
require_once( FACHBETRIEB_PLUGDIR . "includes/rest.php" );

register_activation_hook( __FILE__, \Fachbetrieb\Db\install(...) );


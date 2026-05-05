<?php 
/*
 * Plugin Name: Fachbetrieb finden
 * Plugin URI: https://github.com/Wunst/wp-plugin-bauinnung-fachbetriebe-search
 * Author: Ben Matthies
 * Author URI: https://github.com/Wunst
 * Version: 1.5.1
 * Update URI: false
 * GitHub Plugin URI: Wunst/wp-plugin-bauinnung-fachbetriebe-search
 * Primary Branch: main
 * Release Asset: true
 * Requires at least: 6.2
 * Requires PHP: 8.2
 */

if ( !defined( 'ABSPATH' ) ) {
  exit();
}

require_once __DIR__ . '/vendor/autoload.php';

register_activation_hook ( __FILE__, \Fachbetrieb\Db::instance()->install(...) );
add_action( "admin_menu", \Fachbetrieb\AdminMenu::register(...) );

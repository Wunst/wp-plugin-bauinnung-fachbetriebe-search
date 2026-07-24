<?php


namespace Fachbetrieb\RestApi;

require_once( FACHBETRIEB_PLUGDIR . "includes/db.php" );
require_once( FACHBETRIEB_PLUGDIR . "includes/geo.php" );


use \Fachbetrieb\Db;
use \Fachbetrieb\Geo;


add_action( "rest_api_init", \Fachbetrieb\RestApi\init(...) );

function init( ): void {
  register_rest_route( 
    'fachbetrieb/v2', 
    '/categories', 
    array(
      'methods' => 'GET',
      'callback' => \Fachbetrieb\RestApi\categories(...),
    )
  );

  register_rest_route( 
    'fachbetrieb/v2', 
    '/coordinates', 
    array(
      'methods' => 'GET',
      'callback' => \Fachbetrieb\RestApi\coordinates(...),
    )
  );
}


/**
 * Get list of categories.
 * @return string[]  List of category names.
 */
function categories( \WP_REST_Request $request ): array {
  return array_map(
    function ( $entry ): string {
      return $entry['name'];
    },
    Db\list_category( )
  );
}


/**
 * Resolve address to coordinates.
 * @param \WP_REST_Request $request  Request URL
 * should be of the form `/coordinates?address=...`
 * @return array[
 *   'lat' => float,
 *   'lon' => float,
 * ]  Coordinates, or empty array if not resolved.
 */
function coordinates( \WP_REST_Request $request ): array {
  /// XXX: This may require rate limiting.
  // We'll see. If the server gets ddosed one 
  // day this is it.
  $result = Geo\resolve( $request['address'] );
  if ( $result )
    return $result;
  else
    return array();
}


<?php

require_once( fachb_PLUGDIR . "includes/db.php" );
require_once( fachb_PLUGDIR . "includes/geo.php" );

add_action( "rest_api_init", "fachb_rest_api_init" );

function fachb_rest_api_init() {
  register_rest_route( "fachbetrieb/v1", "/search", array(
    "methods" => "GET",
    "callback" => "fachb_rest_search"
  ) );
}

/*
 * GET /fachbetrieb/v1/search?c=[kategorien]&a=[adresse]&d=[maxDistanz]
 * Liefert die Fachbetriebe nach Entfernung von der Adresse geordnet.
 *
 * c= Komma-getrennte Liste von Kategorien
 * Betrieb muss ALLE Kategorien haben, um angezeigt zu werden.
 *
 * Response:
 *  {
 *    "sorted": "boolean",
 *    "results": [
 *      {
 *        "name": "string",
 *        "adresse": "string",
 *        "url": "string?",
 *        "distance": "number?"
 *      }
 *    ]
 *  }
 *
 * Anmerkung: sorted ist false, wenn die Adresse des Kunden ("a=") von
 * Nominatim nicht aufgelöst werden konnte.
 */
function fachb_rest_search( WP_REST_Request $request ) {
  $cat = $request[ "c" ];
  $betriebe = $cat ?
    fachb_list_with_categories(
      array_map( "intval", explode( ",", $request[ "c" ] ) ) 
    ) :
    fachb_list();

  $address = $request[ "a" ];
  if ( !$address || !fachb_check_address( $address ) ) {
    // Keine Kundenadresse oder Kundenadresse ungültig.
    return array(
      "sorted" => false,
      "results" => $betriebe // Betriebe in Reihenfolge des Hinzufügens.
    );
  }

  // Betrieben Entfernung zuordnen.
  $betriebe = array_map( function ( $b ) use ( $address ) {
    return array_merge( (array)$b, array(
      "distance" => fachb_distance( $address, $b->adresse )
    ) );
  }, $betriebe );

  // Betriebe nach Entfernung filtern.
  $d = intval( $request[ "d" ] );
  if ( $d ) {
    $betriebe = array_filter( $betriebe, function ( $b ) use ( $d ) {
      return intval( $b["distance"] ) <= $d;
    } );
  }

  // Betriebe nach Entfernung sortieren.
  usort( $betriebe, function ( $a, $b ) {
    // FIXME: was wenn Betrieb ungültige Adresse hat? (ganz hinten einsortieren?)
    return $a["distance"] <=> $b["distance"];
  } );

  return array(
    "sorted" => true,
    "results" => $betriebe
  );
}


<?php

namespace Fachbetrieb\Geo;

use Geocoder\Query\GeocodeQuery;

$provider = new \Geocoder\Provider\Cache\ProviderCache(
  \Geocoder\Provider\Nominatim\Nominatim::withOpenStreetMapServer(
    new \Symfony\Component\HttpClient\Psr18Client(),
    "Fachbetriebesuche der Bauinnung Kiel"
  ),
  new \Symfony\Component\Cache\Psr16Cache(
    new \Symfony\Component\Cache\Adapter\FilesystemAdapter()
  )
);

/**
 * Resolve address to coordinates.
 * @param string  $address  Address of the form 
 * "street, postcode, city" or similar
 * @return null|array[
 *   'lat' => float,
 *   'lon' => float
 * ]  Coordinates, or null if address not resolvable.
 */
function resolve( string $address ): ?array {
  global $provider;

  $result = $provider->geocodeQuery(
    GeocodeQuery::create( $address )->withLimit( 1 ) 
  );
  if ( $result->isEmpty( ) ) {
    return null;
  }

  $coordinates = $result->first( )->getCoordinates( );
  return array(
    'lat' => $coordinates->getLatitude( ),
    'lon' => $coordinates->getLongitude( ),
  );
}


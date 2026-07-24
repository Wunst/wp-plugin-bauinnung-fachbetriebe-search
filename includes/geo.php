<?php

namespace Fachbetrieb\Geo;

use Geocoder\Query\GeocodeQuery;


// Use a rate-limited http client to avoid hitting API limits.
$stack = \GuzzleHttp\HandlerStack::create();
$stack->push(\Spatie\GuzzleRateLimiterMiddleware\RateLimiterMiddleware::perSecond(2));

$httpClient = new \GuzzleHttp\Client(['handler' => $stack, 'timeout' => 30.0]);

$provider = new \Geocoder\Provider\Cache\ProviderCache(
  \Geocoder\Provider\Nominatim\Nominatim::withOpenStreetMapServer(
    $httpClient,
    "Fachbetriebesuche Bauinnung Kiel"
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


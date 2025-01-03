<?php

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

/*
 * Ermittelt Distanz zwischen Koordinaten.
 */
function fachb_haversine( $a, $b ) {
    $earthRadius = 6371; // Earth's radius in kilometers

    $latFrom = deg2rad( $a->getLatitude() );
    $lonFrom = deg2rad( $a->getLongitude() );
    $latTo = deg2rad( $b->getLatitude() );
    $lonTo = deg2rad( $b->getLongitude() );

    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
    return $angle * $earthRadius;
}

/*
 * Ermittelt die Distanz in Kilometern zwischen addressA und addressB.
 */
function fachb_distance( $addressA, $addressB ) {
  global $provider;

  $a = $provider->geocodeQuery(
    GeocodeQuery::create( $addressA )->withLimit( 1 ) 
  );

  $b = $provider->geocodeQuery(
    GeocodeQuery::create( $addressB )->withLimit( 1 ) 
  );

  if ( $a->isEmpty() || $b->isEmpty() ) {
    return null;
  }

  return fachb_haversine(
    $a->first()->getCoordinates(), 
    $b->first()->getCoordinates() 
  );
}

function fachb_check_address( $address ) {
  global $provider;

  return !$provider->geocodeQuery(
    GeocodeQuery::create( $address )->withLimit( 1 )
  )->isEmpty();
}


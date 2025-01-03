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

$geocoder = new \Geocoder\StatefulGeocoder( $provider, "de" );

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
  $a = $geocoder->geocode(
    GeocodeQuery::create( $addressA ) 
  )->first();

  $b = $geocoder->geocode(
    GeocodeQuery::create( $addressB ) 
  )->first();

  if ( !$a || !$b ) {
    return null;
  }

  return fachb_haversine( $a->getCoordinates(), $b->getCoordinates() );
}

function fachb_check_address( $address ) {
  return null != $geocoder->geocode(
    GeocodeQuery::create( $address )
  )->first();
}


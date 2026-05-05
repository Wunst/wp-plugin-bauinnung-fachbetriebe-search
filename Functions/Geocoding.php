<?php

namespace Fachbetrieb;

use \Geocoder\Common\Model\Coordinates;
use \Geocoder\Common\Provider\Provider;

use \Geocoder\Provider\Cache\ProviderCache;
use \Geocoder\Provider\Nominatim\Nominatim;

use \Geocoder\Query\GeocodeQuery;

/** Geocoding class. */
class Geocoding {
  private static Provider $provider = new ProviderCache(
    Nominatim::withOpenStreetMapServer(
      new \Symfony\Component\HttpClient\Psr18Client(),
      "Fachbetriebesuche der Bauinnung Kiel"
    ),
    new \Symfony\Component\Cache\Psr16Cache(
      new \Symfony\Component\Cache\Adapter\FilesystemAdapter()
    )
  );

  /** Gets distance between coordinates. */
  private static function haversine( Coordinates $a, Coordinates $b ): double {
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

  /** Gets distance between addresses. */
  public static function get_distance( string $address_a, string $address_b ): double {
    $a = self::$provider->geocodeQuery( GeocodeQuery::create( $address_a )->withLimit( 1 ) );
    $b = self::$provider->geocodeQuery( GeocodeQuery::create( $address_b )->withLimit( 1 ) );
    if ( $a->isEmpty() || $b->isEmpty() ) {
      return null;
    }

    return fachb_haversine(
      $a->first()->getCoordinates(), 
      $b->first()->getCoordinates() 
    );
  }

  /** Checks if address is valid, */
  public static function address_valid( string $address ): bool {
    return !self::$provider->geocodeQuery( GeocodeQuery::create( $address )->withLimit( 1 ) )->isEmpty();
  }


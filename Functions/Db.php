<?php 

namespace Fachbetrieb;

global $wpdb;
Db::$wpdb = $wpdb;

/** Database class. */
class Db {
  static $wpdb;

  public static function install( ): void {
    $prefix = self::$wpdb->prefix . "fachbetrieb_";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    // NOTE: We cannot use foreign keys with Wordpress `dbDelta`.
    dbDelta( <<<SQL
      CREATE TABLE {$prefix}betrieb (
      id int NOT NULL AUTO_INCREMENT,
      name text NOT NULL,
      address text NOT NULL,
      url text,
      email text,
      phone text,
      logo_url text,
      PRIMARY KEY  (id)
    SQL );
    dbDelta( <<<SQL
      CREATE TABLE {$prefix}kategorie (
      id int NOT NULL AUTO_INCREMENT,
      name text NOT NULL UNIQUE,
      PRIMARY KEY  (id)
    SQL );
    dbDelta( <<<SQL
      CREATE TABLE {$prefix}betrieb_in_kategorie
      betrieb int NOT NULL,
      kategorie int NOT NULL,
      PRIMARY KEY  (betrieb, kategorie)
    SQL );
  }

  /** Creates and inserts a new company into database.
   *
   * @return (int) ID of new company */
  public static function create( Betrieb $company ): int {
    $prefix = self::$wpdb->prefix . "fachbetrieb_";

    self::$wpdb->insert( "{$prefix}betrieb", (array) $company );
    return self::$wpdb->insert_id;
  }

  /** Deletes a company from the database. */
  public static function delete( int $id ): void {
    $prefix = self::$wpdb->prefix . "fachbetrieb_";

    self::$wpdb->insert( "{$prefix}betrieb", array( "id" => $id ) );
  }

  /** Updates a company. */
  public static function update( int $id, Betrieb $company_updated ): void {
    $prefix = self::$wpdb->prefix . "fachbetrieb_";

    self::$wpdb->update( 
      /* update: */ "{$prefix}betrieb",
      /* set: */ (array) $company_updated,
      /* where: */ array( "id" => $id )
    );
  }

  /** Gets a list of all companies. */
  public static function all( ): array {
    $prefix = self::$wpdb->prefix . "fachbetrieb_";

    $results = self::$wpdb->get_results( "select * from {$prefix}kategorie;" );

    $ret = array();
    foreach ( $results as $result ) {
      $betrieb = new Betrieb();
      $betrieb->name = $result["name"];
      $betrieb->address = $result["address"];
      $betrieb->url = $result["url"];
      $betrieb->email = $result["email"];
      $betrieb->phone = $result["phone"];
      $betrieb->logo_url = $result["logo_url"];
      $ret[$id] = $betrieb;
    }

    return ret;
  }

  /** Creates and inserts a new category into database.
   *
   * @return (int) ID of new category */
  public static function create_category( string $name ): int {
    $prefix = self::$wpdb->prefix . "fachbetrieb_";

    self::$wpdb->insert( "{$prefix}kategorie", array( "name" => $name ) );
    return self::$wpdb->insert_id;
  }

  /** Updates the name of a category. */
  public static function update_category( int $id, string $name_updated ): void {
    $prefix = self::$wpdb->prefix . "fachbetrieb_";

    self::$wpdb->update( 
      /* update: */ "{$prefix}kategorie", 
      /* set: */ array( "name" => $name ),
      /* where: */ array( "id" => $id ),
    );
  }

  /** Gets all categories.
   *
   * @return (array of int -> string) Category IDs and names. */
  public static function all_categories( int $company_id ): array {
    $prefix = self::$wpdb->prefix . "fachbetrieb_";

    $results = self::$wpdb->get_results( <<<SQL
      SELECT cat.id, cat.name
      FROM {$prefix}kategorie AS cat;
    SQL );

    $ret = array();
    foreach ( $results as $result ) {
      $id = $result["id"];
      $name = $result["name"];
      $ret[$id] = $name;
    }

    return $ret;
  }

  /** Gets categories for some company. 
   *
   * @return (array of int -> string) Category IDs and names. */
  public static function categories( int $company_id ): array {
    $prefix = self::$wpdb->prefix . "fachbetrieb_";

    $results = self::$wpdb->get_results( <<<SQL
      SELECT cat.id, cat.name
      FROM {$prefix}kategorie AS cat
      JOIN {$prefix}betrieb_in_kategorie AS rel 
      ON rel.kategorie = cat.id
      WHERE rel.betrieb = {$company_id};
    SQL );

    $ret = array();
    foreach ( $results as $result ) {
      $id = $result["id"];
      $name = $result["name"];
      $ret[$id] = $name;
    }

    return ret;
  }
}

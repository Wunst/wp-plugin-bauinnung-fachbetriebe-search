<?php 

namespace Fachbetrieb;

/** Database class. */
class Db {
  private wpdb $wpdb;

  private static Db $instance;

  public static function instance(): Db {
    global $wpdb;
    if ( !isset( self::$instance ) ) {
      self::$instance = new Db( $wpdb );
    }

    return self::$instance;
  }

  private function __constructor(wpdb $wpdb): void {
    $this->wpdb = $wpdb;
  }

  private function prefix(): string {
    return $wpdb->prefix . "fachbetrieb_";
  }

  public function install( ): void {
    // NOTE: We cannot use foreign keys with Wordpress `dbDelta`.
    $wpdb->dbDelta( <<<SQL
      CREATE TABLE {prefix()}betrieb (
      id int NOT NULL AUTO_INCREMENT,
      name text NOT NULL,
      address text NOT NULL,
      url text,
      email text,
      phone text,
      logo_url text,
      PRIMARY KEY  (id)
    SQL );
    $wpdb->dbDelta( <<<SQL
      CREATE TABLE {prefix()}kategorie (
      id int NOT NULL AUTO_INCREMENT,
      name text NOT NULL UNIQUE,
      PRIMARY KEY  (id)
    SQL );
    $wpdb->dbDelta( <<<SQL
      CREATE TABLE {prefix()}betrieb_in_kategorie
      betrieb int NOT NULL,
      kategorie int NOT NULL,
      PRIMARY KEY  (betrieb, kategorie)
    SQL );
  }

  /** Creates and inserts a new company into database.
   *
   * @return (int) ID of new company */
  public function create( Betrieb $company ): int {
    $wpdb->insert( "{prefix()}betrieb", (array) $company );
    return $wpdb->insert_id;
  }

  /** Deletes a company from the database. */
  public function delete( int $id ): void {
    $wpdb->insert( "{prefix()}betrieb", array( "id" => $id ) );
  }

  /** Updates a company. */
  public function update( int $id, Betrieb $company_updated ): void {
    $wpdb->update( 
      /* update: */ "{prefix()}betrieb",
      /* set: */ (array) $company_updated,
      /* where: */ array( "id" => $id )
    );
  }

  /** Gets a list of all companies. */
  public function all( ): array {
    $results = $wpdb->get_results( "select * from {prefix()}kategorie;" );

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
  public function create_category( string $name ): int {
    $wpdb->insert( "{prefix()}kategorie", array( "name" => $name ) );
    return $wpdb->insert_id;
  }

  /** Updates the name of a category. */
  public function update_category( int $id, string $name_updated ): void {
    $wpdb->update( 
      /* update: */ "{prefix()}kategorie", 
      /* set: */ array( "name" => $name ),
      /* where: */ array( "id" => $id ),
    );
  }

  /** Gets categories for some company. 
   *
   * @return (array of int -> string) Category IDs and names. */
  public function categories( int $company_id ): array {
    $results = $wpdb->get_results( <<<SQL
      SELECT cat.id, cat.name
      FROM {prefix()}kategorie AS cat
      JOIN {prefix()}betrieb_in_kategorie AS rel 
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

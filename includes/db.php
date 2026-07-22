<?php

namespace Fachbetrieb\Db;


/**
 * Represents company main data (Stammdaten) 
 * without list of categories.
 */
class Betrieb {
  public string $name;
  public string $address;

  public ?string $url;
  public ?string $email;
  public ?string $phone;
  public ?string $logo_url;


  /**
   * Constructs company object from data array 
   * (e.g. database, form data).
   */
  public function __construct( array $raw_data ) {
    $this->name = $raw_data[ 'name' ];
    $this->address = $raw_data[ 'address' ];

    $this->url = $raw_data[ 'url' ];
    $this->email = $raw_data[ 'email' ];
    $this->phone = $raw_data[ 'phone' ];
    $this->logo_url = $raw_data[ 'logo_url' ];
  }
}


/**
 * Shorthand to not declare globals everywhere.
 */
function wpdb( ): \wpdb {
  global $wpdb;
  return $wpdb;
}

function prefix( ): string {
  return wpdb( )->prefix . 'fachbetrieb_';
}


function install( ) : void {
  $prefix = prefix( );

  require_once ( ABSPATH . 'wp-admin/includes/upgrade.php' );

  // TODO: We cannot use foreign keys `dbDelta'.
  // May want to add them for performance using 
  // plain `query'.
  dbDelta(
    <<<SQL
      CREATE TABLE {$prefix}betrieb (
        id int NOT NULL auto_increment, 
        name text NOT NULL,
        address text NOT NULL,
        url text,
        email text,
        phone text,
        logo_url text,
        PRIMARY KEY  (id)
      );
    SQL 
  );
  dbDelta(
    <<<SQL
      CREATE TABLE {$prefix}kategorie (
        id int NOT NULL auto_increment,
        name text NOT NULL unique,
        PRIMARY KEY  (id)
      );
    SQL
  );
  dbDelta(
    <<<SQL
      CREATE TABLE {$prefix}betrieb_in_kategorie (
        betrieb int NOT NULL,
        kategorie int NOT NULL,
        PRIMARY KEY  (betrieb, kategorie)
      );
    SQL
  );
}


/**
 * Returns a list of all companies.
 * @return (array[
 *   "id" => int,
 *   "betrieb" => Betrieb,
 * ])[]  Map of ids to companies.
 */
function list_betrieb( ): array {
  $results = wpdb( )->get_results( 'select * from ' . prefix( ) . 'betrieb;' );

  return array_map(
    function ( $row ) {
      return array(
        'id' => $row->id,
        'betrieb' => new Betrieb( (array)$row ),
      );
    },
    $results
  );
}

/**
 * Returns a list of all categories.
 * @return (array[
 *   "id" => int,
 *   "name" => string,
 * ])[]  Map of ids to category names.
 */
function list_category( ): array {
  $results = wpdb( )->get_results( 'select * from ' . prefix( ) . 'kategorie;' );

  return array_map(
    function ( $row ) {
      return array(
        'id' => $row->id,
        'name' => $row->name,
      );
    },
    $results
  );
}


/**
 * Gets a company by id.
 */
function get_betrieb( int $id ): Betrieb {
  $raw_data = wpdb( )->get_row(
    wpdb( )->prepare(
      'select * from ' . prefix( ) . 'betrieb where id = %d',
      $id
    )
  );

  return new Betrieb( (array)$raw_data );
}


/**
 * Creates and inserts a new company into the 
 * database.
 * @return int  Id of the new company.
 */
function create_betrieb( Betrieb $betrieb ): int {
  wpdb( )->insert( prefix( ) . 'betrieb', (array)$betrieb );
  return wpdb( )->insert_id;
}


/**
 * Deletes a company by id.
 */
function delete_betrieb( int $id ): void {
  wpdb( )->delete( prefix( ) . 'betrieb', array( "id" => $id ) );
}


/**
 * Updates a company by id.
 * @param Betrieb  betrieb_updated
 * This will completely override company data. 
 * Existing data needs to be included.
 */
function update_betrieb( int $id,  Betrieb $betrieb_updated ): void {
  wpdb( )->update(
    /* update: */ prefix( ) . 'betrieb',
    /* set: */ (array)$betrieb_updated,
    /* where: */ array( "id" => $id )
  );
}


/**
 * Creates and inserts a new category into the 
 * database.
 * @return int  Id of the new category.
 */
function create_category( string $name ): int {
  wpdb( )->insert( prefix( ) . 'kategorie', array( "name" => $name ) );
  return wpdb( )->insert_id;
}


/**
 * Renames a category by id.
 */
function rename_category( int $id, string $name_updated ): void {
  wpdb( )->update(
    /* update: */ prefix( ) . 'kategorie',
    /* set: */ array( "name" => $name ),
    /* where: */ array( "id" => $id ),
  );
}


/**
 * Deletes a category by id.
 */
function delete_category( int $id ): void {
  wpdb( )->delete( prefix( ) . 'kategorie', array( "id" => $id ) );
}


/**
 * Updates category assignment on a company.
 * @param int   betrieb     Company id.
 * @param int[] categories  List of category ids.
 */
function assign_categories( int $betrieb, array $categories ): void {
  // Remove existing assignments.
  // XXX: The whole interface is most inelegant.
  // But it works for the admin panel.
  wpdb( )->delete( 
    prefix( ) . 'betrieb_in_kategorie', 
    array( 'betrieb' => $betrieb ),
  );

  array_map( 
    function ( int $category ) use ( $betrieb ) {
      wpdb( )->insert(
        prefix( ) . 'betrieb_in_kategorie',
        array(
          'betrieb' => $betrieb,
          'kategorie' => $category,
        )
      );
    },
    $categories
  );
}


/**
 * Get categories for company.
 * @param int  $betrieb  Company id.
 * @return (array[
 *   "id" => int,
 *   "name" => string,
 * ])[]  Map of ids to category names.
 */
function get_categories( int $betrieb ): array {
  $prefix = prefix( );
  $results = wpdb( )->get_results(
    wpdb( )->prepare(
      <<<SQL
        SELECT k.id as id, k.name as name
        FROM {$prefix}kategorie as k
        JOIN {$prefix}betrieb_in_kategorie as bik
        ON k.id = bik.kategorie
        WHERE bik.betrieb = %d
      SQL,
      $betrieb
    )
  );

  return array_map(
    function ( $row ) {
      return array(
        'id' => $row->id,
        'name' => $row->name,
      );
    },
    $results
  );
}


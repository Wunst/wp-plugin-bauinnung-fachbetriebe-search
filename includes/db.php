<?php

$prefix = $wpdb->prefix . "fachb_";

function fachb_install() {
  global $wpdb, $prefix;

  // We do not and cannot use the dbDelta mechanism as it does not support
  // FOREIGN KEY constraints.
  
  $wpdb->query("CREATE TABLE IF NOT EXISTS {$prefix}betrieb (
    id int NOT NULL AUTO_INCREMENT,
    name text NOT NULL,
    adresse text NOT NULL,
    url text DEFAULT '' NOT NULL,
    PRIMARY KEY  (id)
  );");

  $wpdb->query("CREATE TABLE IF NOT EXISTS {$prefix}kategorie (
    id int NOT NULL AUTO_INCREMENT,
    name text NOT NULL UNIQUE,
    PRIMARY KEY  (id)
  );");

  $wpdb->query("CREATE TABLE IF NOT EXISTS {$prefix}betrieb_in_kategorie (
    betrieb int NOT NULL,
    kategorie int NOT NULL,
    PRIMARY KEY  (betrieb, kategorie),
    FOREIGN KEY (betrieb) REFERENCES $prefix$betrieb(id),
    FOREIGN KEY (kategorie) REFERENCES $prefix$kategorie(id)
  );");

  // Migrations.
  // Make url nullable.
  $wpdb->query( "ALTER TABLE {$prefix}betrieb MODIFY url text" );
}

// TODO: Deinstallation.

// TODO: How to handle migrations.

function fachb_list() {
  global $wpdb, $prefix;
  return $wpdb->get_results( "SELECT * FROM {$prefix}betrieb;" );
}

function fachb_get( $id ) {
  global $wpdb, $prefix;
  return $wpdb->get_row( $wpdb->prepare( "SELECT * 
    FROM {$prefix}betrieb
    WHERE id = %d;", 
    $id )
  );
}

/*
 * Erstellt einen Betrieb und gibt die ID zurück.
 */
function fachb_create( $name, $adresse, $url ) {
  global $wpdb, $prefix;
  $wpdb->insert( "{$prefix}betrieb", array(
    "name" => $name,
    "adresse" => $adresse,
    "url" => $url
  ) );

  return $wpdb->insert_id;
}

function fachb_delete( $id ) {
  global $wpdb, $prefix;
  $wpdb->delete( "{$prefix}betrieb", array( "id" => $id ) );
}

function fachb_update( $id, $name, $adresse, $url ) {
  global $wpdb, $prefix;
  $wpdb->update( "{$prefix}betrieb", array(
    "name" => $name,
    "adresse" => $adresse,
    "url" => $url
  ), array( "id" => $id ) );
}

/*
 * Erstellt eine Kategorie und gibt die ID zurück.
 */
function fachb_category_create( $name )
{
  global $wpdb, $prefix;
  $wpdb->insert( "{$prefix}kategorie", array(
    "name" => $name
  ) );

  return $wpdb->insert_id;
}

function fachb_category_list()
{
  global $wpdb, $prefix;
  return $wpdb->get_results( "SELECT * FROM {$prefix}kategorie;" );
}

function fachb_category_delete( $id ) {
  global $wpdb, $prefix;
  $wpdb->delete( "{$prefix}kategorie", array( "id" => $id ) );
}

function fachb_category_update( $id, $name ) {
  global $wpdb, $prefix;
  $wpdb->update( "{$prefix}kategorie", array(
    "name" => $name
  ), array( "id" => $id ) );
}


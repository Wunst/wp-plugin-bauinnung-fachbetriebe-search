<?php

function fachb_install() {
  global $wpdb;

  $prefix = $wpdb->prefix . "fachb_";

  $betrieb = "betrieb";
  $kategorie = "kategorie";
  $betrieb_in_kategorie = "betrieb_in_kategorie";

  // We do not and cannot use the dbDelta mechanism as it does not support
  // FOREIGN KEY constraints.
  
  $wpdb->query("CREATE TABLE IF NOT EXISTS $prefix$betrieb (
    id int NOT NULL AUTO_INCREMENT,
    name text NOT NULL,
    adresse text NOT NULL,
    url text DEFAULT '' NOT NULL,
    PRIMARY KEY  (id)
  );");

  $wpdb->query("CREATE TABLE IF NOT EXISTS $prefix$kategorie (
    id int NOT NULL AUTO_INCREMENT,
    name text NOT NULL UNIQUE,
    PRIMARY KEY  (id)
  );");

  $wpdb->query("CREATE TABLE IF NOT EXISTS $prefix$betrieb_in_kategorie (
    betrieb int NOT NULL,
    kategorie int NOT NULL,
    PRIMARY KEY  (betrieb, kategorie),
    FOREIGN KEY (betrieb) REFERENCES $prefix$betrieb(id),
    FOREIGN KEY (kategorie) REFERENCES $prefix$kategorie(id)
  );");

  // Migrations.
  // Make table nullable.
  $wpdb->query( "ALTER TABLE $prefix$betrieb MODIFY url text" );
}

// TODO: Deinstallation.

// TODO: How to handle migrations.

function fachb_list() {
  global $wpdb;

  $prefix = $wpdb->prefix . "fachb_";
  $betrieb = "betrieb";

  return $wpdb->get_results( "SELECT * FROM $prefix$betrieb;" );
}

function fachb_get( $id ) {
  global $wpdb;

  $prefix = $wpdb->prefix . "fachb_";
  $betrieb = "betrieb";

  return $wpdb->get_row( $wpdb->prepare( "SELECT * 
    FROM $prefix$betrieb
    WHERE id = %d;", 
    $id )
  );
}

/*
 * Erstellt einen Betrieb und gibt die ID zurück.
 */
function fachb_create( $name, $adresse, $url ) {
  global $wpdb;

  $prefix = $wpdb->prefix . "fachb_";
  $betrieb = "betrieb";

  $wpdb->insert( "$prefix$betrieb", array(
    "name" => $name,
    "adresse" => $adresse,
    "url" => $url
  ) );

  return $wpdb->insert_id;
}


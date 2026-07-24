<?php

require_once( FACHBETRIEB_PLUGDIR . 'includes/db.php' );

use \Fachbetrieb\Db;
use \Fachbetrieb\Geo;

// Get list of companies to display.
// This has the type
//   array[
//     'id' => int,
//     'betrieb' => Betrieb,
//     'categories' => string[]
//   ][]
$betriebe = array_map(
  function( $entry ) {
    return array(
      'id' => $entry['id'],
      'betrieb' => $entry['betrieb'],
      'categories' => array_map(
        function( $category ): string {
          return $category['name'];
        },
        Db\get_categories( $entry['id'] )
      ),
    );
  },
  Db\list_betrieb()
);

?>
<div id="fachbetrieb">
<div id="fachbetrieb-searchform">
  <noscript>Bitte aktivieren Sie JavaScript, um alle Funktionen der 
Fachbetriebesuche nutzen zu können!</noscript>
</div>
<ul class="list fachbetrieb-results">
<?

array_map(
  function( $entry ) {
    $id = $entry['id'];
    $betrieb = $entry['betrieb'];
    $categories = $entry['categories'];

    echo '<li>';
    if ( $betrieb->url )
      // Link name to company site. 
      echo '<a href="' . $betrieb->url . '"> <h4 class="name">' .
        $betrieb->name .
        '</h4></a>';
    else
      echo '<h4 class="name">' . $betrieb->name . '</h4>';

    echo '<address class="address">' . $betrieb->address . '</address>';

    $coordinates = Geo\resolve( $betrieb->address );
    if ( $coordinates ) {
      echo '<p class="latitude" style="display: none;">' . $coordinates['lat'] . '</p>';
      echo '<p class="longitude" style="display: none;">' . $coordinates['lon'] . '</p>';
      echo '<p class="distance"></p>'; // This is filled in by the frontend.
    }

    if ( $betrieb->phone )
      echo '<a class="phone" href="tel:' . $betrieb->phone . '">' . 
        $betrieb->phone . 
        '</a>';

    if ( $betrieb->email )
      echo '<a class="email" href="mailto:' . $betrieb->email . '">' . 
        $betrieb->email . 
        '</a>';

    echo '<ul class="categories">';
    array_map(
      function ( $category ) {
        echo '<li>' . $category . '</li>';
      },
      $categories
    );
    echo '</ul>';
    echo '</li>';
  },
  $betriebe
);

?>
</ul>
</div>
<?


<?php
/*
 * This file is strictly only for the edge case of static, server side
 * rendering, e.g. for search engine optimization.
 * In the (JS-capable) browser, we use React. See file `view.js`.
 *
 * TODO: Maybe don't serve the entire database on page load, and then `fetch`
 * it again from JS? We'll see how this turns out performance-wise.
 */

require_once( fachb_PLUGDIR . "includes/db.php" );

?>
<div id="fachbetriebe-suche">
  <p>
    Suchen Sie Fachbetriebe nach Kategorie oder Adresse.
    Die Fachbetriebe werden Ihnen in Reihenfolge der Entfernung vom angegebenen
    Wohnort angezeigt.
  </p>
  <noscript>
    <p>
      Aktivieren Sie JavaScript, um die Fachbetriebesuche zu nutzen.
    </p>
  </noscript>
  <ul>

<?php
foreach ( fachb_list() as $betrieb ) {
  $cats = fachb_get_categories( $betrieb->id );
  $catNames = implode( ", ",
    array_map( function ( $cat ) {
      return $cat->name;
    }, $cats
  ) );
?>
    <li>
      <a href="<?php echo $betrieb->url; ?>">
        <img src="<?php echo $betrieb->logo; ?>" alt="Logo von <?php echo $betrieb->name; ?>">
        <?php echo $betrieb->name; ?>
      </a>
      <ul>
        <li><?php echo $betrieb->adresse; ?></li>
        <li>
          Fachbetrieb für
          <?php echo $catNames; ?>
        </li>
      </ul>
    </li>

<?php
}
?>
  </ul>
</div>

<?php


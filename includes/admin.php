<?php

namespace Fachbetrieb\Admin;

require_once( FACHBETRIEB_PLUGDIR . 'includes/geo.php' );


use \Fachbetrieb\Db;
use \Fachbetrieb\Db\Betrieb;
use \Fachbetrieb\Geo;


add_action( "admin_menu", \Fachbetrieb\Admin\register(...) );
add_action( "admin_post_fachbetrieb", \Fachbetrieb\Admin\admin_post(...) );


function register( ): void {
  add_menu_page( 
    "Fachbetrieb", 
    "Fachbetrieb", 
    "publish_posts", 
    "fachbetrieb", 
    \Fachbetrieb\Admin\display_form(...)
  );
}


function admin_post( ): void {
  if ( !current_user_can( "publish_posts" ) ) {
    status_header(403);
    exit( "Du hast keine Berechtigung, die Datenbank zu aktualisieren." );
  }


  switch ( $_POST['inner_action'] ) {
  case 'create_company':
    $id = Db\create_betrieb( new Betrieb( $_POST ) );
    wp_redirect( admin_url( '?page=fachbetrieb&id=' . $id ) );
    exit();

  case 'update_company':
    // This is called from the update sub page.
    $id = $_POST['id'];
    $betrieb_updated = new Betrieb( $_POST );
    Db\update_betrieb( $id, $betrieb_updated );

    // Filter only the checkboxes for enabled categories.
    $categories = array_keys (
      array_filter(
        $_POST,
        function ( $v, $k ) {
          return is_int( $k )
            // Category checkboxes are 
            // differentiated by having a 
            // numerical key.
            && $v;
        },
        ARRAY_FILTER_USE_BOTH
      )
    );
    Db\assign_categories( $id, $categories );

    break;

  case 'delete_company':
    Db\delete_betrieb( $_POST['id'] );
    break;


  case 'create_category':
    Db\create_category( $_POST['name'] );
    break;

  case 'rename_category':
    Db\rename_category( $_POST['id'], $_POST['name'] );
    break;

  case 'delete_category':
    Db\delete_category( $_POST['id'] );
    break;
  }

  // Most routes should go back to main form.
  wp_redirect( admin_url( '?page=fachbetrieb' ) );
  exit();
}


function display_form( ): void {
  if ( isset( $_GET['id'] ) ) {
    $betrieb = Db\get_betrieb( $_GET["id"] );
    if ( $betrieb ) {
      display_update_form( $_GET['id'], $betrieb );
      return;
    }
  }

?>
  <h1>Fachbetrieb</h1>
  <h2>Betrieb hinzufügen</h2>
  <form action="<?php echo admin_url( "admin-post.php" ); ?>" method="post">
    <input type="hidden" name="action" value="fachbetrieb" />
    <input type="hidden" name="inner_action" value="create_company" />
    <div>
      <input type="text" name="name" id="name" 
        placeholder="Baugeschäft Mustermann" required>
      <label for="name">
        Name des Betriebs
      </label>
    </div>
    <div>
      <input type="text" name="address" id="address" 
        placeholder="Musterstr. 123, 24114 Kiel" required>
      <label for="address">
        Adresse
      </label>
    </div>
    <div>
      <input type="text" name="url" id="url" 
        placeholder="https://musterbau.de">
      <label for="url">
        Internetadresse (optional)
      </label>
    </div>
    <div>
      <input type="text" name="email" id="email" 
        placeholder="max@musterbau.de">
      <label for="email">
        E-Mail-Adresse (optional)
      </label>
    </div>
    <div>
      <input type="text" name="phone" id="phone" 
        placeholder="+49 431 xxxxxx">
      <label for="phone">
        Telefonnummer (optional)
      </label>
    </div>
    <div>
      <input type="text" name="logo_url" id="logo_url" 
        placeholder="https://musterbau.de/pfad/zu/logo.png">
      <label for="logo_url">
        Logo-URL (optional)
      </label>
    </div>
    <input type="submit" value="Hinzufügen"/>
  </form>

  <h2>Betrieb ändern</h2>
  <form action="" method="get">
    <!-- Stay in form. -->
    <input type="hidden" name="page" value="fachbetrieb"/>
    <?php display_company_selector( ); ?>
    <input type="submit" value="Ändern..."/>
  </form>

  <h2>Betrieb löschen</h2>
  <form action="<?php echo admin_url( "admin-post.php" ); ?>" method="post">
    <input type="hidden" name="action" value="fachbetrieb" />
    <input type="hidden" name="inner_action" value="delete_company" />
    <?php display_company_selector( ); ?>
    <input type="submit" value="Löschen" 
      onclick="return confirm('Sind Sie sicher?')"/>
  </form>

  <h1>Kategorie</h1>
  <h2>Kategorie hinzufügen</h2>
  <form action="<?php echo admin_url( "admin-post.php" ); ?>" method="post">
    <input type="hidden" name="action" value="fachbetrieb" />
    <input type="hidden" name="inner_action" value="create_category" />
    <div>
      <input type="text" name="name" id="name" 
        placeholder="Energetische Sanierung" required>
      <label for="name">
        Name der Kategorie
      </label>
    </div>
    <input type="submit" value="Hinzufügen"/>
  </form>

  <h2>Kategorie umbenennen</h2>
  <form action="<?php echo admin_url( "admin-post.php" ); ?>" method="post">
    <input type="hidden" name="action" value="fachbetrieb" />
    <input type="hidden" name="inner_action" value="rename_category" />
    <?php display_category_selector( ); ?>
    <div>
      <input type="text" name="name" id="name" required>
      <label for="name">
        Neuer Name
      </label>
    </div>
    <input type="submit" value="Umbenennen" />
  </form>

  <h2>Kategorie löschen</h2>
  <form action="<?php echo admin_url( "admin-post.php" ); ?>" method="post">
    <input type="hidden" name="action" value="fachbetrieb" />
    <input type="hidden" name="inner_action" value="delete_category" />
    <?php display_category_selector( ); ?>
    <input type="submit" value="Löschen" 
      onclick="return confirm('Sind Sie sicher?')"/>
  </form>
<?

  // Signal errors (invalid addresses). 
  $errs = array_filter(
    Db\list_betrieb( ), 
    function ( $entry ) {
      return !Geo\resolve( $entry['betrieb']->address );
    }
  );

  if ( $errs ) {
?>
  <h1>Probleme (<?php echo count( $errs ); ?>)</h1>
<?php
    foreach ( $errs as $err ) {
      $id = $err['id'];
      $betrieb = $err['betrieb'];
?>
  <h3 style="color:red;">
    Betrieb <?php echo $betrieb->name; ?> hat ungültige Adresse:
    <?php echo $betrieb->address; ?>.
    <a href="<?php echo admin_url( "?page=fachbetrieb&id=" . $id ); ?>">Ändern</a>
  </h3>
<?php
    }
  }
}


function display_update_form( int $id, Betrieb $betrieb ) {
  $all_cat = Db\list_category( );
  $my_cat = Db\get_categories( $id );

?>
  <h1>Ändern des Betriebs: <?php
    echo $betrieb->name;
  ?></h1>
  <a href="<?php echo admin_url( "?page=fachbetrieb" ) ?>">&lt; Zurück</a>
  <!-- Main form. -->
  <form action="<?php echo admin_url( "admin-post.php" ); ?>" method="post">
    <input type="hidden" name="action" value="fachbetrieb">
    <input type="hidden" name="inner_action" value="update_company">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <h2>Daten</h2>
    <div>
      <input type="text" name="name" id="name" 
        value="<?php echo $betrieb->name ?>" required>
      <label for="name">
        Name des Betriebs
      </label>
    </div>
    <div>
      <input type="text" name="address" id="address" 
        placeholder="Musterstr. 123, 24114 Kiel" 
        value="<?php echo $betrieb->address ?>" required>
      <label for="address">
        Adresse
      </label>
    </div>
    <div>
      <input type="text" name="url" id="url" 
        placeholder="https://musterbau.de"
        value="<?php echo $betrieb->url ?>">
      <label for="url">
        Internetadresse (optional)
      </label>
    </div>
    <div>
      <input type="text" name="email" id="email" 
        placeholder="max@musterbau.de"
        value="<?php echo $betrieb->email ?>">
      <label for="email">
        E-Mail-Adresse (optional)
      </label>
    </div>
    <div>
      <input type="text" name="phone" id="phone" 
        placeholder="+49 431 xxxxxx"
        value="<?php echo $betrieb->phone ?>">
      <label for="phone">
        Telefonnummer (optional)
      </label>
    </div>
    <div>
      <input type="text" name="logo_url" id="logo_url" 
        placeholder="https://musterbau.de/pfad/zu/logo.png"
        value="<?php echo $betrieb->logo_url ?>">
      <label for="logo_url">
        Logo-URL (optional)
      </label>
    </div>
    <h2>Kategorienzuordnung</h2>
    <div>
      <?php foreach ( $all_cat as $cat ) { ?>
        <input type="checkbox"
          name="<?php echo $cat['id']; ?>"
          id="<?php echo $cat['id']; ?>"
          <?php echo array_filter( $my_cat, function ($my) use($cat) {
            return $my['id'] == $cat['id'];
          } ) ? "checked" : "" ?>
        />
        <label for="<?php echo $cat['id']; ?>">
          <?php echo $cat['name']; ?>
        </label>
      <?php } ?>
    </div>
    <input type="submit" value="Ändern"/>
  </form>
<?php }

function fachb_form_base() { 
  // TODO: add back once geocoding is refactored
}

function display_company_selector( ): void { 
?>
  <div>
    <label for="id">
      Betrieb wählen
    </label>
    <select name="id" id="id" required>
<?php
  foreach ( Db\list_betrieb() as $entry ) {
    echo '<option value="' . $entry['id'] .'">' . 
      esc_html( $entry['betrieb']->name ) . 
      '</option>';
  }
?>
    </select>
  <div>
<?php
}

function display_category_selector( ): void { 
?>
  <div>
    <label for="id">
      Kategorie wählen
    </label>
    <select name="id" id="id" required>
<?php
  foreach ( Db\list_category() as $entry ) {
    echo '<option value="' . $entry['id'] .'">' . 
      esc_html( $entry['name'] ) .
      '</option>';
  }
?>
    </select>
  <div>
<?php
}


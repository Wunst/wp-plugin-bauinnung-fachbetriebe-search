<?php
require_once( fachb_PLUGDIR . "includes/db.php" );

add_action( "admin_menu", "fachb_form_register" );
add_action( "admin_post_fachb_create", "fachb_create_handler");
add_action( "admin_post_fachb_delete", "fachb_delete_handler");
add_action( "admin_post_fachb_update", "fachb_update_handler" );
add_action( "admin_post_fachb_cat_create", "fachb_cat_create_handler" );
add_action( "admin_post_fachb_cat_update", "fachb_cat_update_handler" );
add_action( "admin_post_fachb_cat_delete", "fachb_cat_delete_handler" );

function fachb_form_register() {
  add_menu_page( "Fachbetrieb", "Fachbetrieb", "publish_posts", "fachbetrieb", "fachb_form" );
}

/*
 * Exits with error if user does not have permission to update the database.
 * Note: Permission is tied to capability to `publish_posts`.
 */
function fachb_check_permission() {
  if ( !current_user_can( "publish_posts" ) ) {
    status_header(403);
    exit( "Du hast keine Berechtigung, die Datenbank zu aktualisieren." );
  }
}

/*
 * Exits with error if param with key not given, otherwise returns param.
 */
function fachb_require_param($key) {
  if ( !$_POST[$key] ) {
    status_header(400);
    exit( "Fehlender Parameter: $key" );
  }
  return $_POST[$key];
}

function fachb_create_handler() {
  fachb_check_permission();
  $name = fachb_require_param("name");
  $address = fachb_require_param("address");
  $url = $_POST["url"]; // optional
  $logo = $_POST["logo"]; // optional

  $id = fachb_create( $name, $address, $url, $logo );

  wp_redirect( admin_url( "?page=fachbetrieb&id=$id" ) );
  exit();
}

function fachb_delete_handler() {
  fachb_check_permission();
  $id = fachb_require_param("id");
  fachb_delete( intval( $id ) );
  wp_redirect( admin_url( "?page=fachbetrieb" ) );
  exit();
}

function fachb_update_handler() {
  fachb_check_permission();
  $id = fachb_require_param("id");

  $name = fachb_require_param("name");
  $address = fachb_require_param("adresse");
  $url = $_POST["url"];
  $logo = $_POST["logo"];

  if ( !$url ) // Do not display a link on empty URL.
    $url = null;

  if ( !$logo )
    $logo = null;

  $new_cat = array_filter( fachb_category_list(), function ($cat) {
    return $_POST[strval( $cat->id )] == "on";
  } );

  fachb_update( intval( $id ), $name, $address, $url, $logo, $new_cat );

  wp_redirect( admin_url( "?page=fachbetrieb" ) );
  exit();
}

function fachb_cat_create_handler() {
  fachb_check_permission();
  $name = fachb_require_param( "name" );

  $id = fachb_category_create( $name );

  wp_redirect( admin_url( "?page=fachbetrieb" ) );
  exit();
}

function fachb_cat_delete_handler() {
  fachb_check_permission();
  $id = fachb_require_param("id");
  fachb_category_delete( intval( $id ) );
  wp_redirect( admin_url( "?page=fachbetrieb" ) );
  exit();
}

function fachb_cat_update_handler() {
  fachb_check_permission();
  $id = fachb_require_param("id");
  $name = fachb_require_param("name");

  fachb_category_update( intval( $id ), $name );

  wp_redirect( admin_url( "?page=fachbetrieb" ) );
  exit();
}

function fachb_form() {
  if ( $_GET["id"] ) {
    fachb_form_update();
  } else {
    fachb_form_base();
  }
}

function fachb_form_update() {
  $betrieb = fachb_get( $_GET["id"] );
  $all_cat = fachb_category_list();
  $my_cat = fachb_get_categories( $betrieb->id );

  if ( !$betrieb ) {
    fachb_form_base();
    exit();
  }
 
?>
  <h1>Ändern des Betriebs: <?php
    echo $betrieb->name;
  ?></h1>
  <a href="<?php echo admin_url( "?page=fachbetrieb" ) ?>">&lt; Zurück</a>
  <!-- Main form. -->
  <form action="<?php echo admin_url( "admin-post.php" ); ?>" method="post">
    <h2>Daten</h2>
    <input type="hidden" name="action" value="fachb_update" />
    <input type="hidden" name="id" value="<?php echo $_GET["id"]; ?>" />
    <div>
      <input type="text" name="name" value="<?php
        echo $betrieb->name; 
      ?>">
      <label for="name">
        Name des Betriebs
      </label>
    </div>
    <div>
      <input type="text" name="adresse" value="<?php
        echo $betrieb->adresse; 
      ?>">
      <label for="address">
        Adresse
      </label>
    </div>
    <div>
      <input type="text" name="url" value="<?php
        echo $betrieb->url; 
      ?>">
      <label for="url">
        Internetadresse (optional)
      </label>
    </div>
    <div>
      <input type="text" name="logo" id="logo" value="<?php
        echo $betrieb->logo;
      ?>">
      <label for="logo">
        Logo-URL (optional)
      </label>
    </div>
    <h2>Kategorienzuordnung</h2>
    <div>
      <?php foreach ( $all_cat as $cat ) { ?>
        <input type="checkbox"
          name="<?php echo $cat->id; ?>"
          id="<?php echo $cat->id; ?>"
          <?php echo array_filter( $my_cat, function ($my) use($cat) {
            return $my->id == $cat->id;
          } ) ? "checked" : "" ?>
        />
        <label for="<?php echo $cat->id; ?>">
          <?php echo $cat->name; ?>
        </label>
      <?php } ?>
    </div>
    <input type="submit" value="Ändern"/>
  </form>
<?php }

function fachb_form_base() { ?>
  <h1>Fachbetrieb</h1>
  <h2>Betrieb hinzufügen</h2>
  <form action="<?php echo admin_url( "admin-post.php" ); ?>" method="post">
    <input type="hidden" name="action" value="fachb_create" />
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
      <input type="text" name="logo" id="logo" 
        placeholder="https://musterbau.de/pfad/zu/logo.png">
      <label for="logo">
        Logo-URL (optional)
      </label>
    </div>
    <input type="submit" value="Hinzufügen"/>
  </form>

  <h2>Betrieb ändern</h2>
  <form action="" method="get">
    <!-- Stay in form. -->
    <input type="hidden" name="page" value="fachbetrieb"/>
    <?php fachb_form_select(); ?>
    <input type="submit" value="Ändern..."/>
  </form>

  <h2>Betrieb löschen</h2>
  <form action="<?php echo admin_url( "admin-post.php" ); ?>" method="post">
    <input type="hidden" name="action" value="fachb_delete" />
    <?php fachb_form_select(); ?>
    <input type="submit" value="Löschen" 
      onclick="return confirm('Sind Sie sicher?')"/>
  </form>

  <h1>Kategorie</h1>
  <h2>Kategorie hinzufügen</h2>
  <form action="<?php echo admin_url( "admin-post.php" ); ?>" method="post">
    <input type="hidden" name="action" value="fachb_cat_create" />
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
    <input type="hidden" name="action" value="fachb_cat_update" />
    <?php fachb_form_select_category(); ?>
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
    <input type="hidden" name="action" value="fachb_cat_delete" />
    <?php fachb_form_select_category(); ?>
    <input type="submit" value="Löschen" 
      onclick="return confirm('Sind Sie sicher?')"/>
  </form>
<?php }

function fachb_form_select() { ?>
  <div>
    <label for="id">
      Betrieb wählen
    </label>
    <select name="id" id="id" required>
<?php
  foreach ( fachb_list() as $betrieb ) {
    echo "<option value=$betrieb->id>$betrieb->name</option>";
  }
?>
    </select>
  <div>
<?php }

function fachb_form_select_category() { ?>
  <div>
    <label for="id">
      Kategorie wählen
    </label>
    <select name="id" id="id" required>
<?php
  foreach ( fachb_category_list() as $cat ) {
    echo "<option value=$cat->id>$cat->name</option>";
  }
?>
    </select>
  <div>
<?php }


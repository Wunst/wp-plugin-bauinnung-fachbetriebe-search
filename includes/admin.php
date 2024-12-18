<?php
require_once( fachb_PLUGDIR . "includes/db.php" );

add_action( "admin_menu", "fachb_form_register" );
add_action( "admin_post_fachb_create", "fachb_create_handler");

function fachb_form_register() {
  add_menu_page( "Fachbetrieb", "Fachbetrieb", "publish_posts", "fachbetrieb", "fachb_form" );
}

function fachb_create_handler() {
  function require_param($key) {
    if ( !$_POST[$key] ) {
      status_header(400);
      exit( "Fehlender Parameter: $key" );
    }
    return $_POST[$key];
  }

  $name = require_param("name");
  $address = require_param("address");
  $url = $_POST[$url]; // optional

  $id = fachb_create( $name, $address, $url );

  wp_redirect( admin_url( "?page=fachbetrieb&id=$id" ) );
  exit();
}

function fachb_form() {
  if ( $_GET["id"] ) {
    fachb_form_update( $_GET["id"] );
  } else {
    fachb_form_base();
  }
}

function fachb_form_update() {
  $betrieb = fachb_get( $_GET["id"] );
?>
  <h1>Ändern des Betriebs: <?php
    echo $betrieb->name;
  ?></h1>
  <!-- Form to go back without leaving admin panel. -->
  <form action"" method="get">
    <input type="hidden" name="page" value="fachbetrieb"/>
    <input type="submit" value="Zurück zum Menü"/>
  </form>
  <p/>
  <!-- Main form. -->
  <form action="<?php echo admin_url( "admin-post.php" ); ?>" method="post">
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
        placeholder="www.musterbau.de">
      <label for="url">
        Internetadresse (optional)
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


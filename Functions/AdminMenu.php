<?php

namespace Fachbetrieb;

class AdminMenu {
  public static function register(): void {
    add_menu_page( "Fachbetrieb", "Fachbetrieb", "publish_posts", "fachbetrieb", self::form(...) );
    add_action( "admin_post_fachbetrieb", self::handle_admin_post(...) );
  }

  private static function form(): void {
    $action = admin_url( 'admin-post.php' );
    
    // Generate form for selecting company and category.
    $betrieb_select_options = implode( array_map( function ( $betrieb ) {
      return <<<HTML
        <option value="$betrieb->id">$betrieb->name</option>
      HTML;
    }, Db::all() ) );
    $category_select_options = implode( array_map( function ( $id, $name ) {
      return <<<HTML
        <option value="$id">$name</option>
      HTML;
    }, array_keys( Db::all_categories() ), array_values( Db::all_categories() ) ) );

    echo <<<HTML
      <h1>Fachbetrieb</h1>
      <h2>Betrieb hinzufügen</h2>
      <form action="{$action}" method="post">
        <input name="action" type="hidden" value="fachbetrieb"/>
        <input name="internal-action" type="hidden" value="create"/>
        <div><input name="name" id="name" required/> <label for="name">Name</label></div>
        <div><input name="address" id="address" required/> <label for="address">Adresse</label></div>
        <div><input name="url" id="url"/> <label for="url">URL</label></div>
        <div><input name="email" id="email"/> <label for="email">E-Mail</label></div>
        <div><input name="phone" id="phone"/> <label for="phone">Telefon</label></div>
        <div><input name="logo_url" id="logo_url"/> <label for="logo_url">Logo URL</label></div>
        <input type="submit" value="Hinzufügen"/>
      </form>

      <h2>Betrieb ändern</h2>
      <!-- TODO --->

      <h2>Betrieb löschen</h2>
      <form action="{$action}" method="post">
        <input name="action" type="hidden" value="fachbetrieb"/>
        <input name="internal-action" type="hidden" value="delete"/>
        <div>
          <label for="id">Betrieb wählen</label>
          <select name="id" id="id" required>
            {$betrieb_select_options}
          </select>
        </div>
        <input type="submit" value="Löschen" onclick="return confirm('Sind Sie sicher?')" />
      </form>
      
      <h2>Kategorie hinzufügen</h2>
      <form action="{$action}" method="post">
        <input name="action" type="hidden" value="fachbetrieb"/>
        <input name="internal-action" type="hidden" value="create category"/>
        <div><input name="name" id="name" required/> <label for="name">Name</label></div>
        <input type="submit" value="Hinzufügen"/>
      </form>

      <h2>Kategorie löschen</h2>
      <form action="{$action}" method="post">
        <input name="action" type="hidden" value="fachbetrieb"/>
        <input name="internal-action" type="hidden" value="delete category"/>
        <div>
          <label for="id">Kategorie wählen</label>
          <select name="id" id="id" required>
            {$category_select_options}
          </select>
        </div>
        <input type="submit" value="Löschen" onclick="return confirm('Sind Sie sicher?')" />
      </form>

      <h2>Kategorie umbenennen</h2>
      <form action="{$action}" method="post">
        <input name="action" type="hidden" value="fachbetrieb"/>
        <input name="internal-action" type="hidden" value="rename category"/>
        <div>
          <label for="id">Kategorie wählen</label>
          <select name="id" id="id" required>
            {$category_select_options}
          </select>
        </div>
        <div><input name="name" id="name" required/> <label for="name">Name</label></div>
        <input type="submit" value="Umbenennen">
      </form>
    HTML;
  }

  private static function handle_admin_post( ): void {
    if ( !current_user_can( 'publish_posts' ) ) {
      status_header( 403 );
      exit( 'Du hast keine Berechtigung, die Datenbank zu aktualisieren.' );
    }

    switch ( $_POST['internal-action'] ) {
    case 'create':
      Betrieb $betrieb = new Betrieb();
      $betrieb->name = $_POST['name'];
      $betrieb->address = $_POST['address'];
      $betrieb->url = $_POST['url'];
      $betrieb->email = $_POST['email'];
      $betrieb->phone = $_POST['phone'];
      $betrieb->logo_url = $_POST['logo_url'];
      Db::create( $betrieb );
      wp_redirect( admin_url( "?page=fachbetrieb&id={$id}" );
      return;

    case 'delete':
      Db::delete( $_POST['id'] );
      break;

    case 'create category':
      Db::create_category( $_POST['name'] );
      break;
      
    case 'rename category':
      Db::update_category( $_POST['id'], $_POST['name'] );
      break;

    case 'delete category':
      //Db::delete_category( $_POST['id'], $_POST['name'] );
      // TODO: NOT YET IMPLEMENTED
      break;

    case 'update':
      // TODO: NOT YET IMPLEMENTED
    }

    wp_redirect( admin_url( "?page=fachbetrieb" );
  }
}

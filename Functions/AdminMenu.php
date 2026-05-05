<?php

namespace Fachbetrieb;

class AdminMenu {
  public static function register(): void {
    add_menu_page( "Fachbetrieb", "Fachbetrieb", "publish_posts", "fachbetrieb", self::form(...) );
  }

  private static function form(): void {
    echo <<<HTML
      <h1>Fachbetrieb</h1>
      <h2>Betrieb hinzufügen</h2>
      <form method="post">
        <input name="action" type="hidden" value="create"/>
        <div><input name="name" id="name" required/> <label for="name">Name</label></div>
        <div><input name="address" id="address" required/> <label for="address">Adresse</label></div>
        <div><input name="url" id="url"/> <label for="url">URL</label></div>
        <div><input name="email" id="email"/> <label for="email">E-Mail</label></div>
        <div><input name="phone" id="phone"/> <label for="phone">Telefon</label></div>
        <input type="submit" value="Hinzufügen"/>
      </form>
      
      <h2>Kategorie hinzufügen</h2>
      <form method="post">
        <input name="action" type="hidden" value="create category"/>
        <div><input name="name" id="name" required/> <label for="name">Name</label></div>
        <input type="submit" value="Hinzufügen"/>
      </form>
    HTML;
  }
}

<?php 

namespace Fachbetrieb;

/** Represents company main data (without list of categories). */
class Betrieb {
  public string $name;
  public string $address;

  public ?string $url;
  public ?string $email;
  public ?string $phone;
  public ?string $logo_url;
}

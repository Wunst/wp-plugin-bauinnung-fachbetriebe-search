{
  description = "Reproducible dev environment for php + JS WordPress plugins";

  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixpkgs-unstable";
    flake-utils.url = "github:numtide/flake-utils";
  };

  outputs = { nixpkgs, flake-utils, ... }:
    flake-utils.lib.eachDefaultSystem
    (system: 
    let
      pkgs = import nixpkgs {
        inherit system;
      };
    in {
      devShells.default = 
        with pkgs; 
        mkShell {
          buildInputs = [ phpPackages.composer nodePackages.npm nodePackages.typescript-language-server ];
          shellHook = ''
            echo Installing composer dependencies…
            composer install

            echo Installing npm dependencies…
            npm install

            command -v docker || echo Docker not installed. wp-env will not work
          '';
        };
    });
}

( function( wp ) {
	var registerBlockType = wp.blocks.registerBlockType;
	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor.useBlockProps;

	registerBlockType( 'bauinnung-kiel-fachbetrieb/fachbetrieb', {
		edit: function() {
			return el(
				'p',
				useBlockProps(),
        "Fachbetrieb finden"
			);
		},
	} );
}(
	window.wp
) );


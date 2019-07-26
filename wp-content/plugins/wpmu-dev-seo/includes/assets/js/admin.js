
( function( $ ) {

	$( function() {

		$( '.toggle-contextual-help' ).click( function () {
			$( '#contextual-help-link' ).trigger( 'click' );
			return false;
		} );

	} );

} )( jQuery );

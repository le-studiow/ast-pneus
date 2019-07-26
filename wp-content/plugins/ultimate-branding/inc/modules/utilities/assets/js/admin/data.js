jQuery( document ).ready( function( $ ) {
	/**
	 * reset!
	 */
	$( '.branda-data-reset-confirm' ).on( 'click', function () {
		var data = {
			action: 'branda_data_reset',
			_wpnonce: $(this).data('nonce')
		};
		$.post( ajaxurl, data, function( response ) {
			if ( response.success ) {
				window.location.href = response.data.url;
			} else {
				window.ub_sui_notice( response.data.message, 'error' );
			}
		});
	});
	/**
	 * delete subsites data
	 */
	function branda_delete_subsite( input ) {
		var data = {
			action: 'branda_data_delete_subsites',
			id: input.id,
			offset: input.offset,
			_wpnonce: input.nonce
		};
		var parent = $('#branda-data-delete-subsites-container');
		var value = input.progress + '%';
		/**
		 * end!
		 */
		if ( 'end' === input.offset ) {
			parent.html( input.html );
			return;
		}
		/**
		 * set progress
		 */
		$('.sui-progress-state span', parent ).html( input.state );
		$('.sui-progress-text span', parent ).html( value );
		$('.sui-progress-bar span', parent ).css( 'width', value );
		/**
		 * next site
		 */
		$.post( ajaxurl, data, function( response ) {
			if ( response.success ) {
				branda_delete_subsite( response.data );
			} else {
				window.ub_sui_notice( response.data.message, 'error' );
			}
		});
	}
	$( '.branda-data-delete-subsites-confirm' ).on( 'click', function () {
		SUI.dialogs[ $(this).closest( '.sui-dialog').attr('id') ].hide();
		var data = {
			offset: 0,
			action: 'branda_data_delete_subsites',
			_wpnonce: $(this).data('nonce')
		};
		$.post( ajaxurl, data, function( response ) {
			if ( response.success ) {
				$('#branda-data-delete-subsites-container').html( response.data.html );
				branda_delete_subsite( response.data );
			} else {
				window.ub_sui_notice( response.data.message, 'error' );
			}
		});
	});
});

/**
 * Branda admin file for module "Header Content".
 * ￼
 * @since 3.0.0
 */
jQuery( document ).ready( function($) {
    $('.ub-header-subsites-toggle').on( 'change', function() {
        var $parent = $(this).closest( '.sui-box-settings-row');
        var value = $(this).val();
        if ( 'on' === value ) {
            $('.ub-header-subsites .sui-accordion-item', $parent ).removeClass( 'branda-not-affected' );
            $('.sui-notice', $parent ).hide();
        } else {
            $('.ub-header-subsites .sui-accordion-item', $parent ).addClass( 'branda-not-affected' );
            $('.sui-notice', $parent ).show();
        }
    });
});

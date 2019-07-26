/**
 * Branda: Dashboard Feeds
 * http://premium.wpmudev.org/
 *
 * Copyright (c) 2018-2019 Incsub
 * Licensed under the GPLv2 +  license.
 */
/* global window, SUI, ajaxurl */

/**
 * Add feed
 */
jQuery( window.document ).ready( function( $ ){
    "use strict";

    /**
     * Open add/edit modal
     */
    $('.branda-dashboard-feeds-add, .branda-dashboard-feeds-save').on( 'click', function() {
        var parent = $('.sui-box-body', $(this).closest( '.sui-box' ) );
        var reqired = false;
        var id = $(this).data('id');
        $( '[data-required=required]', parent ).each( function() {
            if ( '' === $(this).val() ) {
                var local_parent = $(this).parent();
                local_parent.addClass('sui-form-field-error');
                $('span', local_parent ).addClass( 'sui-error-message' );
                reqired = true;
            }
        });
        if ( reqired ) {
            return;
        }
        var data = {
            action: 'branda_dashboard_feed_save',
            _wpnonce: $(this).data('nonce'),
            id: id,
            link: $('#branda-general-link-' + id, parent).val(),
            url: $('#branda-general-url-' + id, parent).val(),
            title: $('#branda-general-title-' + id, parent).val(),
            items: $('#branda-display-items-' + id, parent).val(),
            show_summary: $('.branda-display-show_summary input[type=radio]:checked', parent).val(),
            show_date: $('.branda-display-show_date input[type=radio]:checked', parent).val(),
            show_author: $('.branda-display-show_author input[type=radio]:checked', parent).val(),
            site: $('.branda-visibility-site input[type=radio]:checked', parent).val(),
            network: $('.branda-visibility-network input[type=radio]:checked', parent).val(),
        };
        $.post( ajaxurl, data, function( response ) {
            if ( response.success ) {
                window.location.reload();
            } else {
                window.ub_sui_notice( response.data.message, 'error' );
            }
        });
    });

    /**
     * Delete feed
     */
    $('.branda-dashboard-feeds-delete').on( 'click', function() {
        if ( 'bulk' === $(this).data('id') ) {
            return;
        }
        var data = {
            action: 'branda_dashboard_feed_delete',
            _wpnonce: $(this).data('nonce'),
            id: $(this).data('id' )
        };
        $.post( ajaxurl, data, function( response ) {
            if ( response.success ) {
                window.location.reload();
            } else {
                window.ub_sui_notice( response.data.message, 'error' );
            }
        });
    });
    /**
     * Bulk: confirm
     */
    $( '.branda-dashboard-feeds-delete[data-id=bulk]').on( 'click', function() {
        var data = {
            action: 'branda_dashboard_feed_delete_bulk',
            _wpnonce: $(this).data('nonce'),
            ids: [],
        }
        $('#branda-dashboard-feeds-panel .check-column :checked').each( function() {
            data.ids.push( $(this).val() );
        });
        $.post( ajaxurl, data, function( response ) {
            if ( response.success ) {
                window.location.reload();
            } else {
                window.ub_sui_notice( response.data.message, 'error' );
            }
        });
    });
    /**
     * Try to fetch site name and feed
     */
    $( '.branda-dashboard-feeds-url button' ).on( 'click', function() {
        var $parent = $(this).closest( '.sui-tabs' );
        var $input = $('input', $parent );
        var $target = $( '.'+$input.data('target'), $parent );
        var field;
        var data = {
            action: 'branda_get_site_data',
            _wpnonce: $input.data('nonce'),
            id: $input.data('id'),
            url: $input.val(),
        }
        $( '.sui-notice', $target ).hide();
        $( '.sui-notice-loading', $target ).show();
        $( '.branda-list', $target ).html('').hide();
        $.post( ajaxurl, data, function( response ) {
            if (
                response.success &&
                'undefined' !== response.data
            ) {
                if ( 0 === response.data.length ) {
                    $( '.sui-notice', $target ).hide();
                    $( '.sui-notice-warning', $target ).show();
                    return;
                }
                if ( 1 === response.data.length ) {
                    /**
                     * Title
                     */
                    field = $('.branda-general-title input', $parent );
                    if (
                        '' === field.val() &&
                        'undefined' !== response.data[0].title
                    ) {
                        field.val( response.data[0].title );
                    }
                    /**
                     * href
                     */
                    field = $('.branda-general-url input', $parent );
                    if (
                        '' === field.val() &&
                        'undefined' !== response.data[0].href
                    ) {
                        field.val( response.data[0].href );
                    }
                } else {
                    var row = wp.template( $input.data('tmpl') + '-row' );
                    var list = '';
                    $.each( response.data, function( index, value ) {
                        list += row( value );
                    });
                    $('.sui-notice', $target ).hide();
                    $('.branda-list', $target ).html( list ).show();
                    $( 'label', $target ).on( 'click', function() {
                        /**
                         * Title
                         */
                        field = $('.branda-general-title input', $parent );
                        field.val( $('.branda-title', $(this) ).html() );
                        /**
                         * href
                         */
                        field = $('.branda-general-url input', $parent );
                        field.val( $('.branda-href', $(this) ).html() );
                    });
                }
                $('.sui-notice-loading', $target ).hide();
            } else {
                $( '.sui-notice', $target ).hide();
                $( '.sui-notice-warning', $target ).show();
            }
        });
    });
});

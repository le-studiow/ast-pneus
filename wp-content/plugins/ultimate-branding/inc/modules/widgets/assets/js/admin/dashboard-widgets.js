/**
 * Branda: Dashboard Widgets
 * http://premium.wpmudev.org/
 *
 * Copyright (c) 2018-2019 Incsub
 * Licensed under the GPLv2 +  license.
 */
/* global window, SUI, ajaxurl */
var Branda = Branda || {};
/**
 * Dialogs
 */
Branda.dashboard_widgets_dialog_edit = 'branda-dashboard-widgets-edit';
Branda.dashboard_widgets_dialog_delete = 'branda-dashboard-widgets-delete';
/**
 * Dashboard Row Buttons Bind
 */
Branda.dashboard_widgets_bind = function( container ) {
    /**
     * Delete item
     */
    $('.branda-dashboard-widgets-delete', container ).on( 'click', function( e ) {
        var data = {
            action: 'branda_dashboard_widget_delete',
            _wpnonce: $(this).data('nonce'),
            id: $(this).data('id' )
        };
        e.preventDefault();
        $.post( ajaxurl, data, function( response ) {
            if ( response.success ) {
                var $parent = $('.branda-dashboard-widgets-items' );
                $( '[data-id=' + response.data.id + ']', $parent ).detach();
                SUI.dialogs[ Branda.dashboard_widgets_dialog_delete ].hide();
                window.ub_sui_notice( response.data.message, 'success' );
                if ( 1 > $( '[data-id]', $parent ).length ) {
                    $( '.sui-box-builder-message', $parent.parent() ).show();
                }
            } else {
                window.ub_sui_notice( response.data.message, 'error' );
            }
        });
    });
    /**
     * Dialog edit
     */
    $( '[data-a11y-dialog-show=' + Branda.dashboard_widgets_dialog_edit + ']', container ).on( 'click', function( e ) {
        var $button = $(this);
        var template;
        var $dialog = $( '#' + Branda.dashboard_widgets_dialog_edit );
        var $parent = $button.closest( '.sui-builder-field' );
        var data = {
            id: 'undefined' !== typeof $parent.data( 'id' )? $parent.data( 'id' ):'new',
            title: '',
            content: '',
            content_meta: '',
            nonce: $button.data( 'nonce' ),
            is_network: false,
            site: 'on',
            network: 'on'
        };
        var editor;
        e.preventDefault();
        /**
         * Dialog title
         */
        if ( 'new' === data.id ) {
            $dialog.addClass( 'branda-dialog-new' );
        } else {
            var args = {
                action: 'branda_dashboard_widgets_get',
                _wpnonce: $parent.data('nonce'),
                id: data.id
            };
            $dialog.removeClass( 'branda-dialog-new' );
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: args,
                async: false
            }).success( function( response ) {
                if ( ! response.success ) {
                    window.ub_sui_notice( response.data.message, 'error' );
                }
                data = response.data;
            });
            if ( 'undefined' === typeof data.title ) {
                return false;
            }
            data.nonce =  $parent.data( 'nonce' );
        }
        /**
         * set
         */
        $( 'input[name="branda[title]"]', $dialog ).val( data.title );
        $( 'input[name="branda[content]"]', $dialog ).val( data.content );
        $( 'input[name="branda[id]"]', $dialog ).val( data.id );
        $( 'input[name="branda[nonce]"]', $dialog ).val( data.nonce );
        editor = $( 'textarea', $dialog );
        editor.val( data.content );
        editor = editor.attr( 'id' );
        editor = tinymce.get( editor );
        if ( null !== editor ) {
            editor.setContent( data.content );
        }
        /**
         * visibility
         */
        template = wp.template( Branda.dashboard_widgets_dialog_edit + '-pane-visibility' );
        $( '.' + Branda.dashboard_widgets_dialog_edit + '-pane-visibility', $dialog ).html( template( data ) );
        /**
         * Re-init elements
         */
        SUI.brandaSideTabs();
        $( '.sui-tabs-flushed .branda-first-tab', $dialog ).trigger( 'click' );
    });
    /**
     * Dialog delete
     */
    $( '[data-a11y-dialog-show=' + Branda.dashboard_widgets_dialog_delete + ']', container ).on( 'click', function( e ) {
        var $dialog = $( '#' + Branda.dashboard_widgets_dialog_delete );
        var $parent = $(this).closest( '.sui-builder-field' );
        $( '.branda-dashboard-widgets-delete', $dialog )
            .data( 'id', $parent.data('id') )
            .data( 'nonce', $parent.data('nonce') )
        ;
    });
}
/**
 * Add feed
 */
jQuery( window.document ).ready( function( $ ){
    "use strict";
    /**
     * Sortable
     */
    $.fn.branda_dashboard_widgets_sortable_init = function() {
        $('.branda-dashboard-widgets-items').sortable({
            items: '.sui-builder-field'
        });
    }
    $.fn.branda_dashboard_widgets_sortable_init();
    /**
     * Save Dashboard Widget
     */
    $('.branda-dashboard-widgets-save').on( 'click', function() {
        var $parent = $(this).closest( '.sui-dialog' );
        var editor_id = Branda.dashboard_widgets_dialog_edit + '-content';
        var data = {
            action: 'branda_dashboard_widget_save',
            _wpnonce: $( 'input[name="branda[nonce]"]', $parent ).val(),
            id: $( 'input[name="branda[id]"]', $parent ).val(),
            content: $.fn.branda_editor( editor_id ),
            title: $( 'input[name="branda[title]"]', $parent ).val(),
            site: $( '[name="branda[site]"]:checked', $parent ).val(),
            network: $( '[name="branda[network]"]:checked', $parent).val(),
        };
        $.post( ajaxurl, data, function( response ) {
            if ( response.success ) {
                var $parent = $('.branda-dashboard-widgets-items' );
                var $row = $('[data-id='+response.data.id+']', $parent );
                if ( 0 < $row.length ) {
                    $( '.sui-builder-field-label', $row ).html( response.data.title );
                    $( '.sui-builder-field', $row )
                        .data( 'id', response.data.id )
                        .data( 'nonce', response.data.nonce )
                    ;
                } else {
                    var template = wp.template( Branda.dashboard_widgets_dialog_edit + '-row' );
                    $parent.append( template( response.data ) );
                    $.fn.branda_sui_dialog_rebind([
                        Branda.dashboard_widgets_dialog_edit,
                        Branda.dashboard_widgets_dialog_delete
                    ]);
                    $row = $('[data-id='+response.data.id+']', $parent );
                    Branda.dashboard_widgets_bind( $row );
                }
                SUI.dialogs[ Branda.dashboard_widgets_dialog_edit ].hide();
                window.ub_sui_notice( response.data.message, 'success' );
                $.fn.branda_dashboard_widgets_sortable_init;
            } else {
                window.ub_sui_notice( response.data.message, 'error' );
            }
        });
    });
    /**
     * bind
     */
    Branda.dashboard_widgets_bind( $( '.branda-admin-page' ) );
    /**
     * Dialog "Reset Widget Visibility List"
     */
    $( '#branda-dashboard-widgets-visibility-reset .branda-data-reset-confirm' ).on( 'click', function() {
        var data = {
            action: 'branda_dashboard_widget_visibility_reset',
            _wpnonce: $( this ).data( 'nonce' )
        };
        $.post( ajaxurl, data, function( response ) {
            if ( response.success ) {
                window.location.reload();
            } else {
                window.ub_sui_notice( response.data.message, 'error' );
            }
        });
    });
});

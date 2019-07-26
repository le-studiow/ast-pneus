/**
 * Branda: Admin Help Content
 * http://premium.wpmudev.org/
 *
 * Copyright (c) 2018-2019 Incsub
 * Licensed under the GPLv2 +  license.
 */
/* global window, SUI, ajaxurl */
var Branda = Branda || {};
Branda.admin_help_content_dialog_edit = 'branda-admin-help-content-edit';
Branda.admin_help_content_dialog_delete = 'branda-admin-help-content-delete';
jQuery( document ).ready( function ( $ ) {
    "use strict";
    /**
     * Sortable
     */
    $.fn.branda_admin_help_content_sortable_init = function() {
        $('.branda-admin-help-content-items').sortable({
            items: '.sui-builder-field'
        });
    }
    $.fn.branda_admin_help_content_sortable_init();
    /**
     * Scroll window to top when a help menu opens
     */
    $( document ).on( 'screen:options:open', function () {
        $( 'html, body' ).animate( {scrollTop: 0}, 'fast' );
    } );
    /**
     * SUI: add item
     */
    $( '.branda-admin-help-content-save' ).on( 'click', function () {
        var button = $( this );
        var $dialog = button.closest( '.sui-dialog' );
        var id = $('[name="branda[id]"]', $dialog ).val();
        var editor_id = $( 'textarea.wp-editor-area', $dialog ).attr( 'id' );
        var content = $.fn.branda_editor( editor_id );
        var data = {
            action: 'branda_admin_help_save',
            _wpnonce: button.data( 'nonce' ),
            id: id,
            title: $( 'input[type=text]', $dialog ).val(),
            content: content,
        };
        $.post( ajaxurl, data, function ( response ) {
            if ( response.success ) {
                var $parent = $('.module-admin-help-content-php .branda-admin-help-content-items' );
                var $row = $('[data-id='+response.data.id+']', $parent );
                if ( 0 < $row.length ) {
                    $( '.sui-builder-field-label', $row ).html( response.data.title );
                    $( '.sui-builder-field', $row )
                        .data( 'id', response.data.id )
                        .data( 'nonce', response.data.nonce )
                    ;
                } else {
                    var template = wp.template( Branda.admin_help_content_dialog_edit + '-row' );
                    $parent.append( template( response.data ) );
                    $row = $('[data-id='+response.data.id+']', $parent );
                }
                SUI.dialogs[ Branda.admin_help_content_dialog_edit ].hide();
                window.ub_sui_notice( response.data.message, 'success' );
                $.fn.branda_admin_help_content_sortable_init;
                $.fn.branda_sui_dialog_rebind([
                    Branda.admin_help_content_dialog_edit,
                    Branda.admin_help_content_dialog_delete
                ]);
                Branda.admin_help_content_dialog_bind( $row );
            } else {
                window.ub_sui_notice( response.data.message, 'error' );
            }
        } );
    } );
    /**
     * Dialog delete
     */
    $( '[data-a11y-dialog-show=' + Branda.admin_help_content_dialog_delete + ']' ).on( 'click', function( e ) {
        var $dialog = $( '#' + Branda.admin_help_content_dialog_delete );
        var $parent = $(this).closest( '.sui-builder-field' );
        $( '.branda-admin-help-content-delete', $dialog )
            .data( 'id', $parent.data('id') )
            .data( 'nonce', $parent.data('nonce') )
        ;
    });
    /**
     * SUI: delete item
     */
    $( '.branda-admin-help-content-delete' ).on( 'click', function () {
        var button = $( this );
        var data = {
            action: 'branda_admin_help_delete',
            _wpnonce: button.data( 'nonce' ),
            id: button.data( 'id' ),
        };
        $.post( ajaxurl, data, function ( response ) {
            if ( response.success ) {
                var $parent = $('.module-admin-help-content-php .branda-admin-help-content-items' );
                $( '[data-id=' + data.id + ']', $parent ).detach();
                SUI.dialogs[ Branda.admin_help_content_dialog_delete ].hide();
                window.ub_sui_notice( response.data.message, 'success' );
            } else {
                window.ub_sui_notice( response.data.message, 'error' );
            }
        } );
    } );
    /**
     * Dialog edit
     */
    Branda.admin_help_content_dialog_bind = function( container ) {
        $( '[data-a11y-dialog-show=' + Branda.admin_help_content_dialog_edit + ']', container ).on( 'click', function( e ) {
            var $button = $(this);
            var template;
            var $dialog = $( '#' + Branda.admin_help_content_dialog_edit );
            var $parent = $button.closest( '.sui-builder-field' );
            var data = {
                id: 'undefined' !== typeof $parent.data( 'id' )? $parent.data( 'id' ):'new',
                title: '',
                content: '',
                nonce: $button.data( 'nonce' )
            };
            var editor = $( 'textarea[name="branda[content]"]', $dialog ).attr( 'id' );
            editor = tinymce.get( editor );
            e.preventDefault();
            /**
             * Dialog title
             */
            if ( 'new' === data.id ) {
                $dialog.addClass( 'branda-dialog-new' );
            } else {
                var args = {
                    action: 'branda_admin_help_content_get',
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
            $('input[name="branda[title]"]', $dialog ).val( data.title );
            $('textarea[name="branda[content]"]', $dialog ).val( data.content );
            editor.setContent( data.content );
            $('.branda-admin-help-content-save', $dialog ).data( 'nonce', data.nonce );
            $( 'input[name="branda[id]"]', $dialog ).val( data.id );
        });
    }
    Branda.admin_help_content_dialog_bind( $( 'body' ) );
});

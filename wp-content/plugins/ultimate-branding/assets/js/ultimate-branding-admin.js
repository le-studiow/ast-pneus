/*! Branda - v3.1.2
 * https://premium.wpmudev.org/project/ultimate-branding/
 * Copyright (c) 2019; * Licensed GPLv2+ */
/* global window, jQuery  */

jQuery( window.document ).ready(function($){
    "use strict";
    var self = this,
        $suiPickerInputs = $( '.sui-colorpicker-input' );

    $suiPickerInputs.wpColorPicker( {

        change: function( event, ui ) {
            var $this = $( this );
            $this.val( ui.color.toCSS() ).trigger( 'change' );
        }
    });

    if ( $suiPickerInputs.hasClass( 'wp-color-picker' ) ) {

        $suiPickerInputs.each( function() {

            var $suiPickerInput = $(this),
                $suiPicker      = $suiPickerInput.closest( '.sui-colorpicker-wrap' ),
                $suiPickerColor = $suiPicker.find( '.sui-colorpicker-value span[role=button]' ),
                $suiPickerValue = $suiPicker.find( '.sui-colorpicker-value' ),
                $suiPickerClear = $suiPickerValue.find( 'button' ),
                $suiPickerType  = 'hex'
            ;

            var $wpPicker       = $suiPickerInput.closest( '.wp-picker-container' ),
                $wpPickerButton = $wpPicker.find( '.wp-color-result' ),
                $wpPickerAlpha  = $wpPickerButton.find( '.color-alpha' ),
                $wpPickerClear  = $wpPicker.find( '.wp-picker-clear' )
            ;

            // Check if alpha exists
            if ( $suiPickerInput.data( 'alpha' ) === true ) {

                $suiPickerType = 'rgba';

                // Listen to color change
                $suiPickerInput.bind( 'change', function() {

                    // Change color preview
                    $suiPickerColor.find( 'span' ).css({
                        'background-color': $wpPickerAlpha.css( 'background' )
                    });

                    // Change color value
                    $suiPickerValue.find( 'input' ).val( $suiPickerInput.val() );

                } );

            } else {

                // Listen to color change
                $suiPickerInput.bind( 'change', function() {

                    // Change color preview
                    $suiPickerColor.find( 'span' ).css({
                        'background-color': $wpPickerButton.css( 'background-color' )
                    });

                    // Change color value
                    $suiPickerValue.find( 'input' ).val( $suiPickerInput.val() );

                } );
            }

            // Add picker type class
            $suiPicker.find( '.sui-colorpicker' ).addClass( 'sui-colorpicker-' + $suiPickerType );

            // Open iris picker
            $suiPicker.find( '.sui-button, span[role=button]' ).on( 'click', function( e ) {

                $wpPickerButton.click();

                e.preventDefault();
                e.stopPropagation();

            } );

            // Clear color value
            $suiPickerClear.on( 'click', function( e ) {
                e.preventDefault();

                var reset_value = '';

                $wpPickerClear.click();
                $suiPickerValue.find( 'input' ).val( reset_value );
                $suiPickerInput.val( reset_value ).trigger( 'change' );
                $suiPickerColor.find( 'span' ).css({
                    'background-color': reset_value
                });

                e.preventDefault();
                e.stopPropagation();

            } );

        } );
    }

});

/* global window, jQuery, switch_button */
jQuery( window.document ).ready( function ( $ ) {
	"use strict";
	// Show/hide on button toggle.
	$( '#subsites_option .ub-footer-subsites-toggle' ).on( 'change', function () {
		var subsites_options = $( '#subsites_option .ub-footer-subsites' );
		if ( $( this ).is( ':checked' ) && 'on' === $( this ).val() ) {
			subsites_options.show();
		} else {
			subsites_options.hide();
		}
	} );
} );
/* global window, jQuery, SUI, ajaxurl */

jQuery( window.document ).ready(function($){
    /**
     * Show Welcome
     */
    function branda_show_welcome() {
        if (
            'undefined' === typeof SUI ||
            'undefined' === typeof SUI.dialogs
        ) {
            window.setTimeout( branda_show_welcome, 100 );
        } else if ( 'object' === typeof SUI.dialogs['branda-welcome'] ) {
            SUI.dialogs['branda-welcome'].show();
            $('button.branda-welcome-all-modules').on( 'click', function () {
                var dialog = $(this).closest( '.sui-dialog');
                var data = {
                    action: 'branda_welcome_get_modules',
                    nonce: $(this).data('nonce')
                };
                jQuery.post(ajaxurl, data, function( response ) {
                    if ( response.success ) {
                        dialog
                            .addClass( 'sui-dialog-lg' )
                            .removeClass( 'branda-welcome-step1' ).addClass( 'branda-welcome-step2' )
                        ;
                        $('.sui-box-title', dialog ).html( response.data.title );
                        $('.sui-box-body p', dialog )
                            .html( response.data.description )
                            .after( response.data.content )
                        ;
                        window.branda_modules_mark_all( '.branda-group-checkbox', dialog );
                        window.branda_modules_save_bulk( '.branda-welcome-activate', dialog, true );
                    } else if ( undefined !== typeof response.data.message ) {
                        window.ub_sui_notice( response.data.message, 'error' );
                    }
                });
            });
        }
    }
    branda_show_welcome();
    /**
     * Search modules on dashboard
     */
    $('#branda-dashboard-widget-summary-search')
        .on( 'change keydown keyup blur reset copy paste cut input', function() {
            var search = $(this).val();
            var target = $('#branda-dashboard-widget-modules');
            var re;
            if ( '' === search ) {
                $('tr, .sui-box', target ).show();
                $('#branda-dashboard-search-no-results').hide();
                return;
            }
            re = new RegExp( search, 'i' );
            $('td.sui-table--name', target).each( function() {
                var value = $(this).html();
                if ( value.match( re ) ) {
                    $(this).parent().show();
                    $(this).closest('.sui-box').show();
                } else {
                    $(this).parent().hide();
                }
            });
            $('table', target).each( function() {
                if ( 1 > $('tbody tr:visible', $(this)).length ) {
                    $(this).closest('.sui-box').hide();
                } else {
                    $(this).closest('.sui-box').show();
                }
            });
            if ( 1 > $('table:visible', target).length ) {
                $('#branda-dashboard-search-no-results').show();
                $('#branda-dashboard-search-no-results span').html( search );
            } else {
                $('#branda-dashboard-search-no-results').hide();
            }
        });
});

/* global window, jQuery, ace */

jQuery( window.document ).ready(function($){
    "use strict";
    if ( $.fn.datepicker ) {
        $('.simple-option input.datepicker').each( function() {
            $(this).datepicker({
                altFormat: 'yy-mm-dd',
                altField: '#'+$(this).data('alt')
            });
        });
    }
});

/* global window, jQuery */

jQuery( window.document ).ready(function( $ ){
    /**
     * Open upload input
     */
    $( '.simple-option-file .sui-upload-button' ).on( 'click', function() {
        $('.branda-upload', $(this).closest( '.sui-upload' ) ).click();
    });
    /**
     * reset
     */
    $( '.simple-option-file .sui-upload-file button' ).on( 'click', function() {
        $(this).closest( '.sui-upload' ).removeClass( 'sui-has_file' );
        $('.sui-notice', $(this).closest( '.simple-option' ) ).hide();
    });
    /**
     * bind change
     */
    jQuery( '.module-utilities-import-php .branda-upload').on( 'change', function(e) {
        var parent = $(this).closest( '.sui-box-body' );
        var button = jQuery( 'button[type=submit]', parent );
        var value =  jQuery(this).val();
        if ( '' === value ) {
            button.attr( 'disabled' );
            $( '.sui-upload', parent ).removeClass( 'sui-has_file' );
            $('.sui-notice', parent ).hide();
        } else {
            $( '.sui-upload', parent ).addClass( 'sui-has_file' );
            var base = value.split( /[\/\\]/ );
            if ( 0 < base.length ) {
                $('.sui-upload-file span', parent ).html( base[ base.length - 1 ] );
            }
            var re = /json$/i;
            if ( re.test( value ) ) {
                button.removeAttr( 'disabled' );
                $('.sui-notice', parent ).hide();
            } else {
                $('.sui-notice', parent ).show();
            }
        }
    });
});

/* global window, jQuery, wp, ajaxurl, wp_media_post_id */

function ub_bind_reset_image( obj ) {
    var container = jQuery(obj).closest('.simple-option');
    if ( container.hasClass( 'simple-option-gallery' ) ) {
        jQuery(obj).closest('.sui-upload').detach();
        return;
    }
    jQuery('.attachment-id', container ).val( null );
    jQuery('.sui-upload', container).removeClass( 'sui-has_file' );
}

function ub_media_bind( obj, event ) {
    var file_frame;
    var wp_media_post_id;
    var container = jQuery(obj).closest('.sui-upload');
    var set_to_post_id = jQuery('.attachment-id', container ).val();
    event.preventDefault();
    // If the media frame already exists, reopen it.
    if ( file_frame ) {
        // Set the post ID to what we want
        file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
        // Open frame
        file_frame.open();
        return;
    } else {
        // Set the wp.media post id so the uploader grabs the ID we want when initialised
        wp.media.model.settings.post.id = set_to_post_id;
    }

    // Create the media frame.
    file_frame = wp.media.frames.file_frame = wp.media({
        title: 'Select a image to upload',
        button: {
            text: 'Use this image',
        },
        multiple: false	// Set to true to allow multiple files to be selected
    });

    // When an image is selected, run a callback.
    file_frame.on( 'select', function() {
        // We set multiple to false so only get one image from the uploader
        var attachment = file_frame.state().get('selection').first().toJSON();
        // Do something with attachment.id and/or attachment.url here
        jQuery('.sui-image-preview', container ).attr( 'style', 'background-image: url(' + attachment.url + ')' );

        /**
         * Fill focal background
         */
        if ( 'content_background' === container.data( 'id' ) ) {
            jQuery('canvas.branda-focal', container.closest('form') )
                .css( 'backgroundImage', 'url(' + attachment.url + ')' )
            ;
        }
        /**
         * Fill SUI data
         */
        jQuery('.sui-upload-file span', container ).html( attachment.filename );
        jQuery('.attachment-id', container ).val( attachment.id );
        container.addClass( 'sui-has_file' );
        /**
         * Restore the main post ID
         */
        wp.media.model.settings.post.id = wp_media_post_id;
        /**
         * add new
         */
        var target = container.closest( 'div.simple-option' );
        if ( target.hasClass( 'simple-option-gallery' ) ) {
            if ( '' !== jQuery('.image-wrapper:last-child .attachment-id', target ).val() ) {
                var ub_template = wp.template( 'simple-options-media' );
                jQuery('.images', target).append( ub_template({
                    id: container.data('id'),
                    section_key: container.data('section_key'),
                    disabled: 'disabled',
                    container_class: ''
                }));
                jQuery( ".image-wrapper:last-child .button-select-image", target ).on( 'click', function( event ) {
                    ub_media_bind( this, event );
                });
                jQuery( ".images .image-reset", target ).on( 'click', function() {
                    ub_bind_reset_image( this );
                    return false;
                });
            }
        }

    });
    // Finally, open the modal
    file_frame.open();
}

jQuery( window.document ).ready( function( $ ) {
    $(".simple-option-media .images, .simple-option-gallery .images").each( function() {
        var ub_template = wp.template( 'simple-options-media' );
        var target = $( this );
        var data = window['_'+target.attr('id')];
        $.each( data.images, function(){
            target.append( ub_template( this ) );
            $( ".image-wrapper .button-select-image", target ).on( 'click', function( event ) {
                ub_media_bind( this, event );
            });
            $( ".images .image-reset", target ).on( 'click', function() {
                ub_bind_reset_image( this );
                return false;
            });
        });
    });
    $(".simple-option-media .image-reset, .simple-option-gallery .image-reset").on("click", function(){
        ub_bind_reset_image( this );
        return false;
    });
    // Restore the main ID when the add media button is pressed
    $( 'a.add_media' ).on( 'click', function() {
        wp.media.model.settings.post.id = wp_media_post_id;
    });
    $('.branda-add-image').on('click', function() {
        var ub_template = wp.template( 'simple-options-media' );
        var taget = $( '.images', $(this).parent());
    });
});


/* global wp, window, wp_media_post_id, jQuery, ajaxurl, ace, switch_button */

/**
 * close block
 */
/**
 * Simple Options: select2
 */
jQuery( window.document ).ready(function($){
    function brandaFormat(site) {
        if (site.loading) {
            return site.text;
        }
        var markup = "<div class='select2-result-site clearfix'>";
        markup += "<div class='select2-result-title'>" + site.title + "</div>";
        if ( 'undefined' !== typeof site.subtitle ) {
            markup += "<div class='select2-result-subtitle'>" + site.subtitle + "</div>";
        }
        markup += "</div>";
        return markup;
    }
    function brandaFormatSelection( site ) {
        return site.title || site.text;
    }
    if (jQuery.fn.SUIselect2) {
        $('.sui-select-ajax').SUIselect2({
            ajax: {
                url: ajaxurl,
                dataType: 'json',
                delay: 250,
                data: function( params ) {
                    var query = {
                        user_id: $(this).data('user-id') || $('select', $(this)).data('user-id'),
                        _wpnonce: $(this).data('nonce') || $('select', $(this)).data('nonce'),
                        action: $(this).data('action') || $('select', $(this)).data('action'),
                        page: params.page,
                        q: params.term,
                        extra: ( 'function' === typeof window[$(this).data('extra')] )? window[$(this).data('extra')]() : ''
                    };
                    return query;
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 1,
            escapeMarkup: function (markup) { return markup; },
            templateResult: brandaFormat,
            templateSelection: brandaFormatSelection
        });
    }
});

/* global window, jQuery, ajaxurl, ub_admin, wp, tinyMCE, SUI, ace */


jQuery( window.document ).ready(function($){
    "use strict";

    /**
     * add editor content
     */
    $.fn.extend( {
        branda_editor: function( editor_id ) {
            var content = $( '#' + editor_id ).val();
            var editor = tinyMCE.get( editor_id );
            if ( editor ) {
                content = editor.getContent();
            }
            return content;
        },
        branda_flag_status: function( element ) {
            var $header = $('[data-tab=' + $( element ).closest('.sui-box.branda-settings-tab').data( 'tab' ) + '] .sui-box-status');
            /**
             * Avoid to flag dialog changes!
             */
            if ( 0 < $( element ).closest( '.sui-dialog' ).length ) {
                return;
            }
            /**
             * Avoid to flag unnecessary!
             */
            if ( 0 < $( element ).closest( '.branda-avoid-flag' ).length ) {
                return;
            }
            window.branda_has_changed = 'unsaved';
            $( '.branda-status-changes-saved', $header ).hide();
            $( '.branda-status-changes-unsaved', $header ).show();
        },
        branda_sui_dialog_rebind: function( names ) {
            if ( 'string' === typeof names ) {
                names = [ names];
            }
            $.each( names, function( i, key ) {
                if ( 'undefined' !== typeof SUI.dialogs[ key ] ) {
                    SUI.dialogs[ key ].destroy();
                    SUI.dialogs[ key ].create();
                }
            });
        },
        branda_generate_id: function() {
            var text = "";
            var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
            for (var i = 0; i < 10; i++) {
                text += possible.charAt(Math.floor(Math.random() * possible.length));
            }
            return text;
        }
    });
    /**
     * Slider widget
     */
    if ( $.fn.slider ) {
        $('.simple-option div.ui-slider').each( function() {
            var id = $(this).data('target-id');
            if ( id ) {
                var target = $('#'+id);
                var value = target.val();
                var max = $(this).data('max') || 100;
                var min = $(this).data('min') || 0;
                $(this).slider({
                    value: value,
                    min: min,
                    max: max,
                    slide: function( event, ui ) {
                        target.val( ui.value );
                    }
                });
            }
        });
    }
    /**
     * sortable
     */
    $('table.sortable').sortable({
        items: 'tbody tr'
    });
    $('.branda-social-logos-main-container').sortable({
        items: '.simple-option'
    });
    $('.sui-box-builder-fields').sortable({
        items: '.sui-can-move'
    });
    /**
     * reset section
     */
    $('.branda-reset-section').on('click', function() {
        var dialog_id = 'branda-dialog-reset-section';
        var $dialog = $('#'+dialog_id);
        var $button = $('.branda-data-reset-confirm', $dialog );
        $button.data( 'module', $(this).data('module') );
        $button.data( 'network', $(this).data('network') );
        $button.data( 'nonce', $(this).data('nonce') );
        $button.data( 'section', $(this).data('section') );
        $('.sui-box-body b', $dialog ).html( $(this).data('title') );
        SUI.dialogs[ dialog_id ].show();
    });
    $('#branda-dialog-reset-section .branda-data-reset-confirm').on( 'click', function() {
        var data = {
            action: 'simple_option_reset_section',
            module: $(this).data('module'),
            nonce: $(this).data('nonce'),
            network: $(this).data('network'),
            section: $(this).data('id') || $(this).data('section')
        };
        jQuery.post(ajaxurl, data, function(response) {
            if ( response.success ) {
                window.location.reload();
            } else if ( 'undefined' !== response.data.message ) {
                window.ub_sui_notice( response.data.message, 'error' );
                SUI.dialogs[ 'branda-dialog-reset-section' ].hide();
            }
        });
    });
    /**
     * slave sections
     */
    $('.simple-options .postbox.section-is-slave').each( function() {
        var $this = $(this);
        var section = $this.data('master-section');
        var field = $this.data('master-field');
        var value = $this.data('master-value');
        $('[name="simple_options['+section+']['+field+']"]').on( 'change', function() {
            if ( 'checkbox' === $(this).attr('type') ) {
                if ( 'on' === value && $(this).is(":checked") ) {
                    $this.show();
                } else if ( 'off' === value && !$(this).is(":checked") ) {
                    $this.show();
                } else {
                    $this.hide();
                }
            } else {
                if ( $(this).val() === value ) {
                    $this.show();
                } else {
                    $this.hide();
                }
            }
        });
    });
    /**
     * Field complex master
     */
    $( '.simple-option.simple-option-complex-master' ).each( function() {
        var $this = $(this);
        var section = $this.data('master-section');
        var field = $this.data('master-field');
        var value = $this.data('master-value');
        if ( "undefined" === typeof section || "undefined" === typeof field || "undefined" === typeof value) {
            return;
        }
        $( '[name="simple_options[' + section + '][' + field + ']' )
            .on( 'change', function() {
                if ( $(this).val() === value ) {
                    $this.show();
                } else {
                    $this.hide();
                }
            });
    });
    /**
     * Copy section data
     */
    $( '.simple-options .ub-copy-section-settings a' ).on( 'click', function(e) {
        e.preventDefault();
        var parent = $(this).closest('div');
        var data = {
            'action': 'simple_option',
            'do': 'copy',
            'nonce': parent.data('nonce'),
            'module': parent.data('module'),
            'section': parent.data('section'),
            'from': $('select', parent).val()
        };
        if ( '-1' === data.from ) {
            window.alert( window.ub_admin.messages.copy.select_first );
            return false;
        }
        if ( window.confirm( window.ub_admin.messages.copy.confirm ) ) {
            jQuery.post(ajaxurl, data, function(response) {
                if ( response.success ) {
                    window.location.reload();
                } else {
                    if ( "undefined" === typeof response.data.message ) {
                        window.alert( window.ub_admin.messages.wrong );
                    } else {
                        window.alert( response.data.message );
                    }
                }
            });
        }
        return false;
    });
    /**
     * CSS editor
     * Using the Shared UI ACE editor.
     */
    function branda_ace_editor_placeholder( id, placeholder ) {
        var shouldShow = !SUI.editors[id].session.getValue().length;
        var node = SUI.editors[id].renderer.emptyMessageNode;
        if (!shouldShow && node) {
            SUI.editors[id].renderer.scroller.removeChild(SUI.editors[id].renderer.emptyMessageNode);
            SUI.editors[id].renderer.emptyMessageNode = null;
        } else if (shouldShow && !node) {
            node = SUI.editors[id].renderer.emptyMessageNode = window.document.createElement('div');
            node.textContent = placeholder;
            node.className = 'ace_emptyMessage';
            node.style.padding = '0 9px';
            SUI.editors[id].renderer.scroller.appendChild(node);
        }
    }
    SUI.editors = {};
    SUI.editors_value = {};
    jQuery( '.sui-ace-editor' ).each( function() {
        var id = this.id;
        var editor_id = $(this).data( 'id' );
        SUI.editors[id] = ace.edit( id );
        SUI.editors[id].getSession().setUseWorker( false );
        SUI.editors[id].setShowPrintMargin( false );
        SUI.editors[id].setTheme( 'ace/theme/sui' );
        SUI.editors[id].getSession().setMode( 'ace/mode/css' );
        if ( $(this).hasClass('ub_html_editor') ) {
            SUI.editors[id].getSession().setMode( 'ace/mode/html' );
        }
        SUI.editors[id].session.setTabSize(4);
        SUI.editors_value[id] = $( '#' + editor_id );
        SUI.editors[id].getSession().on('change', function () {
            SUI.editors_value[id].val(SUI.editors[id].getSession().getValue());
        });
        if ( '' !== $(this).attr( 'placeholder' ) ) {
            SUI.editors[id].on( 'input', function() {
                branda_ace_editor_placeholder( id, $(this).attr( 'placeholder' ) );
            });
            branda_ace_editor_placeholder( id, $(this).attr( 'placeholder' ) ); 
        }
        $(this).on( 'change', function() {
            $.fn.branda_flag_status( this );
        });
    });

    /**
     * ACE Editor selectors.
     *
     * Add selectors content to the editor.
     */
    jQuery( '.sui-ace-selectors .sui-selector' ).on( 'click', function(e) {
        e.preventDefault();
        var $this = $(this);
        // Editor id.
        var $editor_id = $( 'div.sui-ace-editor', $(this).closest( '.simple-option' ) ).attr( 'id' );
        // Editor.
        var $editor = SUI.editors[$editor_id];
        // Editor mode.
        var $mode = $editor.session.$modeId;
        // Get the selector data based on mode.
        var $selector = $mode === 'ace/mode/css' ? $this.data( "selector" ) + " {}" : $this.data( "selector" );
        // Add the selector data to the editor.
        $editor.insert( $selector );
        if ( $('#' + $editor_id).hasClass( 'ub_css_editor' ) ) {
            $editor.navigateLeft( 1 );
            $editor.insert( "\n" );
            $editor.navigateRight( 2 );
            $editor.insert( "\n" );
            $editor.navigateLeft( 3 );
        }
        $editor.focus();
    });
    /**
     * Handle SUI-tab
     */
    // Define global SUI object if it doesn't exist.
    if ( 'object' !== typeof window.SUI ) {
        window.SUI = {};
    }
    SUI.brandaSideTabs = function()  {
        $('.sui-side-tabs label.sui-tab-item input').on( 'click', function() {
            var $this      = $(this),
                $label     = $this.parent( 'label' ),
                $data      = $this.data( 'tab-menu' ),
                $wrapper   = $this.closest( '.sui-side-tabs' ),
                $alllabels = $( '.sui-tab-item', $(this).closest( '.sui-tabs-menu' ) ),
                $allinputs = $alllabels.find( 'input' ),
                $container = $wrapper.find('.sui-tabs-content').first()
            ;
            $alllabels.removeClass( 'active' );
            $allinputs.removeAttr( 'checked' );
            $container.find( '> div' ).removeClass( 'active' );
            $label.addClass( 'active' );
            $this.attr( 'checked', 'checked' );
            if ( $wrapper.find( '.sui-tabs-content div[data-tab-content="' + $data + '"]' ).length ) {
                $wrapper.find( '.sui-tabs-content div[data-tab-content="' + $data + '"]' ).addClass( 'active' );
            }
        });
    };
    SUI.brandaSideTabs();
    /**
     * SUI: save
     */
    $('.branda-module-save').on( 'click', function() {
        var form = $('[data-tab=' + $(this).closest('.sui-box').data('tab') + '] form');
        if ( 'undefined' !== typeof form ) {
            var error = false;
            $( 'input[type=number]', form ).each( function() {
                var val = $(this).val();
                if ( 'undefined' !== typeof val ) {
                    var min = $(this).attr( 'min' );
                    var max = $(this).attr( 'max' );
                    var $parent = $(this).closest( '.sui-form-field' );
                    $('.sui-error-message', $parent ).detach();
                    $parent.removeClass( 'sui-form-field-error' );
                    val = parseInt( val );
                    if ( 'undefined' !== typeof min ) {
                        min = parseInt( min );
                        if ( val < min ) {
                            $parent.addClass( 'sui-form-field-error' );
                            $parent.append( '<span class="sui-error-message">'+ub_admin.messages.form.number.min+'</span>' );
                            error = true;
                            return;
                        }
                    }
                    if ( 'undefined' !== typeof min ) {
                        max = parseInt( max );
                        if ( val > max ) {
                            $parent.addClass( 'sui-form-field-error' );
                            $parent.append( '<span class="sui-error-message">' + ub_admin.messages.form.number.max + '</span>' );
                            error = true;
                            return;
                        }
                    }
                }
            });
            if ( error ) {
                return;
            }
            form.submit();
        }
    });
    /**
     * reset whole module
     */
    $(' .branda-reset-module' ).on( 'click', function() {
        var dialog_id = 'branda-dialog-reset-module-' + $(this).data('module');
        if ( 'undefined' === typeof SUI.dialogs[ dialog_id ] ) {
            if ( window.confirm( ub_admin.messages.reset.module ) ) {
                var args = {
                    action: 'branda_reset_module',
                    module: $(this).data('module'),
                    _wpnonce: $(this).data('nonce')
                };
                $.post( ajaxurl, args, function( response ) {
                    if ( response.success ) {
                        window.location.reload();
                    } else {
                        window.ub_sui_notice( response.data.message, 'error' );
                    }
                });
            }
        } else {
            SUI.dialogs[ dialog_id ].show();
        }
        return false;
    });
    $( '.branda-dialog-reset-module button.branda-reset' ).on( 'click', function() {
        var args = {
            action: 'branda_reset_module',
            module: $(this).data('module'),
            _wpnonce: $(this).data('nonce')
        };
        $.post( ajaxurl, args, function( response ) {
            if ( response.success ) {
                window.location.reload();
            } else {
                window.ub_sui_notice( response.data.message, 'error' );
            }
        });
    });
    /**
     * show hide slaves
     */
    $( 'input[type=checkbox].master-field' ).on( 'change', function() {
        var slaves;
        var slave = $(this).data('slave');
        if ( slave ) {
            slaves = $( '.' + slave );
            if ( $(this).is(':checked') ) {
                slaves.show();
                return;
            }
            slaves.hide();
        }
    });
    /**
     * required fields
     */
    $('.sui-form-field [data-required=required]').on( 'change, paste, keyup', function() {
        if ( '' !== $(this).val() ) {
            var local_parent = $(this).parent();
            local_parent.removeClass('sui-form-field-error');
            $('span', local_parent ).removeClass( 'sui-error-message' );
        }
    });

    /**
     * Admin social media icons
     */
    $.each( $('.branda-social-logo-preview'), function() {
        var target = $(this);
        var source = $('.sui-tabs-menu input[type=radio]', target.parent() );
        var mode = false;
        source.each( function() {
            if ( $(this).is(':checked') ) {
                mode = $(this).val();
            }
            $(this).on( 'change', function() {
                if ( $(this).is(':checked' ) ) {
                    if ('color' === $(this).val() ) {
                        target.addClass( 'social-logo-color' );
                    } else {
                        target.removeClass( 'social-logo-color' );
                    }
                }
            });
        });
        if ( 'color' === mode ) {
            target.addClass( 'social-logo-color' );
        } else {
            target.removeClass( 'social-logo-color' );
        }
    });

    /**
     * social media
     */
    function branda_social_media_bind_delete() {
        $('button.branda-social-logo-remove' ).on( 'click', function() {
            var parent = $(this).closest('.sui-accordion-item-body');
            $( '.branda-social-logo-add-dialog-button', parent ).removeAttr( 'disabled' );
            var target = $('.branda-social-logo-add-dialog .branda-social-logo-li-'+$(this).data('id'), parent );
            target.removeClass( 'hidden' );
            $( 'input', target ).removeAttr( 'checked' );
            $(this).closest('.sui-form-field').detach();
        });
    }
    branda_social_media_bind_delete();

    $('.branda-social-logo-add-accounts' ).on( 'click', function() {
        var parent = $(this).closest('.sui-box');
        var items = $('li:not(.hidden) :checked', parent );
        var branda_template = wp.template( $(this).data( 'template' ) );
        items.each( function() {
            $('.branda-social-logos-main-container', $(this).closest( 'form' ) ).append(
                branda_template({
                    id: $(this).val(),
                    label: $(this).data('label')
                })
            );
            $(this).closest( 'li' ).addClass( 'hidden' );
        });
        branda_social_media_bind_delete();
        /**
         * check to disable button
         */
        if ( 0 === $( 'li', parent ).not('.hidden').length ) {
            $('.branda-social-logo-add-dialog-button', $(this).closest('.sui-accordion-item-body') ).attr( 'disabled', 'disabled' );
        }
        SUI.dialogs[ $(this).data( 'dialog' ) ].hide();
        /**
         * re-inicialize sortable
         */
        $('.branda-social-logos-main-container').sortable({
            items: '.simple-option'
        });
    });

    /**
     * add circle to canvas
     */
    function branda_move_focal( context, el, x, y ) {
        var canvas = el.get(0);
        var parent = el.closest( '.sui-form-field' );
        var nx = Math.round( 100 * x / canvas.width );
        var ny = Math.round( 100 * y / canvas.height );
        context.clearRect( 0, 0, canvas.width, canvas.height );
        context.beginPath();
        context.arc( x, y, 7, 0, 2 * Math.PI, false);
        context.fillStyle = $('.sui-wrap-branda').hasClass( 'sui-color-accessible' )? '#000':'#17A8E3';
        context.fill();
        context.lineWidth = 3;
        context.strokeStyle = '#fff';
        context.stroke();
        $( 'input.branda-focal-x', parent ).val( nx );
        $( 'input.branda-focal-y', parent ).val( ny );
        $( 'span.branda-focal-x', parent ).html( nx );
        $( 'span.branda-focal-y', parent ).html( ny );
        el.css( 'backgroundPosition', nx + '% ' + ny + '%' );
        return context;
    }
    $('canvas.branda-focal').each( function() {
        var el = $(this);
        var parent = el.closest( '.sui-form-field' );
        var canvas = el.get(0);
        var context = canvas.getContext('2d');
        var x = Math.round( canvas.width * $( 'input.branda-focal-x', parent ).val() / 100 );
        var y = Math.round( canvas.height * $( 'input.branda-focal-y', parent ).val() / 100 );
        context = branda_move_focal( context, el, x, y );
        canvas.addEventListener('mousedown', function(e) {
            context = branda_move_focal( context, el, e.offsetX, e.offsetY );
        }, true);
        $(this).css( 'backgroundImage', 'url('+$(this).data( 'background-image' )+')' );
    });

    /**
     * Social media: show inactive options
     */
    $( '.ub-radio.branda-social-media-show' ).on( 'click', function() {
        var elements = $('.simple-option-sui-tab', $(this).closest( '.sui-box-body' ) );
        if ( $(this).is(':checked' ) && 'on' === $(this).val() ) {
            elements.removeClass( 'branda-not-affected' );
            $('.sui-notice', $(this).closest( '.simple-option-sui-tab' ) ).hide();
        } else {
            var first = true;
            $('.sui-notice', $(this).closest( '.simple-option-sui-tab' ) ).show();
            $.each( elements, function() {
                if ( ! first ) {
                    $(this).addClass( 'branda-not-affected' );
                }
                first = false;
            });
        }
    });

    /**
     * Indicator
     */
    window.branda_has_changed = 'saved';
    window.onbeforeunload = function() {
        if ( 'unsaved' === window.branda_has_changed ) {
            return ub_admin.messages.unsaved;
        }
    };
    $( '.branda-module-save' ).on( 'click', function() {
        window.branda_has_changed = 'saving';
    });
    $( '.branda-settings-tab-content input, .branda-settings-tab-content textarea, .branda-settings-tab-content select' ).on( 'change', function() {
        $.fn.branda_flag_status( this );
    });

});

/* global window, jQuery, switch_button */

jQuery( window.document ).ready(function(){
    "use strict";
    if ( jQuery.fn.switchButton ) {
        var ultimate_branding_admin_check_slaves  = function() {
            jQuery('.simple-option .master-field' ).each( function() {
                var slave = jQuery(this).data('slave');
                if ( slave ) {
                    var slaves = jQuery( '.simple-option.' + slave );
                    var show = jQuery( '.switch-button-background', jQuery(this).closest('td') ).hasClass( 'checked' );
                    /**
                     * exception:
                     * module: Comments Control
                     */
                    if ( show && 'enabled-posts' === slave ) {
                        show = jQuery( '.switch-button-background', jQuery( '.simple-option .master-field[data-slave="enabled"]' ).closest( 'td' ) ).hasClass( 'checked' );
                    }
                    if ( show ) {
                        slaves.show();
                    } else {
                        slaves.hide();
                    }
                }
            });
        };
    }
});


/* global window, jQuery */
jQuery( window.document ).ready(function($){
    "use strict";
    $('.sui-wrap-branda .sui-sidenav .sui-vertical-tab a' ).on( 'click', function() {
        var container = $(this).closest('.sui-wrap-branda');
        var tabs = $('.sui-sidenav li', container );
        var tab = $(this).data('tab');
        var content, current;
        var url = window.location.href;
        var re = /module=[^&]+/;

        if ( 'undefined' === typeof tab ) {
            return;
        }
        if ( tab === window.branda_current_tab ) {
            return;
        }
        window.branda_current_tab = tab;
        content = $('.sui-box[data-tab]');
        current = $('.sui-box[data-tab="' + tab + '"]');
        if ( url.match( re ) ) {
            url = url.replace( re, 'module=' + tab );
        } else {
            url += '&module=' + tab;
        }
        window.history.pushState( { module: tab }, 'Branda', url );
        tabs.removeClass( 'current' );
        content.hide();
        $(this).parent().addClass( 'current' );
        current.show();
        return false;
    });
});

/* global wp, window, wp_media_post_id, jQuery, ajaxurl, SUI, tinyMCE */

function ub_sui_notice_hide( notice ) {
    if ( notice.hasClass( 'sui-can-dismiss' ) ) {
        return;

    }
    if ( notice.hasClass( 'sui-cant-dismiss' ) ) {
        return;
    }
    if ( notice.hasClass( 'sui-marked-to-hide' ) ) {
        return;
    }
    if (
        notice.parent().hasClass( 'sui-description' ) ||
        notice.parent().hasClass( 'simple-option' )
    ) {
        return;
    }
    /**
     * Preserve in dialogs
     */
    var dialog = jQuery(this).closest( '.dialog' );
    if ( 'object' === typeof dialog ) {
        return;
    }
    notice.addClass( 'sui-marked=to-hide' );
    window.setTimeout( function() {
        notice.fadeOut( 'slow' );
    }, 3000 );
}

function ub_sui_notice( message, notice_class ) {
    var m = jQuery('#ub-settings-notice');
    if ( 0 === m.length ) {
        return;
    }
    if ( "undefined" === typeof notice_class ) {
        notice_class = 'info';
    }
    m.removeClass( 'sui-marked-to-hide' );
    m.removeClass( 'sui-notice-info' );
    m.removeClass( 'sui-notice-error' );
    m.removeClass( 'sui-notice-success' );
    m.addClass( 'sui-notice-' + notice_class );
    if ( 'success' === notice_class ) {
        m.removeClass( 'sui-can-dismiss' );
        jQuery( '.sui-notice-dismiss', m ).hide();
    } else {
        m.addClass( 'sui-can-dismiss' );
        jQuery( '.sui-notice-dismiss', m ).css( { display: 'flex' } );
    }
    jQuery( '.sui-notice-content', m ).html( '<p>' + message + '</p>' );
    m.show();
    ub_sui_notice_hide( m );
}

/**
 * Handle activate/deactivate module
 */
jQuery( window.document ).ready( function($) {
    $( '.ub-activate-module, .ub-deactivate-module' ).on( 'click', function() {
        var data = {
            action: 'ultimate_branding_toggle_module',
            module: $(this).data('slug'),
            nonce: $(this).data('nonce'),
            state: $(this).hasClass('ub-activate-module')? 'on':'off'
        };
        jQuery.post(ajaxurl, data, function( response ) {
            if ( response.success ) {
                window.location.reload();
            } else if ( undefined !== typeof response.data.message ) {
                ub_sui_notice( response.data.message, 'error' );
            }
        });
        return false;
    });
});
/**
 * Manage all modules
 */
function branda_modules_mark_all( selector, parent ) {
    jQuery( selector, parent )
        .on( 'click', function() {
            jQuery(this).prop( 'indeterminate', false );
        }).on( 'change', function() {
            if ( jQuery(this).is(':checked' ) ) {
                jQuery('input[type=checkbox]', jQuery(this).closest( '.sui-col' ) ).attr( 'checked', 'checked' );
            } else {
                jQuery('input[type=checkbox]', jQuery(this).closest( '.sui-col' ) ).removeAttr( 'checked' );
            }
        });
    jQuery.each( jQuery( selector, parent ), function() {
        if ( ! jQuery( this ).is( ':checked' ) ) {
            jQuery( this ).prop( 'indeterminate', true );
        }
    });
}

jQuery( window.document ).ready( function($) {
    branda_modules_mark_all( '#branda-manage-all-modules .branda-group-checkbox, .branda-import-import .branda-group-checkbox', 'body' );
});
/**
 * Trigger tab change when mobile dropdown is changed.
 */
jQuery( window.document ).ready( function($) {
    $( '#branda-mobile-nav' ).change(function () {
        var tab = $(this).val();
        $('a[data-tab="' + tab + '"]').trigger('click');
    });
});
/**
 * save bulk "Manage All Modules".
 */
function branda_modules_save_bulk( selector, parent, is_welcome ) {
    jQuery( selector, parent ).on( 'click', function() {
        var branda = [];
        var button = jQuery(this);
        var dialog = button.closest('.sui-dialog');
        jQuery('input[type=checkbox]:checked', dialog ).each( function() {
            var value = jQuery(this).attr('name');
            if ( value ) {
                branda.push( value );
            }
        });
        if ( 0 === branda.length ) {
            if ( is_welcome ) {
                ub_sui_notice( window.ub_admin.messages.welcome.empty, 'warning' );
                return false;
            } else {
                branda = 'turn-off-all-modules';
            }
        }
        jQuery('.sui-loading-text', button ).hide();
        jQuery('.sui-loading', button ).show();
        button.prop('disabled', 'disabled' );
        var data = {
            action: 'branda_manage_all_modules',
            branda: branda,
            nonce: jQuery(this).data('nonce'),
        };
        jQuery.post(ajaxurl, data, function( response ) {
            if ( response.success ) {
                window.location.reload();
            } else if ( undefined !== typeof response.data.message ) {
                var notice_class = response.data.class || 'error';
                ub_sui_notice( response.data.message, notice_class );
                jQuery('.sui-loading-text', button ).show();
                jQuery('.sui-loading', button ).hide();
                button.removeProp( 'disabled' );
            }
        });
    });
}
jQuery( window.document ).ready( function($) {
    branda_modules_save_bulk( '#branda-manage-all-modules .sui-box-footer button.branda-save-all', 'body', false );
});
/**
 * Reset dialog content
 */
jQuery( window.document ).ready( function($) {
    $('.branda-dialog-reset').on( 'click', function() {
        var parent = $('.sui-box-body', $(this).closest( '.sui-box' ) );
        var id = $(this).data('id');
            $.each( $('input', parent ), function() {
                switch( $(this).attr('type' ) ) {
                    case 'radio':
                        if ( $(this).val() === $(this).data('default' ) ) {
                            $(this).attr( 'checked', 'checked' );
                            $(this).closest( 'label').trigger( 'click' ).addClass( 'active' );
                        } else {
                            $(this).removeAttr( 'checked' );
                            $(this).closest( 'label').trigger( 'click' ).removeClass( 'active' );
                        }
                        break;
                    default:
                        this.value = this.defaultValue;
                }
            });
            $.each( $('textarea', parent ), function() {
                this.value = this.defaultValue;
                tinyMCE.get( $(this).attr('id') ).setContent( this.defaultValue );
            });
            return false;
    });
});
/**
 * Copy Settings
 *
 * @since 3.0.0
 */
jQuery( window.document ).ready( function($) {
    function branda_copy_settings_check( dialog ) {
        var module_id = $('.branda-copy-settings-select option:selected', dialog ).val();
        var sections = $('.branda-copy-settings-' + module_id + ' .branda-copy-settings-section:checked');
        if ( 1 > sections.length ) {
            $('.branda-copy-settings-copy-button', dialog ).attr( 'disabled', 'disabled' );
        } else {
            $('.branda-copy-settings-copy-button', dialog ).removeAttr( 'disabled' );
        }
    }
    $('.branda-copy-settings-select').on( 'change', function() {
        var dialog = $(this).closest('.sui-dialog');
        var module_id = $(':selected', $(this) ).val();
        $('.branda-copy-settings-options', dialog ).hide();
        if ( '' !== module_id ) {
            $('.branda-copy-settings-copy-button', dialog ).attr( 'disabled', 'disabled' );
            $('.branda-copy-settings-' + module_id ).show();
        }
        branda_copy_settings_check( dialog );
    });
    $('.branda-copy-settings-section').on( 'change', function() {
        var dialog = $(this).closest('.sui-dialog');
        branda_copy_settings_check( dialog );
    });
    $('.branda-copy-settings-copy-button').on( 'click', function() {
        var dialog = $(this).closest('.sui-dialog');
        var module_id = $('.branda-copy-settings-select option:selected', dialog ).val();
        var data = {
            action: 'branda_module_copy_settings',
            target_module: $(this).data('module'),
            source_module: module_id,
            nonce: $(this).data('nonce'),
            sections: [],
        };
        var sections = $('.branda-copy-settings-' + module_id + ' .branda-copy-settings-section:checked');
        if ( 0 < sections.length ) {
            $.each( sections, function() {
                data.sections.push( $(this).val() );
            });
            jQuery.post(ajaxurl, data, function( response ) {
                if ( response.success ) {
                    window.location.reload();
                } else if ( undefined !== typeof response.data.message ) {
                    ub_sui_notice( response.data.message, 'error' );
                }
            });
        }
        return false;

    });
});
/**
 * Window scroll
 *
 * @since 3.0.0
 */
jQuery( window.document ).ready( function($) {
    var branda_top = 0;
    /**
     * Tabs
     */
    var branda_settings_tabs = $('.branda-settings-tab-title:visible');
    var branda_init_width = branda_settings_tabs.width();
    /**
     * Content
     */
    var branda_settings_vertical = $('.sui-vertical-tabs:visible');
    var branda_init_vertical_width = branda_settings_vertical.width();
    if ( 0 === branda_settings_tabs.length ) {
        return;
    }
    /**
     * title
     */
    branda_top = parseInt( branda_settings_tabs.offset().top );
    /**
     * Bind scroll
     */
    $( window ).on( 'scroll', function() {
        var scroll = parseInt( $(this).scrollTop() );
        var branda_top_offset = parseInt( $('#wpadminbar').height() );
        if ( scroll > branda_top ) {
            /**
             * title
             */
            branda_settings_tabs
                .addClass( 'branda-settings-tab-title-fixed' )
                .css( 'width', branda_init_width + 'px' )
                .css( 'top', branda_top_offset );
            /**
             * Submenu
             */
            branda_settings_vertical
                .addClass( 'branda-settings-vertical-tab-fixed' )
                .css( 'width', branda_init_vertical_width + 'px' )
                .css( 'top', branda_top_offset );
        } else {
            /**
             * title
             */
            branda_settings_tabs
                .removeClass( 'branda-settings-tab-title-fixed' )
                .css( 'top', 0 )
                .css( 'width', 'auto' );
            /**
             * Submenu
             */
            branda_settings_vertical
                .removeClass( 'branda-settings-vertical-tab-fixed' )
                .css( 'top', 0 )
                .css( 'width', 'auto' );
        }
    });
});
/**
 * Bulk delete button
 *
 * @since 3.0.0
 */
jQuery( window.document ).ready( function($) {
    $( 'input[type=checkbox].branda-cb-select-all' ).on( 'change', function() {
        var parent = $(this).closest( 'form' );
        if ( $(this).is(':checked') ) {
            $('.check-column input[type=checkbox]', parent ).prop( 'checked', 'checked' );
        } else {
            $('.check-column input[type=checkbox]', parent ).removeProp( 'checked' );
        }
    });
    $( '.branda-bulk-delete' ).on( 'click', function() {
        var parent = $(this).closest( 'form' );
        var action = $( 'select option:selected', parent ).val();
        if ( '-1' === action ) {
            return false;
        }
        if ( 0 === $( '.check-column input:checked', parent ).length ) {
            return false;
        }
        SUI.dialogs[ $(this).data('dialog') ].show();
    });
});
/**
 * Send test email
 *
 * @since 3.0.0
 */
jQuery( window.document ).ready( function($) {
    $( '.branda-test-email .sui-box-footer button' ).on( 'click', function() {
        var $button = $(this);
        var email = $('input[type=email]', $(this).closest('.sui-box') );
        var data = {
            action: $(this).data('action'),
            _wpnonce: $(this).data('nonce'),
            email: email.val()
        };
        if ( '' === email.val() ) {
            email.parent().addClass( 'sui-form-field-error' );
            $('span', email.parent()).addClass('sui-error-message');
            return;
        }
        $('span', $button ).hide();
        $('i', $button ).show();
        $button.addClass( 'sui-button-ghost' ).prop( 'disabled', 'disabled' );
        $.post( ajaxurl, data, function( response ) {
            if ( response.success ) {
                window.location.reload();
            } else {
                $('span', $button ).show();
                $('i', $button ).hide();
                $button.removeClass( 'sui-button-ghost' ).removeProp( 'disabled' );
                window.ub_sui_notice( response.data.message, 'error' );
            }
        });
    });
});
/**
 * Change animation on manage all modules modal
 *
 * @since 3.0.0
 */
jQuery( window ).bind( 'load', function( $ ) {
    // Change animation on the show event
    SUI.dialogs['branda-manage-all-modules'].on( 'show', function( dialogEl, event ) {
        var content = dialogEl.getElementsByClassName( 'sui-dialog-content' );
        content[0].className = 'sui-dialog-content sui-fade-in';
    });

    // Change animation on the hide event
    SUI.dialogs['branda-manage-all-modules'].on( 'hide', function( dialogEl, event ) {
        var content = dialogEl.getElementsByClassName( 'sui-dialog-content' );
        content[0].className = 'sui-dialog-content sui-fade-out';
    });
});


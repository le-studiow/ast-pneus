;(function ($) {

	var redirect_selector = '.wds-redirects-container',
		new_redirect_form_id = 'wds-add-redirect-form',
		edit_redirect_form_id = 'wds-edit-redirect-form',
		bulk_update_form_id = 'wds-bulk-update-redirects',
		_templates;

	var show_message = function (id) {
		Wds.show_floating_message(window.Wds.l10n('redirects', id));
	};

	var handle_bulk_checkbox_click = function () {
		var $bulk_checkbox = $(this),
			is_checked = $bulk_checkbox.is(":checked"),
			$redirects = $(redirect_selector),
			$targets = $(':checkbox[name*="bulk"]', $redirects);

		if ($targets.length) {
			$targets.attr("checked", is_checked);
		}

		change_bulk_action_button_status();
	};

	var remove_redirect_item = function (e) {
		e.preventDefault();

		var $remove_button = $(this),
			$redirects = $(redirect_selector),
			$message = $('.wds-redirects-unsaved-notice', $redirects);

		$remove_button.closest('.wds-redirect-item').remove();
		$message.show();
		update_select_all_checkbox();
		change_bulk_action_button_status();
		show_message('redirect_removed');
	};

	var open_new_redirect_form = function (e) {
		e.preventDefault();

		var overlay_id = new_redirect_form_id,
			overlay_selector = '#' + overlay_id,
			add_form = _templates.add_form({});

		$(overlay_selector).replaceWith(add_form);
		Wds.open_dialog(overlay_id);
		hook_select2($('select', $(overlay_selector)));
	};

	var edit_redirect = function (e) {
		e.preventDefault();

		var $dialog = $('#' + edit_redirect_form_id);
		if (!validate_fields($dialog)) {
			return;
		}

		do_update_redirect(
			$('.wds-redirect-index', $dialog).val(),
			$('.wds-source-url', $dialog).val(),
			$('.wds-destination-url', $dialog).val(),
			$('select.wds-redirect-type', $dialog).val()
		);

		Wds.close_dialog();
		update_select_all_checkbox();
		show_message('redirect_updated');
	};

	var do_update_redirect = function (index, source, destination, type) {
		var $redirects = $(redirect_selector),
			$redirect_item = $('[data-index="' + index + '"]', $redirects),
			updated_item = _templates.redirect_item({
				source: source,
				destination: destination,
				selected_type: type,
				index: index
			});

		$redirect_item.replaceWith(updated_item);
	};

	var add_new_redirect = function (e) {
		e.preventDefault();

		var $redirects = $(redirect_selector),
			$dialog = $('#' + new_redirect_form_id),
			index = $('.wds-redirect-item', $redirects).length,
			new_item = _templates.redirect_item({
				source: $('.wds-source-url', $dialog).val(),
				destination: $('.wds-destination-url', $dialog).val(),
				selected_type: $('select.wds-redirect-type', $dialog).val(),
				index: index
			});

		if (!validate_fields($dialog)) {
			return;
		}

		$('.wds-redirects', $redirects).append(new_item);

		Wds.close_dialog();
		update_select_all_checkbox();
		show_message('redirect_added');
		$('.wds-no-redirects').removeClass('wds-no-redirects');
	};

	var bulk_remove = function (e) {
		e.preventDefault();

		var $redirects = $(redirect_selector),
			$message = $('.wds-redirects-unsaved-notice', $redirects);

		$('.wds-redirect-item input[type="checkbox"]:checked', $redirects).each(function () {
			var $checkbox = $(this);
			$checkbox.closest('.wds-redirect-item').remove();
		});

		$message.show();
		update_select_all_checkbox();
		change_bulk_action_button_status();
		show_message('redirects_removed');
	};

	var bulk_update_redirects = function (e) {
		e.preventDefault();

		var $redirects = $(redirect_selector),
			$dialog = $('#' + bulk_update_form_id),
			indices = $('input[type="hidden"]', $dialog).val().split(','),
			new_destination_url = $('input[type="text"]', $dialog).val(),
			new_type = $('select', $dialog).val(),
			$checkboxes = $('input[type="checkbox"]', $redirects);

		$.each(indices, function (index, value) {
			var $redirect_item = $('[data-index="' + value + '"]', $redirects),
				destination_url = new_destination_url === ''
					? $redirect_item.find('.wds-destination-url').val()
					: new_destination_url,
				source_url = $('.wds-source-url', $redirect_item).val();

			do_update_redirect(value, source_url, destination_url, new_type);
		});

		$checkboxes.prop('checked', false);

		Wds.close_dialog();
		change_bulk_action_button_status();
		show_message('redirects_updated');
	};

	var validate_fields = function ($container) {
		var is_valid = true;
		$('.sui-form-field', $container).each(function () {
			var $form_field = $(this),
				$input = $('input', $form_field);

			if ($input.length && !$input.val()) {
				is_valid = false;
				$form_field.addClass('sui-form-field-error');

				$input.on('focus keydown', function () {
					$(this).closest('.sui-form-field-error').removeClass('sui-form-field-error');
				});
			}
		});

		return is_valid;
	};

	var open_edit_redirect_form = function (e) {
		e.preventDefault();

		var $redirect_item = $(e.target).closest('[data-index]'),
			overlay_id = edit_redirect_form_id,
			overlay_selector = '#' + overlay_id,
			edit_form = _templates.edit_form({
				source: $('.wds-source-url', $redirect_item).val(),
				destination: $('.wds-destination-url', $redirect_item).val(),
				selected_type: $('.wds-redirect-type', $redirect_item).val(),
				index: $redirect_item.data('index')
			});

		$(overlay_selector).replaceWith(edit_form);
		Wds.open_dialog(overlay_id);
		hook_select2($('select', $(overlay_selector)));
	};

	var open_bulk_update_form = function (e) {
		e.preventDefault();

		var overlay_id = bulk_update_form_id,
			overlay_selector = '#' + overlay_id,
			indices = [];

		get_checked_boxes().each(function () {
			var $checkbox = $(this),
				index = $checkbox.closest('[data-index]').data('index');

			indices.push(index);
		});

		var updated_form = _templates.update_form({
			indices: indices.join(',')
		});

		$(overlay_selector).replaceWith(updated_form);
		Wds.open_dialog(overlay_id);

		hook_select2($('select', $(overlay_selector)));
	};

	var get_checked_boxes = function () {
		var $redirects = $(redirect_selector);

		return $('.wds-redirect-item input[type="checkbox"]:checked', $redirects);
	};

	function change_bulk_action_button_status() {
		var $redirects = $(redirect_selector),
			$checked_checkboxes = get_checked_boxes(),
			$bulk_action_buttons = $('.wds-redirect-controls button', $redirects);

		$bulk_action_buttons.prop('disabled', $checked_checkboxes.length <= 0);
	}

	function update_select_all_checkbox() {
		var $redirects = $(redirect_selector),
			$check_all = $('.wds-redirect-controls input[type="checkbox"]', $redirects),
			$all_checkboxes = $('.wds-redirect-item input[type="checkbox"]', $redirects),
			$checked_checkboxes = get_checked_boxes();

		if (!$check_all.is(':checked') && $all_checkboxes.length === $checked_checkboxes.length) {
			$check_all.prop('checked', true);
		}

		if (
			$check_all.is(':checked')
			&& (
				$all_checkboxes.length > $checked_checkboxes.length
				|| ($all_checkboxes.length === 0 && $checked_checkboxes.length === 0)
			)
		) {
			$check_all.prop('checked', false);
		}
	}

	var handle_checkbox_change = function (e) {
		e.preventDefault();

		update_select_all_checkbox();
		change_bulk_action_button_status();
	};

	function hook_select2($select) {
		if (SUI && SUI.suiSelect) {
			SUI.suiSelect($select);
		}
	}

	$(function () {
		var bulk_update_form_selector = '#' + bulk_update_form_id,
			new_redirect_form_selector = '#' + new_redirect_form_id,
			edit_redirect_form_selector = '#' + edit_redirect_form_id;

		$(redirect_selector)
			.on('click', '.wds-redirect-controls input', handle_bulk_checkbox_click)
			.on('change', '.wds-redirect-item input[type="checkbox"]', handle_checkbox_change)

			.on('click', 'a[href="#remove"]', remove_redirect_item)
			.on('click', '.wds-bulk-remove', bulk_remove)

			.on('click', 'a[href="#edit"]', open_edit_redirect_form)
			.on('click', edit_redirect_form_selector + ' .wds-action-button', edit_redirect)

			.on('click', '.wds-add-redirect', open_new_redirect_form)
			.on('click', new_redirect_form_selector + ' .wds-action-button', add_new_redirect)

			.on('click', '.wds-bulk-update', open_bulk_update_form)
			.on('click', bulk_update_form_selector + ' .wds-action-button', bulk_update_redirects);

		_templates = {
			redirect_item: Wds.tpl_compile(Wds.template('redirects', 'redirect-item')),
			edit_form: Wds.tpl_compile(Wds.template('redirects', 'edit-form')),
			add_form: Wds.tpl_compile(Wds.template('redirects', 'add-form')),
			update_form: Wds.tpl_compile(Wds.template('redirects', 'update-form'))
		};
	});

})(jQuery);

;(function ($) {

	function submit_dialog_form_on_enter(e) {
		var $button = $(this).find('.wds-action-button'),
			key = e.which;

		if ($button.length && 13 === key) {
			e.preventDefault();
			e.stopPropagation();

			$button.click();
		}
	}

	function validate_moz_form(e) {
		var is_valid = true,
			$form = $(this),
			$submit_button = $('button[type="submit"]', $form);

		$('.sui-form-field', $form).each(function () {
			var $form_field = $(this),
				$input = $('input', $form_field);

			if (!$input.val().trim()) {
				is_valid = false;
				$form_field.addClass('sui-form-field-error');

				$input.on('focus keydown', function () {
					$(this).closest('.sui-form-field-error').removeClass('sui-form-field-error');
				});
			}
		});

		if (is_valid) {
			$submit_button.addClass('sui-button-onload');
		} else {
			$submit_button.removeClass('sui-button-onload');
			e.preventDefault();
		}
	}

	$(function () {
		$(".box-autolinks-custom-keywords-settings").each(function () {
			window.Wds.Keywords.custom_pairs($(this));
		});
		$("#ignorepost").closest(".wds-excluded-posts").each(function () {
			window.Wds.Postlist.exclude($(this));
		});

		$('.wds-vertical-tabs').on('wds_vertical_tabs:tab_change', function (event, active_tab) {
			$(active_tab)
				.find('.wds-vertical-tab-section')
				.removeClass('hidden');
		});

		$(document)
			.on('submit', '.wds-moz-form', validate_moz_form)
			.on('keydown', '.sui-dialog', submit_dialog_form_on_enter);

		window.Wds.upsell();
		window.Wds.link_dropdown();
		window.Wds.accordion();
		window.Wds.vertical_tabs();
	});

})(jQuery);

(function ($) {
	window.Wds = window.Wds || {};

	function add_custom_meta_tag_field() {
		var $this = $(this),
			$container = $this.closest('.wds-custom-meta-tags'),
			$new_input = $container.find('.wds-custom-meta-tag:first-of-type').clone();

		$new_input.insertBefore($this);
		$new_input.find('input').val('').focus();
	}

	function update_toggles() {
		var $sitewide_toggle = $('[name="wds_settings_options[wds_sitewide_mode]"]');

		$('[data-prereq]').each(function () {
			var $checkbox = $(this).find('input[type="checkbox"]'),
				prereq = $checkbox.closest('[data-prereq]').data('prereq'),
				$prereq_checkbox = $('[name="wds_settings_options[' + prereq + ']"]');

			$checkbox.prop('disabled', false);
			if (
				$sitewide_toggle.is(':checked')
				|| ($prereq_checkbox.length && !$prereq_checkbox.is(':checked'))
			) {
				$checkbox.prop('disabled', true);
				$checkbox.prop('checked', false);
			}

			$prereq_checkbox.off('change', update_toggles).on('change', update_toggles);
		});

		$sitewide_toggle.off('change', update_toggles).on('change', update_toggles);
	}

	function init() {
		window.Wds.styleable_file_input();
		$(document).on('click', '.wds-custom-meta-tags button', add_custom_meta_tag_field);

		update_toggles();
		Wds.vertical_tabs();
	}

	$(init);
})(jQuery);

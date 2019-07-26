(function ($, undefined) {
	window.Wds = window.Wds || {};

	function reload_box(box_id) {
		return $.post(ajaxurl, {
			action: 'wds-reload-box',
			box_id: box_id,
			_wds_nonce: _wds_dashboard.nonce
		}, function (data) {
			if ((data || {}).success) {
				if (!$.isArray(box_id)) {
					box_id = [box_id];
				}

				$.each(box_id, function (index, value) {
					var $box = $('#' + value);

					if ($box.length && data[value]) {
						$box.replaceWith(data[value]);
					}
				});
			}
		}, 'json').always(function () {
			update_page_status();
			load_accordions();
		});
	}

	function activate_component(e) {
		e.preventDefault();
		var $button = $(this),
			$box = $button.closest('.wds-dashboard-widget'),
			box_id = $box.attr('id');

		before_ajax_request($button);

		$.post(ajaxurl, {
			action: 'wds-activate-component',
			option: $button.data('optionId'),
			flag: $button.data('flag'),
			_wds_nonce: _wds_dashboard.nonce
		}, function (data) {
			if ((data || {}).success) {
				reload_box_and_dependents(box_id);
			}
		}, 'json');
	}

	function box_exists(box_id) {
		return !!$('#' + box_id).length;
	}

	function reload_box_and_dependents(box_id) {
		var $box = $('#' + box_id),
			dependent = $box.data('dependent'),
			reload_boxes = [box_id];

		if (dependent) {
			var dependents = dependent.split(';');
			reload_boxes = reload_boxes.concat(dependents);
		}

		return reload_box(
			_.filter(reload_boxes, box_exists)
		);
	}

	function update_page_status() {
		$('.wds-disabled-during-request').prop('disabled', false);
		$('.sui-button-onload').removeClass('sui-button-onload');
	}

	function before_ajax_request($target_element) {
		if (!$target_element.is('.sui-button-onload')) {
			$target_element.addClass('sui-button-onload');
			$('.wds-disabled-during-request').prop('disabled', true);
		}
	}

	function reload_boxes() {
		var $boxes_requiring_refresh = $('.wds-box-refresh-required'),
			box_ids = [];

		if ($boxes_requiring_refresh.length) {
			$.each($boxes_requiring_refresh, function () {
				var $box = $(this).closest('.wds-dashboard-widget'),
					box_id = $box.attr('id');

				if (!box_ids.includes(box_id)) {
					box_ids.push(box_id);
				}
			});

			reload_box(box_ids);
		}

		setTimeout(reload_boxes, 20000);
	}

	function load_accordions() {
		$('.wds-page .sui-accordion').each(function () {
			SUI.suiAccordion(this);
		});
	}

	function checkup_accordion_item_click(event) {
		event.preventDefault();
		event.stopPropagation();

		var checkup_page = _wds_dashboard.full_checkup_url,
			checkup_item = $(event.target).closest('.wds-check-item');

		if (checkup_item.length && checkup_page) {
			window.location.href = checkup_page + '&check=' + checkup_item.attr('id');
		}
	}

	function get_checkup_box_el() {
		return $('#wds-seo-checkup');
	}

	function update_checkup_progress() {
		var $checkup_box = get_checkup_box_el(),
			$progress_bar = $('.wds-progress', $checkup_box);

		if (!$progress_bar.length) {
			return;
		}

		window.Wds.update_checkup_progress(
			$progress_bar,
			function () {
				reload_box_and_dependents($checkup_box.attr('id'))
					.then(hook_checkup_accordion_item_click);
			}
		);
	}

	function hook_checkup_accordion_item_click() {
		$('div.sui-accordion-item-header', get_checkup_box_el())
			.off('click')
			.on('click', checkup_accordion_item_click);
	}

	function init() {
		reload_boxes();
		load_accordions();

		window.Wds.upsell();
		window.Wds.accordion();
		window.Wds.dismissible_message();

		$(document)
			.on('ready', update_checkup_progress)
			.on('click', '.wds-activate-component', activate_component);

		hook_checkup_accordion_item_click();
	}

	$(init);
})(jQuery);

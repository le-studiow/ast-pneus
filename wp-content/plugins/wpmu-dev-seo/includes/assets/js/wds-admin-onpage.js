;(function ($) {

	var selectors = {
		title_field: ':text[name*="[title-"]',
		desc_field: 'textarea[name*="[metadesc-"]',
		preview: '.wds-preview-container'
	};

	/**
	 * Wraps a raw notice string with appropriate markup
	 *
	 * @param {String} str Raw notice
	 *
	 * @return {String} Notice markup
	 */
	function to_warning_string(str) {
		if (!str) return '';
		return '<div class="wds-notice sui-notice sui-notice-warning">' +
			'<p>' + str + '</p>' +
			'</div>';
	}

	/**
	 * Handles tab switching title&meta preview update dispatch
	 */
	function tab_preview_change() {
		var $tab_section = $(".wds-vertical-tab-section:visible"),
			$accordion = $('.sui-accordion', $tab_section),
			trigger_change = function ($container) {
				var $text = $container.find(selectors.title_field),
					$preview = $container.find(selectors.preview);

				if ($text.length && $preview.data('showingDefault')) {
					render_preview_change.apply($text.get(), arguments);
				}
			};

		if ($accordion.length) {
			$accordion.find('.sui-accordion-item').each(function () {
				trigger_change($(this));
			});
		} else if ($('[data-type="static-homepage"]', $tab_section).length) {
			load_static_homepage_preview();
		} else {
			trigger_change($tab_section);
		}
	}

	function load_static_homepage_preview() {
		var $container = $('[data-type="static-homepage"]'),
			$preview = $container.find(".wds-preview-container");

		if (!$preview.data('showingDefault')) {
			return;
		}

		$preview.addClass("wds-preview-loading");
		$.post(ajaxurl, {
			action: "wds-onpage-preview",
			type: $container.data("type"),
			_wds_nonce: _wds_onpage.nonce
		}, 'json').done(function (rsp) {
			var status = (rsp || {}).status || false,
				html = (rsp || {}).markup || false;

			if (status && !!html) {
				$preview.replaceWith(html);
			}
		}).always(function () {
			$preview.removeClass("wds-preview-loading");
		});
	}

	/**
	 * Handles change/keyup event title&meta preview update dispatch
	 */
	function render_preview_change() {
		var $target_field = $(this),
			$container = $target_field.closest('[data-type]'),
			$preview = $container.find(selectors.preview),
			$title = $container.find(selectors.title_field),
			$meta = $container.find(selectors.desc_field);

		if ($title.length > 1 || $meta.length > 1) {
			return;
		}

		$preview.addClass("wds-preview-loading");

		return $.post(ajaxurl, {
			action: "wds-onpage-preview",
			type: $container.data("type"),
			title: $title.val(),
			description: $meta.val(),
			_wds_nonce: _wds_onpage.nonce
		}, 'json')
			.done(function (rsp) {
				var status = (rsp || {}).status || false,
					html = (rsp || {}).markup || false,
					warnings = (rsp || {}).warnings || {}
				;

				if (status && !!html) {
					$preview.replaceWith(html);
				}

				$container.find(".wds-notice").remove();

				if ((warnings || {}).title) {
					$title.after(to_warning_string(warnings.title));
				}
				if ((warnings || {}).description) {
					$meta.after(to_warning_string(warnings.description));
				}
			})
			.always(function () {
				$preview.removeClass("wds-preview-loading");
			});
	}

	function toggle_archive_status() {
		var $checkbox = $(this),
			$accordion_section = $checkbox.closest('.sui-accordion-item'),
			disabled_class = 'sui-accordion-item--disabled',
			open_class = 'sui-accordion-item--open';

		if (!$checkbox.is(':checked')) {
			$accordion_section.removeClass(open_class).addClass(disabled_class);
		}
		else {
			$accordion_section.removeClass(disabled_class);
		}
	}

	function init_onpage() {
		$(document).on("input propertychange", ":text, textarea", _.debounce(render_preview_change, 1000));
		$(document).on("wds_vertical_tabs:tab_change", ".wds-vertical-tabs", tab_preview_change);

		// Also update on init, because of potential hash change
		window.Wds.macro_dropdown();
		window.Wds.vertical_tabs();

		var $tab_status_checkboxes = $('.sui-accordion-item-header input[type="checkbox"]');
		$tab_status_checkboxes.each(function () {
			toggle_archive_status.apply($(this));
		});
		$tab_status_checkboxes.change(toggle_archive_status);
	}

	function handle_accordion_item_click() {
		var $accordion_item = $(this).closest('.sui-accordion-item');

		// Keep one section open at a time
		$('.sui-accordion-item--open').not($accordion_item).removeClass('sui-accordion-item--open');
	}

	function init() {
		if ($("body").is(".smartcrawl_page_wds_onpage")) init_onpage();
		$('.sui-accordion-item-header')
			.off('click.sui.accordion')
			.on('click.sui.accordion', handle_accordion_item_click);
		Wds.floating_message();
	}

	// Boot
	$(init);

})(jQuery);

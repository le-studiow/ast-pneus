(function ($) {
	function refresh_preview() {
		var title = $('#wds_title').val(),
			description = $('#wds_metadesc').val(),
			term_id = $('[name="tag_ID"]').val(),
			$preview_container = $('.wds-preview-container');

		if (!$preview_container.length) {
			return;
		}

		$preview_container.addClass('wds-preview-loading');
		$.post(ajaxurl, {
			action: "wds-term-form-preview",
			wds_title: title,
			wds_description: description,
			term_id: term_id,
			_wds_nonce: _wds_term_form.nonce
		}, 'json').done(function (data) {
			if ((data || {}).success) {
				$('.wds-metabox-preview').replaceWith(
					$((data || {}).markup)
				);
			}
		}).always(function () {
			$('.wds-preview-container').removeClass("wds-preview-loading");
		});
	}

	function init() {
		Wds.hook_toggleables();
		Wds.side_tabs();

		$(document)
			.on('ready', refresh_preview)
			.on('input propertychange', '.wds-meta-field', _.debounce(refresh_preview, 2000));
	}

	$(init);
})(jQuery);

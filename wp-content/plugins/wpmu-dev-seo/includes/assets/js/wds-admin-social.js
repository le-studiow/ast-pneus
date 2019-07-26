(function ($) {
	window.Wds = window.Wds || {};

	function init() {
		window.Wds.hook_conditionals();
		window.Wds.hook_toggleables();
		window.Wds.media_url($('.wds-media-url'));
		window.Wds.vertical_tabs();
		window.Wds.side_tabs();

		$('.sui-side-tabs').on('wds_side_tabs:tab_change', function (event, tab) {
			$('#twitter-card-type').val($(tab).data('cardType')).trigger('change');
		});
	}

	$(init);
})(jQuery);

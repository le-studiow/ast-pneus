;(function ($, undefined) {

	window.Wds = window.Wds || {};

	window.Wds.OgImage = function ($root) {

		var idx = $root.data('name'),
			singular = $root.data('singular') || false,
			$plus_sign = $root.find('a[href="#add"]').closest('.add-action-wrapper');

		var template = '<div class="og-image item">' +
			'<img src="{{= url }}" />' +
			'<input type="hidden" value="{{= url }}" name="{{= name }}" />' +
			'<a href="#remove" class="remove-action"><i class="sui-icon-close" aria-hidden="true"></i></a>' +
		'</div>';

		var init = function () {
			if (!(wp || {}).media) {
				// No media to use
				$("td.og-images").closest("tr").remove();
				return false;
			}

			wp.media.frames.wds_ogimg = wp.media.frames.wds_ogimg || {};
			wp.media.frames.wds_ogimg[idx] = wp.media.frames.wds_ogimg[idx] || new wp.media({
				multiple: false,
				library: {type: 'image'}
			});
			$root.find('a[href="#add"]').off('click').on('click', function (e) {
				if (e && e.stopPropagation) e.stopPropagation();
				if (e && e.preventDefault) e.preventDefault();

				wp.media.frames.wds_ogimg[idx].open();

				return false;
			});
			wp.media.frames.wds_ogimg[idx].off('select').on('select', add_handler);

			if(singular && $root.find("input:text").length) {
				$plus_sign.hide();
			}

			$root.find("input:text").each(function () {
				$(this).replaceWith(Wds.tpl_compile(template)({
					url: $(this).val(),
					name: $root.data("name") + '[]'
				}));
			});

			$root.on("click", 'a[href="#remove"]', remove_handler);
		};

		var remove_handler = function (e) {
			if (e && e.stopPropagation) e.stopPropagation();
			if (e && e.preventDefault) e.preventDefault();

			$(this).closest(".og-image.item").remove();
			if(singular) {
				$plus_sign.show();
			}

			return false;
		};

		var add_handler = function () {
			var selection = wp.media.frames.wds_ogimg[idx].state().get('selection'),
				url
			;
			if (!selection) return false;

			selection.each(function (model) {
				url = model.get("url");
			});

			if (!url) return false;
			$root.append(Wds.tpl_compile(template)({
				url: url,
				name: $root.data("name") + '[]'
			}));
			if(singular) {
				$plus_sign.hide();
			}
		};

		idx = idx || 0;
		init();
	};

	function init () {
		$(".og-images").each(function (idx, el) {
			var imgs = new Wds.OgImage($(el));
		});

		window.Wds.hook_toggleables();
	}

	$(init);

})(jQuery);

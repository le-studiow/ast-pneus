;(function ($) {

	var _posts = [],
		_errors = []
	;

	function process_posts () {
		if (!_posts.length) {
			return false;
		}

		var pid = _posts.shift();
		if (!pid) {
			return false;
		}

		return process_post(pid).always(function () {
			setTimeout(process_posts);
		});
	}

	function process_post (pid, forced) {
		forced = !!forced;
		var $row = $(":checkbox[name='post[]'][value='" + pid + "']").closest('tr'),
			$seo = $row.find('td.seo.column-seo'),
			$rdb = $row.find('td.readability.column-readability'),
			action = 'wds-analysis-' + (forced ? 'recheck' : 'get-markup'),
			already_requested = _errors.indexOf(pid) >= 0,
			handle_error = function () {
				var in_queue = _posts.indexOf(pid) >= 0 ;
				if (!forced && !already_requested) {
					_errors.push(pid);
				}
				if (!forced && !in_queue && !already_requested) {
					_posts.push(pid);
				}
				if (forced || already_requested) {
					console.log(forced, already_requested);
					$seo.add($rdb).html(get_error_msg());
				}
			}
		;

		$row.find('.wds-analysis').qtip('destroy', true);

		if (!forced && !already_requested && !$seo.find(".wds-status-invalid").length && !$rdb.find(".wds-status-invalid").length) {
			var dfr = $.Deferred();
			setTimeout(dfr.resolve);
			return dfr.promise();
		}
		$seo.add($rdb).html('<div class="wds-analysis-checking"><span>' + Wds.l10n('analysis', 'Checking') + '</span></div>');
		return $.post(ajaxurl, {
			action: action,
			post_id: pid,
			_wds_nonce: _wds_analysis.nonce
		})
			.done(function (rsp) {
				if (!(rsp || {}).success) {
					handle_error();
					return false;
				}

				$seo.html(((rsp || {}).data || {}).seo || get_error_msg())
				$rdb.html(((rsp || {}).data || {}).readability || get_error_msg())

				create_qtips(
					$row.find('.wds-analysis')
				);
			})
			.error(function () {
				handle_error();
			})
		;
	}

	function get_error_msg () {
		return '<div class="wds-analysis wds-status-error"><span>' +
			Wds.l10n('analysis', 'Error') +
		'</span></div>';
	}

	function create_qtips($element) {
		$element.each(function () {
			var $this = $(this);

			$this.qtip({
				style: {
					classes: 'wds-qtip qtip-rounded'
				},
				position: {
					my: 'top center',
					at: 'bottom center'
				},
				content: {
					text: $this.next('.wds-analysis-details')
				}
			});
		});
	}

	function init () {
		var $posts = $(":checkbox[name='post[]']");
		$posts.each(function () {
			var $me = $(this),
				pid = $me.val()
			;
			if (!pid) return true;
			_posts.push(pid);
		});

		$(document).on('click', '.wds-analysis', function (e) {
			if (e && e.preventDefault) e.preventDefault();
			if (e && e.stopPropagation) e.stopPropagation();

			var $row = $(this).closest('tr'),
				pid = $row.find(':checkbox[name="post[]"]').val()
			;
			if (!pid) return false;

			process_post(pid, true);

			return false;
		});

		process_posts();
		create_qtips(
			$('.column-seo .wds-analysis, .column-readability .wds-analysis')
		);
	}

	return $(init);

})(jQuery);

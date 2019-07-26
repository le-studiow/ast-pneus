;(function ($, undefined) {

	Wds.Onboard = Wds.Onboard || {
		dialog: false,
		get_dialog: function () {
			Wds.Onboard.dialog = Wds.Onboard.dialog || (new A11yDialog((Wds.Onboard.get_root())[0]));
			return Wds.Onboard.dialog;
		},
		open_dialog: function () {
			setTimeout(function () {
				if (Wds.Onboard.get_root().length) {
					Wds.Onboard.get_dialog().show();
				}
			});
		},
		get_box_content: function () {
			return Wds.Onboard.get_root().find('.sui-box-body');
		},
		get_box_footer: function () {
			return Wds.Onboard.get_root().find('.sui-box-footer');
		},
		get_root: function () {
			return $("#wds-onboarding");
		},
		get_checks: function () {
			return Wds.Onboard.get_root().find(":checkbox");
		},
		process_all: function () {
			var $checks = Wds.Onboard.get_checks();
			Wds.Onboard.get_root().addClass('wds-onboarding-in-progress');
			Wds.Onboard.get_box_footer().hide();
			Wds.Onboard.get_box_content().html(
				Wds.template('onboard', 'progress')
			);
			Wds.Onboard.process_next($checks.toArray(), $checks.length);
		},
		process_next: function ($items, total, processed) {
			if (!$items.length) {
				Wds.Onboard.get_box_content().find(".sui-progress-state").text(Wds.l10n('onboard', 'All done'));
				window.location.reload();
				return false;
			}

			var $item = $($items.pop()),
				processed = processed || 0,
				pct = 0,
				dfr = $.Deferred()
			;
			processed++;
			pct = (processed / total) * 100;
			Wds.update_progress_bar(Wds.Onboard.get_box_content().find(".wds-progress"), pct);

			Wds.Onboard.get_box_content().find(".sui-progress-state").text(
				$item.attr("data-processing")
			);
			if ($item.is(":checked")) {
				$.post(ajaxurl, {
					action: "wds-boarding-toggle",
					target: $item.attr("name"),
					_wds_nonce: _wds_onboard.nonce
				}).always(dfr.resolve);
			} else setTimeout(dfr.resolve);

			dfr.done(function () {
				Wds.Onboard.process_next($items, total, processed);
			});
		},
		skip: function () {
			$(this).html('&hellip;');
			$.post(ajaxurl, {
				action: "wds-boarding-skip"
			}).always(function () {
				Wds.Onboard.get_dialog().hide();
			});
		},
		toggle_enabled_actions: function () {
			var $all = Wds.Onboard.get_checks(),
				$button = $("button.wds-onboarding-setup"),
				enabled = false
			;
			$all.each(function () {
				if (!$(this).is(":checked")) return true;
				enabled = true;
				return false;
			});
			if (enabled) {
				$button
					.attr("disabled", false)
					.removeClass("disabled")
				;
			} else {
				$button
					.attr("disabled", true)
					.addClass("disabled")
				;
			}
		}
	};

	$(document).on("click", "button.wds-onboarding-setup", Wds.Onboard.process_all);
	$(document).on("click", "button.onboard-skip", Wds.Onboard.skip);
	$(document).on("change", ".wds-onboarding-dialog :checkbox", Wds.Onboard.toggle_enabled_actions);
	$(Wds.Onboard.open_dialog);

})(jQuery);

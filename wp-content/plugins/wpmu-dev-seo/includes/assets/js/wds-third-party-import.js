;(function ($, undefined) {
	Wds.ThirdPartyImport = function () {
		var sourcePlugin = false,
			sourcePluginName = false,
			_templates = {
				import_options: Wds.tpl_compile(Wds.template('import', 'import-options')),
				import_error: Wds.tpl_compile(Wds.template('import', 'import-error')),
				import_progress: Wds.tpl_compile(Wds.template('import', 'import-progress')),
				import_progress_reset: Wds.tpl_compile(Wds.template('import', 'import-progress-reset')),
				import_success: Wds.tpl_compile(Wds.template('import', 'import-success'))
			};

		function skip() {
			Wds.close_dialog();
		}

		function update_checkbox() {
			var $label = $(this),
				$dialog = $label.closest('.wds-modal'),
				$checkbox = $dialog.find('#' + $label.attr('for')),
				$dependent = $dialog.find('#' + $checkbox.data('dependent')),
				newStatus = !$checkbox.is(':checked'); // Opposite of the previous value

			if ($checkbox.is(':disabled')) {
				return;
			}

			$checkbox.attr('checked', newStatus);
			$checkbox.trigger('change');

			$dependent.attr('disabled', !newStatus);
			$dependent.closest('.wds-toggle-table').toggleClass('disabled', !newStatus);
			if (!newStatus) {
				$dependent.attr('checked', false);
				$dependent.trigger('change');
			}
		}

		function toggle_enabled_actions() {
			var $all = $(this).closest(".wds-import-status-dialog").find(":checkbox"),
				$button = $("button.wds-import-start"),
				disabled = true;
			$all.each(function () {
				disabled = disabled && !$(this).is(":checked");
			});
			if (disabled) {
				$button.attr("disabled", true).addClass("disabled");
			} else {
				$button.attr("disabled", false).removeClass("disabled");
			}
		}

		function set_source_plugin(plugin) {
			if (plugin === 'yoast') {
				sourcePlugin = 'yoast';
				sourcePluginName = Wds.l10n('import', 'Yoast');
			} else if (plugin === 'aioseop') {
				sourcePlugin = 'aioseop';
				sourcePluginName = Wds.l10n('import', 'All In One SEO');
			}
		}

		function dialog_body() {
			return $('.sui-box-body', $('#wds-import-status'));
		}

		function show_import_options() {
			var importOptionsHTML = _templates.import_options({plugin_name: sourcePluginName});
			dialog_body().html(importOptionsHTML);
		}

		function show_dialog() {
			Wds.open_dialog('wds-import-status');
			show_import_options();
		}

		function show_yoast_dialog(event) {
			event.preventDefault();
			set_source_plugin('yoast');
			show_dialog();
		}

		function show_aioseop_dialog(event) {
			event.preventDefault();
			set_source_plugin('aioseop');
			show_dialog();
		}

		function start_import() {
			var progressHTML = _templates.import_progress({plugin_name: sourcePluginName}),
				itemsToImport = get_items_to_import();
			dialog_body().html(progressHTML);
			import_data(itemsToImport, 1);
		}

		function get_items_to_import() {
			var $all = dialog_body().find(':checkbox'),
				items_to_import = {};

			$all.each(function () {
				var $checkbox = $(this),
					name = $checkbox.attr('name');
				items_to_import[name] = $checkbox.is(":checked") ? 1 : 0;
			});

			return items_to_import;
		}

		function reset_progress_bar($progress_bar) {
			var $new_progress_bar = $(_templates.import_progress_reset({}));
			$progress_bar.replaceWith($new_progress_bar);
			return $new_progress_bar;
		}

		function update_site_progress(status) {
			var $progress = dialog_body().find('.wds-site-progress .wds-progress');
			if ($progress.length === 0) {
				return;
			}

			var total_sites = status.total_sites || 0,
				completed_sites = status.completed_sites || 0,
				site_progress = total_sites > 0 ? (completed_sites / total_sites) * 100 : 0;

			Wds.update_progress_bar($progress, site_progress);
		}

		function update_post_progress(status) {
			var $progress_bar = dialog_body().find('.wds-post-progress .wds-progress');
			if ($progress_bar.length === 0) {
				return;
			}

			var remaining_posts = status.remaining_posts || 0,
				completed_posts = status.completed_posts || 0,
				prev_value = $progress_bar.data('progress'),
				total_posts = remaining_posts + completed_posts,
				post_progress = total_posts > 0 ? (completed_posts / total_posts) * 100 : 0;

			if (prev_value >= post_progress) {
				$progress_bar = reset_progress_bar($progress_bar);
			}

			Wds.update_progress_bar($progress_bar, post_progress);
		}

		function show_success_message(data) {
			Wds.update_progress_bar(dialog_body().find(".wds-progress"), 100);
			setTimeout(function () {
				var successHTML = _templates.import_success({
					plugin_name: sourcePluginName,
					deactivation_url: data.deactivation_url
				});
				dialog_body().html(successHTML);
			}, 2500);
		}

		function import_data(itemsToImport, restart) {
			var action = sourcePlugin === 'yoast' ? 'import_yoast_data' : 'import_aioseop_data';

			return $.post({
				url: ajaxurl,
				data: {
					action: action,
					restart: restart,
					items_to_import: itemsToImport,
					_wds_nonce: _wds_import.nonce
				},
				success: function (data) {
					if (data.success) {
						if (data.in_progress) {
							update_site_progress(data.status);
							update_post_progress(data.status);
							import_data(itemsToImport, 0);
						} else {
							show_success_message(data);
						}
					} else {
						handle_import_error(data.message);
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					handle_import_error(errorThrown);
				},
				dataType: 'JSON'
			});
		}

		function handle_import_error(errorString) {
			var errorHTML = _templates.import_error({error: errorString});

			dialog_body().html(errorHTML);
		}

		function toggle_advanced_options(event) {
			var $this = $(event.target),
				$advanced_options = $this.closest('.wds-advanced-import-options');

			$advanced_options.toggleClass('open');
		}

		$(document).on("click", "button.wds-import-skip", skip);
		$(document).on("click", "div.wds-import-status-dialog label", update_checkbox);
		$(document).on("click", ".wds-yoast button", show_yoast_dialog);
		$(document).on("click", ".wds-aioseop button", show_aioseop_dialog);
		$(document).on("click", "button.wds-import-start", start_import);
		$(document).on("click", "button.wds-reattempt-import", show_import_options);
		$(document).on("click", ".wds-advanced-import-options > span", toggle_advanced_options);
		$(document).on("change", "div.wds-import-status-dialog :checkbox", toggle_enabled_actions);
	};

	function init() {
		var thirdPartyImport = Wds.ThirdPartyImport();
	}

	$(init);
})(jQuery);

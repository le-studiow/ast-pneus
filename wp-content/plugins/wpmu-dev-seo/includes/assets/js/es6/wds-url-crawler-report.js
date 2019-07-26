(function ($) {
	/**
	 * The class responsible for interaction with the crawl report UI. Changes to the markup should ideally affect this class only.
	 */
	class URLCrawlerReportUI {
		static get_open_section() {
			return $('.sui-accordion-item--open');
		}

		static get_open_section_type() {
			return URLCrawlerReportUI.get_open_section().data('type');
		}

		static is_ignored_tab_open() {
			return URLCrawlerReportUI.get_open_section().find('[data-tabs] .active').is('.ignored');
		}

		static replace_report_markup(new_markup) {
			$('.wds-crawl-results-report').replaceWith(new_markup);
		}

		static replace_summary_markup(new_markup) {
			$('#wds-crawl-summary-container').html(new_markup);
		}

		static get_issue_id($context) {
			return $context.closest('[data-issue-id]').data('issueId');
		}

		static get_path($context) {
			return $context.closest('[data-path]').data('path');
		}

		static get_redirect_path($context) {
			return $context.closest('[data-redirect-path]').data('redirectPath');
		}

		static get_issue_ids($context) {
			let $group_container = $context.closest('.wds-crawl-issues-table'),
				$issue_container = $group_container.length
					? $group_container
					: $context.closest('.tab_url_crawler'),
				$issues = $issue_container.find('[data-issue-id]'),
				issue_ids = [];

			$issues.each(function (index, issue) {
				issue_ids.push($(issue).data('issueId'));
			});

			return issue_ids;
		}

		static block_ui($target_el) {
			if ($target_el.closest('.wds-links-dropdown').length) {
				$target_el = $target_el.closest('.wds-links-dropdown').find('.sui-dropdown-anchor');
			}

			if ($target_el.is('.sui-button-onload')) {
				// Already blocked
				return;
			}

			$target_el.addClass('sui-button-onload');
			$('.wds-disabled-during-request').prop('disabled', true);
		}

		static unblock_ui() {
			$('.wds-disabled-during-request').prop('disabled', false);
			$('.sui-button-onload').removeClass('sui-button-onload');
		}

		static show_sitemap_message($message, $context) {
			let $tabs = $context.closest('.sui-tabs');
			if (!$tabs.length) {
				return;
			}

			$tabs.prev('.wds-notice').remove();
			$message.insertBefore($tabs);
		}

		static get_sitemap_path($context) {
			return $context.closest('[data-issue-id]').data('path');
		}

		static get_sitemap_paths($context) {
			let $container = $context.closest('.wds-crawl-issues-table'),
				$issues = $container.find('[data-issue-id]'),
				paths = [];

			$issues.each(function (index, issue) {
				paths.push($(issue).data('path'));
			});

			return paths;
		}

		static get_dialog($context) {
			return $context.closest('.sui-dialog');
		}

		static get_redirect_data($context) {
			let $modal = URLCrawlerReportUI.get_dialog($context),
				$fields = $modal.find("input"),
				data = {};

			$fields.each(function () {
				let $me = $(this);
				data[$me.attr("name")] = $me.val();
			});

			return data;
		}

		static open_dialog(dialog_id) {
			let dialog_el = $('#' + dialog_id),
				dialog = dialog_el.length
					? new A11yDialog(dialog_el[0])
					: false;

			if (dialog) {
				dialog.show();
			}
		}

		static close_dialog($context) {
			URLCrawlerReportUI.get_dialog($context).find(".sui-dialog-close").click();
		}

		static replace_dialog_markup(dialog, markup) {
			$('#' + dialog).replaceWith(markup);
		}

		static validate_dialog(dialog_id) {
			let is_valid = true;
			$('.sui-form-field', $('#' + dialog_id)).each(function () {
				let $form_field = $(this),
					$input = $('input', $form_field);

				if (!$input.val()) {
					is_valid = false;
					$form_field.addClass('sui-form-field-error');

					$input.on('focus keydown', function () {
						$(this).closest('.sui-form-field-error').removeClass('sui-form-field-error');
					});
				}
			});

			return is_valid;
		}
	}

	/**
	 * The class containing the business logic for the crawl report.
	 *
	 * @see URLCrawlerReportUI
	 */
	class URLCrawlerReport {
		init() {
			$(document)
				.on('click', '[href="#ignore"]', (e) => this.handle_ignore_single_action(e))
				.on('click', '.wds-crawl-issues-table .wds-ignore-all', (e) => this.handle_ignore_group_action(e))
				.on('click', '.sui-box-header .wds-ignore-all', (e) => this.handle_ignore_all_action(e))

				.on('click', '.wds-ignored-items-table .wds-unignore', (e) => this.handle_restore_single_action(e))

				.on('click', '[href="#add-to-sitemap"]', (e) => this.handle_add_single_to_sitemap_action(e))
				.on('click', '.wds-crawl-issues-table .wds-add-all-to-sitemap', (e) => this.handle_add_all_to_sitemap_action(e))

				.on('click', '[href="#occurrences"]', (e) => this.handle_open_occurrences_dialog_action(e))
				.on('click', '[href="#redirect"]', (e) => this.handle_open_redirect_dialog_action(e))
				.on('click', '.wds-submit-redirect', (e) => this.handle_save_redirect_action(e))
			;

			this.templates = {
				redirect_dialog: Wds.tpl_compile(Wds.template('url_crawler', 'redirect_dialog')),
				occurrences_dialog: Wds.tpl_compile(Wds.template('url_crawler', 'occurrences_dialog')),
				issue_occurrences: Wds.tpl_compile(Wds.template('url_crawler', 'issue_occurrences'))
			};
			this.occurrences_request = new $.Deferred();
		}

		post(action, data) {
			data = $.extend({
				action: action,
				_wds_nonce: _wds_sitemaps.nonce
			}, data);

			return $.post(ajaxurl, data);
		}

		reload_report() {
			return this.post('wds-get-sitemap-report', {
				open_type: URLCrawlerReportUI.get_open_section_type(),
				ignored_tab_open: URLCrawlerReportUI.is_ignored_tab_open() ? 1 : 0
			}).done(
				/**
				 * @param {{summary_markup:string, markup:string}} data
				 */
				(data) => {
					data = (data || {});
					if (data.success && data.markup) {
						URLCrawlerReportUI.replace_report_markup(data.markup);
						URLCrawlerReportUI.replace_summary_markup(data.summary_markup);
						URLCrawlerReportUI.unblock_ui();

						$(this).trigger('wds_url_crawler_report:reloaded');
					}
				});
		}

		change_issue_status(issue_id, action) {
			return this.post(action, {issue_id: issue_id})
				.done((data) => {
					let status = parseInt(
						(data || {}).status || '0',
						10
					);
					if (status > 0) {
						this.reload_report();
					}
				});
		}

		ignore_issue(issue_id) {
			return this.change_issue_status(issue_id, 'wds-service-ignore');
		}

		restore_issue(issue_id) {
			return this.change_issue_status(issue_id, 'wds-service-unignore');
		}

		handle_ignore_single_action(e) {
			e.preventDefault();

			let $target = $(e.target),
				issue_id = URLCrawlerReportUI.get_issue_id($target);

			URLCrawlerReportUI.block_ui($target);

			return this.ignore_issue(issue_id);
		}

		handle_restore_single_action(e) {
			e.preventDefault();

			let $target = $(e.target),
				issue_id = URLCrawlerReportUI.get_issue_id($target);

			URLCrawlerReportUI.block_ui($target);

			return this.restore_issue(issue_id);
		}

		handle_ignore_group_action(e) {
			e.preventDefault();

			let $target = $(e.target),
				issue_ids = URLCrawlerReportUI.get_issue_ids($target);

			URLCrawlerReportUI.block_ui($target);

			return this.ignore_issue(issue_ids);
		}

		handle_ignore_all_action(e) {
			e.preventDefault();

			let $target = $(e.target),
				issue_ids = URLCrawlerReportUI.get_issue_ids($target);

			URLCrawlerReportUI.block_ui($target);

			return this.ignore_issue(issue_ids);
		}

		add_to_sitemap(path, $context) {
			return this.post('wds-sitemap-add_extra', {path: path})
				.done(
					/**
					 * @param {{status:string, add_all_message:string}} data
					 */
					(data) => {
						data = (data || {});
						let status = parseInt(
							data.status || '0',
							10
						);
						if (status > 0) {
							let $message = $(data.add_all_message || '');

							URLCrawlerReportUI.show_sitemap_message($message, $context);
							URLCrawlerReportUI.unblock_ui();
						}
					});
		}

		handle_add_single_to_sitemap_action(e) {
			e.preventDefault();

			let $target = $(e.target),
				path = URLCrawlerReportUI.get_sitemap_path($target);

			URLCrawlerReportUI.block_ui($target);

			return this.add_to_sitemap(path, $target);
		}

		handle_add_all_to_sitemap_action(e) {
			e.preventDefault();

			let $target = $(e.target),
				path = URLCrawlerReportUI.get_sitemap_paths($target);

			URLCrawlerReportUI.block_ui($target);

			return this.add_to_sitemap(path, $target);
		}

		load_issue_occurrences(issue_id) {
			let deferred = new $.Deferred();

			this.post('wds-load-issue-occurrences', {
				issue_id: issue_id
			}).done((response) => {
				if (deferred.state() !== 'pending') {
					return;
				}

				let success = (response || {}).success || false,
					data = (response || {}).data || {};

				if (success && data.occurrences) {
					deferred.resolve(data.occurrences);
				} else {
					deferred.reject();
				}
			}).fail(deferred.reject);

			return deferred;
		}

		handle_open_occurrences_dialog_action(e) {
			e.preventDefault();
			// Cancel the previous request if it's still in progress
			if (this.occurrences_request.state() === 'pending') {
				this.occurrences_request.reject();
			}

			let $target = $(e.target),
				issue_id = URLCrawlerReportUI.get_issue_id($target),
				path = URLCrawlerReportUI.get_path($target),
				markup = this.templates.occurrences_dialog({
					'issue_id': issue_id,
					'issue_path': path,
					'issue_occurrences': ''
				});

			URLCrawlerReportUI.replace_dialog_markup('wds-issue-occurrences', markup);
			URLCrawlerReportUI.open_dialog('wds-issue-occurrences');
			this.occurrences_request = this.load_issue_occurrences(issue_id)
				.done((occurrences) => {
					let occurrences_markup = this.templates.issue_occurrences({
						occurrences: occurrences
					});

					$('#wds-issue-occurrences').find('.wds-issue-occurrences').html(occurrences_markup);
				});
		}

		handle_open_redirect_dialog_action(e) {
			e.preventDefault();

			let $target = $(e.target),
				issue_id = URLCrawlerReportUI.get_issue_id($target),
				path = URLCrawlerReportUI.get_path($target),
				redirect_path = URLCrawlerReportUI.get_redirect_path($target),
				markup = this.templates.redirect_dialog({
					'issue_id': issue_id,
					'issue_path': path,
					'issue_redirect_path': redirect_path
				});

			URLCrawlerReportUI.replace_dialog_markup('wds-issue-redirect', markup);
			URLCrawlerReportUI.open_dialog('wds-issue-redirect');
		}

		handle_save_redirect_action(e) {
			e.preventDefault();

			let is_valid = URLCrawlerReportUI.validate_dialog('wds-issue-redirect');
			if (!is_valid) {
				return;
			}

			let $target = $(e.target),
				issue_id = URLCrawlerReportUI.get_issue_id($target),
				redirect_data = URLCrawlerReportUI.get_redirect_data($target);

			URLCrawlerReportUI.block_ui($target);

			return this.post('wds-service-redirect', redirect_data)
				.always(() => {
					URLCrawlerReportUI.close_dialog($target);
					this.ignore_issue(issue_id);
				});
		}
	}

	window.Wds = window.Wds || {};
	window.Wds.URLCrawlerReport = new URLCrawlerReport();
})(jQuery);

(function ($, undefined) {
	window.Wds = window.Wds || {};
	var crawlerReport = window.Wds.URLCrawlerReport;

	function update_page_after_report_reload() {
		var $report = $('.wds-crawl-results-report'),
			active_issues = $report.data('activeIssues'),
			ignored_issues = $report.data('ignoredIssues'),
			$vertical_tab = $report.closest('.tab_url_crawler'),
			$title_issues_indicator = $vertical_tab.find('.sui-box-header .sui-tag'),
			$crawler_tab = $('li.tab_url_crawler'),
			$label_issues_indicator = $crawler_tab.find('.sui-tag'),
			$label_tick = $crawler_tab.find('.sui-icon-check-tick'),
			$label_spinner = $crawler_tab.find('.sui-icon-loader'),
			$new_crawl_button = $('.wds-new-crawl-button'),
			$title_ignore_all_button = $('.sui-box-header .wds-ignore-all').closest('div');

		if (active_issues === undefined) {
			// In progress or no data
			return;
		}

		if (active_issues > 0) {
			$title_issues_indicator.show().html(active_issues);
			$label_issues_indicator.show().html(active_issues);
			$title_ignore_all_button.show();
			$label_tick.hide();
		}
		else {
			$title_issues_indicator.hide();
			$label_issues_indicator.hide();
			$title_ignore_all_button.hide();
			$label_tick.show();
		}

		// Hide the spinner and show the new crawl button regardless of the result
		$label_spinner.hide();
		$new_crawl_button.show();
	}

	function update_progress() {
		var $container = $('.tab_url_crawler');
		if (
			!$container.find('.wds-url-crawler-progress').length
			|| !crawlerReport
		) {
			return;
		}

		crawlerReport.reload_report().done(function () {
			setTimeout(update_progress, 5000);
		});
	}

	function handle_accordion_item_click() {
		var $accordion_item = $(this).closest('.sui-accordion-item');

		// Keep one section open at a time
		$('.sui-accordion-item--open').not($accordion_item).removeClass('sui-accordion-item--open');
	}

	function initialize_components() {
		$('.sui-accordion').each(function () {
			SUI.suiAccordion(this);
		});
		$('.sui-accordion-item-header').off('click.sui.accordion').on('click.sui.accordion', handle_accordion_item_click);
		window.Wds.side_tabs();
	}

	// As soon as a link is clicked inside the dropdown close it
	function close_links_dropdown() {
		var $dropdown = $(this).closest('.wds-links-dropdown');
		$dropdown.removeClass('open');
	}

	function change_crawl_frequency(event, tab) {
		$('#wds-crawler-frequency').val($(tab).data('frequency')).trigger('change');
	}

	function update_sitemap_sub_section_visbility() {
		$('.wds-sitemap-toggleable').each(function () {
			var $toggleable = $(this),
				$nested_table = $toggleable.next('tr').find('.sui-table');

			if ($toggleable.find('input[type="checkbox"]').is(':checked')) {
				$nested_table.show();
			} else {
				$nested_table.hide();
			}
		});
	}

	function submit_dialog_form_on_enter(e) {
		var $button = $(this).find('.wds-submit-redirect'),
			key = e.which;

		if ($button.length && 13 === key) {
			e.preventDefault();
			e.stopPropagation();

			$button.click();
		}
	}

	function init() {
		window.Wds.hook_toggleables();
		window.Wds.upsell();
		window.Wds.conditional_fields();
		window.Wds.dismissible_message();
		window.Wds.vertical_tabs();

		update_progress();
		initialize_components();

		$(document)
			.on('click', '.wds-links-dropdown a', close_links_dropdown)
			.on('change', '.wds-sitemap-toggleable input[type="checkbox"]', update_sitemap_sub_section_visbility)
			.on('ready', update_sitemap_sub_section_visbility)
			.on('keydown', '.sui-dialog', submit_dialog_form_on_enter)
			.on('wds_side_tabs:tab_change', '.sui-side-tabs', change_crawl_frequency);

		if (crawlerReport) {
			crawlerReport.init();
			$(crawlerReport)
				.on('wds_url_crawler_report:reloaded', update_page_after_report_reload)
				.on('wds_url_crawler_report:reloaded', initialize_components);
		}
	}

	$(init);
})(jQuery);

(function ($) {
	// -----------------------------------------------------
	//	Boot
	// -----------------------------------------------------

	function init() {
		// General metabox stuff
		window.Wds.hook_toggleables();
		window.Wds.dismissible_message();
		window.Wds.side_tabs();
		load_accordions();
		save_metabox_state();

		// Modules
		hook_analysis();
		hook_onpage();
	}

	$(init);

	// -----------------------------------------------------
	//	General functions
	// -----------------------------------------------------

	function metabox_el() {
		return $("#wds-wds-meta-box");
	}

	function load_accordions() {
		$('.wds-page .sui-accordion').each(function () {
			SUI.suiAccordion(this);
		});
	}

	function save_metabox_state() {
		/**
		 * Set cookie value each time the metabox is toggled.
		 */
		metabox_el().on('click', function () {
			if ($(this).is(".closed")) {
				window.Wds.set_cookie('wds-seo-metabox', '');
			} else {
				window.Wds.set_cookie('wds-seo-metabox', 'open');
			}
		});

		// Set metabox state on page load based on cookie value.
		// Fixes: https://app.asana.com/0/0/580085427092951/f
		if ('open' === window.Wds.get_cookie('wds-seo-metabox')) {
			metabox_el().removeClass('closed');
		}
	}

	// -----------------------------------------------------
	//	Functions related to onpage
	// -----------------------------------------------------

	function hook_onpage() {
		var onpage = window.Wds.metaboxOnpage;
		if (!onpage) {
			return;
		}

		var tlimit = (l10nWdsCounters || {}).title_limit || 70;
		window.Wds.optimum_length_indicator($('#wds_title'), 50, parseInt(tlimit, 10));

		var mlimit = (l10nWdsCounters || {}).metad_limit || 160;
		window.Wds.optimum_length_indicator($('#wds_metadesc'), 135, parseInt(mlimit, 10));
	}

	// -----------------------------------------------------
	//	Function related to SEO & readability analysis
	// -----------------------------------------------------

	function hook_analysis() {
		var analysis = window.Wds.metaboxAnalysis;
		if (!analysis) {
			return;
		}

		analysis.addEventListener('before-ignore-status-change', handle_analysis_ignore_change);
		analysis.addEventListener('before-focus-keyword-update', handle_focus_keyword_update);
		analysis.addEventListener('before-analysis-refresh', handle_analysis_refresh);
		analysis.addEventListener('after-seo-analysis-update', handle_seo_analysis_update);
		analysis.addEventListener('after-readability-analysis-update', handle_readability_analysis_update);
	}

	function handle_analysis_ignore_change() {
		display_navigation_loaders(true);
	}

	function handle_focus_keyword_update() {
		display_navigation_loaders(true);
	}

	function handle_analysis_refresh() {
		display_navigation_loaders(false);
	}

	function handle_seo_analysis_update(event) {
		hide_navigation_loaders();
		update_seo_issue_count(event);
		load_accordions();
	}

	function handle_readability_analysis_update(event) {
		update_readability_state(event);
		load_accordions();
	}

	function display_navigation_loaders(active_only) {
		var $issues = active_only
			? $('[data-tabs] .active .wds-issues')
			: $('[data-tabs] .wds-issues');

		$issues.addClass('wds-item-loading');
	}

	function hide_navigation_loaders() {
		var $metabox = metabox_el(),
			metabox_closed = $metabox.is('.closed'),
			$all_issues = $('.wds-issues');

		$all_issues.find('span').html('');
		$all_issues.removeClass().addClass('wds-issues');
		$metabox.removeClass().addClass('postbox');
		if (metabox_closed) {
			$metabox.addClass('closed');
		}
	}

	function update_seo_issue_count(event) {
		var $metabox = metabox_el(),
			detail = event.detail,
			$seo_issues = $('.wds_seo-tab').find('.wds-issues'),
			seo_errors = detail.errors;

		if (seo_errors > 0) {
			$metabox.addClass('wds-seo-warning');
			$seo_issues.addClass('wds-issues-warning').find('span').html(seo_errors);
		}
		else if (seo_errors === 0) {
			$metabox.addClass('wds-seo-success');
			$seo_issues.addClass('wds-issues-success');
		}
		else if (seo_errors === -1) {
			$metabox.addClass('wds-seo-invalid');
			$seo_issues.addClass('wds-issues-invalid').find('span').html('0');
		}
	}

	function update_readability_state(event) {
		var $metabox = metabox_el(),
			detail = event.detail,
			$readability_issues = $('.wds_readability-tab').find('.wds-issues'),
			readability_state = detail.state;

		$metabox.addClass('wds-readability-' + readability_state);
		$readability_issues.addClass('wds-issues-' + readability_state);
		if ($readability_issues.is('.wds-issues-warning') || $readability_issues.is('.wds-issues-error')) {
			$readability_issues.find('span').html('1');
		} else if ($readability_issues.is('.wds-issues-invalid')) {
			$readability_issues.find('span').html('0');
		}
	}
})(jQuery);

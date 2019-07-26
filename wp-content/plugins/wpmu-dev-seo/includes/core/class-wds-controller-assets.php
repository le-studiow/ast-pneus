<?php

class Smartcrawl_Controller_Assets extends Smartcrawl_Base_Controller {
	const SUI_JS = 'wds-shared-ui';
	const ADMIN_JS = 'wds-admin';
	const OPENGRAPH_JS = 'wds-admin-opengraph';
	const QTIP2_JS = 'wds-qtip2-script';

	const CUSTOM_KEYWORDS_JS = 'wds-admin-keywords';
	const POSTLIST_JS = 'wds-admin-postlist';
	const REDIRECTS_JS = 'wds-admin-redirects';
	const AUTOLINKS_PAGE_JS = 'wds-admin-autolinks';

	const ONPAGE_JS = 'wds-admin-onpage';

	const URL_CRAWLER_REPORT_JS = 'wds-url-crawler-report';
	const SITEMAPS_PAGE_JS = 'wds-admin-sitemaps';

	const DASHBOARD_PAGE_JS = 'wds-admin-dashboard';

	const ONBOARDING_JS = 'wds-onboard';

	const EMAIL_RECIPIENTS_JS = 'wds-admin-email-recipients';
	const CHECKUP_PAGE_JS = 'wds-admin-checkup';

	const SOCIAL_PAGE_JS = 'wds-admin-social';

	const THIRD_PARTY_IMPORT_JS = 'wds-third-party-import';
	const SETTINGS_PAGE_JS = 'wds-admin-settings';

	const METABOX_COUNTER_JS = 'wds_metabox_counter';
	const METABOX_JS = 'wds_metabox';
	const POST_EDITOR_JS = 'wds-post-editor';
	const METABOX_ONPAGE_JS = 'wds-metabox-onpage';
	const METABOX_ANALYSIS_JS = 'wds-metabox-analysis';

	const WP_POSTLIST_ANALYSIS_JS = 'wds-admin-analysis-postlist';

	const TERM_FORM_JS = 'wds-term-form';

	const QTIP2_CSS = 'wds-qtip2-style';
	const APP_CSS = 'wds-app';
	const WP_DASHBOARD_CSS = 'wds-wp-dashboard';
	const WP_POSTLIST_ANALYSIS_CSS = 'wds-admin-analysis-postlist-styling';

	/**
	 * Singleton instance
	 *
	 * @var self
	 */
	private static $_instance;

	/**
	 * Obtain instance without booting up
	 *
	 * @return self instance
	 */
	public static function get() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Bind listening actions
	 *
	 * @return bool
	 */
	public function init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_assets' ), - 10 );

		return true;
	}

	public function register_assets() {
		$this->register_general_scripts();
		$this->register_advanced_tools_scripts();
		$this->register_onpage_page_scripts();
		$this->register_sitemap_page_scripts();
		$this->register_dashboard_page_scripts();
		$this->register_checkup_page_scripts();
		$this->register_social_page_scripts();
		$this->register_settings_page_scripts();
		$this->register_metabox_scripts();
		$this->register_post_list_scripts();
		$this->register_term_form_scripts();

		$this->register_general_styles();
		$this->register_wp_dashboard_styles();
		$this->register_post_list_styles();
	}

	private function register_js( $handle, $src, $deps = array() ) {
		wp_register_script( $handle, $this->base_url( $src ), $deps, Smartcrawl_Loader::get_version(), true );
	}

	private function register_css( $handle, $src, $deps = array() ) {
		wp_register_style( $handle, $this->base_url( $src ), $deps, Smartcrawl_Loader::get_version(), 'all' );
	}

	private function base_url( $url ) {
		return trailingslashit( SMARTCRAWL_PLUGIN_URL ) . "assets/$url";
	}

	private function register_general_scripts() {
		// Shared UI
		$this->register_js( self::SUI_JS, 'shared-ui/js/shared-ui.js', array(
			'jquery',
		) );

		// Common JS functions and utils
		$this->register_js( self::ADMIN_JS, 'js/wds-admin.js', array(
			self::SUI_JS,
			'jquery',
		) );

		wp_localize_script( self::ADMIN_JS, '_wds_admin', array(
			'templates' => array(
				'floating_message' => Smartcrawl_Simple_Renderer::load( 'underscore-floating-message' ),
			),
			'strings'   => array(
				'initializing' => esc_html__( 'Initializing ...', 'wds' ),
				'running'      => esc_html__( 'Running SEO checks ...', 'wds' ),
				'finalizing'   => esc_html__( 'Running final checks and finishing up ...', 'wds' ),
			),
			'nonce'     => wp_create_nonce( 'wds-admin-nonce' ),
		) );

		$this->register_js( self::OPENGRAPH_JS, 'js/wds-admin-opengraph.js', array(
			'underscore',
			'jquery',
			self::ADMIN_JS,
		) );

		$this->register_js( self::QTIP2_JS, 'js/external/jquery.qtip.min.js', array(
			'jquery',
		) );
	}

	private function register_advanced_tools_scripts() {
		$this->register_js( self::CUSTOM_KEYWORDS_JS, 'js/wds-admin-keywords.js', array(
			'underscore',
			'jquery',
			self::ADMIN_JS,
		) );

		wp_localize_script( self::CUSTOM_KEYWORDS_JS, '_wds_keywords', array(
			'templates' => array(
				'custom' => Smartcrawl_Simple_Renderer::load( 'advanced-tools/underscore-keywords-custom' ),
				'pairs'  => Smartcrawl_Simple_Renderer::load( 'advanced-tools/underscore-keywords-pairs' ),
				'form'   => Smartcrawl_Simple_Renderer::load( 'advanced-tools/underscore-keywords-form' ),
			),
			'strings'   => array(
				'Add'    => esc_html__( 'Add', 'wds' ),
				'Update' => esc_html__( 'Update', 'wds' ),
			),
		) );

		$this->register_js( self::POSTLIST_JS, 'js/wds-admin-postlist.js', array(
			'underscore',
			'jquery',
			self::ADMIN_JS,
		) );

		wp_localize_script( self::POSTLIST_JS, '_wds_postlist', array(
			'templates'  => array(
				'exclude'      => Smartcrawl_Simple_Renderer::load( 'advanced-tools/underscore-postlist-exclusion' ),
				'exclude-item' => Smartcrawl_Simple_Renderer::load( 'advanced-tools/underscore-postlist-exclusion-item' ),
				'selector'     => Smartcrawl_Simple_Renderer::load( 'advanced-tools/underscore-postlist-selector' ),
			),
			'post_types' => Smartcrawl_Autolinks_Settings::get_post_types(),
			'strings'    => array(),
			'nonce'      => wp_create_nonce( 'wds-autolinks-nonce' ),
		) );

		$this->register_js( self::REDIRECTS_JS, 'js/wds-admin-redirects.js', array(
			'underscore',
			'jquery',
			self::ADMIN_JS,
		) );

		wp_localize_script( self::REDIRECTS_JS, '_wds_redirects', array(
			'templates' => array(
				'redirect-item' => Smartcrawl_Simple_Renderer::load( 'advanced-tools/underscore-redirect-item' ),
				'add-form'      => Smartcrawl_Simple_Renderer::load( 'advanced-tools/underscore-add-redirect-form' ),
				'edit-form'     => Smartcrawl_Simple_Renderer::load( 'advanced-tools/underscore-edit-redirect-form' ),
				'update-form'   => Smartcrawl_Simple_Renderer::load( 'advanced-tools/underscore-bulk-update-form' ),
			),
			'strings'   => array(
				'redirect_added'    => esc_html__( "The redirect has been added. You need to save the changes to make them live.", 'wds' ),
				'redirect_updated'  => esc_html__( "The redirect has been updated. You need to save the changes to make them live.", 'wds' ),
				'redirect_removed'  => esc_html__( "The redirect has been removed. You need to save the changes to make them live.", 'wds' ),
				'redirects_updated' => esc_html__( "The redirects have been updated. You need to save the changes to make them live.", 'wds' ),
				'redirects_removed' => esc_html__( "The redirects have been removed. You need to save the changes to make them live.", 'wds' ),
			),
		) );

		$this->register_js( self::AUTOLINKS_PAGE_JS, 'js/wds-admin-autolinks.js', array(
			'underscore',
			'jquery',
			self::ADMIN_JS,
			self::CUSTOM_KEYWORDS_JS,
			self::POSTLIST_JS,
			self::REDIRECTS_JS,
		) );
	}

	private function register_onpage_page_scripts() {
		$this->register_js( self::ONPAGE_JS, 'js/wds-admin-onpage.js', array(
			'jquery',
			self::ADMIN_JS,
			self::OPENGRAPH_JS,
		) );

		wp_localize_script( self::ONPAGE_JS, '_wds_onpage', array(
			'nonce' => wp_create_nonce( 'wds-onpage-nonce' ),
		) );
	}

	private function register_sitemap_page_scripts() {
		$this->register_js( self::URL_CRAWLER_REPORT_JS, 'js/build/wds-url-crawler-report.js', array(
			'underscore',
			'jquery',
			self::ADMIN_JS,
		) );
		wp_localize_script( self::URL_CRAWLER_REPORT_JS, '_wds_url_crawler', array(
			'templates' => array(
				'redirect_dialog'    => Smartcrawl_Simple_Renderer::load( 'sitemap/underscore-redirect-dialog' ),
				'occurrences_dialog' => Smartcrawl_Simple_Renderer::load( 'sitemap/underscore-occurrences-dialog' ),
				'issue_occurrences'  => Smartcrawl_Simple_Renderer::load( 'sitemap/underscore-issue-occurrences' ),
			),
		) );

		$this->register_js( self::SITEMAPS_PAGE_JS, 'js/wds-admin-sitemaps.js', array(
			'jquery',
			self::ADMIN_JS,
			self::URL_CRAWLER_REPORT_JS,
		) );

		wp_localize_script( self::SITEMAPS_PAGE_JS, '_wds_sitemaps', array(
			'nonce' => wp_create_nonce( 'wds-nonce' ),
		) );
	}

	private function register_dashboard_page_scripts() {
		$this->register_js( self::ONBOARDING_JS, 'js/wds-admin-onboard.js', array(
			self::ADMIN_JS,
		) );

		wp_localize_script( self::ONBOARDING_JS, '_wds_onboard', array(
			'templates' => array(
				'progress' => Smartcrawl_Simple_Renderer::load( 'dashboard/onboard-progress' ),
			),
			'strings'   => array(
				'All done' => esc_html__( 'All done, please hold on...', 'wds' ),
			),
			'nonce'     => wp_create_nonce( 'wds-onboard-nonce' ),
		) );

		$this->register_js( self::DASHBOARD_PAGE_JS, 'js/wds-admin-dashboard.js', array(
			'jquery',
			'underscore',
			self::ADMIN_JS,
			self::ONBOARDING_JS,
		) );

		wp_localize_script( self::DASHBOARD_PAGE_JS, '_wds_dashboard', array(
			'nonce'            => wp_create_nonce( 'wds-nonce' ),
			'full_checkup_url' => Smartcrawl_Settings_Admin::admin_url( Smartcrawl_Settings::TAB_CHECKUP ),
		) );
	}

	private function register_checkup_page_scripts() {
		$this->register_js( self::EMAIL_RECIPIENTS_JS, 'js/wds-admin-email-recipients.js', array(
			'jquery',
			'underscore',
			self::ADMIN_JS,
		) );

		wp_localize_script( self::EMAIL_RECIPIENTS_JS, '_wds_email_recipients', array(
			'templates' => array(
				'recipient' => Smartcrawl_Simple_Renderer::load( 'underscore-email-recipient' ),
			),
			'strings'   => array(
				'recipient_added' => esc_html__( ' has been added as a recipient. Please save your changes to set this live.', 'wds' ),
			),
		) );

		$this->register_js( self::CHECKUP_PAGE_JS, 'js/wds-admin-checkup.js', array(
			'jquery',
			self::ADMIN_JS,
			self::EMAIL_RECIPIENTS_JS,
		) );
	}

	private function register_social_page_scripts() {
		$this->register_js( self::SOCIAL_PAGE_JS, 'js/wds-admin-social.js', array(
			'jquery',
			self::ADMIN_JS,
		) );
	}

	private function register_settings_page_scripts() {
		$this->register_js( self::THIRD_PARTY_IMPORT_JS, 'js/wds-third-party-import.js', array(
			'jquery',
			'underscore',
			self::ADMIN_JS,
		) );

		wp_localize_script( self::THIRD_PARTY_IMPORT_JS, '_wds_import', array(
			'templates' => array(
				'import-options'        => Smartcrawl_Simple_Renderer::load( 'settings/underscore-import-options' ),
				'import-error'          => Smartcrawl_Simple_Renderer::load( 'settings/underscore-import-error' ),
				'import-progress'       => Smartcrawl_Simple_Renderer::load( 'settings/underscore-import-progress' ),
				'import-progress-reset' => Smartcrawl_Simple_Renderer::load( 'settings/underscore-progress-reset' ),
				'import-success'        => Smartcrawl_Simple_Renderer::load( 'settings/underscore-import-success' ),
			),
			'strings'   => array(
				'Yoast'          => esc_html__( 'Yoast', 'wds' ),
				'All In One SEO' => esc_html__( 'All In One SEO', 'wds' ),
			),
			'nonce'     => wp_create_nonce( 'wds-io-nonce' ),
		) );

		$this->register_js( self::SETTINGS_PAGE_JS, 'js/wds-admin-settings.js', array(
			'jquery',
			self::ADMIN_JS,
			self::THIRD_PARTY_IMPORT_JS,
		) );
	}

	private function register_metabox_scripts() {
		$options = Smartcrawl_Settings::get_options();
		$this->register_js( self::METABOX_COUNTER_JS, 'js/wds-metabox-counter.js', array() );
		wp_localize_script( self::METABOX_COUNTER_JS, 'l10nWdsCounters', array(
			'title_length'      => esc_html__( '{TOTAL_LEFT} characters left', 'wds' ),
			'title_longer'      => esc_html__( 'Over {MAX_COUNT} characters ({CURRENT_COUNT})', 'wds' ),
			'main_title_longer' => esc_html__( 'Over {MAX_COUNT} characters ({CURRENT_COUNT}) - make sure your SEO title is shorter', 'wds' ),

			'title_limit'        => SMARTCRAWL_TITLE_LENGTH_CHAR_COUNT_LIMIT,
			'metad_limit'        => SMARTCRAWL_METADESC_LENGTH_CHAR_COUNT_LIMIT,
			'main_title_warning' => ! ( defined( 'SMARTCRAWL_MAIN_TITLE_LENGTH_WARNING_HIDE' ) && SMARTCRAWL_MAIN_TITLE_LENGTH_WARNING_HIDE ),
			'lax_enforcement'    => ( isset( $options['metabox-lax_enforcement'] ) ? ! ! $options['metabox-lax_enforcement'] : false ),
		) );

		$this->register_js( self::METABOX_JS, 'js/wds-metabox.js', array(
			'underscore',
			self::OPENGRAPH_JS,
			self::METABOX_COUNTER_JS,
		) );
		wp_localize_script( self::METABOX_JS, 'l10nWdsMetabox', array(
			'content_analysis_working' => esc_html__( 'Analyzing content, please wait a few moments', 'wds' ),
		) );

		if ( $this->is_block_editor_active() ) {
			$this->register_js( self::POST_EDITOR_JS, 'js/build/wds-editor.js', array(
				'jquery',
				'underscore',
				'wp-api-fetch',
				'wp-data',
			) );
		} else {
			$this->register_js( self::POST_EDITOR_JS, 'js/build/wds-classic-editor.js', array(
				'jquery',
				'underscore',
				'autosave',
				'editor',
			) );
		}
		$this->register_js( self::METABOX_ONPAGE_JS, 'js/build/wds-metabox-onpage.js', array(
			'jquery',
			'underscore',
			self::POST_EDITOR_JS,
		) );
		wp_localize_script( self::METABOX_ONPAGE_JS, '_wds_metabox_onpage', array(
			'nonce' => wp_create_nonce( 'wds-metabox-nonce' ),
		) );

		$this->register_js( self::METABOX_ANALYSIS_JS, 'js/build/wds-metabox-analysis.js', array(
			'jquery',
			'underscore',
			self::POST_EDITOR_JS,
		) );
		wp_localize_script( self::METABOX_ANALYSIS_JS, '_wds_metabox_analysis', array(
			'nonce' => wp_create_nonce( 'wds-metabox-nonce' ),
		) );
	}

	private function is_block_editor_active() {
		$screen = get_current_screen();
		if ( $screen && method_exists( $screen, 'is_block_editor' ) ) {
			return $screen->is_block_editor();
		}

		if ( function_exists( 'is_gutenberg_page' ) ) {
			return is_gutenberg_page();
		}

		return false;
	}

	private function register_post_list_scripts() {
		$this->register_js( self::WP_POSTLIST_ANALYSIS_JS, 'js/wds-admin-analysis-postlist.js', array(
				'jquery',
				self::ADMIN_JS,
				self::QTIP2_JS,
			)
		);

		wp_localize_script( self::WP_POSTLIST_ANALYSIS_JS, '_wds_analysis', array(
			'nonce' => wp_create_nonce( 'wds-metabox-nonce' ),
		) );
	}

	private function register_term_form_scripts() {
		$this->register_js( self::TERM_FORM_JS, 'js/wds-term-form.js', array(
				'jquery',
				self::ADMIN_JS,
				self::OPENGRAPH_JS,
			)
		);
		wp_localize_script( self::TERM_FORM_JS, '_wds_term_form', array(
			'nonce' => wp_create_nonce( 'wds-metabox-nonce' ),
		) );
	}

	private function register_general_styles() {
		$this->register_css( self::QTIP2_CSS, 'css/external/jquery.qtip.min.css' );

		$this->register_css( self::APP_CSS, 'css/app.css' );
	}

	private function register_wp_dashboard_styles() {
		$this->register_css( self::WP_DASHBOARD_CSS, 'css/wp-dashboard.css', array() );
	}

	private function register_post_list_styles() {
		$this->register_css(
			self::WP_POSTLIST_ANALYSIS_CSS, 'css/wds-admin-analysis-postlist.css', array( self::QTIP2_CSS )
		);
	}
}

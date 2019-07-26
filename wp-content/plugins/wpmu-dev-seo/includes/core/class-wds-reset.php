<?php

class Smartcrawl_Reset extends Smartcrawl_Model_IO {

	public static function reset() {
		$me = new self();

		return $me->_reset();
	}

	private function _reset() {
		foreach ( $this->get_sections() as $section ) {
			if ( ! is_callable( array( $this, "reset_{$section}" ) ) ) {
				continue;
			}
			call_user_func( array( $this, "reset_{$section}" ) );
		}
	}

	public function reset_options() {
		delete_site_option( 'wds_blog_tabs' );
		delete_site_option( 'wds_sitewide_mode' );

		delete_option( 'wds_engine_notification' );
		delete_option( 'wds_sitemap_dashboard' );

		delete_option( Smartcrawl_Controller_Checkup_Progress::FAKE_PROGRESS_OPTION );
		delete_option( Smartcrawl_Controller_Checkup_Progress::CHECKUP_PROGRESS_OPTION );
		delete_site_option( Smartcrawl_Controller_Checkup_Progress::FAKE_PROGRESS_OPTION );
		delete_site_option( Smartcrawl_Controller_Checkup_Progress::CHECKUP_PROGRESS_OPTION );

		delete_site_option( 'wds-onboarding-done' );
		delete_site_option( 'wds-free-install-date' );

		delete_site_option( Smartcrawl_Xml_Sitemap::SITEMAP_PRISTINE_OPTION );

		return Smartcrawl_Settings::reset_options();
	}

	public function reset_ignores() {
		delete_site_option( Smartcrawl_Model_Ignores::IGNORES_STORAGE );
		delete_option( Smartcrawl_Model_Ignores::IGNORES_STORAGE );
	}

	public function reset_extra_urls() {
		delete_site_option( Smartcrawl_Xml_Sitemap::EXTRAS_STORAGE );
		delete_option( Smartcrawl_Xml_Sitemap::EXTRAS_STORAGE );
	}

	public function reset_postmeta() {
		global $wpdb;
		$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_wds%'" );
	}

	public function reset_taxmeta() {
		delete_site_option( 'wds_taxonomy_meta' );
		delete_option( 'wds_taxonomy_meta' );
	}

	public function reset_redirects() {
		delete_site_option( Smartcrawl_Model_Redirection::OPTIONS_KEY );
		delete_option( Smartcrawl_Model_Redirection::OPTIONS_KEY );

		delete_site_option( Smartcrawl_Model_Redirection::OPTIONS_KEY_TYPES );
		delete_option( Smartcrawl_Model_Redirection::OPTIONS_KEY_TYPES );
	}

}

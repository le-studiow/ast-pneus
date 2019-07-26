<?php
// phpcs:ignoreFile -- Contains a simple array of mappings

return array(
	/**
	 * General Settings > General Settings
	 */
	'aioseop_options/aiosp_schema_markup'                                                          => '!wds_social_options/disable-schema',

	/**
	 * General Settings > Home Page Settings
	 */
	'aioseop_options/aiosp_home_title'                                                             => 'wds_onpage_options/title-home',
	'aioseop_options/aiosp_home_description'                                                       => 'wds_onpage_options/metadesc-home',
	'aioseop_options/aiosp_home_keywords'                                                          => 'wds_onpage_options/keywords-home',
	'aioseop_options/aiosp_use_static_home_info'                                                   => false,
	// aiosp_use_static_home_info does not need to be handled because front page info is always used in SmartCrawl

	/**
	 * General Settings > Title Settings
	 */
	'aioseop_options/aiosp_home_page_title_format'                                                 => 'wds_onpage_options/title-home',
	'aioseop_options/aiosp_POSTTYPE_title_format'                                                  => 'wds_onpage_options/title-POSTTYPE',
	'aioseop_options/aiosp_TAXONOMY_tax_title_format'                                              => 'wds_onpage_options/title-TAXONOMY',
	'aioseop_options/aiosp_category_title_format'                                                  => 'wds_onpage_options/title-category',
	'aioseop_options/aiosp_tag_title_format'                                                       => 'wds_onpage_options/title-post_tag',
	'aioseop_options/aiosp_archive_title_format'                                                   => 'wds_onpage_options/title-archive',
	'aioseop_options/aiosp_date_title_format'                                                      => 'wds_onpage_options/title-date',
	'aioseop_options/aiosp_author_title_format'                                                    => 'wds_onpage_options/title-author',
	'aioseop_options/aiosp_search_title_format'                                                    => 'wds_onpage_options/title-search',
	'aioseop_options/aiosp_404_title_format'                                                       => 'wds_onpage_options/title-404',

	/**
	 * General Settings > Display Settings
	 */
	'aioseop_options/aiosp_admin_bar'                                                              => '!!wds_settings_options/extras-admin_bar',

	/**
	 * General Settings > Webmaster Verification
	 */
	'aioseop_options/aiosp_google_verify'                                                          => 'wds_sitemap_options/verification-google-meta',
	'aioseop_options/aiosp_bing_verify'                                                            => 'wds_sitemap_options/verification-bing-meta',
	'aioseop_options/aiosp_pinterest_verify'                                                       => 'wds_social_options/pinterest-verify',

	/**
	 * General Settings > Noindex Settings
	 */
	'aioseop_options/aiosp_cpostnoindex'                                                           => false,
	'aioseop_options/aiosp_cpostnofollow'                                                          => false,
	'aioseop_options/aiosp_tax_noindex'                                                            => false,
	'aioseop_options/aiosp_category_noindex'                                                       => '!!wds_onpage_options/meta_robots-noindex-category',
	'aioseop_options/aiosp_archive_date_noindex'                                                   => '!!wds_onpage_options/meta_robots-noindex-date',
	'aioseop_options/aiosp_archive_author_noindex'                                                 => '!!wds_onpage_options/meta_robots-noindex-author',
	'aioseop_options/aiosp_tags_noindex'                                                           => '!!wds_onpage_options/meta_robots-noindex-post_tag',
	'aioseop_options/aiosp_search_noindex'                                                         => '!!wds_onpage_options/meta_robots-noindex-search',

	/**
	 * General Settings > Advanced Settings
	 */
	'aioseop_options/aiosp_redirect_attachement_parent'                                            => '!!wds_autolinks_options/redirect-attachments',

	/**
	 * Social Meta > Home Page Settings
	 */
	'aioseop_options/modules/aiosp_opengraph_options/aiosp_opengraph_sitename'                     => 'wds_social_options/sitename',
	'aioseop_options/modules/aiosp_opengraph_options/aiosp_opengraph_hometitle'                    => 'wds_onpage_options/og-title-home',
	'aioseop_options/modules/aiosp_opengraph_options/aiosp_opengraph_description'                  => 'wds_onpage_options/og-description-home',
	'aioseop_options/modules/aiosp_opengraph_options/aiosp_opengraph_homeimage'                    => 'wds_onpage_options/og-images-home[]',

	/**
	 * Social Meta > Social Profile Links
	 */
	'aioseop_options/modules/aiosp_opengraph_options/aiosp_opengraph_profile_links'                => false,
	'aioseop_options/modules/aiosp_opengraph_options/aiosp_opengraph_person_or_org'                => 'wds_social_options/schema_type',
	'aioseop_options/modules/aiosp_opengraph_options/aiosp_opengraph_social_name'                  => false,

	/**
	 * Social Meta > Facebook Settings
	 */
	'aioseop_options/modules/aiosp_opengraph_options/aiosp_opengraph_appid'                        => 'wds_social_options/fb-app-id',

	/**
	 * Social Meta > Twitter Settings
	 */
	'aioseop_options/modules/aiosp_opengraph_options/aiosp_opengraph_defcard'                      => 'wds_social_options/twitter-card-type',
	'aioseop_options/modules/aiosp_opengraph_options/aiosp_opengraph_twitter_site'                 => 'wds_social_options/twitter_username',

	/**
	 * XML Sitemap > Excluded Items
	 */
	'aioseop_options/modules/aiosp_sitemap_options/aiosp_sitemap_excl_pages'                       => false,
	'aioseop_options/modules/aiosp_sitemap_options/aiosp_sitemap_addl_pages'                       => false,

	/**
	 * Feature Manager
	 */
	'aioseop_options/aiosp_rewrite_titles'                                                         => '!!wds_settings_options/onpage',
	'aioseop_options/modules/aiosp_feature_manager_options/aiosp_feature_manager_enable_sitemap'   => '!!wds_settings_options/sitemap',
	'aioseop_options/modules/aiosp_feature_manager_options/aiosp_feature_manager_enable_opengraph' => '!!wds_settings_options/social',
);

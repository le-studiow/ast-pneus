<?php

return array(
	/**
	 * YOAST > GENERAL
	 */
	// Features
	'wpseo/keyword_analysis_active'            => 'wds_settings_options/analysis-seo',
	'wpseo/content_analysis_active'            => 'wds_settings_options/analysis-readability',
	'wpseo/enable_xml_sitemap'                 => 'wds_settings_options/sitemap',
	'wpseo/enable_admin_bar_menu'              => 'wds_settings_options/extras-admin_bar',
	//Webmaster Tools
	'wpseo/googleverify'                       => 'wds_sitemap_options/verification-google-meta',
	'wpseo/msverify'                           => 'wds_sitemap_options/verification-bing-meta',

	/**
	 * YOAST > SEARCH APPEARANCE
	 */
	// General
	'wpseo_titles/separator'                   => 'wds_onpage_options/preset-separator',
	'wpseo_titles/title-home-wpseo'            => 'wds_onpage_options/title-home',
	'wpseo_titles/metadesc-home-wpseo'         => 'wds_onpage_options/metadesc-home',
	'wpseo_titles/company_logo'                => 'wds_social_options/organization_logo',
	'wpseo_titles/company_name'                => 'wds_social_options/organization_name',
	'wpseo_titles/company_or_person'           => 'wds_social_options/schema_type',
	'wpseo_titles/person_name'                 => 'wds_social_options/override_name',
	'wpseo_titles/website_name'                => 'wds_social_options/sitename',
	// Content types
	'wpseo_titles/noindex-POSTTYPE'            => false,
	'wpseo_titles/title-POSTTYPE'              => 'wds_onpage_options/title-POSTTYPE',
	'wpseo_titles/metadesc-POSTTYPE'           => 'wds_onpage_options/metadesc-POSTTYPE',
	// Media
	'wpseo_titles/disable-attachment'          => 'wds_autolinks_options/redirect-attachments',
	// Taxonomies
	'wpseo_titles/noindex-tax-TAXONOMY'        => false,
	'wpseo_titles/title-tax-TAXONOMY'          => 'wds_onpage_options/title-TAXONOMY',
	'wpseo_titles/metadesc-tax-TAXONOMY'       => 'wds_onpage_options/metadesc-TAXONOMY',
	// Archives
	'wpseo_titles/disable-author'              => '!wds_onpage_options/enable-author-archive',
	'wpseo_titles/noindex-author-wpseo'        => 'wds_onpage_options/meta_robots-noindex-author',
	'wpseo_titles/title-author-wpseo'          => 'wds_onpage_options/title-author',
	'wpseo_titles/metadesc-author-wpseo'       => 'wds_onpage_options/metadesc-author',
	'wpseo_titles/disable-date'                => '!wds_onpage_options/enable-date-archive',
	'wpseo_titles/noindex-archive-wpseo'       => 'wds_onpage_options/meta_robots-noindex-date',
	'wpseo_titles/title-archive-wpseo'         => 'wds_onpage_options/title-date',
	'wpseo_titles/metadesc-archive-wpseo'      => 'wds_onpage_options/metadesc-date',
	'wpseo_titles/title-search-wpseo'          => 'wds_onpage_options/title-search',
	'wpseo_titles/title-404-wpseo'             => 'wds_onpage_options/title-404',
	'wpseo_titles/title-ptarchive-POSTTYPE'    => false,
	'wpseo_titles/metadesc-ptarchive-POSTTYPE' => false,

	/**
	 * YOAST > SOCIAL
	 */
	'wpseo_social/facebook_site'               => 'wds_social_options/facebook_url',
	'wpseo_social/instagram_url'               => 'wds_social_options/instagram_url',
	'wpseo_social/linkedin_url'                => 'wds_social_options/linkedin_url',
	'wpseo_social/opengraph'                   => 'wds_social_options/og-enable',
	'wpseo_social/pinterest_url'               => 'wds_social_options/pinterest_url',
	'wpseo_social/pinterestverify'             => 'wds_social_options/pinterest-verify',
	'wpseo_social/twitter'                     => 'wds_social_options/twitter-card-enable',
	'wpseo_social/twitter_site'                => 'wds_social_options/twitter_username',
	'wpseo_social/twitter_card_type'           => 'wds_social_options/twitter-card-type',
	'wpseo_social/youtube_url'                 => 'wds_social_options/youtube_url',
	'wpseo_social/fbadminapp'                  => 'wds_social_options/fb-app-id',
	'wpseo_social/og_frontpage_title'          => 'wds_onpage_options/og-title-home',
	'wpseo_social/og_frontpage_desc'           => 'wds_onpage_options/og-description-home',
	'wpseo_social/og_frontpage_image'          => 'wds_onpage_options/og-images-home[]',

	/**
	 * YOAST > REDIRECTS
	 */
	// Redirects are difficult to express as simple mappings so they will have to be handled in code.
);

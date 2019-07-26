<?php
/**
 * Plugin configuration constants,
 * legacy data propagation layer (pro only).
 *
 * @package wpmu-dev-seo
 */

/**
 * Autolinks module contains code from SEO Smart Links plugin
 * (http://wordpress.org/extend/plugins/seo-automatic-links/ and http://www.prelovac.com/products/seo-smart-links/)
 * by Vladimir Prelovac (http://www.prelovac.com/).
 */

if ( defined( 'WDS_SITEWIDE' ) ) {
	define( 'SMARTCRAWL_SITEWIDE', WDS_SITEWIDE );
}
if ( defined( 'WDS_SITEMAP_POST_LIMIT' ) ) {
	define( 'SMARTCRAWL_SITEMAP_POST_LIMIT', WDS_SITEMAP_POST_LIMIT );
}
if ( defined( 'WDS_BP_GROUPS_LIMIT' ) ) {
	define( 'SMARTCRAWL_BP_GROUPS_LIMIT', WDS_BP_GROUPS_LIMIT );
}
if ( defined( 'WDS_BP_PROFILES_LIMIT' ) ) {
	define( 'SMARTCRAWL_BP_PROFILES_LIMIT', WDS_BP_PROFILES_LIMIT );
}
if ( defined( 'WDS_EXPIRE_TRANSIENT_TIMEOUT' ) ) {
	define( 'SMARTCRAWL_EXPIRE_TRANSIENT_TIMEOUT', WDS_EXPIRE_TRANSIENT_TIMEOUT );
}
if ( defined( 'WDS_AUTOLINKS_DEFAULT_CHAR_LIMIT' ) ) {
	define( 'SMARTCRAWL_AUTOLINKS_DEFAULT_CHAR_LIMIT', WDS_AUTOLINKS_DEFAULT_CHAR_LIMIT );
}
if ( defined( 'WDS_TITLE_LENGTH_CHAR_COUNT_LIMIT' ) ) {
	define( 'SMARTCRAWL_TITLE_LENGTH_CHAR_COUNT_LIMIT', WDS_TITLE_LENGTH_CHAR_COUNT_LIMIT );
}
if ( defined( 'WDS_METADESC_LENGTH_CHAR_COUNT_LIMIT' ) ) {
	define( 'SMARTCRAWL_METADESC_LENGTH_CHAR_COUNT_LIMIT', WDS_METADESC_LENGTH_CHAR_COUNT_LIMIT );
}
if ( defined( 'WDS_SITEMAP_SKIP_IMAGES' ) ) {
	define( 'SMARTCRAWL_SITEMAP_SKIP_IMAGES', WDS_SITEMAP_SKIP_IMAGES );
}
if ( defined( 'WDS_SITEMAP_SKIP_TAXONOMIES' ) ) {
	define( 'SMARTCRAWL_SITEMAP_SKIP_TAXONOMIES', WDS_SITEMAP_SKIP_TAXONOMIES );
}
if ( defined( 'WDS_SITEMAP_SKIP_SE_NOTIFICATION' ) ) {
	define( 'SMARTCRAWL_SITEMAP_SKIP_SE_NOTIFICATION', WDS_SITEMAP_SKIP_SE_NOTIFICATION );
}
if ( defined( 'WDS_SITEMAP_SKIP_ADMIN_UPDATE' ) ) {
	define( 'SMARTCRAWL_SITEMAP_SKIP_ADMIN_UPDATE', WDS_SITEMAP_SKIP_ADMIN_UPDATE );
}
if ( defined( 'WDS_EXPERIMENTAL_FEATURES_ON' ) ) {
	define( 'SMARTCRAWL_EXPERIMENTAL_FEATURES_ON', WDS_EXPERIMENTAL_FEATURES_ON );
}
if ( defined( 'WDS_ENABLE_LOGGING' ) ) {
	define( 'SMARTCRAWL_ENABLE_LOGGING', WDS_ENABLE_LOGGING );
}
if ( defined( 'WDS_WHITELABEL_ON' ) ) {
	define( 'SMARTCRAWL_WHITELABEL_ON', WDS_WHITELABEL_ON );
}
if ( defined( 'WDS_OMIT_PORT_MATCHES' ) ) {
	define( 'SMARTCRAWL_OMIT_PORT_MATCHES', WDS_OMIT_PORT_MATCHES );
}

define( 'SMARTCRAWL_PROJECT_TITLE', 'SmartCrawl https://premium.wpmudev.org/project/smartcrawl-wordpress-seo/' );

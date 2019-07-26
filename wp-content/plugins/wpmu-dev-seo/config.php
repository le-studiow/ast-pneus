<?php
/**
 * Plugin configuration constants
 *
 * @package wpmu-dev-seo
 */

// First up, try to inherit define flags named with legacy prefix.
if ( file_exists( dirname( __FILE__ ) . '/config-pro.php' ) ) {
	require_once dirname( __FILE__ ) . '/config-pro.php';
}

/*
By this time, any legacy defines are re-defined with new prefix.
We can carry on as if the new defines have been used all along.
*/

// you can override this in wp-config.php to enable blog-by-blog settings in multisite.
if ( ! defined( 'SMARTCRAWL_SITEWIDE' ) ) {
	define( 'SMARTCRAWL_SITEWIDE', get_site_option( 'wds_sitewide_mode', true ) );
}

// you can override this in wp-config.php to enable more posts in the sitemap, but you may need alot of memory.
if ( ! defined( 'SMARTCRAWL_SITEMAP_POST_LIMIT' ) ) {
	define( 'SMARTCRAWL_SITEMAP_POST_LIMIT', 1000 );
}

// you can override this in wp-config.php to enable more BuddyPress groups in the sitemap, but you may need alot of memory.
if ( ! defined( 'SMARTCRAWL_BP_GROUPS_LIMIT' ) ) {
	define( 'SMARTCRAWL_BP_GROUPS_LIMIT', 200 );
}

// you can override this in wp-config.php to enable more BuddyPress profiles in the sitemap, but you may need alot of memory.
if ( ! defined( 'SMARTCRAWL_BP_PROFILES_LIMIT' ) ) {
	define( 'SMARTCRAWL_BP_PROFILES_LIMIT', 200 );
}

// You can override this value in wp-config.php to allow more or less time for caching SEOmoz results.
if ( ! defined( 'SMARTCRAWL_EXPIRE_TRANSIENT_TIMEOUT' ) ) {
	define( 'SMARTCRAWL_EXPIRE_TRANSIENT_TIMEOUT', 3600 );
}

// You can override this value in wp-config.php to allow for longer or shorter minimum autolink requirement.
if ( ! defined( 'SMARTCRAWL_AUTOLINKS_DEFAULT_CHAR_LIMIT' ) ) {
	define( 'SMARTCRAWL_AUTOLINKS_DEFAULT_CHAR_LIMIT', 3 );
}

// Suppress redundant canonicals?
// if ( ! defined( 'SMARTCRAWL_SUPPRESS_REDUNDANT_CANONICAL' ) ) define( 'SMARTCRAWL_SUPPRESS_REDUNDANT_CANONICAL', false );
// Char counting defines.
if ( ! defined( 'SMARTCRAWL_TITLE_LENGTH_CHAR_COUNT_LIMIT' ) ) {
	define( 'SMARTCRAWL_TITLE_LENGTH_CHAR_COUNT_LIMIT', 70 );
}
if ( ! defined( 'SMARTCRAWL_METADESC_LENGTH_CHAR_COUNT_LIMIT' ) ) {
	define( 'SMARTCRAWL_METADESC_LENGTH_CHAR_COUNT_LIMIT', 300 );
}

// Debugging defines.
if ( ! defined( 'SMARTCRAWL_SITEMAP_SKIP_IMAGES' ) ) {
	define( 'SMARTCRAWL_SITEMAP_SKIP_IMAGES', false );
}
if ( ! defined( 'SMARTCRAWL_SITEMAP_SKIP_TAXONOMIES' ) ) {
	define( 'SMARTCRAWL_SITEMAP_SKIP_TAXONOMIES', false );
}
if ( ! defined( 'SMARTCRAWL_SITEMAP_SKIP_SE_NOTIFICATION' ) ) {
	define( 'SMARTCRAWL_SITEMAP_SKIP_SE_NOTIFICATION', false );
}
if ( ! defined( 'SMARTCRAWL_SITEMAP_SKIP_ADMIN_UPDATE' ) ) {
	define( 'SMARTCRAWL_SITEMAP_SKIP_ADMIN_UPDATE', false );
}

if ( ! defined( 'SMARTCRAWL_EXPERIMENTAL_FEATURES_ON' ) ) {
	define( 'SMARTCRAWL_EXPERIMENTAL_FEATURES_ON', false );
}
if ( ! defined( 'SMARTCRAWL_ENABLE_LOGGING' ) ) {
	define( 'SMARTCRAWL_ENABLE_LOGGING', false );
}

if ( ! defined( 'SMARTCRAWL_WHITELABEL_ON' ) ) {
	define( 'SMARTCRAWL_WHITELABEL_ON', false );
}
if ( ! defined( 'SMARTCRAWL_OMIT_PORT_MATCHES' ) ) {
	define( 'SMARTCRAWL_OMIT_PORT_MATCHES', true );
}
if ( ! defined( 'SMARTCRAWL_ANALYSIS_REQUEST_TIMEOUT' ) ) {
	define( 'SMARTCRAWL_ANALYSIS_REQUEST_TIMEOUT', 5 );
}

/**
 * Setup plugin path and url.
 */
define( 'SMARTCRAWL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) . 'includes/' );
define( 'SMARTCRAWL_PLUGIN_URL', plugin_dir_url( __FILE__ ) . 'includes/' );

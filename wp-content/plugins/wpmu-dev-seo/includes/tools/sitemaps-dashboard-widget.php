<?php

/**
 * Init WDS Sitemaps Dashboard Widget
 */
class Smartcrawl_Sitemaps_Dashboard_Widget extends Smartcrawl_Base_Controller {

	/**
	 * Static instance
	 *
	 * @var Smartcrawl_Sitemaps_Dashboard_Widget
	 */
	private static $_instance;

	public function should_run() {
		return Smartcrawl_Settings::get_setting( 'sitemap' )
		       && smartcrawl_is_allowed_tab( Smartcrawl_Settings::TAB_SITEMAP );
	}

	protected function init() {
		add_action( 'wp_dashboard_setup', array( &$this, 'dashboard_widget' ) );
	}

	/**
	 * Static instance getter
	 */
	public static function get() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Dashboard Widget
	 */
	public function dashboard_widget() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return false;
		}
		wp_add_dashboard_widget( 'wds_sitemaps_dashboard_widget', __( 'Sitemaps - SmartCrawl', 'wds' ), array(
			&$this,
			'widget',
		) );
	}

	/**
	 * Widget
	 */
	public function widget() {
		$sitemap_options = Smartcrawl_Settings::get_options();
		$sitemap_stats = get_option( 'wds_sitemap_dashboard' );
		$engines = get_option( 'wds_engine_notification' );
		$last_update_date = ! empty( $sitemap_stats['time'] ) ? date_i18n( get_option( 'date_format' ), $sitemap_stats['time'] ) : false;
		$last_update_time = ! empty( $sitemap_stats['time'] ) ? date_i18n( get_option( 'time_format' ), $sitemap_stats['time'] ) : false;
		$last_update_timestamp = ( $last_update_date && $last_update_time )
			? sprintf( esc_html__( 'It was last updated on %1$s, at %2$s.', 'wds' ), $last_update_date, $last_update_time )
			: esc_html__( "Your sitemap hasn't been updated recently.", 'wds' );
		$se_notifications_enabled = (boolean) smartcrawl_get_array_value( $sitemap_options, 'ping-google' )
		                            || (boolean) smartcrawl_get_array_value( $sitemap_options, 'ping-bing' );

		Smartcrawl_Simple_Renderer::render( 'wp-dashboard/sitemaps-widget', array(
			'engines'                  => $engines,
			'sitemap_stats'            => $sitemap_stats,
			'last_update_date'         => $last_update_date,
			'last_update_time'         => $last_update_time,
			'last_update_timestamp'    => $last_update_timestamp,
			'se_notifications_enabled' => $se_notifications_enabled,
		) );

		Smartcrawl_Simple_Renderer::render( 'wp-dashboard/sitemaps-widget-js', array(
			'updating'  => __( 'Updating...', 'wds' ),
			'updated'   => __( 'Done updating the sitemap, please hold on...', 'wds' ),
			'notifying' => __( 'Notifying...', 'wds' ),
			'notified'  => __( 'Done notifying search engines, please hold on...', 'wds' ),
		) );
	}

}

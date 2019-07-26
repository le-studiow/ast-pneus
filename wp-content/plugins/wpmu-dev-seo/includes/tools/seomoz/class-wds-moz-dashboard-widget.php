<?php

/**
 * Init WDS SEOMoz Dashboard Widget
 */
class Smartcrawl_Moz_Dashboard_Widget extends Smartcrawl_Base_Controller {

	/**
	 * Static instance
	 *
	 * @var self
	 */
	private static $_instance;

	public function should_run() {
		return smartcrawl_is_allowed_tab( Smartcrawl_Settings::TAB_AUTOLINKS )
		       && Smartcrawl_Settings::get_setting( 'access-id' )
		       && Smartcrawl_Settings::get_setting( 'secret-key' );
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
	 * Widget
	 */
	public static function widget() {
		$renderer = new Smartcrawl_Moz_Results_Renderer();
		?>
		<div class="<?php echo esc_attr( smartcrawl_sui_class() ); ?>">
			<div class="sui-wrap">
				<?php $renderer->render(
					get_bloginfo( 'url' ),
					'seomoz-dashboard-widget'
				); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Dashboard Widget
	 */
	public function dashboard_widget() {

		if ( ! current_user_can( 'edit_posts' ) ) {
			return false;
		}
		wp_add_dashboard_widget( 'wds_seomoz_dashboard_widget', __( 'Moz - SmartCrawl', 'wds' ), array(
			&$this,
			'widget',
		) );

	}

}

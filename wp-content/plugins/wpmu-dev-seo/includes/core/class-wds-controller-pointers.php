<?php

class Smartcrawl_Controller_Pointers extends Smartcrawl_Base_Controller {
	/**
	 * Singleton instance holder
	 *
	 * @var Smartcrawl_Controller_Pointers
	 */
	private static $_instance;

	/**
	 * Obtain instance without booting up
	 *
	 * @return Smartcrawl_Controller_Pointers instance
	 */
	public static function get() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Bind listening actions
	 */
	public function init() {
		if ( get_bloginfo( 'version' ) < '3.3' ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'admin_footer', array( $this, 'print_styles' ) );
		// add_action( 'wds_admin_pointers-plugins', array( $this, 'smartcrawl_activation_pointer' ) );
	}

	public function enqueue() {
		if ( ! $this->get_valid_pointers() ) {
			return;
		}

		wp_enqueue_script( 'wp-pointer' );
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script(
			'wds-admin-pointers',
			SMARTCRAWL_PLUGIN_URL . 'assets/js/wds-admin-pointers.js',
			array( 'jquery' ),
			Smartcrawl_Loader::get_version()
		);
		wp_localize_script( 'wds-admin-pointers', '_wds_pointers', $this->get_valid_pointers() );
	}

	private function get_valid_pointers() {
		$dismissed = explode(
			',',
			(string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
		// Get the screen ID
		$screen = get_current_screen();
		$screen_id = $screen->id;
		$pointers = apply_filters( 'wds_admin_pointers-' . $screen_id, array() );

		// Check pointers and remove dismissed ones.
		foreach ( $pointers as $pointer_id => $pointer ) {

			// Sanity check
			if ( in_array( $pointer_id, $dismissed, true ) || empty( $pointer ) || empty( $pointer_id ) || empty( $pointer['target'] ) || empty( $pointer['options'] ) ) {
				continue;
			}

			$pointer['pointer_id'] = $pointer_id;

			// Add the pointer to $valid_pointers array
			$valid_pointers['pointers'][ $pointer_id ] = $pointer;
		}

		// No valid pointers? Stop here.
		if ( empty( $valid_pointers ) ) {
			return array();
		} else {
			return $valid_pointers;
		}
	}

	public function smartcrawl_activation_pointer( $pointers ) {
		$pointers['wds-activation-pointer'] = array(
			'target'  => '#toplevel_page_wds_wizard',
			'options' => array(
				'content'  => sprintf( '<h3> %s </h3> <p> %s </p>',
					esc_html__( 'Optimize your SEO', 'wds' ),
					esc_html__( 'Configure your SEO Titles & Meta, enable OpenGraph and activate readability analysis here.', 'wds' )
				),
				'position' => array(
					'edge'  => 'left',
					'align' => 'right',
				),
			),
		);

		return $pointers;
	}

	public function print_styles() {
		if ( ! $this->get_valid_pointers() ) {
			return;
		}

		?>
		<style>
			@media screen and (max-width: 782px) {
				.wds-pointer {
					display: none !important;
				}
			}
		</style>
		<?php
	}
}

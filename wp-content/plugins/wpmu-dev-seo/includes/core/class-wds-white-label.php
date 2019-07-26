<?php

class Smartcrawl_White_Label extends Smartcrawl_Base_Controller {
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
		add_action( 'admin_enqueue_scripts', array( $this, 'add_inline_styling' ), 15 );

		return true;
	}

	public function add_inline_styling() {
		$new_image = $this->get_wpmudev_hero_image( '' );
		if ( $this->is_hide_wpmudev_branding() && $new_image ) {
			wp_add_inline_style(
				Smartcrawl_Controller_Assets::APP_CSS,
				".wrap-wds .sui-rebranded .sui-summary-image-space {
					background-image: url('{$new_image}');
				}"
			);
		}

		if ( $this->is_hide_wpmudev_doc_link() ) {
			wp_add_inline_style(
				Smartcrawl_Controller_Assets::APP_CSS,
				".wrap-wds .sui-header .sui-actions-right .wds-docs-button {
					display: none;
				}"
			);
		}


	}

	public function get_wpmudev_hero_image( $hero_image ) {
		return apply_filters( 'wpmudev_branding_hero_image', $hero_image );
	}

	public function is_hide_wpmudev_branding() {
		return apply_filters( 'wpmudev_branding_hide_branding', false );
	}

	public function is_hide_wpmudev_doc_link() {
		return apply_filters( 'wpmudev_branding_hide_doc_link', false );
	}

	public function get_wpmudev_footer_text( $footer_text ) {
		return apply_filters( 'wpmudev_branding_footer_text', $footer_text );
	}

	public function summary_class() {
		if ( ! $this->is_hide_wpmudev_branding() ) {
			return '';
		}

		return $this->get_wpmudev_hero_image( '' )
			? 'sui-rebranded'
			: 'sui-unbranded';
	}
}

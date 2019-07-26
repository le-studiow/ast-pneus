<?php

class Smartcrawl_Social_Settings extends Smartcrawl_Settings_Admin {


	private static $_instance;

	public static function get_instance() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Validate submitted options
	 *
	 * @param array $input Raw input
	 *
	 * @return array Validated input
	 */
	public function validate( $input ) {
		$result = array();

		if ( ! empty( $input['wds_social-setup'] ) ) {
			$result['wds_social-setup'] = true;
		}

		$result['disable-schema'] = ! empty( $input['disable-schema'] );

		$urls = array(
			'facebook_url',
			'instagram_url',
			'linkedin_url',
			'pinterest_url',
			'youtube_url',
		);
		foreach ( $urls as $type ) {
			if ( empty( $input[ $type ] ) ) {
				continue;
			}
			$social_url = trim( $input[ $type ] );
			if ( ! preg_match( '/^https?:\/\//', $social_url ) ) {
				add_settings_error(
					$this->option_name,
					'social_url_invalid',
					esc_html__( 'Some social URLs could not be saved. Please try again.', 'wds' )
				);
				continue;
			}
			$result[ $type ] = $social_url;
		}

		if ( ! empty( $input['sitename'] ) ) {
			$result['sitename'] = sanitize_text_field( $input['sitename'] );
		}
		if ( ! empty( $input['override_name'] ) ) {
			$result['override_name'] = sanitize_text_field( $input['override_name'] );
		}
		if ( ! empty( $input['organization_name'] ) ) {
			$result['organization_name'] = sanitize_text_field( $input['organization_name'] );
		}
		if ( ! empty( $input['organization_logo'] ) ) {
			$result['organization_logo'] = sanitize_text_field( $input['organization_logo'] );
		}
		if ( ! empty( $input['schema_type'] ) ) {
			$result['schema_type'] = sanitize_text_field( $input['schema_type'] );
		}
		if ( ! empty( $input['twitter_username'] ) ) {
			$result['twitter_username'] = sanitize_text_field( $input['twitter_username'] );
		}
		if ( ! empty( $input['twitter-card-type'] ) ) {
			$result['twitter-card-type'] = sanitize_text_field( $input['twitter-card-type'] );
		}
		if ( ! empty( $input['fb-app-id'] ) ) {
			$result['fb-app-id'] = sanitize_text_field( $input['fb-app-id'] );
		}

		$result['og-enable'] = ! empty( $input['og-enable'] );
		$result['twitter-card-enable'] = ! empty( $input['twitter-card-enable'] );

		$this->_toggle_og_globally(
			$result['og-enable']
		);

		$this->_toggle_twitter_cards_globally(
			$result['twitter-card-enable']
		);

		if ( ! empty( $input['pinterest-verify'] ) ) {
			$pin = Smartcrawl_Pinterest_Printer::get();
			$raw = trim( $input['pinterest-verify'] );
			$tag = $pin->get_verified_tag( $raw );
			$result['pinterest-verify'] = str_replace( ' ', '', $raw ) === str_replace( ' ', '', $tag ) ? $tag : false;
			$result['pinterest-verification-status'] = str_replace( ' ', '', $raw ) === str_replace( ' ', '', $tag ) ? '' : 'fail';
		} else {
			$result['pinterest-verification-status'] = false;
		}

		return $result;
	}

	private function _toggle_og_globally( $new_value ) {
		$this->toggle_setting_globally( 'og-active', $new_value );
	}

	private function toggle_setting_globally( $prefix, $new_value ) {
		$existing_settings = Smartcrawl_Settings::get_specific_options( 'wds_onpage_options' );
		$strings = array(
			'home',
			'author',
			'date',
			'search',
			'404',
			'category',
			'post_tag',
			'bp_groups',
			'bp_profile',
		);

		foreach ( get_taxonomies( array( '_builtin' => false ), 'objects' ) as $taxonomy ) {
			$strings[] = $taxonomy->name;
		}

		foreach ( get_post_types( array( 'public' => true ) ) as $post_type ) {
			$strings[] = $post_type;
		}

		foreach ( smartcrawl_get_archive_post_types() as $archive_post_type ) {
			$strings[] = $archive_post_type;
		}

		foreach ( $strings as $string ) {
			$existing_settings[ sprintf( '%s-%s', $prefix, $string ) ] = $new_value;
		}

		Smartcrawl_Settings::update_specific_options( 'wds_onpage_options', $existing_settings );
	}

	private function _toggle_twitter_cards_globally( $new_value ) {
		$this->toggle_setting_globally( 'twitter-active', $new_value );
	}

	public function init() {
		$this->option_name = 'wds_social_options';
		$this->name = Smartcrawl_Settings::COMP_SOCIAL;
		$this->slug = Smartcrawl_Settings::TAB_SOCIAL;
		$this->action_url = admin_url( 'options.php' );
		$this->page_title = __( 'SmartCrawl Wizard: Social', 'wds' );

		parent::init();
	}

	public function get_title() {
		return __( 'Social', 'wds' );
	}

	/**
	 * Add admin settings page
	 */
	public function options_page() {
		parent::options_page();

		$options = Smartcrawl_Settings::get_component_options( $this->name );
		$options = wp_parse_args(
			( is_array( $options ) ? $options : array() ),
			$this->get_default_options()
		);

		$arguments = array(
			'options' => $options,
		);
		$arguments['active_tab'] = $this->_get_active_tab( 'tab_accounts' );
		wp_enqueue_script( Smartcrawl_Controller_Assets::SOCIAL_PAGE_JS );
		wp_enqueue_media();

		$this->_render_page( 'social/social-settings', $arguments );
	}

	/**
	 * Gets default options set and their initial values
	 *
	 * @return array
	 */
	public function get_default_options() {
		return array(
			// Accounts
			'sitename'            => get_bloginfo( 'name' ),
			'disable-schema'      => false,
			'schema_type'         => '',
			'override_name'       => '',
			'organization_name'   => '',
			'organization_logo'   => '',
			'twitter_username'    => '',
			'facebook_url'        => '',
			'instagram_url'       => '',
			'linkedin_url'        => '',
			'pinterest_url'       => '',
			'youtube_url'         => '',
			// Twitter
			'twitter-card-enable' => false,
			'twitter-card-type'   => '',
			// Pinterest
			'pinterest-verify'    => '',
			// OpenGraph
			'og-enable'           => true,
			// Facebook-specific
			'fb-app-id'           => '',
		);
	}

	/**
	 * Default settings
	 */
	public function defaults() {
		$options = Smartcrawl_Settings::get_component_options( $this->name );
		$options = is_array( $options ) ? $options : array();

		foreach ( $this->get_default_options() as $opt => $default ) {
			if ( ! isset( $options[ $opt ] ) ) {
				$options[ $opt ] = $default;
			}
		}

		if ( is_multisite() && SMARTCRAWL_SITEWIDE ) {
			update_site_option( $this->option_name, $options );
		} else {
			update_option( $this->option_name, $options );
		}
	}

}


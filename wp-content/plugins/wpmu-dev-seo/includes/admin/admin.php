<?php
/**
 * Admin side handling
 *
 * @package wpmu-dev-seo
 */

/**
 * Admin handling root class
 */
class Smartcrawl_Admin extends Smartcrawl_Base_Controller {
	/**
	 * Static instance
	 *
	 * @var self
	 */
	private static $_instance;

	/**
	 * Admin page handlers
	 *
	 * @var array
	 */
	private $_handlers = array();

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
	 * Initializing method
	 */
	protected function init() {
		// Set up dash.
		if ( file_exists( SMARTCRAWL_PLUGIN_DIR . 'external/dash/wpmudev-dash-notification.php' ) ) {
			global $wpmudev_notices;
			if ( ! is_array( $wpmudev_notices ) ) {
				$wpmudev_notices = array();
			}
			$wpmudev_notices[] = array(
				'id'      => 167,
				'name'    => 'SmartCrawl',
				'screens' => array(
					'smartcrawl_page_wds_onpage-network',
					'smartcrawl_page_wds_onpage',
					'smartcrawl_page_wds_sitemap-network',
					'smartcrawl_page_wds_sitemap',
					'smartcrawl_page_wds_settings-network',
					'smartcrawl_page_wds_settings',
					'smartcrawl_page_wds_autolinks-network',
					'smartcrawl_page_wds_autolinks',
					'smartcrawl_page_wds_social-network',
					'smartcrawl_page_wds_social',
				),
			);
			require_once SMARTCRAWL_PLUGIN_DIR . 'external/dash/wpmudev-dash-notification.php';
		}

		add_action( 'admin_init', array( $this, 'register_setting' ) );
		add_action( 'admin_init', array( $this, 'admin_master_reset' ) );
		add_filter( 'whitelist_options', array( $this, 'save_options' ), 20 );
		add_filter( 'load-index.php', array( $this, 'enqueue_dashboard_resources' ), 20 );

		add_action( 'wp_ajax_wds_dismiss_message', array( $this, 'smartcrawl_dismiss_message' ) );

		if ( Smartcrawl_Settings::get_setting( 'extras-admin_bar' ) ) {
			add_action( 'admin_bar_menu', array( $this, 'add_toolbar_items' ), 99 );
		}

		add_filter( 'plugin_action_links_' . SMARTCRAWL_PLUGIN_BASENAME, array( $this, 'add_settings_link' ) );

		// Sanity check first!
		if ( ! get_option( 'blog_public' ) ) {
			add_action( 'admin_notices', array( $this, 'blog_not_public_notice' ) );
		}

		$this->_handlers['dashboard'] = Smartcrawl_Settings_Dashboard::get_instance();

		if ( Smartcrawl_Settings::get_setting( 'checkup' ) ) {
			$this->_handlers['checkup'] = Smartcrawl_Checkup_Settings::get_instance();
		}

		if ( Smartcrawl_Settings::get_setting( 'onpage' ) ) {
			$this->_handlers['onpage'] = Smartcrawl_Onpage_Settings::get_instance();
		}

		if ( Smartcrawl_Settings::get_setting( 'social' ) ) {
			$this->_handlers['social'] = Smartcrawl_Social_Settings::get_instance();
		}

		$this->_handlers['sitemap'] = Smartcrawl_Sitemap_Settings::get_instance();
		$this->_handlers['autolinks'] = Smartcrawl_Autolinks_Settings::get_instance();
		$this->_handlers['settings'] = Smartcrawl_Settings_Settings::get_instance();
	}

	/**
	 * Adds settings plugin action link
	 *
	 * @param array $links Action links list.
	 *
	 * @return array Augmented action links
	 */
	public function add_settings_link( $links ) {
		if ( ! is_array( $links ) ) {
			return $links;
		}

		$links[] = sprintf(
			'<a href="%s">%s</a>',
			esc_url( add_query_arg( 'page', Smartcrawl_Settings::TAB_DASHBOARD, admin_url( 'admin.php' ) ) ),
			esc_html( __( 'Settings', 'wds' ) )
		);

		return $links;
	}

	/**
	 * Saves the submitted options
	 *
	 * @param mixed $whitelist_options Options.
	 *
	 * @return array
	 */
	public function save_options( $whitelist_options ) {
		global $action;

		$smartcrawl_pages = array(
			'wds_settings_options',
			'wds_autolinks_options',
			'wds_onpage_options',
			'wds_sitemap_options',
			'wds_social_options',
			'wds_redirections_options',
			'wds_checkup_options',
		);
		$data = isset( $_POST['_wpnonce'], $_POST['option_page'] ) && wp_verify_nonce( $_POST['_wpnonce'], $_POST['option_page'] . '-options' )
			? stripslashes_deep( $_POST )
			: array();

		if ( is_multisite() && SMARTCRAWL_SITEWIDE && 'update' === $action && in_array( $data['option_page'], $smartcrawl_pages, true ) ) {
			global $option_page;

			check_admin_referer( $option_page . '-options' );

			if ( ! isset( $whitelist_options[ $option_page ] ) ) {
				wp_die( esc_html__( 'Error: options page not found.', 'wds' ) );
			}

			$options = $whitelist_options[ $option_page ];

			if ( $options && is_array( $options ) ) {
				foreach ( $options as $option ) {
					$option = trim( $option );
					$value = null;
					if ( isset( $data[ $option ] ) ) {
						$value = $data[ $option ];
					}
					if ( ! is_array( $value ) ) {
						$value = trim( $value );
					}
					$value = stripslashes_deep( $value );

					// Sanitized/validated via sanitize_option_<option_page>.
					// See each of the admin classes validate method.
					update_site_option( $option, $value );
				}
			}

			$errors = get_settings_errors();
			set_transient( 'wds-settings-save-errors', $errors, 30 );

			$goback = add_query_arg( 'updated', 'true', wp_get_referer() );
			wp_safe_redirect( $goback );
			die;
		}

		return $whitelist_options;
	}

	/**
	 * Admin reset options switch processing
	 *
	 * @return bool|void
	 */
	public function admin_master_reset() {
		if ( is_multisite() && ! current_user_can( 'manage_network_options' ) ) {
			return false;
		}
		if ( ! is_multisite() && ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		// phpcs:ignore
		if ( isset( $_GET['wds-reset'] ) ) { // Simple presence switch, no value needed.
			Smartcrawl_Reset::reset();
			wp_safe_redirect( add_query_arg( 'wds-reset-reload', 'true', remove_query_arg( 'wds-reset' ) ) );
			die;
		}

		// phpcs:ignore
		if ( isset( $_GET['wds-reset-reload'] ) ) { // Simple presence switch, no value needed.
			wp_safe_redirect( remove_query_arg( 'wds-reset-reload' ) );
			die;
		}

		return false;
	}

	/**
	 * Brute-register all the settings.
	 *
	 * If we got this far, this is a sane thing to do.
	 * This overrides the `Smartcrawl_Core_Admin::register_setting()`.
	 *
	 * In response to "Unable to save options multiple times" bug.
	 */
	public function register_setting() {
		register_setting( 'wds_settings_options', 'wds_settings_options', array(
			$this->get_handler( 'settings' ),
			'validate',
		) );
		register_setting( 'wds_sitemap_options', 'wds_sitemap_options', array(
			$this->get_handler( 'sitemap' ),
			'validate',
		) );
		register_setting( 'wds_onpage_options', 'wds_onpage_options', array(
			$this->get_handler( 'onpage' ),
			'validate',
		) );
		register_setting( 'wds_social_options', 'wds_social_options', array(
			$this->get_handler( 'social' ),
			'validate',
		) );
		register_setting( 'wds_autolinks_options', 'wds_autolinks_options', array(
			$this->get_handler( 'autolinks' ),
			'validate',
		) );
		register_setting( 'wds_redirections_options', 'wds_redirections_options', array(
			$this->get_handler( 'redirections' ),
			'validate',
		) );
		register_setting( 'wds_checkup_options', 'wds_checkup_options', array(
			$this->get_handler( 'checkup' ),
			'validate',
		) );
	}

	/**
	 * Admin page handler getter
	 *
	 * @param string $hndl Handler to get.
	 *
	 * @return object Handler
	 */
	public function get_handler( $hndl ) {
		return isset( $this->_handlers[ $hndl ] )
			? $this->_handlers[ $hndl ]
			: $this;
	}

	/**
	 * Adds admin toolbar items
	 *
	 * @param object $admin_bar Admin toolbar object.
	 *
	 * @return bool
	 */
	public function add_toolbar_items( $admin_bar ) {
		if ( empty( $admin_bar ) || ! function_exists( 'is_admin_bar_showing' ) ) {
			return false;
		}
		if ( ! is_admin_bar_showing() ) {
			return false;
		}
		if ( ! apply_filters( 'wds-admin-ui-show_bar', true ) ) {
			return false;
		}
		// Do not show if sitewide and we're not super admin.
		if ( smartcrawl_is_switch_active( 'SMARTCRAWL_SITEWIDE' ) && ! is_super_admin() ) {
			return false;
		}

		$optional_nodes = array();
		foreach ( $this->_handlers as $handler ) {
			if ( empty( $handler ) || empty( $handler->slug ) ) {
				continue;
			}

			if ( ! $this->is_admin_bar_node_allowed( $handler->slug ) ) {
				continue;
			}

			$optional_nodes[] = $this->create_admin_bar_node( $handler->slug, $handler->get_title() );
		}

		if ( ! empty( $optional_nodes ) ) {
			$admin_bar->add_node( $this->create_admin_bar_node( Smartcrawl_Settings::TAB_DASHBOARD, __( 'SmartCrawl', 'wds' ) ) );
			$admin_bar->add_node( $this->create_admin_bar_node( Smartcrawl_Settings::TAB_DASHBOARD . '_dashboard', __( 'Dashboard', 'wds' ), Smartcrawl_Settings::TAB_DASHBOARD ) );
			foreach ( $optional_nodes as $optional_node ) {
				$admin_bar->add_node( $optional_node );
			}
		}

		return true;
	}

	private function is_admin_bar_node_allowed( $slug ) {
		// If sitewide is off
		if ( ! smartcrawl_is_switch_active( 'SMARTCRAWL_SITEWIDE' ) && is_multisite() ) {
			if (
				// and this is network admin then pages other than the settings page are not allowed
				( is_network_admin() && Smartcrawl_Settings::TAB_SETTINGS !== $slug )
				// if this is a sub-site then the page is not allowed if it is disabled in the settings
				|| ( ! is_network_admin() && ! Smartcrawl_Settings_Admin::is_tab_allowed( $slug ) )
			) {
				return false;
			}
		}

		return true;
	}

	private function create_admin_bar_node( $id, $title, $slug = '' ) {
		$node = array(
			'id'    => $id,
			'title' => $title,
			'href'  => sprintf(
				'%s?page=%s',
				smartcrawl_is_switch_active( 'SMARTCRAWL_SITEWIDE' ) ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' ),
				empty( $slug ) ? $id : $slug
			),
		);

		if ( Smartcrawl_Settings::TAB_DASHBOARD !== $id ) {
			$node['parent'] = Smartcrawl_Settings::TAB_DASHBOARD;
		}

		return $node;
	}

	/**
	 * Validate user data for some/all of your input fields
	 *
	 * @param mixed $input Raw input.
	 *
	 * @return mixed
	 */
	public function validate( $input ) {
		return $input; // return validated input.
	}

	/**
	 * Shows blog not being public notice.
	 */
	public function blog_not_public_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$message = sprintf(
			'%1$s <a href="%3$s">%2$s</a>',
			esc_html__( 'This site discourages search engines from indexing the pages, which will affect your SEO efforts.', 'wds' ),
			esc_html__( 'You can fix this here', 'wds' ),
			admin_url( '/options-reading.php' )
		);

		echo '<div class="notice-error notice is-dismissible"><p>' . wp_kses_post( $message ) . '</p></div>';
	}

	/**
	 * Process message dismissal request
	 */
	public function smartcrawl_dismiss_message() {
		$data = $this->get_request_data();
		$message = sanitize_key( smartcrawl_get_array_value( $data, 'message' ) );
		if ( null === $message ) {
			wp_send_json_error();

			return;
		}

		$dismissed_messages = get_user_meta( get_current_user_id(), 'wds_dismissed_messages', true );
		$dismissed_messages = '' === $dismissed_messages ? array() : $dismissed_messages;
		$dismissed_messages[ $message ] = true;
		update_user_meta( get_current_user_id(), 'wds_dismissed_messages', $dismissed_messages );
		wp_send_json_success();
	}

	public function enqueue_dashboard_resources() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_dashboard_css' ) );
	}

	function enqueue_dashboard_css() {
		wp_enqueue_style( Smartcrawl_Controller_Assets::WP_DASHBOARD_CSS );
	}

	private function get_request_data() {
		return isset( $_POST['_wds_nonce'] ) && wp_verify_nonce( $_POST['_wds_nonce'], 'wds-admin-nonce' ) ? stripslashes_deep( $_POST ) : array();
	}
}

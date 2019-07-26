<?php
/**
 * Branda Administrator Menu class.
 *
 * Class that handles admin menu functionality.
 *
 * @package Branda
 * @subpackage Admin Menu
 */
if ( ! class_exists( 'Branda_Admin_Menu' ) ) {

	/**
	 * Class Branda_Admin_Menu.
	 */
	class Branda_Admin_Menu extends Branda_Helper {

		/**
		 * Admin menu module option name.
		 *
		 * @var string
		 */
		protected $option_name = 'ub_admin_menu';

		/**
		 * Branda_Admin_Menu constructor.
		 */
		public function __construct() {
			parent::__construct();
			// Set options.
			$this->set_options();
			// Base hooks.
			add_filter( 'ultimatebranding_settings_admin_menu', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_admin_menu_process', array( $this, 'update' ) );
			// Upgrade options to new.
			add_action( 'init', array( $this, 'upgrade_options' ) );
			// Dashboard link.
			add_action( 'user_admin_menu', array( $this, 'remove_dashboard_link' ) );
			// Link manager.
			add_filter( 'pre_option_link_manager_enabled', array( $this, 'link_manager' ) );
			// WP Admin -> Settings -> permalinks.
			add_action( 'admin_menu', array( $this, 'remove_permalinks_menu_item' ) );
			// Admin panel tips.
			$this->maybe_load_tips();
		}

		/**
		 * Set options for module admin page.
		 *
		 * @since 3.0.0
		 */
		protected function set_options() {
			$this->module = 'admin_menu';
			$options = array(
				'dashboard-link' => array(
					'network-only' => true,
					'title' => __( 'Dashboard Link', 'ub' ),
					'description' => __( 'Remove "Dashboard" link from admin panel for users without site (in WP Multisite).', 'ub' ),
					'fields' => array(
						'status' => array(
							'checkbox_label' => __( 'Remove link for users without site', 'ub' ),
							'type' => 'checkbox',
							'classes' => array( 'switch-button' ),
						),
					),
				),
				'link-manager' => array(
					'title' => __( 'Link Manager', 'ub' ),
					'description' => __( 'Enables the Link Manager that existed in WordPress until version 3.5.', 'ub' ),
					'fields' => array(
						'status' => array(
							'checkbox_label' => __( 'Enable link manager', 'ub' ),
							'type' => 'checkbox',
							'classes' => array( 'switch-button' ),
						),
					),
				),
				'tips' => array(
					'title' => __( 'Admin Tips', 'ub' ),
					'description' => __( 'Provide your users with helpful random tips, or promotions/news in their admin panels.', 'ub' ),
					'fields' => array(
						'status' => array(
							'checkbox_label' => __( 'Enable Admin Tips', 'ub' ),
							'type' => 'checkbox',
							'description' => array(
								'content' => __( 'Add a custom post type “Tips” in the WordPress menu and start adding tips for the users.', 'ub' ),
								'position' => 'bottom',
							),
							'classes' => array( 'switch-button' ),
						),
					),
				),
				'permalink' => array(
					'title' => __( 'Permalink', 'ub' ),
					'description' => __( 'Choose whether you want to removes the "permalinks" configuration options.', 'ub' ),
					'fields' => array(
						'status' => array(
							'checkbox_label' => __( 'Remove permalinks menu item', 'ub' ),
							'type' => 'checkbox',
							'classes' => array( 'switch-button' ),
						),
					),
				),
			);
			$this->options = $options;
		}

		/**
		 * Upgrade options to new structure.
		 *
		 * @since 3.0.0
		 */
		public function upgrade_options() {
			$uba = ub_get_uba_object();
			$modules = get_ub_activated_modules();
			$update  = false;
			$data = array(
				'dashboard-link' => array(
					'status' => 'off',
				),
				'link-manager' => array(
					'status' => 'off',
				),
				'tips' => array(
					'status' => 'off',
				),
				'permalink' => array(
					'status' => 'off',
				),
			);
			$m = array(
				'admin-panel-tips/admin-panel-tips.php' => 'tips',
				'link-manager.php' => 'link-manager',
				'remove-dashboard-link-for-users-without-site.php' => 'dashboard-link',
				'remove-permalinks-menu-item.php' => 'permalink',
			);
			foreach ( $m as $module => $option_name ) {
				if (
					isset( $modules[ $module ] )
					&& 'yes' === $modules[ $module ]
				) {
					$data[ $option_name ]['status'] = 'on';
					$update                         = true;
					$uba->deactivate_module( $module );
				}
			}
			if ( ! $update ) {
				return;
			}
			$this->update_value( $data );
		}

		/**
		 * Remove dashboard link from WP Admin.
		 *
		 * Remove dashboard link for users withou any sites.
		 */
		public function remove_dashboard_link() {
			$status = $this->get_value( 'dashboard-link', 'status', 'off' );
			if ( 'off' === $status ) {
				return;
			}
			$user_blogs = get_blogs_of_user( get_current_user_id() );
			if ( 0 === count( $user_blogs ) ) {
				remove_menu_page( 'index.php' );
				$current_url = $this->get_admin_current_page_url();
				if ( preg_match( '/user\//', $current_url ) && ! preg_match( '/profile.php/', $current_url ) ) {
					wp_redirect( 'profile.php' );
				}
			}
		}

		/**
		 * Get current admin page url.
		 *
		 * @access private
		 *
		 * @return string $page_url
		 */
		private function get_admin_current_page_url() {
			$page_url = 'http';
			if ( isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ) {
				$page_url .= 's';
			}
			$page_url .= '://';
			if ( isset( $_SERVER['SERVER_PORT'] ) && '80' !== $_SERVER['SERVER_PORT'] ) {
				$page_url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
			} else {
				$page_url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
			}
			return $page_url;
		}

		/**
		 * Handle link manager enable/disable.
		 *
		 * @param bool $status Disabled status.
		 *
		 * @return bool|mixed|null|string
		 */
		public function link_manager( $status ) {
			$status = $this->get_value( 'link-manager', 'status', 'off' );
			if ( 'off' === $status ) {
				return false;
			}
			return true;
		}

		/**
		 * Remove permalinks option from admin menu.
		 */
		public function remove_permalinks_menu_item() {
			$status = $this->get_value( 'permalink', 'status', 'off' );
			if ( 'off' === $status ) {
				return;
			}
			global $submenu;
			// Check parent menu.
			if ( ! isset( $submenu['options-general.php'] ) || ! is_array( $submenu['options-general.php'] ) ) {
				return;
			}
			foreach ( $submenu['options-general.php'] as $key => $data ) {
				if ( 'options-permalink.php' === $data[2] ) {
					unset( $submenu['options-general.php'][ $key ] );
					return;
				}
			}
		}

		/**
		 * Load tips for admin panel.
		 */
		public function maybe_load_tips() {
			$status = $this->get_value( 'tips', 'status', 'off' );
			if ( 'off' === $status ) {
				return;
			}
			$file = ub_files_dir( 'modules/admin/tips.php' );
			include_once $file;
		}
	}
}
new Branda_Admin_Menu;

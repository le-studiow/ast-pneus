<?php
if ( ! class_exists( 'Branda_Admin' ) ) {
	require_once dirname( __FILE__ ) . '/class-branda-base.php';
	require_once dirname( __FILE__ ) . '/class-branda-admin-stats.php';
	class Branda_Admin extends Branda_Base {

		var $modules = array();
		var $plugin_msg = array();

		/**
		 * Default messages.
		 *
		 * @since 1.8.5
		 */
		var $messages = array();

		/**
		 * Stats
		 *
		 * @since 2.3.0
		 */
		private $stats = null;

		/**
		 * module
		 *
		 * @since 3.0.0
		 */
		private $module = '';

		/**
		 * Show Welcome Dialog
		 *
		 * @since 3.0.0
		 */
		private $show_welcome_dialog = false;

		/**
		 * Top page slug
		 */
		private $top_page_slug;

		/**
		 * Messages storing
		 *
		 * @since 3.1.0
		 */
		private $messages_option_name = 'branda_messages';

		public function __construct() {
			parent::__construct();
			/**
			 * set and sanitize variables
			 */
			add_action( 'plugins_loaded', array( $this, 'set_and_sanitize_variables' ), 2 );
			/**
			 * run stats
			 */
			$this->stats = new Branda_Admin_Stats;
			/**
			 * debug only when WP_DEBUG && WPMUDEV_BETATEST
			 */
			$debug = defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WPMUDEV_BETATEST' ) && WPMUDEV_BETATEST;
			$this->debug = apply_filters( 'ultimatebranding_debug', $debug );
			foreach ( $this->configuration as $key => $data ) {
				$is_avaialble = $this->can_load_module( $data );
				if ( ! $is_avaialble ) {
					continue;
				}
				if ( isset( $data['disabled'] ) && $data['disabled'] ) {
					continue;
				}
				$this->modules[ $key ] = $data['module'];
			}
			/**
			 * Filter allow to turn off available modules.
			 *
			 * @since 1.9.4
			 *
			 * @param array $modules available modules array.
			 */
			$this->modules = apply_filters( 'ultimatebranding_available_modules', $this->modules );
			add_action( 'plugins_loaded', array( $this, 'load_modules' ), 11 );
			add_action( 'plugins_loaded', array( $this, 'setup_translation' ) );
			add_action( 'init', array( $this, 'initialise_ub' ) );
			add_action( 'network_admin_menu', array( $this, 'network_admin_page' ) );
			add_filter( 'admin_title', array( $this, 'admin_title' ), 10, 2 );
			/**
			 * AJAX
			 */
			add_action( 'wp_ajax_ultimate_branding_toggle_module', array( $this, 'toggle_module' ) );
			add_action( 'wp_ajax_branda_reset_module', array( $this, 'ajax_reset_module' ) );
			add_action( 'wp_ajax_branda_manage_all_modules', array( $this, 'ajax_bulk_modules' ) );
			add_action( 'wp_ajax_branda_module_copy_settings', array( $this, 'ajax_copy_settings' ) );
			add_action( 'wp_ajax_branda_welcome_get_modules', array( $this, 'ajax_welcome' ) );
			/**
			 * default messages
			 */
			$this->messages = array(
				'success' => __( 'Success! Your changes were successfully saved!', 'ub' ),
				'fail' => __( 'There was an error, please try again.', 'ub' ),
				'reset-section-success' => __( 'Section was reset to defaults.', 'ub' ),
				'wrong' => __( 'Something went wrong!', 'ub' ),
				'security' => __( 'Nope! Security check failed!', 'ub' ),
				'missing' => __( 'Missing required data!', 'ub' ),
			);
			/**
			 * remove default footer
			 */
			add_filter( 'admin_footer_text', array( $this, 'remove_default_footer' ), PHP_INT_MAX );
			/**
			 * upgrade
			 *
			 * @since 3.0.0
			 */
			add_action( 'init', array( $this, 'upgrade' ) );
			/**
			 * Add branda class to admin body
			 */
			add_filter( 'admin_body_class', array( $this, 'add_branda_admin_body_class' ), PHP_INT_MAX );
			/**
			 * Add import/export modules instantly on
			 */
			add_filter( 'ub_get_option-ultimatebranding_activated_modules', array( $this, 'add_instant_modules' ), 10, 3 );
			/**
			 * Allow to uload SVG files.
			 *
			 * @since 1.8.9
			 */
			add_filter( 'upload_mimes', array( $this, 'add_svg_to_allowed_mime_types' ) );
			/**
			 * Add sui-wrap classes
			 *
			 * @since 3.0.6
			 */
			add_filter( 'branda_sui_wrap_class', array( $this, 'add_sui_wrap_classes' ) );
			/**
			 * Delete image from modules, when it is deleted from WordPress
			 *
			 * @since 3.1.0
			 */
			add_action( 'delete_attachment', array( $this, 'delete_attachment_from_configs' ), 10, 1 );
		}

		/**
		 * Allow to uload SVG files.
		 *
		 * @since 1.8.9
		 */
		public function add_svg_to_allowed_mime_types( $mimes ) {
			$mimes['svg'] = 'image/svg+xml';
			return $mimes;
		}

		/**
		 * Faked instant on modules as active.
		 *
		 * @since 3.0.0
		 */
		public function add_instant_modules( $value, $option, $default ) {
			if ( ! is_array( $value ) ) {
				$value = array();
			}
			foreach ( $this->configuration as $key => $module ) {
				if ( isset( $module['instant'] ) && 'on' === $module['instant'] ) {
					$value[ $key ] = 'yes';
				}
			}
			return $value;
		}

		/**
		 * Add "Branda" to admin title.
		 *
		 * @since 1.9.8
		 */
		public function admin_title( $admin_title, $title ) {
			$screen = get_current_screen();
			if ( is_a( $screen, 'WP_Screen' ) && preg_match( '/_page_branding/', $screen->id ) ) {
				$admin_title = sprintf( '%s%s%s',
					_x( 'Branda', 'admin title', 'ub' ),
					_x( ' &lsaquo; ', 'admin title separator', 'ub' ),
					$admin_title
				);
				if ( ! empty( $this->module ) ) {
					$module_data = $this->get_module_by_module( $this->module );
					if ( ! empty( $module_data ) && isset( $module_data['group'] ) ) {
						$groups = branda_get_groups_list();
						if ( isset( $groups[ $module_data['group'] ] ) ) {
							$admin_title = sprintf( '%s%s%s',
								$groups[ $module_data['group'] ]['title'],
								_x( ' &lsaquo; ', 'admin title separator', 'ub' ),
								$admin_title
							);
						}
					}
				}
			}
			return $admin_title;
		}

		/**
		 * Add message to show
		 */
		public function add_message( $message ) {
			$messages = get_user_option( $this->messages_option_name );
			if ( empty( $messages ) ) {
				$messages = array();
			}
			if ( ! in_array( $message, $messages ) ) {
				$user_id = get_current_user_id();
				$messages[] = $message;
				update_user_option( $user_id, $this->messages_option_name, $messages, false );
			}
		}

		/**
		 * Print admin notice from option.
		 *
		 * @since 1.9.5
		 */
		public function show_messages() {
			$screen = get_current_screen();
			if ( ! preg_match( '/_page_branding/', $screen->id ) ) {
				return;
			}
			$messages = get_user_option( $this->messages_option_name );
			if ( empty( $messages ) ) {
				return;
			}
			$fire_delete = false;
			foreach ( $messages as $message ) {
				if ( ! isset( $message['message'] ) || empty( $message['message'] ) ) {
					continue;
				}
				$fire_delete = true;
				echo $this->sui_notice(
					$message['message'],
					isset( $message['class'] )? $message['class']:'success',
					isset( $message['id'] )? $message['id']:null,
					isset( $message['style'] )? $message['style']:null,
					isset( $message['position'] )? $message['position']:'top',
					isset( $message['no-dissmiss'] )? false:null
				);
			}
			if ( $fire_delete ) {
				add_action( 'shutdown', array( $this, 'delete_messages' ) );
			}
		}

		/**
		 * delete messages
		 *
		 * @since 3.1.0
		 */
		public function delete_messages() {
			$user_id = get_current_user_id();
			delete_user_option( $user_id, $this->messages_option_name, false );
		}

		public function initialise_ub() {
			global $blog_id;
			// For this version only really - to bring settings across from the old storage locations
			if ( ! is_multisite() ) {
				if ( UB_HIDE_ADMIN_MENU != true ) {
					add_action( 'admin_menu', array( $this, 'network_admin_page' ) );
				}
			} else {
				global $branda_network;
				if ( $branda_network ) {
					$show_in_subsites = $this->check_show_in_subsites();
					if ( $show_in_subsites ) {
						add_action( 'admin_menu', array( $this, 'admin_page' ) );
					}
				} else {
					// Added to allow single site activation across a network
					if ( UB_HIDE_ADMIN_MENU != true && ! defined( 'UB_HIDE_ADMIN_MENU_' . $blog_id ) ) {
						add_action( 'admin_menu', array( $this, 'network_admin_page' ) );
					}
				}
			}
			// Header actions
			add_action( 'load-toplevel_page_branding', array( $this, 'add_admin_header_branding' ) );
		}

		public function setup_translation() {
			// Load up the localization file if we're using WordPress in a different language
			// Place it in this plugin's "languages" folder and name it "mp-[value in wp-config].mo"
			$dir = sprintf( '/%s/languages', basename( ub_dir( '' ) ) );
			load_plugin_textdomain( 'ub', false, $dir );
		}

		public function add_admin_header_core() {
			/**
			 * Filter allow to avoid run wp_enqueue* functions.
			 *
			 * @since 3.0.0
			 * @param boolean $add Load assets or not load - it is a question.
			 */
			$add = apply_filters( 'branda_add_admin_header_core', true );
			if ( ! $add ) {
				return;
			}
			/**
			 * Shared UI
			 *
			 * @since 3.0.0
			 */
			if ( defined( 'BRANDA_SUI_VERSION' ) ) {
				$sanitize_version = str_replace( '.', '-', BRANDA_SUI_VERSION );
				$sui_body_class   = "sui-$sanitize_version";
				wp_register_script(
					'sui-scripts',
					ub_url( 'assets/js/shared-ui.min.js' ),
					array( 'jquery' ),
					$sui_body_class,
					true
				);
				wp_enqueue_style(
					'sui-styles',
					ub_url( 'assets/css/shared-ui.min.css' ),
					array(),
					$sui_body_class
				);
			}
			// Add in the core CSS file
			$file = ub_url( 'assets/css/ultimate-branding-admin.min.css' );
			wp_enqueue_style( 'branda-admin', $file, array(), $this->build );
			wp_enqueue_script( array(
				'jquery-ui-sortable',
			) );
			$file = sprintf( 'assets/js/ultimate-branding-admin%s.js', defined( 'WP_DEBUG' ) && WP_DEBUG ? '':'.min' );
			wp_enqueue_script( 'ub_admin', ub_url( $file ), array( 'jquery', 'jquery-effects-highlight', 'sui-scripts', 'underscore', 'wp-util' ), $this->build, true );
			wp_enqueue_style( 'wp-color-picker' );
			$file = ub_url( 'external/wp-color-picker-alpha/wp-color-picker-alpha.min.js' );
			wp_enqueue_script( 'wp-color-picker-alpha', $file, array( 'wp-color-picker' ), '2.1.3', true );
			/**
			 * Messages
			 */
			$messages = array(
				'messages' => array(
					'copy' => array(
						'confirm' => __( 'Are you sure to replace all section data?', 'ub' ),
						'select_first' => __( 'Please select a source module first.', 'ub' ),
					),
					'reset' => array(
						'module' => __( 'Are you sure? This will replace all entered data by defaults.', 'ub' ),
					),
					'welcome' => array(
						'empty' => __( 'Please select some modules first or skip this step.', 'ub' ),
					),
					'form' => array(
						'number' => array(
							'max' => __( 'Entered value is above field limit!', 'ub' ),
							'min' => __( 'Entered value is below field limit!', 'ub' ),
						),
					),
					'unsaved' => __( 'Changes are not saved, are you sure you want to navigate away?', 'ub' ),
				),
				'buttons' => array(
					'save_changes' => __( 'Save Changes', 'ub' ),
				),
			);
			foreach ( $this->messages as $key => $value ) {
				$messages['messages'][ $key ] = $value;
			}
			/**
			 * Filter messages array
			 *
			 * @since 3.0.0
			 */
			$messages = apply_filters( 'branda_admin_messages_array', $messages );
			wp_localize_script( 'ub_admin', 'ub_admin', $messages );
		}

		public function add_admin_header_branding() {
			$this->add_admin_header_core();
			do_action( 'ultimatebranding_admin_header_global' );
			$update = apply_filters( 'ultimatebranding_update_branding_page', true );
			if ( $update ) {
				$this->update_branding_page();
			}
		}

		/**
		 * Set module status from "Manage All Modules" page.
		 *
		 * @since 3.0.0
		 */
		public function ajax_bulk_modules() {
			$fields = array( 'branda', 'nonce' );
			foreach ( $fields as $field ) {
				if ( ! isset( $_POST[ $field ] ) ) {
					$args = array(
						'class' => 'error',
						'message' => $this->messages['missing'],
					);
					wp_send_json_error( $args );
				}
			}
			if (
				! wp_verify_nonce( $_POST['nonce'], 'branda-manage-all-modules' )
				&& ! wp_verify_nonce( $_POST['nonce'], 'branda-welcome-activate' )
			) {
				$args = array(
					'class' => 'error',
					'message' => $this->messages['security'],
				);
				wp_send_json_error( $args );
			}
			$modules = $_POST['branda'];
			if ( ! is_array( $modules ) ) {
				$modules = array();
			}
			$activated = $deactivated = 0;
			foreach ( $this->configuration as $key => $module ) {
				if ( isset( $module['instant'] ) && $module['instant'] ) {
					continue;
				}
				$is_active = ub_is_active_module( $key );
				if ( in_array( $module['module'], $modules ) ) {
					if ( ! $is_active ) {
						$this->activate_module( $key );
						$activated++;
					}
				} else {
					if ( $is_active ) {
						$this->deactivate_module( $key );
						$deactivated++;
					}
				}
			}
			$message = '';
			if ( 0 < $activated ) {
				$message .= sprintf(
					_n(
						'%d new module was activated successfully.',
						'%d new modules was activated successfully.',
						$activated,
						'ub'
					),
					number_format_i18n( $activated )
				);
				if ( 0 < $deactivated ) {
					$message .= ' ';
				}
			}
			if ( 0 < $deactivated ) {
				$message .= sprintf(
					_n(
						'%d module was deactivated successfully.',
						'%d modules was deactivated successfully.',
						$deactivated,
						'ub'
					),
					number_format_i18n( $deactivated )
				);
			}
			/**
			 * Speciall message, when was nothing to do!
			 */
			if ( 0 === $activated && 0 === $deactivated ) {
				$args = array(
					'class' => 'info',
					'message' => sprintf(
						'<q>%s</q> &mdash; <i>%s</i>',
						esc_html__( '42: The answer to life, the universe and everything.', 'ub' ),
						esc_html__( 'Douglas Adams', 'ub' )
					),
				);
				$args['message'] .= '<br >';
				$args['message'] .= __( 'Nothing was changed, nothing to activate or deactivate.', 'ub' );
				wp_send_json_error( $args );
			}
			if ( empty( $message ) ) {
				$args = array(
					'class' => 'error',
					'message' => $this->messages['wrong'],
				);
				wp_send_json_error( $args );
			}
			$message = array(
				'class' => 'success',
				'message' => $message,
			);
			$this->add_message( $message );
			wp_send_json_success();
		}

		/**
		 * Check plugins those will be used if they are active or not
		 */
		public function load_modules() {
			$this->set_configuration();
			// Load our remaining modules here
			foreach ( $this->modules as $module => $plugin ) {
				if ( ub_is_active_module( $module ) ) {
					if ( ! isset( $this->configuration[ $module ] ) ) {
						continue;
					}
					if ( $this->should_be_module_off( $this->configuration[ $module ] ) ) {
						continue;
					}
					ub_load_single_module( $module );
				}
			}
			/**
			 * set related
			 *
			 * @since 2.3.0
			 */
			$this->related = apply_filters( 'ultimate_branding_related_modules', $this->related );
		}

		/**
		 * add bold
		 *
		 * @since 2.1.0
		 */
		private function bold( $a ) {
			return sprintf( '"<b>%s</b>"', $a );
		}

		/**
		 * Separate logo
		 *
		 * @since 3.0.0
		 */
		public function get_u_logo() {
			$image = 'PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNTAiIGhlaWdodD0iMTUwIj48ZyB0cmFuc2Zvcm09Im1hdHJpeCguMzAzMDMgMCAwIC0uMzAzMDMgLTc2LjUxNSAyNTAuNzYpIj48ZyB0cmFuc2Zvcm09InRyYW5zbGF0ZSgzNjUsNzY1KSI+PHBhdGggZD0ibTAgMHYtMjM1YzAtNTUuMDU3IDMzLjEyOS0xMDIuNTIgODAuNS0xMjMuNTF2MzU4LjUxaC04MC41em0xMzUtNDAwYy05MC45ODEgMC0xNjUgNzQuMDE5LTE2NSAxNjV2MjY1aDE0MC41di0zOTcuNzdjNy45NDgtMS40NjMgMTYuMTM2LTIuMjI4IDI0LjUtMi4yMjggNzQuNDM5IDAgMTM1IDYwLjU2MSAxMzUgMTM1djI2NWgzMHYtMjY1YzAtOTAuOTgxLTc0LjAxOS0xNjUtMTY1LTE2NSIgZmlsbD0iIzViNWM3MiIvPjwvZz48L2c+PC9zdmc+Cg==';
			return 'data:image/svg+xml;base64,'.$image;
		}

		/**
		 * Add main menu
		 *
		 * @since 2.0.0
		 *
		 * @param string $capability Capability.
		 */
		private function menu( $capability ) {
			// Add in our menu page
			$this->top_page_slug = add_menu_page(
				__( 'Branda', 'ub' ),
				__( 'Branda', 'ub' ),
				$capability,
				'branding',
				array( $this, 'handle_main_page' ),
				$this->get_u_logo()
			);

			add_filter( 'load-'.$this->top_page_slug, array( $this, 'add_action_hooks' ) );
		}

		/**
		 * Add pages
		 */
		public function admin_page() {
			$this->menu( 'manage_options' );
		}

		/**
		 * Add pages
		 */
		public function network_admin_page() {
			$capability = 'manage_options';
			global $branda_network, $submenu;
			if ( $branda_network ) {
				$capability = 'manage_network_options';
			}
			// Add in our menu page
			$this->menu( $capability );
			$menu = add_submenu_page(
				'branding',
				__( 'Dashboard', 'ub' ),
				__( 'Dashboard', 'ub' ),
				$capability,
				'branding',
				array( $this, 'handle_main_page' )
			);
			add_action( 'load-'.$menu, array( $this, 'load_dashboard' ) );
			/**
			 * Add groups submenus
			 */
			foreach ( $this->submenu as $key => $data ) {
				$menu = add_submenu_page(
					'branding',
					$data['title'],
					$data['title'],
					$capability,
					sprintf( 'branding_group_%s', esc_attr( $key ) ),
					array( $this, 'handle_group' )
				);
				add_action( 'load-'.$menu, array( $this, 'add_admin_header_branding' ) );
			}
			do_action( 'ultimate_branding_add_menu_pages' );
			// Sort sub menu items.
			if ( isset( $submenu['branding'] ) ) {
				$items = $submenu['branding'];
				usort( $items, array( $this, 'sort_sub_menus' ) );
				$submenu['branding'] = $items;
			}
		}

		/**
		 * Sort admin sub menus.
		 *
		 * We need to make sure the main dashboard menu
		 * gets the first priority.
		 *
		 * @param mixed $a
		 * @param mixed $b
		 *
		 * @return int
		 */
		private function sort_sub_menus( $a, $b ) {
			if ( 'branding' === $b[2] ) {
				return 1;
			}
			if ( 'branding' === $a[2] ) {
				return -1;
			}
			if ( 'branding_group_data' === $b[2] ) {
				return -1;
			}
			if ( 'branding_group_data' === $a[2] ) {
				return 1;
			}
			return strcasecmp( $a[0], $b[0] );
		}

		public function activate_module( $module ) {
			$update = true;
			$modules = get_ub_activated_modules();
			if (
				isset( $modules[ $module ] )
			) {
				if ( 'yes' !== $modules[ $module ] ) {
					$update = true;
					$modules[ $module ] = 'yes';
				}
			} else {
				$update = true;
				$modules[ $module ] = 'yes';
			}
			if ( $update ) {
				$modules[ $module ] = 'yes';
				update_ub_activated_modules( $modules );
				do_action( 'branda_module_activated', $module );
				return true;
			}
			return false;
		}

		public function deactivate_module( $module ) {
			$modules = get_ub_activated_modules();
			if ( isset( $modules[ $module ] ) ) {
				unset( $modules[ $module ] );
				update_ub_activated_modules( $modules );
				do_action( 'branda_module_deactivated', $module );
				return true;
			}
			return false;
		}

		public function update_branding_page() {
			global $action, $page;
			wp_reset_vars( array( 'action', 'page' ) );
			if ( isset( $_REQUEST['action'] ) && ! empty( $_REQUEST['action'] ) ) {
				$t = preg_replace( '/-/', '_', $this->module );
				/**
				 * check
				 */
				check_admin_referer( 'ultimatebranding_settings_'.$t );
				$result = apply_filters( 'ultimatebranding_settings_'.$t.'_process', true );
				$url = wp_get_referer();
				if ( is_array( $result ) ) {
					$url = add_query_arg( $result, $url );
				}
				wp_safe_redirect( $url );
				do_action( 'ultimatebranding_settings_update_' . $t );
			}
		}

		/**
		 * Helper to build link
		 *
		 * @since 3.0.0
		 */
		private function get_module_link( $module ) {
			$url = add_query_arg(
				array(
					'page' => sprintf( 'branding_group_%s', $module['group'] ),
					'module' => $module['module'],
				),
				is_network_admin()? network_admin_url( 'admin.php' ):admin_url( 'admin.php' )
			);
			$link = sprintf(
				'<a href="%s" class="branda-module branda-module-%s" data-group="%s">%s</a>',
				esc_url( $url ),
				esc_attr( $module['module'] ),
				esc_attr( $module['group'] ),
				esc_html( $module['name'] )
			);
			return $link;
		}

		/**
		 * Helper to get array of modules state.
		 *
		 * @since 3.0.0
		 */
		private function get_modules_stats() {
			$modules = array();
			foreach ( $this->configuration as $key => $module ) {
				if ( ! array_key_exists( $key, $this->modules ) ) {
					continue;
				}
				if ( ! isset( $modules[ $module['group'] ] ) ) {
					$modules[ $module['group'] ] = array();
				}
				$modules[ $module['group'] ]['modules'][ $key ] = $module;
				$modules[ $module['group'] ]['modules'][ $key ]['status'] = 'inactive';
				if ( ub_is_active_module( $key ) ) {
					$modules[ $module['group'] ]['modules'][ $key ]['status'] = 'active';
				}
			}
			foreach ( $modules as $group => $data ) {
				$m = $data['modules'];
				uasort( $m, array( $this, 'sort_modules_by_name' ) );
				$modules[ $group ]['modules'] = $m;
			}
			return $modules;
		}

		public function handle_main_page() {
			$stats = $this->stats->get_stats();
			$recently_activated = $recently_deactivated = __( 'none', 'ub' );
			if ( isset( $stats['activites'] ) ) {
				if (
					isset( $stats['activites']['activate'] )
					&& isset( $this->configuration[ $stats['activites']['activate'] ] )
				) {
					$recently_activated = $this->get_module_link(
						$this->configuration[ $stats['activites']['activate'] ]
					);
				}
				if (
					isset( $stats['activites']['deactivate'] )
					&& isset( $this->configuration[ $stats['activites']['deactivate'] ] )
				) {
					$recently_deactivated = $this->get_module_link(
						$this->configuration[ $stats['activites']['deactivate'] ]
					);
				}
			}
			$args = array(
				'stats' => array(
					'active' => 0,
					'total' => 0,
					'recently_activated' => $recently_activated,
					'recently_deactivated' => $recently_deactivated,
					'frequently_used' => array(),
					'modules' => $this->stats->get_frequently_used_modules(),
					'raw' => $this->stats->get_modules_raw_data(),
				),
			);
			if ( $args['stats']['modules'] ) {
				foreach ( $args['stats']['modules'] as $key => $value ) {
					if ( ! array_key_exists( $key, $this->modules ) ) {
						continue;
					}
					if ( isset( $this->configuration[ $key ] ) ) {
						$args['stats']['modules'][ $key ] = $this->configuration[ $key ];
						$args['stats']['modules'][ $key ]['status'] = 'inactive';
						if ( ub_is_active_module( $key ) ) {
							$args['stats']['modules'][ $key ]['status'] = 'active';
						}
					} else {
						unset( $args['stats']['modules'][ $key ] );
					}
				}
			}
			/**
			 * Count
			 */
			foreach ( $this->configuration as $key => $module ) {
				if ( ! array_key_exists( $key, $this->modules ) ) {
					continue;
				}
				if ( ub_is_active_module( $key ) ) {
					if ( isset( $module['instant'] ) && $module['instant'] ) {
						continue;
					}
					$args['stats']['active']++;
				}
				$args['stats']['total']++;
			}
			/**
			 * Modules Status
			 */
			$args['modules'] = $this->get_modules_stats();
			/**
			 * groups
			 */
			$args['groups'] = branda_get_groups_list();
			/**
			 * SUI
			 */
			$args['sui'] = array(
				'summary' => array(
					'style' => $this->get_box_summary_image_style(),
					'classes' => array(
						'sui-box',
						'sui-summary',
					),
				),
			);
			/**
			 * Dash integration
			 *
			 * @since 3.2.0
			 */
			$hide_branding = apply_filters( 'wpmudev_branding_hide_branding', $this->hide_branding );
			$branding_image = apply_filters( 'wpmudev_branding_hero_image', null );
			if ( $hide_branding && ! empty( $branding_image ) ) {
				$args['sui']['summary']['classes'][] = 'sui-rebranded';
			} elseif ( $hide_branding && empty( $branding_image ) ) {
				$args['sui']['summary']['classes'][] = 'sui-unbranded';
			}
			/**
			 * render
			 */
			$classes = apply_filters( 'branda_sui_wrap_class', array(), $this->module );
			$template = 'admin/dashboard';
			printf(
				'<main class="%s">',
				implode( ' ', $classes )
			);
			$this->render( $template, $args );
			if ( $this->show_welcome_dialog ) {
				$args = array(
					'dialog_id' => 'branda-welcome',
					'modules' => $this->get_modules_stats(),
					'groups' => branda_get_groups_list(),
				);
				$template = 'admin/dashboard/welcome';
				$this->render( $template, $args );
			}
			$this->footer();
			/**
			 * Messages
			 */
			$this->show_messages();
			echo '</main>';
		}

		/**
		 * Show group page
		 *
		 * @since 3.0.0
		 */
		public function handle_group() {
			$classes = array(
				sprintf( 'sui-wrap-branda-module-%s', $this->module ),
			);
			$classes = apply_filters( 'branda_sui_wrap_class', $classes, $this->module );
			printf( '<main class="%s">', implode( ' ', $classes ) );
			$content = apply_filters( 'branda_handle_group_page', '', $this->module );
			if ( ! empty( $content ) ) {
				echo $content;
			} else {
				$this->render( 'admin/common/header', array( 'title' => $this->get_current_group_title() ) );
				echo '<div class="sui-row-with-sidenav">';
				echo '<div class="sui-sidenav">';
				$this->group_tabs( 'menu' );
				echo '</div>'; //sui-sidenav
				$this->group_tabs( 'content' );
				echo '</div>'; // sui-row-with-sidenav
			}
			$this->show_messages();
			$this->footer();
			echo '</main>';
		}

		/**
		 * Helper to show group
		 *
		 * @since 3.0.0
		 */
		private function group_tabs( $type ) {
			$modules = $this->get_modules_by_group();
			if ( is_wp_error( $modules ) ) {
				if ( 'content' === $type ) {
					$error_string = $modules->get_error_message();
					echo '<div class="error"><p>' . $error_string . '</p></div>';
				}
				return;
			}
			/**
			 * Get current module or set first
			 */
			$current = $modules[ key( $modules ) ]['module'];
			if ( ! empty( $this->module ) ) {
				$current = $this->module;
			}
			$content = '';
			switch ( $type ) {
				case 'menu':
					$content = $this->group_tabs_menu( $modules, $current );
				break;
				case 'content':
					$content = $this->group_tabs_content( $modules, $current );
				break;
				default:
					break;
			}
			echo $content;
		}

		private function group_tabs_menu( $modules, $current ) {
			$tabs = '';
			$select = '';
			foreach ( $modules as $id => $module ) {
				$slug = $module['module'];
				$title = $module['name'];
				if ( isset( $module['title'] ) ) {
					$title = $module['title'];
				}
				if ( isset( $module['menu_title'] ) ) {
					$title = $module['menu_title'];
				}
				/**
				 * Active?
				 */
				$icon = ub_is_active_module( $id )? '<i class="sui-icon-check-tick"></i>':'';
				if ( isset( $module['instant'] ) && 'on' === $module['instant'] ) {
					$icon = '';
				}
				$tabs .= sprintf(
					'<li class="sui-vertical-tab %s"><a href="#" data-tab="%s">%s%s</a></li>',
					esc_attr( $current === $slug ? 'current':'' ),
					sanitize_title( $slug ),
					esc_html( $title ),
					$icon
				);
				$select .= sprintf(
					'<option %s value="%s">%s</option>',
					esc_attr( $current === $slug ? 'selected="selected':'' ),
					sanitize_title( $slug ),
					esc_html( $title )
				);
			}
			$content = '<ul class="sui-vertical-tabs sui-sidenav-hide-md">';
			$content .= $tabs;
			$content .= '</ul>';
			$content .= '<div class="sui-sidenav-hide-lg">';
			$content .= '<select class="sui-mobile-nav" id="branda-mobile-nav" style="display: none;">';
			$content .= $select;
			$content .= '</select>';
			$content .= '</div>';
			return $content;
		}

		private function group_tabs_content( $modules, $current ) {
			$content = '';
			$some_module_is_active = false;
			foreach ( $modules as $id => $module ) {
				$slug = $module['module'];
				$is_active = ub_is_active_module( $module['key'] );
				$action = preg_replace( '/-/', '_', 'ultimatebranding_settings_'.$module['module'] );
				/**
				 * Module header
				 *
				 * hide for instant active modules
				 */
				$show_module_header = true;
				if ( isset( $module['instant'] ) && 'on' === $module['instant'] ) {
					$show_module_header = false;
				}
				if ( $show_module_header ) {
					$classes = array(
						'sui-box',
						'branda-settings-tab',
						sprintf( 'branda-settings-tab-%s', sanitize_title( $slug ) ),
						sprintf( 'branda-settings-tab-title-%s', sanitize_title( $slug ) ),
						'branda-settings-tab-title',
					);
					$buttons = '';
					if ( $is_active ) {
						/**
						 * deactivate button
						 */
						if ( ! isset( $module['instant'] ) || 'on' !== $module['instant'] ) {
							$args = array(
								'data' => array(
									'nonce' => wp_create_nonce( $slug ),
									'slug' => $slug,
								),
								'class' => 'ub-deactivate-module',
								'text' => __( 'Deactivate', 'ub' ),
								'sui' => 'ghost',
							);
							$buttons .= $this->button( $args );
						}
						/**
						 * submit button
						 */
						$filter = $action.'_process';
						if (
							has_filter( $filter )
							&& apply_filters( 'ultimatebranding_settings_panel_show_submit', true, $module )
						) {
							$args = array(
								'text' => __( 'Save Changes', 'ub' ),
								'sui' => 'blue',
								'icon' => 'save',
								'class' => 'branda-module-save',
							);
							$buttons .= $this->button( $args );
						}
					} else {
						/**
						 * activate button
						 */
						$args = array(
							'data' => array(
								'nonce' => wp_create_nonce( $slug ),
								'slug' => $slug,
							),
							'class' => 'ub-activate-module',
							'sui' => 'blue',
							'text' => __( 'Activate', 'ub' ),
						);
						$buttons = $this->button( $args );
					}
					$template = 'admin/common/module-header';
					$args = array(
						'box_title' => isset( $module['name_alt'] ) ? $module['name_alt'] : $module['name'],
						'classes' => $classes,
						'is_active' => $is_active,
						'module' => $module,
						'copy_button' => $this->get_copy_button( $module ),
						'buttons' => $buttons,
						'slug' => $slug,
						'current' => $current,
						'status_indicator' => isset( $module['status-indicator'] )? $module['status-indicator']:'show',
					);
					$content .= $this->render( $template, $args, true );
				}
				/**
				 * body
				 */
				if ( $is_active  ) {
					$classes = array(
						'sui-box',
						'branda-settings-tab',
						sprintf( 'branda-settings-tab-%s', sanitize_title( $slug ) ),
						'branda-settings-tab-content',
						sprintf( 'branda-settings-tab-content-%s', sanitize_title( $slug ) ),
					);
					$classes = apply_filters( 'branda_settings_tab_content_classes', $classes, $module );
					$content .= sprintf(
						'<div class="%s" data-tab="%s"%s>',
						esc_attr( implode( ' ', $classes ) ),
						esc_attr( sanitize_title( $slug ) ),
						$current === $slug ? '':' style="display: none;"'
					);
					$module_content = $this->get_module_content( $module );
					if ( is_wp_error( $module_content ) ) {
						$content .= '<div class="sui-box-body">';
						$content .= $this->notice( $module_content->get_error_message(), 'error inline' );
						$content .= '</div>'; // sui-box-body
					} else {
						$content .= $module_content;
					}
					$content .= '</div>'; // sui-box
				}
				if ( $current === $slug ) {
					$some_module_is_active = true;
				}
			}
			if ( ! $some_module_is_active ) {
				$template = 'admin/common/no-module';
				$content .= $this->render( $template, array(), true );
			}
			return $content;
		}

		/**
		 * Add notice template and footer "In love by WPMU DEV".
		 *
		 * @since 3.0.0
		 */
		private function footer() {
			/**
			 * Modules Status & Manage All Modules
			 */
			$args = array(
				'modules' => $this->get_modules_stats(),
				'groups' => branda_get_groups_list(),
			);
			$template = 'admin/common/manage-all-modules';
			$this->render( $template, $args );
			$hide_footer = false;
			$footer_text = sprintf( __( 'Made with %s by WPMU DEV', 'ub' ), ' <i class="sui-icon-heart"></i>' );
			if ( Branda_Helper::is_member() ) {
				$hide_footer = apply_filters( 'wpmudev_branding_change_footer', $hide_footer );
				$footer_text = apply_filters( 'wpmudev_branding_footer_text', $footer_text );
				$hide_footer = apply_filters( 'branda_change_footer', $hide_footer, $this->module );
				$footer_text = apply_filters( 'branda_footer_text', $footer_text, $this->module );
			}
			$args = array(
				'hide_footer' => $hide_footer,
				'footer_text' => $footer_text,
			);
			$template = 'admin/common/footer';
			$this->render( $template, $args );
			echo $this->sui_notice( '', '', 'ub-settings-notice', 'display:none' );
			do_action( 'branda_ubadmin_footer', $this->module );
		}

		/**
		 * Get inline sui notice with SUI syntax
		 *
		 * @since 3.0.0
		 */
		public function get_inline_sui_notice( $message, $classes = '' ) {
			if ( is_array( $classes ) ) {
				$classes = implode( ' ', $classes );
			}
			return $this->sui_notice( $message, $classes, false, false, false, false );
		}

		/**
		 * Notice with SUI syntax
		 *
		 * @since 3.0.0
		 *
		 * @param string $message Message to show.
		 * @param string $class extra class to add.
		 * @param string $id Notice ID.
		 * @param string $style Extra style to notice div.
		 * @param string $position Position of notice.
		 * @param string $can_dissmiss Show can dissmiss button, if empty, * then state depend on $class.
		 *
		 * @return string $content Complete notice code.
		 */
		private function sui_notice( $message, $class = '', $id = '', $style = '', $position = 'top', $can_dissmiss = null ) {
			/**
			 * Set default "Can Dissmiss" only when it is not set, and set it
			 * to false for "success" $class and false otherwise set true
			 */
			if ( null === $can_dissmiss ) {
				$can_dissmiss = true;
				if ( 'success' === $class ) {
					$can_dissmiss = false;
				}
			}
			if ( ! empty( $id ) ) {
				$id = sprintf( ' id="%s"', esc_attr( $id ) );
			}
			if ( ! empty( $style ) ) {
				$style = sprintf( ' style="%s"', esc_attr( $style ) );
			}
			$content = '';
			$content .= sprintf(
				'<div class="sui-notice%s %s sui-notice-%s"%s%s>',
				$position? '-'.$position:'',
				$can_dissmiss? 'sui-can-dismiss':'',
				esc_attr( $class ),
				$id,
				$style
			);
			$content .= sprintf( '<div class="sui-notice-content">%s</div>', wpautop( $message ) );
			if ( $can_dissmiss ) {
				$content .= '<span class="sui-notice-dismiss">';
				$content .= sprintf(
					'<a role="button" href="#" aria-label="%s" class="sui-icon-check">',
					esc_attr__( 'Dismiss', 'ub' )
				);
				$content .= '</a></span>';
			}
			$content .= '</div>';
			return $content;
		}

		/**
		 * Print button save.
		 *
		 * @since 1.8.4
		 * @since 3.0.0 returns value instead of print.
		 */
		public function button_save() {
			$content = sprintf(
				'<p class="submit"><input type="submit" name="submit" class="button-primary" value="%s" /></p>',
				esc_attr__( 'Save Changes', 'ub' )
			);
			return $content;
		}

		/**
		 * Should I show menu in admin subsites?
		 *
		 * @since 1.8.6
		 */
		private function check_show_in_subsites() {
			if ( is_multisite() && is_network_admin() ) {
				return true;
			}
			$modules = get_ub_activated_modules();
			if ( empty( $modules ) ) {
				return false;
			}
			foreach ( $modules as $module => $state ) {
				if ( 'yes' != $state ) {
					continue;
				}
				if (
					isset( $this->configuration[ $module ] )
					&& isset( $this->configuration[ $module ]['show-on-single'] )
					&& $this->configuration[ $module ]['show-on-single']
				) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Get module by group.
		 *
		 * @since 3.0.0
		 *
		 * @param string $group group.
		 */
		public function get_modules_by_group( $group = null ) {
			if ( null === $group ) {
				$group = $this->group;
			}
			$modules = array();
			foreach ( $this->configuration as $key => $module ) {
				if ( ! array_key_exists( $key, $this->modules ) ) {
					continue;
				}
				if ( ! isset( $module['group'] ) ) {
					continue;
				}
				if ( $group == $module['group'] ) {
					$modules[ $key ] = $module;
				}
			}
			if ( empty( $modules ) ) {
				return new WP_Error( 'error', __( 'There is no modules in selected group!', 'ub' ) );
			}
			uasort( $modules, array( $this, 'sort_modules_by_name' ) );
			return $modules;
		}

		/**
		 * get nonced url
		 *
		 * @since 1.8.8
		 */
		private function get_nonce_url( $module ) {
			$page = $this->get_current_page();
			$is_active = ub_is_active_module( $module );
			$url = add_query_arg(
				array(
					'page' => $page,
					'action' => $is_active? 'disable':'enable',
					'module' => $module,
				),
				is_network_admin()? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' )
			);
			$nonce_action = sprintf( '%s-module-%s', $is_active? 'disable':'enable', $module );
			$url = wp_nonce_url( $url, $nonce_action );
			return $url;
		}

		/**
		 * Get base url
		 *
		 * @since 1.8.8
		 */
		private function get_base_url() {
			if ( empty( $this->base_url ) ) {
				$page = $this->get_current_page();
				$this->base_url = add_query_arg(
					array(
						'page' => $page,
					),
					is_network_admin()? network_admin_url( 'admin.php' ):admin_url( 'admin.php' )
				);
			}
			return $this->base_url;
		}

		/**
		 * sanitize variables
		 *
		 * @since 3.0.0
		 */
		public function set_and_sanitize_variables() {
			$this->module = '';
			/**
			 * group
			 */
			$this->group = 'dashboard';
			if (
				isset( $_REQUEST['page'] )
				&& preg_match( '/branding_group_(.+)$/', $_REQUEST['page'], $matches )
			) {
				if ( array_key_exists( $matches[1], $this->submenu ) ) {
					$this->group = $matches[1];
				}
			}
			if ( 'dashboard' === $this->group ) {
				return;
			}
			/**
			 * module
			 */
			$input_module = filter_input( INPUT_POST, 'module', FILTER_SANITIZE_STRING );
			if ( empty( $input_module ) ) {
				$input_module = filter_input( INPUT_GET, 'module', FILTER_SANITIZE_STRING );
			}
			$is_empty = empty( $input_module );
			if ( ! $is_empty ) {
				if ( 'dashboard' !== $input_module ) {
					foreach ( $this->configuration as $module ) {
						if ( isset( $module['module'] ) && $input_module === $module['module'] ) {
							$this->module = $module['module'];
							return;
						}
					}
				}
			}
			/**
			 * module is not requested!
			 */
			$modules = $this->get_modules_by_group();
			/**
			 * try to find active one first
			 */
			$mods = $modules;
			while ( empty( $this->module ) && $module = array_shift( $mods ) ) {
				$is_active = ub_is_active_module( $module['key'] );
				if ( $is_active ) {
					$this->module = $module['module'];
				}
			}
			/**
			 * Set first module as current module.
			 */
			if ( empty( $this->module ) && is_array( $modules ) && ! empty( $modules ) ) {
				$module_data = array_shift( $modules );
				$this->module = $module_data['module'];
			}
		}

		/**
		 * get tab
		 *
		 * @since 1.9.1
		 */
		public function get_current_tab() {
			return $this->tab;
		}

		/**
		 * Activate/deactivate single module AJAX action.
		 *
		 * @since 1.9.6
		 */
		public function toggle_module() {
			if (
				isset( $_POST['nonce'] )
				&& isset( $_POST['state'] )
				&& isset( $_POST['module'] )
			) {
				/**
				 * get module
				 */
				$module_data = $this->get_module_by_module( $_POST['module'] );
				if ( is_wp_error( $module_data ) ) {
					$message = array(
						'class' => 'error',
						'message' => $module_data->get_error_message(),
					);
					wp_send_json_error( $message );
				}
				if ( ! wp_verify_nonce( $_POST['nonce'], $module_data['module'] ) ) {
					wp_send_json_error( array( 'message' => __( 'Nope! Security check failed!', 'ub' ) ) );
				}
				$result = false;
				$message = array(
					'class' => 'error',
					'message' => $this->messages['fail'],
				);
				/**
				 * try to activate or deactivate
				 */
				if ( 'on' == $_POST['state'] ) {
					$result = $this->activate_module( $module_data['key'] );
					if ( $result ) {
						$message = array(
							'class' => 'success',
							'message' => sprintf(
								__( '%s module is active now.', 'ub' ),
								$this->bold( $module_data['name'] )
							),
						);
					}
				} else {
					$result = $this->deactivate_module( $module_data['key'] );
					if ( $result ) {
						$message = array(
							'class' => 'success',
							'message' => sprintf(
								__( 'Module %s was deactivated without errors.', 'ub' ),
								$this->bold( $module_data['name'] )
							),
						);
					}
				}
				$this->add_message( $message );
				$data = array(
					'state' => $result,
					'module' => $_POST['module'],
				);
				wp_send_json_success( $data );
			}
			wp_send_json_error( array( 'message' => $this->messages['wrong'] ) );
		}

		/**
		 * Sort module by menu_title or page_title.
		 *
		 * @since 2.0.0
		 */
		public function sort_modules_by_name( $a, $b ) {
			$an = $a['name'];
			$bn = $b['name'];
			if ( isset( $a['menu_title'] ) ) {
				$an = $a['menu_title'];
			}
			if ( isset( $b['menu_title'] ) ) {
				$bn = $b['menu_title'];
			}
			return strcmp( $an, $bn );
		}

		private function get_module_content( $module ) {
			$is_active = ub_is_active_module( $module['key'] );
			if ( ! $is_active ) {
				return new WP_Error( 'error',__( 'This module is not active!', 'ub' ) );
			}
			/**
			 * Turn off Smush scripts
			 *
			 * @since 3.0.0
			 */
			add_filter( 'wp_smush_enqueue', '__return_false' );
			$content = '';
			/**
			 * Form encoding type
			 */
			$enctype = apply_filters( 'ultimatebranding_settings_form_enctype', 'multipart/form-data' );
			if ( ! empty( $enctype ) ) {
				$enctype = sprintf(
					' enctype="%s"',
					esc_attr( $enctype )
				);
			}
			/**
			 * Fields with form
			 */
			$action = preg_replace( '/-/', '_', 'ultimatebranding_settings_'.$module['module'] );
			$messages = apply_filters( $action.'_messages', $this->messages );
			if ( has_filter( $action ) ) {
				$content .= apply_filters( 'branda_before_module_form', '', $module );
				/**
				 * Filter Branda form classes.
				 *
				 * @since 3.0.0
				 *
				 * @param $classes array Array of Branda form classes.
				 * @param $module array Current module data,
				 */
				$classes = apply_filters( 'branda_form_classes',
					array(
						'branda-form',
						sprintf( 'module-%s', sanitize_title( $module['key'] ) ),
						$this->is_network? 'branda-network':'branda-single',
					),
					$module
				);
				$content .= sprintf(
					'<form action="%s" method="%s" class="module-%s"%s>',
					remove_query_arg( array( 'module' ) ),
					apply_filters( 'ultimatebranding_settings_form_method', 'post' ),
					esc_attr( implode( ' ', $classes ) ),
					$enctype
				);
				$content .= $this->hidden( 'module', $module['module'] );
				$content .= $this->hidden( 'page', $this->get_current_page() );
				if ( apply_filters( 'ultimatebranding_settings_form_add_fields', true ) ) {
					$content .= $this->hidden( 'action', 'process' );
					/**
					 * nonce
					 */
					$content .= wp_nonce_field( $action, '_wpnonce', false, false );
				}
				$content .= apply_filters( $action, '' );
				/**
				 * footer
				 */
				if ( isset( $module['add-bottom-save-button'] ) && $module['add-bottom-save-button'] ) {
					$filter = $action.'_process';
					if (
						has_filter( $filter )
						&& apply_filters( 'ultimatebranding_settings_panel_show_submit', true, $module )
					) {
						$content .= '<div class="sui-box-footer">';
						$content .= '<div class="sui-actions-right">';
						$args = array(
							'text' => __( 'Save Changes', 'ub' ),
							'sui' => 'blue',
							'icon' => 'save',
							'class' => 'branda-module-save',
						);
						$args = apply_filters( 'branda_after_form_save_button_args', $args, $module );
						$content .= $this->button( $args );
						$content .= '</div>'; // sui-actions-right
						$content .= '</div>'; // sui-box-header
					}
				}
				$content .= '</form>';
				do_action( 'branda_after_module_form', $module );
			} else {
				$content .= $this->sui_notice( $this->messages['wrong'], 'error', null, null, false, false );
				if ( $this->debug ) {
					error_log( 'Missing action: '.$action );
				}
			}
			/**
			 * filter module content.
			 *
			 * @since 3.0.0
			 *
			 * @param string $content Current module content.
			 * @param array $module Current module.
			 */
			return apply_filters( 'branda_get_module_content', $content, $module );
		}

		/**
		 * SUI button
		 */
		public function button( $args ) {
			$content = $data = '';
			$add_sui_loader = true;
			/**
			 * add data attributes
			 */
			if ( isset( $args['data'] ) ) {
				foreach ( $args['data'] as $key => $value ) {
					$data .= sprintf(
						' data-%s="%s"',
						sanitize_title( $key ),
						esc_attr( $value )
					);
				}
			}
			/**
			 * add ID attribute
			 */
			if ( isset( $args['id'] ) ) {
				$data .= sprintf( ' id="%s"', esc_attr( $args['id'] ) );
			}
			/**
			 * add style attribute
			 */
			if ( isset( $args['style'] ) ) {
				$data .= sprintf( ' style="%s"', esc_attr( $args['style'] ) );
			}
			/**
			 * Build classes
			 */
			$classes = array(
				'sui-button',
			);
			if ( isset( $args['only-icon'] ) && true === $args['only-icon'] ) {
				$classes = array();
				$add_sui_loader = false;
			}
			if ( isset( $args['sui'] ) ) {
				if ( ! empty( $args['sui'] ) ) {
					if ( ! is_array( $args['sui'] ) ) {
						$args['sui'] = array( $args['sui'] );
					}
					foreach ( $args['sui'] as $sui ) {
						$classes[] = sprintf( 'sui-button-%s', $sui );
					}
				} else if ( false !== $args['sui'] ) {
					$classes[] = 'sui-button-blue';
				}
			}
			if ( ! isset( $args['text'] ) ) {
				$classes[] = 'sui-button-icon';
			}
			if ( isset( $args['class'] ) ) {
				$classes[] = $args['class'];
			}
			if ( isset( $args['classes'] ) && is_array( $args['classes'] ) ) {
				$classes = array_merge( $classes, $args['classes'] );
			}
			/**
			 * Start
			 */
			$content .= sprintf(
				'<button class="%s" %s type="%s">',
				esc_attr( implode( ' ', $classes ) ),
				$data,
				isset( $args['type'] )? esc_attr( $args['type'] ):'button'
			);
			if ( $add_sui_loader ) {
				$content .= '<span class="sui-loading-text">';
			}
			/**
			 * Icon
			 */
			if ( isset( $args['icon'] ) ) {
				$content .= sprintf(
					'<i class="sui-icon-%s"></i>',
					sanitize_title( $args['icon'] )
				);
			}
			if ( isset( $args['text'] ) ) {
				$content .= esc_attr( $args['text'] );
			} else if ( isset( $args['value'] ) ) {
				$content .= esc_attr( $args['value'] );
			}
			if ( $add_sui_loader ) {
				$content .= '</span>';
				$content .= '<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>';
			}
			$content .= '</button>';
			/**
			 * Wrap
			 */
			if ( isset( $args['wrap'] ) && is_string( $args['wrap'] ) ) {
				$content = sprintf(
					'<div class="%s">%s</div>',
					esc_attr( $args['wrap'] ),
					$content
				);
			}
			return $content;
		}

		/**
		 * Helper for hidden field.
		 *
		 * @since 3.0.0
		 *
		 * @input string $name HTML form field name.
		 * @input string $value HTML form field value.
		 *
		 * @return string HTML hidden syntax.
		 */
		private function hidden( $name, $value ) {
			return sprintf(
				'<input type="hidden" name="%s" value="%s" />',
				esc_attr( $name ),
				esc_attr( $value )
			);
		}

		/**
		 * Remove default footer on Branda screens.
		 *
		 * @since 3.0.0
		 */
		public function remove_default_footer( $content ) {
			$screen = get_current_screen();
			if (
				is_a( $screen, 'WP_Screen' )
				&& preg_match( '/_page_branding/', $screen->id )
			) {
				remove_filter( 'update_footer', 'core_update_footer' );
				return '';
			}
			return $content;
		}

		/**
		 * Get current module
		 *
		 * @since 3.0.0
		 *
		 * @return string Current module.
		 */
		public function get_current_module() {
			return $this->module;
		}

		/**
		 * Check is current module?
		 *
		 * @since 3.0.0
		 */
		public function is_current_module( $module ) {
			return $this->module === $module;
		}

		/**
		 * reset whole module
		 *
		 * @since 3.0.0
		 */
		public function ajax_reset_module() {
			if (
				isset( $_POST['_wpnonce'] )
				&& isset( $_POST['module'] )
			) {
				/**
				 * get module
				 */
				$module_data = $this->get_module_by_module( $_POST['module'] );
				if ( is_wp_error( $module_data ) ) {
					$message = array(
						'class' => 'error',
						'message' => $module_data->get_error_message(),
					);
					wp_send_json_error( $message );
				}
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'reset-module-'.$module_data['module'] ) ) {
					wp_send_json_error( array( 'message' => $this->messages['security'] ) );
				}
				$filter = sprintf( 'ultimatebranding_settings_%s_reset', $module_data['module'] );
				$status = apply_filters( $filter, false );
				if ( $status ) {
					$message = array(
						'class' => 'success',
						'message' => sprintf(
							__( '%s module was reset.', 'ub' ),
							$this->bold( $module_data['name'] )
						),
					);
					$this->add_message( $message );
					wp_send_json_success();
				}
			}
			wp_send_json_error( array( 'message' => $this->messages['wrong'] ) );
		}

		/**
		 * Return messages
		 *
		 * @since 3.0.0
		 */
		public function get_messages() {
			return $this->messages;
		}

		/**
		 * Map of old -> new modules.
		 *
		 * @since 3.0.0
		 */
		private function get_modules_map() {
			$map = array(
				/**
				 * Dashboard Widgets
				 */
				'dashboard-text-widgets/dashboard-text-widgets.php' => 'widgets/dashboard-widgets.php',
				'custom-dashboard-welcome.php' => 'widgets/dashboard-widgets.php',
				'remove-wp-dashboard-widgets.php' => 'widgets/dashboard-widgets.php',
				'remove-wp-dashboard-widgets/remove-wp-dashboard-widgets.php' => 'widgets/dashboard-widgets.php',
				'dashboard-widgets/dashboard-widgets.php' => 'widgets/dashboard-widgets.php',
				'dashboard-feeds/dashboard-feeds.php' => 'widgets/dashboard-feeds.php',
				/**
				 * Turn on Content Header
				 */
				'global-header-content.php' => 'content/header.php',
				/**
				 * Turn on Content Footer
				 */
				'global-footer-content.php' => 'content/footer.php',
				/**
				 * Turn on Email Header
				 */
				'custom-email-from.php' => 'emails/headers.php',
				/**
				 * Turn on Registration Emails
				 */
				'custom-ms-register-emails.php' => 'emails/registration.php',
				/**
				 * Text Replacement ( Text Change )
				 */
				'site-wide-text-change.php' => 'utilities/text-replacement.php',
				'text-replacement/text-replacement.php' => 'utilities/text-replacement.php',
				/**
				 * Images: Favicons
				 * Images: Image upload size
				 */
				'favicons.php' => 'utilities/images.php',
				'image-upload-size.php' => 'utilities/images.php',
				/**
				 * Admin Bar
				 * Admin Bar Logo
				 */
				'custom-admin-bar.php' => 'admin/bar.php',
				'admin-bar-logo.php' => 'admin/bar.php',
				/**
				 * Login Screen
				 */
				'custom-login-screen.php' => 'login-screen/login-screen.php',
				/**
				 * Site Generator
				 */
				'site-generator-replacement.php' => 'utilities/site-generator.php',
				/**
				 * Email Temlate
				 */
				'htmlemail.php' => 'emails/template.php',
				/**
				 * Blog creation: signup code
				 */
				'signup-code.php' => 'login-screen/signup-code.php',
				/**
				 * Color Schemes
				 */
				'ultimate-color-schemes.php' => 'admin/color-schemes.php',
				/**
				 * Admin Footer Text
				 */
				'admin-footer-text.php' => 'admin/footer.php',
				/**
				 * Meta Widget
				 */
				'rebranded-meta-widget.php' => 'widgets/meta-widget.php',
				/**
				 * Admin Custom CSS
				 */
				'custom-admin-css.php' => 'admin/custom-css.php',
				/**
				 * Admin Message
				 */
				'admin-message.php' => 'admin/message.php',
				/**
				 * Comments Control
				 */
				'comments-control.php' => 'utilities/comments-control.php',
				/**
				 * Blog Description on Blog Creation
				 */
				'signup-blog-description.php' => 'front-end/signup-blog-description.php',
				/**
				 * Document
				 */
				'document.php' => 'front-end/document.php',
				/**
				 * Admin Help Content
				 */
				'admin-help-content.php' => 'admin/help-content.php',
				/**
				 * ms-site-check
				 */
				'ms-site-check/ms-site-check.php' => 'front-end/site-status-page.php',
				/**
				 * Cookie Notice
				 */
				'cookie-notice/cookie-notice.php' => 'front-end/cookie-notice.php',
				/**
				 * DB Error Page
				 */
				'db-error-page/db-error-page.php' => 'front-end/db-error-page.php',
				/**
				 * Author Box
				 */
				'author-box/author-box.php' => 'front-end/author-box.php',
				/**
				 * SMTP
				 */
				'smtp/smtp.php' => 'emails/smtp.php',
				/**
				 * Tracking Codes
				 */
				'tracking-codes/tracking-codes.php' => 'utilities/tracking-codes.php',
				/**
				 * Website Mode
				 */
				'maintenance/maintenance.php' => 'utilities/maintenance.php',
			);
			return $map;
		}

		/**
		 * Upgrade
		 *
		 * @since 3.0.0
		 */
		public function upgrade() {
			$key = 'branda_db_version';
			$db_version = intval( ub_get_option( $key, 0 ) );
			/**
			 * Branda 3.0.0
			 */
			$value = 20190205;
			if ( $value > $db_version ) {
				$modules = get_ub_activated_modules();
				$map = $this->get_modules_map();
				foreach ( $map as $old => $new ) {
					if (
						isset( $modules[ $old ] )
						&& 'yes' === $modules[ $old ]
					) {
						$this->deactivate_module( $old );
						$this->activate_module( $new );
					}
				}
				/**
				 * Turn on Registration Emails
				 */
				$module = 'export-import.php';
				if (
					isset( $modules[ $module ] )
					&& 'yes' === $modules[ $module ]
				) {
					$this->activate_module( 'utilities/import.php' );
					$this->activate_module( 'utilities/export.php' );
					$this->deactivate_module( $module );
				}
				/**
				 * Turn on Admin Menu
				 *
				 * Urgent: do not turn off previous modules!
				 */
				$m = array(
					'admin-panel-tips/admin-panel-tips.php',
					'link-manager.php',
					'remove-dashboard-link-for-users-without-site.php',
					'remove-permalinks-menu-item.php',
				);
				foreach ( $m as $module ) {
					if (
						isset( $modules[ $module ] )
						&& 'yes' === $modules[ $module ]
					) {
						$this->activate_module( 'admin/menu.php' );
					}
				}
				/**
				 * update
				 */
				ub_update_option( $key, $value );
			}
		}

		/**
		 * Add admin body classes
		 *
		 * @since 3.0.0
		 */
		public function add_branda_admin_body_class( $classes ) {
			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();
				if (
					preg_match( '/page_branda/', $screen->id )
					|| preg_match( '/page_branding/', $screen->id )
				) {
					if ( ! is_string( $classes ) ) {
						$classes = '';
					}
					$classes .= ' branda-admin-page';
					/**
					 * Shared UI
					 * Include library version as class on body.
					 *
					 * @since 3.0.0
					 */
					if ( defined( 'BRANDA_SUI_VERSION' ) ) {
						$sanitize_version = str_replace( '.', '-', BRANDA_SUI_VERSION );
						$classes .= sprintf( ' sui-%s', $sanitize_version );
					}
					/**
					 * add import class
					 */
					if ( 'import' === $this->module ) {
						if (
							isset( $_REQUEST['key'] )
							&& 'error' === $_REQUEST['key']
						) {
							$classes .= ' branda-import';
						}
						if (
							isset( $_REQUEST['step'] )
							&& 'import' === $_REQUEST['step']
						) {
							$classes .= ' branda-import';
						}
					}
				}
			}
			return $classes;
		}

		/**
		 * Get configuration
		 *
		 * @since 3.0.0
		 */
		public function get_configuration() {
			return $this->configuration;
		}

		/**
		 * Get modules
		 *
		 * @since 3.0.0
		 */
		public function get_modules() {
			return $this->modules;
		}

		/**
		 * Set last "write" module usage
		 *
		 * @since 3.0.0
		 */
		public function set_last_write( $module ) {
			$module = $this->get_module_by_module( $module );
			$this->stats->set_last_write( $module['key'] );
		}

		/**
		 * Copy settings from another modules.
		 *
		 * @since 3.0.0
		 */
		private function get_copy_button( $module ) {
			$content = '';
			if ( empty( $this->related ) || ! is_array( $this->related ) ) {
				return $content;
			}
			$module_module = $module['module'];
			$related = array();
			foreach ( $this->related as $section => $data ) {
				if ( array_key_exists( $module_module, $data ) ) {
					unset( $data[ $module_module ] );
					if ( empty( $data ) ) {
						continue;
					}
					$related[ $section ] = $data;
				}
			}
			if ( empty( $related ) ) {
				return $content;
			}
			$trans = array(
				'background' => __( 'Background', 'ub' ),
				'logo' => __( 'Logo', 'ub' ),
				'social_media_settings' => __( 'Social Media Settings', 'ub' ),
				'social_media' => __( 'Social Media', 'ub' ),
			);
			$c = array();
			foreach ( $related as $section => $section_data ) {
				foreach ( $section_data as $module_key => $module_key_data ) {
					if ( ! isset( $c[ $module_key ] ) ) {
						$c[ $module_key ] = $module_key_data;
						$c[ $module_key ]['options'] = array();
					}
					$c[ $module_key ]['options'][ $section ] = $trans[ $section ];
				}
			}
			$args = array(
				'related' => $c,
				'module' => $module,
			);
			$template = 'admin/common/copy';
			$content .= $this->render( $template, $args, true );
			return $content;
		}

		/**
		 * Copy settings from source to target module.
		 *
		 * @since 3.0.0
		 */
		public function ajax_copy_settings() {
			$target = filter_input( INPUT_POST, 'target_module', FILTER_SANITIZE_STRING );
			$source = filter_input( INPUT_POST, 'source_module', FILTER_SANITIZE_STRING );
			$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );
			if (
				empty( $target )
				|| empty( $source )
				|| empty( $nonce )
				|| ! isset( $_POST['sections'] )
			) {
				wp_send_json_error( array( 'message' => $this->messages['missing'] ) );
			}
			$nonce_action = sprintf( 'branda-copy-settings-%s', $target );
			if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
				wp_send_json_error( array( 'message' => $this->messages['security'] ) );
			}
			$source_module_data = $this->get_module_by_module( $source );
			$target_module_data = $this->get_module_by_module( $target );
			$source_module_configuration = ub_get_option( $source_module_data['options'][0] );
			$target_module_configuration = ub_get_option( $target_module_data['options'][0], array() );
			if ( empty( $source_module_configuration ) ) {
				wp_send_json_error( array( 'message' => __( 'Please configure source module first!', 'ub' ) ) );
			}
			/**
			 *
			 */
			if ( ! is_array( $target_module_configuration ) ) {
				$target_module_configuration = array();
			}
			$copy = array(
				'content' => array(),
				'design' => array(),
				'colors' => array(),
			);
			foreach ( $_POST['sections'] as $section ) {
				switch ( $section ) {
					case 'background':
						$copy['content'][] = 'content_background';
						$copy['design'][] = 'background_mode';
						$copy['design'][] = 'background_duration';
						$copy['design'][] = 'background_size';
						$copy['design'][] = 'background_size_width';
						$copy['design'][] = 'background_size_height';
						$copy['design'][] = 'background_focal';
						$copy['colors'][] = 'background_color';
					break;
					case 'logo':
						$copy['content'][] = 'logo_show';
						$copy['content'][] = 'logo_image';
						$copy['content'][] = 'logo_url';
						$copy['content'][] = 'logo_alt';
						$copy['content'][] = 'logo_image_meta';
						$copy['design'][] = 'logo_width';
						$copy['design'][] = 'logo_opacity';
						$copy['design'][] = 'logo_margin_top';
						$copy['design'][] = 'logo_margin_right';
						$copy['design'][] = 'logo_margin_bottom';
						$copy['design'][] = 'logo_margin_left';
						$copy['design'][] = 'logo_rounded';
					break;
					case 'social_media':
						if (
						isset( $source_module_configuration['content'] )
						&& is_array( $source_module_configuration['content'] )
						) {
							foreach ( $source_module_configuration['content'] as $key => $value ) {
								if ( ! preg_match( '/^social_media_/', $key ) ) {
									continue;
								}
								$target_module_configuration['content'][ $key ] = $value;
							}
						}
					break;
					case 'social_media_settings':
						$copy['design'][] = 'social_media_show';
						$copy['design'][] = 'social_media_target';
						$copy['design'][] = 'social_media_colors';
					break;
					default:
						wp_send_json_error( array( 'message' => $this->messages['wrong'] ) );
				}
			}
			foreach ( $copy as $group => $data ) {
				if (
					! isset( $target_module_configuration[ $group ] )
					|| ! is_array( $target_module_configuration[ $group ] )
				) {
					$target_module_configuration[ $group ] = array();
				}
				foreach ( $data as $option ) {
					if ( isset( $source_module_configuration[ $group ][ $option ] ) ) {
						$target_module_configuration[ $group ][ $option ] = $source_module_configuration[ $group ][ $option ];
					}
				}
			}
			$message = array(
				'class' => 'success',
				'message' => sprintf(
					__( 'Module %s was updated.', 'ub' ),
					$this->bold( $target_module_data['name'] )
				),
			);
			$this->add_message( $message );
			ub_update_option( $target_module_data['options'][0], $target_module_configuration );
			wp_send_json_success();
		}

		/**
		 * Load dashboard
		 *
		 * @since 3.0.0
		 */
		public function load_dashboard() {
			$modules = get_ub_activated_modules( 'raw' );
			if ( empty( $modules ) ) {
				$user_id = get_current_user_id();
				$show = get_user_meta( $user_id, 'show_welcome_dialog', true );
				$show = empty( $show );
				if ( $show  ) {
					$this->show_welcome_dialog = true;
					update_user_meta( $user_id, 'show_welcome_dialog', 'hide' );
				}
			}
		}

		/**
		 * Branda Welcome!
		 *
		 * @since 3.0.0
		 */
		public function ajax_welcome() {
			$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );
			if ( ! wp_verify_nonce( $nonce, 'branda-welcome-all-modules' ) ) {
				$args = array(
					'class' => 'error',
					'message' => $this->messages['security'],
				);
				wp_send_json_error( $args );
			}
			$args = array(
				'dialog_id' => 'branda-welcome',
				'modules' => $this->get_modules_stats(),
				'groups' => branda_get_groups_list(),
			);
			$template = 'admin/dashboard/welcome-modules';
			$args = array(
				'content' => $this->render( $template, $args, true ),
				'title' => esc_html__( 'Activate Modules', 'ub' ),
				'description' => esc_html__( 'Choose the modules you want to activate. Each module helps you white-label a specific part of your website. If you’re not sure or forget to activate any module now, you can always do that later.', 'ub' ),
			);
			wp_send_json_success( $args );
		}

		/**
		 * Check if high contrast mode is enabled.
		 *
		 * For the accessibility support, enable/disable
		 * high contrast support on admin area.
		 *
		 * @since 3.0.0
		 *
		 * @return bool
		 */
		private function high_contrast_mode() {
			// Get accessibility settings.
			$accessibility_options = ub_get_option( 'ub_accessibility', array() );
			if ( isset( $accessibility_options['accessibility']['high_contrast'] )
				&& 'on' === $accessibility_options['accessibility']['high_contrast'] ) {
				return true;
			}
			return false;
		}

		/**
		 * Common hooks for all screens
		 *
		 * @since 3.0.1
		 */
		public function add_action_hooks() {
			// Filter built-in wpmudev branding script.
			add_filter( 'wpmudev_whitelabel_plugin_pages', array( $this, 'builtin_wpmudev_branding' ) );
		}

		/**
		 * Add more pages to builtin wpmudev branding.
		 *
		 * @since 3.0.1
		 *
		 * @param array $plugin_pages Nextgen pages is not introduced in built in wpmudev branding.
		 *
		 * @return array
		 */
		public function builtin_wpmudev_branding( $plugin_pages ) {
			$plugin_pages[ $this->top_page_slug ] = array(
				'wpmudev_whitelabel_sui_plugins_footer',
				'wpmudev_whitelabel_sui_plugins_doc_links',
			);
			return $plugin_pages;
		}

		/**
		 * Handle Branda SUI wrapper container classes.
		 *
		 * @since 3.0.6
		 */
		public function add_sui_wrap_classes( $classes ) {
			if ( is_string( $classes ) ) {
				$classes = array( $classes );
			}
			if ( ! is_array( $classes ) ) {
				$classes = array();
			}
			$classes[] = 'sui-wrap';
			$classes[] = 'sui-wrap-branda';
			/**
			 * Add high contrast mode.
			 */
			$is_high_contrast_mode = $this->high_contrast_mode();
			if ( $is_high_contrast_mode ) {
				$classes[] = 'sui-color-accessible';
			}
			/**
			 * Set hide branding
			 *
			 * @since 3.0.6
			 */
			$hide_branding = apply_filters( 'wpmudev_branding_hide_branding', $this->hide_branding );
			if ( $hide_branding ) {
				$classes[] = 'no-branda';
			}
			return $classes;
		}

		/**
		 * Delete image from modules, when it is deleted from WordPress
		 *
		 * @since 3.1.0
		 */
		public function delete_attachment_from_configs( $attachemnt_id ) {
			$affected_modules = array(
				'admin-bar',
				'db-error-page',
				'login-screen',
				'ms-site-check',
				'images',
				'maintenance',
			);
			foreach ( $this->configuration as $module ) {
				if ( ! in_array( $module['module'], $affected_modules ) ) {
					continue;
				}
				if ( ! isset( $module['options'] ) ) {
					continue;
				}
				foreach ( $module['options'] as $option_name ) {
					$value = ub_get_option( $option_name );
					if ( empty( $value ) ) {
						continue;
					}

					$update = false;
					foreach ( $value as $group => $group_data ) {
						if ( ! is_array( $group_data ) ) {
							continue;
						}
						foreach ( $group_data as $key => $field ) {
							switch ( $key ) {
								/**
								 * Single image
								 */
								case 'favicon':
								case 'logo_image':
								case 'logo':
									$field = intval( $field );
									if ( $attachemnt_id === $field ) {
										$update = true;
										unset( $value[ $group ][ $key ] );
										$key .= '_meta';
										if ( isset( $value[ $group ][ $key ] ) ) {
											unset( $value[ $group ][ $key ] );
										}
									}
								break;
								/**
								 * Background
								 */
								case 'content_background':
									if ( is_array( $field ) ) {
										foreach ( $field as $index => $one ) {
											$id = intval( $one['value'] );
											if ( $attachemnt_id === $id ) {
												if ( isset( $value[ $group ][ $key ] ) ) {
													$update = true;
													unset( $value[ $group ][ $key ][ $index ] );
												}
											}
										}
									}
								break;
								default:
							}
						}
					}
					if ( $update ) {
						ub_update_option( $option_name, $value );
					}
				}
			}
		}
		/**
		 * Get inline style for box summary-image div
		 *
		 * @since 3.2.0
		 * @return string
		 */
		private function get_box_summary_image_style() {
			$image_url = apply_filters( 'wpmudev_branding_hero_image', null );
			if ( ! empty( $image_url ) ) {
				return 'background-image:url(' . esc_url( $image_url ) . ')';
			}
			return '';
		}
	}
}

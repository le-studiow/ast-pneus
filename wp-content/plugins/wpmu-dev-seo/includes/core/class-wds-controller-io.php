<?php
/**
 * Import and export settings admin handler
 *
 * @package wpmu-dev-seo
 */

/**
 * IO controller class
 */
class Smartcrawl_Controller_IO extends Smartcrawl_WorkUnit {

	/**
	 * Singleton instance holder
	 *
	 * @var Smartcrawl_Controller_IO
	 */
	private static $_instance;

	/**
	 * Controller state flag
	 *
	 * @var bool
	 */
	private $_is_running = false;

	/**
	 * Boot controller listeners
	 *
	 * Do it only once, if they're already up do nothing
	 *
	 * @return bool Status
	 */
	public static function serve() {
		$me = self::get();
		if ( $me->is_running() ) {
			return false;
		}

		return $me->_add_hooks();
	}

	/**
	 * Obtain instance without booting up
	 *
	 * @return Smartcrawl_Controller_IO instance
	 */
	public static function get() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Check if we already have the actions bound
	 *
	 * @return bool Status
	 */
	public function is_running() {
		return $this->_is_running;
	}

	/**
	 * Bind listening actions
	 *
	 * @return bool
	 */
	private function _add_hooks() {

		add_action( 'admin_init', array( $this, 'dispatch_actions' ) );
		add_action( 'all_admin_notices', array( $this, 'show_notice' ), 10 );
		add_action( 'wp_ajax_import_yoast_data', array( $this, 'import_yoast_data' ) );
		add_action( 'wp_ajax_import_aioseop_data', array( $this, 'import_aioseop_data' ) );

		$this->_is_running = true;

		return ! ! $this->_is_running;
	}

	/**
	 * Stops controller listeners
	 *
	 * @return bool
	 */
	public static function stop() {
		$me = self::get();
		if ( ! $me->is_running() ) {
			return false;
		}

		return $me->_remove_hooks();
	}

	/**
	 * Unbinds listening actions
	 *
	 * @return bool
	 */
	private function _remove_hooks() {

		remove_action( 'admin_init', array( $this, 'dispatch_actions' ) );

		$this->_is_running = false;

		return ! $this->_is_running;
	}

	/**
	 * Filter prefix getter
	 *
	 * @return string
	 */
	public function get_filter_prefix() {
		return 'wds-controller-io';
	}

	/**
	 * Dispatches action listeners for admin pages
	 *
	 * @return bool
	 */
	public function dispatch_actions() {
		if ( ! is_network_admin() && ! is_admin() ) {
			return false;
		}
		if ( is_network_admin() && ! current_user_can( 'manage_network_options' ) ) {
			return false;
		}
		if ( is_admin() && ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$data = $this->get_request_data();
		$action = ! empty( $data['io-action'] ) ? sanitize_text_field( $data['io-action'] ) : false;
		if ( 'export' === $action ) {
			return $this->process_export_request();
		}
		if ( 'import' === $action ) {
			return $this->process_import_request();
		}

		return false;
	}

	/**
	 * Handles export request processing
	 *
	 * @return bool
	 */
	public function process_export_request() {
		if ( ! is_network_admin() && ! is_admin() ) {
			wp_die( 'Nope' );
		}
		if ( is_network_admin() && ! current_user_can( 'manage_network_options' ) ) {
			wp_die( 'Nope' );
		}
		if ( is_admin() && ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Nope' );
		}

		$json = Smartcrawl_Export::load()->get_json();
		$filename = 'wds-settings-' . date( 'Y-m-d.H-i-s' ) . '.json';
		if ( empty( $json ) ) {
			$this->add_error( 'export', __( 'Something went wrong gathering your settings data for export. If the problem persists then contact support.', 'wds' ) );

			return false;
		}

		header( 'Content-Type: application/json' );
		header( "Content-Disposition: attachment; filename=\"{$filename}\"" );
		header( 'Cache-Control: private' );
		header( 'Pragma: private' );

		echo $json; // phpcs:ignore -- Can't escape json
		@ob_flush(); // phpcs:ignore
		die;
	}

	/**
	 * Handles import request processing
	 *
	 * @return bool
	 */
	public function process_import_request() {
		if ( ! is_network_admin() && ! is_admin() ) {
			wp_die( 'Nope' );
		}
		if ( is_network_admin() && ! current_user_can( 'manage_network_options' ) ) {
			wp_die( 'Nope' );
		}
		if ( is_admin() && ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Nope' );
		}

		$_POST['option_page'] = 'wds-export';

		// Verify uploaded file.
		$json = false;
		if ( empty( $_FILES['wds_import_json'] ) ) {
			$this->add_error( 'import', __( 'Supplied file is invalid or not a JSON file. Check that your selected file is a SmartCrawl JSON export and try again. If the problem persists then contact support.', 'wds' ) );
		} else {
			$file = $_FILES['wds_import_json'];
			$error = __( 'Supplied file is broken. Try creating the export file again then re-importing.', 'wds' );

			if ( ! isset( $file['error'] ) || is_array( $file['error'] ) || empty( $file['tmp_name'] ) ) {
				$this->add_error( 'import', $error );

				return false;
			}

			if ( UPLOAD_ERR_OK !== $file['error'] ) {
				$this->add_error( 'import', $error );

				return false;
			}

			if ( wp_max_upload_size() < $file['size'] ) {
				$this->add_error( 'import', $error );

				return false;
			}

			$mime = false;
			if ( extension_loaded( 'fileinfo' ) ) {
				$fi = finfo_open( FILEINFO_MIME_TYPE );
				$mime = finfo_file( $fi, $file['tmp_name'] );
				finfo_close( $fi );
			} elseif ( function_exists( 'mime_content_type' ) ) {
				$mime = mime_content_type( $file['tmp_name'] );
			} else {
				// euh... let's go with least secure approach.
				$mime = $file['type'];
			}
			$mime_types = array(
				'application/json',
				'text/json',
				'text/x-json',
				'text/plain',
			);
			if ( ! in_array( $mime, $mime_types, true ) ) {
				$this->add_error( 'import', $error );

				return false;
			}

			$json = smartcrawl_file_get_contents( $file['tmp_name'] );
			$test = json_decode( $json, true );
			if ( empty( $test ) ) {
				$json = false;
			}
		}

		if ( empty( $json ) ) {
			$this->add_error( 'import', __( 'Supplied file is broken. Try creating the export file again then re-importing.', 'wds' ) );

			return false;
		}

		$instance = Smartcrawl_Import::load( $json );
		$result = $instance->save();

		if ( empty( $result ) ) {
			$msg = array( __( 'Something went wrong importing your data.', 'wds' ) );
			$instance_errors = $instance->get_errors();
			if ( ! empty( $instance_errors ) ) {
				foreach ( $instance->get_errors() as $code => $error ) {
					$msg[] = '- ' . esc_html( $error );
				}
			}
			$msg[] = __( 'Try importing the selected file again or create a new export file. Contact support if you need further assistance.', 'wds' );
			$this->add_error( 'import', join( '<br />', $msg ) );

			return false;
		}

		$errors = $this->get_errors();
		if ( empty( $errors ) ) {
			wp_safe_redirect( esc_url_raw( add_query_arg( 'import', 'success', remove_query_arg( 'updated' ) ) ) );
			die;
		}

		return true;
	}

	public function import_yoast_data() {
		$options = $this->get_import_options_from_request();
		$this->do_import( new Smartcrawl_Yoast_Importer(), $options );
	}

	/**
	 * @param $importer Smartcrawl_Importer
	 * @param $plugin
	 */
	private function do_import( $importer, $options = array() ) {
		$result = array( 'success' => false );

		if ( ! $this->user_has_permission_to_import() ) {
			$result['message'] = __( "You don't have permission to perform this operation.", 'wds' );
			die( wp_json_encode( $result ) );
		}

		if ( ! $importer->data_exists() ) {
			$result['message'] = __( "We couldn't find any compatible data to import.", 'wds' );
			die( wp_json_encode( $result ) );
		}

		if ( is_multisite() ) {
			$importer->import_for_all_sites( $options );
			$in_progress = $importer->is_network_import_in_progress();
		} else {
			$importer->import( $options );
			$in_progress = $importer->is_import_in_progress();
		}
		$result['success'] = true;
		$result['in_progress'] = $in_progress;
		$result['status'] = $importer->get_status();
		$result['deactivation_url'] = $importer->get_deactivation_link();

		die( wp_json_encode( $result ) );
	}

	private function user_has_permission_to_import() {
		if ( ! is_network_admin() && ! is_admin() ) {
			return false;
		}
		if ( is_network_admin() && ! current_user_can( 'manage_network_options' ) ) {
			return false;
		}
		if ( is_admin() && ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		return true;
	}

	public function import_aioseop_data() {
		$options = $this->get_import_options_from_request();
		$this->do_import( new Smartcrawl_AIOSEOP_Importer(), $options );
	}

	private function get_request_data() {
		if ( isset( $_POST['_wds_nonce'] ) && wp_verify_nonce( $_POST['_wds_nonce'], 'wds-io-nonce' ) ) {
			return stripslashes_deep( $_POST );
		} else {
			if ( ! empty( $_POST['io-action'] ) ) {
				$this->add_error( 'io-nonce-failure', __( 'Invalid parameters. Try refreshing the page and attempting again.', 'wds' ) );
			}
		}

		return array();
	}

	private function get_import_options_from_request() {
		$request_data = $this->get_request_data();
		$options = smartcrawl_get_array_value( $request_data, 'items_to_import' );
		$options['force-restart'] = (boolean) smartcrawl_get_array_value( $request_data, 'restart' );

		return empty( $options ) ? array() : $options;
	}

	public function show_notice() {
		$settings_errors = self::get()->get_errors();

		if (
			! empty( $_GET['import'] )
			&& 'success' === $_GET['import']
		) {
			$class = 'sui-notice-info';
			$message = esc_html__( 'Settings successfully imported', 'wds' );
		} else {
			// TODO: when SUI adds support for stacked errors, show all of the following instead of just the first
			$class = 'sui-notice-error';
			$message = ! empty( $settings_errors )
				? array_shift( $settings_errors )
				: '';
		}

		if ( $message ) {
			Smartcrawl_Simple_Renderer::render( 'floating-message', array(
				'class'   => $class,
				'message' => $message,
			) );
		}
	}
}


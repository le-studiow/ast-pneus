<?php
/**
 * Checkup service
 *
 * @package wpmu-dev-seo
 */

/**
 * Checkup service admin handler class
 */
class Smartcrawl_Checkup_Settings extends Smartcrawl_Settings_Admin {

	/**
	 * Singleton instance
	 *
	 * @var Smartcrawl_Checkup_Settings
	 */
	private static $_instance;

	/**
	 * Singleton instance getter
	 *
	 * @return Smartcrawl_Checkup_Settings instance
	 */
	public static function get_instance() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Validate submitted options
	 *
	 * @param array $input Raw input.
	 *
	 * @return array Validated input
	 */
	public function validate( $input ) {
		$result = array();
		$email_recipients = smartcrawl_get_array_value( $input, 'checkup-email-recipients' );
		if ( ! empty( $email_recipients ) ) {
			$sanitized_recipients = array();
			foreach ( $email_recipients as $recipient ) {
				$recipient_name = smartcrawl_get_array_value( $recipient, 'name' );
				$recipient_email = smartcrawl_get_array_value( $recipient, 'email' );

				if (
					$recipient_name && $recipient_email
					&& sanitize_text_field( $recipient_name ) === $recipient_name
					&& sanitize_email( $recipient_email ) === $recipient_email
					&& ! self::recipient_exists( $recipient, $sanitized_recipients )
				) {
					$sanitized_recipients[] = $recipient;
				} else {
					add_settings_error(
						$this->option_name,
						'email-recipients-invalid',
						esc_html__( 'Some email recipients could not be saved.', 'wds' )
					);
				}
			}
			$result['checkup-email-recipients'] = $sanitized_recipients;
		}

		if ( empty( $email_recipients ) ) {
			$defaults = $this->get_default_options();
			$result['checkup-email-recipients'] = $defaults['checkup-email-recipients'];
		}

		if ( empty( $input['checkup-cron-enable'] ) || empty( $email_recipients ) ) {
			$result['checkup-cron-enable'] = false;
		} else {
			$result['checkup-cron-enable'] = true;
		}

		$frequency = ! empty( $input['checkup-frequency'] )
			? Smartcrawl_Controller_Cron::get()->get_valid_frequency( $input['checkup-frequency'] )
			: Smartcrawl_Controller_Cron::get()->get_default_frequency();
		$result['checkup-frequency'] = $frequency;

		$dow = isset( $input['checkup-dow'] ) && is_numeric( $input['checkup-dow'] )
			? (int) $input['checkup-dow']
			: 0;
		$result['checkup-dow'] = in_array( $dow, range( 0, 6 ), true ) ? $dow : 0;

		$tod = isset( $input['checkup-tod'] ) && is_numeric( $input['checkup-tod'] )
			? (int) $input['checkup-tod']
			: 0;
		$result['checkup-tod'] = in_array( $tod, range( 0, 23 ), true ) ? $tod : 0;

		return $result;
	}

	/**
	 * Gets default options set and their initial values
	 *
	 * @return array
	 */
	public function get_default_options() {
		return array(
			'checkup-cron-enable'      => false,
			'checkup-frequency'        => 'weekly',
			'checkup-dow'              => rand( 0, 6 ),
			'checkup-tod'              => rand( 0, 23 ),
			'checkup-email-recipients' => array( self::get_email_recipient( get_current_user_id() ) ),
		);
	}

	/**
	 * Initialize admin pane
	 */
	public function init() {
		$this->option_name = 'wds_checkup_options';
		$this->name = Smartcrawl_Settings::COMP_CHECKUP;
		$this->slug = Smartcrawl_Settings::TAB_CHECKUP;
		$this->action_url = admin_url( 'options.php' );
		$this->page_title = __( 'SmartCrawl Wizard: SEO Checkup', 'wds' );

		parent::init();
	}

	public function get_title() {
		return __( 'SEO Checkup', 'wds' );
	}

	/**
	 * Process run action
	 */
	public function process_run_action() {
		if ( isset( $_GET['_wds_nonce'], $_GET['run-checkup'] ) && wp_verify_nonce( $_GET['_wds_nonce'], 'wds-checkup-nonce' ) ) { // Simple presence switch, no value.
			return $this->run_checkup();
		}
	}

	public static function checkup_url() {
		$checkup_url = Smartcrawl_Settings_Admin::admin_url( Smartcrawl_Settings::TAB_CHECKUP );

		return esc_url_raw( add_query_arg( array(
			'run-checkup' => 'yes',
			'_wds_nonce'  => wp_create_nonce( 'wds-checkup-nonce' ),
		), $checkup_url ) );
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
			'options'          => $options,
			'active_tab'       => $this->_get_active_tab( 'tab_checkup' ),
			'email_recipients' => self::get_email_recipients(),
		);

		$service = Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_CHECKUP );
		wp_enqueue_script( Smartcrawl_Controller_Assets::CHECKUP_PAGE_JS );

		$this->_render_page( 'checkup/checkup-settings', $arguments );
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

	/**
	 * Since 2.3.0 email recipients are stored as simple Name, Email pairs.
	 * Prior to this recipients were stored as an array of user IDs. This method merges the two formats into a single array.
	 *
	 * @return array
	 */
	public static function get_email_recipients() {
		$email_recipients = array();
		$options = Smartcrawl_Settings::get_component_options( self::COMP_CHECKUP );
		$new_recipients = empty( $options['checkup-email-recipients'] )
			? array()
			: $options['checkup-email-recipients'];
		$old_recipients = empty( $options['email-recipients'] )
			? array()
			: $options['email-recipients'];

		foreach ( $old_recipients as $user_id ) {
			if ( ! is_numeric( $user_id ) ) {
				continue;
			}
			$old_recipient = self::get_email_recipient( $user_id );
			if ( self::recipient_exists( $old_recipient, $new_recipients ) ) {
				continue;
			}

			$email_recipients[] = $old_recipient;
		}

		return array_merge(
			$email_recipients,
			$new_recipients
		);
	}

	private static function recipient_exists( $recipient, $recipient_array ) {
		$emails = array_column( $recipient_array, 'email' );
		$needle = (string) smartcrawl_get_array_value( $recipient, 'email' );

		return in_array( $needle, $emails, true );
	}

	private static function get_email_recipient( $user_id ) {
		$user = Smartcrawl_Model_User::get( $user_id );
		$email_details = array(
			'name'  => $user->get_display_name(),
			'email' => $user->get_email(),
		);
		return $email_details;
	}
}


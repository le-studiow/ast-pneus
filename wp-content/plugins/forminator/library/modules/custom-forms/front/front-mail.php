<?php

/**
 * Front ajax for custom forms
 *
 * @since 1.0
 */
class Forminator_CForm_Front_Mail extends Forminator_Mail {

	protected $message_vars;

	/**
	 * Default content type
	 *
	 * @since 1.5
	 * @var string
	 */
	protected $content_type = 'text/html; charset=UTF-8';

	/**
	 * Skipped custom form_data parsing
	 *
	 * @since 1.0.3
	 * @var array
	 */
	private $skip_custom_form_data
		= array(
			'admin' => array(),
			'user'  => array(),
		);

	/**
	 * Initialize the mail
	 *
	 * @since 1.0
	 *
	 * @param string $user_email - the user email
	 * @param array  $post_vars  - post variables
	 */
	public function init( $post_vars ) {
		$user_email  = false;
		$user_name   = '';
		$user_login  = '';
		$embed_id    = $post_vars['page_id'];
		$embed_title = get_the_title( $embed_id );
		$embed_url   = forminator_get_current_url();
		$site_url    = site_url();

		//Check if user is logged in
		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			$user_email   = $current_user->user_email;
			if ( ! empty( $current_user->user_firstname ) ) {
				$user_name = $current_user->user_firstname . ' ' . $current_user->user_lastname;
			} elseif ( ! empty( $current_user->display_name ) ) {
				$user_name = $current_user->display_name;
			} else {
				$user_name = $current_user->display_name;
			}
			$user_login = $current_user->user_login;
		}

		//Set up mail variables
		$message_vars = forminator_set_message_vars( $embed_id, $embed_title, $embed_url, $user_name, $user_email, $user_login, $site_url );

		/**
		 * Message variables filter
		 *
		 * @since 1.0.2
		 *
		 * @param array $message_vars - the message variables
		 * @param int   $embed_id     - the current form id
		 * @param array $post_vars    - the post params
		 *
		 * @return array $message_vars
		 */
		$this->message_vars = apply_filters( 'forminator_custom_form_message_vars', $message_vars, $embed_id, $post_vars );
	}

	/**
	 * Process mail
	 *
	 * @since 1.0
	 *
	 * @param Forminator_Custom_Form_Model $custom_form
	 * @param array                        $data
	 * @param Forminator_Form_Entry_Model  $entry - saved entry @since 1.0.3
	 */
	public function process_mail( $custom_form, $data, Forminator_Form_Entry_Model $entry ) {
		$setting = $custom_form->settings;

		if ( ! isset( $data['current_url'] ) || empty( $data['current_url'] ) ) {
			$data['current_url'] = forminator_get_current_url();
		}

		// Set If E-mail Sender and Names are overridden
		if ( isset( $custom_form->settings['override-defaults'] ) && ( "true" === $custom_form->settings['override-defaults'] ) ) {
			if ( isset( $custom_form->settings['override-sender-name'] ) ) {
				$this->set_sender_name( $custom_form->settings['override-sender-name'] );
			}

			if ( isset( $custom_form->settings['override-sender-mail'] ) ) {
				$this->set_sender_email( $custom_form->settings['override-sender-mail'] );
			}

			$this->set_headers();

		}

		/**
		 * Message data filter
		 *
		 * @since 1.0.4
		 *
		 * @param array                        $data        - the post data
		 * @param Forminator_Custom_Form_Model $custom_form - the form
		 * @param Forminator_Form_Entry_Model  $entry       - saved entry @since 1.0.3
		 *
		 *
		 * @return array $data
		 */
		$data = apply_filters( 'forminator_custom_form_mail_data', $data, $custom_form, $entry );

		/**
		 * Action called before mail is sent
		 *
		 * @param Forminator_CForm_Front_Mail - the current form
		 * @param Forminator_Custom_Form_Model - the current form
		 * @param array                       $data  - current data
		 * @param Forminator_Form_Entry_Model $entry - saved entry @since 1.0.3
		 */
		do_action( 'forminator_custom_form_mail_before_send_mail', $this, $custom_form, $data, $entry );

		//Process Email
		if ( $this->send_admin_mail( $setting ) || $this->send_user_mail( $setting ) ) {
			$this->init( $_POST ); // WPCS: CSRF OK
			//Process admin mail
			if ( $this->send_admin_mail( $setting ) ) {
				$recipients = $this->get_admin_email_recipients( $setting, $data, $custom_form, $entry );

				/**
				 * Custom form admin mail recipients filter
				 *
				 * @since 1.0.3
				 *
				 * @param array $recipients
				 * @param Forminator_Custom_Form_Model - the current form
				 *
				 * @return array $recipients
				 */
				$recipients = apply_filters_deprecated(
					'forminator_custom_form_mail_admin_recipients',
					array( $recipients, $custom_form, $data, $entry, $this ),
					'1.6.2',
					'forminator_get_admin_email_recipients'
				);

				if ( ! empty( $recipients ) ) {
					$subject = forminator_replace_form_data( $setting['admin-email-title'], $data, $custom_form, $entry );
					$subject = forminator_replace_variables( $subject, $custom_form->id, $data['current_url'] );
					$subject = forminator_replace_custom_form_data( $subject, $custom_form, $data, $entry, $this->skip_custom_form_data['admin'] );

					$message = forminator_replace_form_data( $setting['admin-email-editor'], $data, $custom_form, $entry );
					$message = forminator_replace_variables( $message, $custom_form->id, $data['current_url'] );
					$message = forminator_replace_custom_form_data( $message, $custom_form, $data, $entry, $this->skip_custom_form_data['admin'] );
					/**
					 * Custom form mail subject filter
					 *
					 * @since 1.0.2
					 *
					 * @param string $subject
					 * @param Forminator_Custom_Form_Model - the current form
					 *
					 * @return string $subject
					 */
					$subject = apply_filters( 'forminator_custom_form_mail_admin_subject', $subject, $custom_form, $data, $entry, $this );

					/**
					 * Custom form mail message filter
					 *
					 * @since 1.0.2
					 *
					 * @param string $message
					 * @param Forminator_Custom_Form_Model - the current form
					 *
					 * @return string $message
					 */
					$message = apply_filters( 'forminator_custom_form_mail_admin_message', $message, $custom_form, $data, $entry, $this );

					$from_name = $this->sender_name;
					if ( isset( $setting['admin-email-from-name'] ) && ! empty( $setting['admin-email-from-name'] ) ) {
						$setting_from_name = $setting['admin-email-from-name'];
						$setting_from_name = forminator_replace_form_data( $setting_from_name, $data );
						$setting_from_name = forminator_replace_variables( $setting_from_name, $custom_form->id, $data['current_url'] );
						$setting_from_name = forminator_replace_custom_form_data( $setting_from_name, $custom_form, $data, $entry, $this->skip_custom_form_data['admin'] );

						if ( ! empty( $setting_from_name ) ) {
							$from_name = $setting_from_name;
						}
					}
					/**
					 * Filter `From` name of mail that send to admin
					 *
					 * @since 1.5
					 *
					 * @param string                       $from_name
					 * @param Forminator_Custom_Form_Model $custom_form Current Form Model
					 * @param array                        $data        POST data
					 * @param Forminator_Form_Entry_Model  $entry       entry model
					 * @param Forminator_CForm_Front_Mail  $this        mail class
					 */
					$from_name = apply_filters( 'forminator_custom_form_mail_admin_from_name', $from_name, $custom_form, $data, $entry, $this );

					$from_email = $this->sender_email;
					if ( isset( $setting['admin-email-from-address'] ) && ! empty( $setting['admin-email-from-address'] ) ) {
						$setting_from_address = $setting['admin-email-from-address'];
						$setting_from_address = forminator_replace_form_data( $setting_from_address, $data );
						$setting_from_address = forminator_replace_variables( $setting_from_address, $custom_form->id, $data['current_url'] );
						$setting_from_address = forminator_replace_custom_form_data( $setting_from_address, $custom_form, $data, $entry, $this->skip_custom_form_data['admin'] );

						if ( is_email( $setting_from_address ) ) {
							$from_email = $setting_from_address;
						}
					}
					/**
					 * Filter `From` email address of mail that send to admin
					 *
					 * @since 1.5
					 *
					 * @param string                       $from_email
					 * @param Forminator_Custom_Form_Model $custom_form Current Form Model
					 * @param array                        $data        POST data
					 * @param Forminator_Form_Entry_Model  $entry       entry model
					 * @param Forminator_CForm_Front_Mail  $this        mail class
					 */
					$from_email = apply_filters( 'forminator_custom_form_mail_admin_from_email', $from_email, $custom_form, $data, $entry, $this );

					$reply_to_address = '';
					if ( isset( $setting['admin-email-reply-to-address'] ) && ! empty( $setting['admin-email-reply-to-address'] ) ) {
						$setting_reply_to_address = $setting['admin-email-reply-to-address'];
						$setting_reply_to_address = forminator_replace_form_data( $setting_reply_to_address, $data );
						$setting_reply_to_address = forminator_replace_variables( $setting_reply_to_address, $custom_form->id, $data['current_url'] );
						$setting_reply_to_address = forminator_replace_custom_form_data( $setting_reply_to_address, $custom_form, $data, $entry, $this->skip_custom_form_data['admin'] );

						if ( is_email( $setting_reply_to_address ) ) {
							$reply_to_address = $setting_reply_to_address;
						}
					}

					/**
					 * Filter `Reply To` email address of mail that send to admin
					 *
					 * @since 1.5
					 *
					 * @param string                       $reply_to_address
					 * @param Forminator_Custom_Form_Model $custom_form Current Form Model
					 * @param array                        $data        POST data
					 * @param Forminator_Form_Entry_Model  $entry       entry model
					 * @param Forminator_CForm_Front_Mail  $this        mail class
					 */
					$reply_to_address = apply_filters( 'forminator_custom_form_mail_admin_reply_to', $reply_to_address, $custom_form, $data, $entry, $this );

					$cc_addresses = array();
					if ( isset( $setting['admin-email-cc-address'] ) && ! empty( $setting['admin-email-cc-address'] ) && is_array( $setting['admin-email-cc-address'] ) ) {
						$setting_cc_addresses = $setting['admin-email-cc-address'];

						foreach ( $setting_cc_addresses as $key => $setting_cc_address ) {
							$setting_cc_address = forminator_replace_form_data( $setting_cc_address, $data );
							$setting_cc_address = forminator_replace_variables( $setting_cc_address, $custom_form->id, $data['current_url'] );
							$setting_cc_address = forminator_replace_custom_form_data( $setting_cc_address, $custom_form, $data, $entry, $this->skip_custom_form_data['admin'] );
							if ( is_email( $setting_cc_address ) ) {
								$cc_addresses[] = $setting_cc_address;
							}
						}
					}
					/**
					 * Filter `CC` email addresses of mail that send to admin
					 *
					 * @since 1.5
					 *
					 * @param array                        $cc_addresses
					 * @param Forminator_Custom_Form_Model $custom_form Current Form Model
					 * @param array                        $data        POST data
					 * @param Forminator_Form_Entry_Model  $entry       entry model
					 * @param Forminator_CForm_Front_Mail  $this        mail class
					 */
					$cc_addresses = apply_filters( 'forminator_custom_form_mail_admin_cc_addresses', $cc_addresses, $custom_form, $data, $entry, $this );

					$bcc_addresses = array();
					if ( isset( $setting['admin-email-bcc-address'] ) && ! empty( $setting['admin-email-bcc-address'] ) && is_array( $setting['admin-email-bcc-address'] ) ) {
						$setting_bcc_addresses = $setting['admin-email-bcc-address'];

						foreach ( $setting_bcc_addresses as $key => $setting_bcc_address ) {
							$setting_bcc_address = forminator_replace_form_data( $setting_bcc_address, $data );
							$setting_bcc_address = forminator_replace_variables( $setting_bcc_address, $custom_form->id, $data['current_url'] );
							$setting_bcc_address = forminator_replace_custom_form_data( $setting_bcc_address, $custom_form, $data, $entry, $this->skip_custom_form_data['admin'] );
							if ( is_email( $setting_bcc_address ) ) {
								$bcc_addresses[] = $setting_bcc_address;
							}
						}
					}
					/**
					 * Filter `BCC` email addresses of mail that send to admin
					 *
					 * @since 1.5
					 *
					 * @param array                        $bcc_addresses
					 * @param Forminator_Custom_Form_Model $custom_form Current Form Model
					 * @param array                        $data        POST data
					 * @param Forminator_Form_Entry_Model  $entry       entry model
					 * @param Forminator_CForm_Front_Mail  $this        mail class
					 */
					$bcc_addresses = apply_filters( 'forminator_custom_form_mail_admin_bcc_addresses', $bcc_addresses, $custom_form, $data, $entry, $this );

					$content_type = $this->content_type;
					/**
					 * Filter `Content-Type` of mail that send to admin
					 *
					 * @since 1.5
					 *
					 * @param string                       $content_type
					 * @param Forminator_Custom_Form_Model $custom_form Current Form Model
					 * @param array                        $data        POST data
					 * @param Forminator_Form_Entry_Model  $entry       entry model
					 * @param Forminator_CForm_Front_Mail  $this        mail class
					 */
					$content_type = apply_filters( 'forminator_custom_form_mail_admin_content_type', $content_type, $custom_form, $data, $entry, $this );

					$headers = array();

					// only change From header if these two are valid
					if ( ! empty( $from_name ) && ! empty( $from_email ) ) {
						$headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';
					}

					if ( ! empty( $reply_to_address ) ) {
						$headers[] = 'Reply-To: ' . $reply_to_address;
					}

					if ( ! empty( $cc_addresses ) && is_array( $cc_addresses ) ) {
						$headers[] = 'Cc: ' . implode( ', ', $cc_addresses );
					}

					if ( ! empty( $bcc_addresses ) && is_array( $bcc_addresses ) ) {
						$headers[] = 'BCc: ' . implode( ', ', $bcc_addresses );
					}

					if ( ! empty( $content_type ) ) {
						$headers[] = 'Content-Type: ' . $content_type;
					}

					/**
					 * Filter headers of mail that send to admin
					 *
					 * @since 1.5
					 *
					 * @param array                        $headers
					 * @param Forminator_Custom_Form_Model $custom_form Current Form Model
					 * @param array                        $data        POST data
					 * @param Forminator_Form_Entry_Model  $entry       entry model
					 * @param Forminator_CForm_Front_Mail  $this        mail class
					 */
					$headers = apply_filters( 'forminator_custom_form_mail_admin_headers', $headers, $custom_form, $data, $entry, $this );

					$this->set_headers( $headers );

					$this->set_subject( $subject );
					$this->set_recipients( $recipients );
					$this->set_message_with_vars( $this->message_vars, $message );
					$this->send_multiple();

					/**
					 * Action called after admin mail sent
					 *
					 * @param Forminator_CForm_Front_Mail - the current form
					 * @param Forminator_Custom_Form_Model - the current form
					 * @param array                       $data       - current data
					 * @param Forminator_Form_Entry_Model $entry      - saved entry @since 1.0.3
					 * @param array                       $recipients - array or recipients
					 */
					do_action( 'forminator_custom_form_mail_admin_sent', $this, $custom_form, $data, $entry, $recipients );
				}
			}

			$user_data_email = $this->get_user_email_recipients( $data, $custom_form, $entry );

			//Process user mail
			if ( $this->send_user_mail( $setting ) && $user_data_email && ! empty( $user_data_email ) ) {
				$subject = forminator_replace_form_data( $setting['user-email-title'], $data, $custom_form, $entry );
				$subject = forminator_replace_variables( $subject, $custom_form->id, $data['current_url'] );
				$subject = forminator_replace_custom_form_data( $subject, $custom_form, $data, $entry, $this->skip_custom_form_data['user'] );

				$message = forminator_replace_form_data( $setting['user-email-editor'], $data, $custom_form, $entry );
				$message = forminator_replace_variables( $message, $custom_form->id, $data['current_url'] );
				$message = forminator_replace_custom_form_data( $message, $custom_form, $data, $entry, $this->skip_custom_form_data['user'] );

				/**
				 * Custom form mail subject filter
				 *
				 * @since 1.0.2
				 *
				 * @param string $subject
				 * @param Forminator_Custom_Form_Model - the current form
				 *
				 * @return string $subject
				 */
				$subject = apply_filters( 'forminator_custom_form_mail_user_subject', $subject, $custom_form, $data, $entry, $this );

				/**
				 * Custom form mail filter
				 *
				 * @since 1.0.2
				 *
				 * @param string $message
				 * @param Forminator_Custom_Form_Model - the current form
				 *
				 * @return string $message
				 */
				$message = apply_filters( 'forminator_custom_form_mail_user_message', $message, $custom_form, $data, $entry, $this );

				$from_name = $this->sender_name;
				if ( isset( $setting['user-email-from-name'] ) && ! empty( $setting['user-email-from-name'] ) ) {
					$setting_from_name = $setting['user-email-from-name'];
					$setting_from_name = forminator_replace_form_data( $setting_from_name, $data );
					$setting_from_name = forminator_replace_variables( $setting_from_name, $custom_form->id, $data['current_url'] );
					$setting_from_name = forminator_replace_custom_form_data( $setting_from_name, $custom_form, $data, $entry, $this->skip_custom_form_data['user'] );

					if ( ! empty( $setting_from_name ) ) {
						$from_name = $setting_from_name;
					}
				}
				/**
				 * Filter `From` name of mail that send to user
				 *
				 * @since 1.5
				 *
				 * @param string                       $from_name
				 * @param Forminator_Custom_Form_Model $custom_form Current Form Model
				 * @param array                        $data        POST data
				 * @param Forminator_Form_Entry_Model  $entry       entry model
				 * @param Forminator_CForm_Front_Mail  $this        mail class
				 */
				$from_name = apply_filters( 'forminator_custom_form_mail_user_from_name', $from_name, $custom_form, $data, $entry, $this );

				$from_email = $this->sender_email;
				if ( isset( $setting['user-email-from-address'] ) && ! empty( $setting['user-email-from-address'] ) ) {
					$setting_from_address = $setting['user-email-from-address'];
					$setting_from_address = forminator_replace_form_data( $setting_from_address, $data );
					$setting_from_address = forminator_replace_variables( $setting_from_address, $custom_form->id, $data['current_url'] );
					$setting_from_address = forminator_replace_custom_form_data( $setting_from_address, $custom_form, $data, $entry, $this->skip_custom_form_data['user'] );

					if ( is_email( $setting_from_address ) ) {
						$from_email = $setting_from_address;
					}
				}
				/**
				 * Filter `From` email address of mail that send to user
				 *
				 * @since 1.5
				 *
				 * @param string                       $from_email
				 * @param Forminator_Custom_Form_Model $custom_form Current Form Model
				 * @param array                        $data        POST data
				 * @param Forminator_Form_Entry_Model  $entry       entry model
				 * @param Forminator_CForm_Front_Mail  $this        mail class
				 */
				$from_email = apply_filters( 'forminator_custom_form_mail_user_from_email', $from_email, $custom_form, $data, $entry, $this );

				$reply_to_address = '';
				if ( isset( $setting['user-email-reply-to-address'] ) && ! empty( $setting['user-email-reply-to-address'] ) ) {
					$setting_reply_to_address = $setting['user-email-reply-to-address'];
					$setting_reply_to_address = forminator_replace_form_data( $setting_reply_to_address, $data );
					$setting_reply_to_address = forminator_replace_variables( $setting_reply_to_address, $custom_form->id, $data['current_url'] );
					$setting_reply_to_address = forminator_replace_custom_form_data( $setting_reply_to_address, $custom_form, $data, $entry, $this->skip_custom_form_data['user'] );

					if ( is_email( $setting_reply_to_address ) ) {
						$reply_to_address = $setting_reply_to_address;
					}
				}

				/**
				 * Filter `Reply To` email address of mail that send to user
				 *
				 * @since 1.5
				 *
				 * @param string                       $reply_to_address
				 * @param Forminator_Custom_Form_Model $custom_form Current Form Model
				 * @param array                        $data        POST data
				 * @param Forminator_Form_Entry_Model  $entry       entry model
				 * @param Forminator_CForm_Front_Mail  $this        mail class
				 */
				$reply_to_address = apply_filters( 'forminator_custom_form_mail_user_reply_to', $reply_to_address, $custom_form, $data, $entry, $this );

				$cc_addresses = array();
				if ( isset( $setting['user-email-cc-address'] ) && ! empty( $setting['user-email-cc-address'] ) && is_array( $setting['user-email-cc-address'] ) ) {
					$setting_cc_addresses = $setting['user-email-cc-address'];

					foreach ( $setting_cc_addresses as $key => $setting_cc_address ) {
						$setting_cc_address = forminator_replace_form_data( $setting_cc_address, $data );
						$setting_cc_address = forminator_replace_variables( $setting_cc_address, $custom_form->id, $data['current_url'] );
						$setting_cc_address = forminator_replace_custom_form_data( $setting_cc_address, $custom_form, $data, $entry, $this->skip_custom_form_data['user'] );
						if ( is_email( $setting_cc_address ) ) {
							$cc_addresses[] = $setting_cc_address;
						}
					}
				}

				/**
				 * Filter `CC` email addresses of mail that send to user
				 *
				 * @since 1.5
				 *
				 * @param array                        $cc_addresses
				 * @param Forminator_Custom_Form_Model $custom_form Current Form Model
				 * @param array                        $data        POST data
				 * @param Forminator_Form_Entry_Model  $entry       entry model
				 * @param Forminator_CForm_Front_Mail  $this        mail class
				 */
				$cc_addresses = apply_filters( 'forminator_custom_form_mail_user_cc_addresses', $cc_addresses, $custom_form, $data, $entry, $this );

				$bcc_addresses = array();
				if ( isset( $setting['user-email-bcc-address'] ) && ! empty( $setting['user-email-bcc-address'] ) && is_array( $setting['user-email-bcc-address'] ) ) {
					$setting_bcc_addresses = $setting['user-email-bcc-address'];

					foreach ( $setting_bcc_addresses as $key => $setting_bcc_address ) {
						$setting_bcc_address = forminator_replace_form_data( $setting_bcc_address, $data );
						$setting_bcc_address = forminator_replace_variables( $setting_bcc_address, $custom_form->id, $data['current_url'] );
						$setting_bcc_address = forminator_replace_custom_form_data( $setting_bcc_address, $custom_form, $data, $entry, $this->skip_custom_form_data['user'] );
						if ( is_email( $setting_bcc_address ) ) {
							$bcc_addresses[] = $setting_bcc_address;
						}
					}
				}
				/**
				 * Filter `BCC` email addresses of mail that send to user
				 *
				 * @since 1.5
				 *
				 * @param array                        $bcc_addresses
				 * @param Forminator_Custom_Form_Model $custom_form Current Form Model
				 * @param array                        $data        POST data
				 * @param Forminator_Form_Entry_Model  $entry       entry model
				 * @param Forminator_CForm_Front_Mail  $this        mail class
				 */
				$bcc_addresses = apply_filters( 'forminator_custom_form_mail_user_bcc_addresses', $bcc_addresses, $custom_form, $data, $entry, $this );

				$content_type = $this->content_type;
				/**
				 * Filter `Content-Type` of mail that send to user
				 *
				 * @since 1.5
				 *
				 * @param string                       $content_type
				 * @param Forminator_Custom_Form_Model $custom_form Current Form Model
				 * @param array                        $data        POST data
				 * @param Forminator_Form_Entry_Model  $entry       entry model
				 * @param Forminator_CForm_Front_Mail  $this        mail class
				 */
				$content_type = apply_filters( 'forminator_custom_form_mail_user_content_type', $content_type, $custom_form, $data, $entry, $this );

				$headers = array();

				// only change From header if these two are valid
				if ( ! empty( $from_name ) && ! empty( $from_email ) ) {
					$headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';
				}

				if ( ! empty( $reply_to_address ) ) {
					$headers[] = 'Reply-To: ' . $reply_to_address;
				}

				if ( ! empty( $cc_addresses ) && is_array( $cc_addresses ) ) {
					$headers[] = 'Cc: ' . implode( ', ', $cc_addresses );
				}

				if ( ! empty( $bcc_addresses ) && is_array( $bcc_addresses ) ) {
					$headers[] = 'BCc: ' . implode( ', ', $bcc_addresses );
				}

				if ( ! empty( $content_type ) ) {
					$headers[] = 'Content-Type: ' . $content_type;
				}

				/**
				 * Filter headers of mail that send to user
				 *
				 * @since 1.5
				 *
				 * @param array                        $headers
				 * @param Forminator_Custom_Form_Model $custom_form Current Form Model
				 * @param array                        $data        POST data
				 * @param Forminator_Form_Entry_Model  $entry       entry model
				 * @param Forminator_CForm_Front_Mail  $this        mail class
				 */
				$headers = apply_filters( 'forminator_custom_form_mail_user_headers', $headers, $custom_form, $data, $entry, $this );

				$this->set_headers( $headers );
				$this->set_subject( $subject );
				$this->set_recipients( $user_data_email );
				$this->set_message_with_vars( $this->message_vars, $message );
				$this->send_multiple();

				/**
				 * Action called after admin mail sent
				 *
				 * @param Forminator_CForm_Front_Mail - the current form
				 * @param Forminator_Custom_Form_Model - the current form
				 * @param array                       $data            - current data
				 * @param Forminator_Form_Entry_Model $entry           - saved entry @since 1.0.3
				 * @param array                       $user_data_email - the recipients email address
				 */
				do_action( 'forminator_custom_form_mail_user_sent', $this, $custom_form, $data, $entry, $user_data_email );
			}
		}


		/**
		 * Action called after mail is sent
		 *
		 * @param Forminator_CForm_Front_Mail - the current form
		 * @param Forminator_Custom_Form_Model - the current form
		 * @param array $data - current data
		 */
		do_action( 'forminator_custom_form_mail_after_send_mail', $this, $custom_form, $data );
	}

	/**
	 * Get user email from data
	 *
	 * @since 1.0.3
	 *
	 * @param                              $data
	 * @param Forminator_Custom_Form_Model $custom_form
	 *
	 * @return bool|string
	 */
	public function get_user_email_data( $data, $custom_form ) {
		// Get form fields
		$fields = $custom_form->get_fields();
		if ( ! is_null( $fields ) ) {
			foreach ( $fields as $field ) {
				$field_array = $field->to_formatted_array();
				$field_type  = $field_array["type"];

				// Check if field is email
				if ( "email" === $field_type ) {
					$field_id = $field_array['element_id'];
					if ( isset( $data[ $field_id ] ) && ! empty( $data[ $field_id ] ) ) {
						return apply_filters(
							'forminator_get_user_email_data',
							$data[ $field_id ],
							$data,
							$custom_form,
							$this
						);
					}
				}
			}
		}

		return false;
	}

	/**
	 * Get user email
	 *
	 * @since 1.0.3
	 *
	 * @param $data
	 * @param $custom_form
	 *
	 * @return bool
	 */
	public function get_user_email( $data, $custom_form ) {
		$email      = false;
		$data_email = $this->get_user_email_data( $data, $custom_form );

		if ( $data_email && ! empty( $data_email ) ) {
			// We have data email, use it
			$email = $data_email;
		} else {
			// Check if user logged in
			if ( is_user_logged_in() ) {
				$email = $this->message_vars['user_email'];
			}
		}

		return apply_filters( 'forminator_get_user_email', $email, $data, $custom_form, $data_email, $this );
	}

	/**
	 * Check if all conditions are met to send admin email
	 *
	 * @since 1.0
	 *
	 * @param array $setting - the form settings
	 *
	 * @return bool
	 */
	public function send_admin_mail( $setting ) {
		if ( isset( $setting['use-admin-email'] ) && ! empty( $setting['use-admin-email'] ) ) {
			if ( filter_var( $setting['use-admin-email'], FILTER_VALIDATE_BOOLEAN ) ) {
				if ( isset( $setting['admin-email-title'] ) && isset( $setting['admin-email-editor'] ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Set Sender Email
	 *
	 * @since 1.1
	 *
	 * @param $email - email address
	 *
	 * @return bool
	 */
	public function set_sender_email( $email ) {

		$this->sender_email = $email;

		return true;
	}

	/**
	 * Set Sender Name
	 *
	 * @since 1.1
	 *
	 * @param $name - sender name
	 *
	 * @return bool
	 */
	public function set_sender_name( $name ) {

		$this->sender_name = $name;

		return true;
	}

	/**
	 * Get Recipients of admin emails
	 *
	 * @since 1.0.3
	 * @since 1.6.2 add $data,$custom_form model, and entry
	 *
	 * @param       $setting (backward compat argument)
	 * @param array $data
	 * @param array $custom_form
	 * @param array $entry
	 *
	 * @return array
	 */
	public function get_admin_email_recipients( $setting, $data = array(), $custom_form = null, $entry = null ) {

		// since 1.6.2 use settings from custom form model
		if ( $custom_form instanceof Forminator_Custom_Form_Model ) {
			$setting = $custom_form->settings;
		}

		$email = array();
		// backward compatibility for version < 1.0.3
		// when `admin-email-recipients` not exist use admin email
		if ( ! isset( $setting['admin-email-recipients'] ) ) {
			$email = array( get_option( 'admin_email' ) );
		}

		if ( isset( $setting['admin-email-recipients'] ) && ! empty( $setting['admin-email-recipients'] ) ) {
			if ( is_array( $setting['admin-email-recipients'] ) ) {
				$email = $setting['admin-email-recipients'];
			}
		}

		$email = apply_filters_deprecated( 'forminator_get_admin_email_recipents', array( $email, $setting, $custom_form, $entry ), '1.6', 'forminator_get_admin_email_recipients' );

		return apply_filters( 'forminator_get_admin_email_recipients', $email, $setting, $data, $custom_form, $entry );
	}

	/**
	 * Check if all conditions are met to send user email
	 *
	 * @since 1.0
	 *
	 * @param array $setting - the form settings
	 *
	 * @return bool
	 */
	public function send_user_mail( $setting ) {
		if ( isset( $setting['use-user-email'] ) && ! empty( $setting['use-user-email'] ) ) {
			if ( filter_var( $setting['use-user-email'], FILTER_VALIDATE_BOOLEAN ) ) {
				if ( isset( $setting['user-email-title'] ) && isset( $setting['user-email-editor'] ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Get Recipients of user emails
	 *
	 * @since 1.6
	 *
	 * @param array                        $data submitted data
	 * @param Forminator_Custom_Form_Model $custom_form
	 * @param Forminator_Form_Entry_Model  $entry
	 *
	 * @return array
	 */
	public function get_user_email_recipients( $data, $custom_form, $entry ) {
		$email   = array();
		$setting = $custom_form->settings;
		if ( ! isset( $setting['user-email-recipients'] ) || ! is_array( $setting['user-email-recipients'] ) || empty( $setting['user-email-recipients'] ) ) {
			$default_email = $this->get_user_email( $data, $custom_form );
			$email         = array( $default_email );

		} else {

			$setting_recipients = $setting['user-email-recipients'];

			foreach ( $setting_recipients as $key => $setting_recipient ) {
				$setting_recipient = forminator_replace_form_data( $setting_recipient, $data );
				$setting_recipient = forminator_replace_variables( $setting_recipient, $custom_form->id, $data['current_url'] );
				$setting_recipient = forminator_replace_custom_form_data( $setting_recipient, $custom_form, $data, $entry, $this->skip_custom_form_data['user'] );
				if ( is_email( $setting_recipient ) ) {
					$email[] = $setting_recipient;
				}
			}

		}

		$email = apply_filters_deprecated( 'forminator_get_admin_email_recipients', array( $email, $setting, $custom_form, $entry ), '1.6.2', 'forminator_get_user_email_recipients' );

		return apply_filters( 'forminator_get_user_email_recipients', $email, $setting, $data, $custom_form, $entry );
	}
}

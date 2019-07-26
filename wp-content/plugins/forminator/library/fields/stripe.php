<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Forminator_Stripe
 *
 * @since 1.7
 */
class Forminator_Stripe extends Forminator_Field {

	/**
	 * @var string
	 */
	public $name = '';

	/**
	 * @var string
	 */
	public $slug = 'stripe';

	/**
	 * @var string
	 */
	public $type = 'stripe';

	/**
	 * @var int
	 */
	public $position = 23;

	/**
	 * @var array
	 */
	public $options = array();

	/**
	 * @var string
	 */
	public $category = 'standard';


	/**
	 * @var string
	 */
	public $icon = 'sui-icon-stripe';

	public $is_connected = false;

	/**
	 * Forminator_Stripe constructor.
	 *
	 */
	public function __construct() {
		parent::__construct();

		$this->name = __( 'Stripe', Forminator::DOMAIN );

		try {
			$stripe = new Forminator_Gateway_Stripe();
			if ( $stripe->is_test_ready() && $stripe->is_live_ready() ) {
				$this->is_connected = true;
			}
		} catch ( Forminator_Gateway_Exception $e ) {
			$this->is_connected = false;
		}

	}

	/**
	 * Field defaults
	 *
	 * @return array
	 */
	public function defaults() {

		$default_currency = 'USD';
		try {
			$stripe           = new Forminator_Gateway_Stripe();
			$default_currency = $stripe->get_default_currency();
		} catch ( Forminator_Gateway_Exception $e ) {
			forminator_maybe_log( __METHOD__, $e->getMessage() );
		}

		return array(
			'mode'                => 'test',
			'currency'            => $default_currency,
			'amount_type'         => 'fixed',
			'logo'                => '',
			'company_name'        => '',
			'product_description' => '',
			'customer_email'      => '',
			'checkout_label'      => __( 'Pay {{amount}}', Forminator::DOMAIN ),
			'remember_me'         => 'true',
			'collect_address'     => 'none', // billing, //billing_shipping, //''
			'receipt'             => 'false',
			'verify_zip'          => 'false',
			'language'            => 'en',
			'options'             => array(),
		);
	}

	/**
	 * Field front-end markup
	 *
	 *
	 * @param $field
	 * @param $settings
	 *
	 * @return mixed
	 */
	public function markup( $field, $settings = array() ) {

		$this->field         = $field;
		$this->form_settings = $settings;

		$id                  = self::get_property( 'element_id', $field );
		$element_name        = $id;
		$field_id            = $id . '-field';
		$mode                = self::get_property( 'mode', $field, 'test' );
		$currency            = self::get_property( 'currency', $field, $this->get_default_currency() );
		$amount_type         = self::get_property( 'amount_type', $field, 'fixed' );
		$amount              = self::get_property( 'amount', $field, '0' );
		$amount_variable     = self::get_property( 'variable', $field, '' );
		$logo                = self::get_property( 'logo', $field, '' );
		$company_name        = self::get_property( 'company_name', $field, '' );
		$product_description = self::get_property( 'product_description', $field, '' );
		$customer_email      = self::get_property( 'customer_email', $field, '' );
		$checkout_label      = self::get_property( 'checkout_label', $field, '' );
		$remember_me         = self::get_property( 'remember_me', $field, true, 'bool' );
		$collect_address     = self::get_property( 'collect_address', $field, 'none', 'string' );
		$verify_zip          = self::get_property( 'verify_zip', $field, false, 'bool' );
		$language            = self::get_property( 'language', $field, 'en' );


		$attr = array(
			'type'                   => 'hidden',
			'name'                   => $element_name,
			'id'                     => $field_id,
			'data-is-payment'        => 'true',
			'data-payment-type'      => $this->type,
			'data-key'               => esc_html( $this->get_publishable_key( 'test' !== $mode ) ),
			'data-currency'          => esc_html( strtolower( $currency ) ),
			'data-amount-type'       => esc_html( $amount_type ),
			'data-amount'            => ( 'fixed' === $amount_type ? esc_html( $amount ) : $amount_variable ),
			'data-label'             => esc_html( $checkout_label ),
			'data-allow-remember-me' => esc_html( $remember_me ? 'true' : 'false' ),
			'data-locale'            => esc_html( $language ),
		);

		if ( ! empty( $logo ) ) {
			$attr['data-image'] = esc_url( $logo );
		}

		if ( ! empty( $company_name ) ) {
			$attr['data-name'] = esc_html( $company_name );
		}

		if ( ! empty( $company_name ) ) {
			$attr['data-description'] = esc_html( $product_description );
		}

		if ( ! empty( $customer_email ) ) {
			$attr['data-email'] = esc_html( $customer_email );
		}

		if ( 'billing' === $collect_address || 'billing_shipping' === $collect_address ) {
			$attr['data-billing-address'] = 'true';
		}

		if ( 'billing_shipping' === $collect_address ) {
			$attr['data-shipping-address'] = 'true';
		}

		if ( $verify_zip ) {
			$attr['data-zip-code'] = 'true';
		}

		$html = self::create_input( $attr );

		return apply_filters( 'forminator_field_stripe_markup', $html, $attr, $field );
	}


	/**
	 * Field back-end validation
	 *
	 *
	 * @param array        $field
	 * @param array|string $data
	 */
	public function validate( $field, $data ) {
		$id = self::get_property( 'element_id', $field );
	}

	/**
	 * Sanitize data
	 *
	 *
	 * @param array        $field
	 * @param array|string $data - the data to be sanitized
	 *
	 * @return array|string $data - the data after sanitization
	 */
	public function sanitize( $field, $data ) {
		$original_data = $data;
		// Sanitize
		$data = forminator_sanitize_field( $data );

		return apply_filters( 'forminator_field_stripe_sanitize', $data, $field, $original_data );
	}

	/**
	 * @since 1.7
	 * @inheritdoc
	 */
	public function is_available( $field ) {
		$mode = self::get_property( 'mode', $field, 'test' );
		try {
			$stripe = new Forminator_Gateway_Stripe();

			if ( 'test' !== $mode ) {
				$stripe->set_live( true );
			}

			if ( $stripe->is_ready() ) {
				return true;
			}
		} catch ( Forminator_Gateway_Exception $e ) {
			return false;
		}
	}

	/**
	 * Get publishable key
	 *
	 * @since 1.7
	 *
	 * @param bool $live
	 *
	 * @return bool|string
	 */
	private function get_publishable_key( $live = false ) {
		try {
			$stripe = new Forminator_Gateway_Stripe();

			if ( $live ) {
				return $stripe->get_live_key();
			}

			return $stripe->get_test_key();
		} catch ( Forminator_Gateway_Exception $e ) {
			return false;
		}

	}

	/**
	 * Get default currency
	 *
	 * @return string
	 */
	private function get_default_currency() {
		try {
			$stripe = new Forminator_Gateway_Stripe();

			return $stripe->get_default_currency();

		} catch ( Forminator_Gateway_Exception $e ) {
			return 'USD';
		}
	}

	/**
	 * @param array                        $field
	 * @param Forminator_Custom_Form_Model $custom_form
	 * @param array                        $submitted_data
	 * @param array                        $pseudo_submitted_data
	 * @param array                        $field_data_array
	 *
	 * @return array
	 */
	public function process_to_entry_data( $field, $custom_form, $submitted_data, $pseudo_submitted_data, $field_data_array ) {
		$entry_data = array(
			'mode'             => '',
			'status'           => '',
			'amount'           => '',
			'currency'         => '',
			'transaction_id'   => '',
			'transaction_link' => '',
		);

		$element_id = self::get_property( 'element_id', $field );
		$mode       = self::get_property( 'mode', $field, 'test' );
		$currency   = self::get_property( 'currency', $field, $this->get_default_currency() );
		$receipt    = self::get_property( 'receipt', $field, false, 'bool' );
		$metadata   = self::get_property( 'options', $field, array() );

		forminator_maybe_log( __METHOD__, $metadata );

		$entry_data['mode']     = $mode;
		$entry_data['currency'] = $currency;
		try {
			$stripe = new Forminator_Gateway_Stripe();

			if ( 'test' !== $mode ) {
				$stripe->set_live( true );
			}

			if ( empty( $currency ) ) {
				throw new Forminator_Gateway_Exception( __( 'Invalid Stripe currency.', Forminator::DOMAIN ) );
			}

			$currency = strtolower( $currency );
			$token    = isset( $submitted_data[ $element_id ] ) ? $submitted_data[ $element_id ] : '';

			if ( empty( $token ) ) {
				throw new Forminator_Gateway_Exception( __( 'Stripe Token not found on submitted data.', Forminator::DOMAIN ) );
			}

			$charge_amount = $this->get_payment_amount( $field, $custom_form, $submitted_data, $pseudo_submitted_data );

			if ( ! is_numeric( $charge_amount ) ) {
				throw new Forminator_Gateway_Exception( __( 'Stripe charge amount is not numeric.', Forminator::DOMAIN ) );
			}

			if ( $charge_amount <= 0 ) {
				throw new Forminator_Gateway_Exception( __( 'Stripe charge amount must be greater than 0.', Forminator::DOMAIN ) );
			}

			$entry_data['amount'] = $charge_amount;

			// @see https://stripe.com/docs/currencies#zero-decimal
			if ( 'jpy' !== $currency ) {
				$charge_amount = $charge_amount * 100;
			}

			$charge_config = array(
				'currency' => $currency,
				'amount'   => $charge_amount,
				'source'   => $token,
			);

			// create dummy entry
			$dummy_entry          = new Forminator_Form_Entry_Model( null );
			$dummy_entry_metadata = array();
			foreach ( $field_data_array as $item ) {
				if ( isset( $item['name'] ) && isset( $item['value'] ) ) {
					$dummy_entry_metadata[ $item['name'] ] = array(
						'id'    => uniqid( 'dummy_meta_', true ),
						'value' => $item['value'],
					);
				}

			}
			$dummy_entry->meta_data = $dummy_entry_metadata;

			// Receipt processing
			if ( $receipt ) {
				// retrieve info from token
				$token_info = $stripe->retrieve_info_from_token( $token );
				if ( isset( $token_info->email ) && ! empty( $token_info->email ) ) {
					$charge_config['receipt_email'] = $token_info->email;
				}
			}

			// metadata processing
			$charge_metadata = array();
			if ( is_array( $metadata ) && ! empty( $metadata ) ) {
				foreach ( $metadata as $key => $metadatum ) {
					if ( isset( $metadatum['label'] ) && isset( $metadatum['value'] ) ) {
						if ( ! empty( $metadatum['label'] ) && ! empty( $metadatum['value'] ) ) {
							$field_id                               = $metadatum['value'];
							$meta_value                             = forminator_replace_form_data( '{' . $field_id . '}', $submitted_data, $custom_form, $dummy_entry );
							$charge_metadata[ $metadatum['label'] ] = $meta_value;
						}
					}

				}
			}

			if ( ! empty( $charge_metadata ) ) {
				$charge_config['metadata'] = $charge_metadata;
			}


			/**
			 * Filter Stripe charge configuration to be send
			 *
			 * @since 1.7
			 *
			 * @param array                        $charge_config
			 * @param array                        $field            field properties
			 * @param Forminator_Custom_Form_Model $custom_form
			 * @param array                        $submitted_data
			 * @param array                        $field_data_array current entry meta
			 *
			 * @return array
			 */
			$charge_config = apply_filters( 'forminator_field_stripe_process_to_entry_data_charge_config', $charge_config, $field, $custom_form, $submitted_data, $field_data_array );

			$charge_data = $stripe->charge( $charge_config );

			if ( 'succeeded' === $charge_data->status && $charge_data->id ) {
				$entry_data['status']         = 'success';
				$entry_data['transaction_id'] = $charge_data->id;

				$transaction_link = 'https://dashboard.stripe.com/payments/' . rawurlencode( $charge_data->id );
				if ( 'test' === $mode ) {
					$transaction_link = 'https://dashboard.stripe.com/test/payments/' . rawurlencode( $charge_data->id );
				}
				$entry_data['transaction_link'] = $transaction_link;
			} else {
				throw new Forminator_Gateway_Exception( __( 'Failed to charge payment via Stripe', Forminator::DOMAIN ) );
			}

		} catch ( \Forminator\Stripe\Error\Card $e ) {
			$err  = array(
				'message' => $e->getMessage(),
				'type'    => 'card_error',
			);
			$body = $e->getJsonBody();
			forminator_maybe_log( __METHOD__, $e->getMessage(), $body );
			$error_body     = is_array( $body ) ? $body : array();
			$err['message'] = isset( $error_body['message'] ) ? $error_body['message'] : $err['message'];
			$err['type']    = isset( $error_body['type'] ) ? $error_body['type'] : $err['type'];

			$entry_data['status']     = 'fail';
			$entry_data['error']      = $err['message'];
			$entry_data['error_type'] = $err['type'];
		} catch ( \Forminator\Stripe\Error\RateLimit $e ) {
			$err  = array(
				'message' => $e->getMessage(),
				'type'    => 'rate_limit',
			);
			$body = $e->getJsonBody();
			forminator_maybe_log( __METHOD__, $e->getMessage(), $body );
			$error_body     = is_array( $body ) ? $body : array();
			$err['message'] = isset( $error_body['message'] ) ? $error_body['message'] : $err['message'];
			$err['type']    = isset( $error_body['type'] ) ? $error_body['type'] : $err['type'];

			$entry_data['status']     = 'fail';
			$entry_data['error']      = $err['message'];
			$entry_data['error_type'] = $err['type'];
		} catch ( \Forminator\Stripe\Error\InvalidRequest $e ) {
			$err  = array(
				'message' => $e->getMessage(),
				'type'    => 'invalid_request',
			);
			$body = $e->getJsonBody();
			forminator_maybe_log( __METHOD__, $e->getMessage(), $body );
			$error_body     = is_array( $body ) ? $body : array();
			$err['message'] = isset( $error_body['message'] ) ? $error_body['message'] : $err['message'];
			$err['type']    = isset( $error_body['type'] ) ? $error_body['type'] : $err['type'];

			$entry_data['status']     = 'fail';
			$entry_data['error']      = $err['message'];
			$entry_data['error_type'] = $err['type'];
		} catch ( \Forminator\Stripe\Error\Authentication $e ) {
			$err  = array(
				'message' => $e->getMessage(),
				'type'    => 'auth_error',
			);
			$body = $e->getJsonBody();
			forminator_maybe_log( __METHOD__, $e->getMessage(), $body );
			$error_body     = is_array( $body ) ? $body : array();
			$err['message'] = isset( $error_body['message'] ) ? $error_body['message'] : $err['message'];
			$err['type']    = isset( $error_body['type'] ) ? $error_body['type'] : $err['type'];

			$entry_data['status']     = 'fail';
			$entry_data['error']      = $err['message'];
			$entry_data['error_type'] = $err['type'];
		} catch ( \Forminator\Stripe\Error\ApiConnection $e ) {
			$err  = array(
				'message' => $e->getMessage(),
				'type'    => 'connection_error',
			);
			$body = $e->getJsonBody();
			forminator_maybe_log( __METHOD__, $e->getMessage(), $body );
			$error_body     = is_array( $body ) ? $body : array();
			$err['message'] = isset( $error_body['message'] ) ? $error_body['message'] : $err['message'];
			$err['type']    = isset( $error_body['type'] ) ? $error_body['type'] : $err['type'];

			$entry_data['status']     = 'fail';
			$entry_data['error']      = $err['message'];
			$entry_data['error_type'] = $err['type'];
		} catch ( \Forminator\Stripe\Error\Base $e ) {
			$err  = array(
				'message' => $e->getMessage(),
				'type'    => 'stripe_error',
			);
			$body = $e->getJsonBody();
			forminator_maybe_log( __METHOD__, $e->getMessage(), $body );
			$error_body     = is_array( $body ) ? $body : array();
			$err['message'] = isset( $error_body['message'] ) ? $error_body['message'] : $err['message'];
			$err['type']    = isset( $error_body['type'] ) ? $error_body['type'] : $err['type'];

			$entry_data['status']     = 'fail';
			$entry_data['error']      = $err['message'];
			$entry_data['error_type'] = $err['type'];
		} catch ( Forminator_Gateway_Exception $e ) {
			$entry_data['status']     = 'fail';
			$entry_data['error']      = $e->getMessage();
			$entry_data['error_type'] = 'gateway_exception';
			forminator_maybe_log( __METHOD__, $e->getMessage(), 'gateway_exception' );
		} catch ( Exception $e ) {
			$entry_data['status']     = 'fail';
			$entry_data['error']      = $e->getMessage();
			$entry_data['error_type'] = 'general_exception';
			forminator_maybe_log( __METHOD__, $e->getMessage(), 'general_exception' );
		}

		/**
		 * Filter stripe entry data that will be stored
		 *
		 * @since 1.7
		 *
		 * @param array                        $entry_data
		 * @param array                        $field            field properties
		 * @param Forminator_Custom_Form_Model $custom_form
		 * @param array                        $submitted_data
		 * @param array                        $field_data_array current entry meta
		 *
		 * @return array
		 */
		$entry_data = apply_filters( 'forminator_field_stripe_process_to_entry_data', $entry_data, $field, $custom_form, $submitted_data, $field_data_array );

		return $entry_data;
	}

	/**
	 * Make linkify transaction_id
	 *
	 * @param $transaction_id
	 * @param $meta_value
	 *
	 * @return string
	 */
	public static function linkify_transaction_id( $transaction_id, $meta_value ) {
		$transaction_link = $transaction_id;
		if ( isset( $meta_value['transaction_link'] ) && ! empty( $meta_value['transaction_link'] ) ) {
			$url              = $meta_value['transaction_link'];
			$transaction_link = '<a href="' . $url . '" target="_blank" rel="noopener noreferrer" title="' . $transaction_id . '">' . $transaction_id . '</a>';
		}

		/**
		 * Filter link to Stripe transaction id
		 *
		 * @since 1.7
		 *
		 * @param string $transaction_link
		 * @param string $transaction_id
		 * @param array  $meta_value
		 *
		 * @return string
		 */
		$transaction_link = apply_filters( 'forminator_field_stripe_linkify_transaction_id', $transaction_link, $transaction_id, $meta_value );

		return $transaction_link;
	}

	/**
	 * Get payment amount
	 *
	 * @since 1.7
	 *
	 * @param array                        $field
	 * @param Forminator_Custom_Form_Model $custom_form
	 * @param array                        $submitted_data
	 * @param array                        $pseudo_submitted_data
	 *
	 * @return double
	 */
	public function get_payment_amount( $field, $custom_form, $submitted_data, $pseudo_submitted_data ) {
		$payment_amount  = 0.0;
		$amount_type     = self::get_property( 'amount_type', $field, 'fixed' );
		$amount          = self::get_property( 'amount', $field, '0' );
		$amount_variable = self::get_property( 'variable', $field, '' );


		if ( 'fixed' === $amount_type ) {
			$payment_amount = $amount;
		} else {
			$amount_var = $amount_variable;
			$form_field = $custom_form->get_field( $amount_var, false );
			if ( $form_field ) {
				$form_field        = $form_field->to_formatted_array();
				$fields_collection = forminator_fields_to_array();
				if ( isset( $form_field['type'] ) ) {
					if ( 'calculation' === $form_field['type'] ) {

						// Calculation field get the amount from pseudo_submit_data
						if ( isset( $pseudo_submitted_data[ $amount_var ] ) ) {
							$payment_amount = $pseudo_submitted_data[ $amount_var ];
						}

					} else {
						if ( isset( $fields_collection[ $form_field['type'] ] ) ) {
							/** @var Forminator_Field $field_object */
							$field_object   = $fields_collection[ $form_field['type'] ];

							$field_id             = $form_field['element_id'];
							$submitted_field_data = isset( $submitted_data[ $field_id ] ) ? $submitted_data[ $field_id ] : null;
							$payment_amount       = $field_object->get_calculable_value( $submitted_field_data, $form_field );
						}
					}

				}
			}

		}

		if ( ! is_numeric( $payment_amount ) ) {
			$payment_amount = 0.0;
		}

		/**
		 * Filter payment amount of stripe
		 *
		 * @since 1.7
		 *
		 * @param double                       $payment_amount
		 * @param array                        $field field settings
		 * @param Forminator_Custom_Form_Model $custom_form
		 * @param array                        $submitted_data
		 * @param array                        $pseudo_submitted_data
		 */
		$payment_amount = apply_filters( 'forminator_field_stripe_payment_amount', $payment_amount, $field, $custom_form, $submitted_data, $pseudo_submitted_data );

		return $payment_amount;
	}
}

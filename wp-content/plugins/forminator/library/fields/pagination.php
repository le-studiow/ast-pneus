<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Forminator_Pagination
 *
 * @since 1.0
 */
class Forminator_Pagination extends Forminator_Field {

	/**
	 * @var string
	 */
	public $name = '';

	/**
	 * @var string
	 */
	public $slug = 'pagination';

	/**
	 * @var string
	 */
	public $type = 'pagination';

	/**
	 * @var int
	 */
	public $position = 18;

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
	public $hide_advanced = "true";

	/**
	 * @var string
	 */
	public $icon = 'sui-icon-arrow-skip-end';

	/**
	 * Forminator_Pagination constructor.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		parent::__construct();

		$this->name = __( 'Pagination', Forminator::DOMAIN );

	}

	/**
	 * Field defaults
	 *
	 * @since 1.0
	 * @return array
	 */
	public function defaults() {
		return apply_filters( 'forminator_pagination_btn_label', array(
			'btn_left'	=> __( '« Previous Step', Forminator::DOMAIN ),
			'btn_right'	=> __( 'Next Step »', Forminator::DOMAIN ),
		) );
	}

	/**
	 * Autofill Setting
	 *
	 * @since 1.0.5
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	public function autofill_settings( $settings = array() ) {
		//Unsupported Autofill
		$autofill_settings = array();

		return $autofill_settings;
	}
}

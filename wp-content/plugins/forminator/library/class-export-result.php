<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Forminator_Export_Result
 *
 * Export result data struct
 *
 * @since 1.5.4
 */
class Forminator_Export_Result {

	/**
	 * @var array
	 */
	public $data = array();

	/**
	 * @var int
	 */
	public $entries_count = 0;

	/**
	 * @var int
	 */
	public $new_entries_count = 0;

	/**
	 * @var Forminator_Base_Form_Model | null
	 */
	public $model = null;

	/**
	 * @var int
	 */
	public $latest_entry_id = 0;

	/**
	 * @var string
	 */
	public $file_path = '';

	/**
	 * @var string
	 */
	public $form_type = '';

	public function __construct() {
	}
}

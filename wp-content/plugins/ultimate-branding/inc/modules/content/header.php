<?php
/**
 * Branda Content Header class.
 *
 * @package Branda
 * @subpackage Front-end
 */
if ( ! class_exists( 'Branda_Content_Header' ) ) {

	/**
	 * Class Branda_Content_Header.
	 */
	class Branda_Content_Header extends Branda_Helper {

		/**
		 * Setting option name.
		 *
		 * @var string
		 */
		protected $option_name = 'ub_content_header';

		/**
		 * Constructor for Branda_Content_Header.
		 *
		 * Register all hooks for the module.
		 */
		public function __construct() {
			parent::__construct();
			$this->set_options();
			add_filter( 'ultimatebranding_settings_content_header', array( $this, 'admin_options_page' ) );
			add_filter( 'ultimatebranding_settings_content_header_process', array( $this, 'update' ), 10, 1 );
			add_action( 'wp_footer', array( $this, 'output' ) );
			add_action( 'init', array( $this, 'upgrade_options' ) );
			add_filter( 'ultimate_branding_options_footer', array( $this, 'add_message' ), 10, 3 );
		}

		/**
		 * Upgrade option
		 *
		 * @since 3.0.0
		 */
		public function upgrade_options() {
			$value = ub_get_option( 'global_header_content' );
			if ( ! empty( $value ) ) {
				/**
				 * Single site
				 */
				$data = array(
					'header' => array(
						'content' => $value,
					),
				);
				/**
				 * For MultiSite
				 */
				if ( $this->is_network ) {
					$data = array(
						'main' => array(
							'content' => $value,
						),
						'subsites_option' => array(
							'different' => 'off',
						),
					);
				}
				$this->update_value( $data );
				ub_delete_option( 'global_header_content' );
			}
		}

		/**
		 * Get options in section.
		 *
		 * @param string $prefix Prefix.
		 *
		 * @return array
		 */
		private function get_section_options( $prefix ) {
			$classes = array();
			if ( 'subsites' === $prefix ) {
				$value = $this->get_value( 'subsites_option', 'different', 'off' );
				if ( 'off' === $value ) {
					$classes[] = 'branda-not-affected';
				}
			}
			$options = array(
				'content' => array(
					'type' => 'wp_editor',
					'placeholder' => esc_html__( 'Enter your page header content here…', 'ub' ),
					'accordion' => array(
						'begin' => true,
						'end' => true,
						'title' => __( 'Content', 'ub' ),
						'item' => array(
							'classes' => $classes,
						),
					),
				),
				'height' => array(
					'label' => __( 'Height', 'ub' ),
					'after_label' => __( 'px', 'ub' ),
					'type' => 'number',
					'min' => 0,
					'default' => 50,
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Design', 'ub' ),
						'item' => array(
							'classes' => $classes,
						),
					),
					'master' => $this->get_name( $prefix . '-height' ),
					'master-value' => 'custom',
					'display' => 'sui-tab-content',
				),
				'height_status' => array(
					'label' => __( 'Height', 'ub' ),
					'description' => __( 'Let your content define the height or set a fixed custom height for header content.', 'ub' ),
					'type' => 'sui-tab',
					'options' => array(
						'auto' => __( 'Auto', 'ub' ),
						'custom' => __( 'Custom', 'ub' ),
					),
					'default' => 'auto',
					'slave-class' => $this->get_name( $prefix . '-height' ),
				),
				'color' => array(
					'label' => __( 'Text', 'ub' ),
					'type' => 'color',
					'master' => $this->get_name( $prefix . '-color' ),
					'master-value' => 'custom',
					'display' => 'sui-tab-content',
					'default' => '#000',
				),
				'background' => array(
					'label' => __( 'Background', 'ub' ),
					'type' => 'color',
					'master' => $this->get_name( $prefix . '-color' ),
					'master-value' => 'custom',
					'display' => 'sui-tab-content',
					'default' => '#fff',
				),
				'color_status' => array(
					'label' => __( 'Colors', 'ub' ),
					'description' => __( 'You can use the default color scheme or customize it to match your theme.', 'ub' ),
					'type' => 'sui-tab',
					'options' => array(
						'default' => __( 'Default', 'ub' ),
						'custom' => __( 'Custom Colors', 'ub' ),
					),
					'default' => 'default',
					'slave-class' => $this->get_name( $prefix . '-color' ),
					'accordion' => array(
						'end' => true,
					),
				),
				'theme_header' => array(
					'type' => 'sui-tab',
					'label' => __( 'Integrate into theme header', 'ub' ),
					'description' => __( 'Enable this to place the header content block inside the theme header element.', 'ub' ),
					'options' => array(
						'off' => __( 'Disable', 'ub' ),
						'on' => __( 'Enable', 'ub' ),
					),
					'default' => 'on',
					'accordion' => array(
						'begin' => true,
						'title' => __( 'Settings', 'ub' ),
						'item' => array(
							'classes' => $classes,
						),
						'classes' => array(
							'body' => array(
								'branda-content-settings',
							),
						),
					),
				),
				'shortcodes' => array(
					'type' => 'sui-tab',
					'label' => __( 'Parse shortcodes', 'ub' ),
					'description' => __( 'Be careful, parsing shortcodes can break the theme UI.', 'ub' ),
					'options' => array(
						'off' => __( 'Disable', 'ub' ),
						'on' => __( 'Enable', 'ub' ),
					),
					'default' => 'off',
					'accordion' => array(
						'end' => true,
					),
				),
			);
			return $options;
		}

		/**
		 * Set options.
		 *
		 * @since 2.1.0
		 */
		protected function set_options() {
			$this->module = 'content-header';
			$options = array(
				'content' => array(
					'title' => __( 'Content', 'ub' ),
					'description' => __( 'Insert any content that you like into the header of every page of your website.', 'ub' ),
					'fields' => array(
						'content' => array(
							'type' => 'wp_editor',
							'label' => __( 'Content', 'ub' ),
							'placeholder' => esc_html__( 'Enter your page header content here…', 'ub' ),
						),
					),
				),
				'design' => array(
					'title' => __( 'Design', 'ub' ),
					'description' => __( 'Customize the design of header content as per your liking.', 'ub' ),
					'fields' => array(
						'height' => array(
							'label' => __( 'Height', 'ub' ),
							'after_label' => __( 'px', 'ub' ),
							'type' => 'number',
							'min' => 0,
							'default' => 50,
							'master' => $this->get_name( 'height' ),
							'master-value' => 'custom',
							'display' => 'sui-tab-content',
						),
						'height_status' => array(
							'label' => __( 'Height', 'ub' ),
							'description' => __( 'Let your content define the height or set a fixed custom height for header content.', 'ub' ),
							'type' => 'sui-tab',
							'options' => array(
								'auto' => __( 'Auto', 'ub' ),
								'custom' => __( 'Custom', 'ub' ),
							),
							'default' => 'auto',
							'slave-class' => $this->get_name( 'height' ),
						),
						'color' => array(
							'label' => __( 'Text', 'ub' ),
							'type' => 'color',
							'master' => $this->get_name( 'color' ),
							'master-value' => 'custom',
							'display' => 'sui-tab-content',
							'default' => '#000',
						),
						'background' => array(
							'label' => __( 'Background', 'ub' ),
							'type' => 'color',
							'master' => $this->get_name( 'color' ),
							'master-value' => 'custom',
							'display' => 'sui-tab-content',
							'default' => '#fff',
						),
						'color_status' => array(
							'label' => __( 'Colors', 'ub' ),
							'description' => __( 'You can use the default color scheme or customize it to match your theme.', 'ub' ),
							'type' => 'sui-tab',
							'options' => array(
								'default' => __( 'Default', 'ub' ),
								'custom' => __( 'Custom Colors', 'ub' ),
							),
							'default' => 'default',
							'slave-class' => $this->get_name( 'color' ),
						),
					),
				),
				'settings' => array(
					'title' => __( 'Settings', 'ub' ),
					'description' => __( 'These provide additional configuration options for the header content.', 'ub' ),
					'fields' => array(
						'theme_header' => array(
							'type' => 'sui-tab',
							'label' => __( 'Integrate into theme header', 'ub' ),
							'description' => __( 'Enable this to place the header content block inside the theme header element.', 'ub' ),
							'options' => array(
								'off' => __( 'Disable', 'ub' ),
								'on' => __( 'Enable', 'ub' ),
							),
							'default' => 'on',
						),
						'shortcodes' => array(
							'type' => 'sui-tab',
							'label' => __( 'Parse shortcodes', 'ub' ),
							'description' => __( 'Be careful, parsing shortcodes can break the theme UI.', 'ub' ),
							'options' => array(
								'off' => __( 'Disable', 'ub' ),
								'on' => __( 'Enable', 'ub' ),
							),
							'default' => 'off',
						),
					),
				),
			);
			/**
			 * For MultiSite
			 */
			if ( $this->is_network ) {
				$options = array(
					'main' => array(
						'title' => __( 'Main Site', 'ub' ),
						'description' => __( 'Insert any content that you like into the header of the main site of your network.', 'ub' ),
						'show-as' => 'accordion',
						'show-reset' => true,
						'fields' => $this->get_section_options( 'main' ),
					),
					// Sub section one.
					'subsites_option' => array(
						'network-only' => true,
						'title' => __( 'Subsites', 'ub' ),
						'description' => __( 'Insert any content that you like into the header of all of the subsites on your network.', 'ub' ),
						'fields' => array(
							'different' => array(
								'type' => 'sui-tab',
								'options' => array(
									'off' => __( 'Same as Main Site', 'ub' ),
									'on' => __( 'Insert Different Content', 'ub' ),
								),
								'default' => 'on',
								'classes' => array(
									'ub-header-subsites-toggle',
								),
							),
						),
						'sub_section' => 'start',
					),
					// Sub section two.
					'subsites' => array(
						'show-as' => 'accordion',
						'show-reset' => true,
						'fields' => $this->get_section_options( 'subsites' ),
						'sub_section' => 'end',
						'accordion' => array(
							'classes' => array( 'ub-header-subsites' ),
						),
					),
				);
			} else {
				unset( $options['content']['fields']['content']['label'] );
			}
			$this->options = $options;
		}

		/**
		 * Output common helper.
		 *
		 * @param string/boolean $key Key, false for single site
		 *
		 * @access private
		 * @since  2.1.0
		 */
		private function output_content( $key ) {
			$content = '';
			$is_single = false === $key;
			/**
			 * get settings
			 */
			if ( $is_single ) {
				$key = 'settings';
			}
			$value = $this->get_value( $key, 'shortcodes', 'off' );
			$parse_shortcodes = 'on' === $value;
			$value = $this->get_value( $key, 'theme_header', 'off' );
			$use_theme_header = 'on' === $value;
			/**
			 * get content
			 */
			if ( $is_single ) {
				$key = 'content';
			}
			// Try content meta.
			if ( $parse_shortcodes ) {
				$content = $this->get_value( $key, 'content_meta', '' );
			}
			// Try content.
			$value = $this->get_value( $key, 'content', false );
			if ( empty( $content ) && ! empty( $value ) ) {
				$content = $value;
				if ( ! empty( $content ) ) {
					$filters = array( 'wptexturize', 'convert_smilies', 'convert_chars', 'wpautop' );
					foreach ( $filters as $filter ) {
						$content = apply_filters( $filter, $content );
					}
				}
			}
			// At least - check.
			if ( empty( $content ) ) {
				return array();
			}
			/**
			 * Parese shortcodes if it is needed.
			 */
			if ( $parse_shortcodes ) {
				$content = do_shortcode( $content );
			}
			/**
			 * get design
			 */
			if ( $is_single ) {
				$key = 'design';
			}
			// Style: color & background
			$style = '';
			$value = $this->get_value( $key, 'color_status', false );
			if ( 'custom' === $value ) {
				$value = $this->get_value( $key, 'color', false );
				if ( ! empty( $value ) ) {
					$style .= $this->css_color( $value );
				}
				$value = $this->get_value( $key, 'background', false );
				if ( ! empty( $value ) ) {
					$style .= $this->css_background_color( $value );
				}
			}
			// Style: custom height
			$value = $this->get_value( $key, 'height_status', '' );
			if ( 'custom' === $value ) {
				$value = $this->get_value( $key, 'height', false );
				if ( ! empty( $value ) ) {
					$style .= $this->css_height( $value );
					$style .= 'overflow: hidden;';
				}
			}
			/**
			 * return
			 */
			return array(
				'style' => preg_replace( '/[\r\n]/', '', $style ),
				'use_theme_header' => $use_theme_header,
				'content' => $content,
			);
		}

		/**
		 * for network
		 *
		 * @since 3.0.0
		 */
		private function output_content_for_network() {
			// Get sub sites flag.
			$subsites_option = $this->get_value( 'subsites_option' );
			// Main site header.
			if ( is_main_site() || ( isset( $subsites_option['different'] ) && 'off' === $subsites_option['different'] ) ) {
				return  $this->output_content( 'main' );
			}
			return $this->output_content( 'subsites' );
		}

		/**
		 * for single site
		 *
		 * @since 3.0.0
		 */
		private function output_content_for_single() {
			return $this->output_content( false );
		}

		/**
		 * Add message abut disabled header for subsites.
		 *
		 * @since 3.0.0
		 */
		public function add_message( $content, $module, $section ) {
			if ( $module !== $this->module ) {
				return $content;
			}
			if ( 'subsites' !== $section ) {
				return $content;
			}
			$classes = array(
				'error',
			);
			$value = $this->get_value( 'subsites_option', 'different', 'off' );
			if ( 'on' === $value ) {
				$classes[] = 'hidden';
			}
			$content .= $this->uba->get_inline_sui_notice( __( 'Subsites configuration has been disabled.', 'ub' ), $classes );
			return $content;
		}

		/**
		 * Output the custom content.
		 *
		 * @since 2.0.0
		 */
		public function output() {
			$data = array();
			if ( $this->is_network ) {
				$data = $this->output_content_for_network();
			} else {
				$data = $this->output_content_for_single();
			}
			if ( ! isset( $data['content'] ) || empty( $data['content'] ) ) {
				return;
			}
			/**
			 * JavaScript template
			 */
			$template = sprintf( '/front-end/modules/%s/js-output', $this->module );
			$data['id'] = $this->get_name( 'js' );
			$data['tag'] = $data['use_theme_header']? 'header':'body';
			$this->render( $template, $data );
		}
	}
}
new Branda_Content_Header;

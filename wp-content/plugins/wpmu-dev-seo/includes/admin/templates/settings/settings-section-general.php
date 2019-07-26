<?php
/**
 * Settings general section template
 *
 * @package wpmu-dev-seo
 */

$sitemap_option_name = empty( $sitemap_option_name ) ? '' : $sitemap_option_name;
$verification_pages = empty( $verification_pages ) ? array() : $verification_pages;
$smartcrawl_options = Smartcrawl_Settings::get_options();
$sitemap_options = Smartcrawl_Settings::get_component_options( Smartcrawl_Settings::COMP_SITEMAP );
$plugin_modules = empty( $plugin_modules ) ? array() : $plugin_modules;
$option_name = empty( $_view['option_name'] ) ? '' : $_view['option_name'];
?>

<?php
$this->_render( 'toggle-group', array(
	'label'       => __( 'Plugin Modules', 'wds' ),
	'description' => __( 'Choose which modules you would like to activate.', 'wds' ),
	'items'       => $plugin_modules,
) );
?>

<?php if ( is_multisite() && is_network_admin() ) : ?>
	<input type="hidden" name="<?php echo esc_attr( $_view['option_name'] ); ?>[save_blog_tabs]" value="1"/>
	<div class="sui-box-settings-row">
		<div class="sui-box-settings-col-1">
			<label class="sui-settings-label"><?php esc_html_e( 'Site Owner Permissions', 'wds' ); ?></label>
			<p class="sui-description">
				<?php esc_html_e( 'Use this section to choose what sections of this plugin will be accessible to Site Admins on your Network.', 'wds' ); ?>
			</p>
		</div>

		<div class="sui-box-settings-col-2">
			<?php $this->_render( 'toggle-item', array(
				'item_value' => 'yes',
				'field_name' => sprintf( '%s[wds_sitewide_mode]', $option_name ),
				'field_id'   => sprintf( '%s[wds_sitewide_mode]', $option_name ),
				'item_label' => esc_html__( 'Sitewide mode (network level changes only)', 'wds' ),
				'checked'    => isset( $wds_sitewide_mode ) ? checked( $wds_sitewide_mode, true, false ) : '',
			) );

			foreach ( $slugs as $item => $label ) {
				$checked = ( ! empty( $blog_tabs[ $item ] ) ) ? 'checked' : '';
				$presence_slug = preg_replace( '/^wds_/', '', $item );
				?>

				<div data-prereq="<?php echo esc_attr( $presence_slug ); ?>">
					<?php $this->_render( 'toggle-item', array(
						'item_value' => 'yes',
						'field_name' => sprintf( '%s[wds_blog_tabs][%s]', $option_name, $item ),
						'field_id'   => sprintf( '%s[wds_blog_tabs][%s]', $option_name, $item ),
						'item_label' => $label,
						'checked'    => $checked,
					) ); ?>
				</div>
				<?php
			}
			?>
		</div>
	</div>
<?php endif; ?>

<?php
$this->_render( 'toggle-group', array(
	'label'       => esc_html__( 'Builtin modules', 'wds' ),
	'description' => esc_html__( 'Choose which modules you would like to activate.', 'wds' ),
	'separator'   => true,
	'items'       => array(
		'analysis-seo'         => array(
			'label'       => esc_html__( 'Page Analysis', 'wds' ),
			'description' => esc_html__( 'Analyses your content against recommend SEO practice and gives recommendations for improvement to make sure content is as optimized as possible.', 'wds' ),
		),
		'analysis-readability' => array(
			'label'       => esc_html__( 'Readability analysis', 'wds' ),
			'description' => esc_html__( 'Benchmarks the readability of your content for the average visitor and gives recommendations for improvement.', 'wds' ),
		),
		'extras-admin_bar'     => array(
			'label'       => esc_html__( 'Admin Bar', 'wds' ),
			'description' => esc_html__( 'Adds a shortcut to SmartCrawl in the WP Admin bar at the top of your screen.', 'wds' ),
		),
	),
) );
?>

<?php
$this->_render( 'toggle-group', array(
	'label'       => __( 'Meta Tags', 'wds' ),
	'description' => __( 'Choose what SmartCrawl modules you want available to use.', 'wds' ),
	'separator'   => true,
	'items'       => array(
		'general-suppress-generator'           => array(
			'label'       => __( 'Hide generator meta tag', 'wds' ),
			'description' => __( 'It can be considered a security risk to have your WordPress version visible to the public, so we recommend you hide it.', 'wds' ),
		),
		'general-suppress-redundant_canonical' => array(
			'label'       => __( 'Hide redundant canonical link tags', 'wds' ),
			'description' => __( 'WordPress automatically generates a canonical tag for your website, but in many cases this isn’t needed so you can turn it off to avoid any potential SEO ‘duplicate content’ backlash from search engines.', 'wds' ),
		),
		'metabox-lax_enforcement'              => array(
			'label'       => __( 'Enforce meta tag character limits', 'wds' ),
			'description' => __( 'Each meta tag type has recommended maximum characters lengths to follow. Turning this off will remove the enforcement preventing you from adding too many characters.', 'wds' ),
		),
	),
) );
?>

<div class="sui-box-settings-row wds-verification-tags">
	<div class="sui-box-settings-col-1">
		<label class="sui-settings-label"><?php esc_html_e( 'Search engines', 'wds' ); ?></label>
		<p class="sui-description"><?php esc_html_e( 'This tool will add the meta tags required by search engines to verify your site with their SEO management tools to your websites <head> tag.', 'wds' ); ?></p>
	</div>
	<div class="sui-box-settings-col-2">
		<div class="sui-form-field">
			<?php
			$value = isset( $sitemap_options['verification-google-meta'] ) ? $sitemap_options['verification-google-meta'] : '';
			?>
			<label for="verification-google"
			       class="sui-settings-label"><?php esc_html_e( 'Google Verification', 'wds' ); ?></label>
			<div class="sui-description">
				<?php esc_html_e( 'Paste the full meta tag from Google.', 'wds' ); ?>
			</div>
			<input id='verification-google'
			       name='<?php echo esc_attr( $_view['option_name'] ); ?>[verification-google-meta]'
			       type='text'
			       placeholder="<?php echo esc_attr( 'E.g. <meta name="google-site-verification" content="...' ); ?>"
			       class='sui-form-control'
			       value='<?php echo esc_attr( $value ); ?>'>
		</div>

		<div class="sui-form-field">
			<?php
			$value = isset( $sitemap_options['verification-bing-meta'] ) ? $sitemap_options['verification-bing-meta'] : '';
			?>
			<label for="verification-bing"
			       class="sui-settings-label"><?php esc_html_e( 'Bing Verification', 'wds' ); ?></label>
			<div class="sui-description">
				<?php esc_html_e( 'Paste the full meta tag from Bing.', 'wds' ); ?>
			</div>
			<input id='verification-bing'
			       name='<?php echo esc_attr( $_view['option_name'] ); ?>[verification-bing-meta]'
			       type='text'
			       class='sui-form-control'
			       placeholder="<?php echo esc_attr( 'E.g. <meta name="msvalidate.01" content="...' ); ?>"
			       value='<?php echo esc_attr( $value ); ?>'>
		</div>

		<div class="sui-form-field">
			<label for="verification-pages"
			       class="sui-settings-label"><?php esc_html_e( 'Add verification code to', 'wds' ); ?></label>
			<select id="verification-pages"
			        data-minimum-results-for-search="-1"
			        name="<?php echo esc_attr( $_view['option_name'] ); ?>[verification-pages]"
			        class="sui-select">
				<?php foreach ( $verification_pages as $item => $label ) : ?>
					<?php
					$selected = isset( $sitemap_options['verification-pages'] ) && $sitemap_options['verification-pages'] === $item ? 'selected' : '';
					?>
					<option
							value="<?php echo esc_attr( $item ); ?>"
						<?php echo esc_attr( $selected ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="sui-form-field">
			<div class="wds-custom-meta-tags">
				<label for="verification-google"
				       class="sui-settings-label"><?php esc_html_e( 'Custom meta tags', 'wds' ); ?></label>
				<span class="sui-description"><?php esc_html_e( 'Have more meta tags you want to add? Add as many as you like.', 'wds' ); ?></span>

				<?php if ( ! empty( $sitemap_options['additional-metas'] ) && is_array( $sitemap_options['additional-metas'] ) ) : ?>
					<?php
					foreach ( $sitemap_options['additional-metas'] as $custom_value ) {
						$this->_render( 'settings/settings-custom-meta-tag', array(
							'value' => $custom_value,
						) );
					}
					?>
				<?php endif; ?>

				<?php $this->_render( 'settings/settings-custom-meta-tag' ); ?>

				<button type="button"
				        class="sui-button sui-button-ghost">
					<i class="sui-icon-plus" aria-hidden="true"></i>

					<?php esc_html_e( 'Add Another', 'wds' ); ?>
				</button>
			</div>
		</div>
	</div>
</div>

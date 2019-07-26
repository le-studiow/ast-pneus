<?php
$engines = empty( $engines ) ? array() : $engines;
?>

<?php
$this->_render( 'toggle-group', array(
	'label'       => esc_html__( 'Include images', 'wds' ),
	'description' => esc_html__( 'If your posts contain imagery you would like others to be able to search, this setting will help Google Images index them correctly.', 'wds' ),
	'items'       => array(
		'sitemap-images' => array(
			'label'       => esc_html__( 'Include image items with the sitemap', 'wds' ),
			'description' => esc_html__( 'Note: When uploading attachments to posts, be sure to add titles and captions that clearly describe your images.', 'wds' ),
			'value'       => '1',
		),
	),
) );

$this->_render( 'toggle-group', array(
	'label'       => esc_html__( 'Auto-notify search engines', 'wds' ),
	'description' => esc_html__( 'Instead of waiting for search engines to crawl your website you can automatically submit your sitemap whenever it changes.', 'wds' ),
	'separator'   => true,
	'items'       => $engines,
) );

$this->_render( 'toggle-group', array(
	'label'       => esc_html__( 'Style sitemap', 'wds' ),
	'description' => esc_html__( 'Adds some nice styling to your sitemap.', 'wds' ),
	'separator'   => true,
	'items'       => array(
		'sitemap-stylesheet' => array(
			'label'       => esc_html__( 'Include stylesheet with sitemap', 'wds' ),
			'description' => esc_html__( 'Note: This doesn’t affect your SEO and is purely visual.', 'wds' ),
			'value'       => '1',
		),
	),
) );
?>

<?php $automatic_updates_disabled = ! empty( $_view['options']['sitemap-disable-automatic-regeneration'] ); ?>
<div class="wds-toggleable wds-disable-updates <?php echo $automatic_updates_disabled ? '' : 'inactive'; ?>">
	<?php
	$this->_render( 'toggle-group', array(
		'label'       => esc_html__( 'Automatic sitemap updates', 'wds' ),
		'description' => esc_html__( 'Choose whether or not you want SmartCrawl to update your Sitemap automatically when you publish new pages, posts, post types or taxonomies.', 'wds' ),
		'separator'   => true,
		'items'       => array(
			'sitemap-disable-automatic-regeneration' => array(
				'label'            => esc_html__( 'Automatically update my sitemap', 'wds' ),
				'inverted'         => true,
				'html_description' => sprintf(
					'<div class="wds-toggleable-inside sui-notice sui-notice-warning"><p>%s</p></div>',
					esc_html__( "Your sitemap isn't being updated automatically. Click Save Settings below to regenerate your sitemap.", 'wds' )
				),
			),
		),
	) );
	?>
	<div></div>
</div>

<div class="sui-box-settings-row">
	<div class="sui-box-settings-col-1">
		<label class="sui-settings-label">
			<?php esc_html_e( 'Deactivate', 'wds' ); ?>
		</label>
		<p class="sui-description">
			<?php esc_html_e( 'If you no longer wish to use the Sitemap generator, you can deactivate it.', 'wds' ); ?>
		</p>
	</div>
	<div class="sui-box-settings-col-2">
		<button type="submit" name="deactivate-sitemap-component"
		        class="sui-button sui-button-ghost">
			<i class="sui-icon-power-on-off" aria-hidden="true"></i>
			<?php esc_html_e( 'Deactivate', 'wds' ); ?>
		</button>

		<p class="sui-description">
			<?php esc_html_e( 'Note: Sitemaps are crucial for helping search engines index all of your content effectively. We highly recommend you have a valid sitemap.', 'wds' ); ?>
		</p>
	</div>
</div>

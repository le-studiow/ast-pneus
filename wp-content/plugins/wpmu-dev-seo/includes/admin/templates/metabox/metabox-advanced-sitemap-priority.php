<?php
$sitemap_priority_options = empty( $sitemap_priority_options ) ? array() : $sitemap_priority_options;
$sitemap_priority = empty( $sitemap_priority ) ? '' : $sitemap_priority;
?>

<?php if ( apply_filters( 'wds-metabox-visible_parts-sitemap_priority_area', true ) ) : ?>
	<div class="sui-box-settings-row">
		<div class="sui-box-settings-col-1">
			<label for='wds_sitemap-priority'
			       class="sui-settings-label"><?php esc_html_e( 'Sitemap Priority', 'wds' ); ?></label>
		</div>
		<div class="sui-box-settings-col-2">
			<select name='wds_sitemap-priority'
			        id='wds_sitemap-priority'
			        class="sui-select"
			        data-minimum-results-for-search="-1">

				<?php foreach ( $sitemap_priority_options as $key => $label ) : ?>
					<option value='<?php echo esc_attr( $key ); ?>' <?php selected( $key, $sitemap_priority ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
<?php endif; ?>

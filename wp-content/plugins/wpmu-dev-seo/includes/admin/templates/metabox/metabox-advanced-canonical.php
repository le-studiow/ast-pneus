<?php
$canonical_url = empty( $canonical_url ) ? '' : $canonical_url;
?>

<?php if ( apply_filters( 'wds-metabox-visible_parts-canonical_area', true ) ) : ?>
	<div class="sui-box-settings-row">
		<div class="sui-box-settings-col-1">
			<label for="wds_canonical" class="sui-settings-label"><?php esc_html_e( 'Canonical', 'wds' ); ?></label>
			<p class="sui-description">
				<?php esc_html_e( 'If you have several similar versions of this page you can point search engines to the canonical or "genuine" version to avoid duplicate content issues.', 'wds' ); ?>
			</p>
		</div>
		<div class="sui-box-settings-col-2">
			<input type='text' id='wds_canonical' name='wds_canonical'
			       value='<?php echo esc_attr( $canonical_url ); ?>'
			       class='wds sui-form-control'/>
			<span
					class="sui-description"><?php esc_html_e( 'Enter the full canonical URL including http:// or https://', 'wds' ); ?></span>
		</div>
	</div>
<?php endif; ?>

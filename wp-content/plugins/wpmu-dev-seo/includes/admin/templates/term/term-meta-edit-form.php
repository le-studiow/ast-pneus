<?php
$tax_meta = empty( $tax_meta ) ? array() : $tax_meta;
$term = empty( $term ) ? null : $term;

$meta_title = smartcrawl_get_array_value( $tax_meta, 'wds_title' );
$title_placeholder = Smartcrawl_Meta_Value_Helper::get()->get_title();

$meta_desc = smartcrawl_get_array_value( $tax_meta, 'wds_desc' );
$desc_placeholder = Smartcrawl_Meta_Value_Helper::get()->get_description();
?>
<div class="wds-toggleable inactive wds-edit-meta-toggleable">
	<label>
		<a class="sui-button sui-button-ghost">
			<i class="sui-icon-pencil" aria-hidden="true"></i>

			<?php esc_html_e( 'Edit Meta', 'wds' ); ?>
		</a>
		<input type="checkbox" class="hidden"/>
	</label>

	<div class="wds-toggleable-inside sui-border-frame">
		<div class="sui-form-field">
			<label class="sui-label" for="wds_title">
				<?php esc_html_e( 'SEO Title', 'wds' ); ?>
			</label>
			<input type="text"
			       id="wds_title"
			       name="wds_title"
			       placeholder="<?php echo esc_attr( $title_placeholder ); ?>"
			       value="<?php echo esc_attr( $meta_title ); ?>"
			       class="sui-form-control wds-meta-field"/>
			<p class="sui-description">
				<?php esc_html_e( 'The SEO title is used on the archive page for this term.', 'wds' ); ?>
			</p>
		</div>

		<div class="sui-form-field">
			<label class="sui-label" for="wds_metadesc">
				<?php esc_html_e( 'Description', 'wds' ); ?>
			</label>
			<textarea name="wds_desc"
			          id="wds_metadesc"
			          placeholder="<?php echo esc_attr( $desc_placeholder ); ?>"
			          class="sui-form-control wds-meta-field"><?php echo esc_textarea( $meta_desc ); ?></textarea>
			<p class="sui-description">
				<?php esc_html_e( 'The SEO description is used for the meta description on the archive page for this term.', 'wds' ); ?>
			</p>
		</div>
	</div>
</div>

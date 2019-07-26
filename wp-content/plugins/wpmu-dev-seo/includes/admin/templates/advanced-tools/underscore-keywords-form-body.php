<?php // phpcs:ignoreFile -- underscore template

$formats = sprintf(
	esc_html__( 'Formats include relative (E.g. %1$s) or absolute URLs (E.g. %2$s or %2$s).', 'wds' ),
	sprintf( '<strong>%s</strong>', esc_html__( '/cats', 'wds' ) ),
	sprintf( '<strong>%s</strong>', esc_html__( 'www.website.com/cats', 'wds' ) ),
	sprintf( '<strong>%s</strong>', esc_html__( 'https://website.com/cats', 'wds' ) )
);
?>
<div class="wds-keyword-form">
	<input type="hidden" class="wds-custom-idx" value="{{- idx }}"/>
	<div class="sui-form-field">
		<label class="sui-label">
			<?php esc_html_e( 'Keyword group', 'wds' ); ?>
			<span><?php esc_html_e( 'Usually related terms', 'wds' ); ?></span>
		</label>
		<input type="text" class="sui-form-control wds-custom-keywords" value="{{- keywords }}"
		       placeholder="<?php esc_attr_e( 'E.g. Cats, Kittens, Felines', 'wds' ); ?>"/>
	</div>

	<div class="sui-form-field">
		<label class="sui-label">
			<?php esc_html_e( 'Link URL', 'wds' ); ?>
			<span><?php esc_html_e( 'Both internal and external links are supported', 'wds' ); ?></span>
		</label>
		<input type="text" class="sui-form-control wds-custom-url" value="{{- url }}"
		       placeholder="<?php esc_attr_e( 'E.g. /cats', 'wds' ); ?>"/>
		<p class="sui-description">
			<small><?php echo wp_kses_post( $formats ); ?></small>
		</p>
	</div>
</div>

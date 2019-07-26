<?php
$post_types = get_post_types( array(
	'public'  => true,
	'show_ui' => true,
) );
if ( isset( $post_types['attachment'] ) ) {
	unset( $post_types['attachment'] );
}
?>

<div class="sui-form-field">
	<label for="wds-post-exclusion-type"
	       class="sui-label"><?php esc_html_e( 'Type', 'wds' ); ?></label>
	<select id="wds-post-exclusion-type"
	        class="sui-select"
	        data-minimum-results-for-search="-1">
		<?php foreach ( $post_types as $type_id => $type_name ): ?>
			<option id="<?php echo esc_attr( $type_id ); ?>">
				<?php echo esc_html( $type_name ); ?>
			</option>
		<?php endforeach; ?>
	</select>
</div>

<div class="sui-form-field">
	<label for="wds-post-exclusion-id"
	       class="sui-label"><?php esc_html_e( 'Post', 'wds' ); ?></label>

	<select id="wds-post-exclusion-id"
	        class="sui-select"
	        multiple
	        data-placeholder="<?php esc_attr_e( 'Start typing to search ...', 'wds' ); ?>">
	</select>
</div>

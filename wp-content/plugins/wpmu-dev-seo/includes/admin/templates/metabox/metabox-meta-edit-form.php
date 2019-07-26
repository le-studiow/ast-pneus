<?php
$post = empty( $post ) ? null : $post;
if ( $post ) {
	$post_id = $post->ID;
} else {
	return;
}
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
		<?php if ( apply_filters( 'wds-metabox-visible_parts-title_area', true ) ) : ?>
			<div class="sui-form-field">
				<label class="sui-label" for="wds_title">
					<?php esc_html_e( 'SEO Title', 'wds' ); ?>
					<span><?php echo esc_html( sprintf( __( '- Include your focus keywords. 50-%d characters recommended.', 'wds' ), SMARTCRAWL_TITLE_LENGTH_CHAR_COUNT_LIMIT ) ); ?></span>
				</label>
				<input type='text'
				       id='wds_title'
				       name='wds_title'
				       value='<?php echo esc_html( smartcrawl_get_value( 'title', $post_id ) ); ?>'
				       class='sui-form-control wds-meta-field'/>
			</div>
		<?php endif; ?>

		<?php if ( apply_filters( 'wds-metabox-visible_parts-description_area', true ) ) : ?>
			<div class="sui-form-field">
				<label class="sui-label" for="wds_metadesc">
					<?php esc_html_e( 'Description', 'wds' ); ?>
					<span><?php echo esc_html( sprintf( __( '- Recommended minimum of 135 characters, maximum %d.', 'wds' ), SMARTCRAWL_METADESC_LENGTH_CHAR_COUNT_LIMIT ) ); ?></span>
				</label>
				<textarea rows='2'
				          name='wds_metadesc'
				          id='wds_metadesc'
				          class='sui-form-control wds-meta-field'><?php echo esc_textarea( smartcrawl_get_value( 'metadesc', $post_id ) ); ?></textarea>
			</div>
		<?php endif; ?>

		<?php if ( apply_filters( 'wds-metabox-visible_parts-keywords_area', true ) ) : ?>
			<div class="sui-form-field">
				<label class="sui-label" for="wds_keywords">
					<?php esc_html_e( 'Keywords', 'wds' ); ?>
					<span><?php esc_html_e( '- Try to avoid stop words like ‘and’ and ‘the’ which search engines ignore.', 'wds' ); ?></span>
				</label>
				<input type='text'
				       id='wds_keywords'
				       name='wds_keywords'
				       value='<?php echo esc_html( smartcrawl_get_value( 'keywords', $post_id ) ); ?>'
				       class='sui-form-control'/>
			</div>

			<div class="wds-extra-keyword-options">
				<div class="sui-form-field">
					<label class="sui-label" for="wds_news_keywords">
						<?php esc_html_e( 'News Keywords', 'wds' ); ?>
						<span><?php esc_html_e( '- Try to avoid stop words like ‘and’ and ‘the’ which search engines ignore.', 'wds' ); ?></span>
					</label>
					<input type='text'
					       id='wds_news_keywords'
					       name='wds_news_keywords'
					       value='<?php echo esc_attr( smartcrawl_get_value( 'news_keywords', $post_id ) ); ?>'
					       class='sui-form-control'/>
				</div>

				<div class="wds-tags-as-keyword">
					<?php
					$this->_render( 'toggle-item', array(
						'field_name'       => 'wds_tags_to_keywords',
						'checked'          => smartcrawl_get_value( 'tags_to_keywords', $post_id ) ? 'checked="checked"' : '',
						'item_label'       => esc_html__( 'Tags As Keywords' ),
						'item_description' => esc_html__( 'If you enable using tags, post tags will be merged in with any other keywords you enter in the text box.' ),
					) );
					?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>

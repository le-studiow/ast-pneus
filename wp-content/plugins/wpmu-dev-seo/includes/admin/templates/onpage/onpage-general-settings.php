<?php
$title_key = empty( $title_key ) ? '' : $title_key;
$description_key = empty( $description_key ) ? '' : $description_key;
$keywords_key = empty( $keywords_key ) ? '' : $keywords_key;

$title_label_desc = empty( $title_label_desc )
	? esc_html__( 'Choose the variables from which SmartCrawl will automatically generate your SEO title from.', 'wds' ) : $title_label_desc;
$title_field_desc = empty( $title_field_desc )
	? '' : $title_field_desc;
$meta_label_desc = empty( $meta_label_desc )
	? esc_html__( 'A title needs a description. Choose the variables to automatically generate a description from.', 'wds' ) : $meta_label_desc;
$meta_field_desc = empty( $meta_field_desc )
	? '' : $meta_field_desc;

$option_name = empty( $_view['option_name'] ) ? '' : $_view['option_name'];
$options = empty( $_view['options'] ) ? array() : $_view['options'];
$option_name_format = $option_name . '[%s]';

$title = $title_key
	? smartcrawl_get_array_value( $options, $title_key )
	: '';
$description = $description_key
	? smartcrawl_get_array_value( $options, $description_key )
	: '';
$keywords = $keywords_key
	? smartcrawl_get_array_value( $options, $keywords_key )
	: '';
?>

<?php if ( $title_key ): ?>
	<div class="sui-box-settings-row wds-title-row">
		<div class="sui-box-settings-col-1">
			<label for="<?php echo esc_attr( $title_key ); ?>"
			       class="sui-settings-label"><?php esc_html_e( 'Title', 'wds' ); ?></label>
			<span class="sui-description"><?php echo esc_html( $title_label_desc ); ?></span>
		</div>
		<div class="sui-box-settings-col-2">
			<div class="sui-insert-variables wds-allow-macros">
				<input id="<?php echo esc_attr( $title_key ); ?>"
				       name="<?php echo esc_attr( sprintf( $option_name_format, $title_key ) ); ?>"
				       type="text" class="sui-form-control"
				       value="<?php echo esc_attr( $title ); ?>">
				<?php $this->_render( 'macros-dropdown' ); ?>
			</div>

			<span class="sui-description"><?php echo esc_html( $title_field_desc ); ?></span>
		</div>
	</div>
<?php endif; ?>

<?php if ( $description_key ): ?>
	<div class="sui-box-settings-row wds-description-row">
		<div class="sui-box-settings-col-1">
			<label for="<?php echo esc_attr( $description_key ); ?>"
			       class="sui-settings-label"><?php esc_html_e( 'Description', 'wds' ); ?></label>
			<span class="sui-description"><?php echo esc_html( $meta_label_desc ); ?></span>
		</div>
		<div class="sui-box-settings-col-2">
			<div class="sui-insert-variables wds-allow-macros">
				<textarea id="<?php echo esc_attr( $description_key ); ?>"
				          name="<?php echo esc_attr( sprintf( $option_name_format, $description_key ) ); ?>"
				          type="text"
				          class="sui-form-control"><?php echo esc_textarea( $description ); ?></textarea>
				<?php $this->_render( 'macros-dropdown' ); ?>
			</div>

			<span class="sui-description"><?php echo esc_html( $meta_field_desc ); ?></span>
		</div>
	</div>
<?php endif; ?>

<?php if ( $keywords_key ): ?>
	<div class="sui-box-settings-row wds-keywords-row">
		<div class="sui-box-settings-col-1">
			<label for="<?php echo esc_attr( $keywords_key ); ?>"
			       class="sui-settings-label"><?php esc_html_e( 'Keywords', 'wds' ); ?></label>
			<span class="sui-description"><?php esc_html_e( 'Adding keyword meta to your pages is largely redundant practice, but you can do this here if you wish to add some.' ); ?></span>
		</div>
		<div class="sui-box-settings-col-2">
			<input id="<?php echo esc_attr( $keywords_key ); ?>"
			       name="<?php echo esc_attr( sprintf( $option_name_format, $keywords_key ) ); ?>"
			       type="text" class="sui-form-control"
			       value="<?php echo esc_attr( $keywords ); ?>">
			<span class="sui-description">
				<?php esc_html_e( 'Add as many keywords as you like, comma separated.', 'wds' ); ?>
			</span>
		</div>
	</div>
<?php endif; ?>

<?php
$main_title = empty( $main_title ) ? '' : $main_title;
$main_description = empty( $main_description ) ? '' : $main_description;
$field_name = empty( $field_name ) ? '' : $field_name;
$disabled = empty( $disabled ) ? false : true;
$current_title = empty( $current_title ) ? '' : $current_title;
$title_placeholder = empty( $title_placeholder ) ? '' : $title_placeholder;
$current_description = empty( $current_description ) ? '' : $current_description;
$description_placeholder = empty( $description_placeholder ) ? '' : $description_placeholder;
$images = empty( $images ) ? array() : $images;
$images_available = ! empty( $images ) && is_array( $images );
$single_image = empty( $single_image ) ? false : true;
$images_description = empty( $images_description ) ? false : $images_description;
$toggle_label = empty( $toggle_label ) ? esc_html__( 'Enable for this post', 'wds' ) : $toggle_label;
?>
<div class="sui-box-settings-row">
	<div class="sui-box-settings-col-1">
		<label class="sui-settings-label"><?php echo esc_html( $main_title ); ?></label>
		<p class="sui-description"><?php echo esc_html( $main_description ); ?></p>
	</div>
	<div class="sui-box-settings-col-2">
		<div class="wds-toggleable inverted <?php echo $disabled ? 'inactive' : ''; ?>">
			<?php
			$this->_render( 'toggle-item', array(
				'inverted'   => true,
				'field_name' => $field_name . '[disabled]',
				'field_id'   => $field_name . '-disabled',
				'checked'    => checked( $disabled, true, false ),
				'item_label' => $toggle_label,
			) );
			?>
			<div class="wds-toggleable-inside sui-border-frame sui-toggle-content <?php echo esc_attr( $field_name ); ?>-meta">
				<div class="sui-form-field">
					<label for="<?php echo esc_attr( $field_name ); ?>-title"
					       class="sui-label"><?php esc_html_e( 'Title', 'wds' ); ?></label>
					<input type="text"
					       class="sui-form-control"
					       id="<?php echo esc_attr( $field_name ); ?>-title"
					       name="<?php echo esc_attr( $field_name ); ?>[title]"
					       placeholder="<?php echo esc_attr( $title_placeholder ); ?>"
					       value="<?php echo esc_attr( $current_title ); ?>"/>
				</div>

				<div class="sui-form-field">
					<label for="<?php echo esc_attr( $field_name ); ?>-description" class="sui-label">
						<?php esc_html_e( 'Description', 'wds' ); ?>
					</label>
					<textarea name="<?php echo esc_attr( $field_name ); ?>[description]"
					          class="sui-form-control"
					          placeholder="<?php echo esc_attr( $description_placeholder ); ?>"
					          id="<?php echo esc_attr( $field_name ); ?>-description"><?php echo esc_textarea( $current_description ); ?></textarea>
				</div>

				<div class="sui-form-field">
					<label for="<?php echo esc_attr( $field_name ); ?>-images" class="sui-label">
						<?php echo $single_image ? esc_html__( 'Featured Image', 'wds' ) : esc_html__( 'Featured Images', 'wds' ); ?>
					</label>
					<div class="og-images"
					     data-singular="<?php echo $single_image ? 'true' : 'false'; ?>"
					     data-name="<?php echo esc_attr( $field_name ); ?>[images]">
						<div class="add-action-wrapper sui-tooltip"
						     data-tooltip="<?php esc_attr_e( 'Add featured image', 'wds' ); ?>"
						     style="<?php echo $single_image && $images_available ? 'display:none;' : ''; ?>">
							<a href="#add" id="<?php echo esc_attr( $field_name ); ?>-images"
							   title="<?php esc_attr_e( 'Add image', 'wds' ); ?>">
								<i class="sui-icon-upload-cloud" aria-hidden="true"></i>
							</a>
						</div>
						<?php if ( $images_available ) : ?>
							<?php foreach ( $images as $img ) : ?>
								<input type="text"
								       name="<?php echo esc_attr( $field_name ); ?>[images][]"
								       value="<?php echo esc_attr( $img ); ?>"/>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>

					<p class="sui-description">
						<?php if ( $images_description ): ?>
							<?php echo esc_html( $images_description ); ?>
						<?php elseif ( $single_image ): ?>
							<?php esc_html_e( 'This image will be used as the featured image when the post is shared.', 'wds' ); ?>
						<?php else: ?>
							<?php esc_html_e( 'Each of these images will be available to use as the featured image when the post is shared.', 'wds' ); ?>
						<?php endif; ?>
					</p>
				</div>
			</div>
		</div>
	</div>
</div>

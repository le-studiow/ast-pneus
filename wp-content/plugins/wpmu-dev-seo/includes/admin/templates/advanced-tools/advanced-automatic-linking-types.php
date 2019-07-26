<?php
$option_name = empty( $_view['option_name'] ) ? '' : $_view['option_name'];
$insert = empty( $insert ) ? array() : $insert;
$linkto = empty( $linkto ) ? array() : $linkto;
?>

<div class="sui-row wds-autolink-types">
	<div class="sui-col">
		<div class="sui-field-list">

			<div class="sui-field-list-header">
				<h3 class="sui-field-list-title"><?php esc_html_e( 'Link To', 'wds' ); ?></h3>
				<p id="link-to-description"
				   class="sui-description"><?php esc_html_e( 'Choose what post types to allow.', 'wds' ); ?></p>
			</div>

			<div class="sui-field-list-body">
				<?php foreach ( $insert as $insert_key => $insert_label ): ?>
					<?php $checked = ( ! empty( $_view['options'][ $insert_key ] ) ) ? "checked" : ''; ?>
					<div class="sui-field-list-item">
						<label class="sui-field-list-item-label"
						       for="<?php echo esc_attr( $option_name ); ?>-<?php echo esc_attr( $insert_key ); ?>">
							<small><strong><?php echo esc_html( $insert_label ); ?></strong></small>
						</label>
						<label class="sui-toggle">
							<input type="checkbox" <?php echo esc_attr( $checked ); ?>
							       aria-describedby="link-to-description"
							       value="<?php echo esc_attr( $insert_key ); ?>"
							       name="<?php echo esc_attr( $option_name ); ?>[<?php echo esc_attr( $insert_key ); ?>]"
							       id="<?php echo esc_attr( $option_name ); ?>-<?php echo esc_attr( $insert_key ); ?>">
							<span aria-hidden="true" class="sui-toggle-slider"></span>
						</label>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<div class="sui-col">
		<div class="sui-field-list">

			<div class="sui-field-list-header">
				<h3 class="sui-field-list-title"><?php esc_html_e( 'Insert Links', 'wds' ); ?></h3>
				<p id="insert-list-description" class="sui-description">
					<?php esc_html_e( 'Select what types can be linked to.', 'wds' ); ?>
				</p>
			</div>

			<div class="sui-field-list-body">
				<?php foreach ( $linkto as $linkto_key => $linkto_label ): ?>
					<?php $checked = ( ! empty( $_view['options'][ $linkto_key ] ) ) ? "checked" : ''; ?>

					<div class="sui-field-list-item">
						<label class="sui-field-list-item-label"
						       for="<?php echo esc_attr( $option_name ); ?>-<?php echo esc_attr( $linkto_key ); ?>">
							<small><strong><?php echo esc_html( $linkto_label ); ?></strong></small>
						</label>
						<label class="sui-toggle">
							<input type="checkbox" <?php echo esc_attr( $checked ); ?>
							       aria-describedby="insert-list-description"
							       value="<?php echo esc_attr( $linkto_key ); ?>"
							       name="<?php echo esc_attr( $option_name ); ?>[<?php echo esc_attr( $linkto_key ); ?>]"
							       id="<?php echo esc_attr( $option_name ); ?>-<?php echo esc_attr( $linkto_key ); ?>">
							<span aria-hidden="true" class="sui-toggle-slider"></span>
						</label>
					</div>
				<?php endforeach; ?>
			</div>

		</div>
	</div>
</div>

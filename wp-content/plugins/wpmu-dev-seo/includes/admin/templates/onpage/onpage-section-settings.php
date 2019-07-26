<?php $separators = empty( $separators ) ? array() : $separators; ?>
	<div class="sui-box-settings-row">
		<div class="sui-box-settings-col-1">
			<label for="separator" class="sui-settings-label"><?php esc_html_e( 'Separator', 'wds' ); ?></label>
			<p class="sui-description">
				<?php echo sprintf(
					esc_html__( 'The separator refers to the break between variables which you can use by referencing the %s tag. You can choose a preset one or bake your own.', 'wds' ),
					'%%sep%%'
				); ?>
			</p>
		</div>
		<div class="sui-box-settings-col-2">
			<div class="wds-preset-separators">
				<?php foreach ( $separators as $key => $separator ) : ?>
					<input
						type="radio"
						name="<?php echo esc_attr( $_view['option_name'] ); ?>[preset-separator]"
						id="separator-<?php echo esc_attr( $key ); ?>"
						value="<?php echo esc_attr( $key ); ?>"
						autocomplete="off"
						<?php echo $_view['options']['preset-separator'] === $key ? 'checked="checked"' : ''; ?> />
					<label class="separator-selector" for="separator-<?php echo esc_attr( $key ); ?>">
						<?php echo esc_html( $separator ); ?>
					</label>
				<?php endforeach; ?>
			</div>
			<p class="wds-custom-separator-message"><?php esc_html_e( 'Or, choose your own custom separator.', 'wds' ); ?></p>
			<input
				id='separator'
				placeholder="<?php esc_attr_e( 'Enter custom separator', 'wds' ); ?>"
				name='<?php echo esc_attr( $_view['option_name'] ); ?>[separator]'
				type='text'
				class='sui-form-control'
				value='<?php echo esc_attr( $_view['options']['separator'] ); ?>'/>
		</div>
	</div>

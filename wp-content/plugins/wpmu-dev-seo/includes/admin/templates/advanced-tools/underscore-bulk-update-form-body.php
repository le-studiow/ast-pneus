<div class="wds-bulk-update-fields">

	{{ if(indices) { }}
	<div class="sui-form-field">
		<label class="sui-label"><?php esc_attr_e( 'New URL', 'wds' ); ?></label>
		<input class="sui-form-control"
		       placeholder="<?php esc_attr_e( 'E.g. /cats-new', 'wds' ); ?>" type="text"/>
	</div>

	<div class="sui-form-field">
		<label class="sui-label"><?php esc_html_e( 'Redirect Type', 'wds' ); ?></label>
		<select>
			<option value="302"><?php esc_html_e( 'Temporary', 'wds' ); ?></option>
			<option value="301"><?php esc_html_e( 'Permanent', 'wds' ); ?></option>
		</select>
		<span class="sui-description">
			<?php esc_html_e( 'This tells search engines whether to keep indexing the old page, or replace it with the new page.', 'wds' ); ?>
		</span>
	</div>
	{{ } else { }}
	<?php $this->_render( 'notice', array(
		'message' => esc_html__( 'Please select some items to edit them.', 'wds' ),
	) ); ?>
	{{ } }}

	<input type="hidden" value="{{- indices }}"/>
</div>

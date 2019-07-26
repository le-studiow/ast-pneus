<p><?php esc_html_e( "We have encountered an error while importing your data. You may retry the import or contact our support if the problem persists.", 'wds' ); ?></p>
{{ if(error) { }}
<?php $this->_render( 'notice', array(
	'message' => '{{- error }}',
) ); ?>
{{ } }}

<div class="wds-import-footer">
	<div class="cf">
		<button type="button" class="sui-button sui-button-ghost wds-import-skip">
			<?php esc_html_e( 'Cancel', 'wds' ); ?>
		</button>

		<button class="sui-button wds-import-main-action wds-reattempt-import">
			<?php esc_html_e( 'Try Again', 'wds' ); ?>
		</button>
	</div>
</div>

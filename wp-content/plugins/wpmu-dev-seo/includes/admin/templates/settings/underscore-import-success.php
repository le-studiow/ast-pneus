<p><?php esc_html_e( 'All done, off to the pub!', 'wds' ); ?></p>
<div class="wds-notice sui-notice sui-notice-success">
	<p>
		<?php printf( esc_html__( 'Your %s settings have been imported successfully and are now active.', 'wds' ), '{{- plugin_name }}' ); ?>
		{{ if(deactivation_url) { }}
		<?php printf( esc_html__( 'We highly recommend you deactivate %s to avoid potential conflicts.', 'wds' ), '{{- plugin_name }}' ); ?>
		{{ } }}
	</p>
</div>

<div class="wds-import-footer">
	<div class="cf">
		<button type="button" class="sui-button sui-button-ghost wds-import-skip">
			<?php esc_html_e( 'Close', 'wds' ); ?>
		</button>

		{{ if(deactivation_url) { }}
		<a class="sui-button wds-import-main-action" href="{{= deactivation_url }}">
			<i class="sui-icon-power-on-off" aria-hidden="true"></i>

			<?php esc_html_e( 'Deactivate', 'wds' ); ?> {{- plugin_name }}
		</a>
		{{ } }}
	</div>
</div>

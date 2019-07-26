<button type="button" data-a11y-dialog-hide
        class="sui-button sui-button-ghost">
	<?php esc_html_e( 'Cancel', 'wds' ); ?>
</button>

{{ if(indices) { }}
<button type="button"
        class="sui-button wds-action-button">
	<i class="sui-icon-check" aria-hidden="true"></i>

	<?php esc_html_e( 'Apply', 'wds' ); ?>
</button>
{{ } }}

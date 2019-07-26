<?php // phpcs:ignoreFile -- underscore template ?>
<button type="button" data-a11y-dialog-hide
        class="sui-button sui-button-ghost">
	<?php esc_html_e( 'Cancel', 'wds' ); ?>
</button>
<button type="button" class="wds-action-button sui-button">
	{{- idx == 0 ? Wds.l10n('keywords', 'Add') : Wds.l10n('keywords', 'Update') }}
</button>

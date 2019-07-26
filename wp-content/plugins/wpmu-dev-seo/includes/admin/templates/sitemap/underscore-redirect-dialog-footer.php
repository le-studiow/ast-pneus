<div data-issue-id="{{- issue_id }}">
	<button data-a11y-dialog-hide=""
	        type="button"
	        aria-label="Close this dialog window"
	        class="sui-button sui-button-ghost wds-disabled-during-request"><?php esc_html_e( 'Cancel', 'wds' ); ?></button>
	<button type="button"
	        class="sui-button wds-submit-redirect wds-disabled-during-request">
	<span class="sui-loading-text"><i class="sui-icon-check" aria-hidden="true"></i>
		<?php esc_html_e( 'Apply', 'wds' ); ?></span>
		<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
	</button>
</div>

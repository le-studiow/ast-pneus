<?php // phpcs:ignoreFile -- underscore template ?>
<div class="wds-keyword-pairs">

	{{ if (pairs) { }}
		<table class="wds-keyword-pairs-existing sui-table">
			<tr>
				<th><?php esc_html_e( 'Keyword', 'wds' ); ?></th>
				<th colspan="2"><?php esc_html_e( 'Auto-Linked URL', 'wds' ); ?></th>
			</tr>
			{{= pairs }}
		</table>
	{{ } }}

	<div class="wds-keyword-pair-new">
		<button type="button" class="sui-button">
			<i class="sui-icon-plus" aria-hidden="true"></i>
			<?php esc_html_e('Add Link', 'wds'); ?>
		</button>
	</div><!-- end wds-keyword-pair-new -->

</div>

<?php // phpcs:ignoreFile -- underscore template ?>
<div class="wds-postlist-list wds-postlist-list-exclude">
{{ if (loaded) { }}
	<table class="wds-postlist sui-table {{= (!!posts ? '' : 'wds-postlist-empty_list') }}">
		<tr>
			<th><?php esc_html_e('Post', 'wds'); ?></th>
			<th colspan="2"><?php esc_html_e('Post Type', 'wds'); ?></th>
		</tr>
		{{= posts }}
	</table>
	<div class="wds-notice sui-notice {{= (!!posts ? '' : 'wds-postlist-empty_list') }}">
		<p><?php esc_html_e("You haven't chosen to exclude any posts/pages.", 'wds'); ?></p>
	</div>
{{ } else { }}
	<p><i><?php esc_html_e('Loading posts, please hold on', 'wds'); ?></i></p>
{{ } }}
	<div class="wds-postlist-add-post">
		<a href="#wds-postlist-selector" rel="dialog" class="sui-button">
			<i class="sui-icon-plus" aria-hidden="true"></i>
			<?php esc_html_e('Add Exclusion', 'wds'); ?>
		</a>
	</div>
</div>

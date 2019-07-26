<?php // phpcs:ignoreFile -- underscore template ?>
<tr data-id="{{- id }}">
	{{ if (is_loaded) { }}
		<td class="wds-postlist-item-title"><strong>{{= title }}</strong></td>
		<td class="wds-postlist-item-type">{{= type }}</td>
		<td class="wds-postlist-item-remove">
			<a href="#remove" class="wds-postlist-list-item-remove">
				<button class="sui-button-icon" type="button">
					<i class="sui-icon-trash" aria-hidden="true"></i>
				</button>
			</a>
		</td>
	{{ } else { }}
		<td colspan="3"><?php esc_html_e('Loading post', 'wds'); ?> {{= id }}...</td>
	{{ } }}
</tr>

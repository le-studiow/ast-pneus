<?php // phpcs:ignoreFile -- underscore template ?>
<div class="insert-macro">
  	<button class="button-fields-trigger" data-content="<?php esc_html_e('Insert dynamic macro', 'wds'); ?>" id="toogle-macro-list-"><i class="wds-icon-plus"></i></button>
	<div class="macro-list" style="display: none">
		<ul>
		{{ _.each(macros, function (desc, macro) { }}
			<li data-macro="{{- macro }}">
				{{- desc }}
			</li>
		{{ }); }}
		</ul>
	</div>
</div>

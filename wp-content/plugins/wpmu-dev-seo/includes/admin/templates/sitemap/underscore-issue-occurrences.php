<?php // phpcs:ignoreFile ?>
<ul>
	{{ _.each(occurrences, function (occurrence) { }}
	<li>
		<a href="{{- occurrence }}">
			{{- occurrence }}
		</a>
	</li>
	{{ }); }}
</ul>

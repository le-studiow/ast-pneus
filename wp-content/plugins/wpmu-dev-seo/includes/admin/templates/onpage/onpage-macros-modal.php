<?php $macros = empty( $macros ) ? array() : $macros; ?>

<div id="wds-show-supported-macros">
	<table class="wds-data-table wds-data-table-inverse-large">
		<thead>
		<tr>
			<th class="label"><?php esc_html_e( 'Title', 'wds' ); ?></th>
			<th class="result"><?php esc_html_e( 'Gets Replaced By', 'wds' ); ?></th>
		</tr>
		</thead>
		<tfoot>
		<tr>
			<th class="label"><?php esc_html_e( 'Title', 'wds' ); ?></th>
			<th class="result"><?php esc_html_e( 'Gets Replaced By', 'wds' ); ?></th>
		</tr>
		</tfoot>
		<tbody>

		<?php foreach ( $macros as $macro => $label ) { ?>
			<tr>
				<td class="data data-small"><?php echo esc_html( $macro ); ?></td>
				<td class="data data-small"><?php echo esc_html( $label ); ?></td>
			</tr>
		<?php } ?>

		</tbody>
	</table>
</div>

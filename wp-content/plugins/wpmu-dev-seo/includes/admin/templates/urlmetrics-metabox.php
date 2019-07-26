<?php
$attribution = empty( $attribution ) ? '#' : $attribution;
$urlmetrics = empty( $urlmetrics ) ? new stdClass() : $urlmetrics;
?>
<table class="sui-table">
	<thead>
	<tr>
		<th class="label"><?php esc_html_e( 'Metric', 'wds' ); ?></th>
		<th class="result"><?php esc_html_e( 'Value', 'wds' ); ?></th>
	</tr>
	</thead>

	<tbody>
	<tr>
		<td>
			<strong><?php esc_html_e( 'External Links', 'wds' ); ?></strong>
			<a href="https://moz.com/learn/seo/external-link" target="_blank">(?)</a>
		</td>
		<td>
			<a href="<?php echo esc_attr( $attribution ); ?>"
			   target="_blank">
				<?php echo ! empty( $urlmetrics->ueid ) ? esc_attr( $urlmetrics->ueid ) : '0'; ?>
			</a>
		</td>
	</tr>
	<tr>
		<td>
			<strong><?php esc_html_e( 'Links', 'wds' ); ?></strong>
			<a href="https://moz.com/learn/seo/internal-link" target="_blank">(?)</a>
		</td>
		<td>
			<a href="<?php echo esc_attr( $attribution ); ?>"
			   target="_blank">
				<?php echo ! empty( $urlmetrics->uid ) ? esc_attr( $urlmetrics->uid ) : '0'; ?>
			</a>
		</td>
	</tr>
	<tr>
		<td>
			<strong><?php esc_html_e( 'mozRank', 'wds' ); ?></strong>
			<a href="https://moz.com/learn/seo/mozrank" target="_blank">(?)</a>
		</td>
		<td>
			<?php esc_html_e( '10-point score:', 'wds' ); ?>
			<a href="<?php echo esc_attr( $attribution ); ?>"
			   target="_blank">
				<?php echo( ! empty( $urlmetrics->umrp ) ? esc_attr( $urlmetrics->umrp ) : '--' ); ?>
			</a>
			<br/>
			<?php esc_html_e( 'Raw score:', 'wds' ); ?>
			<a href="<?php echo esc_attr( $attribution ); ?>"
			   target="_blank">
				<?php echo ! empty( $urlmetrics->umrr ) ? esc_attr( $urlmetrics->umrr ) : '--'; ?>
			</a>
		</td>
	</tr>
	<tr>
		<td>
			<strong><?php esc_html_e( 'Page Authority', 'wds' ); ?></strong>
			<a href="https://moz.com/learn/seo/page-authority" target="_blank">(?)</a>
		</td>
		<td>
			<a href="<?php echo esc_attr( $attribution ); ?>"
			   target="_blank">
				<?php echo ! empty( $urlmetrics->upa ) ? esc_attr( $urlmetrics->upa ) : '0'; ?>
			</a>
		</td>
	</tr>
	</tbody>
	<tfoot>
	<tr>
		<th class="label"><?php esc_html_e( 'Metric', 'wds' ); ?></th>
		<th class="result"><?php esc_html_e( 'Value', 'wds' ); ?></th>
	</tr>
	</tfoot>
</table>

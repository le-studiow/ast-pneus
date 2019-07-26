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
			<strong><?php esc_html_e( 'Domain mozRank', 'wds' ); ?></strong><br>
			<?php printf( esc_html__( 'Measure of the mozRank %s of the domain in the Linkscape index', 'wds' ), '<a href="https://moz.com/learn/seo/mozrank" target="_blank">(?)</a>' ); ?>
		</td>
		<td>
			<?php esc_html_e( '10-point score:', 'wds' ); ?>&nbsp;
			<a href="<?php echo esc_attr( $attribution ); ?>"
			   target="_blank"><?php echo( ! empty( $urlmetrics->fmrp ) ? esc_html( $urlmetrics->fmrp ) : '' ); ?></a>
			<br>
			<?php esc_html_e( 'Raw score:', 'wds' ); ?>&nbsp;
			<a href="<?php echo esc_attr( $attribution ); ?>"
			   target="_blank"><?php echo( ! empty( $urlmetrics->fmrr ) ? esc_html( $urlmetrics->fmrr ) : '' ); ?></a>
		</td>
	</tr>
	<tr>
		<td>
			<strong><?php esc_html_e( 'Domain Authority', 'wds' ); ?></strong>
			<a href="https://moz.com/learn/seo/domain-authority" target="_blank">(?)</a>
		</td>
		<td>
			<a href="<?php echo esc_attr( $attribution ); ?>"
			   target="_blank"><?php echo( ! empty( $urlmetrics->pda ) ? esc_html( $urlmetrics->pda ) : '' ); ?></a>
		</td>
	</tr>
	<tr>
		<td>
			<strong><?php esc_html_e( 'External Links to Homepage', 'wds' ); ?></strong><br>
			<?php printf( esc_html__( 'The number of external (from other subdomains), juice passing links %s to the target URL in the Linkscape index', 'wds' ), '<a href="https://moz.com/learn/seo/external-link" target="_blank">(?)</a>' ); ?>
		</td>
		<td>
			<a href="<?php echo esc_attr( $attribution ); ?>"
			   target="_blank"><?php echo( ! empty( $urlmetrics->ueid ) ? esc_html( $urlmetrics->ueid ) : '' ); ?></a>
		</td>
	</tr>
	<tr>
		<td>
			<strong><?php esc_html_e( 'Links to Homepage', 'wds' ); ?></strong><br>
			<?php printf( esc_html__( 'The number of internal and external, juice and non-juice passing links %s to the target URL in the Linkscape index', 'wds' ), '<a href="https://moz.com/learn/seo/internal-link" target="_blank">(?)</a>' ); ?>
		</td>
		<td>
			<a href="<?php echo esc_attr( $attribution ); ?>"
			   target="_blank"><?php echo( ! empty( $urlmetrics->uid ) ? esc_html( $urlmetrics->uid ) : '' ); ?></a>
		</td>
	</tr>
	<tr>
		<td>
			<strong><?php esc_html_e( 'Homepage mozRank', 'wds' ); ?></strong><br>
			<?php printf( esc_html__( 'Measure of the mozRank %s of the homepage URL in the Linkscape index', 'wds' ), '<a href="https://moz.com/learn/seo/mozrank" target="_blank">(?)</a>' ); ?>
		</td>
		<td>
			<?php esc_html_e( '10-point score:', 'wds' ); ?>&nbsp;
			<a href="<?php echo esc_attr( $attribution ); ?>"
			   target="_blank"><?php echo( ! empty( $urlmetrics->umrp ) ? esc_html( $urlmetrics->umrp ) : '' ); ?></a>
			<br>
			<?php esc_html_e( 'Raw score:', 'wds' ); ?>&nbsp;
			<a href="<?php echo esc_attr( $attribution ); ?>"
			   target="_blank"><?php echo( ! empty( $urlmetrics->umrr ) ? esc_html( $urlmetrics->umrr ) : '' ); ?></a>
		</td>
	</tr>
	<tr>
		<td>
			<strong><?php esc_html_e( 'Homepage Authority', 'wds' ); ?></strong>
			<a href="https://moz.com/learn/seo/page-authority" target="_blank">(?)</a>
		</td>
		<td>
			<a href="<?php echo esc_attr( $attribution ); ?>"
			   target="_blank"><?php echo( ! empty( $urlmetrics->upa ) ? esc_html( $urlmetrics->upa ) : '' ); ?></a>
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

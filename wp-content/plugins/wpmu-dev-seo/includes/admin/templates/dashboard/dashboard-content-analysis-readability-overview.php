<?php
/**
 * Readability analysis dashboard widget template
 *
 * @package wpmu-dev-seo
 */

$analysis_model = new Smartcrawl_Model_Analysis();
$overview = $analysis_model->get_overall_readability_analysis();

if ( ! $overview ) {
	return;
}

$total = smartcrawl_get_array_value( $overview, 'total' );
$passed = smartcrawl_get_array_value( $overview, 'passed' );
$type_breakdown = smartcrawl_get_array_value( $overview, 'post-types' );

if ( is_null( $total ) || is_null( $passed ) || is_null( $type_breakdown ) ) {
	return;
}

$percentage = ! empty( $total )
	? intval( ceil( ( $passed / $total ) * 100 ) )
	: 0;

if ( 0 === $passed && 0 === $total ) {
	$class = 'wds-check-invalid';
	$indicator_class = 'sui-tag-inactive';
	$indicator = esc_html__( 'No data yet', 'wds' );
} elseif ( $percentage > 79 ) {
	$class = 'wds-check-success sui-success';
	$indicator_class = 'sui-tag-success';
	$indicator = esc_html__( 'Easy', 'wds' );
} elseif ( $percentage > 59 ) {
	$class = 'wds-check-warning sui-warning';
	$indicator_class = 'sui-tag-warning';
	$indicator = esc_html__( 'Difficult', 'wds' );
} else {
	$class = 'wds-check-error sui-error';
	$indicator_class = 'sui-tag-error';
	$indicator = esc_html__( 'Difficult', 'wds' );
}
?>
<section class="wds-accordion wds-draw-left sui-accordion wds-readability-analysis-overview">
	<div class="sui-accordion-item wds-check-item <?php echo esc_attr( $class ); ?>">
		<div class="sui-accordion-item-header">
			<div class="sui-accordion-item-title sui-accordion-col-6">
				<?php esc_html_e( 'Overall Readability Analysis', 'wds' ); ?>
			</div>

			<div class="sui-accordion-col-4">
				<span class="sui-tag <?php echo esc_attr( $indicator_class ); ?>"><?php echo esc_html( $indicator ); ?></span>
			</div>

			<div class="sui-accordion-col-1">
				<span class="sui-accordion-open-indicator">
				<i aria-hidden="true" class="sui-icon-chevron-down"></i>
				<button type="button"
				        class="sui-screen-reader-text"><?php esc_html_e( 'Expand', 'wds' ); ?></button>
				</span>
			</div>
		</div>

		<div class="sui-accordion-item-body wds-check-item-content">
			<p>
				<small><?php esc_html_e( "Here's a breakdown of where you can make improvements.", 'wds' ); ?></small>
			</p>

			<div class="sui-box">
				<table class="sui-table">
					<thead>
					<tr>
						<th><?php esc_html_e( 'Post Type', 'wds' ); ?></th>
						<th><?php esc_html_e( 'Difficult', 'wds' ); ?></th>
						<th><?php esc_html_e( 'Okay', 'wds' ); ?></th>
						<th><?php esc_html_e( 'Easy', 'wds' ); ?></th>
					</tr>
					</thead>
					<?php foreach ( $type_breakdown as $post_type => $type_overview ) : ?>
						<?php
						$difficult = intval( smartcrawl_get_array_value( $type_overview, 'error' ) );
						$okay = intval( smartcrawl_get_array_value( $type_overview, 'warning' ) );
						$easy = intval( smartcrawl_get_array_value( $type_overview, 'success' ) );
						$post_type_object = get_post_type_object( $post_type );

						$edit_url = admin_url( 'edit.php?wds_readability_threshold=' );
						?>
						<tr>
							<th><?php echo esc_html( $post_type_object->label ); ?></th>
							<td>
								<?php if ( $difficult > 0 ): ?>
									<a href="<?php echo esc_url( add_query_arg( 'post_type', $post_type, "{$edit_url}0" ) ); ?>">
										<span class="wds-readability-difficult sui-tag sui-tag-error">
											<?php echo intval( $difficult ); ?>
										</span>
									</a>
								<?php else: ?>
									<?php esc_html_e( 'None', 'wds' ); ?>
								<?php endif; ?>
							</td>
							<td>
								<?php if ( $okay > 0 ): ?>
									<a href="<?php echo esc_url( add_query_arg( 'post_type', $post_type, "{$edit_url}1" ) ); ?>">
										<span class="wds-readability-okay sui-tag sui-tag-warning">
											<?php echo intval( $okay ); ?>
										</span>
									</a>
								<?php else: ?>
									<?php esc_html_e( 'None', 'wds' ); ?>
								<?php endif; ?>
							</td>
							<td>
								<?php if ( $easy > 0 ): ?>
									<span class="wds-readability-easy sui-tag sui-tag-success">
										<?php echo intval( $easy ); ?>
									</span>
								<?php else: ?>
									<?php esc_html_e( 'None', 'wds' ); ?>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>
		</div>
	</div>
</section>

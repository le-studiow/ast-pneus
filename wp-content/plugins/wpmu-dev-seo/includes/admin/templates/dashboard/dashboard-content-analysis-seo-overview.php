<?php
/**
 * SEO analyis dashboard widget template
 *
 * @package wpmu-dev-seo
 */

$analysis_model = new Smartcrawl_Model_Analysis();
$overview = $analysis_model->get_overall_seo_analysis();

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
} elseif ( $percentage > 60 ) {
	$class = 'wds-check-success sui-success';
	$indicator_class = 'sui-tag-success';
	$indicator = esc_html__( 'Good', 'wds' );
} else {
	$class = 'wds-check-warning sui-warning';
	$indicator_class = 'sui-tag-warning';
	$indicator = esc_html__( 'Poor', 'wds' );
}
?>
<section class="wds-accordion wds-draw-left sui-accordion wds-seo-analysis-overview">
	<div class="sui-accordion-item wds-check-item <?php echo esc_attr( $class ); ?>">
		<div class="sui-accordion-item-header">
			<div class="sui-accordion-item-title sui-accordion-col-6">
				<?php esc_html_e( 'Overall SEO Analysis', 'wds' ); ?>
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
						<th><?php esc_html_e( 'Poor', 'wds' ); ?></th>
						<th><?php esc_html_e( 'Good', 'wds' ); ?></th>
					</tr>
					</thead>
					<?php foreach ( $type_breakdown as $post_type => $type_overview ) : ?>
						<?php
						$total_for_type = intval( smartcrawl_get_array_value( $type_overview, 'total' ) );
						$passed_for_type = intval( smartcrawl_get_array_value( $type_overview, 'passed' ) );
						$failed_for_type = $total_for_type - $passed_for_type;
						$post_type_object = get_post_type_object( $post_type );

						$fail_url = admin_url( "edit.php?post_type={$post_type}&wds_analysis_threshold=99" );
						$success_url = admin_url( "edit.php?post_type={$post_type}&wds_analysis_threshold=100" );
						?>
						<tr>
							<th><?php echo esc_html( $post_type_object->label ); ?></th>
							<td>
								<?php if ( $failed_for_type > 0 ): ?>
									<a href="<?php echo esc_url( $fail_url ); ?>">
										<span class="wds-seo-analysis-poor sui-tag sui-tag-warning"><?php echo intval( $failed_for_type ); ?></span>
									</a>
								<?php else: ?>
									<?php esc_html_e( 'None', 'wds' ); ?>
								<?php endif; ?>
							</td>
							<td>
								<?php if ( $passed_for_type > 0 ): ?>
									<a href="<?php echo esc_url( $success_url ); ?>">
										<span class="wds-seo-analysis-good sui-tag sui-tag-success"><?php echo intval( $passed_for_type ); ?></span>
									</a>
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

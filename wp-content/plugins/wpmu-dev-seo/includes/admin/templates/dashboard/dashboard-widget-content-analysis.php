<?php
$seo_analysis_enabled = Smartcrawl_Settings::get_setting( 'analysis-seo' );
$readability_analysis_enabled = Smartcrawl_Settings::get_setting( 'analysis-readability' );
$option_name = Smartcrawl_Settings::TAB_SETTINGS . '_options';
$is_ajax_request = defined( 'DOING_AJAX' ) && DOING_AJAX;
$refresh_required = ! $is_ajax_request && ( $seo_analysis_enabled || $readability_analysis_enabled );

$classes = array();
if ( $refresh_required ) {
	$classes[] = 'wds-box-refresh-required';
}
if ( $seo_analysis_enabled ) {
	$classes[] = 'wds-seo-analysis-enabled';
}
if ( $readability_analysis_enabled ) {
	$classes[] = 'wds-readability-analysis-enabled';
}
?>

<section id="<?php echo esc_attr( Smartcrawl_Settings_Dashboard::BOX_CONTENT_ANALYSIS ); ?>"
         class="sui-box wds-dashboard-widget <?php echo esc_attr( implode( ' ', $classes ) ); ?>">

	<div class="sui-box-header">
		<h3 class="sui-box-title">
			<i class="sui-icon-magnifying-glass-search"
			   aria-hidden="true"></i> <?php esc_html_e( 'Content Analysis', 'wds' ); ?>
		</h3>
	</div>

	<div class="sui-box-body">
		<p><?php esc_html_e( 'SEO and Readability Analysis recommend improvements to your content to give it the best chance of ranking highly, as well as being easy for average person to read.', 'wds' ); ?></p>

		<div class="wds-report">
			<?php if ( $seo_analysis_enabled ) : ?>
				<?php if ( $is_ajax_request ) : ?>
					<?php $this->_render( 'dashboard/dashboard-content-analysis-seo-overview' ); ?>
				<?php endif; ?>
			<?php else : ?>
				<div class="wds-separator-top wds-draw-left-padded <?php echo ! $readability_analysis_enabled ? 'wds-separator-bottom' : ''; ?>">
					<small><strong><?php esc_html_e( 'SEO Analysis', 'wds' ); ?></strong></small>
					<p>
						<small><?php esc_html_e( 'Analyses your content against recommend SEO practice and gives recommendations for improvement to make sure content is as optimized as possible.', 'wds' ); ?></small>
					</p>
					<button type="button"
					        id="wds-activate-analysis-seo"
					        data-option-id="<?php echo esc_attr( $option_name ); ?>"
					        data-flag="analysis-seo"
					        class="wds-activate-component sui-button sui-button-blue wds-disabled-during-request">

						<span class="sui-loading-text"><?php esc_html_e( 'Activate', 'wds' ); ?></span>
						<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
					</button>
				</div>
			<?php endif; ?>

			<?php if ( $readability_analysis_enabled ) : ?>
				<?php if ( $is_ajax_request ) : ?>
					<?php $this->_render( 'dashboard/dashboard-content-analysis-readability-overview' ); ?>
				<?php endif; ?>
			<?php else : ?>
				<div>
					<small><strong><?php esc_html_e( 'Readability Analysis', 'wds' ); ?></strong></small>
					<p>
						<small><?php esc_html_e( 'Benchmarks the readability of your content for the average visitor and gives recommendations for improvement.', 'wds' ); ?></small>
					</p>
					<button type="button"
					        id="wds-activate-analysis-readability"
					        data-option-id="<?php echo esc_attr( $option_name ); ?>"
					        data-flag="analysis-readability"
					        class="wds-activate-component sui-button sui-button-blue wds-disabled-during-request">

						<span class="sui-loading-text"><?php esc_html_e( 'Activate', 'wds' ); ?></span>
						<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
					</button>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<div class="<?php echo $readability_analysis_enabled ? 'sui-box-body' : 'sui-box-footer'; ?>">
		<a href="<?php echo esc_attr( admin_url( 'edit.php' ) ); ?>"
		   class="sui-button sui-button-ghost">

			<i class="sui-icon-pencil"
			   aria-hidden="true"></i> <?php esc_html_e( 'Edit Posts', 'wds' ); ?>
		</a>
	</div>
</section>

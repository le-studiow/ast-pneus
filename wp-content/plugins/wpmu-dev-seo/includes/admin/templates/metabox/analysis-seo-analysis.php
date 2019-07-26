<?php
$checks = empty( $checks ) ? array() : $checks;
$error_count = empty( $error_count ) ? 0 : $error_count;
$focus_keywords_available = empty( $focus_keywords_available ) ? false : $focus_keywords_available;
$pending_recommendations_message = _n(
	'You have %d SEO recommendation. We recommend you satisfy as many improvements as possible to ensure your content gets found.',
	'You have %d SEO recommendations. We recommend you satisfy as many improvements as possible to ensure your content gets found.',
	$error_count,
	'wds'
);
?>

<?php if ( ! $focus_keywords_available ) : ?>
	<div class="wds-seo-analysis wds-no-focus-keywords" data-errors="-1">
		<?php $this->_render( 'notice', array(
			'message' => esc_html__( 'You need to add focus keywords to see recommendations for this article.', 'wds' ),
			'class'   => 'sui-notice-inactive',
		) ); ?>
	</div>
	<?php return; ?>
<?php endif; // phpcs:ignore -- PHPCS misfires here and complains about the return statement above ?>

<div class="wds-seo-analysis wds-report" data-errors="<?php echo esc_attr( $error_count ); ?>">

	<?php $this->_render( 'notice', array(
		'message' => esc_html__( 'Analyzing content, please wait a few moments', 'wds' ),
		'class'   => 'wds-analysis-working',
	) ); ?>

	<div class="wds-report-inner">
		<?php $this->_render( 'notice', array(
			'message' => $error_count > 0
				? sprintf( $pending_recommendations_message, intval( $error_count ) )
				: esc_html__( 'All SEO recommendations are met. Your content is as optimized as possible - nice work!', 'wds' ),
			'class'   => $error_count > 0 ? 'sui-notice-warning' : 'sui-notice-success',
		) ); ?>
		<div class="wds-accordion sui-accordion">
			<?php foreach ( $checks as $check_id => $result ) : ?>
				<?php
				$passed = $result['status'];
				$ignored = $result['ignored'];
				$recommendation = $result['recommendation'];
				$more_info = $result['more_info'];
				$status_msg = $result['status_msg'];

				$classes_array = array();
				if ( $ignored ) {
					$classes_array[] = 'wds-check-invalid disabled';
					$icon_class = 'sui-icon-info';
				} else {
					$state_class = $passed ? 'sui-success' : 'sui-warning';
					$icon_class = $passed
						? $state_class . ' sui-icon-check-tick'
						: $state_class . ' sui-icon-info';
					$classes_array[] = $state_class;
					$classes_array[] = $passed ? 'wds-check-success' : 'wds-check-warning';
				}
				$classes = implode( ' ', $classes_array );
				?>
				<div id="wds-check-<?php echo esc_attr( $check_id ); ?>"
				     class="wds-check-item sui-accordion-item <?php echo esc_attr( $classes ); ?>">
					<div class="<?php echo $ignored ? 'wds-ignored-item-header' : 'sui-accordion-item-header'; ?>">
						<div class="sui-accordion-item-title sui-accordion-col-8">
							<i aria-hidden="true" class="<?php echo esc_attr( $icon_class ); ?>"></i>
							<?php echo wp_kses_post( $status_msg ); ?>
						</div>
						<?php if ( $ignored ) : ?>
							<div class="sui-accordion-col-4">
								<button type="button"
								        id="wds-unignore-check-<?php echo esc_attr( $check_id ); ?>"
								        class="wds-unignore wds-disabled-during-request sui-button sui-button-ghost"
								        data-check_id="<?php echo esc_attr( $check_id ); ?>">
									<span class="sui-loading-text">
										<i class="sui-icon-undo" aria-hidden="true"></i>

										<?php esc_html_e( 'Restore', 'wds' ); ?>
									</span>

									<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
								</button>
							</div>
						<?php else : ?>
							<div class="sui-accordion-col-4">
								<button class="sui-button-icon sui-accordion-open-indicator"
								        type="button"
								        aria-label="<?php esc_html_e( 'Open item', 'wds' ); ?>">
									<i class="sui-icon-chevron-down" aria-hidden="true"></i>
								</button>
							</div>
						<?php endif; ?>
					</div>
					<div class="sui-accordion-item-body wds-check-item-content">
						<div class="sui-box">
							<div class="sui-box-body">

								<?php if ( $recommendation ) : ?>
									<div class="wds-recommendation">
										<div>
											<strong><?php esc_html_e( 'Recommendation', 'wds' ); ?></strong>
										</div>
										<p><?php echo wp_kses_post( $recommendation ); ?></p>
									</div>
								<?php endif; ?>

								<?php if ( $more_info ) : ?>
									<div class="wds-more-info">
										<div>
											<strong><?php esc_html_e( 'More Info', 'wds' ); ?></strong>
										</div>
										<p><?php echo wp_kses_post( $more_info ); ?></p>
									</div>
								<?php endif; ?>

								<?php if ( ! $ignored ) : ?>
									<div class="wds-ignore-container">
										<button type="button"
										        id="wds-ignore-check-<?php echo esc_attr( $check_id ); ?>"
										        class="wds-ignore wds-disabled-during-request sui-button sui-button-ghost"
										        data-check_id="<?php echo esc_attr( $check_id ); ?>">
											<span class="sui-loading-text">
												<i class="sui-icon-eye-hide" aria-hidden="true"></i>

												<?php esc_html_e( 'Ignore', 'wds' ); ?>
											</span>

											<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
										</button>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="cf"></div>
	</div>
</div>

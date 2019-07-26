<?php
$results = empty( $results ) ? array() : $results;
?>

<?php if ( ! empty( $results['error'] ) ) { ?>
	<?php
	// We have encountered an error. So let's show that.
	$this->_render( 'notice', array(
		'message' => esc_html( $results['error'] ),
		'class'   => 'sui-notice-error',
	) );
	?>
<?php } elseif ( empty( $results['items'] ) ) { ?>
	<?php $this->_render( 'checkup/checkup-no-data' ); ?>
<?php } else { ?>
	<!--
		This is where we store the actual result items.
		Let's iterate through them.
	-->
	<div class="wds-accordion sui-accordion wds-draw-left">
		<?php foreach ( $results['items'] as $idx => $item ) : ?>
			<?php
			$item_id = "wds-checkup-item-{$idx}";
			$type = ! empty( $item['type'] )
				? sanitize_html_class( $item['type'] )
				: '';
			$custom_class = ! empty( $item['class'] )
				? sanitize_html_class( $item['class'] )
				: '';
			$style_class_map = array(
				'ok'       => 'sui-success',
				'info'     => '',
				'warning'  => 'sui-warning',
				'critical' => 'sui-error',
			);
			$style_class = isset( $style_class_map[ $item['type'] ] ) ? $style_class_map[ $item['type'] ] : '';
			$details = ! empty( $item['tooltip'] ) ? $item['tooltip'] : '';
			$title = ! empty( $item['title'] ) ? $item['title'] : '';
			$body = ! empty( $item['body'] ) ? $item['body'] : '';
			$fix = ! empty( $item['fix'] ) ? $item['fix'] : '';
			?>
			<div class="sui-accordion-item wds-check-item <?php echo esc_attr( $type ); ?> <?php echo esc_attr( $style_class ); ?> <?php echo esc_attr( $custom_class ); ?>"
			     id="<?php echo esc_attr( $item_id ); ?>">
				<div class="sui-accordion-item-header">
					<div class="sui-accordion-item-title">
						<i aria-hidden="true"
						   class="<?php echo esc_attr( $style_class ); ?> <?php echo 'ok' === $type ? 'sui-icon-check-tick' : 'sui-icon-warning-alert'; ?>"></i>
						<?php echo esc_html( $title ); ?>
					</div>
					<div>
					<span class="sui-accordion-open-indicator">
						<i aria-hidden="true" class="sui-icon-chevron-down"></i>
						<button type="button"
						        class="sui-screen-reader-text"><?php esc_html_e( 'Expand', 'wds' ); ?></button>
					</span>
					</div>
				</div>
				<div class="sui-accordion-item-body wds-check-item-content <?php echo esc_attr( $type ); ?>">
					<div class="sui-box">
						<div class="sui-box-body">
							<?php if ( $body || $fix ) : ?>
								<div class="wds-recommendation">
									<strong><?php esc_html_e( 'Recommendation', 'wds' ); ?></strong>

									<?php echo wp_kses_post( $body ); ?>
									<p><?php echo wp_kses_post( $fix ); ?></p>
								</div>
							<?php endif; ?>

							<?php if ( $details ) : ?>
								<div class="wds-more-info">
									<strong><?php esc_html_e( 'More Info', 'wds' ); ?></strong>
									<p><?php echo esc_html( $details ); ?></p>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
<?php } ?>

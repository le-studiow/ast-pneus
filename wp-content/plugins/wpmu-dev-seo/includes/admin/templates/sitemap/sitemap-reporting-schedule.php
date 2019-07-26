<?php
$cron = Smartcrawl_Controller_Cron::get();
// This does the actual rescheduling
$cron->set_up_schedule();
$crawler_freq = empty( $_view['options']['crawler-frequency'] ) ? false : $_view['options']['crawler-frequency'];
$option_name = empty( $_view['option_name'] ) ? '' : $_view['option_name'];
?>

<div class="sui-side-tabs sui-tabs">
	<div data-tabs>
		<?php foreach ( $cron->get_frequencies() as $key => $label ) : ?>
			<div data-frequency="<?php echo esc_attr( $key ); ?>"
			     class="<?php echo $key === $crawler_freq ? 'active' : ''; ?>">
				<?php echo esc_html( $label ); ?>
			</div>
		<?php endforeach; ?>
	</div>

	<div data-panes>
		<div class="wds-single-pane">
			<label class="hidden">
				<select class="none-sui wds-conditional-parent"
				        id="wds-crawler-frequency"
				        name="<?php echo esc_attr( $option_name ); ?>[crawler-frequency]">

					<?php foreach ( $cron->get_frequencies() as $key => $label ) : ?>
						<option
								value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $crawler_freq ); ?>>
							<?php echo esc_html( $label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</label>

			<div class="sui-row">
				<div class="sui-col wds-conditional-child"
				     data-parent="wds-crawler-frequency"
				     data-parent-val="weekly,monthly">

					<div class="sui-form-field">
						<?php $this->_render( 'sitemap/sitemap-reporting-dow-select' ); ?>
					</div>
				</div>

				<div class="sui-col">
					<div class="sui-form-field">
						<?php $this->_render( 'sitemap/sitemap-reporting-tod-select' ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
$checkup_freq = empty( $checkup_freq ) ? false : $checkup_freq;
$cron = Smartcrawl_Controller_Cron::get();

// This does the actual rescheduling
$cron->set_up_schedule();
$is_member = empty( $_view['is_member'] ) ? false : true;
$option_name = empty( $_view['option_name'] ) ? '' : $_view['option_name'];
$disabled = $is_member ? '' : 'disabled';
?>
<div class="sui-side-tabs sui-tabs">
	<div data-tabs>
		<?php foreach ( $cron->get_frequencies() as $key => $label ) : ?>
			<div data-frequency="<?php echo esc_attr( $key ); ?>"
			     class="<?php echo $key === $checkup_freq ? 'active' : ''; ?>">
				<?php echo esc_html( $label ); ?>
			</div>
		<?php endforeach; ?>
	</div>

	<div data-panes>
		<div class="wds-single-pane">
			<label style="display: none;">
				<select <?php echo esc_attr( $disabled ); ?>
						class="none-sui wds-conditional-parent"
						id="wds-checkup-frequency"
						data-minimum-results-for-search="-1"
						name="<?php echo esc_attr( $option_name ); ?>[checkup-frequency]">

					<?php foreach ( $cron->get_frequencies() as $key => $label ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $checkup_freq ); ?>>
							<?php echo esc_html( $label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</label>

			<div class="sui-row">
				<div class="sui-col wds-conditional-child"
				     data-parent="wds-checkup-frequency"
				     data-parent-val="weekly,monthly">

					<div class="sui-form-field">
						<?php $this->_render( 'checkup/checkup-reporting-dow-select' ); ?>
					</div>
				</div>

				<div class="sui-col">
					<div class="sui-form-field">
						<?php $this->_render( 'checkup/checkup-reporting-tod-select' ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

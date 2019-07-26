<?php
$progress = empty( $progress ) ? 0 : $progress;
$progress_state = empty( $progress_state ) ? '' : $progress_state;
?>

<div class="wds-progress sui-progress-block" data-progress="<?php echo (int) $progress; ?>">
	<div class="sui-progress">
		<span class="sui-progress-icon" aria-hidden="true">
			<i class="sui-icon-loader sui-loading"></i>
		</span>

		<div class="sui-progress-text">
			<span><?php echo (int) $progress; ?>%</span>
		</div>
		<div class="sui-progress-bar">
			<span style="width:<?php echo (int) $progress; ?>%;"></span>
		</div>
	</div>
</div>
<div class="sui-progress-state">
	<span><?php echo esc_html( $progress_state ); ?></span>
</div>

<?php
$in_progress = empty( $in_progress ) ? false : $in_progress;
?>
<div class="sui-box">
	<div class="sui-box-header">
		<h2 class="sui-box-title"><?php esc_html_e( 'Get Started', 'wds' ); ?></h2>
	</div>
	<div class="sui-box-body">
		<?php $this->_render( 'checkup/checkup-no-data', array(
			'in_progress' => $in_progress,
		) ); ?>
	</div>
</div>

<?php
$service = Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_CHECKUP );
$checkup_url = Smartcrawl_Checkup_Settings::checkup_url();
$in_progress = empty( $in_progress ) ? false : $in_progress;
?>
<a href="<?php echo esc_attr( $checkup_url ); ?>"
   class="sui-button sui-button-blue <?php echo $in_progress ? 'disabled' : ''; ?>">
	<i class="sui-icon-plus" aria-hidden="true"></i>

	<?php esc_html_e( 'Run checkup', 'wds' ); ?>
</a>

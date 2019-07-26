<div class="sui-header">
	<h1 class="sui-header-title"><?php echo esc_html( $title ); ?></h1>
	<div class="sui-actions-right">
		<button class="sui-button" type="button" data-a11y-dialog-show="branda-manage-all-modules"><?php echo esc_html_x( 'Manage All Modules', 'button', 'ub' ); ?></button>
		<button class="sui-button sui-button-ghost" type="button" data-a11y-dialog-show="branda-view-documentation"><span class="sui-loading-text"><i class="sui-icon-academy"> </i><?php esc_html_e( 'View Documentation', 'ub' ); ?></span><i class="sui-icon-loader sui-loading" aria-hidden="true"></i></button>
	</div>
</div>

<div class="sui-dialog sui-dialog-lg" aria-hidden="true" tabindex="-1" id="branda-view-documentation">
	<div class="sui-dialog-overlay" data-a11y-dialog-hide></div>
	<div class="sui-dialog-content" aria-labelledby="dialogTitle" aria-describedby="dialogDescription" role="dialog">
		<div class="sui-box" role="document">
			<div class="sui-box-header">
				<h3 class="sui-box-title" id="dialogTitle"><?php esc_html_e( 'Documentation', 'ub' ); ?></h3>
				<div class="sui-actions-right">
					<button data-a11y-dialog-hide class="sui-dialog-close" aria-label="<?php esc_attr_e( 'Close this dialog window', 'ub' ); ?>"></button>
				</div>
			</div>
			<div class="sui-box-body">
<?php
$this->render( 'admin/dashboard/help' );
$this->render( 'admin/modules/admin-bar/help' );
$this->render( 'admin/modules/admin-help-content/help' );
$this->render( 'admin/modules/admin-menu/help' );
$this->render( 'admin/modules/content-footer/help' );
$this->render( 'admin/modules/custom-admin-css/help' );
$this->render( 'admin/modules/dashboard-widgets/help' );
$this->render( 'admin/modules/images/help' );
$this->render( 'admin/modules/login-screen/help' );
$this->render( 'admin/modules/site-generator/help' );
$this->render( 'admin/modules/text-replacement/help' );
?>
			</div>
		</div>
	</div>
</div>


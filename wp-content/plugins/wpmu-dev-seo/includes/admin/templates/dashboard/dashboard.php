<?php
/**
 * Dashboard root template
 *
 * @package wpmu-dev-seo
 */
?>
<?php $this->_render( 'before-page-container' ); ?>

<div id="container" class="sui-wrap wrap wrap-wds wds-page wds-dashboard">
	<?php $this->_render( 'page-header', array(
		'title'                 => esc_html__( 'Dashboard', 'wds' ),
		'documentation_chapter' => 'dashboard',
	) ); ?>

	<div class="sui-row">
		<div class="sui-col-md-12">
			<?php $this->_render( 'dashboard/dashboard-top' ); ?>
		</div>

		<div class="sui-col">
			<?php
			if ( smartcrawl_can_show_dash_widget_for( Smartcrawl_Settings_Settings::TAB_CHECKUP ) ) {
				$this->_render( 'dashboard/dashboard-widget-seo-checkup' );
			}
			?>
			<?php $this->_render( 'dashboard/dashboard-widget-content-analysis' ); ?>
			<?php
			if ( smartcrawl_can_show_dash_widget_for( Smartcrawl_Settings_Settings::TAB_SOCIAL ) ) {
				$this->_render( 'dashboard/dashboard-widget-social' );
			}
			?>
		</div>

		<div class="sui-col">
			<?php
			if ( smartcrawl_can_show_dash_widget_for( Smartcrawl_Settings_Settings::TAB_ONPAGE ) ) {
				$this->_render( 'dashboard/dashboard-widget-onpage' );
			}
			?>
			<?php
			if ( smartcrawl_can_show_dash_widget_for( Smartcrawl_Settings_Settings::TAB_SITEMAP ) ) {
				$this->_render( 'dashboard/dashboard-widget-sitemap' );
			}
			?>
			<?php
			if ( smartcrawl_can_show_dash_widget_for( Smartcrawl_Settings_Settings::TAB_AUTOLINKS ) ) {
				$this->_render( 'dashboard/dashboard-widget-advanced-tools' );
			}
			?>
		</div>
	</div>

	<?php $this->_render( 'upsell-modal' ); ?>
	<?php do_action( 'wds-dshboard-after_settings' ); ?>

	<?php $this->_render( 'dashboard/dashboard-cross-sell-footer' ); ?>
	<?php $this->_render( 'footer' ); ?>
</div>

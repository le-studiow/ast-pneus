<?php
$title = empty( $title ) ? '' : $title;
$documentation_chapter = empty( $documentation_chapter ) ? '' : $documentation_chapter;
$left_actions = empty( $left_actions ) ? '' : $left_actions;
$left_actions_args = empty( $left_actions_args ) ? array() : $left_actions_args;
$extra_actions = empty( $extra_actions ) ? '' : $extra_actions;
$extra_actions_args = empty( $extra_actions_args ) ? array() : $extra_actions_args;
?>
<div class="sui-header">
	<h1 class="sui-header-title"><?php echo esc_html( $title ); ?></h1>

	<?php if ( $left_actions ): ?>
		<div class="sui-actions-left">
			<?php $this->_render( $left_actions, $left_actions_args ); ?>
		</div>
	<?php endif; ?>

	<?php if ( $documentation_chapter ): ?>
		<div class="sui-actions-right">
			<?php $this->_render( $extra_actions, $extra_actions_args ); ?>

			<a target="_blank" class="sui-button sui-button-ghost wds-docs-button"
			   href="https://premium.wpmudev.org/docs/wpmu-dev-plugins/smartcrawl/#<?php echo esc_attr( $documentation_chapter ); ?>">
				<i class="sui-icon-academy"></i>
				<?php esc_html_e( 'View Documentation', 'wds' ); ?>
			</a>
		</div>
	<?php endif; ?>
</div>

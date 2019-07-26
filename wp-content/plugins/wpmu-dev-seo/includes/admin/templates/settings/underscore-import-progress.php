<p><?php printf( esc_html__( 'Importing your %s settings, please keep this window open …', 'wds' ), '{{- plugin_name }}' ); ?></p>
<?php if ( is_multisite() ): ?>
	<div class="wds-site-progress">
		<label class="sui-label"><?php esc_html_e( 'Overall Progress', 'wds' ); ?></label>
		<?php $this->_render( 'progress-bar', array(
			'progress' => 0,
		) ); ?>
	</div>
<?php endif; ?>
<div class="wds-post-progress">
	<?php if ( is_multisite() ): ?>
		<label class="sui-label"><?php esc_html_e( 'Current Subsite', 'wds' ); ?></label>
	<?php endif; ?>
	<?php $this->_render( 'progress-bar', array(
		'progress' => 0,
	) ); ?>
</div>

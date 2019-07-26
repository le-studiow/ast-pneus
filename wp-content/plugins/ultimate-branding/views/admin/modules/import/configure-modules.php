<div class="sui-header">
    <?php $this->render( 'admin/modules/import/header' ); ?>
	<div class="sui-notice sui-notice-warning">
		<p><?php esc_html_e( 'This will override the existing configurations of each selected module.', 'ub' ); ?></p>
	</div>
</div>
<form method="post" action="<?php echo esc_url( $action ); ?>">
	<section id="sui-branda-content" class="sui-container branda-avoid-flag">
		<input type="hidden" name="page" value="branding_group_data"/>
		<input type="hidden" name="module" value="import"/>
		<input type="hidden" name="step" value="confirm"/>
		<input type="hidden" name="key" value="<?php echo esc_attr( $key ); ?>"/>
		<?php wp_nonce_field( 'branda-import-save-confirm' ); ?>
		<div class="sui-row branda-import-configure-modules-row">
			<?php
			foreach ( $groups as $group_key => $group ) {
				if ( 'data' === $group_key ) {
					continue;
				}
				if ( ! array_key_exists( $group_key, $modules ) ) {
					continue;
				}
				echo '<div class="sui-col">';
				printf( '<div class="sui-label"">%s</div>', $group['title'] );
				$id = sprintf( 'branda-%s-%s', $group_key, 'all' );
				if ( 2 < count( $modules[ $group_key ] ) ) {
					printf( '<label for="%s" class="sui-checkbox sui-checkbox-stacked">', esc_attr( $id ) );
					printf(
						'<input type="checkbox" class="branda-group-checkbox" id="%s" />',
						esc_html( $id )
					);
					echo '<span aria-hidden="true"></span>';
					printf( '<span>%s</span>', esc_html__( 'All', 'ub' ) );
					echo '</label>';
				}
				foreach ( $modules[ $group_key ]['modules'] as $key => $module ) {
					$id = sprintf( 'branda-%s-%s', $group_key, $key );
					printf( '<label for="%s" class="sui-checkbox sui-checkbox-stacked">', esc_attr( $id ) );
					printf(
						'<input type="checkbox" id="%s" name="modules[%s]"%s />',
						esc_attr( $id ),
						esc_attr( $module['module'] ),
						isset( $module['status'] ) && 'active' === $module['status'] ? ' checked="checked"' : ''
					);
					echo '<span aria-hidden="true"></span>';
					printf( '<span>%s</span>', esc_html( $module['name'] ) );
					echo '</label>';
				}
				echo '</div>';
			}
			?>
	</section>
	<div class="sui-footer">
		<a href="<?php echo $cancel_url; ?>" class="sui-button sui-button-ghost"><?php echo esc_html_x( 'Cancel', 'button', 'ub' ); ?></a>
		<button class="sui-button sui-button-blue" type="submit">
			<span class="sui-loading-text"><?php esc_html_e( 'Configure', 'ub' ); ?></span><i class="sui-icon-loader sui-loading" aria-hidden="true"> </i>
		</button>
	</div>
</form>

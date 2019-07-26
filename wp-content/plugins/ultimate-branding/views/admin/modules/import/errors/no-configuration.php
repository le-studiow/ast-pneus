<div class="sui-header branda-import-error">
	<?php $this->render( 'admin/modules/import/header' ); ?>
	<div class="sui-notice sui-notice-error">
		<p><?php
		printf(
			esc_html__( 'The file %s you are trying to import doesn’t have any module configurations. Please check your file or upload another file.', 'ub' ),
			sprintf(
				'<b>%s</b>',
				esc_html( $filename )
			)
		);
?></p>
	</div>
</div>
<?php $this->render( 'admin/modules/import/errors/footer', array( 'cancel_url' => $cancel_url ) ); ?>

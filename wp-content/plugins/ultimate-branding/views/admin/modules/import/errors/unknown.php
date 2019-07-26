<div class="sui-header branda-import-error">
	<?php $this->render( 'admin/modules/import/header' ); ?>
	<div class="sui-notice sui-notice-error">
		<p><?php
		printf(
			esc_html__( 'During uploading %s an unknown error occurred. Please try again.', 'ub' ),
			sprintf(
				'<b>%s</b>',
				esc_html( $filename )
			)
		);
?></p>
	</div>
</div>
<?php $this->render( 'admin/modules/import/errors/footer', array( 'cancel_url' => $cancel_url ) ); ?>

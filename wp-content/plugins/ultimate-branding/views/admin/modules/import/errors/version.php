<div class="sui-header branda-import-error">
	<h1 class="sui-header-title"><?php esc_html_e( 'Import Error', 'ub' ); ?></h1>
	<div class="sui-notice sui-notice-error">
		<p><?php
		printf(
			esc_html__( 'The file you are trying to import is for %s version %s. Please upgrade source site to Branda and export again.', 'ub' ),
			sprintf(
				'<b>%s</b>',
				esc_html( $product )
			),
			sprintf(
				'<b>%s</b>',
				esc_html( $version )
			)
		);
?></p>
	</div>
</div>
<?php $this->render( 'admin/modules/import/errors/footer', array( 'cancel_url' => $cancel_url ) ); ?>

<?php $progress = empty( $progress ) ? 0 : $progress; ?>

<div class="wds-crawl-results-report wds-report">
	<p><?php esc_html_e( "We're looking for issues with your sitemap, please wait…", 'wds' ); ?></p>
	<div class="wds-url-crawler-progress">
		<?php
		$this->_render( 'progress-bar', array(
			'progress'       => $progress,
			'progress_state' => esc_html__( 'Crawling website...', 'wds' ),
		) );
		?>
		<?php $this->_render( 'progress-notice' ); ?>
	</div>
</div>

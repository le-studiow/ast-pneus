<?php
$last_update_date = empty( $last_update_date ) ? '' : $last_update_date;
$last_update_time = empty( $last_update_time ) ? '' : $last_update_time;
$last_update_timestamp = empty( $last_update_timestamp ) ? '' : $last_update_timestamp;
$se_notifications_enabled = empty( $se_notifications_enabled ) ? '' : $se_notifications_enabled;
$engines = empty( $engines ) ? array() : $engines;
$sitemap_stats = empty( $sitemap_stats ) ? array() : $sitemap_stats;
?>

<div class="wpmud wds-sitemaps-widget">
	<div class="wds-sitemaps-widget-left">
		<div>
			<?php
			printf(
				esc_html__( 'Your sitemap contains %s.', 'wds' ),
				sprintf(
					'<a href="%1$s" target="_blank"><b>%2$d</b> %3$s</a>',
					esc_attr( smartcrawl_get_sitemap_url() ),
					(int) smartcrawl_get_array_value( $sitemap_stats, 'items' ),
					esc_html__( 'items' )
				)
			);
			?>
		</div>
		<p>
			<?php echo esc_html( $last_update_timestamp ); ?>
		</p>
		<p>
			<a href='#update_sitemap' id='wds_update_now'><?php echo esc_html__( 'Update sitemap now', 'wds' ); ?></a>
		</p>
	</div>

	<div class="wds-sitemaps-widget-right">
		<?php if ( $engines ) { ?>
			<ul>
				<?php
				foreach ( $engines as $key => $engine ) {
					$service_name = ucfirst( $key );
					$engine_date = ! empty( $engine['time'] ) ? date_i18n( get_option( 'date_format' ), $engine['time'] ) : false;
					$engine_time = ! empty( $engine['time'] ) ? date_i18n( get_option( 'time_format' ), $engine['time'] ) : false;
					$engine_timestamp = ( $engine_date && $engine_time ) ? sprintf( __( 'Last notified on %1$s, at %2$s.', 'wds' ), $engine_date, $engine_time ) : __( 'Not notified', 'wds' );
					?>
					<li>
						<b><?php echo esc_html( $service_name ); ?>:</b> <?php echo esc_html( $engine_timestamp ); ?>
					</li>
				<?php } ?>

			</ul>

		<?php } else { ?>
			<div><?php esc_html_e( "Search engines haven't been recently updated.", 'wds' ); ?></div>
		<?php } ?>

		<?php if ( $se_notifications_enabled ): ?>
			<p>
				<a href='#update_search_engines' id='wds_update_engines'>
					<?php echo esc_html__( 'Force search engines notification', 'wds' ); ?>
				</a>
			</p>
		<?php else: ?>
			<p><?php esc_html_e( 'Search engine notifications are disabled. You can enable them in the sitemap settings.', 'wds' ); ?></p>
		<?php endif; ?>
	</div>
</div>

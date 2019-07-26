<?php
$smartcrawl_options = Smartcrawl_Settings::get_options();
$showing_default = ! isset( $link ) && ! isset( $title ) && ! isset( $description );
$link = ! isset( $link ) ? home_url() : $link;
$title = ! isset( $title ) ? get_bloginfo( 'name' ) : $title;
$description = ! isset( $description ) ? get_bloginfo( 'description' ) : $description;
?>
<div class="wds-preview-container" data-showing-default="<?php echo empty( $showing_default ) ? 'false' : 'true'; ?>">
	<div class="wds-preview">
		<div class="wds-preview-title">
			<h3>
				<a href="<?php echo esc_url( $link ); ?>">
					<?php echo esc_html( smartcrawl_truncate_meta_title( $title ) ); ?>
				</a>
			</h3>
		</div>
		<div class="wds-preview-url">
			<a href="<?php echo esc_url( $link ); ?>">
				<?php echo esc_url( $link ); ?>
			</a>
		</div>
		<div class="wds-preview-meta">
			<?php echo esc_html( smartcrawl_truncate_meta_description( $description ) ); ?>
		</div>
	</div>
	<p class="wds-preview-description"><?php esc_html_e( 'A preview of how your title and meta will appear in Google Search.', 'wds' ); ?></p>
</div>

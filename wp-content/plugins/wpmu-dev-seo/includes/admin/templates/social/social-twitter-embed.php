<?php
$tweet_url = empty( $tweet_url ) ? '' : $tweet_url;
$embed_query = new WP_Query( array(
	'posts_per_page' => 1,
	'post_status'    => 'any',
) );
$large = empty( $large ) ? false : $large;

if ( ! $tweet_url ) {
	return;
}
?>
<?php if ( $embed_query->have_posts() ) : ?>
	<div class="wds-twitter-embed <?php echo $large ? 'wds-twitter-embed-large' : ''; ?>">
		<?php while ( $embed_query->have_posts() ) : ?>
			<?php
			$embed_query->the_post();
			global $wp_embed;
			/**
			 * @var WP_Embed $wp_embed
			 */
			echo $wp_embed->autoembed( $tweet_url ); // phpcs:ignore -- The embed won't work if escaped
			?>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
	</div>
<?php endif; ?>

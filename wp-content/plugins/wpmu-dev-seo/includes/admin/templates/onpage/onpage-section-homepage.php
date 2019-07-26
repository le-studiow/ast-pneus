<?php
$homepage_title = empty( $homepage_title ) ? '' : $homepage_title;
$homepage_description = empty( $homepage_description ) ? '' : $homepage_description;
$show_homepage_options = empty( $show_homepage_options ) ? '' : $show_homepage_options;
$meta_robots_main_blog_archive = empty( $meta_robots_main_blog_archive ) ? '' : $meta_robots_main_blog_archive;
?>

<?php $this->_render( 'onpage/onpage-preview' ); ?>

<?php if ( $show_homepage_options ) : ?>

	<?php
	$this->_render( 'onpage/onpage-general-settings', array(
		'title_key'        => 'title-home',
		'title_label_desc' => esc_html__( 'Define the main title of your website that Google will index.', 'wds' ),
		'title_field_desc' => esc_html__( 'This is generally your brand name, sometimes with a tagline.', 'wds' ),

		'description_key' => 'metadesc-home',
		'meta_label_desc' => esc_html__( 'Set the default description that will accompany your SEO title in search engine results.', 'wds' ),
		'meta_field_desc' => esc_html__( 'Remember to keep it simple, to the point, and include a bit about what your website can offer potential visitors.', 'wds' ),

		'keywords_key' => 'keywords-home',
	) );

	$this->_render( 'onpage/onpage-og-twitter', array(
		'for_type'          => 'home',
		'social_label_desc' => esc_html__( 'Enable or disable support for social platforms when your homepage is shared on them.', 'wds' ),
	) );

	$this->_render( 'onpage/onpage-meta-robots', array(
		'items' => $meta_robots_main_blog_archive,
	) );
	?>

<?php else : ?>

	<?php
	$front_page = (int) get_option( 'page_on_front' );
	?>
	<div class="wds-notice sui-notice">
		<p>
			<?php
			esc_html_e( 'Your homepage is set to a static page. Configure your homepage SEO via the page itself.', 'wds' );
			if ( $front_page ) {
				?>
				<br/>
				<a type="button"
				   href="<?php echo esc_attr( get_edit_post_link( $front_page ) ); ?>"
				   class="sui-button">
					<?php esc_html_e( 'Go To Homepage', 'wds' ); ?></a>
				<?php
			}
			?>
		</p>
	</div>
<?php endif; ?>

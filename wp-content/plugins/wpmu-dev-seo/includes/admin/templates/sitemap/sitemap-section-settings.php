<?php
/**
 * Sitemaps admin page, Sitemap vertical tab
 *
 * @package wpmu-dev-seo
 */

$post_types = empty( $post_types ) ? array() : $post_types;
$taxonomies = empty( $taxonomies ) ? array() : $taxonomies;
$smartcrawl_buddypress = empty( $smartcrawl_buddypress ) ? array() : $smartcrawl_buddypress;
$option_name = empty( $_view['option_name'] ) ? '' : $_view['option_name'];
$extra_urls = empty( $extra_urls ) ? '' : $extra_urls;
$ignore_urls = empty( $ignore_urls ) ? '' : $ignore_urls;
$ignore_post_ids = empty( $ignore_post_ids ) ? '' : $ignore_post_ids;
?>

<?php if ( Smartcrawl_Xml_Sitemap::is_sitemap_path_writable() ) {
	$this->_render( 'notice', array(
		'message' => sprintf(
			esc_html__( 'Your sitemap is available at %s', 'wds' ),
			sprintf( '<a target="_blank" href="%s">/sitemap.xml</a>', esc_attr( smartcrawl_get_sitemap_url() ) )
		),
		'class'   => 'sui-notice-info',
	) );
} else {
	$this->_render( 'notice', array(
		'message' => sprintf(
			esc_html__( 'Unable to write to sitemap file: %s', 'wds' ),
			sprintf( '<code>%s</code>', esc_html( smartcrawl_get_sitemap_path() ) )
		),
		'class'   => 'sui-notice-error',
	) );
} ?>

<div class="sui-box-settings-row">
	<div class="sui-box-settings-col-1">
		<label class="sui-settings-label"><?php esc_html_e( 'Include', 'wds' ); ?></label>
		<p class="sui-description">
			<?php esc_html_e( 'Choose which post types, archives and taxonomies you wish to include in your sitemap.', 'wds' ); ?>
		</p>
	</div>
	<div class="sui-box-settings-col-2">
		<table class="sui-table wds-sitemap-parts">
			<thead>
			<tr>
				<th><?php esc_html_e( 'Name', 'wds' ); ?></th>
				<th colspan="2"><?php esc_html_e( 'Identifier', 'wds' ); ?></th>
			</tr>
			</thead>

			<tbody>
			<?php foreach ( $post_types as $item => $post_type ) : ?>
				<?php
				$this->_render( 'sitemap/sitemap-part', array(
					'item'        => $item,
					'item_name'   => $post_type->name,
					'item_label'  => $post_type->label,
					'inverted'    => true,
					'option_name' => $option_name . '[exclude_post_types][]',
				) );
				?>
			<?php endforeach; ?>

			<?php foreach ( $taxonomies as $item => $taxonomy ) : ?>
				<?php
				$this->_render( 'sitemap/sitemap-part', array(
					'item'        => $item,
					'item_name'   => $taxonomy->name,
					'item_label'  => $taxonomy->label,
					'inverted'    => true,
					'option_name' => $option_name . '[exclude_taxonomies][]',
				) );
				?>
			<?php endforeach; ?>

			<?php
			if ( $smartcrawl_buddypress ) {
				$this->_render( 'sitemap/sitemap-buddypress-settings', $smartcrawl_buddypress );
			}
			?>
			</tbody>
		</table>
	</div>
</div>

<div class="sui-box-settings-row">
	<div class="sui-box-settings-col-1">
		<label for="extra-sitemap-urls"
		       class="sui-settings-label"><?php esc_html_e( 'Extra URLs', 'wds' ); ?></label>
		<p class="sui-description">
			<?php esc_html_e( "Enter any additional URLs that aren't part of your default pages, posts or custom post types.", 'wds' ); ?>
		</p>
	</div>

	<div class="sui-box-settings-col-2">
			<textarea id="extra-sitemap-urls"
			          class="sui-form-control"
			          name="<?php echo esc_attr( $option_name ); ?>[extra_sitemap_urls]"><?php echo esc_textarea( $extra_urls ); ?></textarea>
		<p class="sui-description">
			<?php esc_html_e( 'Enter one URL per line', 'wds' ); ?>
		</p>
	</div>
</div>

<div class="sui-box-settings-row">
	<div class="sui-box-settings-col-1">
		<label for="<?php echo esc_attr( $option_name ); ?>[sitemap_ignore_urls]"
		       class="sui-settings-label"><?php esc_html_e( 'Exclusions', 'wds' ); ?></label>
		<p class="sui-description">
			<?php esc_html_e( 'If you have custom URLs you want explicitly excluded from your Sitemap you can do this here.', 'wds' ); ?>
		</p>
	</div>

	<div class="sui-box-settings-col-2">
		<div>
			<label for="<?php echo esc_attr( $option_name ); ?>[sitemap_ignore_post_ids]"
			       class="sui-settings-label"><?php esc_html_e( 'Posts', 'wds' ); ?></label>
			<p class="sui-description">
				<?php esc_html_e( 'Enter any particular post IDs you wish to exclude from your sitemap. Note, you can also exclude posts and pages from the post editor page.', 'wds' ); ?>
			</p>
			<input type="text" id="<?php echo esc_attr( $option_name ); ?>[sitemap_ignore_post_ids]"
			       placeholder="<?php echo esc_attr__( 'e.g. 1,5,6,99', 'wds' ); ?>"
			       name="<?php echo esc_attr( $option_name ); ?>[sitemap_ignore_post_ids]"
			       class="sui-form-control"
			       value="<?php echo esc_attr( $ignore_post_ids ); ?>"/>
			<p class="sui-description">
				<?php esc_html_e( 'Enter post IDs separated by commas.', 'wds' ); ?>
			</p>
		</div>

		<div class="wds-separator-top">
			<label for="<?php echo esc_attr( $option_name ); ?>[sitemap_ignore_urls]"
			       class="sui-settings-label"><?php esc_html_e( 'Custom URLs', 'wds' ); ?></label>
			<p class="sui-description">
				<?php esc_html_e( 'Enter any custom URLs you want excluded permanently from the sitemap.', 'wds' ); ?>
			</p>

			<textarea id="<?php echo esc_attr( $option_name ); ?>[sitemap_ignore_urls]"
			          class="sui-form-control"
			          placeholder="<?php echo esc_attr__( 'e.g. /excluded-url', 'wds' ); ?>"
			          name="<?php echo esc_attr( $option_name ); ?>[sitemap_ignore_urls]"><?php echo esc_textarea( $ignore_urls ); ?></textarea>
			<p class="sui-description">
				<?php esc_html_e( 'Enter one URL per line', 'wds' ); ?>
			</p>
		</div>

	</div>
</div>

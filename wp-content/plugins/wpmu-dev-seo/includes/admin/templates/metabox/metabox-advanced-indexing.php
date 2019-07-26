<?php
$robots_index_value = empty( $robots_index_value ) ? false : true;
$robots_follow_value = empty( $robots_follow_value ) ? false : true;
$robots_noindex_value = empty( $robots_noindex_value ) ? false : $robots_noindex_value;
$robots_nofollow_value = empty( $robots_nofollow_value ) ? false : $robots_nofollow_value;
$advanced_value = empty( $advanced_value ) ? array() : $advanced_value;
$post_type_noindexed = empty( $post_type_noindexed ) ? false : true;
$post_type_nofollowed = empty( $post_type_nofollowed ) ? false : true;
?>

<?php if ( apply_filters( 'wds-metabox-visible_parts-robots_area', true ) ) : ?>
	<div class="sui-box-settings-row">
		<div class="sui-box-settings-col-1">
			<label class="sui-settings-label"><?php esc_html_e( 'Indexing', 'wds' ); ?></label>
			<p class="sui-description">
				<?php esc_html_e( 'Choose how search engines will index this particular page.', 'wds' ); ?>
			</p>
		</div>
		<div class="sui-box-settings-col-2">
			<?php
			if ( $post_type_noindexed ) {
				$this->_render( 'toggle-item', array(
					'field_name'       => 'wds_meta-robots-index',
					'field_id'         => 'wds_meta-robots-index',
					'checked'          => $robots_index_value ? 'checked="checked"' : '',
					'item_label'       => esc_html__( 'Index - Override Post Type Setting', 'wds' ),
					'item_description' => esc_html__( 'Instruct search engines whether or not you want this post to appear in search results.', 'wds' ),
				) );
			} else {
				$this->_render( 'toggle-item', array(
					'inverted'         => true,
					'field_name'       => 'wds_meta-robots-noindex',
					'field_id'         => 'wds_meta-robots-noindex',
					'checked'          => $robots_noindex_value ? 'checked="checked"' : '',
					'item_label'       => esc_html__( 'Index', 'wds' ),
					'item_description' => esc_html__( 'Instruct search engines whether or not you want this post to appear in search results.', 'wds' ),
				) );
			}

			if ( $post_type_nofollowed ) {
				$this->_render( 'toggle-item', array(
					'field_name'       => 'wds_meta-robots-follow',
					'field_id'         => 'wds_meta-robots-follow',
					'checked'          => $robots_follow_value ? 'checked="checked"' : '',
					'item_label'       => esc_html__( 'Follow - Override Post Type Setting', 'wds' ),
					'item_description' => esc_html__( 'Tells search engines whether or not to follow the links on your page and crawl them too.', 'wds' ),
				) );
			} else {
				$this->_render( 'toggle-item', array(
					'inverted'         => true,
					'field_name'       => 'wds_meta-robots-nofollow',
					'field_id'         => 'wds_meta-robots-nofollow',
					'checked'          => $robots_nofollow_value ? 'checked="checked"' : '',
					'item_label'       => esc_html__( 'Follow', 'wds' ),
					'item_description' => esc_html__( 'Tells search engines whether or not to follow the links on your page and crawl them too.', 'wds' ),
				) );
			}

			$this->_render( 'toggle-item', array(
				'inverted'         => true,
				'item_value'       => 'noarchive',
				'field_name'       => 'wds_meta-robots-adv[noarchive]',
				'field_id'         => 'wds_meta-robots-noarchive',
				'checked'          => in_array( 'noarchive', $advanced_value, true ) ? 'checked="checked"' : '',
				'item_label'       => esc_html__( 'Archive', 'wds' ),
				'item_description' => esc_html__( 'Instructs search engines to store a cached version of this page.', 'wds' ),
			) );

			$this->_render( 'toggle-item', array(
				'inverted'         => true,
				'item_value'       => 'nosnippet',
				'field_name'       => 'wds_meta-robots-adv[nosnippet]',
				'field_id'         => 'wds_meta-robots-nosnippet',
				'checked'          => in_array( 'nosnippet', $advanced_value, true ) ? 'checked="checked"' : '',
				'item_label'       => esc_html__( 'Snippet', 'wds' ),
				'item_description' => esc_html__( 'Allows search engines to show a snippet of this page in the search results and prevents them from caching the page.', 'wds' ),
			) );
			?>
		</div>
	</div>
<?php endif; ?>

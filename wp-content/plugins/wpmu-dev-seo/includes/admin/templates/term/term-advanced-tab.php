<?php
$tax_meta = empty( $tax_meta ) ? array() : $tax_meta;
$global_noindex = empty( $global_noindex ) ? false : $global_noindex;
$global_nofollow = empty( $global_nofollow ) ? false : $global_nofollow;
$is_active = empty( $is_active ) ? false : $is_active;
?>
<div class="<?php echo $is_active ? 'active' : ''; ?>">
	<div class="wds-metabox-section wds-advanced-metabox-section sui-box-body">
		<p><?php esc_html_e( 'Configure the advanced settings for this term.', 'wds' ); ?></p>

		<div class="sui-box-settings-row">
			<div class="sui-box-settings-col-1">
				<label class="sui-settings-label"><?php esc_html_e( 'Indexing', 'wds' ); ?></label>
				<p class="sui-description">
					<?php esc_html_e( 'Choose how search engines will index the term archive.', 'wds' ); ?>
				</p>
			</div>
			<div class="sui-box-settings-col-2">
				<?php
				$robots_index_value = (boolean) smartcrawl_get_array_value( $tax_meta, 'wds_override_noindex' );
				$robots_noindex_value = (boolean) smartcrawl_get_array_value( $tax_meta, 'wds_noindex' );
				if ( $global_noindex ) {
					$this->_render( 'toggle-item', array(
						'field_name'       => 'wds_override_noindex',
						'field_id'         => 'wds_override_noindex',
						'checked'          => $robots_index_value ? 'checked="checked"' : '',
						'item_label'       => esc_html__( 'Index - Override Taxonomy Setting', 'wds' ),
						'item_description' => esc_html__( 'Instruct search engines whether or not you want this term archive to appear in search results.', 'wds' ),
					) );
				} else {
					$this->_render( 'toggle-item', array(
						'inverted'         => true,
						'field_name'       => 'wds_noindex',
						'field_id'         => 'wds_noindex',
						'checked'          => $robots_noindex_value ? 'checked="checked"' : '',
						'item_label'       => esc_html__( 'Index', 'wds' ),
						'item_description' => esc_html__( 'Instruct search engines whether or not you want this term archive to appear in search results.', 'wds' ),
					) );
				}

				$robots_follow_value = (boolean) smartcrawl_get_array_value( $tax_meta, 'wds_override_nofollow' );
				$robots_nofollow_value = (boolean) smartcrawl_get_array_value( $tax_meta, 'wds_nofollow' );
				if ( $global_nofollow ) {
					$this->_render( 'toggle-item', array(
						'field_name'       => 'wds_override_nofollow',
						'field_id'         => 'wds_override_nofollow',
						'checked'          => $robots_follow_value ? 'checked="checked"' : '',
						'item_label'       => esc_html__( 'Follow - Override Taxonomy Setting', 'wds' ),
						'item_description' => esc_html__( 'Tells search engines whether or not to follow the links on your term archive and crawl them too.', 'wds' ),
					) );
				} else {
					$this->_render( 'toggle-item', array(
						'inverted'         => true,
						'field_name'       => 'wds_nofollow',
						'field_id'         => 'wds_nofollow',
						'checked'          => $robots_nofollow_value ? 'checked="checked"' : '',
						'item_label'       => esc_html__( 'Follow', 'wds' ),
						'item_description' => esc_html__( 'Tells search engines whether or not to follow the links on your term archive and crawl them too.', 'wds' ),
					) );
				}
				?>
			</div>
		</div>

		<div class="sui-box-settings-row">
			<div class="sui-box-settings-col-1">
				<label for="wds_canonical" class="sui-settings-label"><?php esc_html_e( 'Canonical', 'wds' ); ?></label>
				<p class="sui-description">
					<?php esc_html_e( 'The canonical link is shown on the archive page for this term.', 'wds' ); ?>
				</p>
			</div>
			<div class="sui-box-settings-col-2">
				<input type='text' id='wds_canonical' name='wds_canonical'
				       value='<?php echo esc_attr( smartcrawl_get_array_value( $tax_meta, 'wds_canonical' ) ); ?>'
				       class='wds sui-form-control'/>
				<span class="sui-description">
					<?php esc_html_e( 'Enter the full canonical URL including http:// or https://', 'wds' ); ?>
				</span>
			</div>
		</div>
	</div>
</div>

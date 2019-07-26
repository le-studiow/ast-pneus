<?php
/**
 * Metabox entry point template
 *
 * @package wpmu-dev-seo
 */

$post = empty( $post ) ? null : $post;
$seo_sections = apply_filters( 'wds-sections-metabox-seo', array(), $post );
$readability_sections = apply_filters( 'wds-sections-metabox-readability', array(), $post );
$social_sections = apply_filters( 'wds-sections-metabox-social', array(), $post );
$advanced_sections = apply_filters( 'wds-sections-metabox-advanced', array(), $post );
$is_active = true;
?>
<div class="<?php echo esc_attr( smartcrawl_sui_class() ); ?>">
	<div class="sui-wrap wds-page wrap wrap-wds wds-metabox">
		<div class="sui-tabs">
			<?php wp_nonce_field( 'wds-metabox-nonce', '_wds_nonce' ); ?>
			<?php $this->_render( 'metabox/horizontal-tab-nav', array(
				'seo_sections'         => $seo_sections,
				'readability_sections' => $readability_sections,
				'social_sections'      => $social_sections,
				'advanced_sections'    => $advanced_sections,
			) ); ?>
			<div data-panes>
				<?php
				if ( $seo_sections ) {
					$this->_render( 'metabox/horizontal-tab', array(
						'tab_id'           => 'wds_seo',
						'is_active'        => $is_active,
						'content_template' => 'metabox/metabox-tab-seo',
						'content_args'     => array(
							'seo_sections' => $seo_sections,
						),
					) );
					$is_active = false;
				}

				if ( $readability_sections ) {
					$this->_render( 'metabox/horizontal-tab', array(
						'tab_id'           => 'wds_readability',
						'is_active'        => $is_active,
						'content_template' => 'metabox/metabox-tab-readability',
						'content_args'     => array(
							'readability_sections' => $readability_sections,
						),
					) );
					$is_active = false;
				}

				if ( $social_sections ) {
					$this->_render( 'metabox/horizontal-tab', array(
						'tab_id'           => 'wds_social',
						'is_active'        => $is_active,
						'content_template' => 'metabox/metabox-tab-social',
						'content_args'     => array(
							'social_sections' => $social_sections,
						),
					) );
					$is_active = false;
				}

				if ( $advanced_sections ) {
					$this->_render( 'metabox/horizontal-tab', array(
						'tab_id'           => 'wds_advanced',
						'is_active'        => $is_active,
						'content_template' => 'metabox/metabox-tab-advanced',
						'content_args'     => array(
							'advanced_sections' => $advanced_sections,
						),
					) );
					$is_active = false;
				}
				?>
			</div>
		</div>
	</div>
</div>

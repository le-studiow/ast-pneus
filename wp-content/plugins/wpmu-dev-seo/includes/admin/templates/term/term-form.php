<?php
$tax_meta = empty( $tax_meta ) ? array() : $tax_meta;
$term = empty( $term ) ? null : $term;
$global_noindex = empty( $global_noindex ) ? false : $global_noindex;
$global_nofollow = empty( $global_nofollow ) ? false : $global_nofollow;

$resolver = Smartcrawl_Endpoint_Resolver::resolve();
$resolver->simulate_taxonomy_term( $term );

$all_options = Smartcrawl_Settings::get_options();
$og_setting_enabled = (bool) smartcrawl_get_array_value( $all_options, 'og-enable' );
$og_taxonomy_enabled = (bool) smartcrawl_get_array_value( $all_options, 'og-active-' . $term->taxonomy );
$twitter_setting_enabled = (bool) smartcrawl_get_array_value( $all_options, 'twitter-card-enable' );
$twitter_taxonomy_enabled = (bool) smartcrawl_get_array_value( $all_options, 'twitter-active-' . $term->taxonomy );
$show_social_tab = ( $og_setting_enabled && $og_taxonomy_enabled ) || ( $twitter_setting_enabled && $twitter_taxonomy_enabled );
$show_social_tab = $show_social_tab
                   && Smartcrawl_Settings::get_setting( 'social' )
                   && smartcrawl_is_allowed_tab( Smartcrawl_Settings::TAB_SOCIAL );
$show_onpage_tabs = Smartcrawl_Settings::get_setting( 'onpage' )
                    && smartcrawl_is_allowed_tab( Smartcrawl_Settings::TAB_ONPAGE );
if ( ! $show_social_tab && ! $show_onpage_tabs ) {
	return;
}
?>

<div class="<?php echo esc_attr( smartcrawl_sui_class() ); ?>">
	<div class="sui-wrap wrap wrap-wds wds-page wds-metabox">

		<div class="sui-box">
			<div class="sui-box-header">
				<h2 class="sui-box-title"><?php esc_html_e( 'SmartCrawl', 'wds' ); ?></h2>
			</div>

			<div>

				<div class="sui-tabs">

					<?php
					$this->_render( 'term/term-nav', array(
						'show_onpage_tabs' => $show_onpage_tabs,
						'show_social_tab'  => $show_social_tab,
					) );
					$is_active = true;
					?>

					<div data-panes>

						<?php
						if ( $show_onpage_tabs ) {
							$this->_render( 'term/term-seo-tab', array(
								'is_active' => $is_active,
								'tax_meta'  => $tax_meta,
								'term'      => $term,
							) );
							$is_active = false;
						}

						if ( $show_social_tab ) {
							$this->_render( 'term/term-social-tab', array(
								'is_active'                => $is_active,
								'tax_meta'                 => $tax_meta,
								'term'                     => $term,
								'og_setting_enabled'       => $og_setting_enabled,
								'og_taxonomy_enabled'      => $og_taxonomy_enabled,
								'twitter_setting_enabled'  => $twitter_setting_enabled,
								'twitter_taxonomy_enabled' => $twitter_taxonomy_enabled,
							) );
							$is_active = false;
						}

						if ( $show_onpage_tabs ) {
							$this->_render( 'term/term-advanced-tab', array(
								'is_active'       => $is_active,
								'tax_meta'        => $tax_meta,
								'term'            => $term,
								'global_nofollow' => $global_nofollow,
								'global_noindex'  => $global_noindex,
							) );
							$is_active = false;
						}
						?>
					</div>
				</div>

			</div>
		</div>

	</div>
</div>
<?php $resolver->stop_simulation(); ?>

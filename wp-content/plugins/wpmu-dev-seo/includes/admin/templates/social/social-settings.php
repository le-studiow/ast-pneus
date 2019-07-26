<?php $this->_render( 'before-page-container' ); ?>
<div id="container" class="sui-wrap wrap wrap-wds wds-page wds-page-social">

	<?php $this->_render( 'page-header', array(
		'title'                 => esc_html__( 'Social', 'wds' ),
		'documentation_chapter' => 'social',
	) ); ?>

	<form action='<?php echo esc_attr( $_view['action_url'] ); ?>' method='post' class="wds-form">
		<?php $this->settings_fields( $_view['option_name'] ); ?>

		<div class="wds-vertical-tabs-container sui-row-with-sidenav">
			<?php $this->_render( 'social/social-sidenav', array(
				'active_tab' => $active_tab,
			) ); ?>

			<?php
			$this->_render( 'vertical-tab', array(
				'tab_id'       => 'tab_accounts',
				'tab_name'     => esc_html__( 'Accounts', 'wds' ),
				'is_active'    => 'tab_accounts' === $active_tab,
				'tab_sections' => array(
					array(
						'section_description' => esc_html__( 'Let search engines know whether you’re an organization or a person, then add all your social profiles so search engines know which social profiles to attribute your web content to.', 'wds' ),
						'section_template'    => 'social/social-section-accounts',
						'section_args'        => array(
							'options' => $options,
						),
					),
				),
			) );
			?>

			<?php
			$this->_render( 'vertical-tab', array(
				'tab_id'       => 'tab_open_graph',
				'tab_name'     => esc_html__( 'OpenGraph', 'wds' ),
				'is_active'    => 'tab_open_graph' === $active_tab,
				'tab_sections' => array(
					array(
						'section_description' => esc_html__( 'Add meta data to your pages to make them look great when shared platforms such as Facebook and other popular social networks.', 'wds' ),
						'section_template'    => 'social/social-section-open-graph',
						'section_args'        => array(
							'options' => $options,
						),
					),
				),
			) );
			?>

			<?php
			$this->_render( 'vertical-tab', array(
				'tab_id'       => 'tab_twitter_cards',
				'tab_name'     => esc_html__( 'Twitter Cards', 'wds' ),
				'is_active'    => 'tab_twitter_cards' === $active_tab,
				'tab_sections' => array(
					array(
						'section_description' => esc_html__( 'Add meta data to your pages to make them look great when shared on Twitter.', 'wds' ),
						'section_template'    => 'social/social-section-twitter-cards',
						'section_args'        => array(
							'options' => $options,
						),
					),
				),
			) );
			?>

			<?php
			$this->_render( 'vertical-tab', array(
				'tab_id'       => 'tab_pinterest_verification',
				'tab_name'     => esc_html__( 'Pinterest Verification', 'wds' ),
				'is_active'    => 'tab_pinterest_verification' === $active_tab,
				'tab_sections' => array(
					array(
						'section_description' => esc_html__( 'Verify your website with Pinterest to attribute your website when your website content is pinned to the platform.', 'wds' ),
						'section_template'    => 'social/social-section-pinterest-verification',
						'section_args'        => array(
							'options' => $options,
						),
					),
				),
			) );
			?>
		</div>
	</form>

	<?php $this->_render( 'footer' ); ?>
</div>

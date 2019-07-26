<?php
$options = empty( $options ) ? $_view['options'] : $options;
$card_type = smartcrawl_get_array_value( $options, 'twitter-card-type' );
$card_type_summary = Smartcrawl_Twitter_Printer::CARD_SUMMARY === $card_type;
$card_type_image = empty( $card_type ) // Image card used by default in twitter printer
                   || Smartcrawl_Twitter_Printer::CARD_IMAGE === $card_type;
?>

<div class="sui-box-settings-row wds-separator-top">
	<div class="sui-box-settings-col-1">
		<label class="sui-settings-label"><?php esc_html_e( 'Twitter Cards', 'wds' ); ?></label>
		<p class="sui-description"><?php esc_html_e( 'With Twitter Cards, you can attach rich photos, videos and media experiences to Tweets, helping to drive traffic to your website.', 'wds' ); ?></p>
	</div>

	<?php $twitter_card_enabled = $options['twitter-card-enable']; ?>
	<div class="sui-box-settings-col-2 wds-toggleable <?php echo $twitter_card_enabled ? '' : 'inactive'; ?>">
		<?php
		$this->_render( 'toggle-item', array(
			'item_label' => esc_html__( 'Enable Twitter Cards', 'wds' ),
			'checked'    => checked( true, $twitter_card_enabled, false ),
			'field_name' => $_view['option_name'] . '[twitter-card-enable]',
		) );
		?>

		<div class="wds-toggleable-inside wds-conditional sui-toggle-content">
			<p></p>
			<label style="display: none">
				<select name="<?php echo esc_attr( $_view['option_name'] ); ?>[twitter-card-type]"
				        id="twitter-card-type"
				        class="none-sui">
					<option
						<?php selected( $card_type_summary ); ?>
							value="<?php echo esc_attr( Smartcrawl_Twitter_Printer::CARD_SUMMARY ); ?>">
						<?php esc_html_e( 'Summary Card', 'wds' ); ?>
					</option>

					<option
						<?php selected( $card_type_image ); ?>
							value="<?php echo esc_attr( Smartcrawl_Twitter_Printer::CARD_IMAGE ); ?>">
						<?php esc_html_e( 'Summary Card with Large Image', 'wds' ); ?>
					</option>
				</select>
			</label>

			<div class="sui-side-tabs sui-tabs">
				<div data-tabs>
					<div class="<?php echo $card_type_image ? 'active' : ''; ?>"
					     data-card-type="<?php echo esc_attr( Smartcrawl_Twitter_Printer::CARD_IMAGE ); ?>">
						<?php esc_html_e( 'Image', 'wds' ); ?>
					</div>
					<div class="<?php echo $card_type_summary ? 'active' : ''; ?>"
					     data-card-type="<?php echo esc_attr( Smartcrawl_Twitter_Printer::CARD_SUMMARY ); ?>">
						<?php esc_html_e( 'No Image', 'wds' ); ?>
					</div>
				</div>
			</div>

			<div class="wds-conditional-inside"
			     data-conditional-val="<?php echo esc_attr( Smartcrawl_Twitter_Printer::CARD_SUMMARY ); ?>">
				<?php
				$this->_render( 'social/social-twitter-embed', array(
					'tweet_url' => 'https://twitter.com/WordPress/status/1046731890244374528',
				) );
				?>
			</div>
			<div class="wds-conditional-inside"
			     data-conditional-val="<?php echo esc_attr( Smartcrawl_Twitter_Printer::CARD_IMAGE ); ?>">
				<?php
				$this->_render( 'social/social-twitter-embed', array(
					'tweet_url' => 'https://twitter.com/NatGeo/status/1087380060473049091',
					'large'     => true,
				) );
				?>
			</div>
			<p class="sui-description"><?php esc_html_e( 'A preview of how your Homepage will appear as a Twitter Card.', 'wds' ); ?></p>
		</div>

	</div>
</div>

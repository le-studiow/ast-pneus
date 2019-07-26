<?php
$insert = empty( $insert ) ? array() : $insert;
$linkto = empty( $linkto ) ? array() : $linkto;
$custom_keywords = empty( $_view['options']['customkey'] ) ? '' : $_view['options']['customkey'];

$this->_render( 'advanced-tools/advanced-automatic-linking-types', array(
	'insert' => $insert,
	'linkto' => $linkto,
) );
?>

<div class="sui-box-settings-row">
	<div>
		<div class="sui-form-field">
			<label class="sui-settings-label"><?php esc_html_e( 'Custom Links', 'wds' ); ?></label>
			<p class="sui-description"><?php esc_html_e( 'Choose additional custom keywords you want to target, and where to link them to.', 'wds' ); ?></p>
		</div>

		<div class="box-autolinks-custom-keywords-settings">
			<div class="wds-replaceable">
				<?php // phpcs:disable ?>
				<label>
				<textarea id='customkey'
				          name='<?php echo esc_attr( $_view['option_name'] ); ?>[customkey]'
				          class='wds-textarea'><?php echo esc_textarea( $custom_keywords ); ?></textarea>
				</label>
				<?php // phpcs:enable ?>
			</div>

			<div id="wds-custom-keywords"></div>
		</div>
	</div>
</div>

<div class="sui-box-settings-row">
	<div>
		<div class="sui-form-field">
			<label for="ignore" class="sui-settings-label"><?php esc_html_e( 'Exclusions', 'wds' ); ?></label>
			<p class="sui-description"><?php esc_html_e( 'Provide a comma-separated list of keywords that you would like to exclude. You can also select individual posts for exclusion.', 'wds' ); ?></p>

			<label for="ignore" class="sui-label"><?php esc_html_e( 'Excluded Keywords', 'wds' ); ?></label>
			<input id='ignore' name='<?php echo esc_attr( $_view['option_name'] ); ?>[ignore]' size='' type='text'
			       class='sui-form-control' value='<?php echo esc_attr( $_view['options']['ignore'] ); ?>'>
		</div>
		<div class="sui-form-field">
			<div class="wds-excluded-posts">
				<label for="ignorepost"
				       class="sui-label"><?php esc_html_e( 'Exclude Posts/Pages', 'wds' ); ?></label>
				<div class="wds-replaceable">
					<input id='ignorepost' name='<?php echo esc_attr( $_view['option_name'] ); ?>[ignorepost]' size=''
					       type='text' class='sui-form-control'
					       value='<?php echo esc_attr( $_view['options']['ignorepost'] ); ?>'>
					<p class="sui-description">
						<?php esc_html_e( 'Paste in the IDs, slugs or titles for the post/pages you wish to exclude and separate them by commas', 'wds' ); ?>
					</p>
				</div>
				<?php $this->_render( 'advanced-tools/underscore-postlist-selector' ); ?>
			</div>
		</div>
	</div>
</div>

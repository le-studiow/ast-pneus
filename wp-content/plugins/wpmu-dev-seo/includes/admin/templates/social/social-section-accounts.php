<?php
$options = empty( $options ) ? $_view['options'] : $options;
?>
<div class="wds-compact-setting-rows wds-separator-top">
	<div class="sui-box-settings-row">
		<div class="sui-box-settings-col-1">
			<label class="sui-settings-label" for="website_name"><?php esc_html_e( 'Website name', 'wds' ); ?></label>
		</div>
		<div class="sui-box-settings-col-2">
			<input type="text" class="sui-form-control" id="website_name"
			       name="<?php echo esc_attr( $_view['option_name'] ); ?>[sitename]"
			       value="<?php echo esc_attr( $options['sitename'] ); ?>"/>
		</div>
	</div>
</div>

<div class="sui-box-settings-row">
	<div class="sui-box-settings-col-1">
		<label class="sui-settings-label" for="schema_type"><?php esc_html_e( 'Type', 'wds' ); ?></label>
	</div>
	<div class="sui-box-settings-col-2">
		<div class="wds-conditional">
			<select id="schema_type"
			        name="<?php echo esc_attr( $_view['option_name'] ); ?>[schema_type]"
			        data-minimum-results-for-search="-1"
			        class="sui-select">
				<option <?php selected( $options['schema_type'], Smartcrawl_Schema_Printer::PERSON ); ?>
						value="<?php echo esc_attr( Smartcrawl_Schema_Printer::PERSON ); ?>">
					<?php esc_html_e( 'Person', 'wds' ); ?>
				</option>

				<option <?php selected( $options['schema_type'], Smartcrawl_Schema_Printer::ORGANIZATION ); ?>
						value="<?php echo esc_attr( Smartcrawl_Schema_Printer::ORGANIZATION ); ?>">
					<?php esc_html_e( 'Organization', 'wds' ); ?>
				</option>
			</select>

			<div data-conditional-val="<?php echo esc_attr( Smartcrawl_Schema_Printer::PERSON ); ?>"
			     class="sui-border-frame wds-conditional-inside">

				<div class="sui-form-field">
					<label for="override_name" class="sui-label"><?php esc_html_e( 'Your name', 'wds' ); ?></label>
					<input id="override_name" type="text"
					       class="sui-form-control"
					       name="<?php echo esc_attr( $_view['option_name'] ); ?>[override_name]"
					       value="<?php echo esc_attr( $options['override_name'] ); ?>"/>
				</div>
			</div>

			<div data-conditional-val="<?php echo esc_attr( Smartcrawl_Schema_Printer::ORGANIZATION ); ?>"
			     class="sui-border-frame wds-conditional-inside">

				<div class="sui-form-field">
					<label for="organization_name"
					       class="sui-label"><?php esc_html_e( 'Organization Name', 'wds' ); ?></label>
					<input id="organization_name" type="text"
					       class="sui-form-control"
					       name="<?php echo esc_attr( $_view['option_name'] ); ?>[organization_name]"
					       value="<?php echo esc_attr( $options['organization_name'] ); ?>"/>
				</div>

				<div class="sui-form-field">
					<label for="organization_logo"
					       class="sui-label"><?php esc_html_e( 'Organization Logo', 'wds' ); ?></label>
					<?php
					$this->_render( 'media-url-field', array(
						'item' => 'organization_logo',
					) );
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
$this->_render( 'toggle-group', array(
	'label'     => esc_html__( 'Schema markup', 'wds' ),
	'items'     => array(
		'disable-schema' => array(
			'label'       => esc_html__( 'Enable schema markup output', 'wds' ),
			'inverted'    => true,
			'description' => esc_html__( 'By default, the plugin will render appropriate schema markup to all your pages. You can disable this kind of output here.', 'wds' ),
		),
	),
	'separator' => true,
) );
?>

<div class="wds-compact-setting-rows">
	<div class="sui-box-settings-row">
		<div class="sui-box-settings-col-1">
			<label for="twitter_username"
			       class="sui-settings-label"><?php esc_html_e( 'Twitter Username', 'wds' ); ?></label>
		</div>
		<div class="sui-box-settings-col-2 wds-twitter-username">
			<input type="text" id="twitter_username"
			       class="sui-form-control"
			       name="<?php echo esc_attr( $_view['option_name'] ); ?>[twitter_username]"
			       value="<?php echo esc_attr( $options['twitter_username'] ); ?>"
			       placeholder="<?php esc_attr_e( 'username', 'wds' ); ?>"/>
		</div>
	</div>

	<div class="sui-box-settings-row">
		<div class="sui-box-settings-col-1">
			<label for="fb-app-id" class="sui-settings-label"><?php esc_html_e( 'Facebook App ID', 'wds' ); ?></label>
		</div>
		<div class="sui-box-settings-col-2 wds-fb-app-id">
			<input type="text" id="fb-app-id" name="<?php echo esc_attr( $_view['option_name'] ); ?>[fb-app-id]"
			       class="sui-form-control"
			       value="<?php echo esc_attr( $options['fb-app-id'] ); ?>"
			       placeholder="<?php esc_attr_e( 'App ID', 'wds' ); ?>"/>
		</div>
	</div>

	<div class="sui-box-settings-row">
		<div class="sui-box-settings-col-1">
			<label for="facebook_url"
			       class="sui-settings-label"><?php esc_html_e( 'Facebook Page Url', 'wds' ); ?></label>
		</div>
		<div class="sui-box-settings-col-2">
			<input type="text" id="facebook_url" name="<?php echo esc_attr( $_view['option_name'] ); ?>[facebook_url]"
			       class="sui-form-control"
			       value="<?php echo esc_attr( $options['facebook_url'] ); ?>"
			       placeholder="<?php esc_attr_e( 'https://facebook.com/pagename', 'wds' ); ?>"/>
		</div>
	</div>

	<div class="sui-box-settings-row">
		<div class="sui-box-settings-col-1">
			<label for="instagram_url" class="sui-settings-label"><?php esc_html_e( 'Instagram URL', 'wds' ); ?></label>
		</div>
		<div class="sui-box-settings-col-2">
			<input type="text" id="instagram_url" name="<?php echo esc_attr( $_view['option_name'] ); ?>[instagram_url]"
			       class="sui-form-control"
			       value="<?php echo esc_attr( $options['instagram_url'] ); ?>"
			       placeholder="<?php esc_attr_e( 'https://instagram.com/username', 'wds' ); ?>"/>
		</div>
	</div>

	<div class="sui-box-settings-row">
		<div class="sui-box-settings-col-1">
			<label for="linkedin_url" class="sui-settings-label"><?php esc_html_e( 'Linkedin URL', 'wds' ); ?></label>
		</div>
		<div class="sui-box-settings-col-2">
			<input type="text" id="linkedin_url" name="<?php echo esc_attr( $_view['option_name'] ); ?>[linkedin_url]"
			       class="sui-form-control"
			       value="<?php echo esc_attr( $options['linkedin_url'] ); ?>"
			       placeholder="<?php esc_attr_e( 'https://linkedin.com/username', 'wds' ); ?>"/>
		</div>
	</div>

	<div class="sui-box-settings-row">
		<div class="sui-box-settings-col-1">
			<label for="pinterest_url" class="sui-settings-label"><?php esc_html_e( 'Pinterest URL', 'wds' ); ?></label>
		</div>
		<div class="sui-box-settings-col-2">
			<input type="text" id="pinterest_url" name="<?php echo esc_attr( $_view['option_name'] ); ?>[pinterest_url]"
			       class="sui-form-control"
			       value="<?php echo esc_attr( $options['pinterest_url'] ); ?>"
			       placeholder="<?php esc_attr_e( 'https://pinterest.com/username', 'wds' ); ?>"/>
		</div>
	</div>

	<div class="sui-box-settings-row">
		<div class="sui-box-settings-col-1">
			<label for="youtube_url" class="sui-settings-label"><?php esc_html_e( 'Youtube URL', 'wds' ); ?></label>
		</div>
		<div class="sui-box-settings-col-2">
			<input type="text" id="youtube_url" name="<?php echo esc_attr( $_view['option_name'] ); ?>[youtube_url]"
			       class="sui-form-control"
			       value="<?php echo esc_attr( $options['youtube_url'] ); ?>"
			       placeholder="<?php esc_attr_e( 'https://www.youtube.com/user/username', 'wds' ); ?>"/>
		</div>
	</div>
</div>

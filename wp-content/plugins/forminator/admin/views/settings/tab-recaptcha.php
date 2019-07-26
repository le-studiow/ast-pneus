<?php
$section = isset( $_GET['section'] ) ? $_GET['section'] : 'dashboard'; // wpcs csrf ok.

$captcha_key      = get_option( "forminator_captcha_key", "" );
$captcha_secret   = get_option( "forminator_captcha_secret", "" );
$captcha_language = get_option( "forminator_captcha_language", "" );
$captcha_theme    = get_option( "forminator_captcha_theme", "" );
$nonce            = wp_create_nonce( 'forminator_save_popup_captcha' );

$new = true;
?>

<div class="sui-box" data-nav="recaptcha" style="<?php echo esc_attr( 'recaptcha' !== $section ? 'display: none;' : '' ); ?>">

	<div class="sui-box-header">
		<h2 class="sui-box-title"><?php esc_html_e( 'Google reCAPTCHA', Forminator::DOMAIN ); ?></h2>
	</div>

	<form class="forminator-settings-save" action="">

		<div class="sui-box-body">

			<div class="sui-box-settings-row">

				<div class="sui-box-settings-col-1">
					<span class="sui-settings-label"><?php esc_html_e( 'Credentials', Forminator::DOMAIN ); ?></span>
					<span class="sui-description"><?php esc_html_e( 'You need to enter reCAPTCHA credentials here to use reCAPTCHA form field.', Forminator::DOMAIN ); ?></span>
					&nbsp;
					<span class="sui-description"><?php printf( esc_html( __( "Note: Click %1\$shere%2\$s to register your site with reCAPTCHA API and generate credentials.", Forminator::DOMAIN ) ), '<a href="https://www.google.com/recaptcha/admin#list" target="_blank">', '</a>' ); ?></span>
				</div>

				<div class="sui-box-settings-col-2">

					<div class="sui-form-field">
						<label for="captcha_key" class="sui-label"><?php esc_html_e( 'Site Key', Forminator::DOMAIN ); ?></label>
						<input type="text"
							name="captcha_key"
							placeholder="<?php esc_html_e( 'Enter your site key here', Forminator::DOMAIN ); ?>"
							value="<?php echo esc_attr( $captcha_key ); ?>"
							id="captcha_key"
							class="sui-form-control" />
					</div>

					<div class="sui-form-field">
						<label for="captcha_secret" class="sui-label"><?php esc_html_e( 'Secret Key', Forminator::DOMAIN ); ?></label>
						<input type="text"
							name="captcha_secret"
							placeholder="<?php esc_html_e( 'Enter your secret key here', Forminator::DOMAIN ); ?>"
							value="<?php echo esc_attr( $captcha_secret ); ?>"
							id="captcha_secret"
							class="sui-form-control" />
					</div>

					<div class="sui-form-field">
						<label for="captcha_language" class="sui-label"><?php esc_html_e( 'Language', Forminator::DOMAIN ); ?></label>
						<select name="captcha_language" id="captcha_language" class="sui-select">
							<?php $languages = forminator_get_captcha_languages(); ?>
							<?php foreach ( $languages as $key => $lang ): ?>
								<option value="<?php echo $key; ?>" <?php selected( $captcha_language, $key ); ?>><?php echo $lang; ?></option>
							<?php endforeach; ?>
						</select>
						<span class="sui-description"><?php esc_html_e( 'By default, we’ll show the reCAPTCHA in your website’s language.', Forminator::DOMAIN ); ?></span>
					</div>

					<div class="sui-form-field">
						<label for="captcha_theme" class="sui-label"><?php esc_html_e( 'Theme', Forminator::DOMAIN ); ?></label>
						<select name="captcha_theme" id="captcha_theme">
							<option value="light" <?php selected( $captcha_theme, 'light' ); ?>><?php esc_html_e( 'Light', Forminator::DOMAIN ); ?></option>
							<option value="dark" <?php selected( $captcha_theme, 'dark' ); ?>><?php esc_html_e( 'Dark', Forminator::DOMAIN ); ?></option>
						</select>
					</div>

				</div>

			</div>

			<div class="sui-box-settings-row">

				<div class="sui-box-settings-col-1">
					<span class="sui-settings-label"><?php esc_html_e( 'Connection Test', Forminator::DOMAIN ); ?></span>
					<span class="sui-description"><?php esc_html_e( 'Once you’ve saved your credentials, you should see a preview of reCAPTCHA without any errors.', Forminator::DOMAIN ); ?></span>
				</div>

				<div class="sui-box-settings-col-2">

					<label class="sui-label"><?php esc_html_e( 'reCAPTCHA Preview', Forminator::DOMAIN ); ?></label>

					<div id="recaptcha-preview" class="sui-border-frame">
						<p class="fui-loading-dialog">
							<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
						</p>
					</div>

					<span class="sui-description"><?php printf( esc_html( __( "If you see any errors, make sure that the keys you’ve entered are valid and you’ve listed your domain name while generating the keys. Click %1\$shere%2\$s to open reCAPTCHA admin panel.", Forminator::DOMAIN ) ), '<a href="https://www.google.com/recaptcha/admin" target="_blank">', '</a>' ); ?></span>

				</div>

			</div>

		</div>

		<div class="sui-box-footer">

			<div class="sui-actions-right">

				<button class="sui-button sui-button-blue wpmudev-action-done" data-title="<?php esc_attr_e( "reCaptcha settings", Forminator::DOMAIN ); ?>" data-action="captcha"  data-nonce="<?php echo esc_attr( $nonce ); ?>">
					<span class="sui-loading-text"><?php esc_html_e( 'Save Settings', Forminator::DOMAIN ); ?></span>
					<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
				</button>

			</div>

		</div>

	</form>

</div>

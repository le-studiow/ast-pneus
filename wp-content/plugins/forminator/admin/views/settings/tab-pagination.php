<?php
$section = isset( $_GET['section'] ) ? $_GET['section'] : 'dashboard'; // wpcs csrf ok.

$entries_per_page = get_option( "forminator_pagination_entries", 10 );
$module_per_page  = get_option( "forminator_pagination_listings", 10 );
$nonce            = wp_create_nonce( 'forminator_save_popup_pagination' );

?>

<div class="sui-box" data-nav="pagination" style="<?php echo esc_attr( 'pagination' !== $section ? 'display: none;' : '' ); ?>">

	<div class="sui-box-header">
		<h2 class="sui-box-title"><?php esc_html_e( 'Pagination', Forminator::DOMAIN ); ?></h2>
	</div>

	<form class="forminator-settings-save" action="">

		<div class="sui-box-body">

			<div class="sui-box-settings-row">

				<div class="sui-box-settings-col-1">
					<span class="sui-settings-label"><?php esc_html_e( 'Submissions', Forminator::DOMAIN ); ?></span>
					<span class="sui-description"><?php esc_html_e( 'Choose the number of entries per page for the submissions.', Forminator::DOMAIN ); ?></span>
				</div>

				<div class="sui-box-settings-col-2">

					<div class="sui-form-field">
						<label for="forminator-limit-entries" class="sui-label"><?php esc_html_e( 'Entries per page', Forminator::DOMAIN ); ?></label>
						<input type="number"
						       name="pagination_entries"
						       placeholder="<?php esc_html_e( 'E.g. 10', Forminator::DOMAIN ); ?>"
						       value="<?php echo esc_attr( $entries_per_page ); ?>"
						       min="1"
						       id="forminator-limit-entries"
						       class="sui-form-control forminator-required"/>
						<span class="sui-error-message" style="display: none;"><?php esc_html_e( 'This field cannot be empty.', Forminator::DOMAIN ); ?></span>

					</div>

				</div>

			</div>

			<div class="sui-box-settings-row">

				<div class="sui-box-settings-col-1">
					<span class="sui-settings-label"><?php esc_html_e( 'Modules', Forminator::DOMAIN ); ?></span>
					<span class="sui-description"><?php esc_html_e( 'Choose the number of modules per page for the list of Forms, Polls and Quizzes.', Forminator::DOMAIN ); ?></span>
				</div>

				<div class="sui-box-settings-col-2">

					<div class="sui-form-field">
						<label for="forminator-limit-listing" class="sui-label"><?php esc_html_e( 'Modules per page', Forminator::DOMAIN ); ?></label>
						<input type="number"
						       name="pagination_listings"
						       placeholder="<?php esc_html_e( 'E.g. 10', Forminator::DOMAIN ); ?>"
						       value="<?php echo esc_attr( $module_per_page ); ?>"
						       min="1"
						       id="forminator-limit-listing"
						       class="sui-form-control forminator-required"/>
						<span class="sui-error-message" style="display: none;"><?php esc_html_e( 'This field cannot be empty.', Forminator::DOMAIN ); ?></span>

					</div>

				</div>

			</div>

		</div>

		<div class="sui-box-footer">

			<div class="sui-actions-right">

				<button class="sui-button sui-button-blue wpmudev-action-done" data-title="<?php esc_attr_e( "Pagination settings", Forminator::DOMAIN ); ?>" data-action="pagination"
				        data-nonce="<?php echo esc_attr( $nonce ); ?>">
					<span class="sui-loading-text"><?php esc_html_e( 'Save Settings', Forminator::DOMAIN ); ?></span>
					<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
				</button>

			</div>

		</div>

	</form>

</div>

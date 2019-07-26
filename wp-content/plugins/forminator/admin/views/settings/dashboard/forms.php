<?php
$dashboard_settings = forminator_get_dashboard_settings( 'forms', array() );
$num_recent         = isset( $dashboard_settings['num_recent'] ) ? $dashboard_settings['num_recent'] : 4;
$published          = isset( $dashboard_settings['published'] ) ? filter_var( $dashboard_settings['published'], FILTER_VALIDATE_BOOLEAN ) : true;
$draft              = isset( $dashboard_settings['draft'] ) ? filter_var( $dashboard_settings['draft'], FILTER_VALIDATE_BOOLEAN ) : true;

?>
<div class="sui-box-settings-row">

	<div class="sui-box-settings-col-1">
		<span class="sui-settings-label"><?php esc_html_e( 'Forms', Forminator::DOMAIN ); ?></span>
		<span class="sui-description"><?php esc_html_e( 'Customize your dashboard form listing as per your liking.', Forminator::DOMAIN ); ?></span>
	</div>

	<div class="sui-box-settings-col-2">

		<?php
		// SECTION: Number of forms ?>
		<div class="sui-form-field">

			<label class="sui-settings-label"><?php esc_html_e( 'Number of Forms', Forminator::DOMAIN ); ?></label>

			<span class="sui-description" style="margin-bottom: 10px;"><?php esc_html_e( 'Choose the number of recent forms to be displayed on the dashboard form listing.', Forminator::DOMAIN ); ?></span>

			<input
				type="number"
				placeholder="0"
				class="sui-form-control"
				style="max-width: 100px;"
				min="0"
				value="<?php echo esc_attr( $num_recent ); ?>"
				name="num_recent[forms]"
			/>

			<span class="sui-error-message" style="display: none;"><?php esc_html_e( "This field shouldn't be empty." ); ?></span>

		</div>

		<?php
		// SECTION: Status ?>
		<div class="sui-form-field">

			<label class="sui-settings-label"><?php esc_html_e( 'Status', Forminator::DOMAIN ); ?></label>

			<span class="sui-description" style="margin-bottom: 10px;"><?php esc_html_e( 'Choose forms with a specific status to be listed on the dashboard. You need to select at least one of the following otherwise the form listing would appear empty.', Forminator::DOMAIN ); ?></span>

			<label for="forminator-forms-status-published" class="sui-checkbox sui-checkbox-sm sui-checkbox-stacked">
				<input
					type="checkbox"
					id="forminator-forms-status-published"
					value="true"
					<?php echo checked( $published ); ?>
					name="published[forms]"
				/>
				<span aria-hidden="true"></span>
				<span><?php esc_html_e( 'Published', Forminator::DOMAIN ); ?></span>
			</label>

			<label for="forminator-forms-status-drafts" class="sui-checkbox sui-checkbox-sm sui-checkbox-stacked">
				<input
					type="checkbox"
					id="forminator-forms-status-drafts"
					value="true"
					<?php echo checked( $draft ); ?>
					name="draft[forms]"
				/>
				<span aria-hidden="true"></span>
				<span><?php esc_html_e( 'Drafts', Forminator::DOMAIN ); ?></span>
			</label>

		</div>

	</div>

</div>

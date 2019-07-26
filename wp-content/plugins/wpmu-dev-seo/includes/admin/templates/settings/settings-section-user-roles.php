<?php
$seo_metabox_permission_level = empty( $seo_metabox_permission_level ) ? array() : $seo_metabox_permission_level;
$seo_metabox_301_permission_level = empty( $seo_metabox_301_permission_level ) ? array() : $seo_metabox_301_permission_level;
$urlmetrics_metabox_permission_level = empty( $urlmetrics_metabox_permission_level ) ? array() : $urlmetrics_metabox_permission_level;
$option_name = empty( $_view['option_name'] ) ? '' : $_view['option_name'];
?>
<div class="sui-box-settings-row">
	<div class="sui-box-settings-col-1">
		<label class="sui-settings-label"><?php esc_html_e( 'Access', 'wds' ); ?></label>
	</div>
	<div class="sui-box-settings-col-2">

		<label for="seo_metabox_permission_level"
		       class="sui-settings-label"><?php esc_html_e( 'In page SEO meta box', 'wds' ); ?></label>
		<span class="sui-description"><?php esc_html_e( 'Choose what user level you want to be able to edit individual post and page meta tags.', 'wds' ); ?></span>

		<select id="seo_metabox_permission_level"
		        name="<?php echo esc_attr( $option_name ); ?>[seo_metabox_permission_level][]"
		        class="sui-select"
		        data-minimum-results-for-search="-1">
			<?php foreach ( $seo_metabox_permission_level as $item => $label ) : ?>
				<?php
				$selected = ! empty( $_view['options']['seo_metabox_permission_level'] ) && is_array( $_view['options']['seo_metabox_permission_level'] )
					? ( in_array( $item, $_view['options']['seo_metabox_permission_level'], true ) ? "selected" : '' ) // New
					: ( $_view['options']['seo_metabox_permission_level'] === $item ? "selected" : '' );
				?>
				<option
					<?php echo esc_attr( $selected ); ?>
						value="<?php echo esc_attr( $item ); ?>">
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>

		<label for="seo_metabox_301_permission_level"
		       class="sui-settings-label"><?php esc_html_e( '301 Redirections', 'wds' ); ?></label>
		<span class="sui-description"><?php esc_html_e( 'Choose what user level has the ability to add 301 redirects to individual posts and pages.', 'wds' ); ?></span>

		<select id="seo_metabox_301_permission_level"
		        name="<?php echo esc_attr( $option_name ); ?>[seo_metabox_301_permission_level][]"
		        class="sui-select"
		        data-minimum-results-for-search="-1">
			<?php foreach ( $seo_metabox_301_permission_level as $item => $label ) { ?>
				<?php
				$selected = ! empty( $_view['options']['seo_metabox_301_permission_level'] ) && is_array( $_view['options']['seo_metabox_301_permission_level'] )
					? ( in_array( $item, $_view['options']['seo_metabox_301_permission_level'], true ) ? "selected" : '' ) // New
					: ( $_view['options']['seo_metabox_301_permission_level'] === $item ? "selected" : '' );
				?>
				<option
					<?php echo esc_attr( $selected ); ?>
						value="<?php echo esc_attr( $item ); ?>">
					<?php echo esc_html( $label ); ?>
				</option>
			<?php } ?>
		</select>

		<label for="urlmetrics_metabox_permission_level"
		       class="sui-settings-label"><?php esc_html_e( 'Show Moz data to roles', 'wds' ); ?></label>
		<span class="sui-description"><?php esc_html_e( 'Choose what user level gets to view the Moz data.', 'wds' ); ?></span>

		<select id="urlmetrics_metabox_permission_level"
		        name="<?php echo esc_attr( $option_name ); ?>[urlmetrics_metabox_permission_level][]"
		        class="sui-select"
		        data-minimum-results-for-search="-1">
			<?php foreach ( $urlmetrics_metabox_permission_level as $item => $label ) : ?>
				<?php
				$selected = ! empty( $_view['options']['urlmetrics_metabox_permission_level'] ) && is_array( $_view['options']['urlmetrics_metabox_permission_level'] )
					? ( in_array( $item, $_view['options']['urlmetrics_metabox_permission_level'], true ) ? "selected" : '' ) // New
					: ( $_view['options']['urlmetrics_metabox_permission_level'] === $item ? "selected" : '' );
				?>
				<option
					<?php echo esc_attr( $selected ); ?>
						value="<?php echo esc_attr( $item ); ?>">
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</div>
</div>

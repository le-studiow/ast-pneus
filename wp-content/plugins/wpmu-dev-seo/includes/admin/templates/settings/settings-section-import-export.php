<?php wp_nonce_field( 'wds-io-nonce', '_wds_nonce' ); ?>
<div class="wds-io">
	<div class="sui-box-settings-row">
		<div class="sui-box-settings-col-1">
			<label class="sui-settings-label"><?php esc_html_e( 'Import', 'wds' ); ?></label>
			<p class="sui-description"><?php esc_html_e( 'Use this tool to import your SmartCrawl settings from another site.', 'wds' ); ?></p>
		</div>
		<div class="sui-box-settings-col-2 wds-io wds-import">
			<div>
				<label class="sui-settings-label"><?php esc_html_e( 'SmartCrawl', 'wds' ); ?></label>
				<span class="sui-description">
					<?php esc_html_e( 'Import your exported SmartCrawl XML settings file.', 'wds' ); ?>
				</span>

				<div class="sui-upload">
					<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo esc_attr( wp_max_upload_size() ); ?>"/>
					<input id="wds_import_json" type="file" name="wds_import_json"/>
					<label class="sui-upload-button" for="wds_import_json">
						<i class="sui-icon-upload-cloud" aria-hidden="true"></i>
						<?php esc_html_e( 'Upload File', 'wds' ); ?>
					</label>
					<div class="sui-upload-file">
						<span></span>
						<button aria-label="<?php esc_html_e( 'Remove file', 'wds' ); ?>">
							<i class="sui-icon-close" aria-hidden="true"></i>
						</button>
					</div>
				</div>
				<button name="io-action" value="import"
				        class="sui-button">
					<i class="sui-icon-download-cloud" aria-hidden="true"></i>

					<?php esc_html_e( 'Import', 'wds' ); ?>
				</button>
			</div>
			<?php if ( is_main_site() ): ?>
				<div class="wds-separator-top">
					<label class="sui-settings-label"><?php esc_html_e( 'Third Party', 'wds' ); ?></label>
					<p class="sui-description">
						<?php esc_html_e( 'Automatically import your SEO configuration from other SEO plugins.', 'wds' ); ?>
					</p>

					<table class="sui-table">
						<tr class="wds-yoast">
							<td>
								<strong><?php esc_html_e( 'Yoast SEO', 'wds' ); ?></strong>
							</td>
							<td>
								<button type="button" class="sui-button">
									<i class="sui-icon-download-cloud" aria-hidden="true"></i>

									<?php esc_html_e( 'Import', 'wds' ); ?>
								</button>
							</td>
						</tr>
						<tr class="wds-aioseop">
							<td>
								<strong><?php esc_html_e( 'All In One SEO', 'wds' ); ?></strong>
							</td>
							<td>
								<button type="button" class="sui-button">
									<i class="sui-icon-download-cloud" aria-hidden="true"></i>

									<?php esc_html_e( 'Import', 'wds' ); ?>
								</button>
							</td>
						</tr>
					</table>
					<p class="sui-description">
						<?php esc_html_e( 'Automatically import your SEO configuration from other SEO plugins. Note: This will override all of your current settings. We recommend exporting your current settings first, just in case.', 'wds' ); ?>
					</p>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<div class="sui-box-settings-row">
		<div class="sui-box-settings-col-1">
			<label class="sui-settings-label"><?php esc_html_e( 'Export', 'wds' ); ?></label>
			<p class="sui-description"><?php esc_html_e( 'Export your full SmartCrawl configuration to use on another site.', 'wds' ); ?></p>
		</div>
		<div class="sui-box-settings-col-2">
			<button name="io-action" value="export"
			        class="sui-button sui-button-ghost">
				<i class="sui-icon-download-cloud" aria-hidden="true"></i>

				<?php esc_html_e( 'Export', 'wds' ); ?>
			</button>
		</div>
	</div>
</div>
<?php $this->_render( 'settings/import-status-modal' ); ?>

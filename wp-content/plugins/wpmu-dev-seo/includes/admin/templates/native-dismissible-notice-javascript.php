<script type="application/javascript">
	jQuery(function ($) {
		$(document).on('click', '.wds-native-dismissible-notice .notice-dismiss', function () {
			var message_key = $(this).closest('.wds-native-dismissible-notice').data('messageKey');
			$.post(
				ajaxurl,
				{
					action: 'wds_dismiss_message',
					message: message_key,
					_wds_nonce: '<?php echo esc_js( wp_create_nonce( 'wds-admin-nonce' ) ); ?>'
				},
				'json'
			);
		});
	});
</script>

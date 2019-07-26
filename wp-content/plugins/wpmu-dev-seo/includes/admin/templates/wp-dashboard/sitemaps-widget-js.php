<?php
$updating = empty( $updating ) ? '' : $updating;
$updated = empty( $updated ) ? '' : $updated;
$notifying = empty( $notifying ) ? '' : $notifying;
$notified = empty( $notified ) ? '' : $notified;
?>
<script type="text/javascript">
	;(function ($) {
		$(function () {
			$("#wds_update_now").click(function () {
				var me = $(this);
				me.html("<?php echo esc_js( $updating ); ?>");

				$.post(ajaxurl, {"action": "wds_update_sitemap"}, function () {
					me.html("<?php echo esc_js( $updated ); ?>");
					window.location.reload();
				});

				return false;
			});

			$("#wds_update_engines").click(function () {
				var me = $(this);
				me.html("<?php echo esc_js( $notifying ); ?>");

				$.post(ajaxurl, {"action": "wds_update_engines"}, function () {
					me.html("<?php echo esc_js( $notified ); ?>");
					window.location.reload();
				});

				return false;
			});
		});
	})(jQuery);
</script>

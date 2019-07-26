<script type="text/javascript">
	(function ($) {

		$('body').on('click', 'td.column-title', '.editinline', function () {
			var id = inlineEditPost.getId(this),
                loading = "<?php echo esc_js( __( 'Loading, please hold on...', 'wds' ) ); ?>"
				;
			setTimeout(function () {
				$(".smartcrawl_title:visible").attr("placeholder", loading);
				$(".smartcrawl_metadesc:visible").attr("placeholder", loading);
				$(".smartcrawl_focus:visible").attr("placeholder", loading);
				$(".smartcrawl_keywords:visible").attr("placeholder", loading);
			}); // Just move off stack
			$.post(ajaxurl, {
			    "action": "wds_get_meta_fields",
                "id": id,
                "_wds_nonce": '<?php echo esc_js(wp_create_nonce( 'wds-metabox-nonce' )); ?>'
            }, function (data) {
				$(".smartcrawl_title:visible, .smartcrawl_metadesc:visible, .smartcrawl_focus:visible, .smartcrawl_keywords:visible").attr("placeholder", "");
				if (!data) return false;
				if ("title" in data && data.title) {
					$(".smartcrawl_title:visible")
						.val(data.title)
					;
				}
				if ("description" in data && data.description) {
					$(".smartcrawl_metadesc:visible")
						.val(data.description)
					;
				}
				if ("keywords" in data && data.keywords) {
					$(".smartcrawl_keywords:visible")
						.val(data.keywords)
					;
				}
				if ("focus" in data && data.focus) {
					$(".smartcrawl_focus:visible")
						.val(data.focus)
					;
				}
			}, "json");
		});

	})(jQuery);
</script>

jQuery(document).ready(function ($) {
	function open_pointer(id) {
		var pointers = (_wds_pointers || {}).pointers;
		if (!pointers || !pointers[id]) {
			return;
		}

		var pointer = pointers[id],
			options = $.extend(pointer.options, {
				pointerClass: 'wp-pointer wds-pointer',
				show: function (event, target) {
					target.pointer.css({'position': 'fixed'});
					target.pointer.data('target', pointer.target);
				},
				close: function () {
					$.post(ajaxurl, {
						pointer: pointer.pointer_id,
						action: 'dismiss-wp-pointer'
					});
				}
			});

		var $target = $(pointer.target);
		$target.pointer(options);
		if ($target.is(':visible')) {
			$target.pointer('open');
		}
	}

	function reposition_all_pointers() {
		$('.wds-pointer').each(function (index, element) {
			var $target = $($(element).data('target'));
			if ($target.is(':visible') && $target.pointer('instance')) {
				$target.pointer('reposition');
			}
		});
	}

	$(document).on('wp-collapse-menu', reposition_all_pointers);
	$(window).on('resize', _.debounce(reposition_all_pointers, 250));

	open_pointer('wds-activation-pointer');
});

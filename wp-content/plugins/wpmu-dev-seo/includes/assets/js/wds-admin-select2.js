;(function ($) {


	$(function () {
		$(".wds-multiselect select[multiple]").select2({
			tags: true
		});

		var $select2 = $(".select2-container");
		$select2.on('click', function() {
			var $this = $(this),
				$selectContainer = $this.parent('.select-container');

			if( $selectContainer.hasClass("select-container--open") ) {
				$selectContainer.removeClass("select-container--open");
			} else {
				if( $this.hasClass("select2-container--open") ) {
					$selectContainer.addClass("select-container--open");
				}
			}

		});

	});


})(jQuery);

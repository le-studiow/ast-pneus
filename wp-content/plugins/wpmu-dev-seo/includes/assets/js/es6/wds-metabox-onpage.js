(function ($) {
	window.Wds = window.Wds || {};

	class MetaboxOnpageHelper {
		static get_title() {
			return $('#wds_title').val();
		}

		static get_description() {
			return $('#wds_metadesc').val();
		}

		static preview_loading(loading) {
			let $preview = this.get_preview_el().find('.wds-preview-container'),
				loading_class = 'wds-preview-loading';

			if (loading) {
				$preview.addClass(loading_class);
			} else {
				$preview.removeClass(loading_class);
			}
		}

		static get_preview_el() {
			return $('.wds-metabox-preview');
		}

		static replace_preview_markup(new_markup) {
			this.get_preview_el().replaceWith(new_markup)
		}

		static set_title_placeholder(placeholder) {
			$('#wds_title').attr('placeholder', placeholder);
		}

		static set_desc_placeholder(placeholder) {
			$('#wds_metadesc').attr('placeholder', placeholder);
		}
	}

	class MetaboxOnpage extends EventTarget {
		constructor() {
			super();

			this.editor = window.Wds.postEditor;
			this.init();
		}

		init() {
			this.editor.addEventListener('autosave', (e) => this.handle_autosave_event(e));
			this.editor.addEventListener('content-change', (e) => this.handle_content_change_event(e));

			$(document)
				.on('input propertychange', '.wds-meta-field', _.debounce((e) => this.handle_meta_change(e), 2000));

			$(window)
				.on('load', () => this.handle_page_load());
		}

		handle_autosave_event() {
			this.refresh_preview();
			this.refresh_placeholders();
		}

		handle_content_change_event() {
			this.refresh_preview();
			this.refresh_placeholders();
		}

		handle_meta_change() {
			this.dispatch_meta_change_event();
			this.refresh_preview();
		}

		handle_page_load() {
			this.refresh_preview();
			this.refresh_placeholders();
		}

		refresh_preview() {
			MetaboxOnpageHelper.preview_loading(true);

			this.post('wds-metabox-preview', {
				wds_title: MetaboxOnpageHelper.get_title(),
				wds_description: MetaboxOnpageHelper.get_description(),
				post_id: this.editor.get_data().post_id,
				is_dirty: this.editor.is_post_dirty() ? 1 : 0,
			}).done(function (data) {
				data = (data || {});

				if (data.success) {
					MetaboxOnpageHelper.replace_preview_markup(data.markup);
				}
			}).always(function () {
				MetaboxOnpageHelper.preview_loading(false);
			});
		}

		refresh_placeholders() {
			this.post('wds_metabox_update', {
				id: this.editor.get_data().post_id,
				post: this.editor.get_data(),
			}).done(function (data) {
				data = data || {};
				let description = (data).description || '',
					title = (data).title || '';

				MetaboxOnpageHelper.set_title_placeholder(title);
				MetaboxOnpageHelper.set_desc_placeholder(description);
			});
		}

		dispatch_meta_change_event() {
			this.dispatchEvent(new Event('meta-change'));
		}

		post(action, data) {
			data = $.extend({
				action: action,
				_wds_nonce: _wds_metabox_onpage.nonce
			}, data);

			return $.post(ajaxurl, data);
		}
	}

	window.Wds.metaboxOnpage = new MetaboxOnpage();
})(jQuery);

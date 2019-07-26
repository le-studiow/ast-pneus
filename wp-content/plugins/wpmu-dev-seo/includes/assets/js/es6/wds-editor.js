(function ($) {
	window.Wds = window.Wds || {};

	class GutenbergEditor extends EventTarget {
		constructor() {
			super();

			this.init();
		}

		init() {
			this.hook_change_listener();
			this.register_api_fetch_middleware();
		}

		get_data() {
			let fields = ['content', 'excerpt', 'post_author', 'post_id', 'post_title', 'post_type'],
				data = {};

			fields.forEach((field) => {
				data[field] = this.get_editor().getEditedPostAttribute(field.replace('post_', '')) || '';
			});

			if (!data.post_id) {
				data.post_id = $('#post_ID').val() || 0;
			}

			return data;
		}

		get_editor() {
			return wp.data.select("core/editor");
		}

		dispatch_content_change_event() {
			this.dispatchEvent(new Event('content-change'));
		}

		dispatch_editor() {
			return wp.data.dispatch("core/editor");
		}

		hook_change_listener() {
			let debounced = _.debounce(() => this.dispatch_content_change_event(), 10000);

			wp.data.subscribe(() => {
				if (
					this.get_editor().isEditedPostDirty()
					&& !this.get_editor().isAutosavingPost()
					&& !this.get_editor().isSavingPost()
				) {
					debounced();
				}
			});
		}

		register_api_fetch_middleware() {
			if (!(wp || {}).apiFetch) {
				return;
			}

			wp.apiFetch.use((options, next) => {
				let result = next(options);
				result.then(() => {
					if (this.is_autosave_request(options) || this.is_post_save_request(options)) {
						this.dispatch_autosave_event();
					}
				});

				return result;
			});
		}

		dispatch_autosave_event() {
			this.dispatchEvent(new Event('autosave'));
		}

		is_autosave_request(request) {
			return request && request.path
				&& request.path.includes('/autosaves');
		}

		is_post_save_request(request) {
			let post = this.get_data(),
				post_id = post.post_id,
				post_type = post.post_type;

			return request && request.path
				&& request.method === 'PUT'
				&& request.path.includes('/' + post_id)
				&& request.path.includes('/' + post_type);
		}

		autosave() {
			// TODO: Keep track of this error: https://github.com/WordPress/gutenberg/issues/7416
			if (this.get_editor().isEditedPostAutosaveable()) {
				this.dispatch_editor().autosave();
			} else {
				this.dispatch_autosave_event();
			}
		}

		is_post_dirty() {
			return this.get_editor().isEditedPostDirty();
		}
	}

	window.Wds.postEditor = new GutenbergEditor();
})(jQuery);

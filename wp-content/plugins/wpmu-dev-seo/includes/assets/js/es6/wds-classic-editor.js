(function ($) {
	window.Wds = window.Wds || {};

	class ClassicEditor extends EventTarget {
		constructor() {
			super();

			this.init();
		}

		init() {
			$(document)
				.on('input', 'input#title,textarea#content,textarea#excerpt', this.get_debounced_change_callback())
				.on('after-autosave.smartcrawl', () => this.dispatch_autosave_event());

			$(window)
				.on('load', () => this.hook_tinymce_change_listener());
		}

		get_data() {
			return wp.autosave.getPostData();
		}

		dispatch_content_change_event() {
			this.dispatchEvent(new Event('content-change'));
		}

		hook_tinymce_change_listener() {
			let editor = typeof tinymce !== 'undefined' && tinymce.get('content');
			if (editor) {
				editor.on('change', this.get_debounced_change_callback());
			}
		}

		get_debounced_change_callback() {
			return _.debounce(() => this.dispatch_content_change_event(), 2000);
		}

		dispatch_autosave_event() {
			this.dispatchEvent(new Event('autosave'));
		}

		/**
		 * When the classic editor is active and we trigger an autosave programmatically,
		 * the heartbeat API is used for the autosave.
		 *
		 * To provide a seamless experience, this method temporarily removes our usual handler
		 * and hooks a handler to the heartbeat event.
		 */
		autosave() {
			let handle_heartbeat = () => {
				this.dispatch_autosave_event();

				// Re-hook our regular autosave handler
				$(document).on('after-autosave.smartcrawl', () => this.dispatch_autosave_event());
			};

			// We are already hooked to autosave so let's disable our regular autosave handler momentarily to avoid multiple calls ...
			$(document).off('after-autosave.smartcrawl');
			// hook a new handler to heartbeat-tick.autosave
			$(document).one('heartbeat-tick.autosave', handle_heartbeat);

			// Save any changes pending in the editor to the textarea
			this.trigger_tinymce_save();
			// Actually trigger the autosave
			wp.autosave.server.triggerSave();
		}

		trigger_tinymce_save() {
			let editorSync = (tinyMCE || {}).triggerSave;
			if (editorSync) {
				editorSync();
			}
		}

		is_post_dirty() {
			return wp.autosave.server.postChanged();
		}
	}

	window.Wds.postEditor = new ClassicEditor();
})(jQuery);

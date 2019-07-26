;(function ($, undefined) {

	window.Wds = window.Wds || {};

	window.Wds.Postlist = window.Wds.Postlist || {
		
		/**
		 * Simple post representation
		 *
		 * @param {Integer} post_id (Optional)
		 */
		Post: function (post_id) {
			
			var _post_id = parseInt(post_id, 10) || undefined,
				_title = '',
				_type = '',
				_date = '',
				_deferred = false
			;

			/**
			 * Checks post loaded state
			 *
			 * @return {Boolean}
			 */
			var is_loaded = function () {
				if (!_deferred) return false;
				return 'pending' !== _deferred.state();
			};

			/**
			 * Gets post instance ID
			 *
			 * @return {Integer} Post WP ID
			 */
			var get_id = function () {
				return _post_id || 0;
			};

			/**
			 * Gets post title
			 *
			 * @return {String} Post title
			 */
			var get_title = function () {
				return _title || '';
			};

			/**
			 * Gets post type
			 *
			 * @return {String} Post type
			 */
			var get_type = function () {
				return _type || '';
			};

			/**
			 * Gets post date
			 *
			 * @return {String} Post date
			 */
			var get_date = function () {
				return _date || '';
			};

			/**
			 * Gets post data as simple object
			 *
			 * @return {Object} Post data
			 */
			var get = function () {
				return {
					id: get_id(),
					title: get_title(),
					type: get_type(),
					date: get_date(),
					is_loaded: is_loaded()
				};
			};

			/**
			 * Dry-initializes data via passed post simple object
			 *
			 * @param {Object} data Post data
			 */
			var set = function (data) {
				_deferred = new $.Deferred();

				data = data || {};
				_post_id = parseInt(data.id, 10) || undefined;
				_title = data.title || '';
				_type = data.type || '';
				_date = data.date || '';

				_deferred.resolve();
			};

			/**
			 * Populates local post data via remote request
			 *
			 * @param {Integer} post_id Optional post ID
			 *
			 * @return {Object} jQuery Deferred promise
			 */
			var load = function (post_id) {
				var pid = parseInt(post_id, 10) || get_id(),
					prm
				;
				_deferred = new $.Deferred();
				prm = _deferred.promise();

				if (pid) {
					_post_id = pid;
					// Send post request
					$.post(ajaxurl, {
							action: 'wds-load_exclusion-post_data',
							id: pid,
							_wds_nonce: _wds_postlist.nonce
						}, _.noop, 'json')
						.done(function (data) {
							set(data);
						})
						.always(function () {
							setTimeout(dfr.resolve);
						})
					;
				} else {
					setTimeout(dfr.reject);
				}

				return dfr.promise();
			};

			return {
				get_id: get_id,
				get_title: get_title,
				get_type: get_type,
				get: get,
				set: set,
				load: load,
				is_loaded: is_loaded
			};
		},

		Selector: function (list) {
			var _dialog = $('#wds-post-exclusion-selector'),
				_exclusion_dropdown = $('#wds-post-exclusion-id'),
				_exclusion_type = $('#wds-post-exclusion-type');

			function prepare_request(params) {
				return {
					action: 'wds-load_exclusion_posts-posts_data-paged',
					search: params.term,
					type: _exclusion_type.val(),
					_wds_nonce: _wds_postlist.nonce
				};
			}

			function prepare_dropdown_results(data) {
				var results = [];
				_.each(data.posts || [], function (post) {
					results.push({
						id: post.id,
						text: post.title
					});

					list.cache_post_data(post);
				});

				return {
					results: results
				}
			}

			init_dropdown();

			function init_dropdown() {
				_exclusion_dropdown.SUIselect2({
					dropdownCssClass: 'sui-select-dropdown',
					minimumInputLength: 3,
					ajax: {
						url: ajaxurl,
						type: "POST",
						dataType: 'json',
						delay: 1000,
						data: prepare_request,
						processResults: prepare_dropdown_results
					}
				});
			}

			var open = function (close_callback) {
				_exclusion_type.val('post').trigger('change');
				_exclusion_dropdown.val('').trigger('change');

				_dialog.one('click', '.wds-action-button', function () {
					close_callback(_exclusion_dropdown.val());
					Wds.close_dialog();
				});

				window.Wds.open_dialog('wds-post-exclusion-selector');
			};

			return {
				open: open,
			};
		},

		/**
		 * General list abstraction
		 *
		 * @param {string} type List type
		 */
		_List: function (type) {

			this._type = type || 'general';
			this._posts = [];
			this._cache = {};
			this._meta = {};

			this.get_type = function () { return this._type; };

			this.is_loading = function () { return _.isEmpty(this.get_meta()); };

			/**
			 * Adds a post by its ID to internal list and cache
			 *
			 * Also initializes the post
			 *
			 * @param {Integer} pid Post WP ID
			 * @param {Function} cback Optional callback
			 */
			this.add = function (pid, cback) {
				var post = new Wds.Postlist.Post(),
					me = this
				;

				if (this.has(pid)) return false;

				if (this._cache[pid]) {
					post.set(this._cache[pid]);
				} else {
					post.load(pid).done(function () {
						me._cache[pid] = post.get();
						if (cback) cback();
					});
				}
				this._posts.push(post);
			};

			/**
			 * Removes post from the internal list, by ID
			 *
			 * @param {Integer} pid Post WP ID
			 */
			this.remove = function (pid) {
				this._posts = _(this._posts).reject(function (post) {
					return post.get_id() === pid;
				});
			};

			/**
			 * Adds initialized post to internal list and cache
			 *
			 * Also initializes the post
			 *
			 * @param {Object} post A Wds.Postlist.Post instance
			 */
			this.add_post = function (post) {
				var pid = post.get_id();
				if (this.has(pid)) return false;

				this._cache[pid] = post.get();
				this._posts.push(post);
			};

			this.cache_post_data = function (post_data) {
				var pid = post_data.id || 0;
				if (this.get_cached(pid)) {
					return false;
				}

				this._cache[pid] = post_data;
			};

			/**
			 * Check if the current post ID is already in the list
			 *
			 * @param {Integer} pid Post WP ID
			 *
			 * @return {Boolean} [description]
			 */
			this.has = function (pid) {
				return _.contains(
					_.invoke(this._posts, 'get_id'),
					pid
				);
			};

			this.get_cached = function (pid) {
				return this._cache[pid] || false;
			};

			this.get_meta = function () {
				return this._meta || {};
			};

			this.get_post = function (pid) {
				return this._posts[pid] || false;
			};

			this.get_posts = function () {
				return this._posts;
			};

			/**
			 * Bulk-load posts for certain type
			 *
			 * @param {Mixed} arg Either a page offset for paged request (optional - integer), or (Array)post IDs
			 *
			 * @return {Object} jQuery Deferred promise
			 */
			this.load_posts = function (arg) {
				this._posts = [];
				this._meta = {};
				var dfr = new $.Deferred(),
					me = this,
					ps = arg,
					_load_specific_posts = function (type) {
						return $.post(ajaxurl, {
							action: 'wds-load_exclusion_posts-posts_data-specific',
							type: type,
							posts: ps,
							_wds_nonce: _wds_postlist.nonce
						}, _.noop, 'json');
					},
					promise = _load_specific_posts(this.get_type());
				promise
					.done(function (data) {
						var posts = (data || {}).posts || [],
							meta = (data || {}).meta
						;
						_.each(posts, function (raw) {
							var post = new Wds.Postlist.Post();
							post.set(raw);
							me.add_post(post);
						});
						me._meta = meta;
						if (posts.length || !_.isEmpty(meta)) dfr.resolve();
					})
					.fail(dfr.reject)
					.always(function () {
						var state = dfr.state();
						if ('resolved' !== state && 'rejected' !== state) setTimeout(dfr.reject);
					})
				;
				return dfr.promise();
			};


			return this;
		},

		/**
		 * Post list rendering implementation
		 *
		 * @param {Object} $root Root element
		 * @param {String} $type List type
		 */
		List: function ($root, type) {

			var _list = new Wds.Postlist._List(type),
				_loaded = false,
				_list_template = Wds.tpl_compile(Wds.template('postlist', _list.get_type())),
				_post_item_template = Wds.tpl_compile(Wds.template('postlist', _list.get_type() + '-item')),
				_$box = $root.find(":text"),
				_selector = new Wds.Postlist.Selector(_list)
			;

			var _handlers = {
				/**
				 * Event propagation helper
				 *
				 * @param {Object} e Event
				 *
				 * @return {Boolean} Always false
				 */
				stop: function (e) {
					if (e && e.stopPropagation) e.stopPropagation();
					if (e && e.preventDefault) e.preventDefault();
				},

				remove: function (e) {
					var id = parseInt($(e.target).closest('tr').attr('data-id'), 10) || 0;
					if (!id) return _handlers.stop();

					_list.remove(id);
					sync();

					return _handlers.stop();
				},

				open_selector: function (e) {
					_selector.open(_handlers.selected);
					return _handlers.stop(e);
				},

				selected: function (pids) {
					if (!(pids || []).length) return false;
					_.each(pids, function (pid) {
						var post_data = _list.get_cached(pid),
							post;

						if (!post_data) {
							return;
						}
						post = new Wds.Postlist.Post();
						post.set(post_data);
						_list.add_post(post);
					});
					sync();
				}
			};

			var boot = function () {
				box_to_list();
				render();

				$root.on('click', 'a[rel="dialog"]', _handlers.open_selector);
				$root.on('click', 'a[href="#remove"]', _handlers.remove);
			};

			var destroy = function () {
				var selector = 'wds-postlist-list-' + _list.get_type();
				$root
					.find("." + selector).remove().end()
					.find(".wds-replaceable").show().end()
				;
				$root.off('click', 'a[rel="dialog"]');
				$root.off('click', 'a[href="#remove"]');
			};

			/**
			 * Actually renders the post list
			 */
			var render = function () {
				var selector = 'wds-postlist-list-' + _list.get_type(),
					out = ''
				;
				$root
					.find("." + selector).remove().end()
					.find(".wds-replaceable").hide().end()
				;
				_.each(_list.get_posts(), function (post) {
					out += _post_item_template(post.get());
				});
				$root.append(
					_list_template({posts: out, loaded: _loaded})
				);
			};

			/**
			 * Converts box content to internal post list representation
			 */
			var box_to_list = function () {
				var raw = _.map(_$box.val().split(','), $.trim),
					ps = []
				;
				_loaded = false;
				_.each(raw, function (pid) {
					pid = parseInt(pid, 10);
					if (!pid) return true;

					ps.push(pid);
				});
				
				if (!ps.length) {
					_loaded = true;
				} else {
					_list.load_posts(ps)
						.always(function () {
							_loaded = true;
						})
						.done(function () {
							setTimeout(render);
						})
						.fail(destroy)
					;
				}
			};

			/**
			 * Converts internal list post list to box representation
			 */
			var list_to_box = function () {
				var list = [];
				_.each(_list.get_posts(), function (post) {
					var pid = post.get_id();
					if (pid) list.push(pid);
				});
				_$box.val(list.join(", "));
			};

			/**
			 * Updates text box and re-renders the list
			 */
			var sync = function () {
				list_to_box();
				render();
			};

			return {
				boot: boot,
				sync: sync,
				destroy: destroy
			};

		},
		
		exclude: function ($el) {
			var list = new Wds.Postlist.List($el, 'exclude');
			list.boot();
		}
	};

})(jQuery);

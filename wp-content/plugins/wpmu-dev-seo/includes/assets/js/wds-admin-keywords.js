;(function ($) {

	window.Wds = window.Wds || {};

	window.Wds.Keywords = window.Wds.Keywords || {
		Pair: function ($el_or_keywords, url) {

			var _keywords = [],
				_url = ''
			;

			/**
			 * Checks if a pair is valid
			 *
			 * @return {Boolean}
			 */
			function is_valid () {
				return typeof _keywords === typeof [] &&
					!!_keywords.length &&
					typeof _url === typeof "" &&
					!!_url.length
				;
			}

			/**
			 * Gets pair URL
			 *
			 * @return {String} URL
			 */
			function get_url () { return _url || ''; }

			/**
			 * Gets pair keywords
			 *
			 * @return {Array} Keywords
			 */
			function get_keywords () { return _keywords || []; }

			/**
			 * Returns pair as string (for textarea syncs)
			 *
			 * @return {String} Pair as string
			 */
			function to_string () {
				var tmp = _.clone(get_keywords());
				tmp.push(get_url());
				return tmp.join(", ");
			}

			/**
			 * Initialize pair from arguments
			 *
			 * @param {Array} kw Array of keyword strings
			 * @param {String} url URL
			 */
			function init_from_source (kw, url) {
				_url = url;
				_keywords = typeof kw === typeof [] ? kw : [];
			}

			/**
			 * Initialize pair from DOM container element
			 */
			function init_from_element () {
				var $kws = $el_or_keywords.find(".wds-pair-keyword-field"),
					$lnk = $el_or_keywords.find(".wds-pair-url-field")
				;
				return init_from_source(
					_.map($kws.val().split(','), $.trim),
					$.trim($lnk.val())
				);
			}

			/**
			 * Dispatch pair initialization
			 */
			function init () {
				if ($el_or_keywords && $el_or_keywords.after && !url) init_from_element();
				else init_from_source($el_or_keywords, url);
			}
			init();

			return {
				is_valid: is_valid,
				get_url: get_url,
				get_keywords: get_keywords,
				to_string: to_string
			}
		},
		Custom: function ($root) {

			var _pairs = [],
				_template = Wds.tpl_compile(Wds.template('keywords', 'custom')),
				_pair_template = Wds.tpl_compile(Wds.template('keywords', 'pairs')),
				_form_template = Wds.tpl_compile(Wds.template('keywords', 'form')),
				_$textarea = $root.find("textarea"),
				_$group = $root.find(".wds-replaceable")
			;

			/**
			 * UI event handlers
			 *
			 * @type {Object}
			 */
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

				/**
				 * Pair removal click listener
				 *
				 * @param {Object} e Event
				 */
				remove: function (e) {
					if (!e) return false;
					var $el = $(e.target).closest(".wds-keyword-pair"),
						idx = parseInt($el.attr("data-idx"), 10) || false
					;
					if (!$el.length || !idx) return false;

					if (remove_pair(idx-1)) { // idx-1 because we're counting indices from 1 in data-idx
						sync();
					}

					return _handlers.stop(e);
				},

				/**
				 * Opens form dialog when adding new entry or editing an existing one
				 *
				 * @param {Object} e Event
				 */
				open_form: function (e) {
					if (!e) {
						return false;
					}

					var $this = $(e.target),
						overlay_id = 'wds-custom-keywords',
						overlay_selector = '#' + overlay_id,
						updated_form;

					if ($this.is('[href="#edit"]')) {
						var $el = $this.closest(".wds-keyword-pair"),
							$keywords = $el.find(".wds-pair-keyword-field"),
							$url = $el.find(".wds-pair-url-field"),
							keywords = $.trim($keywords.val()),
							url = $.trim($url.val());

						updated_form = _form_template({keywords: keywords, url: url, idx: $el.data('idx')});
					}
					else {
						updated_form = _form_template({keywords: '', url: '', idx: 0});
					}

					$(overlay_selector).replaceWith($(updated_form));
					Wds.open_dialog(overlay_id, {});

					return _handlers.stop(e);
				},

				/**
				 * Adds or updates pairs.
				 *
				 * @param {Object} e Event
				 */
				add_update: function (e) {
					var $button = $(e.target),
						$el = $button.closest('.sui-dialog'),
						$keywords = $el.find('input.wds-custom-keywords'),
						$url = $el.find('input.wds-custom-url'),
						$idx = $el.find('input.wds-custom-idx'),
						keywords = $.trim($keywords.val()),
						url = $.trim($url.val()),
						idx = parseInt($idx.val());

					if (idx > 0) {
						var $new_box = $(_pair_template({
							idx: idx,
							url: url,
							keywords: keywords
						}));

						$(".wds-keyword-pair[data-idx='" + idx + "']").replaceWith($new_box);

						var existing_pair = new Wds.Keywords.Pair($new_box);
						if (idx && existing_pair.is_valid()) {
							update_pair(idx - 1, existing_pair);
						}
					}
					else {
						if (keywords) {
							keywords = _.map(keywords.split(','), $.trim);
						}

						var new_pair = new Wds.Keywords.Pair(keywords, url);
						if (new_pair.is_valid()) {
							add_pair(new_pair);
						}
					}
					sync();

					window.Wds.close_dialog();
					return _handlers.stop(e);
				}
			};

			/**
			 * Initialize custom box
			 */
			function boot () {
				box_to_pairs();
				
				render();

				$root.on('click', 'a[href="#remove"]', _handlers.remove);
				$root.on('click', 'a[href="#edit"]', _handlers.open_form);
				$root.on('click', '.wds-keyword-pair-new button', _handlers.open_form);
				$root.on('click', '.wds-action-button', _handlers.add_update);
			}

			function render () {
				var out = '',
					$target = $root.find(".wds-keyword-pairs")
				;
				$target.remove();
				_.each(_pairs, function (pair, idx) {
					out += _pair_template({
						idx: idx + 1,
						url: pair.get_url(),
						keywords: pair.get_keywords()
					});
				});
				_$group
					.hide()
					.after(_template({
						pairs: out
					}))
				;

			}

			/**
			 * Parses textarea for pairs
			 *
			 * @return {Array} Array of Wds.Keywords.Pair object instances detected in textarea
			 */
			function parse_box () {
				var text = _$textarea.val(),
					raw = text.split(/\n/),
					result = []
				;
				_.each(raw, function (line) {
					var tmp = _(_.map(line.split(','), $.trim)),
						pair = new Wds.Keywords.Pair(tmp.initial(), tmp.last())
					;
					if (pair.is_valid()) result.push(pair);
				});

				return result;
			}

			/**
			 * Sets box content to the current pairs list
			 *
			 * @return {String} New box content
			 */
			function pairs_to_box () {
				var raw = [];
				_(_pairs).each(function (pair) {
					if (pair.is_valid()) raw.push(pair.to_string());
				});
				_$textarea.val(raw.join("\n"));
			}
			
			/**
			 * Adds parsed box pairs to current pairs list
			 *
			 * @return {Array} Array of pairs
			 */
			function box_to_pairs () {
				return _.map(parse_box(), function (pair) {
					add_pair(pair);
				});
			}

			/**
			 * Add pair to the internal queue
			 *
			 * @param {Object} pair Wds.Keywords.Pair object instance
			 */
			function add_pair (pair) {
				if (pair) _pairs.push(pair);
			}

			/**
			 * Removes pair in particular index location
			 *
			 * @param {Integer} idx Array index to drop
			 *
			 * @return {Boolean}
			 */
			function remove_pair (idx) {
				if (!_pairs[idx]) return false;
				_pairs.splice(idx, 1);
				return true;
			}

			/**
			 * Update individual pair in a collection
			 *
			 * @param {Integer} idx Array index to update
			 * @param {Object} pair Wds.Keywords.Pair object instance
			 *
			 * @return {Boolean}
			 */
			function update_pair (idx, pair) {
				if (!_pairs[idx]) return false;
				_pairs[idx] = pair;
				return true;
			}

			/**
			 * Syncs box with pairs and UI
			 */
			function sync () {
				pairs_to_box();
				render();
			}

			return {
				boot: boot,
				add: add_pair,
				sync: sync
			};
		},
		custom_pairs: function ($root, pairs) {
			var custom = new Wds.Keywords.Custom($root);
			if (pairs && typeof pairs === typeof []) _.each(pairs, function (pair) {
				custom.add(pair);
			});
			custom.boot();
		}
	};

})(jQuery);

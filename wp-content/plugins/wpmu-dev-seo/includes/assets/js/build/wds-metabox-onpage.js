"use strict";

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _wrapNativeSuper(Class) { var _cache = typeof Map === "function" ? new Map() : undefined; _wrapNativeSuper = function _wrapNativeSuper(Class) { if (Class === null || !_isNativeFunction(Class)) return Class; if (typeof Class !== "function") { throw new TypeError("Super expression must either be null or a function"); } if (typeof _cache !== "undefined") { if (_cache.has(Class)) return _cache.get(Class); _cache.set(Class, Wrapper); } function Wrapper() { return _construct(Class, arguments, _getPrototypeOf(this).constructor); } Wrapper.prototype = Object.create(Class.prototype, { constructor: { value: Wrapper, enumerable: false, writable: true, configurable: true } }); return _setPrototypeOf(Wrapper, Class); }; return _wrapNativeSuper(Class); }

function isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _construct(Parent, args, Class) { if (isNativeReflectConstruct()) { _construct = Reflect.construct; } else { _construct = function _construct(Parent, args, Class) { var a = [null]; a.push.apply(a, args); var Constructor = Function.bind.apply(Parent, a); var instance = new Constructor(); if (Class) _setPrototypeOf(instance, Class.prototype); return instance; }; } return _construct.apply(null, arguments); }

function _isNativeFunction(fn) { return Function.toString.call(fn).indexOf("[native code]") !== -1; }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

(function ($) {
  window.Wds = window.Wds || {};

  var MetaboxOnpageHelper =
  /*#__PURE__*/
  function () {
    function MetaboxOnpageHelper() {
      _classCallCheck(this, MetaboxOnpageHelper);
    }

    _createClass(MetaboxOnpageHelper, null, [{
      key: "get_title",
      value: function get_title() {
        return $('#wds_title').val();
      }
    }, {
      key: "get_description",
      value: function get_description() {
        return $('#wds_metadesc').val();
      }
    }, {
      key: "preview_loading",
      value: function preview_loading(loading) {
        var $preview = this.get_preview_el().find('.wds-preview-container'),
            loading_class = 'wds-preview-loading';

        if (loading) {
          $preview.addClass(loading_class);
        } else {
          $preview.removeClass(loading_class);
        }
      }
    }, {
      key: "get_preview_el",
      value: function get_preview_el() {
        return $('.wds-metabox-preview');
      }
    }, {
      key: "replace_preview_markup",
      value: function replace_preview_markup(new_markup) {
        this.get_preview_el().replaceWith(new_markup);
      }
    }, {
      key: "set_title_placeholder",
      value: function set_title_placeholder(placeholder) {
        $('#wds_title').attr('placeholder', placeholder);
      }
    }, {
      key: "set_desc_placeholder",
      value: function set_desc_placeholder(placeholder) {
        $('#wds_metadesc').attr('placeholder', placeholder);
      }
    }]);

    return MetaboxOnpageHelper;
  }();

  var MetaboxOnpage =
  /*#__PURE__*/
  function (_EventTarget) {
    _inherits(MetaboxOnpage, _EventTarget);

    function MetaboxOnpage() {
      var _this;

      _classCallCheck(this, MetaboxOnpage);

      _this = _possibleConstructorReturn(this, _getPrototypeOf(MetaboxOnpage).call(this));
      _this.editor = window.Wds.postEditor;

      _this.init();

      return _this;
    }

    _createClass(MetaboxOnpage, [{
      key: "init",
      value: function init() {
        var _this2 = this;

        this.editor.addEventListener('autosave', function (e) {
          return _this2.handle_autosave_event(e);
        });
        this.editor.addEventListener('content-change', function (e) {
          return _this2.handle_content_change_event(e);
        });
        $(document).on('input propertychange', '.wds-meta-field', _.debounce(function (e) {
          return _this2.handle_meta_change(e);
        }, 2000));
        $(window).on('load', function () {
          return _this2.handle_page_load();
        });
      }
    }, {
      key: "handle_autosave_event",
      value: function handle_autosave_event() {
        this.refresh_preview();
        this.refresh_placeholders();
      }
    }, {
      key: "handle_content_change_event",
      value: function handle_content_change_event() {
        this.refresh_preview();
        this.refresh_placeholders();
      }
    }, {
      key: "handle_meta_change",
      value: function handle_meta_change() {
        this.dispatch_meta_change_event();
        this.refresh_preview();
      }
    }, {
      key: "handle_page_load",
      value: function handle_page_load() {
        this.refresh_preview();
        this.refresh_placeholders();
      }
    }, {
      key: "refresh_preview",
      value: function refresh_preview() {
        MetaboxOnpageHelper.preview_loading(true);
        this.post('wds-metabox-preview', {
          wds_title: MetaboxOnpageHelper.get_title(),
          wds_description: MetaboxOnpageHelper.get_description(),
          post_id: this.editor.get_data().post_id,
          is_dirty: this.editor.is_post_dirty() ? 1 : 0
        }).done(function (data) {
          data = data || {};

          if (data.success) {
            MetaboxOnpageHelper.replace_preview_markup(data.markup);
          }
        }).always(function () {
          MetaboxOnpageHelper.preview_loading(false);
        });
      }
    }, {
      key: "refresh_placeholders",
      value: function refresh_placeholders() {
        this.post('wds_metabox_update', {
          id: this.editor.get_data().post_id,
          post: this.editor.get_data()
        }).done(function (data) {
          data = data || {};
          var description = data.description || '',
              title = data.title || '';
          MetaboxOnpageHelper.set_title_placeholder(title);
          MetaboxOnpageHelper.set_desc_placeholder(description);
        });
      }
    }, {
      key: "dispatch_meta_change_event",
      value: function dispatch_meta_change_event() {
        this.dispatchEvent(new Event('meta-change'));
      }
    }, {
      key: "post",
      value: function post(action, data) {
        data = $.extend({
          action: action,
          _wds_nonce: _wds_metabox_onpage.nonce
        }, data);
        return $.post(ajaxurl, data);
      }
    }]);

    return MetaboxOnpage;
  }(_wrapNativeSuper(EventTarget));

  window.Wds.metaboxOnpage = new MetaboxOnpage();
})(jQuery);
//# sourceMappingURL=wds-metabox-onpage.js.map

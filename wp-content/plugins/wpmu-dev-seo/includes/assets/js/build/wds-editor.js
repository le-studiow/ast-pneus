"use strict";

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _wrapNativeSuper(Class) { var _cache = typeof Map === "function" ? new Map() : undefined; _wrapNativeSuper = function _wrapNativeSuper(Class) { if (Class === null || !_isNativeFunction(Class)) return Class; if (typeof Class !== "function") { throw new TypeError("Super expression must either be null or a function"); } if (typeof _cache !== "undefined") { if (_cache.has(Class)) return _cache.get(Class); _cache.set(Class, Wrapper); } function Wrapper() { return _construct(Class, arguments, _getPrototypeOf(this).constructor); } Wrapper.prototype = Object.create(Class.prototype, { constructor: { value: Wrapper, enumerable: false, writable: true, configurable: true } }); return _setPrototypeOf(Wrapper, Class); }; return _wrapNativeSuper(Class); }

function isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _construct(Parent, args, Class) { if (isNativeReflectConstruct()) { _construct = Reflect.construct; } else { _construct = function _construct(Parent, args, Class) { var a = [null]; a.push.apply(a, args); var Constructor = Function.bind.apply(Parent, a); var instance = new Constructor(); if (Class) _setPrototypeOf(instance, Class.prototype); return instance; }; } return _construct.apply(null, arguments); }

function _isNativeFunction(fn) { return Function.toString.call(fn).indexOf("[native code]") !== -1; }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

(function ($) {
  window.Wds = window.Wds || {};

  var GutenbergEditor =
  /*#__PURE__*/
  function (_EventTarget) {
    _inherits(GutenbergEditor, _EventTarget);

    function GutenbergEditor() {
      var _this;

      _classCallCheck(this, GutenbergEditor);

      _this = _possibleConstructorReturn(this, _getPrototypeOf(GutenbergEditor).call(this));

      _this.init();

      return _this;
    }

    _createClass(GutenbergEditor, [{
      key: "init",
      value: function init() {
        this.hook_change_listener();
        this.register_api_fetch_middleware();
      }
    }, {
      key: "get_data",
      value: function get_data() {
        var _this2 = this;

        var fields = ['content', 'excerpt', 'post_author', 'post_id', 'post_title', 'post_type'],
            data = {};
        fields.forEach(function (field) {
          data[field] = _this2.get_editor().getEditedPostAttribute(field.replace('post_', '')) || '';
        });

        if (!data.post_id) {
          data.post_id = $('#post_ID').val() || 0;
        }

        return data;
      }
    }, {
      key: "get_editor",
      value: function get_editor() {
        return wp.data.select("core/editor");
      }
    }, {
      key: "dispatch_content_change_event",
      value: function dispatch_content_change_event() {
        this.dispatchEvent(new Event('content-change'));
      }
    }, {
      key: "dispatch_editor",
      value: function dispatch_editor() {
        return wp.data.dispatch("core/editor");
      }
    }, {
      key: "hook_change_listener",
      value: function hook_change_listener() {
        var _this3 = this;

        var debounced = _.debounce(function () {
          return _this3.dispatch_content_change_event();
        }, 10000);

        wp.data.subscribe(function () {
          if (_this3.get_editor().isEditedPostDirty() && !_this3.get_editor().isAutosavingPost() && !_this3.get_editor().isSavingPost()) {
            debounced();
          }
        });
      }
    }, {
      key: "register_api_fetch_middleware",
      value: function register_api_fetch_middleware() {
        var _this4 = this;

        if (!(wp || {}).apiFetch) {
          return;
        }

        wp.apiFetch.use(function (options, next) {
          var result = next(options);
          result.then(function () {
            if (_this4.is_autosave_request(options) || _this4.is_post_save_request(options)) {
              _this4.dispatch_autosave_event();
            }
          });
          return result;
        });
      }
    }, {
      key: "dispatch_autosave_event",
      value: function dispatch_autosave_event() {
        this.dispatchEvent(new Event('autosave'));
      }
    }, {
      key: "is_autosave_request",
      value: function is_autosave_request(request) {
        return request && request.path && request.path.includes('/autosaves');
      }
    }, {
      key: "is_post_save_request",
      value: function is_post_save_request(request) {
        var post = this.get_data(),
            post_id = post.post_id,
            post_type = post.post_type;
        return request && request.path && request.method === 'PUT' && request.path.includes('/' + post_id) && request.path.includes('/' + post_type);
      }
    }, {
      key: "autosave",
      value: function autosave() {
        // TODO: Keep track of this error: https://github.com/WordPress/gutenberg/issues/7416
        if (this.get_editor().isEditedPostAutosaveable()) {
          this.dispatch_editor().autosave();
        } else {
          this.dispatch_autosave_event();
        }
      }
    }, {
      key: "is_post_dirty",
      value: function is_post_dirty() {
        return this.get_editor().isEditedPostDirty();
      }
    }]);

    return GutenbergEditor;
  }(_wrapNativeSuper(EventTarget));

  window.Wds.postEditor = new GutenbergEditor();
})(jQuery);
//# sourceMappingURL=wds-editor.js.map

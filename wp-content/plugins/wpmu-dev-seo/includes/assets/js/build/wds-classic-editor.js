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

  var ClassicEditor =
  /*#__PURE__*/
  function (_EventTarget) {
    _inherits(ClassicEditor, _EventTarget);

    function ClassicEditor() {
      var _this;

      _classCallCheck(this, ClassicEditor);

      _this = _possibleConstructorReturn(this, _getPrototypeOf(ClassicEditor).call(this));

      _this.init();

      return _this;
    }

    _createClass(ClassicEditor, [{
      key: "init",
      value: function init() {
        var _this2 = this;

        $(document).on('input', 'input#title,textarea#content,textarea#excerpt', this.get_debounced_change_callback()).on('after-autosave.smartcrawl', function () {
          return _this2.dispatch_autosave_event();
        });
        $(window).on('load', function () {
          return _this2.hook_tinymce_change_listener();
        });
      }
    }, {
      key: "get_data",
      value: function get_data() {
        return wp.autosave.getPostData();
      }
    }, {
      key: "dispatch_content_change_event",
      value: function dispatch_content_change_event() {
        this.dispatchEvent(new Event('content-change'));
      }
    }, {
      key: "hook_tinymce_change_listener",
      value: function hook_tinymce_change_listener() {
        var editor = typeof tinymce !== 'undefined' && tinymce.get('content');

        if (editor) {
          editor.on('change', this.get_debounced_change_callback());
        }
      }
    }, {
      key: "get_debounced_change_callback",
      value: function get_debounced_change_callback() {
        var _this3 = this;

        return _.debounce(function () {
          return _this3.dispatch_content_change_event();
        }, 2000);
      }
    }, {
      key: "dispatch_autosave_event",
      value: function dispatch_autosave_event() {
        this.dispatchEvent(new Event('autosave'));
      }
      /**
       * When the classic editor is active and we trigger an autosave programmatically,
       * the heartbeat API is used for the autosave.
       *
       * To provide a seamless experience, this method temporarily removes our usual handler
       * and hooks a handler to the heartbeat event.
       */

    }, {
      key: "autosave",
      value: function autosave() {
        var _this4 = this;

        var handle_heartbeat = function handle_heartbeat() {
          _this4.dispatch_autosave_event(); // Re-hook our regular autosave handler


          $(document).on('after-autosave.smartcrawl', function () {
            return _this4.dispatch_autosave_event();
          });
        }; // We are already hooked to autosave so let's disable our regular autosave handler momentarily to avoid multiple calls ...


        $(document).off('after-autosave.smartcrawl'); // hook a new handler to heartbeat-tick.autosave

        $(document).one('heartbeat-tick.autosave', handle_heartbeat); // Save any changes pending in the editor to the textarea

        this.trigger_tinymce_save(); // Actually trigger the autosave

        wp.autosave.server.triggerSave();
      }
    }, {
      key: "trigger_tinymce_save",
      value: function trigger_tinymce_save() {
        var editorSync = (tinyMCE || {}).triggerSave;

        if (editorSync) {
          editorSync();
        }
      }
    }, {
      key: "is_post_dirty",
      value: function is_post_dirty() {
        return wp.autosave.server.postChanged();
      }
    }]);

    return ClassicEditor;
  }(_wrapNativeSuper(EventTarget));

  window.Wds.postEditor = new ClassicEditor();
})(jQuery);
//# sourceMappingURL=wds-classic-editor.js.map

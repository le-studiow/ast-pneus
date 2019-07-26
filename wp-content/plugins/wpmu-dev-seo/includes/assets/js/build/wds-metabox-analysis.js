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

  var MetaboxAnalysisHelper =
  /*#__PURE__*/
  function () {
    function MetaboxAnalysisHelper() {
      _classCallCheck(this, MetaboxAnalysisHelper);
    }

    _createClass(MetaboxAnalysisHelper, null, [{
      key: "get_focus_keyword_el",
      value: function get_focus_keyword_el() {
        return $('#wds_focus');
      }
    }, {
      key: "get_focus_keyword",
      value: function get_focus_keyword() {
        return this.get_focus_keyword_el().val();
      }
    }, {
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
      key: "get_metabox_el",
      value: function get_metabox_el() {
        return $("#wds-wds-meta-box");
      }
    }, {
      key: "get_seo_report_el",
      value: function get_seo_report_el() {
        return $('.wds-seo-analysis', this.get_seo_analysis_el());
      }
    }, {
      key: "get_readability_report_el",
      value: function get_readability_report_el() {
        return $(".wds-readability-report", this.get_readability_analysis_el());
      }
    }, {
      key: "get_postbox_fields_el",
      value: function get_postbox_fields_el() {
        return $('.wds-post-box-fields');
      }
    }, {
      key: "replace_seo_report",
      value: function replace_seo_report(new_report) {
        this.get_seo_report_el().replaceWith(new_report);
      }
    }, {
      key: "replace_readability_report",
      value: function replace_readability_report(new_report) {
        this.get_readability_report_el().replaceWith(new_report);
      }
    }, {
      key: "replace_post_fields",
      value: function replace_post_fields(new_fields) {
        this.get_postbox_fields_el().replaceWith(new_fields);
      }
    }, {
      key: "get_refresh_button_el",
      value: function get_refresh_button_el() {
        return $(".wds-refresh-analysis", this.get_metabox_el());
      }
    }, {
      key: "update_refresh_button",
      value: function update_refresh_button(enable) {
        this.get_refresh_button_el().attr('disabled', !enable);
      }
    }, {
      key: "get_seo_error_count",
      value: function get_seo_error_count() {
        return this.get_seo_report_el().data('errors');
      }
    }, {
      key: "get_readability_state",
      value: function get_readability_state() {
        return this.get_readability_report_el().data('readabilityState');
      }
    }, {
      key: "get_seo_analysis_el",
      value: function get_seo_analysis_el() {
        return $('.wds-seo-analysis-container', this.get_metabox_el());
      }
    }, {
      key: "get_readability_analysis_el",
      value: function get_readability_analysis_el() {
        return $('.wds-readability-analysis-container', this.get_metabox_el());
      }
    }, {
      key: "block_ui",
      value: function block_ui() {
        var $el = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
        var $container = this.get_analysis_containers();

        if ($el) {
          $el.addClass($el.is('button') ? 'sui-button-onload' : 'wds-item-loading');
        } else {
          $('.wds-report-inner', $container).hide();
          $('.wds-analysis-working', $container).show();
        }

        $('.wds-disabled-during-request', $container).prop('disabled', true);
      }
    }, {
      key: "unblock_ui",
      value: function unblock_ui() {
        var $container = this.get_analysis_containers();
        $('.wds-item-loading', $container).removeClass('wds-item-loading');
        $('.sui-button-onload', $container).removeClass('sui-button-onload');
        $('.wds-report-inner', $container).show();
        $('.wds-analysis-working', $container).hide();
        $('.wds-disabled-during-request', $container).prop('disabled', false);
      }
    }, {
      key: "get_analysis_containers",
      value: function get_analysis_containers() {
        return $('.wds-seo-analysis-container, .wds-readability-analysis-container', this.get_metabox_el());
      }
    }, {
      key: "update_focus_field_state",
      value: function update_focus_field_state(focusValid) {
        this.get_focus_container_el().removeClass('wds-focus-keyword-loaded wds-focus-keyword-invalid').addClass(focusValid ? 'wds-focus-keyword-loaded' : 'wds-focus-keyword-invalid');
      }
    }, {
      key: "get_focus_container_el",
      value: function get_focus_container_el() {
        return $('.wds-focus-keyword');
      }
    }]);

    return MetaboxAnalysisHelper;
  }();

  var MetaboxAnalysis =
  /*#__PURE__*/
  function (_EventTarget) {
    _inherits(MetaboxAnalysis, _EventTarget);

    function MetaboxAnalysis() {
      var _this;

      _classCallCheck(this, MetaboxAnalysis);

      _this = _possibleConstructorReturn(this, _getPrototypeOf(MetaboxAnalysis).call(this));
      _this.editor = window.Wds.postEditor;

      _this.init();

      return _this;
    }

    _createClass(MetaboxAnalysis, [{
      key: "init",
      value: function init() {
        var _this2 = this;

        this.editor.addEventListener('autosave', function () {
          return _this2.refresh_analysis();
        });
        $(document).on('click', '.wds-refresh-analysis', function (e) {
          return _this2.handle_refresh_click(e);
        }).on('click', '.wds-seo-analysis-container .wds-ignore', function (e) {
          return _this2.handle_ignore_click(e);
        }).on('click', '.wds-seo-analysis-container .wds-unignore', function (e) {
          return _this2.handle_unignore_click(e);
        }).on('click', '.wds-readability-analysis-container .wds-ignore', function (e) {
          return _this2.handle_ignore_click(e);
        }).on('click', '.wds-readability-analysis-container .wds-unignore', function (e) {
          return _this2.handle_unignore_click(e);
        }).on('input propertychange', '.wds-focus-keyword input', _.debounce(function (e) {
          return _this2.handle_focus_keywords_change(e);
        }, 2000));
        $(window).on('load', function () {
          return _this2.hook_meta_change_listener();
        }) // Hook meta change listener as late as possible
        .on('load', function () {
          return _this2.handle_page_load();
        });
      }
    }, {
      key: "refresh_analysis",
      value: function refresh_analysis() {
        var _this3 = this;

        var focusKeyword = MetaboxAnalysisHelper.get_focus_keyword();
        return this.post('wds-analysis-get-editor-analysis', {
          post_id: this.editor.get_data().post_id,
          is_dirty: this.editor.is_post_dirty() ? 1 : 0,
          wds_title: MetaboxAnalysisHelper.get_title(),
          wds_description: MetaboxAnalysisHelper.get_description(),
          wds_focus_keywords: focusKeyword
        }).done(function (response) {
          if (!(response || {}).success) {
            return false;
          }

          var data = (response || {}).data,
              seo_report = (data || {}).seo || '',
              readability_report = (data || {}).readability || '',
              post_fields = (data || {}).postbox_fields || '';
          MetaboxAnalysisHelper.replace_seo_report(seo_report);
          MetaboxAnalysisHelper.replace_readability_report(readability_report);
          MetaboxAnalysisHelper.replace_post_fields(post_fields);
          var seo_errors = MetaboxAnalysisHelper.get_seo_error_count(),
              readability_state = MetaboxAnalysisHelper.get_readability_state();

          _this3.dispatch_seo_update_event(seo_errors);

          _this3.dispatch_readability_update_event(readability_state);
        }).always(function () {
          MetaboxAnalysisHelper.unblock_ui();
          var focusValid = !!(focusKeyword && focusKeyword.length);
          MetaboxAnalysisHelper.update_focus_field_state(focusValid);
          MetaboxAnalysisHelper.update_refresh_button(true);
        });
      }
    }, {
      key: "handle_refresh_click",
      value: function handle_refresh_click(e) {
        this.prevent_default(e);
        this.dispatch_event('before-analysis-refresh');
        MetaboxAnalysisHelper.block_ui();
        this.editor.autosave();
      }
    }, {
      key: "handle_ignore_click",
      value: function handle_ignore_click(e) {
        var _this4 = this;

        this.prevent_default(e);
        var $button = $(e.target).closest('button'),
            check_id = $button.attr('data-check_id');
        MetaboxAnalysisHelper.block_ui($button);
        return this.change_ignore_status(check_id, true).done(function () {
          return _this4.refresh_analysis();
        });
      }
    }, {
      key: "handle_unignore_click",
      value: function handle_unignore_click(e) {
        var _this5 = this;

        this.prevent_default(e);
        var $button = $(e.target).closest('button'),
            check_id = $button.attr('data-check_id');
        MetaboxAnalysisHelper.block_ui($button);
        return this.change_ignore_status(check_id, false).done(function () {
          return _this5.refresh_analysis();
        });
      }
    }, {
      key: "handle_focus_keywords_change",
      value: function handle_focus_keywords_change() {
        this.dispatch_event('before-focus-keyword-update');
        MetaboxAnalysisHelper.block_ui(MetaboxAnalysisHelper.get_focus_container_el());
        this.refresh_analysis();
      }
    }, {
      key: "hook_meta_change_listener",
      value: function hook_meta_change_listener() {
        var _this6 = this;

        var metaboxOnpage = window.Wds.metaboxOnpage;

        if (metaboxOnpage) {
          metaboxOnpage.addEventListener('meta-change', function () {
            return _this6.refresh_analysis();
          });
        }
      }
    }, {
      key: "handle_page_load",
      value: function handle_page_load() {
        this.dispatch_event('before-analysis-refresh');
        MetaboxAnalysisHelper.block_ui();
        this.refresh_analysis();
      }
    }, {
      key: "post",
      value: function post(action, data) {
        data = $.extend({
          action: action,
          _wds_nonce: _wds_metabox_analysis.nonce
        }, data);
        return $.post(ajaxurl, data);
      }
    }, {
      key: "change_ignore_status",
      value: function change_ignore_status(check_id, ignore) {
        this.dispatch_event('before-ignore-status-change');
        var action = !!ignore ? 'wds-analysis-ignore-check' : 'wds-analysis-unignore-check';
        return this.post(action, {
          post_id: this.editor.get_data().post_id,
          check_id: check_id
        });
      }
    }, {
      key: "prevent_default",
      value: function prevent_default(event) {
        if (event && event.preventDefault && event.stopPropagation) {
          event.preventDefault();
          event.stopPropagation();
        }
      }
    }, {
      key: "dispatch_event",
      value: function dispatch_event(event) {
        this.dispatchEvent(new Event(event));
      }
    }, {
      key: "dispatch_seo_update_event",
      value: function dispatch_seo_update_event(error_count) {
        var event = new CustomEvent('after-seo-analysis-update', {
          detail: {
            errors: error_count
          }
        });
        this.dispatchEvent(event);
      }
    }, {
      key: "dispatch_readability_update_event",
      value: function dispatch_readability_update_event(state) {
        var event = new CustomEvent('after-readability-analysis-update', {
          detail: {
            state: state
          }
        });
        this.dispatchEvent(event);
      }
    }]);

    return MetaboxAnalysis;
  }(_wrapNativeSuper(EventTarget));

  window.Wds.metaboxAnalysis = new MetaboxAnalysis();
})(jQuery);
//# sourceMappingURL=wds-metabox-analysis.js.map

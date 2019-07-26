"use strict";

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

(function ($) {
  /**
   * The class responsible for interaction with the crawl report UI. Changes to the markup should ideally affect this class only.
   */
  var URLCrawlerReportUI =
  /*#__PURE__*/
  function () {
    function URLCrawlerReportUI() {
      _classCallCheck(this, URLCrawlerReportUI);
    }

    _createClass(URLCrawlerReportUI, null, [{
      key: "get_open_section",
      value: function get_open_section() {
        return $('.sui-accordion-item--open');
      }
    }, {
      key: "get_open_section_type",
      value: function get_open_section_type() {
        return URLCrawlerReportUI.get_open_section().data('type');
      }
    }, {
      key: "is_ignored_tab_open",
      value: function is_ignored_tab_open() {
        return URLCrawlerReportUI.get_open_section().find('[data-tabs] .active').is('.ignored');
      }
    }, {
      key: "replace_report_markup",
      value: function replace_report_markup(new_markup) {
        $('.wds-crawl-results-report').replaceWith(new_markup);
      }
    }, {
      key: "replace_summary_markup",
      value: function replace_summary_markup(new_markup) {
        $('#wds-crawl-summary-container').html(new_markup);
      }
    }, {
      key: "get_issue_id",
      value: function get_issue_id($context) {
        return $context.closest('[data-issue-id]').data('issueId');
      }
    }, {
      key: "get_path",
      value: function get_path($context) {
        return $context.closest('[data-path]').data('path');
      }
    }, {
      key: "get_redirect_path",
      value: function get_redirect_path($context) {
        return $context.closest('[data-redirect-path]').data('redirectPath');
      }
    }, {
      key: "get_issue_ids",
      value: function get_issue_ids($context) {
        var $group_container = $context.closest('.wds-crawl-issues-table'),
            $issue_container = $group_container.length ? $group_container : $context.closest('.tab_url_crawler'),
            $issues = $issue_container.find('[data-issue-id]'),
            issue_ids = [];
        $issues.each(function (index, issue) {
          issue_ids.push($(issue).data('issueId'));
        });
        return issue_ids;
      }
    }, {
      key: "block_ui",
      value: function block_ui($target_el) {
        if ($target_el.closest('.wds-links-dropdown').length) {
          $target_el = $target_el.closest('.wds-links-dropdown').find('.sui-dropdown-anchor');
        }

        if ($target_el.is('.sui-button-onload')) {
          // Already blocked
          return;
        }

        $target_el.addClass('sui-button-onload');
        $('.wds-disabled-during-request').prop('disabled', true);
      }
    }, {
      key: "unblock_ui",
      value: function unblock_ui() {
        $('.wds-disabled-during-request').prop('disabled', false);
        $('.sui-button-onload').removeClass('sui-button-onload');
      }
    }, {
      key: "show_sitemap_message",
      value: function show_sitemap_message($message, $context) {
        var $tabs = $context.closest('.sui-tabs');

        if (!$tabs.length) {
          return;
        }

        $tabs.prev('.wds-notice').remove();
        $message.insertBefore($tabs);
      }
    }, {
      key: "get_sitemap_path",
      value: function get_sitemap_path($context) {
        return $context.closest('[data-issue-id]').data('path');
      }
    }, {
      key: "get_sitemap_paths",
      value: function get_sitemap_paths($context) {
        var $container = $context.closest('.wds-crawl-issues-table'),
            $issues = $container.find('[data-issue-id]'),
            paths = [];
        $issues.each(function (index, issue) {
          paths.push($(issue).data('path'));
        });
        return paths;
      }
    }, {
      key: "get_dialog",
      value: function get_dialog($context) {
        return $context.closest('.sui-dialog');
      }
    }, {
      key: "get_redirect_data",
      value: function get_redirect_data($context) {
        var $modal = URLCrawlerReportUI.get_dialog($context),
            $fields = $modal.find("input"),
            data = {};
        $fields.each(function () {
          var $me = $(this);
          data[$me.attr("name")] = $me.val();
        });
        return data;
      }
    }, {
      key: "open_dialog",
      value: function open_dialog(dialog_id) {
        var dialog_el = $('#' + dialog_id),
            dialog = dialog_el.length ? new A11yDialog(dialog_el[0]) : false;

        if (dialog) {
          dialog.show();
        }
      }
    }, {
      key: "close_dialog",
      value: function close_dialog($context) {
        URLCrawlerReportUI.get_dialog($context).find(".sui-dialog-close").click();
      }
    }, {
      key: "replace_dialog_markup",
      value: function replace_dialog_markup(dialog, markup) {
        $('#' + dialog).replaceWith(markup);
      }
    }, {
      key: "validate_dialog",
      value: function validate_dialog(dialog_id) {
        var is_valid = true;
        $('.sui-form-field', $('#' + dialog_id)).each(function () {
          var $form_field = $(this),
              $input = $('input', $form_field);

          if (!$input.val()) {
            is_valid = false;
            $form_field.addClass('sui-form-field-error');
            $input.on('focus keydown', function () {
              $(this).closest('.sui-form-field-error').removeClass('sui-form-field-error');
            });
          }
        });
        return is_valid;
      }
    }]);

    return URLCrawlerReportUI;
  }();
  /**
   * The class containing the business logic for the crawl report.
   *
   * @see URLCrawlerReportUI
   */


  var URLCrawlerReport =
  /*#__PURE__*/
  function () {
    function URLCrawlerReport() {
      _classCallCheck(this, URLCrawlerReport);
    }

    _createClass(URLCrawlerReport, [{
      key: "init",
      value: function init() {
        var _this = this;

        $(document).on('click', '[href="#ignore"]', function (e) {
          return _this.handle_ignore_single_action(e);
        }).on('click', '.wds-crawl-issues-table .wds-ignore-all', function (e) {
          return _this.handle_ignore_group_action(e);
        }).on('click', '.sui-box-header .wds-ignore-all', function (e) {
          return _this.handle_ignore_all_action(e);
        }).on('click', '.wds-ignored-items-table .wds-unignore', function (e) {
          return _this.handle_restore_single_action(e);
        }).on('click', '[href="#add-to-sitemap"]', function (e) {
          return _this.handle_add_single_to_sitemap_action(e);
        }).on('click', '.wds-crawl-issues-table .wds-add-all-to-sitemap', function (e) {
          return _this.handle_add_all_to_sitemap_action(e);
        }).on('click', '[href="#occurrences"]', function (e) {
          return _this.handle_open_occurrences_dialog_action(e);
        }).on('click', '[href="#redirect"]', function (e) {
          return _this.handle_open_redirect_dialog_action(e);
        }).on('click', '.wds-submit-redirect', function (e) {
          return _this.handle_save_redirect_action(e);
        });
        this.templates = {
          redirect_dialog: Wds.tpl_compile(Wds.template('url_crawler', 'redirect_dialog')),
          occurrences_dialog: Wds.tpl_compile(Wds.template('url_crawler', 'occurrences_dialog')),
          issue_occurrences: Wds.tpl_compile(Wds.template('url_crawler', 'issue_occurrences'))
        };
        this.occurrences_request = new $.Deferred();
      }
    }, {
      key: "post",
      value: function post(action, data) {
        data = $.extend({
          action: action,
          _wds_nonce: _wds_sitemaps.nonce
        }, data);
        return $.post(ajaxurl, data);
      }
    }, {
      key: "reload_report",
      value: function reload_report() {
        var _this2 = this;

        return this.post('wds-get-sitemap-report', {
          open_type: URLCrawlerReportUI.get_open_section_type(),
          ignored_tab_open: URLCrawlerReportUI.is_ignored_tab_open() ? 1 : 0
        }).done(
        /**
         * @param {{summary_markup:string, markup:string}} data
         */
        function (data) {
          data = data || {};

          if (data.success && data.markup) {
            URLCrawlerReportUI.replace_report_markup(data.markup);
            URLCrawlerReportUI.replace_summary_markup(data.summary_markup);
            URLCrawlerReportUI.unblock_ui();
            $(_this2).trigger('wds_url_crawler_report:reloaded');
          }
        });
      }
    }, {
      key: "change_issue_status",
      value: function change_issue_status(issue_id, action) {
        var _this3 = this;

        return this.post(action, {
          issue_id: issue_id
        }).done(function (data) {
          var status = parseInt((data || {}).status || '0', 10);

          if (status > 0) {
            _this3.reload_report();
          }
        });
      }
    }, {
      key: "ignore_issue",
      value: function ignore_issue(issue_id) {
        return this.change_issue_status(issue_id, 'wds-service-ignore');
      }
    }, {
      key: "restore_issue",
      value: function restore_issue(issue_id) {
        return this.change_issue_status(issue_id, 'wds-service-unignore');
      }
    }, {
      key: "handle_ignore_single_action",
      value: function handle_ignore_single_action(e) {
        e.preventDefault();
        var $target = $(e.target),
            issue_id = URLCrawlerReportUI.get_issue_id($target);
        URLCrawlerReportUI.block_ui($target);
        return this.ignore_issue(issue_id);
      }
    }, {
      key: "handle_restore_single_action",
      value: function handle_restore_single_action(e) {
        e.preventDefault();
        var $target = $(e.target),
            issue_id = URLCrawlerReportUI.get_issue_id($target);
        URLCrawlerReportUI.block_ui($target);
        return this.restore_issue(issue_id);
      }
    }, {
      key: "handle_ignore_group_action",
      value: function handle_ignore_group_action(e) {
        e.preventDefault();
        var $target = $(e.target),
            issue_ids = URLCrawlerReportUI.get_issue_ids($target);
        URLCrawlerReportUI.block_ui($target);
        return this.ignore_issue(issue_ids);
      }
    }, {
      key: "handle_ignore_all_action",
      value: function handle_ignore_all_action(e) {
        e.preventDefault();
        var $target = $(e.target),
            issue_ids = URLCrawlerReportUI.get_issue_ids($target);
        URLCrawlerReportUI.block_ui($target);
        return this.ignore_issue(issue_ids);
      }
    }, {
      key: "add_to_sitemap",
      value: function add_to_sitemap(path, $context) {
        return this.post('wds-sitemap-add_extra', {
          path: path
        }).done(
        /**
         * @param {{status:string, add_all_message:string}} data
         */
        function (data) {
          data = data || {};
          var status = parseInt(data.status || '0', 10);

          if (status > 0) {
            var $message = $(data.add_all_message || '');
            URLCrawlerReportUI.show_sitemap_message($message, $context);
            URLCrawlerReportUI.unblock_ui();
          }
        });
      }
    }, {
      key: "handle_add_single_to_sitemap_action",
      value: function handle_add_single_to_sitemap_action(e) {
        e.preventDefault();
        var $target = $(e.target),
            path = URLCrawlerReportUI.get_sitemap_path($target);
        URLCrawlerReportUI.block_ui($target);
        return this.add_to_sitemap(path, $target);
      }
    }, {
      key: "handle_add_all_to_sitemap_action",
      value: function handle_add_all_to_sitemap_action(e) {
        e.preventDefault();
        var $target = $(e.target),
            path = URLCrawlerReportUI.get_sitemap_paths($target);
        URLCrawlerReportUI.block_ui($target);
        return this.add_to_sitemap(path, $target);
      }
    }, {
      key: "load_issue_occurrences",
      value: function load_issue_occurrences(issue_id) {
        var deferred = new $.Deferred();
        this.post('wds-load-issue-occurrences', {
          issue_id: issue_id
        }).done(function (response) {
          if (deferred.state() !== 'pending') {
            return;
          }

          var success = (response || {}).success || false,
              data = (response || {}).data || {};

          if (success && data.occurrences) {
            deferred.resolve(data.occurrences);
          } else {
            deferred.reject();
          }
        }).fail(deferred.reject);
        return deferred;
      }
    }, {
      key: "handle_open_occurrences_dialog_action",
      value: function handle_open_occurrences_dialog_action(e) {
        var _this4 = this;

        e.preventDefault(); // Cancel the previous request if it's still in progress

        if (this.occurrences_request.state() === 'pending') {
          this.occurrences_request.reject();
        }

        var $target = $(e.target),
            issue_id = URLCrawlerReportUI.get_issue_id($target),
            path = URLCrawlerReportUI.get_path($target),
            markup = this.templates.occurrences_dialog({
          'issue_id': issue_id,
          'issue_path': path,
          'issue_occurrences': ''
        });
        URLCrawlerReportUI.replace_dialog_markup('wds-issue-occurrences', markup);
        URLCrawlerReportUI.open_dialog('wds-issue-occurrences');
        this.occurrences_request = this.load_issue_occurrences(issue_id).done(function (occurrences) {
          var occurrences_markup = _this4.templates.issue_occurrences({
            occurrences: occurrences
          });

          $('#wds-issue-occurrences').find('.wds-issue-occurrences').html(occurrences_markup);
        });
      }
    }, {
      key: "handle_open_redirect_dialog_action",
      value: function handle_open_redirect_dialog_action(e) {
        e.preventDefault();
        var $target = $(e.target),
            issue_id = URLCrawlerReportUI.get_issue_id($target),
            path = URLCrawlerReportUI.get_path($target),
            redirect_path = URLCrawlerReportUI.get_redirect_path($target),
            markup = this.templates.redirect_dialog({
          'issue_id': issue_id,
          'issue_path': path,
          'issue_redirect_path': redirect_path
        });
        URLCrawlerReportUI.replace_dialog_markup('wds-issue-redirect', markup);
        URLCrawlerReportUI.open_dialog('wds-issue-redirect');
      }
    }, {
      key: "handle_save_redirect_action",
      value: function handle_save_redirect_action(e) {
        var _this5 = this;

        e.preventDefault();
        var is_valid = URLCrawlerReportUI.validate_dialog('wds-issue-redirect');

        if (!is_valid) {
          return;
        }

        var $target = $(e.target),
            issue_id = URLCrawlerReportUI.get_issue_id($target),
            redirect_data = URLCrawlerReportUI.get_redirect_data($target);
        URLCrawlerReportUI.block_ui($target);
        return this.post('wds-service-redirect', redirect_data).always(function () {
          URLCrawlerReportUI.close_dialog($target);

          _this5.ignore_issue(issue_id);
        });
      }
    }]);

    return URLCrawlerReport;
  }();

  window.Wds = window.Wds || {};
  window.Wds.URLCrawlerReport = new URLCrawlerReport();
})(jQuery);
//# sourceMappingURL=wds-url-crawler-report.js.map

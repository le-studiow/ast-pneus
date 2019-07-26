// the semi-colon before function invocation is a safety net against concatenated
// scripts and/or other plugins which may not be closed properly.
;// noinspection JSUnusedLocalSymbols
(function ($, window, document, undefined) {

	"use strict";

	// undefined is used here as the undefined global variable in ECMAScript 3 is
	// mutable (ie. it can be changed by someone else). undefined isn't really being
	// passed in so we can ensure the value of it is truly undefined. In ES5, undefined
	// can no longer be modified.

	// window and document are passed through as local variables rather than global
	// as this (slightly) quickens the resolution process and can be more efficiently
	// minified (especially when both are regularly referenced in your plugin).

	// Create the defaults once
	var pluginName = "forminatorFrontPayment",
	    defaults   = {
		    type: 'stripe',
		    paymentEl: null,
		    paymentRequireSsl: false,
		    generalMessages: {},
	    };

	// The actual plugin constructor
	function ForminatorFrontPayment(element, options) {
		this.element = element;
		this.$el     = $(this.element);

		// jQuery has an extend method which merges the contents of two or
		// more objects, storing the result in the first object. The first object
		// is generally empty as we don't want to alter the default options for
		// future instances of the plugin
		this.settings              = $.extend({}, defaults, options);
		this._defaults             = defaults;
		this._name                 = pluginName;
		this._stripeHandler        = null;
		this._stripeData           = null;
		this._beforeSubmitCallback = null;
		this.init();
	}

	// Avoid Plugin.prototype conflicts
	$.extend(ForminatorFrontPayment.prototype, {
		init: function () {
			if (!this.settings.paymentEl) {
				return;
			}

			var self         = this;
			this._stripeData = this.settings.paymentEl.data();

			this.configure();

			$(this.element).on('payment.before.submit.forminator', function (e, formData, callback) {
				self._beforeSubmitCallback = callback;
				self.beforeSubmit(e, formData);
			});

			// Close Checkout on page navigation:
			window.addEventListener('popstate', function () {
				self.onPopState();
			});
		},

		replaceEmailTags: function (value) {
			if(value.indexOf('{') > -1 ){
				value = value.replace(/[{}]/g, '');
			}

			return value.trim();
		},
		isValidEmail: function (email){
			return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
		},
		configure: function () {
			if (this.settings.type === 'stripe' && typeof StripeCheckout !== 'undefined') {
				this.configureStripe();
			}

		},

		configureStripe: function () {
			var self   = this;
			var config = {
				key: this.getStripeData('key'),
				locale: this.getStripeData('locale'),
				currency: this.getStripeData('currency'),
				panelLabel: this.getStripeData('label'),
				allowRememberMe: this.getStripeData('allowRememberMe'),
			};

			if (this.getStripeData('image')) {
				config.image = this.getStripeData('image');
			}

			if (this.getStripeData('name')) {
				config.name = this.getStripeData('name');
			}

			if (this.getStripeData('description')) {
				config.description = this.getStripeData('description');
			}

			if (this.getStripeData('billingAddress')) {
				config.billingAddress = true;
			}

			if (this.getStripeData('shippingAddress')) {
				config.shippingAddress = true;
			}


			if (this.getStripeData('zipCode')) {
				config.zipCode = true;
			}

			config.token = function (token, args) {
				// You can access the token ID with `token.id`.
				// Get the token ID to your server-side code for use.
				self.stripeToken(token, args);
			};

			this._stripeHandler = StripeCheckout.configure(config);
		},

		getStripeData: function (key) {
			if (typeof this._stripeData[key] !== 'undefined') {
				return this._stripeData[key];
			}

			return null;
		},

		stripeToken: function (token, args) {
			this.settings.paymentEl.val(token.id);

			if (this._beforeSubmitCallback) {
				this._beforeSubmitCallback.call();
			}
		},

		beforeSubmit: function (e, formData) {
			this.settings.paymentEl.val('');

			if (this.settings.paymentRequireSsl) {
				if ('https:' !== location.protocol) {
					var $target_message = this.$el.find('.forminator-response-message');
					$target_message.html('');
					$target_message.html('<label class="forminator-label--error"><span>' + this.settings.generalMessages.payment_require_ssl_error + '</span></label>');
					var forminatorFrontSubmit = this.$el.data('forminatorFrontSubmit');
					if (typeof forminatorFrontSubmit !== 'undefined' && forminatorFrontSubmit) {
						forminatorFrontSubmit.focus_to_element($target_message);
					}
					return false;
				}
			}

			if (this.settings.type === 'stripe') {
				this.beforeSubmitStripe(e, formData);
			}
		},

		beforeSubmitStripe: function (e, formData) {
			var amount     = 0;
			var amountType = this.getStripeData('amountType');
			if (amountType === 'fixed') {
				amount = this.getStripeData('amount');
			} else {
				amount = this.get_field_calculation(this.getStripeData('amount'));
			}

			var currency = this.getStripeData('currency');
			var email 	 = this.getStripeData('email');

			// @see https://stripe.com/docs/currencies#zero-decimal
			if ('jpy' !== currency) {
				amount = amount * 100;
			}
			var config = {
				amount: amount
			};
			if ( email && !this.isValidEmail(email) ) {
				var field = this.replaceEmailTags(email),
					email_prefill = formData.get(field);
				if (email_prefill) {
					config.email = email_prefill;
				}
			}else{
				config.email = email;
			}

			this._stripeHandler.open(config);
		},

		onPopState: function () {
			if (this.settings.type === 'stripe' && this._stripeHandler) {
				this._stripeHandler.close();
			}
		},

		// taken from forminatorFrontCondition
		get_form_field: function (element_id) {
			//find element by suffix -field on id input (default behavior)
			var $element = this.$el.find('#' + element_id + '-field');
			if ($element.length === 0) {
				//find element by its on name (for radio on singlevalue)
				$element = this.$el.find('input[name=' + element_id + ']');
				if ($element.length === 0) {
					// for text area that have uniqid, so we check its name instead
					$element = this.$el.find('textarea[name=' + element_id + ']');
					if ($element.length === 0) {
						//find element by its on name[] (for checkbox on multivalue)
						$element = this.$el.find('input[name="' + element_id + '[]"]');
						if ($element.length === 0) {
							//find element by direct id (for name field mostly)
							//will work for all field with element_id-[somestring]
							$element = this.$el.find('#' + element_id);
						}
					}
				}
			}

			return $element;
		},

		get_field_value: function (element_id) {
			var $element = this.get_form_field(element_id);
			var value    = '';
			var checked  = null;

			if (this.field_is_radio($element)) {
				checked = $element.filter(":checked");
				if (checked.length) {
					value = checked.val();
				}
			} else if (this.field_is_checkbox($element)) {
				$element.each(function () {
					if ($(this).is(':checked')) {
						value = $(this).val();
					}
				});

			} else if (this.field_is_select($element)) {
				value = $element.val();
			} else {
				value = $element.val()
			}

			return value;
		},

		get_field_calculation: function (element_id) {
			var $element    = this.get_form_field(element_id);
			var value       = 0;
			var calculation = 0;
			var checked     = null;

			if (this.field_is_radio($element)) {
				checked = $element.filter(":checked");
				if (checked.length) {
					calculation = checked.data('calculation');
					if (calculation !== undefined) {
						value = Number(calculation);
					}
				}
			} else if (this.field_is_checkbox($element)) {
				$element.each(function () {
					if ($(this).is(':checked')) {
						calculation = $(this).data('calculation');
						if (calculation !== undefined) {
							value += Number(calculation);
						}
					}
				});

			} else if (this.field_is_select($element)) {
				checked = $element.find("option").filter(':selected');
				if (checked.length) {
					calculation = checked.data('calculation');
					if (calculation !== undefined) {
						value = Number(calculation);
					}
				}
			} else {
				value = Number($element.val());
			}

			return isNaN(value) ? 0 : value;
		},

		field_is_radio: function ($element) {
			var is_radio = false;
			$element.each(function () {
				if ($(this).attr('type') === 'radio') {
					is_radio = true;
					//break
					return false;
				}
			});

			return is_radio;
		},

		field_is_checkbox: function ($element) {
			var is_checkbox = false;
			$element.each(function () {
				if ($(this).attr('type') === 'checkbox') {
					is_checkbox = true;
					//break
					return false;
				}
			});

			return is_checkbox;
		},

		field_is_select: function ($element) {
			return $element.is('select');
		},


	});

	// A really lightweight plugin wrapper around the constructor,
	// preventing against multiple instantiations
	$.fn[pluginName] = function (options) {
		return this.each(function () {
			if (!$.data(this, pluginName)) {
				$.data(this, pluginName, new ForminatorFrontPayment(this, options));
			}
		});
	};

})(jQuery, window, document);

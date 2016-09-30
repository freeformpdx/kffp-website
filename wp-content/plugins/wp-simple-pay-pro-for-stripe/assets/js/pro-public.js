// Public JS - SP Pro

/* global simplePayFrontendGlobals, simplePayFormSettings, StripeCheckout, accounting */

// Define global-scope variable to use in base plugins and add-ons.
var spApp = {};

(function( $ ) {
	'use strict';

	var body = $( document.body );

	spApp = {

		// Collection of DOM elements of all payment forms
		spFormElList: {},

		// Internal organized collection of all form data
		spFormData: {},

		// *** Main Functions ***/

		init: function() {

			this.debugLog( 'spApp.init', this );
			this.debugLog( 'simplePayFrontendGlobals', simplePayFrontendGlobals );
			this.debugLog( 'simplePayFormSettings', simplePayFormSettings );

			this.spFormElList = body.find( '.sc-checkout-form' );

			// Loop through and initialize each form.
			this.spFormElList.each( function() {

				var spFormEl = $( this );
				spApp.processForm( spFormEl );
				spApp.setupValidation( spFormEl );

				// Trigger each user-entered amount (UEA) input field (first in form) to store amount and set total amount labels.
				spFormEl.find( '.sc-uea-custom-amount:first' ).trigger( 'change.spUserEnteredAmount' );

				// Trigger each quantity input field (first in form) to store value and set total amount labels.
				spFormEl.find( '.sc-cf-quantity:first' ).trigger( 'change.spQuantity' );

				// Trigger amount select input field (first in form) to store amount and set total amount labels.
				spFormEl.find( '.sc-cf-amount:first' ).trigger( 'change.spSelectAmount' );

				// Set data property of button to hold original text.
				// Needed for back button in Safari and other browsers (not Chrome).
				var paymentBtn = spFormEl.find( '.sc-payment-btn' );
				paymentBtn
					.prop( 'disabled', false )
					.data( 'textOriginal', paymentBtn.find( 'span' ).text() );
			} );

			// Trigger event handlers on first load.

			this.initCustomFields();

			// Trigger custom event marking when base plugin init is done.
			body.trigger( 'spBaseInitComplete' );
		},

		// Process individual form
		processForm: function( spFormEl ) {

			// Get internal numeric ID (should start from 1).
			var formId = spFormEl.data( 'sc-id' );
			var wpForm = simplePayFormSettings[ formId ];
			var defaultAmount = wpForm.amount || 0;

			// Store data for this form from both localized values from wp_localize_script and DOM element values.
			this.spFormData[ formId ] = {

				// Set checkout form data from localized values (simplePayFormSettings).

				// Non-boolean properties sent to stripeParams
				stripeKey: wpForm.key,
				storeName: this.neg1toNull( wpForm.name ),
				itemDescription: this.neg1toNull( wpForm.description ),
				storeImageUrl: this.neg1toNull( wpForm.image ),
				locale: this.neg1toNull( wpForm.locale ),
				currency: this.neg1toNull( wpForm.currency ),
				checkoutButtonLabel: this.neg1toNull( wpForm.panelLabel ),
				couponCode: '',

				// Boolean properties sent to stripeParams
				enableRememberMe: this.neg1toNull( wpForm.allowRememberMe ),
				enableBilling: this.neg1toNull( wpForm.billingAddress ),
				enableShipping: this.neg1toNull( wpForm.shippingAddress ),
				enableVerifyZip: this.neg1toNull( wpForm.zipCode ),
				enableBitcoin: this.neg1toNull( wpForm.bitcoin ),
				enableAlipay: this.neg1toNull( wpForm.alipay ),
				enableAlipayReusable: this.neg1toNull( wpForm.alipayReusable ),
				enablePrefillEmail: this.neg1toNull( wpForm.email ),

				// Properties not sent to stripeParams
				formClientId: spFormEl.prop( 'id' ),
				enableTestMode: this.neg1toNull( wpForm.testMode ),

				// Checkout button label used when amount is zero, namely for subscription plan trials.
				// TODO Move to form setting
				zeroAmountCheckoutButtonLabel: simplePayFrontendGlobals.zeroAmountCheckoutButtonLabel,

				// Other amount-related values to track
				// Set initial base and discounted amounts to same as total amount.
				totalAmount: defaultAmount,
				baseAmount: defaultAmount,
				itemQuantity: 1,
				discountedAmount: defaultAmount,
				userEnteredAmount: 0,
				addOnTotalAmountOperand: 0 // Generic property that only add-ons set to add to total amount.
			};

			// Local var for form data
			var formData = this.spFormData[ formId ];

			// Stripe checkout handler configuration.
			// Only token callback function set here. All other params set in stripeParams.
			// Chrome on iOS needs handler set before click event or else checkout won't open in a new tab.
			// See "How do I prevent the Checkout popup from being blocked?"
			// Full docs: https://stripe.com/docs/checkout#integration-custom
			var stripeHandler = StripeCheckout.configure( {

				// Key param MUST be sent here instead of stripeHandler.open(). Discovered 8/11/16.
				key: wpForm.key,

				token: handleStripeToken,

				opened: function() {
					spApp.debugLog( 'checkout opened event fired' );
				},
				closed: function() {
					spApp.debugLog( 'checkout closed event fired' );
				}
			} );

			// Internal Strike token callback function for StripeCheckout.configure
			function handleStripeToken( token, args ) {

				// At this point the Stripe checkout overlay is validated and submitted.
				// Set values to hidden elements to pass via POST when submitting the form for payment.
				spFormEl.find( '.sc_stripeToken' ).val( token.id );
				spFormEl.find( '.sc_stripeEmail' ).val( token.email );

				// Add shipping fields values if the shipping information is filled.
				if ( !$.isEmptyObject( args ) ) {
					spFormEl.find( '.sc-shipping-name' ).val( args.shipping_name );
					spFormEl.find( '.sc-shipping-country' ).val( args.shipping_address_country );
					spFormEl.find( '.sc-shipping-zip' ).val( args.shipping_address_zip );
					spFormEl.find( '.sc-shipping-state' ).val( args.shipping_address_state );
					spFormEl.find( '.sc-shipping-address' ).val( args.shipping_address_line1 );
					spFormEl.find( '.sc-shipping-city' ).val( args.shipping_address_city );
				}

				// Disable original payment button and change text for UI feedback while POST-ing to Stripe.
				spFormEl.find( '.sc-payment-btn' )
					.prop( 'disabled', true )
					.find( 'span' )
					.text( simplePayFrontendGlobals.paymentSubmittingButtonLabel );

				// Unbind original form submit trigger before calling again to "reset" it and submit normally.
				spFormEl.unbind( 'submit' );

				spFormEl.submit();
			}

			// *** Individual Form-level Event Handlers ***/

			// Page-level initial payment button clicked. Use over form submit for more control/validation.
			spFormEl.find( '.sc-payment-btn' ).on( 'click.spPaymentBtn', function( e ) {
				e.preventDefault();

				// Trigger custom event right before executing payment
				spFormEl.trigger( 'spBeforeStripePayment', [ spFormEl, formData ] );

				spApp.execStripePayment( spFormEl, formData, stripeHandler );
			} );

			// When user-entered amount changes, update total amount label.
			// Look for both keyup & change events.
			spFormEl.find( '.sc-uea-custom-amount' ).on( 'keyup.spUserEnteredAmount change.spUserEnteredAmount', function( e ) {
				spApp.processUserEnteredAmount( spFormEl, formData );
				spApp.updateTotalAmount( spFormEl, formData );
			} );

			// Apply coupon event
			spFormEl.find( '.sc-coup-apply-btn' ).on( 'click.spCouponApply', function( e ) {
				e.preventDefault();
				spApp.applyCoupon( spFormEl, formData );
				// Custom event fired when complete (after ajax post).
			} );

			// Remove coupon event
			spFormEl.find( '.sc-coup-remove-coupon' ).on( 'click.spCouponRemove', function( e ) {
				e.preventDefault();
				spApp.removeCoupon( spFormEl, formData );
				// Custom event fired when complete.
			} );

			// Quantity change event.
			spFormEl.find( '.sc-cf-quantity' ).on( 'change.spQuantity', function( e ) {
				spApp.setItemQuantity( spFormEl, formData, $( this ) );

				// Update total now only if no coupon code, otherwise wait for spCouponApplied event.
				if ( formData.couponCode.trim() === '' ) {
					spApp.updateTotalAmount( spFormEl, formData );
				}

				// Trigger custom event when quantity changes.
				// Created for use in subscriptions add-on to update the subs-specific quantity property.
				spFormEl.trigger( 'spQuantityChanged' );
			} );

			// Custom field checkbox change event.
			spFormEl.find( '.sc-cf-checkbox' ).on( 'change.spCheckbox', function( e ) {
				spApp.setCheckboxYesNoValues( spFormEl, $( this ) );
			} );

			// Custom amount radio button or drop-down change event.
			spFormEl.find( '.sc-cf-amount' ).on( 'change.spSelectAmount', function( e ) {
				spApp.setAmountFromUEASelect( spFormEl, formData, $( this ) );

				// Update total now only if no coupon code, otherwise wait for spCouponApplied event.
				if ( formData.couponCode.trim() === '' ) {
					spApp.updateTotalAmount( spFormEl, formData );
				}
			} );

			// Custom event fired after each successful coupon applied and new values returned after ajax post.
			spFormEl.on( 'spCouponApplied', function( e ) {
				spApp.updateTotalAmount( spFormEl, formData );
			} );

			// Custom event fired when coupon removed.
			spFormEl.on( 'spCouponRemoved', function( e ) {
				spApp.updateTotalAmount( spFormEl, formData );
			} );

			// Close Checkout on page navigation
			$( window ).on( 'popstate', function() {
				stripeHandler.close();
			} );
		},

		execStripePayment: function( spFormEl, spFormData, stripeHandler ) {
			// totalAmount could've been changed by user-entered amount, quantity or other fields
			// via client-side processes.

			// Set hidden amount input for final form post.
			spFormEl.find( '.sc_amount' ).val( spFormData.totalAmount );

			// Set UEA input value back to valid rounded value with decimal.
			spFormEl.find( '.sc-uea-custom-amount' ).val( spFormData.userEnteredAmount );

			// Validate using Parsley at this point after UEA input value set back.
			// UEA still needs to be a minimum of 50 cents/units.

			if ( spFormEl.parsley().validate() ) {

				// Set Stripe params to control checkout overlay display.
				var stripeParams = {

					// Key param MUST be sent in StripeCheckout.configure() instead of here. Discovered 8/11/16.
					// key: spFormData.stripeKey,
					amount: spFormData.totalAmount,
					name: spFormData.storeName,
					description: spFormData.itemDescription,
					image: spFormData.storeImageUrl,
					locale: spFormData.locale,
					currency: spFormData.currency,

					// Alternate checkout button label when amount is zero (i.e. subscription plan trials).
					panelLabel: ( spFormData.totalAmount > 0 ) ? spFormData.checkoutButtonLabel : spFormData.zeroAmountCheckoutButtonLabel,

					// Boolean properties
					allowRememberMe: spFormData.enableRememberMe,
					billingAddress: spFormData.enableBilling,
					shippingAddress: spFormData.enableShipping,
					zipCode: spFormData.enableVerifyZip,
					bitcoin: spFormData.enableBitcoin,
					alipay: spFormData.enableAlipay,
					alipayReusable: spFormData.enableAlipayReusable,
					email: spFormData.enablePrefillEmail
				};

				spApp.debugLog( 'stripeParams', stripeParams );

				stripeHandler.open( stripeParams );
			}
		},

		processUserEnteredAmount: function( spFormEl, spFormData ) {

			var userEnteredAmountEl = spFormEl.find( '.sc-uea-custom-amount' );
			var unformattedAmount = accounting.unformat( userEnteredAmountEl.val() );

			// Set baseAmount property in format for Stripe.
			spFormData.baseAmount = this.formatForStripe( unformattedAmount, spFormData.currency );

			// Set amount property to change enteredAmount back to when payment button clicked.
			// Account for zero-decimal currencies.
			var userEnteredAmountPrecision = 2;

			if ( this.isZeroDecimalCurrency( spFormData.currency ) ) {
				userEnteredAmountPrecision = 0;
			}

			spFormData.userEnteredAmount = accounting.toFixed( unformattedAmount, userEnteredAmountPrecision );
		},

		// Update total amount & label.
		updateTotalAmount: function( spFormEl, spFormData ) {

			// First update the totalAmount property.
			// It should equal baseAmount * itemQuantity also if no coupon is applied.
			if ( spFormData.couponCode.trim() === '' ) {

				spFormData.totalAmount = spFormData.baseAmount * spFormData.itemQuantity;
			} else {

				// discountedAmount is already baseAmount * itemQuantity with coupon code applied.
				spFormData.totalAmount = spFormData.discountedAmount;
			}

			// totalAmount can be modified here even further with add-ons.
			// Init add-on operand property (used for adding setup fee, subtracting if trial period, etc).
			spFormData.totalAmount += spFormData.addOnTotalAmountOperand;

			// Now find label, format amount, adjust for currency, and update.
			var finalTotalAmountEl = spFormEl.find( '.sc-total-amount' );

			// Check for total amount label existence.
			if ( finalTotalAmountEl.length > 0 ) {

				// Unformatted decimal amount
				var unformattedAmount = this.unformatFromStripe( spFormData.totalAmount, spFormData.currency );

				finalTotalAmountEl.text( this.formatCurrency( unformattedAmount, spFormData.currency ) );
			}
		},

		applyCoupon: function( spFormEl, spFormData ) {

			var couponInput = spFormEl.find( '.sc-coup-coupon' );
			var couponCode = '';

			// Hidden input for applied coupon
			var couponAppliedEl = spFormEl.find( '.sc-coup-coupon-applied' );
			var validationMsgEl = spFormEl.find( '.sc-coup-validation-message' );
			var successMsgEl = spFormEl.find( '.sc-coup-success-message' );
			var loadingIndicator = spFormEl.find( '.sc-coup-loading' );
			var removeCouponBtn = spFormEl.find( '.sc-coup-remove-coupon' );

			// Check for coupon code field existence.
			if ( couponInput.length > 0 ) {
				couponCode = couponInput.val().trim();
			}

			// Check for non-blank input coupon code value.
			if ( couponCode === '' ) {
				// Also check for coupon code value in form data in case of quantity change.
				if ( spFormData.couponCode.trim() === '' ) {
					// In this case don't proceed.
					return;
				} else {
					couponCode = spFormData.couponCode;
				}
			}

			// Trigger custom event when starting the coupon apply process.
			spFormEl.trigger( 'spCouponApplyStart' );

			// Init discountedAmount, which factors in itemQuantity before coupon code applied.
			spFormData.discountedAmount = spFormData.baseAmount * spFormData.itemQuantity;

			// Clear out any previous validation and success messages.
			validationMsgEl.empty();
			successMsgEl.empty();

			loadingIndicator.show();

			// AJAX POST params
			var couponParams = {
				action: 'scp_get_coupon',
				coupon: couponCode,
				amount: spFormData.discountedAmount,
				test_mode: spFormData.enableTestMode
			};

			// Send AJAX POST with params to WP's admin-ajax.php (localized).
			$.post( simplePayFrontendGlobals.ajaxurl, couponParams, function( response ) {

				if ( response.success ) {

					/* response contents:
					 * - coupon
					 *   - amountOff
					 *   - code
					 *   - type
					 * - message
					 * - success
					 */

					// If a new coupon code successfully applied, remove any previous coupons applied if exists.
					// Right now this causes 2 executions of updating total amount label, but no adverse effects.
					spApp.removeCoupon( spFormEl, spFormData );

					// Update discounted amount from return value (calculated by Stripe).
					spFormData.discountedAmount = response.message;

					// Set form's coupon code property to value returned by Stripe (should be same except maybe capitilization).
					spFormData.couponCode = response.coupon.code;

					// If a valid coupon code, set hidden field value to applied coupon code.
					couponAppliedEl.val( spFormData.couponCode );

					// Then blank out the visible coupon input field.
					couponInput.val( '' );

					// Start coupon code message with successful used code itself.
					// Format: [code] - [#/% off] [x]
					var couponCodeMsg = spFormData.couponCode + ': ';

					// Change "amount off" part of coupon message depending on coupon type.
					if ( response.coupon.type === 'percent' ) {

						couponCodeMsg += response.coupon.amountOff + '% ' + simplePayFrontendGlobals.couponAmountOffText;

					} else if ( response.coupon.type === 'amount' ) {

						// Set unformatted decimal "amount off".
						var unformattedAmountOff = spApp.unformatFromStripe( response.coupon.amountOff, spFormData.currency );

						couponCodeMsg += spApp.formatCurrency( unformattedAmountOff, spFormData.currency ) + ' ' +
						                 simplePayFrontendGlobals.couponAmountOffText;
					}

					// Finally display coupon code success message.
					successMsgEl.text( couponCodeMsg );

					// Show coupon removal link.
					removeCouponBtn.show();

					// Trigger custom event when coupon apply done.
					spFormEl.trigger( 'spCouponApplied' );

				} else {
					// Invalid coupon code or other error from server.
					validationMsgEl.text( response.message );
				}

				loadingIndicator.hide();

			}, 'json' );
		},

		removeCoupon: function( spFormEl, spFormData ) {

			// Hidden input for applied coupon
			var couponAppliedEl = spFormEl.find( '.sc-coup-coupon-applied' );
			var validationMsgEl = spFormEl.find( '.sc-coup-validation-message' );
			var successMsgEl = spFormEl.find( '.sc-coup-success-message' );
			var removeCouponBtn = spFormEl.find( '.sc-coup-remove-coupon' );

			// Set discountedAmount back to baseAmount * itemQuantity.
			spFormData.discountedAmount = spFormData.baseAmount * spFormData.itemQuantity;

			// Remove applied coupon code form property & hidden field value.
			spFormData.couponCode = '';
			couponAppliedEl.val( '' );

			// Clear out any previous coupon messages and hide removal link.
			validationMsgEl.empty();
			successMsgEl.empty();
			removeCouponBtn.hide();

			// Trigger custom event when coupon apply done.
			spFormEl.trigger( 'spCouponRemoved' );
		},

		// Set itemQuantity property.
		// Quantity form element can be radio button list, drop-down or text input.
		setItemQuantity: function( spFormEl, spFormData, quantityInputEl ) {

			// Init actual quantity value.
			var quantityVal = 1;

			// Check for quantity form element existence.
			if ( quantityInputEl.length > 0 ) {

				// We need to check if it is a radio button so we can grab the selected value.
				if ( quantityInputEl.is( 'input[type="radio"]' ) ) {

					// Narrow down to selected radio input here.
					quantityVal = parseInt( quantityInputEl.filter( ':checked' ).val().trim() );

				} else {

					// Retrieve value using jQuery's val() for text & select (drop-down) inputs.
					quantityVal = parseInt( quantityInputEl.val().trim() );
				}
			}

			spFormData.itemQuantity = quantityVal;

			// Attempt to run applyCoupon in case a coupon code is in effect.
			this.applyCoupon( spFormEl, spFormData );
		},

		initCustomFields: function() {
			// Run Pikaday Datepicker method on each date field in each Stripe checkout form.
			// Requires Moment JS, Pikaday and Pikaday jQuery plugin.
			// TODO i18n or setting for date format
			body.find( '.sc-cf-date' ).pikaday( { format: 'M/D/YYYY' } );
		},

		// Set appropriate hidden value (Yes/No) for Stripe payment records.
		setCheckboxYesNoValues: function( spFormEl, checkboxEl ) {

			var checkboxId = checkboxEl.prop( 'id' );

			// Hidden ID field is simply "_hidden" appended to checkbox ID field.
			var hiddenField = spFormEl.find( '#' + checkboxId + '_hidden' );

			// Change to "Yes" or "No" dending on checked or not.
			hiddenField.val( checkboxEl.is( ':checked' ) ? 'Yes' : 'No' );
		},

		// Set base amount from drop-down or radio button item.
		setAmountFromUEASelect: function( spFormEl, spFormData, selectAmountEl ) {

			// Check for select amount element existence.
			if ( selectAmountEl.length > 0 ) {

				var newAmount = 0;

				// We need to check if it is a radio button so we can grab the selected value.
				if ( selectAmountEl.is( 'input[type="radio"]' ) ) {

					// Radio button selected amount
					newAmount = accounting.unformat( selectAmountEl.filter( ':checked' ).data( 'sc-price' ) );

				} else {

					// Drop-down selected amount
					newAmount = accounting.unformat( selectAmountEl.find( 'option:selected' ).data( 'sc-price' ) );
				}

				spFormData.baseAmount = newAmount;

				// Attempt to run applyCoupon in case a coupon code is in effect.
				this.applyCoupon( spFormEl, spFormData );
			}
		},

		// Setup Parsley JS validation.
		setupValidation: function( spFormEl ) {
			// Parsley JS prevents form submit by default. Stripe also suggests using a button click event
			// (not submit) to open the overlay in their custom implementation.
			// https://stripe.com/docs/checkout#integration-custom
			// So we need to explicitly call .validate() instead of auto-binding forms with data-parsley-form.
			// http://parsleyjs.org/doc/index.html#psly-usage-form

			// Update 5/20/2015: Fire off form/Parsley JS validation with button click.
			// Needed for some mobile browsers like Chrome iOS.
			// https://stripe.com/docs/checkout#integration-more-runloop
			// Using event subscription method won't work for them (i.e. .subscribe('parsley:form:validate'... ).

			// Set form's Parsley JS validation error container
			spFormEl.parsley( {
				errorsContainer: function( ParsleyField ) {
					return ParsleyField.$element.closest( '.sc-form-group' );
				}
			} );
		},

		// Update various form elements and values when user-entered amount changes.
		// Log to console if WP SCRIPT_DEBUG constant set to true.
		debugLog: function( key, value ) {
			// Check for string 'true' instead of boolean type as they will come in stringified when localized.
			// Alternatively use JSON.parse (but still need to string 'true' in PHP).
			if ( ( typeof simplePayFrontendGlobals !== 'undefined' ) && ( 'true' === simplePayFrontendGlobals.scriptDebug ) ) {
				console.log( key, value );
			}
		},

		// First multiply amount to what Stripe needs unless zero-decimal currency used.
		// Then round so there's no decimal. Stripe hates that.
		formatForStripe: function( amount, currency ) {
			if ( this.isZeroDecimalCurrency( currency ) ) {
				return Math.round( amount );
			} else {
				return Math.round( parseFloat( amount * 100 ) );
			}
		},

		// Reverse of above. Divide non-decimal amount to decimal amount if needed.
		unformatFromStripe: function( amount, currency ) {
			if ( this.isZeroDecimalCurrency( currency ) ) {
				return Math.round( amount );
			} else {
				return ( amount / 100 ).toFixed( 2 );
			}
		},

		// Zero-decimal currency check
		// Same as Stripe_Checkout_Misc::is_zero_decimal_currency() PHP function.
		isZeroDecimalCurrency: function( currency ) {
			try {
				return ( $.inArray( currency.toUpperCase(), simplePayFrontendGlobals.zeroDecimalCurrencies ) > 1 );
			}
			catch ( ex ) {
				return false;
			}
		},

		// Format currency for labels using accounting.js
		formatCurrency: function( amount, currency ) {
			try {
				currency = currency.toUpperCase();

				// accounting.js default settings and our default to use as a base.
				// symbol: '$'
				// format: '%s%v'
				// precision: 2

				var formatMoneyParams = {};

				// Prepend dollar sign for USD. Append 3-letter currency code for all others.
				if ( currency !== 'USD' ) {
					formatMoneyParams.symbol = currency;
					formatMoneyParams.format = '%v %s';
				}

				// Account for zero-decimal currencies.
				if ( this.isZeroDecimalCurrency( currency ) ) {
					formatMoneyParams.precision = 0;
				}

				return accounting.formatMoney( amount, formatMoneyParams );
			}
			catch ( ex ) {
				return amount;
			}
		},

		// Set negative 1 values to null.
		// TODO Eventually come up with better way of setting null/undefined value instead of -1.
		neg1toNull: function( value ) {
			if ( -1 === parseInt( value ) ) {
				value = null;
			}
			return value;
		}

	};

	// On DOM ready
	$( document ).ready( function( $ ) {
		spApp.init();

		spApp.debugLog( 'spApp.spFormData', spApp.spFormData );
	} );

	// Modify DOM before unload in case of back button navigation here
	// on Safari and some other browsers (not Chrome).
	$( window ).on( 'beforeunload', function() {

		this.spFormElList = body.find( '.sc-checkout-form' );

		// Set payment button text back to original and re-enable.
		this.spFormElList.each( function() {
			var paymentBtn = $( this ).find( '.sc-payment-btn' );

			paymentBtn
				.prop( 'disabled', false )
				.find( 'span' ).text( paymentBtn.data( 'textOriginal' ) );
		} );
	} );


}( jQuery ));

=== WP Simple Pay Pro for Stripe ===
Requires at least: 4.2
Tested up to: 4.6
License: GPLv2 or later

== Description ==

Add highly optimized Stripe checkout form overlays to your site in a few simple steps. **Pro Version**

== Changelog ==

= 2.4.7 - September 15, 2016 =

* Fix: URL encode store name properly so it displays on payment details screen correctly.
* Tweak: Updated translation template (POT) file.
* Tweak: Added partial French translation.
* Dev: Updated to Stripe PHP library v3.22.0.

= 2.4.6 - August 11, 2016 =

* Fix: Change how Stripe publishable key is submitted. Breaking change related to Stripe's checkout.js discovered today.
* Dev: Add filter for payment form CSS classes.
* Dev: Add filter for payment button CSS classes.
* Dev: Add filter for adding icon HTML inside button element preceding button text.

= 2.4.5 - July 29, 2016 =

* Fix: Extra TLS compatibilty check via updated Stripe PHP library (v3.19.0).
* Feature: Added Stripe TLS requirement to system report.
* Dev: System report tweaks for PHP 7 compatibility.
* Dev: Tested up to WordPress 4.6.

= 2.4.4 - June 24, 2016 =

* Fix: Script issue affecting custom field validation in IE11 only fixed.

= 2.4.3 - June 18, 2016 =

* Fix: Payment button should now re-enable and display original text when using the back button on Safari and some other browsers.
* Fix: Compatibility fix with Simple Calendar plugin.
* Fix: Check for Lite version of WP Simple Pay to prevent fatal error in some cases.
* Dev: Improve store name url encoding and escaping for display on payment success page.
* Dev: Improve product name escaping for display on payment success page.

= 2.4.2 - May 16, 2016 =

* Feature: Added compatibility with Easy Pricing Tables Premium.

= 2.4.1 - April 14, 2016 =

* Fix: Truncate custom field metadata key length to 40 characters and value length to 500 characters to prevent errors.

= 2.4.0 - March 28, 2016 =

* Fix: Multiply quantity correctly when calculating total amount.
* Fix: Shipping field metadata now save when enabled via default settings.
* Fix: Chrome on iOS needs handler set before click event or else checkout won't open in a new tab.
* Tweak: Admin toggle switch UI for Test/Live modes updated.
* Tweak: Always enqueue scripts option removed. Now forced on unless dequeued in code.
* Dev: Front-end JS refactored for easier maintenance and feature updates.
* Dev: Converted text in front-end JS to translatable strings.
* Dev: Now using Composer to handle PHP library dependencies (i.e. Stripe PHP).
* Dev: Now using Bower to handle JS/CSS library dependencies (i.e. Moment JS, Parsley JS, Pikaday).
* Dev: Now using Accounting JS library (though Bower configuration isn't working at the moment).
* Dev: Added Stripe customer ID filter: sc_customer_id
* Dev: Moved sc_modify_script_options below shortcode processing.
* Dev: Improved license key activation and plugin updates.
* Dev: Tested up to WordPress 4.5.

= 2.3.7 - September 29, 2015 =

* Fix: Fixed bug with undefined PHP variable.
* Fix: Changed default amount to 0 to avoid possible bug when using the currency attribute.
* Fix: Fixed a bug with default amount not showing up correctly sometimes for radio and dropdown amounts.
* Dev: Updated to most recent Stripe PHP library (v3.4.0).
* Dev: Updated to most recent Moment JS library (v2.10.6).
* Dev: Updated to most recent Parsley JS library (v2.2.0-rc1).
* Dev: Updated to most recent Pikaday JS library (v1.4.0-rc1).

= 2.3.6 - September 12, 2015 =

* Fix: Fixed issue with all checkboxes defaulting as sub_toggle checkboxes.
* Fix: Fixed a bug with apply_filters in stripe_total shortcode.
* Tweak: Changed recommended memory on system report page to 40MB.

= 2.3.5 - September 10, 2015 =

* Fix: Fixed a bug with the payment_details_placement attribute.
* Dev: Added a bunch of filters for CSS classes on the form controls.
* Tweak: Removed live API keys from downloadable system report.

= 2.3.4 - August 31, 2015 =

* Tweak: Updated locale setting to allow specific languages recently added by Stripe. Also now defaults to "auto" (user's browser configuration).
* Feature: Added ability via checkbox for users to select a one-time or recurring payment for the amount they enter.
* Feature: Added System Report page to assist with troubleshooting and support.

= 2.3.3 - August 16, 2015 =

* Fix: Remove admin message for mismatched shortcodes.
* Feature: Added shipping address setting option.

= 2.3.2 - August 15, 2015 =

* Fix: Fixed a bug with the default attribute of 'stripe_amount' not converting properly.
* Fix: Fixed a PHP fatal error happening when trying to activate with the Lite version activated.
* Tweak: Added a message to warn about mismatched opening and closing shortcode tags.
* Tweak: Adjusted name of admin menu label to be shorter.
* Dev: Updated existing sc_redirect filter.
* Dev: Added new sc_redirect_args filter.

= 2.3.1 - August 8, 2015 =

* Tweak: Added deactivation code so Lite and Pro versions don't run at the same time.
* Fix: Prevent conflicts with other plugins using Stripe.
* Fix: Fixed a bug with the default attribute of 'stripe_amount' not working as expected.
* Dev: Now using Grunt to automate build and file minification tasks.
* Dev: Now JS & CSS files referenced are the minified versions. If SCRIPT_DEBUG set to true, all JS & CSS files referenced are the debug/unminified versions.
* Dev: No longer using the WPupdatePHP library.

= 2.3.0.3 - July 30, 2015 =

* Fixed calculation bug when setting a quantity field to required.

= 2.3.0.2 - July 29, 2015 =

* Fixed a bug that was causing a PHP fatal error in some cases.

= 2.3.0.1 - July 27, 2015 =

* Fixed bugs with updating.
* Temporary revert back to old domain name (wpstripe.net) due to DNS issues.

= 2.3.0 - July 25, 2015 =

* Added shortcode attributes to allow alternate Stripe API keys other than those stored in the default settings.
* Change of product name (WP Simple Pay Pro for Stripe) and domain name (wpsimplepay.com).
* Major code refactor.
* Updated to most recent Stripe PHP library (v2.3.0).
* Tested up to WordPress 4.3.

= 2.2.5 - May 20, 2015 =

* Added the ability to accept Alipay payments via shortcode (alipay="true" or "auto").
* Added optional Alipay shortcode attributes (alipay_reusable="true" and/or locale="auto").
* Added the ability to accept Alipay payments via default settings.
* Added the ability to show payment details below post content via shortcode (payment_details_placement="below").
* Upon payment failure, the human-readable payment failure message is displayed instead of the failure code.
* Fixed checkout overlay not loading on Chrome iOS when used with validation library (Parsley JS).
* Fixed plugin license check and activate for a small number of users.
* Improved plugin license check admin JS & CSS specificity.
* Updated to most recent Stripe PHP library (v2.1.4).
* Updated to most recent Moment JS library (v2.10.3).

= 2.2.4 - April 24, 2015 =

* Fixed bug where the data-sc-id attribute of each form was not incrementing when also using custom form IDs.
* Fixed bug in shortcode tracker class when saving posts in some cases.

= 2.2.3 - April 22, 2015 =

* Updated calls to add_query_arg to prevent any possible XSS attacks.
* Fixed bug where checkbox custom field values were recorded as false when default was set to true (checked) and left unchanged.
* Option to always enqueue scripts & styles now enabled by default.
* Updated to most recent Stripe PHP library (v2.1.2).

= 2.2.2 - April 6, 2015 =

* Payment success output now properly encodes slashes for store name and description.
* Fixed default total amount when using coupons with radio button fields.
* Fixed bug in shortcode tracker class.

= 2.2.1.2 - March 25, 2015 =

* Fixed a regressed bug when using drop-downs in forms.
* Tested up to WordPress 4.2.

= 2.2.1.1 - March 24, 2015 =

* Add extra check for WPUpdatePHP class existence. Fixes redeclare error in some cases.

= 2.2.1 - March 21, 2015 =

* Now checks that host is running PHP 5.3.3 or higher using the WPupdatePHP library.
* Fixed bug with coupon codes not applying.

= 2.2.0 - March 18, 2015 =

* Added the ability to accept Bitcoin payments via default settings.
* Fixed bug where test_mode attribute wasn't setting test mode properly.

= 2.1.9.1 - March 13, 2015 =

* Corrected the Stripe PHP class check to include new v2.0.0+ namespace. Should fix issues when running other Stripe-related plugins that utilize a version of the Stripe PHP library less than v2.0.0.

= 2.1.9 - March 12, 2015 =

* Added the ability to accept Bitcoin payments via shortcode (bitcoin="true").
* Updated to most recent Stripe PHP library (v2.1.1), which now requires PHP 5.3.3 or higher.
* Cleaned up payment success and error details HTML.
* Fixed duplicate payment success and failure output for rare themes that render multiple post content areas.
* Added filter to add and change currency symbols and codes before and after the user-entered amount field.
* Fixed bug where coupon codes could be applied more than once.
* Disallow more than one coupon code field in the same form.
* Internationalization updates.
* Added id attribute to shortcode to allow custom form id's.
* Now sanitizes Stripe API keys with invalid copied characters following a space.
* Updated to most recent Moment JS library (v2.9.0).
* Updated to most recent Pikaday JS library (v1.3.2).
* Updated to most recent Parsley JS library (v2.0.7).

= 2.1.8 =

* Fixed bug so drop-down selection saves the text value to the payment (not the amount) when varying amounts are also specified.
* Added option to always enqueue scripts and styles on every post and page.
* Added function to remove unwanted formatting in shortcodes.

= 2.1.7 =

* Updated to most recent Stripe PHP library (v1.17.5).
* Plugin updater performance improvements.
* Scripts and styles now only enqueued on posts and pages where required.
* Fixed bug where the 10-field limit was counting all forms on a post in total. The limit should apply to each individual form.
* Immediately after the last bug fix Stripe increased the limit to allow 20 fields per form. Plugin updated accordingly.
* Moved some code that applied to the Subscriptions add-on only to that add-on.

= 2.1.6 =

* Test/Live mode toggle switch updated. Now CSS only.

= 2.1.5 =

* Fixed bug when using coupons with subscriptions.

= 2.1.4 =

* Updated to most recent Moment JS library (v2.8.4).
* Updated to most recent Bootstrap Switch library (v3.2.2).
* Tested up to WordPress 4.1.
* Fixed bug when using custom amounts with subscriptions.

= 2.1.3.1 =

* Updated user-entered amount validation message.

= 2.1.3 =

* Allow custom redirect URLs to display Stripe buttons.
* Prevent commas from being entered in user-entered amount fields.
* Updated to most recent Stripe PHP library (v1.17.3).

= 2.1.2 =

* Fixed bug where user-entered amounts created a subscription plan.
* Plugin updater performance improvements.

= 2.1.1 =

* Error handling improvements.
* Add-on framework improvements.

= 2.1.0 =

* Redesign of user-entered amounts for radio button and drop-down fields.
* Simplified text domain function.

= 2.0.9 =

* Added option to disable the default success message output.

= 2.0.8 =

* Added support for quantity fields that multiply the amount by a user-selected number.
* Added support for user-entered amounts with radio button and drop-down fields.
* Fixed a bug with custom fields sending blank values.
* Fixed a bug with the remember me option.
* Fixed a bug with the test_mode attribute.
* Fixed a bug with shipping address not saving separately from billing address.

= 2.0.7 =

* Allow display of more charge details on the payment success page. This is made possible by utilizing the Stripe charge ID to retrieve the entire charge object via the Stripe API.
* Renamed a function for better compatibility with themes and other plugins.
* Improved messaging for minimum required amount by Stripe (50 units).

= 2.0.6 =

* Updated to be compatible with Stripe Subscriptions 1.0.5.
* Updated to most recent Stripe PHP library (v1.17.2).

= 2.0.5 =

* Payment button style setting and shortcode added.
* Updated 3rd party JS/CSS libraries: Moment, Parlsey, Bootstrap Switch.

= 2.0.4 =

* Added drop-down custom field shortcode: [stripe_dropdown]
* Added radio button custom field shortcode: [stripe_radio]
* Added test_mode attribute to specify test mode per form.
* Added option to save settings when uninstalling.
* Special characters now encoded properly for overlay form.
* Fixed bug with description not being added to Stripe dashboard.
* Better error handling for Stripe API Requests.
* Better admin-only notices for invalid shortcode combinations.

= 2.0.3 =

* Fixed compatibility issue with Pro shortcodes used alongside add-ons.

= 2.0.2 =

* Fixed bug with update check when other plugins are using the same EDD software licensing library.
* Add-on framework updates.

= 2.0.1 =

* Fixed bug where coupon code Apply button was firing off overlay.

= 2.0.0 =

* Initial plugin conversion from add-on model used previously.
* Updated to most recent Stripe PHP library (v1.17.1).
* Improved license key validation checks and messaging.
* Fixed validation issue with custom text fields.

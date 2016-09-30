// Admin JS - SP Pro

/* global simplePayAdminGlobals */

(function( $ ) {
	'use strict';

	$( document ).ready( function( $ ) {

		var body = $( document.body );

		// Update submit button text depending on the tab id.
		function updateSubmitButtonText( tabId ) {
			var saveBtnText = ( 'license-keys' == tabId ) ? simplePayAdminGlobals.licensesTabSaveButton : simplePayAdminGlobals.otherTabsSaveButton;
			$( '#sc-settings-content form #submit' ).val( saveBtnText );
		}

		// Hide licenses tab link in admin notice if we're on licenses tab.
		// TODO Not getting fired if #license-keys hash is first loaded.
		function hideLicensesTabLink( tabId ) {
			if ( 'license-keys' == tabId ) {
				$( '.simple-pay-licenses-tab-link' ).hide();
			} else {
				$( '.simple-pay-licenses-tab-link' ).show();
			}
		}

		// Custom event fired after admin tab nav or load.
		// tabId = hash fragment
		body.on( 'spAdminTabOnChange', function( e, tabId ) {
			updateSubmitButtonText( tabId );
			hideLicensesTabLink( tabId );
		} );

		// Update submit button text on initial page load also.
		if ( window.location.hash ) {
			updateSubmitButtonText( window.location.hash.substring( 1 ) );
		}

		// Fire off Licenses tab click in admin notice link.
		$( '.simple-pay-licenses-tab-link' ).click( function() {
			$( '.sc-nav-tab-license-keys' ).trigger( 'click' );
		} );
	} );

}( jQuery ));

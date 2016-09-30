<?php
namespace W3TC;

if ( !defined( 'W3TC' ) )
	die();

?>
<div id="w3tc-upgrade">
    <div class="w3tc-overlay-logo"></div>
    <div class="w3tc_overlay_upgrade_header">
        <div>
            <div class="w3tc_overlay_upgrade_left_h">
                W3 Total Cache Pro unlocks more performance options for any website:
            </div>
            <div class="w3tc_overlay_upgrade_right_h">
                only $99 <span class="w3tc_overlay_upgrade_right_text">/year</span>
            </div>
        </div>
        <div class="w3tc_overlay_upgrade_description">
            <div class="w3tc_overlay_upgrade_content_l">
                <img src="<?php echo plugins_url( 'pub/img/overlay/w3-meteor.png', W3TC_FILE ) ?>" 
                    width="238" height="178" />
            </div>
            <div class="w3tc_overlay_upgrade_content_r">
                <ul>
                    <li><strong>Fragment Caching Module</strong><br>
                        Unlocking the fragment caching module delivered enhanced performance for plugins and themes that use the WordPress Transient API. It also provides a framework for increasing for developers that specifically use it like StudioPress' Genesis Framework.</li>
                    <li>
                        <strong>Exclusive Extensions</strong><br>
                        Unlock up to 60% performance improvement in the Genesis Theme Framework by StudioPress using the extension for W3TC's Fragment Caching Module.                </li>
                    <li>
                        <strong>Full Site Content Delivery Network (CDN) Mirroring</strong><br>
                        Upcoming: Provide the best user experience possible by enhancing by hosting HTML pages and RSS feeds with (supported CDNs) high performance global networks.                </li>
                </ul>
            </div>
        </div>
        <div style="clear: both"></div>
    </div>
    <div class="w3tc_overlay_content"></div>
    <div class="w3tc_overlay_footer">
        <input id="w3tc-purchase" type="button" class="btn w3tc-size image btn-default palette-turquoise secure" value="<?php _e( 'Subscribe to Go Faster Now', 'w3-total-cache' ) ?> " />
    </div>
    <div style="clear: both"></div>
</div>

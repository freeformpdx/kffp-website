=== mb.miniAudioPlayer - an HTML5 audio player for your mp3 files ===

Contributors: pupunzi
Tags: audio player, mp3, HTML5 audio, audio, music, podcast, jquery, pupunzi, mb.components
Requires at least: 3.3
Tested up to: 4.5
Stable tag: 1.8.2
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DSHAHSJJCQ53Y

Transform your mp3 audio files into a nice, small light HTML5 player.
== Description ==

**This plug-in let you transform any mp3 file uploaded inside a post into an essential small HTML5 audio player with:**

* volume control
* seeking control
* title bar
* rewind button
* mute button
* play button

[youtube http://www.youtube.com/watch?v=B8Dr4aUNGgo]

**Watch this video to learn how to customize your player using the on line Skin Maker too:**

[youtube https://www.youtube.com/watch?v=2WldUObmRZ4]

**Important!**
From version 1.5.8 the CSS has been changed; for all the one how have customized the skin they need to recreate their skin from <a href="http://pupunzi.com/mb.components/mb.miniAudioPlayer/demo/skinMaker.html" target="_blank">here</a>.

Links:

* demo: http://pupunzi.com/mb.components/mb.miniAudioPlayer/demo/demo.html
* video: http://youtu.be/B8Dr4aUNGgo
* pupunzi blog: http://pupunzi.open-lab.com
* pupunzi site: http://pupunzi.com

<b>From version 1.4.x you can customize your player appearance by using the <a href="http://pupunzi.com/mb.components/mb.miniAudioPlayer/demo/skinMaker.html">on-line miniAudioPlayer Skin Editor</a>.</b>

If you are using others HTML5 audio plugins (like Haiku) there could be conflicts with mb.miniAudioPlayer. You should deactivete the others befor using it.

Other WP plugins:

* **wp-YTPlayer.** A Chromeless video player to play your Youtube videos as background of any WP page or post.
http://wordpress.org/extend/plugins/wpmbytplayer/

== Installation ==

Extract the zip file and upload the contents to the wp-content/plugins/ directory of your WordPress® installation, and then activate the plugin from the plugins page.

== Screenshots ==

1. The settings panel.
2. The player closed with a black skin.
3. The player opened with a green skin.
4. The edit properties button available in the post editor toolbar.
5. The properties window in the post editor.

== How it works: ==

1. Activate the mb.miniAudioPlayer plugin via the WP plugin panel;
2. Edit a post or a page, click on the Upload/Insert media link and choose an mp3 file;
3. place it into the post wherever you want to show the player.
4. save the post and browse it; the player will show instead of the link at the file.

to change the player default settings go to the mb.miniAudioPlayer settings panel (you can find it under the "settings" section of the WP backend).

**Options:**

* @ width = (int) the width in pixel of the player once opened.
* @ skin = the color of the player interface (black, blue, orange, red, gray and green).
* @ volume = (int) the initial volume of the player.
* @ showVolumeLevel = a boolean to show or hide the volume control.
* @ showTime = a boolean to show or hide the time counter.
* @ showRew = a boolean to show or hide the rewind control.
* @ autoPlay = (available only for the TinyMCE editor plugin) a boolean to set in play the player once the page is loaded.
* @ downloadable = a boolean to show the download button next to the player.
* @ excluded = a string containing the CSS class for audio links that should not be converted into player.

**Ubuntu Chromium issue**

Due to some codecs missing in the default Ubuntu Chromium install the player will not work. This problem can be resolved by simply installing that codecs via console:
sudo apt-get-install chromium-codecs-ffmpeg-extra

After that your player should work fine.

== Changelog ==

= 1.8.2 =
* Bug fix: Player broke on v 1.8.1 due to filename case sensitivity.

= 1.8.1 =
* Bug fix: the jQuery.mbMiniPlayer.actualPlayer.mb_miniPlayer_stop() did not fire if the anchor didn't have the ID.

= 1.8.0 =
* Bug fix: Add a patch to jPlayer to solve a bug if on Android devices that was blocking the correct player behavior.

= 1.7.95 =
* Bug fix: after the first download all the other failed till the page reload.

= 1.7.9 =
* Bug fix: moved session_start() to the plugin init to solve the "Cannot send session cookie - headers already sent by ..." issue.

= 1.7.8 =
* Bug fix: If session was already started the download didn't fire correctly.

= 1.7.7 =
* Improved security on download (map_download.php) checking the same origin referral for the download request.

= 1.7.6 =
* Bug fix: With the last Wordpress update the default audio player was not replaced by the miniAudioPlayer.

= 1.7.5 =
* Bug fix: In map_download.php removed space from audio file path.

= 1.7.4 =
* Add the text-domain declaration for the translate.wordpress.org.

= 1.7.3 =
* Bug fix: Fixed a bug that was preventing the correct behavior if used to replace the default Wordpress media player.

= 1.7.2 =
* Bug fix: Solved a bug introduced with the last 1.7.0 release that was preventing the TinyMCE editor to work properly.

= 1.7.1 =
* New option: You can now choose if the speaker icon in the player should mute/unmute or just play the audio.

= 1.7.0 =
* New option: If the "Replace the default WP media player" is checked you can now choose to show or hide the filename inside the player.

= 1.6.9 =
* Feature: On mobile devices clicking on the mute button just play the audio (on mobile devices you can't mute other than from the hardware control).

= 1.6.8 =
* Bug fix: if the file url contain parameters the download failed.
* Bug fix: if used to replace the default WP player with a playlist the Author and the title where not updated.

= 1.6.7 =
* Bug fix: if the component was replacing the default WordPress embed player the settings defined in the settings page were not applied.

= 1.6.6 =
* Feature: Now you can specify if miniAudioPlayer should work also with the default Wordpress short-code. If this option (Replace the default Wordpress embed media player) is checked the player will work also when the audio file is inserted using the "embed media player" option in the "add media" window. It will work also for play-lists as the default Wordpress media player does.

= 1.6.5 =
* bugfix: The "Apply to any .mp3 file link" option were not correctly applied.

= 1.6.4 =
* feature: Added a new option that let choose if apply the component to all the links to audio files or if activate the player manually from the page/post editor component window.

= 1.6.3 =
* Bug fix: fixed a bug that affected the correct customization behavior in certain cases.
* feature: The customization window now adapt its size to the window size.

= 1.6.2 =
* Bug fix: fixed a bug that was preventing the download on IE.

= 1.6.1 =
* Updated to jPlayer 2.9.2.
* fixed a css bug.
* fixed a bug on the TinyMCE customize window.
* fixed a security issue within the map_download.php page; now you can't call that page other than from the download button.

= 1.6.0 =
* Bug fix: Solved a Chrome issue where if many player where instanced in a single page it stops working after an arbitrary numbers of playing players.

= 1.5.9 =
* MAJOR UPDATES: You can now generate a custom skin from the <a href="http://pupunzi.com/mb.components/mb.miniAudioPlayer/demo/skinMaker.html">skinMaker</a> tool, save it as CSS file and upload it from the settings window.
* The from the skinMaker tool you can now manage also the size of the player.
* added the uninstall.php to clean uo the database when the plug-in is removed.

= 1.5.8 =
* MAJOR UPDATES: From this version the player is built only using DIV elements. The CSS has been changed and if you customized your skin you need to regenerate it!.
* Feature: better responsiveness on devices.
* Feature: Customizing the skin you can now increase the size of the player appearance.

= 1.5.7 =
* Update: Updated the miniAudioPlayer-admin.php using the Wordpress "Settings API".

= 1.5.6 =
* New feature: Added support for Google Analytic Universal event tracking; before this update the "play" and the "map_download" events were tracked only if the standard version of GA was present on the page; now they work also if the latest Universal GA is installed.

= 1.5.5 =
* Bug fix: if the "animate" option was set to false, the editor customizer window throw an error preventing the correct behavior.

= 1.5.4 =
* New feature: the plug-in customizer editor will open even if the URL is not explicitally pointing to an MP3 file.

= 1.5.3 =
* Major update: Updated to solve issue compatibilities with WP 3.9 - Needed if you are updating your Wordpress to the latest 3.9 release.

= 1.5.2 =
* Feature: Updated jPlayer to the latest version.
* Feature: Added the GA track event also for downloads.
* Feature: Updated to the latest jquery.mb.CSSAnimate component.

= 1.5.1 =
* bugFix: if the Wordpress instance was running jQuery 1.8--  mobile devices where not detected.

= 1.5.0 =
* bugFix: Fixed (Again) a bug introduced in the 1.4.8 update that prevent the player to work on IE browser.

...


= 0.1 =
* First release

== Frequently Asked Questions ==

= I installed the plugin and the players are correctly displayed but the customization button doesn't show in the post editor =

 You have to check the "Activate the player customizer in the post editor" to make it available in the post editor.

= I inserted my mp3 file using the add media button but even when I select or click on the inserted link the customization button is disabled =

Be sure that once you inserted the file you set the "Attachment Display Settings -> Link" to "media file" in the media file window.

= I installed the plugin and now I can't display the post editor in "visual" mode anymore =

This is probably due to insufficient user permissions on your server that cause an error loading the TinyMCE mb.miniAudioPlayer component. You should try download the component locally on your computer, unzip it and upload the folder via FTP in the remote plugins folder.

= I installed the plugin but on the page there's only the link to the mp3 file and not the player =

Probably there's a conflict with some other installed plugin or even with the theme you are using.
Try first deactivating all the other plugins; if it works then reactivate them one by one to find out which is getting in conflict; if it doesn't work then there's a conflict with your theme.

= The player is working fine but it display differently from the examples you gave =

The appearance of the player is all defined in the "miniplayer.css" file located in the "css" folder of the plugin root. You maybe have some CSS classes that are overwriting the plugin ones.
You can inspect the player using the developer tools within the browser to find out which class is overwritten and fix the problem changing the theme css.

= It doesn't work on Firefox and neither on IE, what is the problem? =

On both those browsers the player falls back to the Flash® solution instead of the standard HTML5; That because FF doesn't accept mp3 files natively and IE is a mess :-).
So if the player doesn't work on them probably is your Flash plugin that is not update or is not working correctly.

= It doesn't work on Ubuntu Chromium =

Due to some codecs missing in the default Ubuntu Chromium install the player will not work. This problem can be resolved by simply by installing that codecs via console:
sudo apt-get-install chromium-codecs-ffmpeg-extra.
After that your player should work fine.

= I can't display the download button on touch devices =

The download button is intentionally removed on touch devices as it is impossible to download the file on that devices.

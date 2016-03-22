<?php

if( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

// Uninstall all the mbMiniAudioPlayer settings
delete_option('miniAudioPlayer_version');
delete_option('miniAudioPlayer_donate');

delete_option('miniAudioPlayer_getMetadata');
delete_option('miniAudioPlayer_width');
delete_option('miniAudioPlayer_skin');
delete_option('miniAudioPlayer_animate');
delete_option('miniAudioPlayer_volume');
delete_option('miniAudioPlayer_autoplay');
delete_option('miniAudioPlayer_showVolumeLevel');
delete_option('miniAudioPlayer_showTime');
delete_option('miniAudioPlayer_showRew');
delete_option('miniAudioPlayer_excluded');
delete_option('miniAudioPlayer_download');
delete_option('miniAudioPlayer_download_security');
delete_option('miniAudioPlayer_customizer');
delete_option('miniAudioPlayer_custom_skin_css');
delete_option('miniAudioPlayer_custom_skin_name');
delete_option('miniAudioPlayer_add_gradient');



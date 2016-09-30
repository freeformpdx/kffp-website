<?php
/*
* Plugin Name Native Emoji
* Version 2.0.2
* Author Davabuu Designs
*/

// Required files
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );
include('lib/emoji.php');

// Check if post content
if($_POST){
	// Post Vars
	$code = emoji_unified_to_html($_POST['code']);
	$img = $_POST['img'];
	// SQL vars
	global $wpbd;
	$table_name = $wpdb->prefix . 'nep_native_emoji';
	$uid = get_current_user_id(); 
	$time = current_time( 'mysql' );
	// Insert Data to table
	$existent_emoji = $wpdb->get_row("SELECT * FROM $table_name WHERE code = '$code' AND uid = '$uid'");
	if ($existent_emoji == null) {
		$wpdb->insert( 
			$table_name, 
			array('time' => $time, 'code' => $code, 'img' => $img, 'uid' => $uid), 
			array('%s','%s','%s','%d') 
		);
	}
	else {
		$wpdb->update( 
			$table_name, 
			array('time' => $time,), 
			array('code' => $code), 
			array('%s'), 
			array('%s') 
		);	
	}
}
?>
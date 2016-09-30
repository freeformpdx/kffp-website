<?php
/*
* Plugin Name: Native Emoji
* Plugin URI: http://native-emoji.davabuu.com/
* Description: This is not just a plugin, this is the plugin for use <cite>emoji</cite> in a native way. When activated you will see a new button in your wordpress editor, from there you will be able to include more than 1,000 emojis in to your posts, pages, and custom posts type.
* Version: 2.0.2
* Author: Davabuu Designs
* Author URI: https://davabuu.net
* Text Domain: native_emoji
* Domain Path: /lang
*/



// Define Plugin Class
class WP_nep_Native_Emoji{

  	// Constructor
	function __construct() {
						
		// Add the plugin filters and actions
		add_action( 'plugins_loaded', array( $this, 'nep_localize_plugin' ));
		add_action( 'admin_enqueue_scripts', array( $this, 'nep_emoji_register_and_enqueue_file' ));
		add_filter( 'mce_buttons', array( $this, 'nep_emoji_register_buttons' ));
		add_filter( 'mce_external_plugins', array( $this, 'nep_emoji_register_tinymce_javascript' ));
		foreach ( array('post.php','post-new.php') as $hook ) {
			add_action( "admin_head-$hook", array( $this, 'nep_emoji_localize_tinymce_javascript' ));
		}
		add_action( 'admin_notices', array( $this, 'nep_emoji_activation_msg' ));
		
		// Register activation and desactivation hook
		register_activation_hook( __FILE__, array( $this, 'nep_emoji_install' ) );
		register_deactivation_hook( __FILE__, array( $this, 'nep_emoji_uninstall' ) );
		
	}
	
	// Localize The Plugn
	function nep_localize_plugin() {
		
		load_plugin_textdomain( 'native_emoji', FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
		
	}		
	
	// Register and enqueue CSS and JS files
	function nep_emoji_register_and_enqueue_file(){
		
		// Register required files
		wp_register_style( 'nep_native-emoji',  plugins_url('/css/style.css',__FILE__), false, '1.0', 'all' );
		wp_register_script( 'nep_native-emoji', plugins_url('/js/script.js',__FILE__), false, '1.0', true );
		// Enqueue required files
		wp_enqueue_style( 'nep_native-emoji' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'nep_native-emoji' );
		
	}
	
	// Register plugin button				
	function nep_emoji_register_buttons($buttons) {
		
	   array_push($buttons, 'separator', 'nep_native_emoji');
	   return $buttons;
	   
	}
	
	// Register tinymce pluglin
	function nep_emoji_register_tinymce_javascript($plugin_array) {
		
	   $plugin_array['nep_native_emoji'] = plugins_url('/js/tinymce-plugin.js',__FILE__);
	   return $plugin_array;
	   
	}
	
	// Localize tinymce and add vars to js plugin
	function nep_emoji_localize_tinymce_javascript() {
		
		// Required files
		include('lib/emoji.php');
		
		// Add inline script
		global $wpdb, $locale;
		$plugin_url = plugins_url( '/', __FILE__ );
		$table_name = $wpdb->prefix . 'nep_native_emoji';
		$uid = get_current_user_id(); 
		$frequently_emojis = $wpdb->get_results( "SELECT * FROM $table_name WHERE uid = '$uid' ORDER BY time DESC LIMIT 0,80");?>
        <!-- TinyMCE Native Emoji Plugin -->
		<script type='text/javascript'>
        var nep_emoji_plugin = {
            'nep_url'				   	: '<?php echo $plugin_url; ?>',
			'nep_emoji_name'		   	: '<?php _e('Native Emoji', 'native_emoji');?>',
			'nep_emoji_frequently_used'	: '<?php _e('Frequently Used', 'native_emoji');?>',
			'nep_emoji_smileys'		   	: '<?php _e('Smileys', 'native_emoji');?>',
			'nep_emoji_people'		   	: '<?php _e('People', 'native_emoji');?>',
			'nep_emoji_animals_nature' 	: '<?php _e('Animals & Nature', 'native_emoji');?>',
			'nep_emoji_food_drink'	   	: '<?php _e('Food & Drink', 'native_emoji');?>',
			'nep_emoji_activity_sports'	: '<?php _e('Activity & Sports', 'native_emoji');?>',
			'nep_emoji_travel_places'   : '<?php _e('Travel & Places', 'native_emoji');?>',
			'nep_emoji_objects_symbols' : '<?php _e('Objects & Symbols', 'native_emoji');?>',
			'nep_emoji_flags' 			: '<?php _e('Flags', 'native_emoji');?>',
			'nep_frequently_codes'		: [{<?php $i= 0; foreach($frequently_emojis as $emoji){echo '"'.emoji_html_to_unified($emoji->code).'":"'.$emoji->img.'",'; $i++; if($i % 10 === 0)print "}, {";}print "}]";?>
        };
        </script>
        <!-- TinyMCE Native Emoji Plugin -->
        <?php
		
	}
	
	// Display Activation Message
	function nep_emoji_activation_msg() {	
	
		if(is_plugin_active('native-emoji/index.php') && !get_option('nep_native_emoji_active')){
			// Add plugin options
			add_option( 'nep_native_emoji_active', 'true' );
			
			// Display Message
			$read_more     = '<a href="http://native-emoji.davabuu.com">'.__('read more', 'native_emoji').'</a>';
			$leave_review  = '<a href="https://wordpress.org/support/view/plugin-reviews/native-emoji">'.__('leave a review', 'native_emoji').'</a>';
			echo '<div id="message" class="updated notice is-dismissible"><p>';
			printf(
				__( 'Thanks for installing Emoji Native Plugin, before using you may want to %1$s about this plugin. If you like this plugin, please %2$s.', 'native_emoji' ),
				$read_more,
				$leave_review
			);
			echo '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">'.__('Discard this notice', 'native_emoji').'</span></button></div>';
		}
		
	}
	
	// Install the plugin
    function nep_emoji_install() {
		
		// Required files
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		// Create Frecuently Used Table
		global $wpdb;
		$table = $wpdb->prefix . 'nep_native_emoji';
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			code varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
			img varchar(255) NOT NULL,
			uid mediumint(9) NOT NULL,
			UNIQUE KEY id (id)
		)  $charset_collate;";			
		dbDelta( $sql );
		
    }
	
	
	// Uninstall the plugin
    function nep_emoji_uninstall() {
		
		global $wpdb;
        $table = $wpdb->prefix . 'nep_native_emoji';
		// Delete Plugin Options
		delete_option( 'nep_native_emoji_active' );
		// Delete Frecuently Used Table
		$wpdb->query("DROP TABLE IF EXISTS $table");
		
    }

}

new WP_nep_Native_Emoji();
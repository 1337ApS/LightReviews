<?php
/*
Plugin Name: LightReviews
Description: An ultra-lightweifght Review system for Wordpress. It actually doesn't do a thing, unless you specifically tell it to.
Author: 1337 ApS
Version: 1.0
Author URI: http://1337.dk/
*/

define('LRV_VERSION', '1.0');
define('LRV_PLUGIN_FILE', __FILE__);
define('LRV_PLUGIN_DIR', trailingslashit(plugin_dir_path(__FILE__)));
define('LRV_PLUGIN_URL', trailingslashit(plugin_dir_url(__FILE__)));
define('LRV_DEBUG', true);
define('LRV_LANG', 'lightreviews');

class LightReviews {
	public function __construct() {
		spl_autoload_register(array(&$this, 'autoloader'));
		add_action( 'plugins_loaded', array(&$this, 'initialize_wpdb_tables'));
		register_activation_hook( __FILE__, array(&$this, 'install') );
		
		$this->load_files();
	}
	
	private function load_files(){
		new LRVAdminSettings();
		new LRVCommentFields();
	}
	
	public function install(){		
		global $wpdb;
		
		$this->initialize_wpdb_tables();
		
		$sql = array();
		
		$sql[] = "CREATE TABLE " . $wpdb->lrv_log . " (
					id INT(11) AUTO_INCREMENT NOT NULL,
					event VARCHAR(100) NOT NULL,
					level VARCHAR(100) NOT NULL DEFAULT 'notice',
					description TEXT,
					details LONGTEXT,
					logtime INT(11) NOT NULL,
					PRIMARY KEY  (id)
				);";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
		foreach($sql as $s)
			dbDelta($s);
	}
	
	public function initialize_wpdb_tables(){
		global $wpdb;
		$wpdb->lrv_log = $wpdb->prefix."lightreviews_log";
	}
	
	private function autoloader($class){
		$path = dirname(__FILE__).'/';
		$paths = array();
		$exts = array('.php', '.class.php');
		
		$paths[] = $path;
		$paths[] = $path.'lib/';
				
		foreach($paths as $p)
			foreach($exts as $ext){
				if(file_exists($p.$class.$ext)){
					require_once($p.$class.$ext);
					return true;
				}
			}
		
		return false;
	}
}
new LightReviews();
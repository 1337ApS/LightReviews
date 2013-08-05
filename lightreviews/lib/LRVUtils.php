<?php

class LRVUtils {
	public static function get_template( $tpl ){
		$child_theme = trailingslashit(get_stylesheet_directory());
		$theme = trailingslashit(get_template_directory());
		if(file_exists($child_theme . $tpl))
			return $child_theme . $tpl;
		elseif(file_exists($theme . $tpl))
			return $theme . $tpl;
		else
			return LRV_PLUGIN_DIR . $tpl;
	}
	
	public static function get_option($key, $default = ''){
		$options = get_option('lrv_settings', null);
		
		if(!is_array($options)){
			$options = self::get_default_options();
			update_option('lrv_settings', $options);
		}
		
		return (isset($options[$key]) ? $options[$key] : $default);
	}
	
	public static function install_options(){
		$settings = get_option('lrv_settings', null);
		
		// Check if any settings have been set
		if(empty($settings)){
			// Set the default values
			$default = self::get_default_options();
			update_option('lrv_settings', $default);
		}
	}
	
	private static function get_default_options(){
		return array(
			'post_types' => array()
		);
	}
}

<?php
class LRVAdminSettings {
	public function __construct() {
		add_action( 'admin_init', array(&$this, 'register_settings'));
	}
	
	public function register_settings(){
		register_setting(
			'discussion',							// settings page
			'lrv_settings',							// option name
			array(&$this, 'validate_options')		// validation callback
		);

		add_settings_field(
			'lrv_post_types',						// id
			__( 'LightReview Post Types'),			// setting title
			array(&$this, 'render_post_types'),		// display callback
			'discussion',							// settings page
			'default'								// settings section
		);
	}
	
	public function render_post_types(){
		$post_types = get_post_types(array('public' => true), 'objects');
		$activated_post_types = LRVUtils::get_option('post_types');
		
		foreach($post_types as $key => $pt)
			printf('<label><input type="checkbox" value="%1$s" name="lrv_settings[post_types][]" %3$s/> %2$s</label><br/>', $key, $pt->labels->name, (in_array($key, $activated_post_types) ? 'checked' : ''));
	}
	
	public function validate_options( $input ){
		return $input;
	}
}
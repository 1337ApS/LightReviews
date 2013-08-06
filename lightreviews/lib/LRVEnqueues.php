<?php
class LRVEnqueues {
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array(&$this, 'enqueue') );
	}
	
	public function enqueue(){
		wp_enqueue_style( 'lightreviews', LRV_PLUGIN_URL . 'assets/css/main.css');
		
		wp_register_script( 'lightreviews', LRV_PLUGIN_URL . 'assets/js/lightreviews.js', array('jquery') );
		wp_enqueue_script( 'lightreviews' );
		
		wp_localize_script( 'lightreviews', 'lrv_data', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'reset' => __('(reset)', LRV_LANG)
		));
	}
}

<?php

class LRVCommentFields {
	public function __construct() {
		add_filter( 'comment_form_default_fields', array(&$this, 'remove_fields' ), 99 );
		add_action( 'comment_form_logged_in_after', array(&$this, 'add_fields') );
		add_action( 'comment_form_after_fields', array(&$this, 'add_fields') );
		add_action( 'comment_post', array(&$this, 'save_meta') );
		add_filter( 'preprocess_comment', array(&$this, 'required_fields') );
		add_filter( 'comment_text', array(&$this, 'comment_text'));
		add_action( 'set_comment_cookies', array(&$this, 'comment_cookies'), 10, 2 );
	}
	
	public function comment_cookies( $comment, $user ){
		if ( $user->exists() || !$this->post_active_post_type($comment->comment_post_ID) )
			return;

		$city = get_comment_meta( $comment->comment_ID, 'lrv_city', true );
		
		$comment_cookie_lifetime = apply_filters('comment_cookie_lifetime', 30000000);
		setcookie('comment_author_city_' . COOKIEHASH, $city, time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN);
	}
	
	public function comment_text( $text ){
		$comment_id = get_comment_ID();
		if(!$this->active_post_type($comment_id))
			return $text;
		
		if( $commentrating = get_comment_meta( $comment_id, 'lrv_rating', true ) )
			$text .= '<p class="lrv-rating" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating"><meta itemprop="worstRating" content="1"><span itemprop="ratingValue">'.$commentrating.'</span>/<span itemprop="bestRating">5</span> stars</p>';
		
		return $text;
	}
	
	public function required_fields( $comment_data ){
		if(!$this->post_active_post_type( $comment_data['comment_post_ID']))
			return $comment_data;
		
		//if( !isset( $_POST['rating'] ) )
		//	wp_die( __( 'Error: You did not add a rating. Hit the Back button on your Web browser and resubmit your comment with a rating.', LRV_LANG ) );
		if( !isset( $_POST['city'] ) )
			wp_die( __( 'Error: You did not input your city. Hit the Back button on your Web browser and resubmit your comment.', LRV_LANG ) );
		if( isset($_POST['rating']) && absint($_POST['rating']) > 0 && absint($_POST['rating']) < 6 && $this->already_rated($comment_data['comment_author_email'], $comment_data['comment_post_ID']) )
			wp_die( __( 'Error: You can only rate the same post once.', LRV_LANG ) );
		
		return $comment_data;
	}
	
	public function save_meta( $comment_id ){
		if(isset($_POST['city'])){
			$city = wp_filter_nohtml_kses($_POST['city']);
			
			add_comment_meta( $comment_id, 'lrv_city', $city );
		}
		
		if(isset($_POST['rating'])){
			$rating = absint($_POST['rating']);
			
			// Check if we should not rate
			if($rating == -1)
				return;
			
			if($rating < 1 || $rating > 5)
				$rating = 3;
			
			add_comment_meta( $comment_id, 'lrv_rating', $rating );
		}
	}
	
	public function remove_fields( $fields ){
		if(!$this->active_post_type())
			return $fields;
		
		// Remove URL field
		if(isset($fields['url']))
			unset($fields['url']);
		
		return $fields;
	}
	
	public function add_fields(){
		if(!$this->active_post_type())
			return;
		
		$commenter = wp_get_current_commenter();
		
		// Get the commenter city, if any
		$comment_author_city = '';
		if ( isset($_COOKIE['comment_author_city_'.COOKIEHASH]) )
			$comment_author_city = $_COOKIE['comment_author_city_'.COOKIEHASH];
		
		echo '<p class="comment-form-city"><input tabindex="3" id="city" name="city" type="text" value="' . esc_attr($comment_author_city) . '" size="30" class="txt" /><label for="city">'. __('City (required)', LRV_LANG) . '</label>';
		
		// Check whether we should show the rating box
		$show_rating = true;
		if(!empty($commenter['comment_author_email'])){
			$show_rating = !$this->already_rated($commenter['comment_author_email'], get_the_ID());
		}
		
		if($show_rating){
			echo '<p class="comment-form-rating"><select tabindex="4" id="rating" name="rating">';
			echo '<option value="-1">' . __('- No rating -', LRV_LANG) . '</option>';
			for($i = 1; $i <= 5; $i++)
				printf('<option value="%1$d">%1$d</option>', $i);

			echo '</select><label for="rating">' . __('Rating') . '</label>';
		}
	}
	
	private function active_post_type( $comment_id = null ){
		$activated_post_types = LRVUtils::get_option('post_types');
		
		if($comment_id == null){
			return in_array(get_post_type(), $activated_post_types);
		}else{
			// Get comment post and check it's post type
			$comment = get_comment( $comment_id );
			return $this->post_active_post_type( $comment->comment_post_ID );
		}
	}
	
	private function post_active_post_type( $post_id ){
		$activated_post_types = LRVUtils::get_option('post_types');
		
		$post_type = get_post_type( $post_id );
		
		return in_array($post_type, $activated_post_types);
	}
	
	private function already_rated( $email, $post_id ){		
		$args = array(
			'author_email' => $email,
			'post_id' => $post_id,
			'number' => 1
		);
		$c = get_comments( $args );
		
		return count($c) > 0;
	}
}
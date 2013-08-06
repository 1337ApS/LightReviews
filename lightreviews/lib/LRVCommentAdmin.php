<?php

class LRVCommentAdmin {
	public function __construct() {
		// Admin actions
		add_filter( 'manage_edit-comments_columns', array(&$this, 'admin_columns') );
		add_filter( 'manage_comments_custom_column', array(&$this, 'admin_column_data' ), 10, 2);
		add_filter( 'comment_author', array(&$this, 'admin_comment_author') );
		add_action( 'add_meta_boxes_comment', array(&$this, 'admin_meta_boxes') );
		add_action( 'edit_comment', array(&$this, 'admin_save_comment') );
	}
	
	public function admin_save_comment( $comment_id) {
		if( ! isset( $_POST['lightreviews_comment_update'] ) || ! wp_verify_nonce( $_POST['lightreviews_comment_update'], 'lightreviews_comment_update' ) ) return;
		
		
		if ( ( isset( $_POST['title'] ) ) && ( $_POST['title'] != '') ){
			$title = wp_filter_nohtml_kses($_POST['title']);
			update_comment_meta( $comment_id, 'lrv_title', $title );
		}else
			delete_comment_meta( $comment_id, 'lrv_title');
		
		if ( ( isset( $_POST['city'] ) ) && ( $_POST['city'] != '') ){
			$city = wp_filter_nohtml_kses($_POST['city']);
			update_comment_meta( $comment_id, 'lrv_city', $city );
		}else
			delete_comment_meta( $comment_id, 'lrv_city');
		
		if ( (isset( $_POST['rating']) ) && ( $_POST['rating'] != '') ){
			$rating = absint(wp_filter_nohtml_kses($_POST['rating']));
			update_comment_meta( $comment_id, 'lrv_rating', $rating );
		}else
			delete_comment_meta( $comment_id, 'lrv_rating');
	}
	
	public function admin_meta_boxes(){
		add_meta_box( 'lightreview-meta-data', __( 'LightReview Meta Data' ), array(&$this, 'admin_render_meta_box'), 'comment', 'normal', 'high' );
	}
	
	public function admin_render_meta_box( $comment ){
		$title = get_comment_meta( $comment->comment_ID, 'lrv_title', true );
		$city = get_comment_meta( $comment->comment_ID, 'lrv_city', true );
		$rating = absint(get_comment_meta( $comment->comment_ID, 'lrv_rating', true ));
		
		wp_nonce_field( 'lightreviews_comment_update', 'lightreviews_comment_update', false );
		
		?>
		<p>
			<label for="title"><?php _e( 'Title', LRV_LANG ); ?></label>
			<input type="text" name="title" value="<?php echo esc_attr( $title ); ?>" class="widefat" />
		</p>
		<p>
			<label for="city"><?php _e( 'Author City', LRV_LANG ); ?></label>
			<input type="text" name="city" value="<?php echo esc_attr( $city ); ?>" class="widefat" />
		</p>
		<p>
			<label for="rating-1"><?php _e( 'Rating: ', LRV_LANG ); ?></label>
				<span class="commentratingbox">
				<?php for( $i=1; $i <= 5; $i++ ) {
					echo '<span class="commentrating"><input type="radio" name="rating" id="rating-' . $i . '" value="'. $i .'"';
					if ( $rating == $i ) echo ' checked="checked"';
					echo ' />'. $i .' </span>';
					}
				?>
				</span>
		</p>
		<?php
	}
	
	public function admin_comment_author( $author ){
		if(!is_admin())
			return $author;
		
		$id = 0;
		$comment = get_comment( $id );
		if($city = get_comment_meta( $comment->comment_ID, 'lrv_city', true ))
			return $author . '<br/>' . $city;
		return $author;
	}
	
	public function admin_column_data( $column_name, $comment_id ){
		if($column_name == 'lrv_rating' && ($commentrating = get_comment_meta( $comment_id, 'lrv_rating', true )))
			echo $commentrating;
		if($column_name == 'lrv_title' && ($commenttitle = get_comment_meta( $comment_id, 'lrv_title', true )))
			echo $commenttitle;
	}
	
	public function admin_columns( $cols ){
		$cols['lrv_title'] = __('Title', LRV_LANG);
		$cols['lrv_rating'] = __('Rating', LRV_LANG);
		return $cols;
	}
}
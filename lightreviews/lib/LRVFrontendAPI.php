<?php
class LRVFrontendAPI {
	public function __construct() {
		add_action( 'lrv_aggregate_rating', array(&$this, 'render_aggregate') );
		add_action( 'lrv_comment_stars', array(&$this, 'render_comment_stars'), 10, 2 );
		add_filter( 'lrv_comment_is_review', array(&$this, 'is_review') );
		add_action( 'lrv_comment_title', array(&$this, 'render_comment_title') );
	}
	
	public function is_review( $comment_id ){
		$rating = absint(get_comment_meta( $comment_id, 'lrv_rating', true ));
		
		if($rating < 1 || $rating > 5)
			return false;
		return true;
	}
	
	public function render_comment_stars( $comment_id, $prefix = '' ){
		$rating = absint(get_comment_meta( $comment_id, 'lrv_rating', true ));
		
		if($rating < 1 || $rating > 5)
			return;
		
		echo '<span itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="lrv-rating-wrapper"><span class="lrv-stars-prefix">' . $prefix . '</span><meta itemprop="worstRating" content="1"><span itemprop="ratingValue" class="lrv-stars lrv-stars-' . $this->number_to_text( $rating ) . '">' . $rating . '</span><meta itemprop="bestRating" content="5"></span>';
	}
	
	public function render_comment_title( $comment_id ){
		$title = get_comment_meta( $comment_id, 'lrv_title', true );
		
		if(empty($title))
			return;
		
		echo '<span class="lrv-comment-title">' . $title . '</span>';
	}
	

	public function render_aggregate( $post_id = 0 ){
		$comments = get_comments( array( 'post_id' => $post_id, 'status' => 'approve' ) );
		
		$total_reviews = 0;
		$total_rating = 0;
		
		foreach($comments as $comment){
			$rating = absint( get_comment_meta( $comment->comment_ID, 'lrv_rating', true ) );
			
			if($rating < 1 || $rating > 5)
				continue;
			
			$total_reviews++;
			$total_rating += $rating;
		}
		
		if($total_reviews == 0)
			return;
		
		$avg_rating = $total_rating / $total_reviews;
		?>

		<div class="lightreviews-aggregate-rating" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
			<meta itemprop="worstRating" content="1">
			<span itemprop="ratingValue" class="lrv-stars lrv-stars-<?php echo $this->number_to_text( $avg_rating ); ?>">
				<?php echo $rating; ?>
			</span>
			<meta itemprop="bestRating" content="5">
			<span class="lrv-reviews"><?php printf(_n('(<span itemprop="reviewCount">%d</span> review)', '(<span itemprop="reviewCount">%d</span> reviews)', $total_reviews, LRV_LANG), $total_reviews); ?></span>
		</div>

		<?php
	}
	
	private function number_to_text( $number ){
		$no = floor( $number );
		
		$half = '';
		if($number - $no >= 0.5)
			$half = '-half';
		
		if($no == 1)
			return 'one' . $half;
		if($no == 2)
			return 'two' . $half;
		if($no == 3)
			return 'three' . $half;
		if($no == 4)
			return 'four' . $half;
		if($no == 5)
			return 'five' . $half;
	}
}

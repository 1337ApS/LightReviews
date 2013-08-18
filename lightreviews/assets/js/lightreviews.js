(function($){
	$.fn.LightReviews = function(){
		var save_stars = false;
		var $reset = $('<a/>');
		var $container = this;
		$container.addClass('lightreview-wrapper');
		var $star_container = $('<div/>');
		$star_container.addClass('lrv-stars lrv-stars-container');
		
		var hover_handler_in = function(){
			save_stars = false;
			var rating = $(this).prevAll('.lr-star').length + 1;

			$(this).prevAll('.lrv-star').removeClass('lrv-star-none').addClass('lrv-star-one');
			$(this).removeClass('lrv-star-none').addClass('lrv-star-one');
			$(this).nextAll('.lrv-star').removeClass('lrv-star-one').addClass('lrv-star-none');
		};

		var hover_handler_out = function(){
			if(save_stars)
				return;
			
			$('.lrv-star', $star_container).removeClass('lrv-star-one');
			
			var rating = parseInt($('input[name="rating"]', $container).val());
			
			if(rating < 1)
				return;
			
			for(var i = 1; i <= rating; i++)
				$('.lrv-star:nth-child(' + i + ')', $star_container).addClass('lrv-star-one');
		};

		var click_stars = function(){
			save_stars = true;
			var rating = $('.lrv-star.lrv-star-one', $star_container).length;
			$('input[name="rating"]', $container).val(rating);
			$reset.show();
			$(document).trigger('lrv-click-stars');
		};
		
		var reset_stars = function(){
			$('.lrv-star-one', $star_container).removeClass('lrv-star-one');
			$('input[name="rating"]', $container).val('-1');
			$reset.hide();
		};
		
		for(var i = 0; i < 5; i++){
			var $star = $('<div/>');
			$star.addClass('lrv-star lrv-star-none');
			
			$star.click(click_stars);
			$star.hover(hover_handler_in, hover_handler_out);
			
			$star_container.append($star);
		}
		
		
		$reset.attr('id', 'lrv-reset-rating');
		$reset.attr('href', 'javascript:void(0);');
		$reset.text(lrv_data.reset);
		$reset.hide();
		$reset.click(reset_stars);
		
		$container.append('<input type="hidden" name="rating" value="-1"/>');
		$container.append($star_container);
		$container.append($reset);
		return this;
	};
})(jQuery);

var lightreviews = (function($){
		
	return {
		initialize: function(){
			$('.lrv-comment-ratings').LightReviews();
		}
	};
})(jQuery);
jQuery(document).ready(lightreviews.initialize);
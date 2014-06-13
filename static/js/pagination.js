(function($){
	$(document).ready(function(){	
			 
		var first_page = 1;
		var block_class_prefix = "page";
		var bullet_id_prefix = "page-change";
		var bullet_class = "pages-changer";
		var bullet_list_id = "pages-changer-block";
		var toggle_next_id = "page-toogle-next";
		var toggle_prev_id = "page-toogle-prev";
		var active_class = "opened";
		var separator = "--";
		var animation_speed = 'fast';
		
		
		function showOnly(page) {
			if (!page) { page = first_page; }
			
			for (i=first_page; i<=last_page; i++) {
				$('#' + bullet_id_prefix + separator + i).removeClass(active_class);
				
				if (i == page) {
					$('.' + block_class_prefix + separator + i).show(animation_speed);
					$('#' + bullet_id_prefix + separator + i).addClass(active_class);
					
					// Add image for current url
					$('#' + bullet_id_prefix + separator + i).append(current_page_html);
				}
				else {
					$('.' + block_class_prefix + separator + i).hide(animation_speed);
					
					// Reset inner html
					$('#' + bullet_id_prefix + separator + i).html(i);
				}
			}
		}
		
				
		// Show first text by default
		showOnly(first_page);
		
		// Handler for bullet clicks
		$('.' + bullet_class).on("click", function(){			
			var number_page = $(this).attr('id').split(separator)[1];
			
			showOnly(number_page);
		});
		
		// Handler for next page
		$('#' + toggle_next_id).on('click', function() { 
			var current_page = $('#' + bullet_list_id + ' .' + active_class).attr('id').split(separator)[1]*1;
			var next_page = (((current_page + 1) > last_page) ? first_page : (current_page + 1));			

			showOnly(next_page);
		});
		
		// Handler for prev page
		$('#' + toggle_prev_id).on('click', function() { 
			var current_page = $('#' + bullet_list_id + ' .' + active_class).attr('id').split(separator)[1]*1;
			var prev_page = (((current_page - 1) < first_page) ? last_page : (current_page - 1));	
			
			showOnly(prev_page);
		});
	})
})(jQuery); 

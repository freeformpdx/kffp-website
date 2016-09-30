/*
* Plugin Name Native Emoji
* Version 2.0.2
* Author Davabuu Designs
*/

(function($) {
	$(document).on('click', 'div.nep_mce-caller img.scroll', function(e){
		e.preventDefault();
		var $destiny = "#" + $(this).attr('data-target'),
			newOffset =	($(""+$destiny+"").position().top + $('div.nep_mce-shown').scrollTop() - 42);
			
		$('div.nep_mce-caller img.scroll').removeClass('active');
		$(this).addClass('active');
		$('div.nep_mce-shown').scrollTop(newOffset);

		
		if(newOffset >= 1 ){
			$('span.nep_mce-up-arrow').removeClass('nep_mce-arrow-inactive');
		}
		else if(newOffset <= 0){
			$('span.nep_mce-up-arrow').addClass('nep_mce-arrow-inactive');
			$('span.nep_mce-up-arrow').removeClass('nep_mce-arrow-active');
		}
		else if(newOffset <= 4832){
			$('span.nep_mce-down-arrow').removeClass('nep_mce-arrow-inactive');
		}
		else if(newOffset >= 4833){
			$('span.nep_mce-down-arrow').addClass('nep_mce-arrow-inactive');
			$('span.nep_mce-down-arrow').removeClass('nep_mce-arrow-active');
		}
		
		return false;
		
	});
	
	var scrollHandle = 0,
        scrollStep = 10;		
		
	$(document).on('mouseenter', 'span.nep_mce-down-arrow', function(){ 
		if(!$(this).hasClass('nep_mce-arrow-inactive')){
	        $(this).addClass('nep_mce-arrow-active');
    	    startScrolling(1, scrollStep);
		}
	});
	$(document).on('mouseenter', 'span.nep_mce-up-arrow', function(){ 
		if(!$(this).hasClass('nep_mce-arrow-inactive')){
	        $(this).addClass('nep_mce-arrow-active');
    	    startScrolling(-1, scrollStep);
		}
	});
	$(document).on('mouseleave', 'span.nep_mce-arrow', function(){
		stopScrolling();
        $(this).removeClass('nep_mce-arrow-active');
	});	
	
	$(document).on('click', 'table#nep_mce-emoji-icons a[role=option]', function(e){
		e.preventDefault();
		var img = $(this).attr('data-img');
		var code = $(this).attr('data-code');
		$.ajax({
			type: "POST",
			url: nep_emoji_plugin.nep_url + "db.php",
		    dataType:"html", 
			data: {
				"code": code,
				"img": img
			},
			success: function (data) {
			}
		});
	});	
	
	
	function startScrolling(modifier, step) {
        if (scrollHandle === 0) {
            scrollHandle = setInterval(function () {
                var newOffset = ($('div.nep_mce-shown').scrollTop() + (scrollStep * modifier));
                $('div.nep_mce-shown').scrollTop(newOffset)	
				if(newOffset >= 1 ){
					$('span.nep_mce-up-arrow').removeClass('nep_mce-arrow-inactive');
				}
				else if(newOffset <= 0){
					$('span.nep_mce-up-arrow').addClass('nep_mce-arrow-inactive');
					$('span.nep_mce-up-arrow').removeClass('nep_mce-arrow-active');
				}
				else if(newOffset <= 4832){
					$('span.nep_mce-down-arrow').removeClass('nep_mce-arrow-inactive');
				}
				else if(newOffset >= 4833){
					$('span.nep_mce-down-arrow').addClass('nep_mce-arrow-inactive');
					$('span.nep_mce-down-arrow').removeClass('nep_mce-arrow-active');
				}
				
				$('div.nep_mce-type').each(function() {
                    var id = $(this).attr('id');
					if(newOffset >= ($("#"+ id).position().top + $('div.nep_mce-shown').scrollTop() - 42)
					&& (($("#"+ id).position().top + $('div.nep_mce-shown').scrollTop() - 42) + $("#"+ id).height())){
						$('div.nep_mce-caller img.scroll').removeClass('active');
						$("img[data-target="+ id +"]").addClass('active');
					}
                });
				
            }, 10);			
        }
    }
	
    function stopScrolling() {
        clearInterval(scrollHandle);
        scrollHandle = 0;
    }		
})(jQuery);
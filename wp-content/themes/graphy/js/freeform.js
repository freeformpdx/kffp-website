(function($) {
  $('.listen-cta').mb_miniPlayer();
})(jQuery);

/*
$(function() {
  var $streamCTA = $('.listen-cta'),
    streamURL = $streamCTA.attr('href'),
    isPlaying = false;
  
  $streamCTA.on('click', function(ev) {
    ev.preventDefault();
    
    if (!isPlaying) {
      
    }
    
    isPlaying = !isPlaying;
  });
});
*/
<?php
/**
 * @package Graphy
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>


		<?php graphy_header_meta(); ?>
		<?php if ( has_post_thumbnail() ): ?>
		<div class="post-thumbnail"><?php the_post_thumbnail(); ?></div>
		<?php endif; ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
	  <?php
	  $id = get_the_ID();
	  $custom_fields = get_post_custom();
	  ?>
	  <h2 class="dj-name"><?= $custom_fields['dj_name'][0] ?></h2>
	  
	  <h3 class="timeslot"><?= get_timeslot($id, true) ?></h3>
	  
		<?php the_content(); ?>
		<?php wp_link_pages( array(	'before' => '<div class="page-links">' . __( 'Pages:', 'graphy' ), 'after'  => '</div>', ) ); ?>
	</div><!-- .entry-content -->
	<?php graphy_footer_meta(); ?>
</article><!-- #post-## -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
$showID = $custom_fields['id'][0];
?>

<? // if dj is logged in, show the show id and a link to create a playlist ?>
<?php if (is_user_logged_in ()) {
?>
<div class="dj-login-wrapper">
<div class="dj-login">
<p class="show-id">KFFP ID: <?= $showID ?></p>
<a href='http://kffp.rocks/api/newSetlist/<?= $showID ?>' target='_blank'>Make New Playlist</a>
</div>
</div>

<?php } ?>

<ul class="playlist-list"></ul>

<script>
if (typeof jQuery !== 'undefined') {
  (function($) {
    function formatDate(date) {
      var monthNames = [
        "January", "February", "March",
        "April", "May", "June", "July",
        "August", "September", "October",
        "November", "December"
      ];
      
      var pieces = date.split('-'),
          output = monthNames[parseInt(pieces[1]) - 1] + ' ' + parseInt(pieces[2]) + ', ' + pieces[0];
      
      return output;
    }
    
    window.formatDate = formatDate;
    
    function fetchPlaylistInfo(showID) {
      var $playlistList = $('<ul>');
      
      $.ajax({
        url: 'http://kffp.rocks/api/setlistsByShowID/' + showID,
        dataType: 'json',
        success: function(data) {
          if (!data.length) {
            $playlistList.append("<li class='loading'>This DJ hasn't created any playlists</li>");
            clearInterval(playlistFetchingInterval);
          }
        
          for (var i = data.length - 1; i >= 0; i--) {
            var playlist = data[i],
            played = false,
            playlistDate = formatDate(playlist.createdAt.split('T')[0]),
            $thisPlaylist = $('<li>');
          
            $thisPlaylist.append('<h3>' + playlistDate + '</h3>');
          
            var $songs = $('<ul class="playlist-songs">').appendTo($thisPlaylist);
          
            $(playlist.songs).each(function(i, song) {
              if (song.played && song.inputs[0].value != 'KFFP 90.3') {
                played = true;

                var songTimeStamp = new Date(Date.parse(song.playedAt));
              
                var songHTML = '<li>';
                songHTML += '<span class="song-timestamp" style="display:none;">' + songTimeStamp + '</span>';
                songHTML += '<span class="song-artist">' + song.inputs[1].value + '</span>';
                songHTML += ' - ';
                songHTML += '<span class="song-title">' + song.inputs[0].value + '</span>';
                songHTML += '<span class="song-album" style="display:none;">' + song.inputs[2].value + '</span>';
                songHTML += '<span class="song-label" style="display:none;">' + song.inputs[3].value + '</span>';
                $songs.prepend(songHTML);
              }
            });
            
            if (played) {
              $thisPlaylist.appendTo($playlistList);
            }
          }
        
          $('.playlist-list').html( $playlistList.html() );
          
          if (typeof createPlaylistLinkList == 'function') {
            createPlaylistLinkList(data);
          }
        },
        error: function(jqXHR, textStatus, errorThrow) {
          $playlistList.empty().append('<li>Error loading playlists</li>');
        }
      });
    }
    
    var showID = '<?= $showID ?>';
    
    fetchPlaylistInfo(showID);
    
    var playlistFetchingInterval = setInterval(fetchPlaylistInfo, 60000, showID);
    
    
  })(jQuery);
}
</script>

<?php get_sidebar(); ?>
<?php get_footer(); ?>

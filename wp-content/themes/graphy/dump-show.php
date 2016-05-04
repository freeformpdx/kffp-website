<?php
/**
	Template Name: DUMP SHOWS 

*/
?>
<html>
<head>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<title><?php wp_title( '|', true, 'right' ); bloginfo('url'); ?></title>
<?php wp_head(); ?>
</head>

<body>

<h1>
SO SHITTY RN
</h1>


<?php
$type = 'show';
$args=array(
  'post_type' => $type,
  'post_status' => 'publish',
  'posts_per_page' => -1,
  'caller_get_posts'=> 1
);

$allShows = array();

$my_query = null;
$my_query = new WP_Query($args);
if( $my_query->have_posts() ) {
  while ($my_query->have_posts()) : $my_query->the_post();

    // make show object && add it to $allShows
    $showData = array(
      "showID" => get_post_meta($post->ID, 'id', true),
      "title" => get_the_title(),
      "startDay" => get_post_meta($post->ID, 'start_day', true),
      "startHour" => get_post_meta($post->ID, 'start_hour', true),
      "endDay" => get_post_meta($post->ID, 'end_day', true),
      "endHour" => get_post_meta($post->ID, 'end_hour', true),
    );

    $allShows[] = $showData;


  endwhile;
}

wp_reset_query();  // Restore global post data stomped by the_post().
?>

<pre><?php echo json_encode($allShows); ?></pre>


</body>
</html>

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

$my_query = null;
$my_query = new WP_Query($args);
if( $my_query->have_posts() ) {
  while ($my_query->have_posts()) : $my_query->the_post(); ?>



    <p><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></p>





    <?php
  endwhile;
}
wp_reset_query();  // Restore global post data stomped by the_post().
?>


</body>
</html>

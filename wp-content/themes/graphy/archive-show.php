<?php
/**
 * @package Graphy
 */

get_header(); ?>

<div id="primary" class="content-area">
  <main id="main" class="site-main" role="main">
    <header class="entry-header">
      <h1 class="entry-title">
      Show Schedule
      </h1>
    </header>
    
    <?php
    $loop_day = '';
    
    if(have_posts()) : while(have_posts()) : the_post();
      $id = get_the_ID();
      $custom_fields = get_post_custom();
      
      $day = $custom_fields['start_day'][0];
      
      if ($day !== $loop_day) {
        if ($loop_day !== '') {
          echo '</ul></section>';
        }
        
        $loop_day = $day
        ?>
        <section class="schedule-block">
          <div class="day">
          <?= display_day_of_week($day, true); ?>
          </div>
          
          <ul class="schedule">
        <?php
      }

      ?>
      <li>
        <a href="<?php the_permalink() ?>">
        <?= $custom_fields['start_hour'][0] ?>:00 - 
        <?php the_title(); ?>
        w/ <?= $custom_fields['dj_name'][0] ?>
        </a>
      </li>
    <?php
    endwhile; endif;
    ?>
    </ul>
    </section>

  </main><!-- #main -->
</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>


<!------ /Add Banner Slider ------>
    <div id="banner_slider" style="margin-top:5px;margin-bottom:5px"><a href="/"> <?php echo do_shortcode("[metaslider id=47]"); ?> </a> </div> 
<!------ /End Banner Slider ------>
<!-- tieu de -->

    <h2 class="ten_trang"><?php echo apply_filters('kadence_page_title', kadence_title() ); ?></h2>

<!-- tieu de -->

<?php while (have_posts()) : the_post(); ?>

  <?php the_content(); ?>

  <?php wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>')); ?>

<?php endwhile; ?>
<?php global $virtue; ?>

<header class="banner headerclass" role="banner">

<?php if(isset($virtue['logo_layout'])) {

  if($virtue['logo_layout'] == 'logocenter') {$logocclass = 'col-md-12'; $menulclass = 'col-md-12';}

  else if($virtue['logo_layout'] == 'logohalf') {$logocclass = 'col-md-6'; $menulclass = 'col-md-6';}

  else {$logocclass = 'col-md-4'; $menulclass = 'col-md-8';} 

} else {$logocclass = 'col-md-4'; $menulclass = 'col-md-8'; }?>

  <div class="container">

    <div class="row">

         <!-- logo-->



       <?php if (has_nav_menu('primary_navigation')) : ?>

         <div class="<?php echo esc_attr($menulclass); ?> kad-header-right">



<?php if (!empty($virtue['virtue_banner_upload']['url'])) { ?><div class="virtue_banner"><a href="/"><img src="<?php echo esc_url($virtue['virtue_banner_upload']['url']); ?>" alt="tu vai gia re" /></a></div> <?php } ?>

                      <!-- close virtue_banner -->





<div class="main_menu">

    <div class="hotline">

       <?php start_short(); ?>[content name=hotline]<?php end_short(); ?>

    </div>

    

    <div class="timkiem">
           <?php global $woocommerce; ?>
              <a class="cart-contents1" href="<?php echo esc_url($woocommerce->cart->get_cart_url()); ?>" title="<?php esc_attr_e('Xem giỏ hàng', 'woocommerce'); ?>">
               		<i class="icon-shopping-cart" style="padding-right:5px;"></i> <?php _e('Giỏ hàng:', 'virtue');?> <span class="kad-cart-dash"></span> <?php echo $woocommerce->cart->get_cart_total(); ?>
              </a>
			  <?php get_search_form();?>
    </div>  
 	<!-- close timkiem -->

           <nav id="nav-main" class="clearfix" role="navigation">

              <?php wp_nav_menu(array('theme_location' => 'primary_navigation', 'menu_class' => 'sf-menu')); ?>

           </nav> 

          </div></div>

 <!-- Close menuclass-->

        <?php endif; ?>       

    </div> <!-- Close Row -->

    <?php if (has_nav_menu('mobile_navigation')) : ?>

           <div id="mobile-nav-trigger" class="nav-trigger">

              <button class="nav-trigger-case mobileclass collapsed" data-toggle="collapse" data-target=".kad-nav-collapse">

                <span class="kad-navbtn"><i class="icon-reorder"></i></span>

                <span class="kad-menu-name"><?php echo __('Menu', 'virtue'); ?></span>

              </button>

            </div>

            <div id="kad-mobile-nav" class="kad-mobile-nav">

              <div class="kad-nav-inner mobileclass">

                <div class="kad-nav-collapse">

                <?php if(isset($virtue['mobile_submenu_collapse']) && $virtue['mobile_submenu_collapse'] == '1') {

                    wp_nav_menu( array('theme_location' => 'mobile_navigation','items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>', 'menu_class' => 'kad-mnav', 'walker' => new kadence_mobile_walker()));

                  } else {

                  wp_nav_menu( array('theme_location' => 'mobile_navigation','items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>', 'menu_class' => 'kad-mnav')); 

                  } ?>

               </div>

            </div>

          </div>   

          <?php  endif; ?> 

  </div> <!-- Close Container -->

  <?php

            if (has_nav_menu('secondary_navigation')) : ?>

  <section id="cat_nav" class="navclass">

    <div class="container">

     <nav id="nav-second" class="clearfix" role="navigation">

     <?php wp_nav_menu(array('theme_location' => 'secondary_navigation', 'menu_class' => 'sf-menu')); ?>

   </nav>

    </div><!--close container-->

    </section>

<?php endif; ?> 



   <!--<div class="container" style="margin-top:5px;margin-bottom:5px"><a href="/"> //echo do_shortcode("[metaslider id=47]"); </a> </div> !-->

<!-- Close slider -->

<!--<div class="list_sanpham" style="margin-top:5px;width: 1140px;

    margin: auto;">

 //echo do_shortcode("[product_categories parent='0' height='auto']"); </div>!-->

<!-- Close productcatalog-->

<script>

  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){

  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),

  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)

  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');



  ga('create', 'UA-71061189-1', 'auto');

  ga('send', 'pageview');



</script>

</header>
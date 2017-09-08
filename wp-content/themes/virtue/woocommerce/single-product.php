<?php

/**

 * The Template for displaying all single products.

 *

 * Override this template by copying it to yourtheme/woocommerce/single-product.php

 *

 * @author 		WooThemes

 * @package 	WooCommerce/Templates

 * @version     1.6.4

 */



if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



	?>



<div id="content" class="container">

   		<div class="row">
			
			<div class="main <?php echo kadence_main_class(); ?>" role="main" id="chitietsp_content">
				<!------ /Add Banner Slider ------>
					<div id="banner_slider" style="margin-top:5px;margin-bottom:5px"><a href="/"> <?php echo do_shortcode("[metaslider id=47]"); ?> </a> </div> 
				<!------ /End Banner Slider ------>
					<div class="product_header clearfix">
						<div class="noidung_hienthi">
							<div class="tieude_sanpham"><h3 style="font-size:19px;line-height:57px;" class="ten_trang">CHI TIẾT SẢN PHẨM</h3></div>
						</div>
						<?php
							$terms = wp_get_post_terms( $post->ID, 'product_cat', array( 'orderby' => 'parent', 'order' => 'DESC' ) );

							if(!empty($terms)) {

								$main_term = $terms[0];

							} else {

								$main_term = "";

							}

							 if($main_term) {				

										echo '<div class="cat_back_btn headerfont"><i class="icon-arrow-left"></i> '.__('Back to', 'virtue').' <a href="'.get_term_link($main_term->slug, 'product_cat').'">'.$main_term->name.'</a></div>';

							} else {

								echo '<div class="cat_back_btn headerfont"><i class="icon-arrow-left"></i> '.__('Back to', 'virtue').' <a href="'.get_permalink( woocommerce_get_page_id( 'shop' ) ).'">'.__('Shop','virtue').'</a></div>';

							}	
						?>
					</div>

				<?php while ( have_posts() ) : the_post();?>
					<?php woocommerce_get_template_part( 'content', 'single-product' ); ?>
				<?php endwhile; // end of the loop. ?>

			</div>
			
			<!-- sidebar-->
				<div id="sidebar_product" class="sidebar"><?php dynamic_sidebar( kadence_sidebar_id() ); ?></div>
			<!-- sidebar-->	
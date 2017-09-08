<!-- Google Code dành cho Thẻ tiếp thị lại -->
<!--------------------------------------------------
Không thể liên kết thẻ tiếp thị lại với thông tin nhận dạng cá nhân hay đặt thẻ tiếp thị lại trên các trang có liên quan đến danh mục nhạy cảm. Xem thêm thông tin và hướng dẫn về cách thiết lập thẻ trên: http://google.com/ads/remarketingsetup
--------------------------------------------------->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 944596421;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/944596421/?value=0&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
<footer id="containerfooter" class="footerclass" role="contentinfo">

<!--<div class="logo_doitac">

<div class="container">

<div class="clear" style="overflow:hidden; clear:both;"></div>



<div class="tieude_doitac">

<p>ĐỐI TÁC</p>

</div>

 //echo do_shortcode('[logo-carousel id=default]')

</div>

</div>--!>



<nav id="nav-main" class="clearfix" role="navigation">

              <?php wp_nav_menu(array('theme_location' => 'primary_navigation', 'menu_class' => 'sf-menu')); ?>

           </nav> 

 <div class="box_footer">

  <div class="container">

  	<div class="row">

  		<?php global $virtue; if(isset($virtue['footer_layout'])) { $footer_layout = $virtue['footer_layout']; } else { $footer_layout = 'fourc'; }

  			if ($footer_layout == "fourc") {

  				if (is_active_sidebar('footer_1') ) { ?> 

					<div class="col-md-3 col-sm-6 footercol1">

					<?php dynamic_sidebar('footer_1'); ?>

					</div> 

            	<?php }; ?>

				<?php if (is_active_sidebar('footer_2') ) { ?> 

					<div class="col-md-3  col-sm-6 footercol2">

					<?php dynamic_sidebar('footer_2'); ?>

					</div> 

		        <?php }; ?>

		        <?php if (is_active_sidebar('footer_3') ) { ?> 

					<div class="col-md-3 col-sm-6 footercol3">

					<?php dynamic_sidebar('footer_3'); ?>

					</div> 

	            <?php }; ?>

				<?php if (is_active_sidebar('footer_4') ) { ?> 

					<div class="col-md-3 col-sm-6 footercol4">

					<?php dynamic_sidebar('footer_4'); ?>

					</div> 

		        <?php }; ?>

		    <?php } else if($footer_layout == "threec") {

		    	if (is_active_sidebar('footer_third_1') ) { ?> 

					<div class="col-md-4 footercol1">

					<?php dynamic_sidebar('footer_third_1'); ?>

					</div> 

            	<?php }; ?>

				<?php if (is_active_sidebar('footer_third_2') ) { ?> 

					<div class="col-md-4 footercol2">

					<?php dynamic_sidebar('footer_third_2'); ?>

					</div> 

		        <?php }; ?>

		        <?php if (is_active_sidebar('footer_third_3') ) { ?> 

					<div class="col-md-4 footercol3">

					<?php dynamic_sidebar('footer_third_3'); ?>

					</div> 

	            <?php }; ?>

			<?php } else {

					if (is_active_sidebar('footer_double_1') ) { ?>

					<div class="col-md-6 footercol1">

					<?php dynamic_sidebar('footer_double_1'); ?> 

					</div> 

		            <?php }; ?>

		        <?php if (is_active_sidebar('footer_double_2') ) { ?>

					<div class="col-md-6 footercol2">

					<?php dynamic_sidebar('footer_double_2'); ?> 

					</div> 

		            <?php }; ?>

		        <?php } ?>

        </div>

</div>

     



  </div>



</footer>



<?php wp_footer(); ?>


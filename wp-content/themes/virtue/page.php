	<div id="pageheader" class="titleclass">

		<div class="container">

			<?php get_template_part('templates/page', 'header'); ?>

		</div><!--container-->

	</div><!--titleclass-->

    <div id="content" class="container">

   		<div class="row">
			<?php if ( is_active_sidebar( 'sidebar-primary' ) ) { ?>
            	<div id="sidebar_product" class="sidebar">
                	<?php dynamic_sidebar( kadence_sidebar_id() ); ?>
           		 </div>
            <?php } ?>

                <div class="main <?php echo esc_attr( kadence_main_class() ); ?>" role="main">

					<div class="entry-content" itemprop="mainContentOfPage">

					<?php get_template_part('templates/content', 'page'); ?>

				</div>

				<?php global $virtue; if(isset($virtue['page_comments']) && $virtue['page_comments'] == '1') { comments_template('/templates/comments.php');} ?>

			</div><!-- /.main -->
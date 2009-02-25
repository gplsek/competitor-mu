<?php get_header(); ?>

		<div class="col1">

		<?php if (have_posts()) : ?>
		
		<div id="archivebox">
        	
            	<h2><?php the_title(); ?></h2>        
		
		</div><!--/archivebox-->
	
			<?php while (have_posts()) : the_post(); ?>		

				<div class="post-alt blog" id="post-<?php the_ID(); ?>">
		
					<div class="entry">
						<?php the_content('<span class="continue">Continue Reading</span>'); ?> 
					</div>
				
				</div><!--/post-->

			<?php endwhile; ?>
	
		<?php endif; ?>							

		</div><!--/col1-->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
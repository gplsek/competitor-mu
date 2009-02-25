<?php get_header(); ?>

		<div class="col1">

		<?php if (have_posts()) : ?>
	
			<?php while (have_posts()) : the_post(); ?>		

				<div id="archivebox">
					
						<h2><em>Categorized |</em> <?php the_category(', ') ?></h2>
						<?php if (function_exists('the_tags')) { ?><div class="singletags"><?php the_tags('Tags | ', ', ', ''); ?></div><?php } ?>        
				
				</div><!--/archivebox-->			

				<div class="post-alt blog" id="post-<?php the_ID(); ?>">
				
					<h3><a title="Permanent Link to <?php the_title(); ?>" href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h3>
					<p class="posted">Posted on <?php the_time('d F Y'); ?> by <?php the_author(); ?></p>
		
					<div class="entry">
						<?php the_content('<span class="continue">Continue Reading</span>'); ?> 
					</div>
				
				</div><!--/post-->
				
				<div id="comment">
					<?php comments_template(); ?>
				</div>

		<?php endwhile; ?>
		
		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Previous Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Next Entries &raquo;') ?></div>
		</div>		
	
	<?php endif; ?>							

		</div><!--/col1-->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
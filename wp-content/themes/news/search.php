<?php get_header(); ?>

		<div class="col1">

		<?php if (have_posts()) : ?>
		
		<div id="archivebox">
        	
            	<h2><em>Search Results |</em> "<?php printf(__('\'%s\''), $s) ?>"</h2>        
		
		</div><!--/archivebox-->
	
			<?php while (have_posts()) : the_post(); ?>		

				<div class="post-alt blog" id="post-<?php the_ID(); ?>">
		
					<?php if ( get_post_meta($post->ID, 'image', true) ) { ?> <!-- DISPLAYS THE IMAGE URL SPECIFIED IN THE CUSTOM FIELD -->
						
						<img src="<?php echo bloginfo('template_url'); ?>/thumb.php?src=<?php echo get_post_meta($post->ID, "image", $single = true); ?>&amp;h=57&amp;w=100&amp;zc=1&amp;q=95" alt="" class="th" />			
						
					<?php } else { ?> <!-- DISPLAY THE DEFAULT IMAGE, IF CUSTOM FIELD HAS NOT BEEN COMPLETED -->
						
						<img src="<?php bloginfo('template_directory'); ?>/images/no-img-thumb.jpg" alt="" class="th" />
						
					<?php } ?> 		
					
					<?php if (function_exists('the_tags')) { ?><h2><?php the_tags('Tags: ', ', ', ''); ?></h2><?php } ?>
					<h3><a title="Permanent Link to <?php the_title(); ?>" href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h3>
					<p class="posted">Posted on <?php the_time('d F Y'); ?> by <?php the_author(); ?></p>
		
					<div class="entry">
						<?php the_content('<span class="continue">Continue Reading</span>'); ?> 
					</div>
		
					<p class="comments"><?php comments_popup_link('Comments (0)', 'Comments (1)', 'Comments (%)'); ?></p>
				
				</div><!--/post-->

		<?php endwhile; ?>
		
		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Previous Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Next Entries &raquo;') ?></div>
		</div>
		
		<?php else : ?>

		<div id="archivebox">
        	
            	<h2><em>Search Results |</em> None Found!</h2>
            	<div class="archivefeed">Sorry! Your search yielded no results. Please search again.</div>				
		
		</div><!--/archivebox-->				
	
	<?php endif; ?>							

		</div><!--/col1-->

<?php get_sidebar(); ?>

<?php get_footer(); ?>	

<?php get_header(); ?>

		<div class="col1">

			<?php
			$url = get_option('siteurl');
			$m = new Marquee;
			$magazine = $m->get_magazine_name($url);
			echo $m->to_tabs($magazine);
			
			?>

			<?php
				
				$showvideo = get_option('woo_show_video');

				if($showvideo){ include(TEMPLATEPATH . '/includes/video.php'); }
				
			?>

			<?php
				
				$layout = get_option('woo_layout');
				if ($layout == "default.php")
					include('layouts/default.php');
				elseif ($layout == "blog.php")
					include('layouts/blog.php');
				else
					include('layouts/default.php');
				
			?>

		</div><!--/col1-->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
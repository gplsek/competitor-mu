<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">

    <title>
		<?php if ( is_home() ) { ?><?php bloginfo('name'); ?>&nbsp;|&nbsp;<?php bloginfo('description'); ?><?php } ?>
		<?php if ( is_search() ) { ?><?php bloginfo('name'); ?>&nbsp;|&nbsp;Search Results<?php } ?>
		<?php if ( is_single() ) { ?><?php wp_title(''); ?>&nbsp;|&nbsp;<?php bloginfo('name'); ?><?php } ?>
		<?php if ( is_page() ) { ?><?php bloginfo('name'); ?>&nbsp;|&nbsp;<?php wp_title(''); ?><?php } ?>
		<?php if ( is_category() ) { ?><?php bloginfo('name'); ?>&nbsp;|&nbsp;Archive&nbsp;|&nbsp;<?php single_cat_title(); ?><?php } ?>
		<?php if ( is_month() ) { ?><?php bloginfo('name'); ?>&nbsp;|&nbsp;Archive&nbsp;|&nbsp;<?php the_time('F'); ?><?php } ?>
		<?php if (function_exists('is_tag')) { if ( is_tag() ) { ?><?php bloginfo('name'); ?>&nbsp;|&nbsp;Tag Archive&nbsp;|&nbsp;<?php  single_tag_title("", true); } } ?>
    </title>

	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />


    


	

	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php if ( get_option('woo_feedburner_url') <> "" ) { echo get_option('woo_feedburner_url'); } else { echo get_bloginfo_rss('rss2_url'); } ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<?php wp_head(); ?>

	<!--[if lte IE 6]>
	<script defer type="text/javascript" src="<?php bloginfo('template_directory'); ?>/images/pngfix.js"></script>
	<![endif]-->
	
	
	<script type="text/javascript" src="http://tworld.com/wp-content/themes/news/includes/js/jquery-1.2.6.js"></script>
	<script type="text/javascript" src="http://tworld.com/wp-content/themes/news/includes/js/ui.core.js"></script>
	<script type="text/javascript" src="http://tworld.com/wp-content/themes/news/includes/js/ui.tabs.js"></script>			

	<script type="text/javascript" src="http://tworld.com/wp-content/themes/news/includes/js/superfish.js"></script>

	<script type="text/javascript">
	            $(function() {
	                $('#rotate > ul').tabs({ fx: { opacity: 'toggle' } }).tabs('rotate', 2000);
	            });
	        </script>
	
	
	<link rel="stylesheet" type="text/css"  href="<?php bloginfo('stylesheet_url'); ?>" media="screen" />
	
	
	<?php include(TEMPLATEPATH . '/includes/stylesheet.php'); ?>
	
	

</head>

<body class="news">

<?php
	$template_path = get_bloginfo('template_directory');
	$GLOBALS['defaultgravatar'] = $template_path . '/images/gravatar.jpg';
?>

<div id="page">
	
	<div id="nav"> <!-- START TOP NAVIGATION BAR -->
	
		<div id="nav-left">
	
			<ul id="lavaLamp">
				<li><a href="<?php echo get_option('home'); ?>/">Home</a></li>
				<?php wp_list_pages('depth=1&sort_column=menu_order&title_li=' ); ?>		
			</ul>
		
		</div><!--/nav-left -->

		<div id="nav-right">		
		
			<form method="get" id="searchform" action="<?php bloginfo('home'); ?>/">
				
				<div id="search">
					<input type="text" value="Enter your search keywords here..." onclick="this.value='';" name="s" id="s" />
					<input name="" type="image" src="<?php bloginfo('stylesheet_directory'); ?>/styles/<?php echo "$style_path"; ?>/ico-go.gif" value="Go" class="btn"  />
				</div><!--/search -->
				
			</form>
		
		</div><!--/nav-right -->
		
	</div><!--/nav-->
	
	<div id="header"><!-- START LOGO LEVEL WITH RSS FEED -->
		
		<h1><a href="<?php echo get_option('home'); ?>/" title="<?php bloginfo('name'); ?>"><img src="<?php if ( get_option('woo_logo') <> "" ) {  echo get_option('woo_logo'); } else { ?><?php bloginfo('stylesheet_directory'); ?>/images/logo.gif<?php } ?>" alt="<?php bloginfo('name'); ?>" title="<?php bloginfo('name'); ?>" /></a></h1>
		
		<div id="rss">
			
			<a href="<?php if ( get_option('woo_feedburner_url') <> "" ) { echo get_option('woo_feedburner_url'); } else { echo get_bloginfo_rss('rss2_url'); } ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/ico-rss.gif" alt="" /></a>
			
			<ul>
				<li class="hl"><a href="<?php if ( get_option('woo_feedburner_url') <> "" ) { echo get_option('woo_feedburner_url'); } else { echo get_bloginfo_rss('rss2_url'); } ?>">SUBSCRIBE TO THE RSS FEED</a></li>
				<li><a href="http://www.feedburner.com/fb/a/emailverifySubmit?feedId=<?php $feedburner_id = get_option('woo_feedburner_id'); echo $feedburner_id; ?>" target="_blank">SUBSCRIBE TO THE FEED VIA E-MAIL</a></li>
			</ul>
			
		</div><!--/rss-->
		
	</div><!--/header -->
	
	<div id="suckerfish"><!-- START CATEGORY NAVIGATION (SUCKERFISH CSS) -->
		
			<ul class="nav2">
				<?php wp_list_categories('title_li=') ?>	
			</ul>
					
	</div><!--/nav2-->
	
	<div id="columns"><!-- START MAIN CONTENT COLUMNS -->
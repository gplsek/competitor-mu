<?php function woothemes_admin_head() { ?>
<style>

h2 { margin-bottom: 20px; }
.title { margin: 0px !important; background: #D4E9FA; padding: 10px; font-family: Georgia, serif; font-weight: normal !important; letter-spacing: 1px; font-size: 18px; }
.container { background: #EAF3FA; padding: 10px; }
.maintable { font-family:"Lucida Grande","Lucida Sans Unicode",Arial,Verdana,sans-serif; background: #EAF3FA; margin-bottom: 20px; padding: 10px 0px; }
.mainrow { padding-bottom: 10px !important; border-bottom: 1px solid #D4E9FA !important; float: left; margin: 0px 10px 10px 10px !important; }
.titledesc { font-size: 14px; font-weight:bold; width: 220px !important; margin-right: 20px !important; }
.forminp { width: 700px !important; valign: middle !important; }
.forminp input, .forminp select, .forminp textarea { margin-bottom: 9px !important; background: #fff; border: 1px solid #D4E9FA; width: 500px; padding: 4px; font-family:"Lucida Grande","Lucida Sans Unicode",Arial,Verdana,sans-serif; font-size: 12px; }
.forminp span { font-size: 10px !important; font-weight: normal !important; ine-height: 14px !important; }
.forminp .checkbox { width:20px }
.info { background: #FFFFCC; border: 1px dotted #D8D2A9; padding: 10px; color: #333; }
.info a { color: #333; text-decoration: none; border-bottom: 1px dotted #333 }
.info a:hover { color: #666; border-bottom: 1px dotted #666; }
.warning { background: #FFEBE8; border: 1px dotted #CC0000; padding: 10px; color: #333; font-weight: bold; }

</style>
<?php }

// VARIABLES

$themename = "Premium News";
$shortname = "woo";
$manualurl = 'http://www.woothemes.com/support/theme-documentation/the-original-premium-news/';
$options = array();

add_option("woothemes_settings",$options);

$template_path = get_bloginfo('template_directory');

$layout_path = TEMPLATEPATH . '/layouts/'; 
$layouts = array();

$alt_stylesheet_path = TEMPLATEPATH . '/styles/';
$alt_stylesheets = array();

$functions_path = TEMPLATEPATH . '/functions/';

$woo_categories_obj = get_categories('hide_empty=0');
$woo_categories = array();

$woo_pages_obj = get_pages('sort_column=post_parent,menu_order');
$woo_pages = array();

if ( is_dir($layout_path) ) {
	if ($layout_dir = opendir($layout_path) ) { 
		while ( ($layout_file = readdir($layout_dir)) !== false ) {
			if(stristr($layout_file, ".php") !== false) {
				$layouts[] = $layout_file;
			}
		}	
	}
}	

if ( is_dir($alt_stylesheet_path) ) {
	if ($alt_stylesheet_dir = opendir($alt_stylesheet_path) ) { 
		while ( ($alt_stylesheet_file = readdir($alt_stylesheet_dir)) !== false ) {
			if(stristr($alt_stylesheet_file, ".css") !== false) {
				$alt_stylesheets[] = $alt_stylesheet_file;
			}
		}	
	}
}	

if ( is_dir($modules_path) ) {
	if ($modules_dir = opendir($modules_path) ) { 
		while ( ($module_file = readdir($modules_dir)) !== false ) {
			if(stristr($module_file, ".php") !== false) {
				$file_tmp = substr($module_file, 0, -4);
				$modules[$file_tmp] = $module_file;
			}
		}	
	}
}

if ( is_dir($ads_path) ) {
	if ($ads_dir = opendir($ads_path) ) { 
		while ( ($ads_file = readdir($ads_dir)) !== false ) {
			if((stristr($ads_file, ".jpg") !== false) || (stristr($ads_file, ".png") !== false) || (stristr($ads_file, ".gif") !== false)) {
				$ads[] = $ads_file;
			}
		}	
	}
}

foreach ($woo_categories_obj as $woo_cat) {
	$woo_categories[$woo_cat->cat_ID] = $woo_cat->cat_name;
}

foreach ($woo_pages_obj as $woo_page) {
	$woo_pages[$woo_page->ID] = $woo_page->post_name;
}


$other_entries = array("Select a number:","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19");
$categories_tmp = array_unshift($woo_categories, "Select a category:");
$woo_pages_tmp = array_unshift($woo_pages, "Select a page:");

// THIS IS THE DIFFERENT FIELDS

$options = array (

				array(	"name" => "General Settings",
						"type" => "heading"),

				array(	"name" => "Theme Stylesheet",
						"desc" => "Please select your colour scheme here.",
					    "id" => $shortname."_alt_stylesheet",
					    "std" => "",
					    "type" => "select",
					    "options" => $alt_stylesheets),

				array(	"name" => "Use Gravatars?",
						"desc" => "Check this box if you wish to use <a href='http://www.gravatar.com'>Gravatars</a> for Author & Commenter profiles.",
						"id" => $shortname."_gravatar",
						"std" => "false",
						"type" => "checkbox"),

				array(	"name" => "Custom Logo",
						"desc" => "Paste the full URL of your custom logo image, should you wish to replace our default logo.",
						"id" => $shortname."_logo",
						"std" => "",
						"type" => "text"),												    

				array(	"name" => "Google Analytics",
						"desc" => "Please paste your Google Analytics (or other) tracking code here.",
			    		"id" => $shortname."_google_analytics",
			    		"std" => "",
			    		"type" => "textarea"),		

				array(	"name" => "Feedburner RSS URL",
						"desc" => "Enter your Feedburner URL here.",
			    		"id" => $shortname."_feedburner_url",
			    		"std" => "",
			    		"type" => "text"),																	    

				array(	"name" => "Feedburner ID",
						"desc" => "Enter your Feedburner ID here.",
			    		"id" => $shortname."_feedburner_id",
			    		"std" => "",
			    		"type" => "text"),

				array(	"name" => "Archives Page",
						"desc" => "Please select your archive page. TIP: Add your archive by creating a new page (Write > Page), and selecting the 'Archive' page template. ",
						"id" => $shortname."_archives",
						"std" => "Select a page:",
						"type" => "select",
						"options" => $woo_pages),

				array(	"name" => "Front Page Layout",
						"type" => "heading"),

				array(	"name" => "Front Page Layout",
						"desc" => "Choose the layout of to be used for the other entries on your homepage.",
			    		"id" => $shortname."_layout",
			    		"std" => "",
			    		"type" => "select",
			    		"options" => $layouts),

				array(	"name" => "Homepage Entries",
						"desc" => "Select the number of entries that should appear below the Featured Entries or Video Panel.",
			    		"id" => $shortname."_other_entries",
			    		"std" => "6",
			    		"type" => "select",
			    		"options" => $other_entries),	
						
				array( 	"name" => "Featured Category",
					   	"desc" => "Select the category that you would like to have displayed in the featured section on your homepage.",
						"id" => $shortname."_featured_category",
						"std" => "Select a category:",
						"type" => "select",
						"options" => $woo_categories),						

				array(	"name" => "Display Video?",
						"desc" => "Check this box if you wish to display the video panel on your homepage.",
						"id" => $shortname."_show_video",
						"std" => "false",
						"type" => "checkbox"),
						
				array( 	"name" => "Video Category",
					   	"desc" => "Select the category that you would like to have displayed in the video panel on your homepage.",
						"id" => $shortname."_video_category",
						"std" => "Select a category:",
						"type" => "select",
						"options" => $woo_categories),
						
				array(	"name" => "Sidebar Components",
						"type" => "heading"),

				array(	"name" => "Flickr ID",
						"desc" => "Use <a href='http://idgettr.com/'>idGettr to find it.",
			    		"id" => $shortname."_flickr_id",
			    		"std" => "",
			    		"type" => "text"),											

				array(	"name" => "Number photos",
						"desc" => "Select the number of photos to display in flickr sidebar box. (3 per row)",
			    		"id" => $shortname."_flickr_entries",
			    		"std" => "Select a Number:",
			    		"type" => "select",
			    		"options" => $other_entries),												

				array(	"name" => "Flickr URL",
						"desc" => "Enter the URL of your Flickr account here.",
			    		"id" => $shortname."_flickr_url",
			    		"std" => "Flickr URL",
			    		"type" => "text"),																						

				array(	"name" => "Banner Ad Management",
						"type" => "heading"),

				array(	"name" => "Advertising Page",
						"desc" => "Please enter the WordPress page that contains your advertising information.",
						"id" => $shortname."_ad_page",
						"std" => "Select a page:",
						"type" => "select",
						"options" => $woo_pages),

				array(	"name" => "Banner Ad #1 - Image Location",
						"desc" => "Enter the URL for this banner ad.",
						"id" => $shortname."_ad_image_1",
						"std" => $template_path . "/images/ad-125x125.gif",
						"type" => "text"),
						
				array(	"name" => "Banner Ad #1 - Destination",
						"desc" => "Enter the URL where this banner ad points to.",
			    		"id" => $shortname."_ad_url_1",
						"std" => "http://example.com/ads/ad1_destination.html",
			    		"type" => "text"),						

				array(	"name" => "Banner Ad #2 - Image Location",
						"desc" => "Enter the URL for this banner ad.",
						"id" => $shortname."_ad_image_2",
						"std" => $template_path . "/images/ad-125x125.gif",
						"type" => "text"),
						
				array(	"name" => "Banner Ad #2 - Destination",
						"desc" => "Enter the URL where this banner ad points to.",
			    		"id" => $shortname."_ad_url_2",
						"std" => "http://example.com/ads/ad1_destination.html",
			    		"type" => "text"),

				array(	"name" => "Banner Ad #3 - Image Location",
						"desc" => "Enter the URL for this banner ad.",
						"id" => $shortname."_ad_image_3",
						"std" => $template_path . "/images/ad-125x125.gif",
						"type" => "text"),
						
				array(	"name" => "Banner Ad #3 - Destination",
						"desc" => "Enter the URL where this banner ad points to.",
			    		"id" => $shortname."_ad_url_3",
						"std" => "http://example.com/ads/ad1_destination.html",
			    		"type" => "text"),

				array(	"name" => "Banner Ad #4 - Image Location",
						"desc" => "Enter the URL for this banner ad.",
						"id" => $shortname."_ad_image_4",
						"std" => $template_path . "/images/ad-125x125.gif",
						"type" => "text"),
						
				array(	"name" => "Banner Ad #4 - Destination",
						"desc" => "Enter the URL where this banner ad points to.",
			    		"id" => $shortname."_ad_url_4",
						"std" => "http://example.com/ads/ad1_destination.html",
			    		"type" => "text")
																														
		  );

// ADMIN PANEL

function woothemes_add_admin() {

	 global $themename, $options;
	
	if ( $_GET['page'] == basename(__FILE__) ) {	
        if ( 'save' == $_REQUEST['action'] ) {
	
                foreach ($options as $value) {
					if($value['type'] != 'multicheck'){
                    	update_option( $value['id'], $_REQUEST[ $value['id'] ] ); 
					}else{
						foreach($value['options'] as $mc_key => $mc_value){
							$up_opt = $value['id'].'_'.$mc_key;
							update_option($up_opt, $_REQUEST[$up_opt] );
						}
					}
				}

                foreach ($options as $value) {
					if($value['type'] != 'multicheck'){
                    	if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } 
					}else{
						foreach($value['options'] as $mc_key => $mc_value){
							$up_opt = $value['id'].'_'.$mc_key;						
							if( isset( $_REQUEST[ $up_opt ] ) ) { update_option( $up_opt, $_REQUEST[ $up_opt ]  ); } else { delete_option( $up_opt ); } 
						}
					}
				}
						
				header("Location: admin.php?page=functions.php&saved=true");								
			
			die;

		} else if ( 'reset' == $_REQUEST['action'] ) {
			delete_option('sandbox_logo');
			
			header("Location: admin.php?page=functions.php&reset=true");
			die;
		}

	}

add_menu_page($themename." Options", $themename." Options", 'edit_themes', basename(__FILE__), 'woothemes_page');

}

function woothemes_page (){

		global $options, $themename, $manualurl;
		
		?>

<div class="wrap">

    			<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

						<h2><?php echo $themename; ?> Options</h2>

						<?php if ( $_REQUEST['saved'] ) { ?><div style="clear:both;height:20px;"></div><div class="warning"><?php echo $themename; ?>'s Options has been updated!</div><?php } ?>
						<?php if ( $_REQUEST['reset'] ) { ?><div style="clear:both;height:20px;"></div><div class="warning"><?php echo $themename; ?>'s Options has been reset!</div><?php } ?>						
						
						<div style="clear:both;height:20px;"></div>
						
						<div class="info">
						
							<div style="width: 70%; float: left; display: inline;padding-top:4px;"><strong>Stuck on these options?</strong> <a href="<?php echo $manualurl; ?>" target="_blank">Read The Documentation Here</a> or <a href="http://forum.woothemes.com" target="blank">Visit Our Support Forum</a></div>
							<div style="width: 30%; float: right; display: inline;text-align: right;"><input name="save" type="submit" value="Save changes" /></div>
							<div style="clear:both;"></div>
						
						</div>	    			
						
						<!--START: GENERAL SETTINGS-->
     						
     						<table class="maintable">
     							
							<?php foreach ($options as $value) { ?>
	
									<?php if ( $value['type'] <> "heading" ) { ?>
	
										<tr class="mainrow">
										<td class="titledesc"><?php echo $value['name']; ?></td>
										<td class="forminp">
		
									<?php } ?>		 
	
									<?php
										
										switch ( $value['type'] ) {
										case 'text':
		
									?>
									
		        							<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo get_settings( $value['id'] ); } else { echo $value['std']; } ?>" />
		
									<?php
										
										break;
										case 'select':
		
									?>
		
	            						<select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
	                					<?php foreach ($value['options'] as $option) { ?>
	                						<option<?php if ( get_settings( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
	                					<?php } ?>
	            						</select>
		
									<?php
		
										break;
										case 'textarea':
										$ta_options = $value['options'];
		
									?>
									
										<textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" cols="<?php echo $ta_options['cols']; ?>" rows="8"><?php  if( get_settings($value['id']) != "") { echo stripslashes(get_settings($value['id'])); } else { echo $value['std']; } ?></textarea>
		
									<?php
										
										break;
										case "radio":
		
 										foreach ($value['options'] as $key=>$option) { 
				
													$radio_setting = get_settings($value['id']);
													
													if($radio_setting != '') {
		    											
		    											if ($key == get_settings($value['id']) ) { $checked = "checked=\"checked\""; } else { $checked = ""; }
													
													} else {
													
														if($key == $value['std']) { $checked = "checked=\"checked\""; } else { $checked = ""; }
									} ?>
									
	            					<input type="radio" name="<?php echo $value['id']; ?>" value="<?php echo $key; ?>" <?php echo $checked; ?> /><?php echo $option; ?><br />
		
									<?php }
		 
										break;
										case "checkbox":
										
										if(get_settings($value['id'])) { $checked = "checked=\"checked\""; } else { $checked = ""; }
									
									?>
		            				
		            				<input type="checkbox" class="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />
		
									<?php
		
										break;
										case "multicheck":
		
 										foreach ($value['options'] as $key=>$option) {
 										
	 											$woo_key = $value['id'] . '_' . $key;
												$checkbox_setting = get_settings($woo_key);
				
 												if($checkbox_setting != '') {
		    		
		    											if (get_settings($woo_key) ) { $checked = "checked=\"checked\""; } else { $checked = ""; }
				
												} else { if($key == $value['std']) { $checked = "checked=\"checked\""; } else { $checked = ""; }
				
									} ?>
									
	            					<input type="checkbox" class="checkbox" name="<?php echo $woo_key; ?>" id="<?php echo $woo_key; ?>" value="true" <?php echo $checked; ?> /><label for="<?php echo $woo_key; ?>"><?php echo $option; ?></label><br />
									
									<?php }
		 
										break;
										case "heading":

									?>
									
										</table> 
		    							
		    									<h3 class="title"><?php echo $value['name']; ?></h3>
										
										<table class="maintable">
		
									<?php
										
										break;
										default:
										break;
									
									} ?>
	
									<?php if ( $value['type'] <> "heading" ) { ?>
	
										<?php if ( $value['type'] <> "checkbox" ) { ?><br/><?php } ?><span><?php echo $value['desc']; ?></span>
										</td></tr>
	
									<?php } ?>		
	
							<?php } ?>	
							
							</table>	

							<p class="submit">
								<input name="save" type="submit" value="Save changes" />    
								<input type="hidden" name="action" value="save" />
							</p>							
							
							<div style="clear:both;"></div>		
						
						<!--END: GENERAL SETTINGS-->						
             
            </form>

</div><!--wrap-->

<div style="clear:both;height:20px;"></div>
 
 <?php

};

function woothemes_wp_head() { 
     $style = $_REQUEST[style];
     if ($style != '') {
          ?> <link href="<?php bloginfo('template_directory'); ?>/styles/<?php echo $style; ?>.css" rel="stylesheet" type="text/css" /><?php 
     } else { 
          $stylesheet = get_option('woo_alt_stylesheet');
          if($stylesheet != ''){
               ?><link href="<?php bloginfo('template_directory'); ?>/styles/<?php echo $stylesheet; ?>" rel="stylesheet" type="text/css" /><?php         
          }
     }     
}

add_action('wp_head', 'woothemes_wp_head');
add_action('admin_menu', 'woothemes_add_admin');
add_action('admin_head', 'woothemes_admin_head');	

// OTHER FUNCTIONS

if ( function_exists('register_sidebar') )
    register_sidebars(2,array(
        'before_widget' => '<div class="widget">',
        'after_widget' => '</div><!--/widget-->',
        'before_title' => '<h2 class="hl">',
        'after_title' => '</h2>',
    ));

$bm_trackbacks = array();
$bm_comments = array();

function split_comments( $source ) {

    if ( $source ) foreach ( $source as $comment ) {

        global $bm_trackbacks;
        global $bm_comments;

        if ( $comment->comment_type == 'trackback' || $comment->comment_type == 'pingback' ) {
            $bm_trackbacks[] = $comment;
        } else {
            $bm_comments[] = $comment;
        }
    }
} 

// Custom fields 
require_once ($functions_path . '/custom.php');

// Easytube
require_once ($functions_path . '/easytube.php');


//use 
//in the single post page
// the_image('medium','post-image'); 
//in the index.php or category page
// the_image('thumbnail','post-thumb');


function the_image($size = 'medium' , $class = ''){
	global $post;
 
	//setup the attachment array
	$att_array = array(
		'post_parent' => $post->ID,
		'post_type' => 'attachment',
		'post_mime_type' => 'image',
		'order_by' => 'menu_order'
	);
 
	//get the post attachments
	$attachments = get_children($att_array);
 
	//make sure there are attachments
	if (is_array($attachments)){
		//loop through them
		foreach($attachments as $att){
			//find the one we want based on its characteristics
			if ( $att->menu_order == 0){
				$image_src_array = wp_get_attachment_image_src($att->ID, $size);
 
				//get url - 1 and 2 are the x and y dimensions
				$url = $image_src_array[0];
				$caption = $att->post_excerpt;
				$image_html = '<img src="%s" alt="%s" class="%s" />';
 
				//combine the data
				$html = sprintf($image_html,$url,$caption,$class);
 
				//echo the result
				echo $html;
			}
		}
	}
 
}?>
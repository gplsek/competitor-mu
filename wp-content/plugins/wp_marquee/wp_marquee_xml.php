<?php
if (!function_exists('add_action')) {
	require_once('../../../wp-config.php');
}
include_once(ABSPATH . "/wp-content/plugins/wp_marquee/wp_marquee_class.php");
	$mar 			= new Marquee;
	$url 			= get_option('siteurl');
	//$magazine = $mar->get_magazine_name($url);
	$magazine = "www";
	$marquee  = $mar->get_data_for_published_xml($magazine);

?>

<? echo '<?xml version="1.0" encoding="UTF-8" ?>' ?>
	<row>
		<?php for($i=0; $i < count($marquee); $i++){ ?>
			<marquee>
				<path type="<?php echo $mar->return_filetype($marquee[$i]["image"]) ?>"><?php echo $siteurl.$marquee[$i]["image"] ?></path>
				<caption_1><?php echo $marquee[$i]["headline"] ?></caption_1>
				<caption_2><?php echo $marquee[$i]["subheadline"] ?></caption_2>
				<link><?php echo $marquee[$i]["image_link"] ?></link>
			</marquee>
		<?php } ?>
	</row>
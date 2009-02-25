<?php

	function marquee_options_page(){
		// Catch the POST of an update
		if( isset($_POST['edit_form']) ) {
			$post_size = count($_POST) - 1;
			global $wpdb;
			
			for($i=0; $i < $post_size; $i++){
				$wpdb->query('UPDATE wp_marquee SET image="'.$_POST["marquee_image_$i"].'", image_link="'.$_POST["marquee_image_link_$i"].'", 
										 headline="'.$_POST["marquee_headline_$i"].'", subheadline="'.$_POST["marquee_subheadline_$i"].'", audio_link="'.$_POST["marquee_audio_link_$i"].'",
										 magazine="'.$_POST["marquee_magazine_$i"].'", status="'.$_POST["marquee_status_$i"].'", position='.$_POST["marquee_position_$i"].' WHERE id='.$_POST["marquee_id_$i"]);
			}

		} elseif( isset($_POST['upload_image']) ) {
			$marquee 	= new Marquee;
			$magazine	= $_POST['marquee_magazine'];
			$name 		= $marquee->sanitize_file_name( basename($_FILES['marquee_image']['name']) );
			
			if(!is_dir('/var/www/competitor/competitor-mu/wp-content/marquees/') && is_dir('/var/www/competitor/competitor-mu/wp-content/marquees/')){
				$wordpress_directory = '/var/www/competitor/competitor-mu/wp-content/marquees/';
			} else {
				$wordpress_directory = '/var/www/competitor/competitor-mu/wp-content/marquees/';
			}
			
			if (!is_dir($wordpress_directory.$magazine)) {
				mkdir($wordpress_directory.$magazine, 0700);
			}
			
			move_uploaded_file($_FILES['marquee_image']['tmp_name'], $wordpress_directory.$magazine.'/'.$name);

			if($magazine != '' && $magazine != ''){
				$location = 'wp-content/marquees/'.$magazine.'/'.$name;
				$marquee->upload_the_image($location, $magazine);
			}
			
		} elseif( isset($_POST['delete']) ){
			$post_size = count($_POST) - 1;
			global $wpdb;
			
			for($i=0; $i < $post_size; $i++){
				if($_POST["marquee_delete_$i"] == "deleteimage"){
					$wpdb->query('DELETE FROM wp_marquee WHERE id='.$_POST["marquee_id_$i"].'') or die(mysql_error());
				}
			}
		}


		$url = get_option('siteurl');
		$mar = new Marquee;
		$magazine = $mar->get_magazine_name($url);
		$marquees = $mar->get_data_for($magazine);

?>




<script type="text/javascript" src="http://tworld.com/wp-content/themes/news/includes/js/jquery-1.2.6.js"></script>
<script type="text/javascript" src="http://tworld.com/wp-content/themes/news/includes/js/ui.core.js"></script>
<script type="text/javascript" src="http://tworld.com/wp-content/themes/news/includes/js/ui.tabs.js"></script>			

<script type="text/javascript" src="http://tworld.com/wp-content/themes/news/includes/js/superfish.js"></script>

<script type="text/javascript">
            $(function() {
                $('#rotate > ul').tabs({ fx: { opacity: 'toggle' } }).tabs('rotate', 2000);
            });
        </script>

<style>
/*  

*/

/*========= FEATURED POSTS (FRONT-PAGE) =========*/

.featured{
	background: #dddddd;
	height: 200px;
	margin: 0 0 10px;
	overflow: hidden;
	padding-right:10px;
}
.featured h2{
	font-size: 11px;
	padding: 10px 0;
}
.featured h3{
	font-size: 14px;
	padding: 0 0 10px 0;
}
.featured h3 a {
	color:#000;
}
.featured h3 a:hover {
	color:#FF7800;
}
.featured p {
	margin-bottom:10px;
}
.featured-img{
	margin: 0 10px 0 0;
	width:350px;
	height:200px;
	float:left;
	display:inline;
}
#ribbon{
	width:138px;
	height:138px;
	float:right;
}
#featured-th{
	height: 57px;
	margin: 0 0 30px;
}
#featured-th img{
	margin: 0;
	border:none;
}
#featured-th .idTabs {
	list-style:none;
	}
#rotate li {
	float:left;
	display:inline;
	margin-right:12px;
	}
#rotate li.last {
	margin-right:0px !important;
	}
	


	/*--- Main Columns ---*/
	#columns{
		margin: 20px 0 0;
		padding: 0 15px 25px;
	}
	.col1{
		float: left;
		width: 550px;
	}
	.col2{
		float: right;
		width: 350px;
	}



</style>






	<div class="wrap">
		<h2>Marquee Upload &amp; Settings</h2>

		<!-- Upload -->
		<form method="post" enctype="multipart/form-data" name="marquee_upload_image">
			<fieldset>
				<input type="file" name="marquee_image" id="marquee_image" /> 
				<input type="hidden" name="marquee_magazine" id="marquee_magazine" value="<? echo $magazine ?>" />
			</fieldset>
			<p class="submit">
				<input type="submit" name="upload_image" id="upload_image" value="<?php _e('Update Image &raquo;') ?>" />
			</p>
		</form>
		
		
		<!-- Settings -->
		<form method="post">
			<fieldset>
				<table>
					<thead>
						<tr>
							<th>Image</th>
							<th>Image Location</th>
							<th>Image Link</th>
							<th>Headline</th>
							<th>Sub-Headline</th>
							<th>Audio Link</th>
							<th>Magazine</th>
							<th>Status</th>
							<th>Position</th>
							<th>Delete?</th>
						</tr>
					</thead>
				
					<tfoot></tfoot>
				
					<tbody>
						<? for($i=0; $i < count($marquees); $i++){ ?>
							<tr>
								<td><img src="<? echo $marquees[$i]["image"] ?>" alt="preview image" height="30" width="30" />
								<input type="hidden" id="marquee_id_<? echo $i ?>" name="marquee_id_<? echo $i ?>" value="<? echo $marquees[$i]["id"] ?>" /></td>
								<td><input type="text" size="14" id="marquee_image_<? echo $i ?>" name="marquee_image_<? echo $i ?>" value="<? echo $marquees[$i]["image"] ?>" /></td>
								<td><input type="text" size="15" id="marquee_image_link_<? echo $i ?>" name="marquee_image_link_<? echo $i ?>" value="<? echo $marquees[$i]["image_link"] ?>" /></td>
								<td><input type="text" size="14" id="marquee_headline_<? echo $i ?>" name="marquee_headline_<? echo $i ?>" value="<? echo $marquees[$i]["headline"] ?>" /></td>
								<td><input type="text" size="14" id="marquee_subheadline_<? echo $i ?>" name="marquee_subheadline_<? echo $i ?>" value="<? echo $marquees[$i]["subheadline"] ?>" /></td>
								<td><input type="text" size="14" id="marquee_audio_link_<? echo $i ?>" name="marquee_audio_link_<? echo $i ?>" value="<? echo $marquees[$i]["audio_link"] ?>" /></td>
								<td><input type="text" size="14" id="marquee_magazine_<? echo $i ?>" name="marquee_magazine_<? echo $i ?>" value="<? echo $marquees[$i]["magazine"] ?>" /></td>
								<td><select name="marquee_status_<? echo $i ?>">
									<option value="pending" <? echo $mar->check_if_selected($marquees[$i]['status'], "pending") ?>>Pending</option>
									<option value="publish" <? echo $mar->check_if_selected($marquees[$i]['status'], "publish") ?>>Publish</option>
								</select></td>
								<td><input type="text" size="3" id="marquee_position_<? echo $i ?>" name="marquee_position_<? echo $i ?>" value="<? echo $marquees[$i]["position"] ?>" /></td>
								<td><input type="checkbox" name="marquee_delete_<? echo $i ?>" value="deleteimage" /></td>
							</tr>
							<?php 
							} 
							?>
					</tbody>

				</table>
			</fieldset>
			<p class="submit">
				<input type="submit" name="edit_form" value="<?php _e('Update Settings &raquo;') ?>" /> 
				<input type="submit" name="delete" value="<?php _e('Delete Image(s) &raquo;') ?>" />
			</p>
		</form>
	</div>
<div class="col1">
<?php
	$m = new Marquee;
	echo $m->to_tabs($magazine);
?>
</div>

<?php

	}
?>

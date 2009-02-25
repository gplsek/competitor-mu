<?php

	class Marquee {
		function marquee() {}	// constructor
		
		
		// to_tabs
		// 
		// This method will create the <li> for the tabbed marquee
		function to_tabs($magazine){
		global $wpdb;	
		
		$tabs = '<div id="rotate">';
			
			$marquee = $wpdb->get_results('SELECT * FROM wp_marquee WHERE magazine="'.$magazine.'" AND status="publish" ORDER BY position LIMIT 4;', ARRAY_A);
			
			for($i=0; $i < count($marquee); $i++){
				
				// $tabs .= $marquee[$i]["headline"];
				// 				$tabs .= $marquee[$i]["image_link"];
				// 				$tabs .= $marquee[$i]["image"];
			   
			   	
				
			   	$tabs .= '<div class="featured" id="fragment-'.$i.'">';
			   	$tabs .=	'<div class="featured-img" style="background:url(/'.$marquee[$i]["image"].') top left no-repeat;">
				</div>';
				$tabs .= '<h3><a rel="bookmark" href="'.$marquee[$i]["image_link"].'">'.$marquee[$i]["headline"].'</a></h3>';
				$tabs .= '<p>'.$marquee[$i]["subheadline"].'</p>';
				$tabs .= '</div>';
			}
				//get the thumbs
				$tabs .= '<ul>';
				$marquee = $wpdb->get_results('SELECT * FROM wp_marquee WHERE magazine="'.$magazine.'" AND status="publish" ORDER BY position LIMIT 4;', ARRAY_A);
				for($i=0; $i < count($marquee); $i++){
				
				 if ($counter == 4) { 
					$tabs .= '<li class="last">';
				}
				else
				{
					$tabs .= '<li>';
				}
				$tabs .= '<a href="#fragment-'.$i.'"><img src="/'.$marquee[$i]["image"].'" height="57" alt=""/></a>';
				$tabs .= '</li>';
				
				
			}
				$tabs .= '</ul></div>';
				
				
					
				return $tabs;
		}
		
		
		// single_creator
		// $row => takes a single row
		// 
		// This will insert an image into the database that can later be added to
		// in the editor view. This will also be able to handle a full row.
		function single_creator($row){
			global $wpdb;
		
			$wpdb->query('INSERT INTO wp_marquee (image, image_link, headline, subheadline, audio_link, magazine, status, position) 		
									 VALUES("'.$row["image"].'", "'.$row["image_link"].'", "'.$row["headline"].'", "'.$row["subheadline"].'", "'.$row["audio_link"].'", 		
									 "'.$row["magazine"].'", "'.$row["status"].'", "'.$row["position"].')') or die(mysql_error());
			return true;
		}
		
		
		
		
		
		
		
		
		
		
		// get_data_for
		function get_data_for($magazine){
			global $wpdb;
			$marquee = $wpdb->get_results('SELECT * FROM wp_marquee WHERE magazine="'.$magazine.'" ORDER BY position;', ARRAY_A);
			return $marquee;
		}
		
		
		// single_updater
		// $row => a single row to update
		//
		// This is just like the single_creator method, except that it updates a row
		// instead of creating.
		function single_updater($row){
			global $wpdb;
			$marquee = $wpdb->get_row("SELECT * FROM wp_marquee WHERE id=".$row['id'].";");
			
			if($marquee != false){
				$wpdb->query('UPDATE wp_marquee set image="'.$row["image"].'" image_link="'.$row["image_link"].'" headline="'.$row["headline"].'"
				 						 subheadline="'.$row["subheadline"].'" audio_link="'.$row["audio_link"].'" magazine="'.$row["magazine"].'" 
										 status="'.$row["status"].'" position="'.$row["position"].'" WHERE id='.$row["id"].';') or die(mysql_error());
				return true;
			}
			return false;
		}

		
		// upload_the_image
		//
		// This will handle uploading a single image
		function upload_the_image($location, $magazine){
			global $wpdb;
			$wpdb->query('INSERT INTO wp_marquee (image, magazine, status) VALUES("'.$location.'", "'.$magazine.'", "pending"); ') or die(mysql_error());
		}
		
		
		// get_magazine_name
		function get_magazine_name($url){
			$parse_url_array 	= parse_url($url);
			$subdomain 				= explode('.', $parse_url_array['host']);
			if ($subdomain[0] == "www")
			{
			   $subdomain[0] = "competitor";
			}
			return $subdomain[0];
		}
		
		// return_filetype
		// $file => the file
		//
		// Takes the file and checks the extension to return the type
		function return_filetype($file){
			$file_basename = substr($file, 0, strripos($file, '.')); // strip extention
      $file_ext      = substr($file, strripos($file, '.'));
			
			if($file_ext == ".flv"){
				return "flv";
			} else {
				return "image";
			}
		}
		
		
		// return_image_name
		// $url => the url to the image location that is viewable by the browser
		//
		// This will return just the image name: e.g. madison.jpg
		function return_image_name($url){
			$arr2 = explode('/', $url);
			$arr2 = str_replace("<", '', $arr2);
			$image_name  = $arr2[count($arr2)-2];
			return $image_name;
		}
		
		
		// check_if_selected
		function check_if_selected($status, $desired){
			if($status == $desired){
				$select = 'selected="selected"';
			} else {
				$select = "";
			}
			return $select;
		}
		
		
		// sanitize_file_name
		function sanitize_file_name( $name ) { // Like sanitize_title, but with periods
			$name = strtolower( $name );
			$name = str_replace( '_', '-', $name );
			$name = preg_replace('/\s+/', '-', $name);
			$name = trim($name, '-');
			return $name;
		}
		
	}

	
	/*
			USE CASE
			---------------
			
			Authors comes and uploads an image for a post s/he is working on. There is a tab for the Marquee in the uploader
			or we can add it straight into the tinymce editor. They can click on that tab and see all images that have not
			been added to the marquee. They can then select an image, by clicking on it, and it will be added to the marquee
			database and set to pending.
			
			Then, an editor can log in and click on the Marquee tab to edit the marquee images in the database and add any
			type of content to: headline, subhealine, audio, image link, sorting order, pending/plublish, magazine etc. They can also
			choose to delete images from the database as well.
			
			The Audio can be handled on a per image basis, using a drop down menu. The audio should be uploaded using wordpress
			2.5's media uploader. 
			
			We'll need an alternate way of handling images that have been uploaded in this editor view as well. Perhaps a drop
			down and when an image name is selected, the image is displayed next to the drop down so the editor can view the 
			image.
			
			
			DEVELOPER NOTES
			---------------
			
			We'll need the following:
				
				* SQL insert method to handle mulitple row insertion
				* SQL insert for single row insertions
				* SQL update method to handle mulitple row updates
				* A display method to print out all the rows, for a given magazine, of the database in a table format
				* An XML output for a given magazine
			
			JavaScript pieces:
			
				* Ajax call for single click insert of images into the database
				* On a select element, pull the value and display the image
				* A Toggle method for inserting that select element
			
	*/
?>
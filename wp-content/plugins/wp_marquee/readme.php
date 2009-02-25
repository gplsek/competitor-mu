<?php

	/*
			FUNCTION TO CALL WITHIN TEMPLATES
			---------------------------------
			
			if(function_exists('wp_marquee_plugin')){
				$m = new Marquee;
				echo $m->to_flash();
			}
			
			'business' is the name of the magazine to pull for
			
		
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
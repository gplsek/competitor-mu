/*

		JavaScript pieces:
		
			* Ajax call for single click insert of images into the database
			* On a select element, pull the value and display the image
			* A Toggle method for inserting that select element
		
*/

$$('.meta_image_for_marquee').hide();

var Marquee = {
	
	
	// Ajax call for single click insert of images into the database
	clicksert : function(image, post_url){
		var req = new Ajax.Request(
			"wp-content/plugins/wp_marquee/wp_marquee.php", {
				method: "post",
				parameters: $H(
					{
						image_source: image, 
						image_link: post_url
					}
				).toQueryString()
			}
		); // ajax.request
	},
	
	
	// On a select element, pull the value and display the image
	imageDisplay : function(){
		
	},
	
	
	// A Toggle method for inserting that select element
	toggleSelect : function(){
		
	}
	
}
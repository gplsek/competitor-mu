<?php
	
	// marquee_install
	//
	// Our Database Installer
	function marquee_db_install(){
		global $wpdb;

		$query = "CREATE TABLE wp_marquee (
		  id int(11) NOT NULL auto_increment,
		  image varchar(255) NOT NULL,
		  image_link varchar(255) NOT NULL,
			headline varchar(255) NOT NULL,
			subheadline varchar(255) NOT NULL,
			audio_link varchar(255) NOT NULL,
			magazine varchar(255) NOT NULL,
			status varchar(255) NOT NULL DEFAULT 'pending',
			position int(11) NOT NULL,
		  PRIMARY KEY (id)
		);";


		$wpdb->query($query);
		
	
	}
	
	
	// Our Database uninstaller
	function marquee_db_uninstall() {
		global $wpdb;
		$query  = "DROP TABLE wp_marquee;";
		$wpdb->query($query);
	}

?>

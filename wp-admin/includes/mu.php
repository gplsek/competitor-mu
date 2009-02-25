<?php
function check_upload_size( $file ) {
	if( $file['error'] != '0' ) // there's already an error
		return $file;

	$space_allowed = 1048576 * get_space_allowed();
	$space_used = get_dirsize( BLOGUPLOADDIR );
	$space_left = $space_allowed - $space_used;
	$file_size = filesize( $file['tmp_name'] );
	if( $space_left < $file_size )
		$file['error'] = sprintf( __( 'Not enough space to upload. %1$s Kb needed.' ), number_format( ($file_size - $space_left) /1024 ) );
	if( $file_size > ( 1024 * get_site_option( 'fileupload_maxk', 1500 ) ) )
		$file['error'] = sprintf(__('This file is too big. Files must be less than %1$s Kb in size.'), get_site_option( 'fileupload_maxk', 1500 ) );
	if( upload_is_user_over_quota( false ) ) {
		$file['error'] = __('You have used your space quota. Please delete files before uploading.');
	}
	if( $file['error'] != '0' )
		wp_die( $file['error'] . ' <a href="javascript:history.go(-1)">' . __( 'Back' ) . '</a>' );

	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'check_upload_size' );

function wpmu_delete_blog($blog_id, $drop = false) {
	global $wpdb;

	if ( $blog_id != $wpdb->blogid ) {
		$switch = true;
		switch_to_blog($blog_id);
	}

	do_action('delete_blog', $blog_id, $drop);

	$users = get_users_of_blog($blog_id);

	// Remove users from this blog.
	if ( !empty($users) ) foreach ($users as $user) {
		remove_user_from_blog($user->user_id, $blog_id);
	}

	update_blog_status( $blog_id, 'deleted', 1 );

	if ( $drop ) {
		$drop_tables = $wpdb->get_results("show tables LIKE '". $wpdb->base_prefix . $blog_id . "\_%'", ARRAY_A); 
		$drop_tables = apply_filters( 'wpmu_drop_tables', $drop_tables ); 

		reset( $drop_tables );
		foreach ( (array) $drop_tables as $drop_table) {
			$wpdb->query( "DROP TABLE IF EXISTS ". current( $drop_table ) ."" );
		}
		$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->blogs WHERE blog_id = %d", $blog_id) );
		$dir = constant( "ABSPATH" ) . "wp-content/blogs.dir/{$blog_id}/files/";
		$dir = rtrim($dir, DIRECTORY_SEPARATOR);
		$top_dir = $dir;
		$stack = array($dir);
		$index = 0;

		while ($index < count($stack)) {
			# Get indexed directory from stack
			$dir = $stack[$index];

			$dh = @ opendir($dir);
			if ($dh) {
				while (($file = @ readdir($dh)) !== false) {
					if ($file == '.' or $file == '..')
						continue;

					if (@ is_dir($dir . DIRECTORY_SEPARATOR . $file))
						$stack[] = $dir . DIRECTORY_SEPARATOR . $file;
					else if (@ is_file($dir . DIRECTORY_SEPARATOR . $file))
						@ unlink($dir . DIRECTORY_SEPARATOR . $file);
				}
			}
			$index++;
		}

		$stack = array_reverse($stack);  // Last added dirs are deepest
		foreach( (array) $stack as $dir) {
			if ( $dir != $top_dir)
			@rmdir($dir);
		}
	}
	$wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->usermeta} WHERE meta_key = %s", 'wp_{$blog_id}_autosave_draft_ids') );

	if ( $switch === true )
		restore_current_blog();
}

function wpmu_delete_user($id) {
	global $wpdb;

	$id = (int) $id;
	$user = get_userdata($id);

	do_action('wpmu_delete_user', $id);

	$blogs = get_blogs_of_user($id);

	if ( ! empty($blogs) ) {
		foreach ($blogs as $blog) {
			switch_to_blog($blog->userblog_id);
			remove_user_from_blog($id, $blog->userblog_id);

			$post_ids = $wpdb->get_col( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_author = %d", $id ) );
			foreach ( (array) $post_ids as $post_id ) {
				wp_delete_post($post_id);
			}

			// Clean links
			$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->links WHERE link_owner = %d", $id) );

			restore_current_blog();
		}
	}

	$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->users WHERE ID = %d", $id) );
	$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE user_id = %d", $id) );

	wp_cache_delete($id, 'users');
	wp_cache_delete($user->user_login, 'userlogins');

	return true;
}

function confirm_delete_users( $users ) {
	if( !is_array( $users ) )
		return false;

	echo '<p>' . __( 'Transfer posts before deleting users:' ) . '</p>';

	echo '<form action="wpmu-edit.php?action=allusers" method="post">';
	echo '<input type="hidden" name="alluser_transfer_delete" />';
	wp_nonce_field( 'allusers' );
	foreach ( (array) $_POST['allusers'] as $key => $val ) {
		if( $val != '' && $val != '0' && $val != '1' ) {
			echo "<input type='hidden' name='user[]' value='{$val}'/>\n";
			$blogs = get_blogs_of_user( $val, true );
			if( !empty( $blogs ) ) {
				foreach ( (array) $blogs as $key => $details ) {
					$blog_users = get_users_of_blog( $details->userblog_id );
					if( is_array( $blog_users ) && !empty( $blog_users ) ) {
						echo "<p><a href='http://{$details->domain}{$details->path}'>{$details->blogname}</a> ";
						echo "<select name='blog[$val][{$key}]'>";
						$out = '';
						foreach( $blog_users as $user ) {
							if( $user->user_id != $val )
								$out .= "<option value='{$user->user_id}'>{$user->user_login}</option>";
						}
						if( $out == '' )
							$out = "<option value='1'>admin</option>";
						echo $out;
						echo "</select>\n";
					}
				}
			}
		}
	}
	echo "<br /><input type='submit' value='" . __( 'Delete user and transfer posts' ) . "' />";
	echo "</form>";
	return true;
}

function wpmu_get_blog_allowedthemes( $blog_id = 0 ) {
	$themes = get_themes();
	if( $blog_id == 0 )
		$blog_allowed_themes = get_option( "allowedthemes" );
	else
		$blog_allowed_themes = get_blog_option( $blog_id, "allowedthemes" );
	if( !is_array( $blog_allowed_themes ) || empty( $blog_allowed_themes ) ) { // convert old allowed_themes to new allowedthemes
		if( $blog_id == 0 )
			$blog_allowed_themes = get_option( "allowed_themes" );
		else
			$blog_allowed_themes = get_blog_option( $blog_id, "allowed_themes" );

		if( is_array( $blog_allowed_themes ) ) {
			foreach( (array) $themes as $key => $theme ) {
				$theme_key = wp_specialchars( $theme[ 'Stylesheet' ] );
				if( isset( $blog_allowed_themes[ $key ] ) == true ) {
					$blog_allowedthemes[ $theme_key ] = 1;
				}
			}
			$blog_allowed_themes = $blog_allowedthemes;
			if( $blog_id == 0 ) {
				add_option( "allowedthemes", $blog_allowed_themes );
				delete_option( "allowed_themes" );
			} else {
				add_blog_option( $blog_id, "allowedthemes", $blog_allowed_themes );
				delete_blog_option( $blog_id, "allowed_themes" );
			}
		}
	}
	return $blog_allowed_themes;
}

function update_option_new_admin_email($old_value, $value) {
	global $current_site;
	if ( $value == get_option( 'admin_email' ) || !is_email( $value ) )
		return;

	$hash = md5( $value. time() .mt_rand() );
	$new_admin_email = array(
		"hash" => $hash,
		"newemail" => $value
	);
	update_option( 'adminhash', $new_admin_email );
	
	$content = apply_filters( 'new_admin_email_content', __("Dear user,

You recently requested to have the administration email address on 
your blog changed.
If this is correct, please click on the following link to change it:
###ADMIN_URL###

You can safely ignore and delete this email if you do not want to
take this action.

This email has been sent to ###EMAIL###

Regards,
All at ###SITENAME###
###SITEURL###"), $new_admin_email );
	
	$content = str_replace('###ADMIN_URL###', clean_url(get_option( "siteurl" ).'/wp-admin/options.php?adminhash='.$hash), $content);
	$content = str_replace('###EMAIL###', $value, $content);
	$content = str_replace('###SITENAME###', get_site_option( 'site_name' ), $content);
	$content = str_replace('###SITEURL###', 'http://' . $current_site->domain . $current_site->path, $content);
	
	wp_mail( $value, sprintf(__('[%s] New Admin Email Address'), get_option('blogname')), $content );
}
add_action('update_option_new_admin_email', 'update_option_new_admin_email', 10, 2);

function profile_page_email_warning_ob_start() {
	ob_start( 'profile_page_email_warning_ob_content' );
}

function profile_page_email_warning_ob_content( $content ) {
	$content = str_replace( ' class="regular-text" /> Required.</td>', ' class="regular-text" /> Required. (You will be sent an email to confirm the change)</td>', $content );
	return $content;
}

function update_profile_email() {
	global $current_user;
	if( isset( $_GET[ 'newuseremail' ] ) && $current_user->ID ) {
		$new_email = get_option( $current_user->ID . '_new_email' );
		if( $new_email[ 'hash' ] == $_GET[ 'newuseremail' ] ) {
			$user->ID = $current_user->ID;
			$user->user_email = wp_specialchars( trim( $new_email[ 'newemail' ] ) );
			wp_update_user( get_object_vars( $user ) );
			delete_option( $current_user->ID . '_new_email' );
			wp_redirect( add_query_arg( array('updated' => 'true'), admin_url( 'profile.php' ) ) );
			die();
		}
	}
}
if( strpos( $_SERVER['PHP_SELF'], 'profile.php' ) ) {
	add_action( 'admin_init', 'update_profile_email' );
	add_action( 'admin_init', 'profile_page_email_warning_ob_start' );
}

function send_confirmation_on_profile_email() {
	global $errors, $wpdb, $current_user, $current_site;
	$errors = new WP_Error();

	if( $current_user->id != $_POST[ 'user_id' ] )
		return false;

	if( $current_user->user_email != $_POST[ 'email' ] ) {
		if ( !is_email( $_POST[ 'email' ] ) ) {
			$errors->add( 'user_email', __( "<strong>ERROR</strong>: The e-mail address isn't correct." ), array( 'form-field' => 'email' ) );
			return;
		}

		if( $wpdb->get_var( $wpdb->prepare( "SELECT user_email FROM {$wpdb->users} WHERE user_email=%s", $_POST[ 'email' ] ) ) ) {
			$errors->add( 'user_email', __( "<strong>ERROR</strong>: The e-mail address is already used." ), array( 'form-field' => 'email' ) );
			delete_option( $current_user->ID . '_new_email' );
			return;
		}

		$hash = md5( $_POST[ 'email' ] . time() . mt_rand() );
		$new_user_email = array(
				"hash" => $hash,
				"newemail" => $_POST[ 'email' ]
				);
		update_option( $current_user->ID . '_new_email', $new_user_email );

		$content = apply_filters( 'new_user_email_content', __("Dear user,

You recently requested to have the email address on your account changed.
If this is correct, please click on the following link to change it:
###ADMIN_URL###

You can safely ignore and delete this email if you do not want to
take this action.

This email has been sent to ###EMAIL###

Regards,
All at ###SITENAME###
###SITEURL###"), $new_user_email );

		$content = str_replace('###ADMIN_URL###', clean_url(get_option( "siteurl" ).'/wp-admin/profile.php?newuseremail='.$hash), $content);
		$content = str_replace('###EMAIL###', $_POST[ 'email' ], $content);
		$content = str_replace('###SITENAME###', get_site_option( 'site_name' ), $content);
		$content = str_replace('###SITEURL###', 'http://' . $current_site->domain . $current_site->path, $content);

		wp_mail( $_POST[ 'email' ], sprintf(__('[%s] New Email Address'), get_option('blogname')), $content );
		$_POST[ 'email' ] = $current_user->user_email;
	}
}
add_action( 'personal_options_update', 'send_confirmation_on_profile_email' );

function new_user_email_admin_notice() {
	global $current_user;
	if( strpos( $_SERVER['PHP_SELF'], 'profile.php' ) && isset( $_GET[ 'updated' ] ) && $email = get_option( $current_user->ID . '_new_email' ) )
		echo "<div id='update-nag'>" . sprintf( __( "Your email address has not been updated yet. Please check your inbox at %s for a confirmation email." ), $email[ 'newemail' ] ) . "</div>";
}
add_action( 'admin_notices', 'new_user_email_admin_notice' );

function get_site_allowed_themes() {
	$themes = get_themes();
	$allowed_themes = get_site_option( 'allowedthemes' );
	if( !is_array( $allowed_themes ) || empty( $allowed_themes ) ) {
		$allowed_themes = get_site_option( "allowed_themes" ); // convert old allowed_themes format
		if( !is_array( $allowed_themes ) ) {
			$allowed_themes = array();
		} else {
			foreach( (array) $themes as $key => $theme ) {
				$theme_key = wp_specialchars( $theme[ 'Stylesheet' ] );
				if( isset( $allowed_themes[ $key ] ) == true ) {
					$allowedthemes[ $theme_key ] = 1;
				}
			}
			$allowed_themes = $allowedthemes;
		}
	}
	return $allowed_themes;
}

function get_space_allowed() {
	$spaceAllowed = get_option("blog_upload_space");
	if( $spaceAllowed == false ) 
		$spaceAllowed = get_site_option("blog_upload_space");
	if( empty($spaceAllowed) || !is_numeric($spaceAllowed) )
		$spaceAllowed = 50;

	return $spaceAllowed;
}

function display_space_usage() {
	$space = get_space_allowed();
	$used = get_dirsize( BLOGUPLOADDIR )/1024/1024;

	if ($used > $space) $percentused = '100';
	else $percentused = ( $used / $space ) * 100;

	if( $space > 1000 ) {
		$space = number_format( $space / 1024 );
		$space .= __('GB');
	} else {
		$space .= __('MB');
	}
	?>
	<strong><?php printf(__('Used: %1s%% of %2s'), number_format($percentused), $space );?></strong> 
	<?php
}

// Display File upload quota on dashboard
function dashboard_quota() {	
	$quota = get_space_allowed();
	$used = get_dirsize( BLOGUPLOADDIR )/1024/1024;

	if ($used > $quota) $percentused = '100';
	else $percentused = ( $used / $quota ) * 100;
	$percentused = number_format($percentused);
	$used = round($used,2);
	$used_color = ($used < 70) ? (($used >= 40) ? 'waiting' : 'approved') : 'spam';
	?>
	<p class="sub musub"><?php _e("Storage Space <a href='upload.php' title='Manage Uploads...'>&raquo;</a>"); ?></p>
	<div class="table">
	<table>
		<tr class="first">
			<td class="first b b-posts"><?php _e('<a href="upload.php" title="Manage Uploads..." class="musublink">'); echo $quota . __('MB</a>'); ?></td>
			<td class="t posts"><?php _e('Space Allowed'); ?></td>
			<td class="b b-comments"><?php _e('<a href="upload.php" title="Manage Uploads..." class="musublink">'. $used .'MB ('. $percentused .'%)</a>'); ?></td>
			<td class="last t comments <?php echo $used_color;?>"><?php _e('Space Used');?></td>
		</tr>
	</table>
	</div>
	<?php
}
if( current_user_can('edit_posts') )
	add_action('activity_box_end', 'dashboard_quota');

// Edit blog upload space setting on Edit Blog page
function upload_space_setting( $id ) {
	$quota = get_blog_option($id, "blog_upload_space"); 
	if( !$quota )
		$quota = '';
	
	?>
	<tr>
		<th><?php _e('Blog Upload Space Quota'); ?></th>
		<td><input type="text" size="3" name="option[blog_upload_space]" value="<?php echo $quota; ?>" /><?php _e('MB (Leave blank for site default)'); ?></td>
	</tr>
	<?php
}
add_action('wpmueditblogaction', 'upload_space_setting');

function update_user_status( $id, $pref, $value, $refresh = 1 ) {
	global $wpdb;

	$wpdb->update( $wpdb->users, array( $pref => $value ), array( 'ID' => $id ) );

	if( $refresh == 1 )
		refresh_user_details($id);
	
	if( $pref == 'spam' ) {
		if( $value == 1 ) 
			do_action( "make_spam_user", $id );
		else
			do_action( "make_ham_user", $id );
	}

	return $value;
}

function refresh_user_details($id) {
	$id = (int) $id;
	
	if ( !$user = get_userdata( $id ) )
		return false;

	wp_cache_delete($id, 'users');
	wp_cache_delete($user->user_login, 'userlogins');
	return $id;
}

/*
  Determines if the available space defined by the admin has been exceeded by the user
*/
function wpmu_checkAvailableSpace() {
	$spaceAllowed = get_space_allowed();

	$dirName = trailingslashit( BLOGUPLOADDIR );
	if (!(is_dir($dirName) && is_readable($dirName))) 
		return; 

  	$dir = dir($dirName);
   	$size = 0;

	while($file = $dir->read()) {
		if ($file != '.' && $file != '..') {
			if (is_dir( $dirName . $file)) {
				$size += get_dirsize($dirName . $file);
			} else {
				$size += filesize($dirName . $file);
			}
		}
	}
	$dir->close();
	$size = $size / 1024 / 1024;

	if( ($spaceAllowed - $size) <= 0 ) {
		define( 'DISABLE_UPLOADS', true );
		define( 'DISABLE_UPLOADS_MESSAGE', __('Sorry, you must delete files before you can upload any more.') );
	}
}
add_action('upload_files_upload','wpmu_checkAvailableSpace');

function format_code_lang( $code = '' ) {
	$code = strtolower(substr($code, 0, 2));
	$lang_codes = array('aa' => 'Afar',  'ab' => 'Abkhazian',  'af' => 'Afrikaans',  'ak' => 'Akan',  'sq' => 'Albanian',  'am' => 'Amharic',  'ar' => 'Arabic',  'an' => 'Aragonese',  'hy' => 'Armenian',  'as' => 'Assamese',  'av' => 'Avaric',  'ae' => 'Avestan',  'ay' => 'Aymara',  'az' => 'Azerbaijani',  'ba' => 'Bashkir',  'bm' => 'Bambara',  'eu' => 'Basque',  'be' => 'Belarusian',  'bn' => 'Bengali',  'bh' => 'Bihari',  'bi' => 'Bislama',  'bs' => 'Bosnian',  'br' => 'Breton',  'bg' => 'Bulgarian',  'my' => 'Burmese',  'ca' => 'Catalan; Valencian',  'ch' => 'Chamorro',  'ce' => 'Chechen',  'zh' => 'Chinese',  'cu' => 'Church Slavic; Old Slavonic; Church Slavonic; Old Bulgarian; Old Church Slavonic',  'cv' => 'Chuvash',  'kw' => 'Cornish',  'co' => 'Corsican',  'cr' => 'Cree',  'cs' => 'Czech',  'da' => 'Danish',  'dv' => 'Divehi; Dhivehi; Maldivian',  'nl' => 'Dutch; Flemish',  'dz' => 'Dzongkha',  'en' => 'English',  'eo' => 'Esperanto',  'et' => 'Estonian',  'ee' => 'Ewe',  'fo' => 'Faroese',  'fj' => 'Fijian',  'fi' => 'Finnish',  'fr' => 'French',  'fy' => 'Western Frisian',  'ff' => 'Fulah',  'ka' => 'Georgian',  'de' => 'German',  'gd' => 'Gaelic; Scottish Gaelic',  'ga' => 'Irish',  'gl' => 'Galician',  'gv' => 'Manx',  'el' => 'Greek, Modern',  'gn' => 'Guarani',  'gu' => 'Gujarati',  'ht' => 'Haitian; Haitian Creole',  'ha' => 'Hausa',  'he' => 'Hebrew',  'hz' => 'Herero',  'hi' => 'Hindi',  'ho' => 'Hiri Motu',  'hu' => 'Hungarian',  'ig' => 'Igbo',  'is' => 'Icelandic',  'io' => 'Ido',  'ii' => 'Sichuan Yi',  'iu' => 'Inuktitut',  'ie' => 'Interlingue',  'ia' => 'Interlingua (International Auxiliary Language Association)',  'id' => 'Indonesian',  'ik' => 'Inupiaq',  'it' => 'Italian',  'jv' => 'Javanese',  'ja' => 'Japanese',  'kl' => 'Kalaallisut; Greenlandic',  'kn' => 'Kannada',  'ks' => 'Kashmiri',  'kr' => 'Kanuri',  'kk' => 'Kazakh',  'km' => 'Central Khmer',  'ki' => 'Kikuyu; Gikuyu',  'rw' => 'Kinyarwanda',  'ky' => 'Kirghiz; Kyrgyz',  'kv' => 'Komi',  'kg' => 'Kongo',  'ko' => 'Korean',  'kj' => 'Kuanyama; Kwanyama',  'ku' => 'Kurdish',  'lo' => 'Lao',  'la' => 'Latin',  'lv' => 'Latvian',  'li' => 'Limburgan; Limburger; Limburgish',  'ln' => 'Lingala',  'lt' => 'Lithuanian',  'lb' => 'Luxembourgish; Letzeburgesch',  'lu' => 'Luba-Katanga',  'lg' => 'Ganda',  'mk' => 'Macedonian',  'mh' => 'Marshallese',  'ml' => 'Malayalam',  'mi' => 'Maori',  'mr' => 'Marathi',  'ms' => 'Malay',  'mg' => 'Malagasy',  'mt' => 'Maltese',  'mo' => 'Moldavian',  'mn' => 'Mongolian',  'na' => 'Nauru',  'nv' => 'Navajo; Navaho',  'nr' => 'Ndebele, South; South Ndebele',  'nd' => 'Ndebele, North; North Ndebele',  'ng' => 'Ndonga',  'ne' => 'Nepali',  'nn' => 'Norwegian Nynorsk; Nynorsk, Norwegian',  'nb' => 'Bokmål, Norwegian, Norwegian Bokmål',  'no' => 'Norwegian',  'ny' => 'Chichewa; Chewa; Nyanja',  'oc' => 'Occitan, Provençal',  'oj' => 'Ojibwa',  'or' => 'Oriya',  'om' => 'Oromo',  'os' => 'Ossetian; Ossetic',  'pa' => 'Panjabi; Punjabi',  'fa' => 'Persian',  'pi' => 'Pali',  'pl' => 'Polish',  'pt' => 'Portuguese',  'ps' => 'Pushto',  'qu' => 'Quechua',  'rm' => 'Romansh',  'ro' => 'Romanian',  'rn' => 'Rundi',  'ru' => 'Russian',  'sg' => 'Sango',  'sa' => 'Sanskrit',  'sr' => 'Serbian',  'hr' => 'Croatian',  'si' => 'Sinhala; Sinhalese',  'sk' => 'Slovak',  'sl' => 'Slovenian',  'se' => 'Northern Sami',  'sm' => 'Samoan',  'sn' => 'Shona',  'sd' => 'Sindhi',  'so' => 'Somali',  'st' => 'Sotho, Southern',  'es' => 'Spanish; Castilian',  'sc' => 'Sardinian',  'ss' => 'Swati',  'su' => 'Sundanese',  'sw' => 'Swahili',  'sv' => 'Swedish',  'ty' => 'Tahitian',  'ta' => 'Tamil',  'tt' => 'Tatar',  'te' => 'Telugu',  'tg' => 'Tajik',  'tl' => 'Tagalog',  'th' => 'Thai',  'bo' => 'Tibetan',  'ti' => 'Tigrinya',  'to' => 'Tonga (Tonga Islands)',  'tn' => 'Tswana',  'ts' => 'Tsonga',  'tk' => 'Turkmen',  'tr' => 'Turkish',  'tw' => 'Twi',  'ug' => 'Uighur; Uyghur',  'uk' => 'Ukrainian',  'ur' => 'Urdu',  'uz' => 'Uzbek',  've' => 'Venda',  'vi' => 'Vietnamese',  'vo' => 'Volapük',  'cy' => 'Welsh',  'wa' => 'Walloon',  'wo' => 'Wolof',  'xh' => 'Xhosa',  'yi' => 'Yiddish',  'yo' => 'Yoruba',  'za' => 'Zhuang; Chuang',  'zu' => 'Zulu');
	$lang_codes = apply_filters('lang_codes', $lang_codes, $code);
	return strtr( $code, $lang_codes );
}

function sync_slugs( $term, $taxonomy, $args ) {
	$args[ 'slug' ] = sanitize_title( $args[ 'name' ] );
	return $args;
}
add_filter( 'pre_update_term', 'sync_slugs', 10, 3 );

function redirect_user_to_blog() {
	global $current_user, $current_site;
	$details = get_active_blog_for_user( $current_user->ID );
	if( $details == "username only" ) {
		add_user_to_blog( get_blog_id_from_url( $current_site->domain, $current_site->path ), $current_user->ID, 'subscriber'); // Add subscriber permission for first blog.
		wp_redirect( 'http://' . $current_site->domain . $current_site->path. 'wp-admin/' );
		exit();
	} elseif( is_object( $details ) ) {
		wp_redirect( "http://" . $details->domain . $details->path . 'wp-admin/' );
		exit;
	} else {
		wp_redirect( "http://" . $current_site->domain . $current_site->path );
		exit;
	}
	wp_die( __('You do not have sufficient permissions to access this page.') );
}
add_action( 'admin_page_access_denied', 'redirect_user_to_blog', 99 );

function wpmu_menu() {
	global $menu, $submenu;

	if( is_site_admin() ) {
		$menu[1] = array( '', 'read', '', '', 'wp-menu-separator' );
		$menu[2] = array(__('Site Admin'), '10', 'wpmu-admin.php', '', 'wp-menu-open menu-top menu-top-first', 'menu-site', 'div');
		$submenu[ 'wpmu-admin.php' ][1] = array( __('Admin'), '10', 'wpmu-admin.php' );
		$submenu[ 'wpmu-admin.php' ][5] = array( __('Blogs'), '10', 'wpmu-blogs.php' );
		$submenu[ 'wpmu-admin.php' ][10] = array( __('Users'), '10', 'wpmu-users.php' );
		$submenu[ 'wpmu-admin.php' ][20] = array( __('Themes'), '10', 'wpmu-themes.php' );
		$submenu[ 'wpmu-admin.php' ][25] = array( __('Options'), '10', 'wpmu-options.php' );
		$submenu[ 'wpmu-admin.php' ][30] = array( __('Upgrade'), '10', 'wpmu-upgrade-site.php' );
	}

	if( !is_site_admin() )
		unset( $submenu['plugins.php'][10] ); // always remove the plugin installer for regular users
	unset( $submenu['plugins.php'][15] ); // always remove the plugin editor
	unset( $submenu['themes.php'][10] ); // always remove the themes editor

	$menu_perms = get_site_option( "menu_items" );
	if( is_array( $menu_perms ) == false )
		$menu_perms = array();
	if( $menu_perms[ 'plugins' ] != 1 ) {
		if( !is_site_admin() ) {
			unset( $menu['45'] ); // Plugins
		} else {
			$menu[45] = array( __('Plugins') . ' <strong>*</strong>', 'activate_plugins', 'wpmu-options.php#menu', '', 'menu-top', 'menu-plugins', 'div' );
		}
		unset( $submenu[ 'plugins.php' ] );
	}
	if( !get_site_option( 'add_new_users' ) ) {
		if( !is_site_admin() ) {
			unset( $submenu['users.php'][10] );
		} else {
			$submenu['users.php'][10] = array(__('Add New') . ' <strong>*</strong>', 'create_users', 'wpmu-options.php#addnewusers');
		}
	}
	unset( $submenu['tools.php'][20] ); // core upgrade
	unset( $submenu['options-general.php'][45] ); // Misc
}
add_action( '_admin_menu', 'wpmu_menu' );

function mu_options( $options ) {
	$removed = array( 
		'general' => array( 'siteurl', 'home', 'admin_email', 'default_role' ),
		'reading' => array( 'gzipcompression' ),
		'writing' => array( 'ping_sites', 'mailserver_login', 'mailserver_pass', 'default_email_category', 'mailserver_port', 'mailserver_url' ),
	);

	$added = array( 'general' => array( 'new_admin_email', 'WPLANG', 'language' ) );

	$options[ 'misc' ] = array();

	$options = remove_option_whitelist( $removed, $options );
	$options = add_option_whitelist( $added, $options );

	return $options;
}
add_filter( 'whitelist_options', 'mu_options' );

function import_no_new_users( $permission ) {
	return false;
}
add_filter( 'import_allow_create_users', 'import_no_new_users' );
// See "import_allow_fetch_attachments" and "import_attachment_size_limit" filters too.

function mu_css() {
	wp_admin_css( 'css/mu' );
}
add_action( 'admin_head', 'mu_css' );

function mu_dropdown_languages( $lang_files = array(), $current = '' ) {
	$flag = false;	
	$output = array();
					
	foreach ( (array) $lang_files as $val ) {
		$code_lang = basename( $val, '.mo' );
		
		if ( $code_lang == 'en_US' ) { // American English
			$flag = true;
			$ae = __('American English');
			$output[$ae] = '<option value="'.$code_lang.'"'.(($current == $code_lang) ? ' selected="selected"' : '').'> '.$ae.'</option>';
		} elseif ( $code_lang == 'en_GB' ) { // British English
			$flag = true;
			$be = __('British English');
			$output[$be] = '<option value="'.$code_lang.'"'.(($current == $code_lang) ? ' selected="selected"' : '').'> '.$be.'</option>';
		} else {
			$translated = format_code_lang($code_lang);
			$output[$translated] =  '<option value="'.$code_lang.'"'.(($current == $code_lang) ? ' selected="selected"' : '').'> '.$translated.'</option>';
		}
		
	}						
	
	if ( $flag === false ) { // WordPress english
		$output[] = '<option value=""'.((empty($current)) ? ' selected="selected"' : '').'>'.__('English')."</option>";
	}
	
	// Order by name
	uksort($output, 'strnatcasecmp');
	
	$output = apply_filters('mu_dropdown_languages', $output, $lang_files, $current);	
	echo implode("\n\t", $output);	
}

// Only show "Media" upload icon
function mu_media_buttons() {
	global $post_ID, $temp_ID;
	$uploading_iframe_ID = (int) (0 == $post_ID ? $temp_ID : $post_ID);
	$context = apply_filters('media_buttons_context', __('Add media: %s'));
	$media_upload_iframe_src = "media-upload.php?post_id=$uploading_iframe_ID";
	$media_title = __('Add Media');
	$mu_media_buttons = get_site_option( 'mu_media_buttons' );
	$out = '';
	if( $mu_media_buttons[ 'image' ] ) {
		$image_upload_iframe_src = apply_filters('image_upload_iframe_src', "$media_upload_iframe_src&amp;type=image");
		$image_title = __('Add an Image');
		$out .= "<a href='{$image_upload_iframe_src}&amp;TB_iframe=true' id='add_image' class='thickbox' title='$image_title'><img src='images/media-button-image.gif' alt='$image_title' /></a>";
	}
	if( $mu_media_buttons[ 'video' ] ) {
		$video_upload_iframe_src = apply_filters('video_upload_iframe_src', "$media_upload_iframe_src&amp;type=video");
		$video_title = __('Add Video');
		$out .= "<a href='{$video_upload_iframe_src}&amp;TB_iframe=true' id='add_video' class='thickbox' title='$video_title'><img src='images/media-button-video.gif' alt='$video_title' /></a>";
	}
	if( $mu_media_buttons[ 'audio' ] ) {
		$audio_upload_iframe_src = apply_filters('audio_upload_iframe_src', "$media_upload_iframe_src&amp;type=audio");
		$audio_title = __('Add Audio');
		$out .= "<a href='{$audio_upload_iframe_src}&amp;TB_iframe=true' id='add_audio' class='thickbox' title='$audio_title'><img src='images/media-button-music.gif' alt='$audio_title' /></a>";
	}
	$out .= "<a href='{$media_upload_iframe_src}&amp;TB_iframe=true&amp;height=500&amp;width=640' class='thickbox' title='$media_title'><img src='images/media-button-other.gif' alt='$media_title' /></a>";
	printf($context, $out);
}
add_action( 'media_buttons', 'mu_media_buttons' );
remove_action( 'media_buttons', 'media_buttons' );

/* Warn the admin if SECRET SALT information is missing from wp-config.php */
function secret_salt_warning() {
	if( !is_site_admin() )
		return;
	$secret_keys = array( 'NONCE_KEY', 'AUTH_KEY', 'AUTH_SALT', 'LOGGED_IN_KEY', 'LOGGED_IN_SALT', 'SECURE_AUTH_KEY', 'SECURE_AUTH_SALT' );
	$out = '';
	foreach( $secret_keys as $key ) {
		if( !defined( $key ) )
			$out .= "define( '$key', '" . wp_generate_password() . wp_generate_password() . "' );<br />";
	}
	if( $out != '' ) {
		$msg = sprintf( __( 'Warning! WordPress encrypts user cookies, but you must add the following lines to <strong>%swp-config.php</strong> for it to be more secure.<br />Please add the code before the line, <code>/* That\'s all, stop editing! Happy blogging. */</code>' ), ABSPATH );
		$msg .= "<blockquote>$out</blockquote>";

		echo "<div id='update-nag'>$msg</div>";
	}
}
add_action( 'admin_notices', 'secret_salt_warning' );

function mu_dashboard() {
	unregister_sidebar_widget( 'dashboard_plugins' );
}
add_action( 'wp_dashboard_setup', 'mu_dashboard' );

function profile_update_primary_blog() {
	global $current_user;

	if ( isset( $_POST['primary_blog'] ) ) {
		update_user_option( $current_user->id, 'primary_blog', (int) $_POST['primary_blog'], true );
	}
}
add_action( 'personal_options_update', 'profile_update_primary_blog' );

function admin_notice_feed() {
	global $current_user;
	if( substr( $_SERVER[ 'PHP_SELF' ], -19 ) != '/wp-admin/index.php' )
		return;

	if( isset( $_GET[ 'feed_dismiss' ] ) )
		update_user_option( $current_user->id, 'admin_feed_dismiss', $_GET[ 'feed_dismiss' ], true );

	$url = get_site_option( 'admin_notice_feed' );
	if( $url == '' )
		return;
	include_once( ABSPATH . 'wp-includes/rss.php' );
	$rss = @fetch_rss( $url );
	if( isset($rss->items) && 1 <= count($rss->items) ) {
		if( md5( $rss->items[0][ 'title' ] ) == get_user_option( 'admin_feed_dismiss', $current_user->id ) )
			return;
		$item = $rss->items[0];
		$msg = "<h3>" . wp_specialchars( $item[ 'title' ] ) . "</h3>\n";
		if ( isset($item['description']) )
			$content = $item['description'];
		elseif ( isset($item['summary']) )
			$content = $item['summary'];
		elseif ( isset($item['atom_content']) )
			$content = $item['atom_content'];
		else
			$content = __( 'something' );
		$content = wp_html_excerpt($content, 200) . ' ...';
		$link = clean_url( strip_tags( $item['link'] ) );
		$msg .= "<p>" . $content . " <a href='$link'>" . __( 'Read More' ) . "</a> <a href='index.php?feed_dismiss=" . md5( $item[ 'title' ] ) . "'>" . __( "Dismiss" ) . "</a></p>";
		echo "<div class='updated fade'>$msg</div>";
	} elseif( is_site_admin() ) {
		printf("<div id='update-nag'>" . __("Your feed at %s is empty.") . "</div>", wp_specialchars( $url ));
	}
}
add_action( 'admin_notices', 'admin_notice_feed' );

function site_admin_notice() {
	global $current_user;
	if( is_site_admin() )
		printf("<div id='update-nag'>" . __("Hi %s! You're logged in as a site administrator.") . "</div>", $current_user->user_login);
}
add_action( 'admin_notices', 'site_admin_notice' );

function wpa_dashboards( $menu ) {
	global $current_user, $current_blog;
	$primary_blog = get_usermeta( $current_user->ID, 'primary_blog' );
	$blogs = get_blogs_of_user( $current_user->ID );

	foreach ( (array) $blogs as $blog ) {
		if ( !$blog->blogname || $blog->blogname == '' ) {
			if( constant( VHOST ) ) {
				$blog->blogname = $blog->domain;
			} else {
				$blog->blogname = $blog->path;
			}
		}

		if ( $current_blog->blog_id == $blog->userblog_id )
			$blog->blogname = $blog->blogname . " *";

		$url = clean_url( $blog->siteurl ) . '/wp-admin/';
		$name = wp_specialchars( strip_tags( $blog->blogname ) );
		$list_item = array( 'url' => $url, 'name' => $name );

		if ( $current_blog->blog_id == $blog->userblog_id ) {
			$list[-2] = $list_item;
		} elseif ( $primary_blog == $blog->userblog_id ) {
			$list[-1] = $list_item;
		} else {
			$list[] = $list_item;
		}
	}
	ksort($list);
	foreach( $list as $blog ) {
		$menu[ 'index.php' ][ $blog[ 'url' ] ] = array( 'title' => $blog[ 'name' ] );
	}

	return $menu;
}
add_filter( 'wpabar_menuitems', 'wpa_dashboards' );
?>

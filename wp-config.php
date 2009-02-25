<?php
/** 
 * The base configurations of the WordPress.
 *
 **************************************************************************
 * Do not try to create this file manually. Read the README.txt and run the 
 * web installer.
 **************************************************************************
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. 
 *
 * This file is used by the wp-config.php creation script during the
 * installation.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'competitor');

/** MySQL database username */
define('DB_USER', 'specialstage');

/** MySQL database password */
define('DB_PASSWORD', '12Sunset');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');
define('VHOST', 'yes'); 
$base = '/';
define('DOMAIN_CURRENT_SITE', 'tworld.com' );
define('PATH_CURRENT_SITE', '/' );
define('BLOGID_CURRENT_SITE', '1' );

/**#@+
 * Authentication Unique Keys.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link http://api.wordpress.org/secret-key/1.1/ WordPress.org secret-key service}
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 'c6109b030a5e06f5b2c677fc7fd26979fd5f1b4c921e4199feec7e82d68ca052');
define('SECURE_AUTH_KEY', 'bb646447e56e6e06e804917bcae8849f0fe899877319f5cdd0380b6debc302d9');
define('LOGGED_IN_KEY', '90b396560b5e8180443e1162c574825f79fa2014e3e3448bf46a8e4ca8358c8f');
define('NONCE_KEY', 'ffa7d3609bae54345c2d4b3a1fd02b252d4ec348b7b0bc81039cd4b163caae77');
define('AUTH_SALT', '7549e02b1ef5f2e026f43553f217fed6d098d0b0b4a036c3e87613db1e2de4cd');
define('LOGGED_IN_SALT', 'c9c46a9411d1a749cfca2607c987d0e8ffd65b041c8a0b127fc6dcf27f3f73c0');
define('SECURE_AUTH_SALT', 'e3786fa2fa95ad48e9ae3c8883273bc47108f75e223254163726de3bec233448');
/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress.  A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de.mo to wp-content/languages and set WPLANG to 'de' to enable German
 * language support.
 */
define ('WPLANG', '');

// double check $base
if( $base == 'BASE' )
	die( 'Problem in wp-config.php - $base is set to BASE when it should be the path like "/" or "/blogs/"! Please fix it!' );

// uncomment this to enable wp-content/sunrise.php support
//define( 'SUNRISE', 'on' );

// uncomment to move wp-content/blogs.dir to another relative path
// remember to change WP_CONTENT too.
// define( "UPLOADBLOGSDIR", "fileserver" );

// If VHOST is 'yes' uncomment and set this to a URL to redirect if a blog does not exist or is a 404 on the main blog. (Useful if signup is disabled)
// For example, the browser will redirect to http://examples.com/ for the following: define( 'NOBLOGREDIRECT', 'http://example.com/' );
// define( 'NOBLOGREDIRECT', '' );
// On a directory based install you can use the 404 handler.

// Location of mu-plugins
// define( 'WPMU_PLUGIN_DIR', '' );
// define( 'WPMU_PLUGIN_URL', '' );
// define( 'MUPLUGINDIR', 'wp-content/mu-plugins' );

// Uncomment to disable the site admin bar
//define( 'NOADMINBAR', 1 );

define( "WP_USE_MULTIPLE_DB", false );

/* That's all, stop editing! Happy blogging. */

/** WordPress absolute path to the Wordpress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
?>

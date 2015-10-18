<?php

// Setup edhost var for full url in code
$edhost = $_SERVER['HTTP_HOST'];

// Determine instance for dev or prod environments
$instance = (preg_match("~local~i", $edhost)) ? 'dev' : 'prod';

// Set up wordpress url overrides
define('WP_HOME', "http://{$edhost}");
define('WP_SITEURL', "http://{$edhost}");

/**
* The base configurations of the WordPress.
*
* This file has the following configurations: MySQL settings, Table Prefix,
* Secret Keys, and ABSPATH. You can find more information by visiting
* {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
* Codex page. You can get the MySQL settings from your web host.
*
* This file is used by the wp-config.php creation script during the
* installation. You don't have to use the web site, you can just copy this file
* to "wp-config.php" and fill in the values.
*
* @package WordPress
*/

// ** MySQL settings - You can get this info from your web host ** //
switch($instance) {
  case 'prod':
  	define('DB_HOST', 'alpha-db.cxr94xqomzr6.us-east-1.rds.amazonaws.com');
  	define('DB_USER', 'root');
  	define('DB_PASSWORD', 'bach3726');
  	define('DB_NAME', 'eardish');
	break;
  case 'dev':
  	define('DB_HOST', '127.0.0.1');
  	define('DB_USER', 'root');
  	define('DB_PASSWORD', 'fuckit');
  	define('DB_NAME', 'eardish');
	break;
}

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
* Authentication Unique Keys and Salts.
*
* Change these to different unique phrases!
* You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
* You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
*
* @since 2.6.0
*/
define('AUTH_KEY',         'K1v^~Vjb`:;7$Zq(#pDG<gIERRDd3]o|6ehO/T}$]ArROhv|< JR*3hhF!)^?XeP');
define('SECURE_AUTH_KEY',  'co2po[JTca+@51qXU>Y+C bClW0z{.0Y_/;+61<Sf+j.h-<QyC43bD!?2^Gk6GOM');
define('LOGGED_IN_KEY',    '7QjC2L*NJhCzoSucp7Z4E*buOf}-sw<4`QW)I_)W_~1teLw]U02u[c+RIIJs-r 0');
define('NONCE_KEY',        'DCthp#6=XYfc][%:$5R=3Q&YS|FSz!LQS?qyD%t8+.=>|>a+C-Gl)L3eQ;&ac|9x');
define('AUTH_SALT',        ')0<Tg=WVt1V+Aecwu2x}x+4=;}Xu:fo>&qnjVDhI@$Z-=++RxuC.Y=u*j#g]*(i?');
define('SECURE_AUTH_SALT', ' <zV> +<)uJX;8]5~uQ+z<b*[6cGnNya*0@|wj5?Wfc8Ofz4x+]Mo6d=AC&,e2Xq');
define('LOGGED_IN_SALT',   '8TZdGOx0mu(),}p;YG?/+[<&}x*MC%Hwq?|?gK9-%a$zmEsnqbpt$La)M(]9zD[[');
define('NONCE_SALT',       'j+ !-zqj.58T5j,SwH)[QTZyR+9Tq qtm[YU[*j9}:@}g+-K+4^H/Dv?em$^r0]J');

/**#@-*/

/**
* WordPress Database Table prefix.
*
* You can have multiple installations in one database if you give each a unique
* prefix. Only numbers, letters, and underscores please!
*/
$table_prefix  = 'wp_';

/**
* For developers: WordPress debugging mode.
*
* Change this to true to enable the display of notices during development.
* It is strongly recommended that plugin and theme developers use WP_DEBUG
* in their development environments.
*/
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');


<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'yogaspor_wordpress');

/** MySQL database username */
define('DB_USER', 'yogaspor_wordpre');

/** MySQL database password */
define('DB_PASSWORD', 'IGXSjV4M2s9bN6koQ');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         'zJ(S7Sj`488r%  FB2@e<~g`77p|`r##]irk1tykcnl(,NtN<Q0X8uK+t/SJ&R{;');
define('SECURE_AUTH_KEY',  '*?;Hu{~^ ;NCAEBUK9(C!Vof<~iyL|5p6P6vB,)s~d@sR!(gZ9J%Z8)7^pB<EDw<');
define('LOGGED_IN_KEY',    'hTXzkLW <+.pe3R:}A[Z+1M?-#L<-Gss(ojT|EBaB?)twGFD|nh(]}BH:D^p=oF2');
define('NONCE_KEY',        '=3Wn|:m.rPB/zU2fRagvNx/NUH-5/7[0Cf4D-h1R1]gXs|&w(+Td.sAHWVS?ItL?');
define('AUTH_SALT',        'ig||# .Hj.1u?Pxn0?TJ71^kxE8-b)vVMADH*cv9?=h77o+2)<>v)z@;@`T(y?>{');
define('SECURE_AUTH_SALT', 'b]i[<gC(;q?4;?%)CJ)yU2cB|Bf[2&P,O-$-/:dgt-G}_/PtA-wA{ <ouUhSBbx:');
define('LOGGED_IN_SALT',   '!WC{@]p~puZ9+|Uhu*St+<)EKtc4PdIMv`.H>S}=8Mu&bd/o;%Cf&lmpi_l1S`&@');
define('NONCE_SALT',       '[ek<kLxH!WQx0Cm%Fv0xT|3LA&XC|pS>#%fnD(.tK`svlPNn8kk-M; F9f&%1V[4');

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
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

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

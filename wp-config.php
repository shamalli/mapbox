<?php
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
define('DB_NAME', 'wordpress_mapbox');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         'LIJ00#[;jWj,QZFb-TJ?k[e1c`Z}qsaRhVTyLxr`#=`ON-{A|O]@Z2fBQBuD?ZX)');
define('SECURE_AUTH_KEY',  'T9;M:XoKC NGqGm/rq,mjweSz9i)gCwKfemH[68Cvezy,1;bUzO8{.6FscK2_r@J');
define('LOGGED_IN_KEY',    'mt qg]cO.A*~M4kH$phUifLLSO{kH%PbMFbzH*`e&2%EQY0*L[l},@kO9~PAH_EW');
define('NONCE_KEY',        'p;v(/oV43n3p$?XhC!oFEMB$xJ^=(@{N/!-d-tlrjH2+t?.gy&Q~cN6P:H,`^J?Z');
define('AUTH_SALT',        'NC*G|#z*}gmb5ta,% Qp_M>mM-19K3?[8kF- BsR3@O AH!0W3a<D}V5C&Yx2@mI');
define('SECURE_AUTH_SALT', 'csCk?r$3z9@n_z|zfs-NQ)l4eI,$]PaUq6aB;Rx0ad)}-gUAyUkV=R/#P`LRY+_8');
define('LOGGED_IN_SALT',   ',25]2>sA<+pN*/;(HNl4a.0_Wc<^ig!H8m_HWZqnOu%Mk#!:&I&w=EAXn|m$9cF>');
define('NONCE_SALT',       'OFfq|6R`JV(tL~D-T&65ABr> V 2O:elpS ELcc>M_Hc/CS]A_1V3n]l6*o>Y^<,');

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
define('WP_DEBUG', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

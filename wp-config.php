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
define('DB_NAME', 'noamo_adamamedcomwp');

/** MySQL database username */
define('DB_USER', 'noamodb');

/** MySQL database password */
define('DB_PASSWORD', 'aULE7HsTX');

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
define('AUTH_KEY',         '7r-+0377R:Ofzw+F@%)&]S%ncJJYGOU]to$1|ID^F<G`Vc,n9,OX*P%tI,#(kiJ>');
define('SECURE_AUTH_KEY',  'aV$X9(R%cX>CIt`Wd~37~A$3_[B]mn{T)`o-Ee~>y]YkUFQmMB!qm.o<VT|0ElS5');
define('LOGGED_IN_KEY',    '>|K,jA2O^wCybx ?Jhg6we Bj1nH6|zVVE8iI@-wqMZ;<zE7l+%N,24u(2GMB$mE');
define('NONCE_KEY',        'eoE+:][_c~+0W8Ku65=jL}[ ;>o7D#6=z+k>:R{ZD,L4]=o1>7u(mrA+p.JSn+{`');
define('AUTH_SALT',        'w9`~-?*v1J M6~gzQ+)={S elf%no:<[7+Cs4lE+eic+U%#67ULc9#;xa#fiT83w');
define('SECURE_AUTH_SALT', 'h|b&n)Q(l_BrYtUSFW/.m?yku.hvV2OY]8P_W$STc(AY@FlE~5CG/Iod?z!:R-+H');
define('LOGGED_IN_SALT',   '] H1k(nn|fH)I1Jr^lqDslmeDm=<O7oAA9[RA3|6u+=M^r<X+BZ26.2+!<FGA<]K');
define('NONCE_SALT',       'Zu/`-)F[z*=MvPSTuH&+&`!3QUvJYH_bHo60k.Py4jmW]pA%+5=zD;7[ B`j&EH>');

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
define('WP_DEBUG', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
  define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
<?php
/**
 * WordPress configuration for production deployment (Dokploy).
 *
 * All sensitive values are read from environment variables.
 *
 * @package TheDigitalFront
 */

// ─── Database ────────────────────────────────────────────────────
define( 'DB_NAME',     getenv( 'DB_NAME' )     ?: 'wordpress' );
define( 'DB_USER',     getenv( 'DB_USER' )     ?: 'wordpress' );
define( 'DB_PASSWORD', getenv( 'DB_PASSWORD' ) ?: '' );
define( 'DB_HOST',     getenv( 'DB_HOST' )     ?: 'localhost' );
define( 'DB_CHARSET',  'utf8mb4' );
define( 'DB_COLLATE',  '' );

$table_prefix = 'wp_';

// ─── Authentication Keys & Salts ─────────────────────────────────
// Set these as env vars in Dokploy for production security.
define( 'AUTH_KEY',          getenv( 'AUTH_KEY' )          ?: 'put-your-unique-phrase-here' );
define( 'SECURE_AUTH_KEY',   getenv( 'SECURE_AUTH_KEY' )   ?: 'put-your-unique-phrase-here' );
define( 'LOGGED_IN_KEY',     getenv( 'LOGGED_IN_KEY' )     ?: 'put-your-unique-phrase-here' );
define( 'NONCE_KEY',         getenv( 'NONCE_KEY' )         ?: 'put-your-unique-phrase-here' );
define( 'AUTH_SALT',         getenv( 'AUTH_SALT' )         ?: 'put-your-unique-phrase-here' );
define( 'SECURE_AUTH_SALT',  getenv( 'SECURE_AUTH_SALT' )  ?: 'put-your-unique-phrase-here' );
define( 'LOGGED_IN_SALT',    getenv( 'LOGGED_IN_SALT' )    ?: 'put-your-unique-phrase-here' );
define( 'NONCE_SALT',        getenv( 'NONCE_SALT' )        ?: 'put-your-unique-phrase-here' );

// ─── Site URLs ───────────────────────────────────────────────────
$site_url = getenv( 'SITE_URL' ) ?: 'http://localhost';
define( 'WP_HOME',    $site_url );
define( 'WP_SITEURL', $site_url );

// ─── SSL / Reverse Proxy ────────────────────────────────────────
// Dokploy's Traefik proxy terminates TLS and forwards HTTP.
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
	$_SERVER['HTTPS'] = 'on';
}

// ─── Debug ───────────────────────────────────────────────────────
define( 'WP_DEBUG',         getenv( 'WP_DEBUG' ) === 'true' );
define( 'WP_DEBUG_LOG',     getenv( 'WP_DEBUG' ) === 'true' );
define( 'WP_DEBUG_DISPLAY', false );

// ─── File Editing ────────────────────────────────────────────────
define( 'DISALLOW_FILE_EDIT', true );

// ─── Absolute Path ───────────────────────────────────────────────
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';

<?php
/**
 * PHPUnit bootstrap file for WP Config
 *
 * @package BuiltNorth\WPConfig
 */

// Suppress PHP 8.4 deprecation warnings
error_reporting(E_ALL & ~E_DEPRECATED);

// Load Composer autoloader, falling back to the monorepo root's autoloader
// when this package has no standalone vendor/ install (the normal dev setup —
// see "Autoloader Architecture" in the root CLAUDE.md).
$autoloader             = dirname( __DIR__ ) . '/vendor/autoload.php';
$using_root_autoloader = ! file_exists( $autoloader );
if ( $using_root_autoloader ) {
	$autoloader = dirname( __DIR__, 3 ) . '/vendor/autoload.php';
}
require_once $autoloader;

// The root autoloader only carries this package's own runtime `autoload` PSR-4
// mapping, never a dependency's `autoload-dev` — register the Tests namespace
// by hand when running under the root autoloader.
if ( $using_root_autoloader ) {
	spl_autoload_register(
		static function ( string $class ): void {
			$prefix = 'BuiltNorth\\WPConfig\\Tests\\';
			if ( ! str_starts_with( $class, $prefix ) ) {
				return;
			}
			$relative = substr( $class, strlen( $prefix ) );
			$file     = __DIR__ . '/' . str_replace( '\\', '/', $relative ) . '.php';
			if ( file_exists( $file ) ) {
				require $file;
			}
		}
	);
}

// Define WordPress constants that might be used
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', '/tmp/wordpress/' );
}

if ( ! defined( 'WP_CONTENT_DIR' ) ) {
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
}

if ( ! defined( 'WP_CONTENT_URL' ) ) {
	define( 'WP_CONTENT_URL', 'http://example.org/wp-content' );
}

// Define test constants
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', true );
}

if ( ! defined( 'WP_DEBUG_LOG' ) ) {
	define( 'WP_DEBUG_LOG', true );
}

if ( ! defined( 'WP_DEBUG_DISPLAY' ) ) {
	define( 'WP_DEBUG_DISPLAY', false );
}
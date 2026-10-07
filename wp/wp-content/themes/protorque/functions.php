<?php
/**
 * ProTorque theme bootstrap.
 */
define( 'PT_VERSION', '0.2.0' );
require_once get_template_directory() . '/inc/nav.php';

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'style', 'script' ] );
	register_nav_menus( [
		'primary' => __( 'Primary navigation', 'protorque' ),
		'footer'  => __( 'Footer links', 'protorque' ),
	] );
} );

add_action( 'wp_enqueue_scripts', function () {
	$dir = get_template_directory_uri();
	wp_enqueue_style( 'pt-tokens', $dir . '/assets/css/tokens.css', [], PT_VERSION );
	wp_enqueue_style( 'pt-main', $dir . '/assets/css/main.css', [ 'pt-tokens' ], PT_VERSION );
	wp_enqueue_script( 'pt-nav', $dir . '/assets/js/nav.js', [], PT_VERSION, true );
	wp_enqueue_script( 'pt-main', $dir . '/assets/js/main.js', [], PT_VERSION, true );
} );

/* GTM container GTM-WJJMKJV: head and noscript snippets go in header.php once the build starts. */

if ( ! defined( 'PT_GTM_ID' ) ) {
	define( 'PT_GTM_ID', 'GTM-WJJMKJV' );
}


/* Lean head: no emoji script, oEmbed discovery, RSD or generator tags. */
add_action( 'init', function () {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
} );

<?php
/**
 * ProTorque theme bootstrap.
 */
define( 'PT_VERSION', '0.1.0' );

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
	wp_enqueue_script( 'pt-main', $dir . '/assets/js/main.js', [], PT_VERSION, true );
} );

/* GTM container GTM-WJJMKJV: head and noscript snippets go in header.php once the build starts. */

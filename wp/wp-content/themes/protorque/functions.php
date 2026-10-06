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

if ( ! defined( 'PT_GTM_ID' ) ) {
	define( 'PT_GTM_ID', 'GTM-WJJMKJV' );
}

/** Fallback primary nav until a menu is assigned to the location. */
function pt_primary_nav_fallback() {
	$home = home_url( '/' );
	echo '<ul class="pt-nav__list">'
		. '<li><a href="' . esc_url( $home . 'about/' ) . '">About</a></li>'
		. '<li class="menu-item-has-children"><a href="' . esc_url( $home . 'tubular-running-services/' ) . '">Services</a><ul class="sub-menu">'
		. '<li><a href="' . esc_url( $home . 'tubular-running-services/' ) . '">Tubular Running Services</a></li>'
		. '<li><a href="' . esc_url( $home . 'drilling-services/' ) . '">Drilling Services</a></li>'
		. '<li><a href="' . esc_url( $home . 'midstream-services/' ) . '">Midstream Services</a></li></ul></li>'
		. '<li class="menu-item-has-children"><a href="' . esc_url( $home . 'equipment-and-innovation/' ) . '">Equipment</a><ul class="sub-menu">'
		. '<li><a href="' . esc_url( $home . 'equipment-and-innovation/' ) . '">Equipment &amp; Innovation</a></li></ul></li>'
		. '<li><a href="' . esc_url( $home . 'news/' ) . '">News</a></li>'
		. '<li><a href="' . esc_url( $home . 'careers/' ) . '">Careers</a></li>'
		. '<li><a href="' . esc_url( $home . 'contact-us/' ) . '">Contact</a></li>'
		. '</ul>';
}

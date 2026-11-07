<?php
/**
 * Front-end performance and head clean-up.
 *
 * @package Contronic
 */

defined( 'ABSPATH' ) || exit;

// Emoji scripts/styles: modern browsers render emoji natively.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

// Head noise not needed by a corporate site.
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
add_filter( 'the_generator', '__return_empty_string' );

// oEmbed discovery for this site's own pages.
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

add_action(
	'wp_enqueue_scripts',
	static function () {
		// No classic theme needs the embed script on the front end.
		wp_deregister_script( 'wp-embed' );
		// Block library CSS only where block content is rendered.
		if ( is_front_page() || is_post_type_archive() || is_tax() ) {
			wp_dequeue_style( 'wp-block-library' );
			wp_dequeue_style( 'wp-block-library-theme' );
			wp_dequeue_style( 'classic-theme-styles' );
		}
	},
	100
);

// The hero image is the LCP element: never lazy-load the first content image on singular pages.
add_filter(
	'wp_omit_loading_attr_threshold',
	static function () {
		return 1;
	}
);

// Make uploaded images lighter by default.
add_filter(
	'wp_editor_set_quality',
	static function ( $quality, $mime ) {
		return 'image/jpeg' === $mime || 'image/webp' === $mime ? 80 : $quality;
	},
	10,
	2
);

// Generate WebP versions of uploaded JPEG/PNG where the server supports it.
add_filter(
	'image_editor_output_format',
	static function ( $formats ) {
		if ( wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) ) ) {
			$formats['image/jpeg'] = 'image/webp';
			$formats['image/png']  = 'image/webp';
		}
		return $formats;
	}
);

// Large uploads are scaled down to 2560px by WordPress already; cap at 2400 for this design.
add_filter(
	'big_image_size_threshold',
	static function () {
		return 2400;
	}
);

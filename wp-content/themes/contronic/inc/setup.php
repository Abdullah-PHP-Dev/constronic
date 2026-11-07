<?php
/**
 * Theme supports, menus, image sizes, assets.
 *
 * @package Contronic
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	static function () {
		load_theme_textdomain( 'contronic', CTR_THEME_DIR . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 120,
				'width'       => 480,
				'flex-height' => true,
				'flex-width'  => true,
			)
		);
		add_editor_style( 'assets/css/editor.css' );

		register_nav_menus(
			array(
				'primary' => __( 'Main navigation', 'contronic' ),
				'footer'  => __( 'Footer — quick links', 'contronic' ),
				'legal'   => __( 'Footer — legal links', 'contronic' ),
			)
		);

		add_image_size( 'ctr-hero', 1920, 1080, true );
		add_image_size( 'ctr-card', 800, 600, true );
		add_image_size( 'ctr-wide', 1280, 720, true );
		add_image_size( 'ctr-logo', 320, 160, false );

		// Editor palette matching the front end.
		add_theme_support(
			'editor-color-palette',
			array(
				array( 'name' => 'Ink', 'slug' => 'ink', 'color' => '#08131c' ),
				array( 'name' => 'Steel', 'slug' => 'steel', 'color' => '#14232f' ),
				array( 'name' => 'Signal', 'slug' => 'signal', 'color' => '#19b3a6' ),
				array( 'name' => 'Amber', 'slug' => 'amber', 'color' => '#f2a93b' ),
				array( 'name' => 'Paper', 'slug' => 'paper', 'color' => '#f3f5f6' ),
				array( 'name' => 'White', 'slug' => 'white', 'color' => '#ffffff' ),
			)
		);
	}
);

add_filter(
	'image_size_names_choose',
	static function ( $sizes ) {
		return array_merge(
			$sizes,
			array(
				'ctr-card' => 'Card (4:3)',
				'ctr-wide' => 'Wide (16:9)',
			)
		);
	}
);

// Main content width for embeds.
add_action(
	'after_setup_theme',
	static function () {
		$GLOBALS['content_width'] = 820;
	},
	0
);

add_action(
	'wp_enqueue_scripts',
	static function () {
		$css_ver = CTR_THEME_VERSION . '.' . filemtime( CTR_THEME_DIR . '/assets/css/main.css' );
		$js_ver  = CTR_THEME_VERSION . '.' . filemtime( CTR_THEME_DIR . '/assets/js/main.js' );

		wp_enqueue_style( 'contronic', CTR_THEME_URI . '/assets/css/main.css', array(), $css_ver );
		wp_add_inline_style( 'contronic', ctr_brand_css_vars() );

		wp_enqueue_script( 'contronic', CTR_THEME_URI . '/assets/js/main.js', array(), $js_ver, array( 'strategy' => 'defer', 'in_footer' => true ) );
		wp_localize_script(
			'contronic',
			'ctrTheme',
			array(
				'sending' => __( 'Sending…', 'contronic' ),
				'error'   => __( 'Sorry, the message could not be sent. Please try again or contact us directly.', 'contronic' ),
			)
		);

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
);

// Preload the primary fonts (latin subset) to avoid layout shift.
add_action(
	'wp_head',
	static function () {
		foreach ( array( 'archivo-latin.woff2', 'inter-latin.woff2' ) as $font ) {
			echo '<link rel="preload" href="' . esc_url( CTR_THEME_URI . '/assets/fonts/' . $font ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
		}
		echo '<meta name="theme-color" content="#08131c">' . "\n";
		// Enables reveal animations before first paint (no flash of hidden content without JS).
		echo "<script>document.documentElement.classList.add('js')</script>\n";
	},
	2
);

/**
 * Brand colour overrides from the Customizer.
 *
 * @return string
 */
function ctr_brand_css_vars() {
	$accent  = sanitize_hex_color( ctr_mod( 'brand_accent' ) );
	$accent2 = sanitize_hex_color( ctr_mod( 'brand_accent2' ) );
	$css     = ':root{';
	if ( $accent ) {
		$css .= '--c-accent:' . $accent . ';';
	}
	if ( $accent2 ) {
		$css .= '--c-accent-2:' . $accent2 . ';';
	}
	return $css . '}';
}

// Body classes for header behaviour.
add_filter(
	'body_class',
	static function ( $classes ) {
		$classes[] = 'has-overlay-header';
		if ( ctr_mod( 'topbar_show' ) ) {
			$classes[] = 'has-topbar';
		}
		return $classes;
	}
);

// Widgets are not used: the footer is driven by the Customizer and menus.
add_action(
	'widgets_init',
	static function () {
		register_sidebar(
			array(
				'name'          => __( 'Blog sidebar', 'contronic' ),
				'id'            => 'blog',
				'description'   => __( 'Shown on news articles.', 'contronic' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget__title">',
				'after_title'   => '</h2>',
			)
		);
	}
);

// Excerpt tweaks.
add_filter(
	'excerpt_length',
	static function () {
		return 24;
	}
);
add_filter(
	'excerpt_more',
	static function () {
		return '…';
	}
);

// Theme default share image for the SEO module.
add_filter(
	'ctr_default_share_image',
	static function () {
		return CTR_THEME_URI . '/assets/img/share-default.jpg';
	}
);

// Page-level fields (hero subtitle + gallery) via the Contronic Core field framework.
add_filter(
	'ctr_meta_groups',
	static function ( $groups ) {
		$groups['page'] = array(
			'title'  => 'Page banner & gallery',
			'fields' => array(
				array( 'key' => 'hero_kicker', 'label' => 'Banner small label', 'type' => 'text' ),
				array( 'key' => 'hero_span', 'label' => 'Banner heading — highlighted second line', 'type' => 'text' ),
				array( 'key' => 'hero_text', 'label' => 'Banner introduction', 'type' => 'textarea', 'rows' => 2, 'help' => 'The banner background is the page’s Featured image.' ),
				array( 'key' => 'gallery', 'label' => 'Gallery images', 'type' => 'gallery', 'help' => 'Used by the Gallery and About templates.' ),
			),
		);
		return $groups;
	}
);

// Favicon fallback until a Site Icon is uploaded (Customize → Site Identity).
add_action(
	'wp_head',
	static function () {
		if ( ! has_site_icon() ) {
			echo '<link rel="icon" href="' . esc_url( CTR_THEME_URI . '/assets/img/favicon.svg' ) . '" type="image/svg+xml">' . "\n";
		}
	},
	3
);

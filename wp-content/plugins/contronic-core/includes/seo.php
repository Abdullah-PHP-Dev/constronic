<?php
/**
 * Technical SEO: titles, meta descriptions, canonical, robots, Open Graph,
 * Twitter cards, JSON-LD schema, sitemap and robots.txt tuning.
 *
 * Automatically stands down if a dedicated SEO plugin (Yoast, Rank Math, SEOPress, AIOSEO) is active.
 *
 * @package ContronicCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Company profile (managed in Appearance → Customize → Company & contact).
 *
 * @return array
 */
function ctr_company() {
	$defaults = array(
		'name'      => get_bloginfo( 'name' ),
		'legal'     => '',
		'tagline'   => get_bloginfo( 'description' ),
		'phone'     => '',
		'phone2'    => '',
		'whatsapp'  => '',
		'email'     => '',
		'email2'    => '',
		'address'   => '',
		'city'      => '',
		'country'   => 'United Arab Emirates',
		'hours'     => '',
		'map_embed' => '',
		'map_link'  => '',
		'facebook'  => '',
		'instagram' => '',
		'linkedin'  => '',
		'x'         => '',
		'youtube'   => '',
		'tiktok'    => '',
	);
	return wp_parse_args( array_filter( (array) get_option( 'ctr_company', array() ), 'strlen' ), $defaults );
}

/**
 * SEO defaults (Customizer → SEO defaults).
 *
 * @return array
 */
function ctr_seo_options() {
	$defaults = array(
		'home_title'       => '',
		'home_description' => '',
		'og_image'         => 0,
		'business_type'    => 'GeneralContractor',
		'services_title'   => 'Services',
		'services_desc'    => '',
		'products_title'   => 'Products & Solutions',
		'products_desc'    => '',
		'projects_title'   => 'Projects',
		'projects_desc'    => '',
	);
	return wp_parse_args( array_filter( (array) get_option( 'ctr_seo', array() ), 'strlen' ), $defaults );
}

/**
 * Whether a third-party SEO plugin is handling output.
 *
 * @return bool
 */
function ctr_external_seo_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'AIOSEO_VERSION' );
}

/* -------------------------------------------------------------------------
 * Customizer: company profile + SEO defaults (stored as options).
 * ---------------------------------------------------------------------- */

add_action(
	'customize_register',
	static function ( $wp_customize ) {
		$wp_customize->add_section(
			'ctr_company',
			array(
				'title'       => 'Company & contact details',
				'priority'    => 25,
				'description' => 'Used across the header, footer, contact page, inquiry emails and search-engine schema. Leave a field empty to hide it.',
			)
		);
		$company = array(
			'name'      => array( 'Company name', 'text' ),
			'legal'     => array( 'Legal / trade-licence name', 'text' ),
			'tagline'   => array( 'Short tagline', 'text' ),
			'phone'     => array( 'Main phone', 'tel' ),
			'phone2'    => array( 'Second phone', 'tel' ),
			'whatsapp'  => array( 'WhatsApp number (international format, digits only, e.g. 9715XXXXXXXX)', 'text' ),
			'email'     => array( 'Main email', 'email' ),
			'email2'    => array( 'Second email (e.g. careers or sales)', 'email' ),
			'address'   => array( 'Street address', 'textarea' ),
			'city'      => array( 'City / emirate', 'text' ),
			'country'   => array( 'Country', 'text' ),
			'hours'     => array( 'Working hours (one line per day range)', 'textarea' ),
			'map_embed' => array( 'Google Maps embed URL (the src="…" of the embed code)', 'url' ),
			'map_link'  => array( 'Google Maps directions link', 'url' ),
			'facebook'  => array( 'Facebook URL', 'url' ),
			'instagram' => array( 'Instagram URL', 'url' ),
			'linkedin'  => array( 'LinkedIn URL', 'url' ),
			'x'         => array( 'X (Twitter) URL', 'url' ),
			'youtube'   => array( 'YouTube URL', 'url' ),
			'tiktok'    => array( 'TikTok URL', 'url' ),
		);
		foreach ( $company as $key => $def ) {
			$sanitize = 'sanitize_text_field';
			if ( 'url' === $def[1] ) {
				$sanitize = 'esc_url_raw';
			} elseif ( 'email' === $def[1] ) {
				$sanitize = 'sanitize_email';
			} elseif ( 'textarea' === $def[1] ) {
				$sanitize = 'sanitize_textarea_field';
			}
			if ( 'whatsapp' === $key ) {
				$sanitize = static function ( $v ) {
					return preg_replace( '/\D/', '', (string) $v );
				};
			}
			$wp_customize->add_setting(
				"ctr_company[{$key}]",
				array(
					'type'              => 'option',
					'capability'        => 'manage_options',
					'sanitize_callback' => $sanitize,
				)
			);
			$wp_customize->add_control(
				"ctr_company_{$key}",
				array(
					'label'    => $def[0],
					'section'  => 'ctr_company',
					'settings' => "ctr_company[{$key}]",
					'type'     => $def[1],
				)
			);
		}

		$wp_customize->add_section(
			'ctr_seo',
			array(
				'title'       => 'SEO defaults',
				'priority'    => 160,
				'description' => 'Per-page SEO is edited in the "SEO & sharing" box on each page, service, product, project and post.',
			)
		);
		$seo = array(
			'home_title'       => array( 'Homepage SEO title', 'text' ),
			'home_description' => array( 'Homepage meta description', 'textarea' ),
			'services_title'   => array( 'Services archive title', 'text' ),
			'services_desc'    => array( 'Services archive description', 'textarea' ),
			'products_title'   => array( 'Products archive title', 'text' ),
			'products_desc'    => array( 'Products archive description', 'textarea' ),
			'projects_title'   => array( 'Projects archive title', 'text' ),
			'projects_desc'    => array( 'Projects archive description', 'textarea' ),
		);
		foreach ( $seo as $key => $def ) {
			$wp_customize->add_setting(
				"ctr_seo[{$key}]",
				array(
					'type'              => 'option',
					'capability'        => 'manage_options',
					'sanitize_callback' => 'textarea' === $def[1] ? 'sanitize_textarea_field' : 'sanitize_text_field',
				)
			);
			$wp_customize->add_control( "ctr_seo_{$key}", array( 'label' => $def[0], 'section' => 'ctr_seo', 'settings' => "ctr_seo[{$key}]", 'type' => $def[1] ) );
		}
		$wp_customize->add_setting( 'ctr_seo[business_type]', array( 'type' => 'option', 'capability' => 'manage_options', 'default' => 'GeneralContractor', 'sanitize_callback' => 'sanitize_text_field' ) );
		$wp_customize->add_control(
			'ctr_seo_business_type',
			array(
				'label'    => 'Schema business type',
				'section'  => 'ctr_seo',
				'settings' => 'ctr_seo[business_type]',
				'type'     => 'select',
				'choices'  => array(
					'GeneralContractor'  => 'General contractor',
					'Electrician'        => 'Electrical contractor',
					'HVACBusiness'       => 'HVAC business',
					'Plumber'            => 'Plumbing contractor',
					'HomeAndConstructionBusiness' => 'Construction business (generic)',
					'Organization'       => 'Organization (generic)',
				),
			)
		);
		$wp_customize->add_setting( 'ctr_seo[og_image]', array( 'type' => 'option', 'capability' => 'manage_options', 'sanitize_callback' => 'absint' ) );
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'ctr_seo_og_image',
				array(
					'label'     => 'Default social share image (1200×630)',
					'section'   => 'ctr_seo',
					'settings'  => 'ctr_seo[og_image]',
					'mime_type' => 'image',
				)
			)
		);
	}
);

/* -------------------------------------------------------------------------
 * Resolution helpers.
 * ---------------------------------------------------------------------- */

/**
 * Archive context for CPT archives.
 *
 * @return string|null services|products|projects
 */
function ctr_archive_key() {
	$map = array(
		'ctr_service' => 'services',
		'ctr_product' => 'products',
		'ctr_project' => 'projects',
	);
	foreach ( $map as $type => $key ) {
		if ( is_post_type_archive( $type ) ) {
			return $key;
		}
	}
	return null;
}

/**
 * Meta description for the current request.
 *
 * @return string
 */
function ctr_current_description() {
	$opts = ctr_seo_options();
	$desc = '';
	if ( is_front_page() ) {
		$desc = $opts['home_description'] ? $opts['home_description'] : get_bloginfo( 'description' );
	} elseif ( is_singular() || ( is_home() && get_option( 'page_for_posts' ) ) ) {
		$post_id = is_home() ? (int) get_option( 'page_for_posts' ) : get_queried_object_id();
		$desc    = get_post_meta( $post_id, '_ctr_seo_description', true );
		if ( ! $desc ) {
			$post = get_post( $post_id );
			$desc = $post ? ( $post->post_excerpt ? $post->post_excerpt : $post->post_content ) : '';
		}
	} elseif ( ctr_archive_key() ) {
		$desc = $opts[ ctr_archive_key() . '_desc' ];
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$desc = term_description();
	}
	$desc = wp_strip_all_tags( strip_shortcodes( excerpt_remove_blocks( (string) $desc ) ) );
	$desc = trim( preg_replace( '/\s+/', ' ', $desc ) );
	return wp_html_excerpt( $desc, 160, mb_strlen( $desc ) > 160 ? '…' : '' );
}

/**
 * Canonical URL for the current request.
 *
 * @return string
 */
function ctr_current_canonical() {
	if ( is_singular() ) {
		$custom = get_post_meta( get_queried_object_id(), '_ctr_seo_canonical', true );
		return $custom ? $custom : wp_get_canonical_url();
	}
	if ( is_front_page() ) {
		return home_url( '/' );
	}
	if ( is_home() && get_option( 'page_for_posts' ) ) {
		$url = get_permalink( (int) get_option( 'page_for_posts' ) );
	} elseif ( ctr_archive_key() ) {
		$url = get_post_type_archive_link( get_query_var( 'post_type' ) );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$url = get_term_link( get_queried_object() );
	} else {
		return '';
	}
	$paged = (int) get_query_var( 'paged' );
	if ( $paged > 1 && ! is_wp_error( $url ) ) {
		$url = trailingslashit( $url ) . user_trailingslashit( 'page/' . $paged, 'paged' );
	}
	return is_wp_error( $url ) ? '' : $url;
}

/**
 * Share image URL + dimensions for current request.
 *
 * @return array|null [url, width, height, alt]
 */
function ctr_current_share_image() {
	$id = 0;
	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		$id      = (int) get_post_meta( $post_id, '_ctr_seo_og_image', true );
		if ( ! $id ) {
			$id = (int) get_post_thumbnail_id( $post_id );
		}
	}
	if ( ! $id ) {
		$id = (int) ctr_seo_options()['og_image'];
	}
	if ( ! $id ) {
		$logo = (int) get_theme_mod( 'custom_logo' );
		$id   = $logo;
	}
	if ( ! $id ) {
		$fallback = apply_filters( 'ctr_default_share_image', '' );
		return $fallback ? array( $fallback, 1200, 630, get_bloginfo( 'name' ) ) : null;
	}
	$src = wp_get_attachment_image_src( $id, 'large' );
	if ( ! $src ) {
		return null;
	}
	$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
	return array( $src[0], $src[1], $src[2], $alt ? $alt : get_bloginfo( 'name' ) );
}

/* -------------------------------------------------------------------------
 * Head output.
 * ---------------------------------------------------------------------- */

add_action(
	'wp',
	static function () {
		if ( ctr_external_seo_active() ) {
			return;
		}
		remove_action( 'wp_head', 'rel_canonical' );
		add_action( 'wp_head', 'ctr_output_head_meta', 1 );
		add_action( 'wp_head', 'ctr_output_schema', 30 );
	}
);

// Titles.
add_filter(
	'pre_get_document_title',
	static function ( $title ) {
		if ( ctr_external_seo_active() ) {
			return $title;
		}
		$opts = ctr_seo_options();
		if ( is_front_page() && $opts['home_title'] ) {
			return $opts['home_title'];
		}
		if ( is_singular() ) {
			$custom = get_post_meta( get_queried_object_id(), '_ctr_seo_title', true );
			if ( $custom ) {
				return $custom;
			}
		}
		$key = ctr_archive_key();
		if ( $key && $opts[ $key . '_title' ] ) {
			return $opts[ $key . '_title' ] . ' | ' . get_bloginfo( 'name' );
		}
		return $title;
	}
);

add_filter(
	'document_title_separator',
	static function () {
		return '|';
	}
);

// Robots meta.
add_filter(
	'wp_robots',
	static function ( $robots ) {
		if ( ctr_external_seo_active() ) {
			return $robots;
		}
		if ( ( is_singular() && get_post_meta( get_queried_object_id(), '_ctr_seo_noindex', true ) ) || is_search() || is_404() ) {
			$robots['noindex'] = true;
			$robots['follow']  = true;
		} else {
			$robots['max-image-preview'] = 'large';
		}
		return $robots;
	}
);

/**
 * Meta description, canonical, Open Graph, Twitter.
 */
function ctr_output_head_meta() {
	$desc      = ctr_current_description();
	$canonical = ctr_current_canonical();
	$title     = wp_get_document_title();
	$image     = ctr_current_share_image();
	$type      = is_singular( 'post' ) ? 'article' : 'website';

	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	if ( $canonical ) {
		echo '<link rel="canonical" href="' . esc_url( $canonical ) . '">' . "\n";
	}
	$og = array(
		'og:locale'      => str_replace( '-', '_', get_bloginfo( 'language' ) ),
		'og:type'        => $type,
		'og:site_name'   => ctr_company()['name'],
		'og:title'       => $title,
		'og:description' => $desc,
		'og:url'         => $canonical ? $canonical : home_url( add_query_arg( array() ) ),
	);
	if ( $image ) {
		$og['og:image']        = $image[0];
		$og['og:image:width']  = $image[1];
		$og['og:image:height'] = $image[2];
		$og['og:image:alt']    = $image[3];
	}
	if ( 'article' === $type ) {
		$og['article:published_time'] = get_the_date( 'c', get_queried_object_id() );
		$og['article:modified_time']  = get_the_modified_date( 'c', get_queried_object_id() );
	}
	foreach ( $og as $property => $content ) {
		if ( '' !== (string) $content ) {
			echo '<meta property="' . esc_attr( $property ) . '" content="' . esc_attr( $content ) . '">' . "\n";
		}
	}
	echo '<meta name="twitter:card" content="' . ( $image ? 'summary_large_image' : 'summary' ) . '">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $desc ) {
		echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	if ( $image ) {
		echo '<meta name="twitter:image" content="' . esc_url( $image[0] ) . '">' . "\n";
	}
}

/**
 * Organization / LocalBusiness node.
 *
 * @return array
 */
function ctr_schema_organization() {
	$c    = ctr_company();
	$node = array(
		'@type' => ctr_seo_options()['business_type'],
		'@id'   => home_url( '/#organization' ),
		'name'  => $c['name'],
		'url'   => home_url( '/' ),
	);
	if ( $c['legal'] ) {
		$node['legalName'] = $c['legal'];
	}
	if ( $c['tagline'] ) {
		$node['description'] = $c['tagline'];
	}
	$logo = (int) get_theme_mod( 'custom_logo' );
	if ( $logo && wp_get_attachment_image_url( $logo, 'full' ) ) {
		$node['logo'] = wp_get_attachment_image_url( $logo, 'full' );
	}
	if ( $c['phone'] ) {
		$node['telephone'] = $c['phone'];
	}
	if ( $c['email'] ) {
		$node['email'] = $c['email'];
	}
	if ( $c['address'] || $c['city'] ) {
		$node['address'] = array_filter(
			array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => preg_replace( '/\s+/', ' ', $c['address'] ),
				'addressLocality' => $c['city'],
				'addressCountry'  => 'United Arab Emirates' === $c['country'] ? 'AE' : $c['country'],
			)
		);
	}
	if ( 'Organization' !== $node['@type'] && $c['city'] ) {
		$node['areaServed'] = $c['country'];
	}
	$same = array_values( array_filter( array( $c['facebook'], $c['instagram'], $c['linkedin'], $c['x'], $c['youtube'], $c['tiktok'] ) ) );
	if ( $same ) {
		$node['sameAs'] = $same;
	}
	return $node;
}

/**
 * Breadcrumb trail for the current page: list of [name, url].
 *
 * @return array
 */
function ctr_breadcrumb_trail() {
	$trail = array( array( 'Home', home_url( '/' ) ) );
	if ( is_front_page() ) {
		return $trail;
	}
	$archives = array(
		'ctr_service' => 'services',
		'ctr_product' => 'products',
		'ctr_project' => 'projects',
	);
	$opts = ctr_seo_options();
	if ( is_singular( array_keys( $archives ) ) ) {
		$type    = get_post_type();
		$trail[] = array( $opts[ $archives[ $type ] . '_title' ], get_post_type_archive_link( $type ) );
		$trail[] = array( get_the_title(), get_permalink() );
	} elseif ( is_singular( 'post' ) ) {
		$blog = (int) get_option( 'page_for_posts' );
		if ( $blog ) {
			$trail[] = array( get_the_title( $blog ), get_permalink( $blog ) );
		}
		$trail[] = array( get_the_title(), get_permalink() );
	} elseif ( is_page() ) {
		foreach ( array_reverse( get_post_ancestors( get_queried_object_id() ) ) as $ancestor ) {
			$trail[] = array( get_the_title( $ancestor ), get_permalink( $ancestor ) );
		}
		$trail[] = array( get_the_title(), get_permalink() );
	} elseif ( ctr_archive_key() ) {
		$trail[] = array( $opts[ ctr_archive_key() . '_title' ], get_post_type_archive_link( get_query_var( 'post_type' ) ) );
	} elseif ( is_home() ) {
		$trail[] = array( single_post_title( '', false ), ctr_current_canonical() );
	} elseif ( is_tax() || is_category() || is_tag() ) {
		$term = get_queried_object();
		$tax  = get_taxonomy( $term->taxonomy );
		if ( $tax && ! empty( $tax->object_type[0] ) && isset( $archives[ $tax->object_type[0] ] ) ) {
			$trail[] = array( $opts[ $archives[ $tax->object_type[0] ] . '_title' ], get_post_type_archive_link( $tax->object_type[0] ) );
		}
		$trail[] = array( $term->name, get_term_link( $term ) );
	} elseif ( is_search() ) {
		$trail[] = array( 'Search results', '' );
	} elseif ( is_404() ) {
		$trail[] = array( 'Page not found', '' );
	}
	return $trail;
}

/**
 * JSON-LD graph.
 */
function ctr_output_schema() {
	$graph   = array();
	$graph[] = ctr_schema_organization();

	if ( is_front_page() ) {
		$graph[] = array(
			'@type'     => 'WebSite',
			'@id'       => home_url( '/#website' ),
			'url'       => home_url( '/' ),
			'name'      => ctr_company()['name'],
			'publisher' => array( '@id' => home_url( '/#organization' ) ),
		);
	} else {
		$items = array();
		foreach ( ctr_breadcrumb_trail() as $i => $crumb ) {
			$item = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => wp_strip_all_tags( $crumb[0] ),
			);
			if ( $crumb[1] ) {
				$item['item'] = $crumb[1];
			}
			$items[] = $item;
		}
		$graph[] = array( '@type' => 'BreadcrumbList', 'itemListElement' => $items );
	}

	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		$image   = get_the_post_thumbnail_url( $post_id, 'large' );
		$desc    = ctr_current_description();
		if ( is_singular( 'ctr_service' ) ) {
			$graph[] = array_filter(
				array(
					'@type'       => 'Service',
					'name'        => get_the_title( $post_id ),
					'description' => $desc,
					'url'         => get_permalink( $post_id ),
					'image'       => $image,
					'provider'    => array( '@id' => home_url( '/#organization' ) ),
					'areaServed'  => ctr_company()['country'],
				)
			);
		} elseif ( is_singular( 'ctr_product' ) ) {
			$graph[] = array_filter(
				array(
					'@type'       => 'Product',
					'name'        => get_the_title( $post_id ),
					'description' => $desc,
					'url'         => get_permalink( $post_id ),
					'image'       => $image,
					'brand'       => ctr_meta( 'brand', $post_id ) ? array( '@type' => 'Brand', 'name' => ctr_meta( 'brand', $post_id ) ) : null,
					'model'       => ctr_meta( 'model', $post_id ),
				)
			);
		} elseif ( is_singular( 'post' ) ) {
			$graph[] = array_filter(
				array(
					'@type'         => 'Article',
					'headline'      => get_the_title( $post_id ),
					'description'   => $desc,
					'image'         => $image,
					'datePublished' => get_the_date( 'c', $post_id ),
					'dateModified'  => get_the_modified_date( 'c', $post_id ),
					'author'        => array( '@type' => 'Organization', 'name' => ctr_company()['name'] ),
					'publisher'     => array( '@id' => home_url( '/#organization' ) ),
					'mainEntityOfPage' => get_permalink( $post_id ),
				)
			);
		}
	}

	$json = wp_json_encode(
		array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		),
		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
	);
	echo '<script type="application/ld+json">' . str_replace( '</', '<\/', $json ) . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON encoded.
}

/* -------------------------------------------------------------------------
 * Sitemap + robots.txt.
 * ---------------------------------------------------------------------- */

// Drop the users sitemap (author pages are not useful for a corporate site).
add_filter(
	'wp_sitemaps_add_provider',
	static function ( $provider, $name ) {
		return 'users' === $name ? false : $provider;
	},
	10,
	2
);

// Exclude noindex content.
add_filter(
	'wp_sitemaps_posts_query_args',
	static function ( $args ) {
		$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
			array(
				'key'     => '_ctr_seo_noindex',
				'compare' => 'NOT EXISTS',
			),
		);
		return $args;
	}
);

// Only taxonomies that are actually useful.
add_filter(
	'wp_sitemaps_taxonomies',
	static function ( $taxonomies ) {
		unset( $taxonomies['post_tag'], $taxonomies['post_format'] );
		return $taxonomies;
	}
);

add_filter(
	'robots_txt',
	static function ( $output, $public ) {
		if ( ! $public ) {
			return $output;
		}
		$extra = "Disallow: /?s=\nDisallow: /search/\n";
		return preg_replace( '#(Allow: /wp-admin/admin-ajax.php\n)#', '$1' . $extra, $output, 1 );
	},
	10,
	2
);

// Images without alt text fall back to their title — better than nothing for accessibility.
add_filter(
	'wp_get_attachment_image_attributes',
	static function ( $attr, $attachment ) {
		if ( empty( $attr['alt'] ) && ! isset( $attr['aria-hidden'] ) ) {
			$attr['alt'] = trim( wp_strip_all_tags( get_the_title( $attachment ) ) );
		}
		return $attr;
	},
	10,
	2
);

// Core only registers sitemap rewrite rules while the site is public, so refresh them when
// "Search engine visibility" changes — otherwise /wp-sitemap.xml 404s until permalinks are re-saved.
add_action(
	'update_option_blog_public',
	static function () {
		update_option( 'rewrite_rules', '' );
	}
);

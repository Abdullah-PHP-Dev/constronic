<?php
/**
 * Navigation helpers.
 *
 * Menu items pointing at sections that have nothing published yet (no projects,
 * no verified partners, no gallery images, no news posts) are hidden automatically,
 * so the site never links to an empty page. They appear as soon as content is published.
 *
 * @package Contronic
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether a section has public content.
 *
 * @param string $kind post type or page template.
 * @return bool
 */
function ctr_section_has_content( $kind ) {
	static $cache = array();
	if ( isset( $cache[ $kind ] ) ) {
		return $cache[ $kind ];
	}
	switch ( $kind ) {
		case 'page-templates/template-partners.php':
			$has = (bool) ctr_verified_items( 'ctr_partner', 1 );
			break;
		case 'page-templates/template-gallery.php':
			$has = (bool) ctr_gallery_ids( 1 );
			break;
		case 'blog':
			$has = (bool) get_posts( array( 'post_type' => 'post', 'posts_per_page' => 1, 'fields' => 'ids' ) );
			break;
		default:
			$has = post_type_exists( $kind ) ? (bool) get_posts( array( 'post_type' => $kind, 'posts_per_page' => 1, 'fields' => 'ids' ) ) : true;
	}
	$cache[ $kind ] = $has;
	return $has;
}

/**
 * All gallery image IDs: images in the Gallery page content + published project galleries.
 *
 * @param int $limit Max (-1 for all).
 * @return int[]
 */
function ctr_gallery_ids( $limit = -1 ) {
	$ids  = array();
	$page = get_pages(
		array(
			'meta_key'   => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value' => 'page-templates/template-gallery.php', // phpcs:ignore WordPress.DB.SlowDBQuery
			'number'     => 1,
		)
	);
	if ( $page ) {
		$ids = array_merge( $ids, ctr_ids( get_post_meta( $page[0]->ID, '_ctr_gallery', true ) ) );
		if ( preg_match_all( '/wp-image-(\d+)/', $page[0]->post_content, $m ) ) {
			$ids = array_merge( $ids, array_map( 'intval', $m[1] ) );
		}
		if ( preg_match_all( '/\[gallery[^\]]*ids="([\d,\s]+)"/', $page[0]->post_content, $m ) ) {
			foreach ( $m[1] as $list ) {
				$ids = array_merge( $ids, ctr_ids( $list ) );
			}
		}
	}
	foreach ( ctr_items( 'ctr_project' ) as $project ) {
		$ids = array_merge( $ids, ctr_ids( get_post_meta( $project->ID, '_ctr_gallery', true ) ) );
		if ( has_post_thumbnail( $project ) ) {
			$ids[] = (int) get_post_thumbnail_id( $project );
		}
	}
	$ids = array_values( array_unique( array_filter( $ids ) ) );
	return $limit > 0 ? array_slice( $ids, 0, $limit ) : $ids;
}

add_filter(
	'wp_nav_menu_objects',
	static function ( $items ) {
		$blog_page = (int) get_option( 'page_for_posts' );
		$removed   = array();
		foreach ( $items as $i => $item ) {
			$hide = false;
			if ( 'post_type_archive' === $item->type ) {
				$hide = ! ctr_section_has_content( $item->object );
			} elseif ( 'post_type' === $item->type && 'page' === $item->object ) {
				$page_id = (int) $item->object_id;
				if ( $page_id === $blog_page ) {
					$hide = ! ctr_section_has_content( 'blog' );
				} else {
					$template = get_page_template_slug( $page_id );
					if ( in_array( $template, array( 'page-templates/template-partners.php', 'page-templates/template-gallery.php' ), true ) ) {
						$hide = ! ctr_section_has_content( $template );
					}
				}
			}
			if ( $hide ) {
				$removed[] = (int) $item->ID;
				unset( $items[ $i ] );
			}
		}
		// Drop children of removed parents.
		if ( $removed ) {
			foreach ( $items as $i => $item ) {
				if ( in_array( (int) $item->menu_item_parent, $removed, true ) ) {
					unset( $items[ $i ] );
				}
			}
		}
		return $items;
	}
);

// Mark CPT archive items as current on their single pages.
add_filter(
	'nav_menu_css_class',
	static function ( $classes, $item ) {
		if ( 'post_type_archive' === $item->type && ( is_singular( $item->object ) || ( is_tax() && in_array( $item->object, get_taxonomy( get_queried_object()->taxonomy )->object_type, true ) ) ) ) {
			$classes[] = 'current-menu-ancestor';
		}
		return $classes;
	},
	10,
	2
);

add_filter(
	'nav_menu_link_attributes',
	static function ( $atts, $item ) {
		if ( in_array( 'current-menu-item', (array) $item->classes, true ) || in_array( 'current-menu-ancestor', (array) $item->classes, true ) ) {
			$atts['aria-current'] = in_array( 'current-menu-item', (array) $item->classes, true ) ? 'page' : 'true';
		}
		return $atts;
	},
	10,
	2
);

/**
 * Fallback navigation when no menu is assigned.
 *
 * @param array $args Menu args.
 */
function ctr_menu_fallback( $args = array() ) {
	$links = array();
	foreach ( array( 'about', 'contact' ) as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			$links[ $slug ] = array( get_permalink( $page ), get_the_title( $page ) );
		}
	}
	$types = array( 'ctr_service', 'ctr_project', 'ctr_product' );
	$out   = array();
	if ( isset( $links['about'] ) ) {
		$out[] = $links['about'];
	}
	foreach ( $types as $type ) {
		if ( post_type_exists( $type ) && ctr_section_has_content( $type ) ) {
			$out[] = array( get_post_type_archive_link( $type ), get_post_type_object( $type )->labels->name );
		}
	}
	if ( isset( $links['contact'] ) ) {
		$out[] = $links['contact'];
	}
	$class = ! empty( $args['menu_class'] ) ? $args['menu_class'] : 'menu';
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $out as $link ) {
		echo '<li class="menu-item"><a href="' . esc_url( $link[0] ) . '">' . esc_html( $link[1] ) . '</a></li>';
	}
	echo '</ul>';
}

/**
 * Pages whose section has nothing published yet (empty Partners, Gallery, News).
 *
 * @return int[]
 */
function ctr_empty_section_page_ids() {
	$ids  = array();
	$blog = (int) get_option( 'page_for_posts' );
	if ( $blog && ! ctr_section_has_content( 'blog' ) ) {
		$ids[] = $blog;
	}
	foreach ( array( 'page-templates/template-partners.php', 'page-templates/template-gallery.php' ) as $template ) {
		if ( ctr_section_has_content( $template ) ) {
			continue;
		}
		$pages = get_pages(
			array(
				'meta_key'   => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value' => $template, // phpcs:ignore WordPress.DB.SlowDBQuery
			)
		);
		$ids   = array_merge( $ids, wp_list_pluck( $pages, 'ID' ) );
	}
	return array_map( 'intval', $ids );
}

// Keep empty sections out of search engines until they have content.
add_filter(
	'wp_sitemaps_posts_query_args',
	static function ( $args, $post_type ) {
		if ( 'page' === $post_type ) {
			$args['post__not_in'] = array_merge( isset( $args['post__not_in'] ) ? (array) $args['post__not_in'] : array(), ctr_empty_section_page_ids() );
		}
		return $args;
	},
	20,
	2
);
add_filter(
	'wp_robots',
	static function ( $robots ) {
		$id = is_home() ? (int) get_option( 'page_for_posts' ) : ( is_page() ? get_queried_object_id() : 0 );
		if ( $id && in_array( $id, ctr_empty_section_page_ids(), true ) ) {
			$robots['noindex'] = true;
			$robots['follow']  = true;
			unset( $robots['max-image-preview'] );
		}
		return $robots;
	},
	20
);

// CPT archives with nothing published: noindex, and drop from the sitemap.
add_filter(
	'wp_robots',
	static function ( $robots ) {
		if ( is_post_type_archive() && ! have_posts() ) {
			$robots['noindex'] = true;
			unset( $robots['max-image-preview'] );
		}
		return $robots;
	},
	20
);

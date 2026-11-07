<?php
/**
 * Custom post types and taxonomies.
 *
 * @package ContronicCore
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'ctr_register_post_types', 5 );
add_action( 'init', 'ctr_register_taxonomies', 5 );

/**
 * Build a full label set.
 *
 * @param string $singular Singular.
 * @param string $plural   Plural.
 * @return array
 */
function ctr_labels( $singular, $plural ) {
	return array(
		'name'                  => $plural,
		'singular_name'         => $singular,
		'menu_name'             => $plural,
		'add_new'               => 'Add New',
		'add_new_item'          => "Add New {$singular}",
		'edit_item'             => "Edit {$singular}",
		'new_item'              => "New {$singular}",
		'view_item'             => "View {$singular}",
		'view_items'            => "View {$plural}",
		'search_items'          => "Search {$plural}",
		'not_found'             => "No {$plural} found.",
		'not_found_in_trash'    => "No {$plural} found in Trash.",
		'all_items'             => "All {$plural}",
		'archives'              => "{$singular} Archives",
		'featured_image'        => "{$singular} image",
		'set_featured_image'    => "Set {$singular} image",
		'remove_featured_image' => "Remove {$singular} image",
		'use_featured_image'    => "Use as {$singular} image",
		'items_list'            => "{$plural} list",
	);
}

/**
 * Register post types.
 */
function ctr_register_post_types() {
	$common = array(
		'public'       => true,
		'show_in_rest' => true,
		'has_archive'  => true,
		'rewrite'      => array( 'with_front' => false ),
	);

	register_post_type(
		'ctr_service',
		array_merge(
			$common,
			array(
				'labels'        => ctr_labels( 'Service', 'Services' ),
				'menu_icon'     => 'dashicons-hammer',
				'menu_position' => 20,
				'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
				'rewrite'       => array( 'slug' => 'services', 'with_front' => false ),
				'has_archive'   => 'services',
			)
		)
	);

	register_post_type(
		'ctr_product',
		array_merge(
			$common,
			array(
				'labels'        => ctr_labels( 'Product', 'Products & Solutions' ),
				'menu_icon'     => 'dashicons-products',
				'menu_position' => 21,
				'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
				'rewrite'       => array( 'slug' => 'products', 'with_front' => false ),
				'has_archive'   => 'products',
			)
		)
	);

	register_post_type(
		'ctr_project',
		array_merge(
			$common,
			array(
				'labels'        => ctr_labels( 'Project', 'Projects' ),
				'menu_icon'     => 'dashicons-building',
				'menu_position' => 22,
				'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
				'rewrite'       => array( 'slug' => 'projects', 'with_front' => false ),
				'has_archive'   => 'projects',
			)
		)
	);

	// Non-public supporting content: shown inside sections, no standalone URLs.
	$internal = array(
		'public'              => false,
		'show_ui'             => true,
		'show_in_rest'        => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'has_archive'         => false,
		'rewrite'             => false,
	);

	register_post_type(
		'ctr_slide',
		array_merge(
			$internal,
			array(
				'labels'        => ctr_labels( 'Hero Slide', 'Hero Slider' ),
				'menu_icon'     => 'dashicons-images-alt2',
				'menu_position' => 19,
				'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
			)
		)
	);

	register_post_type(
		'ctr_partner',
		array_merge(
			$internal,
			array(
				'labels'        => ctr_labels( 'Partner', 'Partners & Brands' ),
				'menu_icon'     => 'dashicons-groups',
				'menu_position' => 23,
				'supports'      => array( 'title', 'thumbnail', 'excerpt', 'page-attributes' ),
			)
		)
	);

	register_post_type(
		'ctr_testimonial',
		array_merge(
			$internal,
			array(
				'labels'        => ctr_labels( 'Testimonial', 'Testimonials' ),
				'menu_icon'     => 'dashicons-format-quote',
				'menu_position' => 24,
				'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			)
		)
	);
}

/**
 * Register taxonomies.
 */
function ctr_register_taxonomies() {
	$tax = array(
		'ctr_service_cat' => array( 'ctr_service', 'Service Category', 'Service Categories', 'service-category' ),
		'ctr_product_cat' => array( 'ctr_product', 'Product Category', 'Product Categories', 'product-category' ),
		'ctr_project_cat' => array( 'ctr_project', 'Project Sector', 'Project Sectors', 'project-sector' ),
	);
	foreach ( $tax as $name => $def ) {
		register_taxonomy(
			$name,
			$def[0],
			array(
				'labels'            => array(
					'name'          => $def[2],
					'singular_name' => $def[1],
					'add_new_item'  => "Add New {$def[1]}",
					'edit_item'     => "Edit {$def[1]}",
					'search_items'  => "Search {$def[2]}",
					'all_items'     => "All {$def[2]}",
				),
				'hierarchical'      => true,
				'public'            => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => $def[3], 'with_front' => false ),
			)
		);
	}
}

/**
 * Order CPT archives by the "Order" field, then title.
 */
add_action(
	'pre_get_posts',
	static function ( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}
		if ( $query->is_post_type_archive( array( 'ctr_service', 'ctr_product', 'ctr_project' ) ) || $query->is_tax( array( 'ctr_service_cat', 'ctr_product_cat', 'ctr_project_cat' ) ) ) {
			$query->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
			$query->set( 'posts_per_page', 12 );
		}
	}
);

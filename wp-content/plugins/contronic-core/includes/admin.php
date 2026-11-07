<?php
/**
 * Admin UX: assets, list-table columns, launch-readiness dashboard and notices.
 *
 * @package ContronicCore
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'admin_enqueue_scripts',
	static function ( $hook ) {
		wp_enqueue_style( 'ctr-admin', CTR_CORE_URL . 'assets/admin.css', array(), CTR_CORE_VERSION );
		if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			wp_enqueue_media();
			wp_enqueue_script( 'ctr-admin', CTR_CORE_URL . 'assets/admin.js', array( 'jquery', 'jquery-ui-sortable' ), CTR_CORE_VERSION, true );
		}
	}
);

/* -------------------------------------------------------------------------
 * List-table columns: image, order, sample/verified flags.
 * ---------------------------------------------------------------------- */

foreach ( array( 'ctr_slide', 'ctr_service', 'ctr_product', 'ctr_project', 'ctr_partner', 'ctr_testimonial' ) as $ctr_type ) {
	add_filter(
		"manage_{$ctr_type}_posts_columns",
		static function ( $columns ) {
			$new = array();
			foreach ( $columns as $key => $label ) {
				if ( 'title' === $key ) {
					$new['ctr_thumb'] = '<span class="screen-reader-text">Image</span>';
				}
				$new[ $key ] = $label;
				if ( 'title' === $key ) {
					$new['ctr_flags'] = 'Status';
					$new['ctr_order'] = 'Order';
				}
			}
			return $new;
		}
	);
	add_action(
		"manage_{$ctr_type}_posts_custom_column",
		static function ( $column, $post_id ) {
			if ( 'ctr_thumb' === $column ) {
				echo has_post_thumbnail( $post_id ) ? get_the_post_thumbnail( $post_id, array( 56, 56 ) ) : '<span class="ctr-thumb-empty" aria-hidden="true"></span>';
			} elseif ( 'ctr_order' === $column ) {
				echo (int) get_post_field( 'menu_order', $post_id );
			} elseif ( 'ctr_flags' === $column ) {
				$flags = array();
				if ( ctr_is_sample( $post_id ) ) {
					$flags[] = '<span class="ctr-badge ctr-badge--sample">Sample — review</span>';
				}
				if ( get_post_meta( $post_id, '_ctr_featured', true ) ) {
					$flags[] = '<span class="ctr-badge ctr-badge--featured">Homepage</span>';
				}
				$type = get_post_type( $post_id );
				if ( in_array( $type, array( 'ctr_partner', 'ctr_testimonial' ), true ) ) {
					$flags[] = get_post_meta( $post_id, '_ctr_verified', true )
						? '<span class="ctr-badge ctr-badge--replied">Verified</span>'
						: '<span class="ctr-badge ctr-badge--spam">Not verified — hidden</span>';
				}
				echo wp_kses_post( implode( ' ', $flags ) );
			}
		},
		10,
		2
	);
	add_filter(
		"manage_edit-{$ctr_type}_sortable_columns",
		static function ( $columns ) {
			$columns['ctr_order'] = 'menu_order';
			return $columns;
		}
	);
}

// Default admin ordering by menu order for our types.
add_action(
	'pre_get_posts',
	static function ( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}
		$type = $query->get( 'post_type' );
		if ( in_array( $type, array( 'ctr_slide', 'ctr_service', 'ctr_product', 'ctr_project', 'ctr_partner', 'ctr_testimonial' ), true ) && ! $query->get( 'orderby' ) ) {
			$query->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
		}
	}
);

// Helpful title placeholders.
add_filter(
	'enter_title_here',
	static function ( $title, $post ) {
		$map = array(
			'ctr_slide'       => 'Internal slide name',
			'ctr_service'     => 'Service name, e.g. HVAC Installation',
			'ctr_product'     => 'Product or solution name',
			'ctr_project'     => 'Project name',
			'ctr_partner'     => 'Partner / brand name',
			'ctr_testimonial' => 'Short summary (internal)',
		);
		return isset( $map[ $post->post_type ] ) ? $map[ $post->post_type ] : $title;
	},
	10,
	2
);

/* -------------------------------------------------------------------------
 * Launch-readiness dashboard widget.
 * ---------------------------------------------------------------------- */

/**
 * Items still missing before launch.
 *
 * @return array[] each: [label, url, done]
 */
function ctr_launch_checklist() {
	$c        = ctr_company();
	$customize = static function ( $section ) {
		return admin_url( 'customize.php?autofocus[section]=' . $section );
	};
	$sample = new WP_Query(
		array(
			'post_type'      => 'any',
			'post_status'    => array( 'publish', 'draft' ),
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_ctr_sample', // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);
	$count = static function ( $type, $meta = null ) {
		$args = array(
			'post_type'      => $type,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		);
		if ( $meta ) {
			$args['meta_key']   = $meta; // phpcs:ignore WordPress.DB.SlowDBQuery
			$args['meta_value'] = 1; // phpcs:ignore WordPress.DB.SlowDBQuery
		}
		return ( new WP_Query( $args ) )->found_posts;
	};

	return array(
		array( 'Upload the company logo', $customize( 'title_tagline' ), (bool) get_theme_mod( 'custom_logo' ) ),
		array( 'Add phone number', $customize( 'ctr_company' ), (bool) $c['phone'] ),
		array( 'Add email address', $customize( 'ctr_company' ), (bool) $c['email'] ),
		array( 'Add office address', $customize( 'ctr_company' ), (bool) ( $c['address'] || $c['city'] ) ),
		array( 'Set inquiry notification recipients', admin_url( 'edit.php?post_type=ctr_inquiry&page=ctr-inquiry-settings' ), (bool) get_option( 'ctr_inquiry_settings' ) ),
		array( 'Review all sample content (' . (int) $sample->found_posts . ' left)', admin_url( 'edit.php?post_type=ctr_service' ), 0 === (int) $sample->found_posts ),
		array( 'Upload real hero / project photography', admin_url( 'edit.php?post_type=ctr_slide' ), (bool) get_posts( array( 'post_type' => 'ctr_slide', 'meta_key' => '_thumbnail_id', 'posts_per_page' => 1, 'fields' => 'ids' ) ) ), // phpcs:ignore WordPress.DB.SlowDBQuery
		array( 'Publish at least one project', admin_url( 'edit.php?post_type=ctr_project' ), $count( 'ctr_project' ) > 0 ),
		array( 'Add verified partners / brands (optional)', admin_url( 'edit.php?post_type=ctr_partner' ), $count( 'ctr_partner', '_ctr_verified' ) > 0 ),
		array( 'Add verified testimonials (optional)', admin_url( 'edit.php?post_type=ctr_testimonial' ), $count( 'ctr_testimonial', '_ctr_verified' ) > 0 ),
		array( 'Write homepage SEO title & description', $customize( 'ctr_seo' ), (bool) ctr_seo_options()['home_description'] ),
		array( 'Allow search engines (Settings → Reading) on the live site', admin_url( 'options-reading.php' ), (bool) get_option( 'blog_public' ) ),
	);
}

add_action(
	'wp_dashboard_setup',
	static function () {
		wp_add_dashboard_widget(
			'ctr_launch',
			'Contronic website — launch checklist',
			static function () {
				$items = ctr_launch_checklist();
				$done  = count( array_filter( wp_list_pluck( $items, 2 ) ) );
				echo '<p><strong>' . (int) $done . ' of ' . count( $items ) . '</strong> complete.</p><ul class="ctr-checklist">';
				foreach ( $items as $item ) {
					printf(
						'<li class="%s"><span class="dashicons %s" aria-hidden="true"></span> <a href="%s">%s</a></li>',
						$item[2] ? 'is-done' : 'is-todo',
						$item[2] ? 'dashicons-yes-alt' : 'dashicons-marker',
						esc_url( $item[1] ),
						esc_html( $item[0] )
					);
				}
				echo '</ul>';
				echo '<p class="ctr-quicklinks"><a class="button" href="' . esc_url( admin_url( 'customize.php' ) ) . '">Customize site</a> <a class="button" href="' . esc_url( admin_url( 'edit.php?post_type=ctr_inquiry' ) ) . '">View inquiries</a></p>';
			}
		);
		// Put our widget first.
		global $wp_meta_boxes;
		if ( isset( $wp_meta_boxes['dashboard']['normal']['core']['ctr_launch'] ) ) {
			$widget = $wp_meta_boxes['dashboard']['normal']['core']['ctr_launch'];
			unset( $wp_meta_boxes['dashboard']['normal']['core']['ctr_launch'] );
			$wp_meta_boxes['dashboard']['normal']['high']['ctr_launch'] = $widget; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		}
	}
);

// Notice on our list screens while sample content exists.
add_action(
	'admin_notices',
	static function () {
		$screen = get_current_screen();
		if ( ! $screen || 'edit' !== $screen->base || ! in_array( $screen->post_type, array( 'ctr_service', 'ctr_product', 'ctr_project', 'page' ), true ) ) {
			return;
		}
		$has = get_posts(
			array(
				'post_type'      => $screen->post_type,
				'post_status'    => array( 'publish', 'draft' ),
				'meta_key'       => '_ctr_sample', // phpcs:ignore WordPress.DB.SlowDBQuery
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		if ( $has ) {
			echo '<div class="notice notice-warning"><p><strong>Sample content:</strong> items marked “Sample — review” were written as starting points. Replace them with verified Contronic UAE information before launch, then tick “Content reviewed and approved” on each.</p></div>';
		}
	}
);

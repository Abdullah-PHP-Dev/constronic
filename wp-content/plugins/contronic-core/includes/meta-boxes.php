<?php
/**
 * Meta box definitions for each content type.
 *
 * @package ContronicCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Icon choices used by services/products (rendered by the theme's icon set).
 *
 * @return array
 */
function ctr_icon_options() {
	return array(
		''           => '— Default —',
		'building'   => 'Building / Civil',
		'bolt'       => 'Electrical',
		'fan'        => 'HVAC / Ventilation',
		'pipe'       => 'Plumbing / Drainage',
		'flame'      => 'Fire protection',
		'gear'       => 'Maintenance',
		'cpu'        => 'Controls / BMS / ELV',
		'paint'      => 'Fit-out / Finishing',
		'shield'     => 'Safety / Compliance',
		'clipboard'  => 'Project management',
		'crane'      => 'Construction',
		'drop'       => 'Water / Pumps',
	);
}

/**
 * All meta box groups keyed by post type.
 *
 * @return array
 */
function ctr_meta_groups() {
	$groups = array(
		'ctr_slide'       => array(
			'title'  => 'Slide content',
			'fields' => array(
				array( 'key' => 'kicker', 'label' => 'Small label above heading', 'type' => 'text', 'placeholder' => 'CONTRONIC UAE · CONSTRUCTION & MEP' ),
				array( 'key' => 'heading', 'label' => 'Heading (main line)', 'type' => 'text', 'help' => 'Leave empty to use the slide title.' ),
				array( 'key' => 'heading_span', 'label' => 'Heading (highlighted second line)', 'type' => 'text' ),
				array( 'key' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 3 ),
				array( 'key' => 'cta_text', 'label' => 'Primary button text', 'type' => 'text' ),
				array( 'key' => 'cta_link', 'label' => 'Primary button link', 'type' => 'url' ),
				array( 'key' => 'cta2_text', 'label' => 'Secondary button text', 'type' => 'text' ),
				array( 'key' => 'cta2_link', 'label' => 'Secondary button link', 'type' => 'url' ),
				array( 'key' => 'image_mobile', 'label' => 'Mobile image (optional, portrait)', 'type' => 'image', 'help' => 'The slide image is the "Slide image" box in the sidebar (1920×1080 recommended).' ),
			),
		),
		'ctr_service'     => array(
			'title'  => 'Service details',
			'fields' => array(
				array( 'key' => 'icon', 'label' => 'Icon', 'type' => 'select', 'options' => ctr_icon_options() ),
				array( 'key' => 'tagline', 'label' => 'Tagline (shown under the title)', 'type' => 'text' ),
				array( 'key' => 'scope', 'label' => 'Scope of work', 'type' => 'lines', 'rows' => 6, 'help' => 'One item per line.' ),
				array( 'key' => 'process', 'label' => 'How we deliver', 'type' => 'lines', 'rows' => 5, 'help' => 'One step per line: <code>Step title | Short description</code>' ),
				array( 'key' => 'faqs', 'label' => 'FAQs', 'type' => 'lines', 'rows' => 5, 'help' => 'One per line: <code>Question | Answer</code>' ),
				array( 'key' => 'gallery', 'label' => 'Gallery', 'type' => 'gallery' ),
				array( 'key' => 'featured', 'label' => 'Feature on homepage', 'type' => 'checkbox' ),
			),
		),
		'ctr_product'     => array(
			'title'  => 'Product details',
			'fields' => array(
				array( 'key' => 'icon', 'label' => 'Icon (used when no image is set)', 'type' => 'select', 'options' => ctr_icon_options() ),
				array( 'key' => 'brand', 'label' => 'Brand / manufacturer', 'type' => 'text' ),
				array( 'key' => 'model', 'label' => 'Model / reference', 'type' => 'text' ),
				array( 'key' => 'specs', 'label' => 'Specifications', 'type' => 'lines', 'rows' => 6, 'help' => 'One per line: <code>Label | Value</code>' ),
				array( 'key' => 'features', 'label' => 'Key features', 'type' => 'lines', 'rows' => 5, 'help' => 'One per line.' ),
				array( 'key' => 'datasheet', 'label' => 'Datasheet / brochure (PDF)', 'type' => 'file' ),
				array( 'key' => 'gallery', 'label' => 'Gallery', 'type' => 'gallery' ),
				array( 'key' => 'featured', 'label' => 'Feature on homepage', 'type' => 'checkbox' ),
			),
		),
		'ctr_project'     => array(
			'title'  => 'Project details',
			'fields' => array(
				array( 'key' => 'client', 'label' => 'Client', 'type' => 'text', 'help' => 'Only publish client names you have permission to show.' ),
				array( 'key' => 'location', 'label' => 'Location', 'type' => 'text', 'placeholder' => 'Dubai, UAE' ),
				array( 'key' => 'year', 'label' => 'Year', 'type' => 'text' ),
				array( 'key' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => array( '' => '—', 'completed' => 'Completed', 'ongoing' => 'Ongoing' ) ),
				array( 'key' => 'scope', 'label' => 'Scope delivered', 'type' => 'lines', 'rows' => 5, 'help' => 'One item per line.' ),
				array( 'key' => 'services', 'label' => 'Related service IDs', 'type' => 'text', 'help' => 'Optional comma-separated Service IDs to link from the project page.' ),
				array( 'key' => 'gallery', 'label' => 'Project gallery', 'type' => 'gallery', 'help' => 'Images here also appear on the Gallery page.' ),
				array( 'key' => 'featured', 'label' => 'Feature on homepage', 'type' => 'checkbox' ),
			),
		),
		'ctr_partner'     => array(
			'title'  => 'Partner details',
			'fields' => array(
				array( 'key' => 'url', 'label' => 'Website', 'type' => 'url' ),
				array( 'key' => 'relationship', 'label' => 'Relationship', 'type' => 'select', 'options' => array( '' => '—', 'partner' => 'Partner', 'brand' => 'Brand we supply / install', 'client' => 'Client' ) ),
				array( 'key' => 'verified', 'label' => 'I confirm this relationship is real and approved for publication', 'type' => 'checkbox', 'help' => 'Unconfirmed partners are never shown on the website.' ),
			),
		),
		'ctr_testimonial' => array(
			'title'  => 'Testimonial details',
			'fields' => array(
				array( 'key' => 'person', 'label' => 'Person name', 'type' => 'text' ),
				array( 'key' => 'role', 'label' => 'Role / position', 'type' => 'text' ),
				array( 'key' => 'company', 'label' => 'Company', 'type' => 'text' ),
				array( 'key' => 'verified', 'label' => 'I confirm this is genuine client feedback approved for publication', 'type' => 'checkbox', 'help' => 'Unconfirmed testimonials are never shown on the website. The quote goes in the main editor.' ),
			),
		),
	);

	return apply_filters( 'ctr_meta_groups', $groups );
}

/**
 * SEO fields (all public post types).
 *
 * @return array
 */
function ctr_seo_fields() {
	return array(
		array( 'key' => 'seo_title', 'label' => 'SEO title', 'type' => 'text', 'help' => 'Recommended under 60 characters. Leave empty to use the post title.' ),
		array( 'key' => 'seo_description', 'label' => 'Meta description', 'type' => 'textarea', 'rows' => 3, 'help' => 'Recommended 120–160 characters. Falls back to the excerpt.' ),
		array( 'key' => 'seo_og_image', 'label' => 'Social share image', 'type' => 'image', 'help' => '1200×630 recommended. Falls back to the featured image.' ),
		array( 'key' => 'seo_canonical', 'label' => 'Canonical URL (advanced)', 'type' => 'url', 'help' => 'Leave empty unless this content is duplicated elsewhere.' ),
		array( 'key' => 'seo_noindex', 'label' => 'Hide from search engines (noindex) and the sitemap', 'type' => 'checkbox' ),
	);
}

/**
 * Post types receiving the SEO box.
 *
 * @return string[]
 */
function ctr_seo_post_types() {
	return array( 'page', 'post', 'ctr_service', 'ctr_product', 'ctr_project' );
}

add_action(
	'add_meta_boxes',
	static function ( $post_type ) {
		$groups = ctr_meta_groups();
		if ( isset( $groups[ $post_type ] ) ) {
			add_meta_box( 'ctr_details', $groups[ $post_type ]['title'], 'ctr_render_meta_box', $post_type, 'normal', 'high', array( 'fields' => $groups[ $post_type ]['fields'] ) );
		}
		if ( in_array( $post_type, ctr_seo_post_types(), true ) ) {
			add_meta_box( 'ctr_seo', 'SEO & sharing', 'ctr_render_meta_box', $post_type, 'normal', 'low', array( 'fields' => ctr_seo_fields() ) );
		}
	}
);

/**
 * Meta box renderer.
 *
 * @param WP_Post $post Post.
 * @param array   $box  Box args.
 */
function ctr_render_meta_box( $post, $box ) {
	wp_nonce_field( 'ctr_save_meta_' . $post->ID, 'ctr_meta_nonce' );
	if ( 'ctr_seo' === $box['id'] ) {
		echo '<div class="ctr-seo-preview" aria-hidden="true"><span class="ctr-seo-preview__title"></span><span class="ctr-seo-preview__url">' . esc_html( get_permalink( $post ) ) . '</span><span class="ctr-seo-preview__desc"></span></div>';
	}
	echo '<div class="ctr-fields">';
	foreach ( $box['args']['fields'] as $field ) {
		ctr_render_field( $field, get_post_meta( $post->ID, '_ctr_' . $field['key'], true ) );
	}
	echo '</div>';
}

add_action(
	'save_post',
	static function ( $post_id, $post ) {
		if ( ! isset( $_POST['ctr_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ctr_meta_nonce'] ) ), 'ctr_save_meta_' . $post_id ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$fields = array();
		$groups = ctr_meta_groups();
		if ( isset( $groups[ $post->post_type ] ) ) {
			$fields = $groups[ $post->post_type ]['fields'];
		}
		if ( in_array( $post->post_type, ctr_seo_post_types(), true ) ) {
			$fields = array_merge( $fields, ctr_seo_fields() );
		}

		foreach ( $fields as $field ) {
			$name  = '_ctr_' . $field['key'];
			$raw   = isset( $_POST[ $name ] ) ? wp_unslash( $_POST[ $name ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below.
			$value = ctr_sanitize_field( $field, $raw );
			if ( '' === $value || null === $value ) {
				delete_post_meta( $post_id, $name );
			} else {
				update_post_meta( $post_id, $name, $value );
			}
		}

		// Editing a sample item counts as reviewing it.
		if ( ctr_is_sample( $post_id ) && ! empty( $_POST['ctr_mark_reviewed'] ) ) {
			delete_post_meta( $post_id, '_ctr_sample' );
		}
	},
	10,
	2
);

/**
 * Sample-content review box.
 */
add_action(
	'add_meta_boxes',
	static function () {
		global $post;
		if ( $post && ctr_is_sample( $post->ID ) ) {
			add_meta_box(
				'ctr_sample',
				'Sample content — needs review',
				static function () {
					echo '<p>This item was created as <strong>sample content</strong>. Replace the text and images with verified company information, then tick the box and update.</p>';
					echo '<label><input type="checkbox" name="ctr_mark_reviewed" value="1"> Content reviewed and approved</label>';
				},
				null,
				'side',
				'high'
			);
		}
	}
);

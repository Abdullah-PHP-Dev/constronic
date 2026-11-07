<?php
/**
 * Banner for pages, driven by the "Page banner & gallery" fields and the featured image.
 *
 * @package Contronic
 */

$ctr_icon = isset( $args['icon'] ) ? $args['icon'] : 'building';
ctr_page_hero(
	array(
		'kicker'   => ctr_meta( 'hero_kicker' ),
		'title'    => get_the_title(),
		'span'     => ctr_meta( 'hero_span' ),
		'text'     => ctr_meta( 'hero_text' ),
		'image_id' => (int) get_post_thumbnail_id(),
		'icon'     => $ctr_icon,
		'seed'     => get_the_ID(),
		'actions'  => isset( $args['actions'] ) ? $args['actions'] : null,
	)
);

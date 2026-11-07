<?php
/**
 * Shared helpers used by the plugin and the theme.
 *
 * @package ContronicCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Public content types managed by this plugin.
 *
 * @return string[]
 */
function ctr_content_types() {
	return array( 'ctr_slide', 'ctr_service', 'ctr_product', 'ctr_project', 'ctr_partner', 'ctr_testimonial' );
}

/**
 * Read a post meta value managed by this plugin.
 *
 * @param string   $key     Field key without prefix.
 * @param int|null $post_id Post ID (defaults to current post).
 * @param mixed    $default Fallback.
 * @return mixed
 */
function ctr_meta( $key, $post_id = null, $default = '' ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	if ( ! $post_id ) {
		return $default;
	}
	$value = get_post_meta( $post_id, '_ctr_' . $key, true );
	return ( '' === $value || null === $value || array() === $value ) ? $default : $value;
}

/**
 * Split a "one item per line" field into an array of rows.
 * Each row can contain columns separated by "|".
 *
 * @param string $text    Raw text.
 * @param int    $columns Expected columns (1 = plain list).
 * @return array
 */
function ctr_lines( $text, $columns = 1 ) {
	$rows = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		if ( 1 === $columns ) {
			$rows[] = $line;
			continue;
		}
		$parts  = array_map( 'trim', explode( '|', $line, $columns ) );
		$rows[] = array_pad( $parts, $columns, '' );
	}
	return $rows;
}

/**
 * Comma separated attachment IDs to an int array.
 *
 * @param string|array $value Value.
 * @return int[]
 */
function ctr_ids( $value ) {
	if ( is_array( $value ) ) {
		$value = implode( ',', $value );
	}
	return array_values( array_filter( array_map( 'absint', explode( ',', (string) $value ) ) ) );
}

/**
 * Whether a post was created by the sample-content installer and still needs review.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function ctr_is_sample( $post_id ) {
	return (bool) get_post_meta( $post_id, '_ctr_sample', true );
}

/**
 * Hashed visitor IP (we never store raw IPs).
 *
 * @return string
 */
function ctr_visitor_hash() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	return wp_hash( $ip . '|ctr' );
}

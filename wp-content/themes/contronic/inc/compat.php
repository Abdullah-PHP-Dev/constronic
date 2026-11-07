<?php
/**
 * Graceful fallbacks if the Contronic Core plugin is inactive, so the theme never fatals.
 *
 * @package Contronic
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'ctr_meta' ) ) {
	/**
	 * Fallback meta reader.
	 *
	 * @param string   $key     Key.
	 * @param int|null $post_id Post.
	 * @param mixed    $default Default.
	 * @return mixed
	 */
	function ctr_meta( $key, $post_id = null, $default = '' ) {
		$value = get_post_meta( $post_id ? $post_id : get_the_ID(), '_ctr_' . $key, true );
		return '' === $value ? $default : $value;
	}
}

if ( ! function_exists( 'ctr_lines' ) ) {
	/**
	 * Fallback line splitter.
	 *
	 * @param string $text    Text.
	 * @param int    $columns Columns.
	 * @return array
	 */
	function ctr_lines( $text, $columns = 1 ) {
		$rows = array();
		foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}
			$rows[] = 1 === $columns ? $line : array_pad( array_map( 'trim', explode( '|', $line, $columns ) ), $columns, '' );
		}
		return $rows;
	}
}

if ( ! function_exists( 'ctr_ids' ) ) {
	/**
	 * Fallback ID parser.
	 *
	 * @param string $value Value.
	 * @return int[]
	 */
	function ctr_ids( $value ) {
		return array_values( array_filter( array_map( 'absint', explode( ',', (string) $value ) ) ) );
	}
}

if ( ! function_exists( 'ctr_company' ) ) {
	/**
	 * Fallback company info.
	 *
	 * @return array
	 */
	function ctr_company() {
		return wp_parse_args(
			array_filter( (array) get_option( 'ctr_company', array() ), 'strlen' ),
			array_fill_keys( array( 'legal', 'phone', 'phone2', 'whatsapp', 'email', 'email2', 'address', 'city', 'hours', 'map_embed', 'map_link', 'facebook', 'instagram', 'linkedin', 'x', 'youtube', 'tiktok' ), '' ) + array(
				'name'    => get_bloginfo( 'name' ),
				'tagline' => get_bloginfo( 'description' ),
				'country' => 'United Arab Emirates',
			)
		);
	}
}

/**
 * Whether the companion plugin is active.
 *
 * @return bool
 */
function ctr_core_active() {
	return defined( 'CTR_CORE_VERSION' );
}

add_action(
	'admin_notices',
	static function () {
		if ( ! ctr_core_active() && current_user_can( 'activate_plugins' ) ) {
			echo '<div class="notice notice-error"><p><strong>Contronic theme:</strong> please activate the <em>Contronic Core</em> plugin. It provides services, projects, inquiries and SEO.</p></div>';
		}
	}
);

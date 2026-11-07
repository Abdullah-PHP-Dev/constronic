<?php
/**
 * Inline SVG icon set (24×24, 1.6px stroke, currentColor).
 *
 * @package Contronic
 */

defined( 'ABSPATH' ) || exit;

/**
 * Raw path data for each icon.
 *
 * @return array
 */
function ctr_icon_paths() {
	return array(
		// Services.
		'building'  => '<path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 21v-5h6v5"/><path d="M9 9h.01M12 9h.01M15 9h.01M9 12.5h.01M12 12.5h.01M15 12.5h.01"/>',
		'bolt'      => '<path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"/>',
		'fan'       => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="1.6"/><path d="M12 10.4c-.6-2.6.4-5.2 2.8-5.4 2 0 2.4 2.6.6 4.2l-1.8 1.5"/><path d="M13.4 12.8c2.4.9 4 3.2 2.9 5.2-1.1 1.6-3.3.4-3.4-2l-.1-2.3"/><path d="M10.7 12.9c-2 1.8-4.8 2.1-6 .1-.8-1.8 1.5-3 3.6-1.9l2 1"/>',
		'pipe'      => '<path d="M3 7h7a4 4 0 0 1 4 4v10"/><path d="M3 11h7v10"/><path d="M3 5v8M8 19h8"/><path d="M18 3v5"/><path d="M15 5h6"/>',
		'flame'     => '<path d="M12 22a7 7 0 0 0 7-7c0-4-3-6.5-4-10-2.5 2-3 4.5-3 6-1-1-1.5-2.3-1.5-3.5C7.5 9.5 5 12 5 15a7 7 0 0 0 7 7Z"/><path d="M12 22a3 3 0 0 0 3-3c0-2-1.5-3-3-4.5-1.5 1.5-3 2.5-3 4.5a3 3 0 0 0 3 3Z"/>',
		'gear'      => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1Z"/>',
		'cpu'       => '<rect x="5" y="5" width="14" height="14" rx="2"/><rect x="9" y="9" width="6" height="6" rx="1"/><path d="M9 2v3M15 2v3M9 19v3M15 19v3M2 9h3M2 15h3M19 9h3M19 15h3"/>',
		'paint'     => '<rect x="3" y="3" width="15" height="6" rx="1.5"/><path d="M18 6h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-8v3"/><rect x="10" y="14" width="4" height="8" rx="1"/>',
		'shield'    => '<path d="M12 22s8-3.5 8-10V5l-8-3-8 3v7c0 6.5 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/>',
		'clipboard' => '<rect x="5" y="4" width="14" height="18" rx="2"/><path d="M9 4V3a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v1"/><path d="M9 11h6M9 15h6M9 19h3"/>',
		'crane'     => '<path d="M6 22V4l3-2v20"/><path d="M3 22h9"/><path d="M9 4h12l-3 3H9"/><path d="M18 7v5"/><rect x="16" y="12" width="4" height="3"/><path d="m6 8 3-3M6 12l3-3M6 16l3-3"/>',
		'drop'      => '<path d="M12 2.7 6.3 9.2a7.5 7.5 0 1 0 11.4 0L12 2.7Z"/><path d="M8.5 14.5a3.5 3.5 0 0 0 3.5 3.5"/>',
		// UI.
		'arrow'     => '<path d="M4 12h16"/><path d="m14 6 6 6-6 6"/>',
		'arrow-up-right' => '<path d="M7 17 17 7"/><path d="M8 7h9v9"/>',
		'chevron-left'  => '<path d="m15 18-6-6 6-6"/>',
		'chevron-right' => '<path d="m9 18 6-6-6-6"/>',
		'chevron-down'  => '<path d="m6 9 6 6 6-6"/>',
		'menu'      => '<path d="M3 7h18M3 12h18M3 17h12"/>',
		'close'     => '<path d="M6 6l12 12M18 6 6 18"/>',
		'check'     => '<path d="m5 12 5 5 9-10"/>',
		'plus'      => '<path d="M12 5v14M5 12h14"/>',
		'phone'     => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2Z"/>',
		'mail'      => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 6-10 7L2 6"/>',
		'pin'       => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
		'clock'     => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'download'  => '<path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/>',
		'search'    => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
		'quote'     => '<path d="M10 7H6a2 2 0 0 0-2 2v4h5v5H4"/><path d="M20 7h-4a2 2 0 0 0-2 2v4h5v5h-5"/>',
		'expand'    => '<path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/>',
		'calendar'  => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
		'user'      => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
		'layers'    => '<path d="m12 2 10 5-10 5L2 7l10-5Z"/><path d="m2 17 10 5 10-5"/><path d="m2 12 10 5 10-5"/>',
		// Brands (filled, simplified marks).
		'whatsapp'  => '<path fill="currentColor" stroke="none" d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2a8.2 8.2 0 0 1-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4.2-.4.7-1.4.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.7 11.8 11.8 0 0 0 4.5 4c1.7.7 2.3.8 3.2.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.2-.3-.3-.5-.4Z"/>',
		'facebook'  => '<path fill="currentColor" stroke="none" d="M14 8.5V6.8c0-.8.2-1.3 1.4-1.3H17V2.6c-.3 0-1.3-.1-2.4-.1-2.4 0-4 1.5-4 4.1v1.9H8v3.2h2.6V22H14v-10.3h2.6l.4-3.2H14Z"/>',
		'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".8" fill="currentColor"/>',
		'linkedin'  => '<path fill="currentColor" stroke="none" d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5ZM3 9.5h4v11H3v-11Zm6.5 0h3.8v1.6c.6-1 1.8-1.9 3.7-1.9 3.9 0 4.5 2.5 4.5 5.8v5.5h-4v-4.9c0-1.2 0-2.7-1.7-2.7s-2 1.3-2 2.6v5h-4v-11Z"/>',
		'x'         => '<path fill="currentColor" stroke="none" d="M17.8 3h3.1l-6.8 7.7L22 21h-6.2l-4.9-6.4L5.3 21H2.2l7.2-8.3L1.8 3h6.4l4.4 5.9L17.8 3Zm-1.1 16.2h1.7L7.4 4.7H5.6l11.1 14.5Z"/>',
		'youtube'   => '<path fill="currentColor" stroke="none" d="M22.5 7.2a2.8 2.8 0 0 0-2-2C18.8 4.8 12 4.8 12 4.8s-6.8 0-8.5.4a2.8 2.8 0 0 0-2 2A29 29 0 0 0 1 12a29 29 0 0 0 .5 4.8 2.8 2.8 0 0 0 2 2c1.7.4 8.5.4 8.5.4s6.8 0 8.5-.4a2.8 2.8 0 0 0 2-2A29 29 0 0 0 23 12a29 29 0 0 0-.5-4.8ZM9.8 15V9l5.7 3-5.7 3Z"/>',
		'tiktok'    => '<path fill="currentColor" stroke="none" d="M16.6 2h-3.4v13.4a2.9 2.9 0 1 1-2-2.8V9.1a6.3 6.3 0 1 0 5.4 6.3V8.6a7.9 7.9 0 0 0 4.6 1.5V6.7a4.6 4.6 0 0 1-4.6-4.7Z"/>',
	);
}

/**
 * Return an inline SVG icon.
 *
 * @param string $name  Icon name.
 * @param array  $args  class, label (accessible name; decorative when empty), size.
 * @return string
 */
function ctr_icon( $name, $args = array() ) {
	$paths = ctr_icon_paths();
	if ( ! isset( $paths[ $name ] ) ) {
		$name = 'layers';
	}
	$args  = wp_parse_args( $args, array( 'class' => '', 'label' => '', 'size' => 24 ) );
	$a11y  = $args['label'] ? 'role="img" aria-label="' . esc_attr( $args['label'] ) . '"' : 'aria-hidden="true" focusable="false"';
	return sprintf(
		'<svg class="icon icon-%1$s %2$s" width="%3$d" height="%3$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" %4$s>%5$s</svg>',
		esc_attr( $name ),
		esc_attr( $args['class'] ),
		(int) $args['size'],
		$a11y,
		$paths[ $name ] // Static, trusted markup.
	);
}

/**
 * Echo an icon.
 *
 * @param string $name Icon.
 * @param array  $args Args.
 */
function ctr_the_icon( $name, $args = array() ) {
	echo ctr_icon( $name, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from static markup with escaped attributes.
}

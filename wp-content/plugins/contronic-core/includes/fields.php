<?php
/**
 * Lightweight field framework (render + sanitize) for meta boxes.
 *
 * Field definition keys:
 *   key, label, type (text|textarea|url|email|number|checkbox|select|image|gallery|file|lines), help, options, placeholder, rows
 *
 * @package ContronicCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render one field.
 *
 * @param array $field Definition.
 * @param mixed $value Current value.
 */
function ctr_render_field( $field, $value ) {
	$type  = $field['type'];
	$name  = '_ctr_' . $field['key'];
	$id    = 'ctr-field-' . $field['key'];
	$ph    = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
	$help  = isset( $field['help'] ) ? $field['help'] : '';
	$label = $field['label'];

	echo '<div class="ctr-field ctr-field--' . esc_attr( $type ) . '">';

	if ( 'checkbox' === $type ) {
		printf(
			'<label for="%1$s"><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s> %4$s</label>',
			esc_attr( $id ),
			esc_attr( $name ),
			checked( (bool) $value, true, false ),
			esc_html( $label )
		);
	} else {
		printf( '<label class="ctr-field__label" for="%s">%s</label>', esc_attr( $id ), esc_html( $label ) );
	}

	switch ( $type ) {
		case 'textarea':
		case 'lines':
			printf(
				'<textarea id="%1$s" name="%2$s" rows="%3$d" placeholder="%4$s" class="widefat">%5$s</textarea>',
				esc_attr( $id ),
				esc_attr( $name ),
				isset( $field['rows'] ) ? (int) $field['rows'] : 4,
				esc_attr( $ph ),
				esc_textarea( (string) $value )
			);
			break;

		case 'select':
			printf( '<select id="%1$s" name="%2$s" class="widefat">', esc_attr( $id ), esc_attr( $name ) );
			foreach ( $field['options'] as $opt_value => $opt_label ) {
				printf( '<option value="%s" %s>%s</option>', esc_attr( $opt_value ), selected( (string) $value, (string) $opt_value, false ), esc_html( $opt_label ) );
			}
			echo '</select>';
			break;

		case 'image':
		case 'file':
			$att_id = absint( $value );
			echo '<div class="ctr-media" data-type="' . esc_attr( $type ) . '">';
			printf( '<input type="hidden" id="%1$s" name="%2$s" value="%3$s" class="ctr-media__input">', esc_attr( $id ), esc_attr( $name ), $att_id ? esc_attr( $att_id ) : '' );
			echo '<div class="ctr-media__preview">';
			if ( $att_id ) {
				if ( 'image' === $type ) {
					echo wp_get_attachment_image( $att_id, 'thumbnail' );
				} else {
					echo '<span>' . esc_html( basename( (string) get_attached_file( $att_id ) ) ) . '</span>';
				}
			}
			echo '</div>';
			printf( '<button type="button" class="button ctr-media__select">%s</button> ', 'image' === $type ? 'Select image' : 'Select file' );
			echo '<button type="button" class="button-link-delete ctr-media__remove"' . ( $att_id ? '' : ' hidden' ) . '>Remove</button>';
			echo '</div>';
			break;

		case 'gallery':
			$ids = ctr_ids( $value );
			echo '<div class="ctr-gallery">';
			printf( '<input type="hidden" id="%1$s" name="%2$s" value="%3$s" class="ctr-gallery__input">', esc_attr( $id ), esc_attr( $name ), esc_attr( implode( ',', $ids ) ) );
			echo '<ul class="ctr-gallery__list">';
			foreach ( $ids as $att_id ) {
				echo '<li data-id="' . esc_attr( $att_id ) . '">' . wp_get_attachment_image( $att_id, 'thumbnail' ) . '</li>';
			}
			echo '</ul>';
			echo '<button type="button" class="button ctr-gallery__edit">Add / edit gallery images</button> ';
			echo '<button type="button" class="button-link-delete ctr-gallery__clear"' . ( $ids ? '' : ' hidden' ) . '>Clear</button>';
			echo '</div>';
			break;

		case 'checkbox':
			break;

		default:
			$input_type = in_array( $type, array( 'url', 'email', 'number' ), true ) ? $type : 'text';
			printf(
				'<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" placeholder="%5$s" class="widefat">',
				esc_attr( $input_type ),
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( (string) $value ),
				esc_attr( $ph )
			);
	}

	if ( $help ) {
		echo '<p class="description">' . wp_kses( $help, array( 'code' => array(), 'strong' => array(), 'br' => array() ) ) . '</p>';
	}
	echo '</div>';
}

/**
 * Sanitize one field value from $_POST.
 *
 * @param array $field Definition.
 * @param mixed $raw   Raw (unslashed) value.
 * @return mixed
 */
function ctr_sanitize_field( $field, $raw ) {
	switch ( $field['type'] ) {
		case 'textarea':
		case 'lines':
			return sanitize_textarea_field( $raw );
		case 'url':
			return esc_url_raw( $raw );
		case 'email':
			return sanitize_email( $raw );
		case 'number':
			return '' === $raw ? '' : (float) $raw;
		case 'checkbox':
			return $raw ? 1 : '';
		case 'image':
		case 'file':
			return absint( $raw ) ? absint( $raw ) : '';
		case 'gallery':
			return implode( ',', ctr_ids( $raw ) );
		case 'select':
			$raw = (string) $raw;
			return array_key_exists( $raw, $field['options'] ) ? $raw : '';
		default:
			return sanitize_text_field( $raw );
	}
}

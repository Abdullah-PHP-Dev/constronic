<?php
/**
 * Reusable template helpers.
 *
 * @package Contronic
 */

defined( 'ABSPATH' ) || exit;

/**
 * Resolve a stored link: "/path/" → site URL, "#id" stays, absolute URLs pass through.
 *
 * @param string $link Link.
 * @return string
 */
function ctr_url( $link ) {
	$link = trim( (string) $link );
	if ( '' === $link ) {
		return '';
	}
	if ( '#' === $link[0] ) {
		return $link;
	}
	if ( '/' === $link[0] && ( ! isset( $link[1] ) || '/' !== $link[1] ) ) {
		return home_url( $link );
	}
	return $link;
}

/**
 * Two-tone heading.
 *
 * @param string $main  Main text.
 * @param string $span  Highlighted continuation.
 * @param string $tag   Heading tag.
 * @param string $class Class.
 */
function ctr_heading( $main, $span = '', $tag = 'h2', $class = 'display' ) {
	if ( '' === trim( $main . $span ) ) {
		return;
	}
	$tag = in_array( $tag, array( 'h1', 'h2', 'h3' ), true ) ? $tag : 'h2';
	printf(
		'<%1$s class="%2$s">%3$s%4$s</%1$s>',
		$tag, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- whitelisted.
		esc_attr( $class ),
		esc_html( $main ),
		$span ? ' <span>' . esc_html( $span ) . '</span>' : ''
	);
}

/**
 * Small label above headings.
 *
 * @param string $text Text.
 */
function ctr_kicker( $text ) {
	if ( $text ) {
		echo '<p class="kicker">' . esc_html( $text ) . '</p>';
	}
}

/**
 * Button link.
 *
 * @param string $text  Label.
 * @param string $link  Link (raw, resolved via ctr_url).
 * @param string $style primary|ghost|line|light.
 * @param array  $attrs Extra attributes.
 */
function ctr_button( $text, $link, $style = 'primary', $attrs = array() ) {
	$url = ctr_url( $link );
	if ( ! $text || ! $url ) {
		return;
	}
	$class = 'btn btn--' . $style;
	if ( isset( $attrs['class'] ) ) {
		$class .= ' ' . $attrs['class'];
		unset( $attrs['class'] );
	}
	$extra = '';
	foreach ( $attrs as $k => $v ) {
		$extra .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
	}
	printf(
		'<a class="%1$s" href="%2$s"%3$s><span>%4$s</span>%5$s</a>',
		esc_attr( $class ),
		esc_url( $url ),
		$extra, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
		esc_html( $text ),
		ctr_icon( 'arrow', array( 'class' => 'btn__icon', 'size' => 18 ) ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	);
}

/**
 * Standard section header.
 *
 * @param array $args kicker, heading, span, text, link_text, link, align (left|split|center), tag.
 */
function ctr_section_head( $args ) {
	$a = wp_parse_args(
		$args,
		array(
			'kicker'    => '',
			'heading'   => '',
			'span'      => '',
			'text'      => '',
			'link_text' => '',
			'link'      => '',
			'align'     => 'split',
			'tag'       => 'h2',
		)
	);
	echo '<header class="section-head section-head--' . esc_attr( $a['align'] ) . ' reveal">';
	echo '<div class="section-head__title">';
	ctr_kicker( $a['kicker'] );
	ctr_heading( $a['heading'], $a['span'], $a['tag'] );
	echo '</div>';
	if ( $a['text'] || $a['link_text'] ) {
		echo '<div class="section-head__aside">';
		if ( $a['text'] ) {
			echo '<p class="lead">' . esc_html( $a['text'] ) . '</p>';
		}
		if ( $a['link_text'] ) {
			ctr_button( $a['link_text'], $a['link'], 'line' );
		}
		echo '</div>';
	}
	echo '</header>';
}

/**
 * Generated blueprint artwork used wherever a photo has not been uploaded yet.
 * Deterministic per seed, so each card looks distinct but stable.
 *
 * @param string     $icon  Icon name.
 * @param int|string $seed  Seed (e.g. post ID).
 * @param string     $ratio "4:3" | "16:9" | "3:4" | "1:1".
 * @param string     $label Accessible label (empty = decorative).
 * @return string
 */
function ctr_art( $icon = 'building', $seed = 1, $ratio = '4:3', $label = '' ) {
	$dims = array(
		'4:3'  => array( 800, 600 ),
		'16:9' => array( 1280, 720 ),
		'3:4'  => array( 600, 800 ),
		'1:1'  => array( 700, 700 ),
		'21:9' => array( 1680, 720 ),
	);
	list( $w, $h ) = isset( $dims[ $ratio ] ) ? $dims[ $ratio ] : $dims['4:3'];
	$seed = abs( crc32( (string) $seed ) );
	mt_srand( $seed ); // phpcs:ignore WordPress.WP.AlternativeFunctions.rand_seeding_mt_srand -- deterministic art, not security.
	$uid   = 'a' . substr( md5( $seed . $ratio . $icon ), 0, 8 );
	$paths = ctr_icon_paths();
	$glyph = isset( $paths[ $icon ] ) ? $paths[ $icon ] : $paths['building'];
	$hue   = mt_rand( 0, 2 );
	$tints = array( array( '#0f2432', '#08131c' ), array( '#10283a', '#0a1620' ), array( '#132a33', '#08131c' ) );
	$tint  = $tints[ $hue ];

	$lines = '';
	// Technical "drawing" rectangles and dimension lines.
	for ( $i = 0; $i < 7; $i++ ) {
		$x  = mt_rand( 0, (int) ( $w * 0.85 ) );
		$y  = mt_rand( 0, (int) ( $h * 0.85 ) );
		$rw = mt_rand( 60, (int) ( $w * 0.4 ) );
		$rh = mt_rand( 40, (int) ( $h * 0.35 ) );
		$lines .= sprintf( '<rect x="%d" y="%d" width="%d" height="%d" />', $x, $y, $rw, $rh );
	}
	for ( $i = 0; $i < 4; $i++ ) {
		$y      = mt_rand( 40, $h - 40 );
		$x1     = mt_rand( 0, (int) ( $w * 0.3 ) );
		$x2     = mt_rand( (int) ( $w * 0.6 ), $w );
		$lines .= sprintf( '<path d="M%1$d %2$dH%3$d M%1$d %4$dv12 M%3$d %4$dv12" />', $x1, $y, $x2, $y - 6 );
	}
	// "Service runs": duct / pipe / cable routes with nodes.
	$runs = '';
	for ( $i = 0; $i < 3; $i++ ) {
		$y1   = mt_rand( (int) ( $h * 0.15 ), (int) ( $h * 0.85 ) );
		$x1   = mt_rand( (int) ( $w * 0.05 ), (int) ( $w * 0.35 ) );
		$x2   = mt_rand( (int) ( $w * 0.45 ), (int) ( $w * 0.95 ) );
		$y2   = mt_rand( (int) ( $h * 0.1 ), (int) ( $h * 0.9 ) );
		$runs .= sprintf( '<path d="M0 %2$dH%1$d V%4$d H%3$d" /><circle cx="%1$d" cy="%2$d" r="5" /><circle cx="%3$d" cy="%4$d" r="5" />', $x1, $y1, $x2, $y2 );
	}

	$size  = min( $w, $h ) * 0.42;
	$scale = $size / 24;
	$gx    = $w * ( 0.5 + ( mt_rand( -10, 10 ) / 100 ) ) - $size / 2;
	$gy    = $h * 0.5 - $size / 2;
	mt_srand(); // phpcs:ignore WordPress.WP.AlternativeFunctions.rand_seeding_mt_srand -- restore randomness.

	$a11y = $label ? 'role="img" aria-label="' . esc_attr( $label ) . '"' : 'aria-hidden="true" focusable="false"';

	return '<svg class="art" viewBox="0 0 ' . $w . ' ' . $h . '" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg" ' . $a11y . '>'
		. '<defs>'
		. '<linearGradient id="' . $uid . 'g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="' . $tint[0] . '"/><stop offset="1" stop-color="' . $tint[1] . '"/></linearGradient>'
		. '<radialGradient id="' . $uid . 'r" cx="' . ( 0.5 + ( $gx / $w - 0.3 ) ) . '" cy=".45" r=".6"><stop offset="0" style="stop-color:var(--c-accent,#19b3a6)" stop-opacity=".28"/><stop offset="1" style="stop-color:var(--c-accent,#19b3a6)" stop-opacity="0"/></radialGradient>'
		. '<pattern id="' . $uid . 'p" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M40 0H0V40" fill="none" stroke="#9fc3cf" stroke-opacity=".08"/></pattern>'
		. '<pattern id="' . $uid . 'P" width="200" height="200" patternUnits="userSpaceOnUse"><path d="M200 0H0V200" fill="none" stroke="#9fc3cf" stroke-opacity=".12"/></pattern>'
		. '</defs>'
		. '<rect width="100%" height="100%" fill="url(#' . $uid . 'g)"/>'
		. '<rect width="100%" height="100%" fill="url(#' . $uid . 'p)"/>'
		. '<rect width="100%" height="100%" fill="url(#' . $uid . 'P)"/>'
		. '<g fill="none" stroke="#9fc3cf" stroke-opacity=".16" stroke-width="1">' . $lines . '</g>'
		. '<g fill="none" style="stroke:var(--c-accent-2,#f2a93b)" stroke-opacity=".5" stroke-width="2">' . $runs . '</g>'
		. '<rect width="100%" height="100%" fill="url(#' . $uid . 'r)"/>'
		. '<g transform="translate(' . round( $gx, 1 ) . ' ' . round( $gy, 1 ) . ') scale(' . round( $scale, 3 ) . ')" fill="none" style="stroke:var(--c-accent,#19b3a6)" stroke-width="' . round( 1.6 / max( 1, $scale / 6 ), 2 ) . '" stroke-linecap="round" stroke-linejoin="round" opacity=".9">' . $glyph . '</g>'
		. '</svg>';
}

/**
 * Featured image or generated artwork.
 *
 * @param int    $post_id Post.
 * @param string $size    Image size.
 * @param string $ratio   Art ratio fallback.
 * @param array  $attr    Image attributes.
 * @return string
 */
function ctr_post_media( $post_id, $size = 'ctr-card', $ratio = '4:3', $attr = array() ) {
	if ( has_post_thumbnail( $post_id ) ) {
		return get_the_post_thumbnail( $post_id, $size, array_merge( array( 'loading' => 'lazy', 'decoding' => 'async' ), $attr ) );
	}
	$icon = ctr_meta( 'icon', $post_id );
	if ( ! $icon ) {
		$defaults = array(
			'ctr_service' => 'building',
			'ctr_product' => 'layers',
			'ctr_project' => 'crane',
			'post'        => 'clipboard',
		);
		$type     = get_post_type( $post_id );
		$icon     = isset( $defaults[ $type ] ) ? $defaults[ $type ] : 'building';
	}
	return ctr_art( $icon, $post_id, $ratio );
}

/**
 * Attachment image or generated artwork.
 *
 * @param int    $attachment_id Attachment.
 * @param string $size          Size.
 * @param string $icon          Fallback icon.
 * @param string $ratio         Fallback ratio.
 * @param array  $attr          Attributes.
 * @return string
 */
function ctr_image_or_art( $attachment_id, $size, $icon = 'building', $ratio = '4:3', $attr = array() ) {
	if ( $attachment_id && wp_attachment_is_image( $attachment_id ) ) {
		return wp_get_attachment_image( $attachment_id, $size, false, array_merge( array( 'loading' => 'lazy', 'decoding' => 'async' ), $attr ) );
	}
	return ctr_art( $icon, $attachment_id ? $attachment_id : $icon, $ratio );
}

/**
 * Site logo: uploaded image, else wordmark.
 *
 * @param string $context header|footer.
 */
function ctr_logo( $context = 'header' ) {
	$logo_id = (int) get_theme_mod( 'custom_logo' );
	$name    = get_bloginfo( 'name' );
	echo '<a class="brand brand--' . esc_attr( $context ) . '" href="' . esc_url( home_url( '/' ) ) . '" rel="home" aria-label="' . esc_attr( $name . ' — ' . __( 'home', 'contronic' ) ) . '">';
	if ( $logo_id ) {
		echo wp_get_attachment_image(
			$logo_id,
			'full',
			false,
			array(
				'class'         => 'brand__img',
				'alt'           => '',
				'loading'       => 'eager',
				'fetchpriority' => 'header' === $context ? 'high' : 'auto',
			)
		);
	} else {
		echo '<span class="brand__mark" aria-hidden="true">' . ctr_brand_mark_svg( 40 ) . '</span>';
		echo '<span class="brand__text">' . esc_html( ctr_mod( 'logo_text' ) ) . '<small>' . esc_html( ctr_mod( 'logo_sub' ) ) . '</small></span>';
	}
	echo '</a>';
}

/**
 * Social links list.
 *
 * @param string $class Wrapper class.
 */
function ctr_social_links( $class = 'social' ) {
	$c     = ctr_company();
	$names = array(
		'linkedin'  => 'LinkedIn',
		'instagram' => 'Instagram',
		'facebook'  => 'Facebook',
		'x'         => 'X',
		'youtube'   => 'YouTube',
		'tiktok'    => 'TikTok',
	);
	$items = '';
	foreach ( $names as $key => $label ) {
		if ( ! empty( $c[ $key ] ) ) {
			$items .= '<li><a href="' . esc_url( $c[ $key ] ) . '" target="_blank" rel="noopener me">' . ctr_icon( $key, array( 'label' => $label, 'size' => 18 ) ) . '</a></li>';
		}
	}
	if ( $items ) {
		echo '<ul class="' . esc_attr( $class ) . '">' . $items . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	}
}

/**
 * tel: link value.
 *
 * @param string $phone Phone.
 * @return string
 */
function ctr_tel( $phone ) {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', (string) $phone );
}

/**
 * WhatsApp chat URL.
 *
 * @return string
 */
function ctr_whatsapp_url() {
	$n = ctr_company()['whatsapp'];
	return $n ? 'https://wa.me/' . rawurlencode( $n ) : '';
}

/**
 * Breadcrumb navigation.
 */
function ctr_breadcrumbs() {
	if ( ! function_exists( 'ctr_breadcrumb_trail' ) || is_front_page() ) {
		return;
	}
	$trail = ctr_breadcrumb_trail();
	$last  = count( $trail ) - 1;
	echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'contronic' ) . '"><ol>';
	foreach ( $trail as $i => $crumb ) {
		if ( $i === $last || ! $crumb[1] ) {
			echo '<li><span aria-current="page">' . esc_html( wp_strip_all_tags( $crumb[0] ) ) . '</span></li>';
		} else {
			echo '<li><a href="' . esc_url( $crumb[1] ) . '">' . esc_html( wp_strip_all_tags( $crumb[0] ) ) . '</a></li>';
		}
	}
	echo '</ol></nav>';
}

/**
 * Inner page banner.
 *
 * @param array $args title, span, kicker, text, image_id, icon, actions(callable).
 */
function ctr_page_hero( $args ) {
	$a = wp_parse_args(
		$args,
		array(
			'title'    => '',
			'span'     => '',
			'kicker'   => '',
			'text'     => '',
			'image_id' => 0,
			'icon'     => 'building',
			'seed'     => 'page',
			'actions'  => null,
			'meta'     => '',
		)
	);
	?>
	<section class="page-hero">
		<div class="page-hero__media" aria-hidden="true">
			<?php
			if ( $a['image_id'] ) {
				echo wp_get_attachment_image( $a['image_id'], 'ctr-hero', false, array( 'loading' => 'eager', 'fetchpriority' => 'high', 'alt' => '' ) );
			} else {
				echo ctr_art( $a['icon'], $a['seed'], '21:9' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</div>
		<div class="page-hero__tone" aria-hidden="true"></div>
		<div class="container page-hero__inner">
			<?php ctr_breadcrumbs(); ?>
			<?php ctr_kicker( $a['kicker'] ); ?>
			<?php ctr_heading( $a['title'], $a['span'], 'h1', 'display display--page' ); ?>
			<?php if ( $a['text'] ) : ?>
				<p class="page-hero__text"><?php echo esc_html( $a['text'] ); ?></p>
			<?php endif; ?>
			<?php
			if ( $a['meta'] ) {
				echo wp_kses_post( $a['meta'] );
			}
			if ( is_callable( $a['actions'] ) ) {
				echo '<div class="page-hero__actions">';
				call_user_func( $a['actions'] );
				echo '</div>';
			}
			?>
		</div>
	</section>
	<?php
}

/**
 * Plain-text excerpt for a post.
 *
 * @param int $post_id Post.
 * @param int $words   Words.
 * @return string
 */
function ctr_excerpt( $post_id, $words = 24 ) {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return '';
	}
	$text = $post->post_excerpt ? $post->post_excerpt : wp_strip_all_tags( excerpt_remove_blocks( strip_shortcodes( $post->post_content ) ) );
	return wp_trim_words( $text, $words, '…' );
}

/**
 * Query helpers.
 *
 * @param string $type  Post type.
 * @param int    $count Max items.
 * @param array  $extra Extra query args.
 * @return WP_Post[]
 */
function ctr_items( $type, $count = -1, $extra = array() ) {
	return get_posts(
		array_merge(
			array(
				'post_type'        => $type,
				'post_status'      => 'publish',
				'posts_per_page'   => $count,
				'orderby'          => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
				'suppress_filters' => false,
			),
			$extra
		)
	);
}

/**
 * Featured-first selection.
 *
 * @param string $type  Post type.
 * @param int    $count Count.
 * @return WP_Post[]
 */
function ctr_featured_items( $type, $count ) {
	$featured = ctr_items(
		$type,
		$count,
		array(
			'meta_key'   => '_ctr_featured', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value' => 1, // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);
	if ( count( $featured ) < $count ) {
		$featured = array_merge(
			$featured,
			ctr_items( $type, $count - count( $featured ), array( 'post__not_in' => wp_list_pluck( $featured, 'ID' ) ? wp_list_pluck( $featured, 'ID' ) : array( 0 ) ) )
		);
	}
	return $featured;
}

/**
 * Partners/testimonials only when explicitly verified.
 *
 * @param string $type  Post type.
 * @param int    $count Count.
 * @return WP_Post[]
 */
function ctr_verified_items( $type, $count = -1 ) {
	return ctr_items(
		$type,
		$count,
		array(
			'meta_key'   => '_ctr_verified', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value' => 1, // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);
}

/**
 * Accessible lightbox gallery.
 *
 * @param int[]  $ids   Attachment IDs.
 * @param string $class Extra class.
 */
function ctr_gallery( $ids, $class = '' ) {
	$ids = array_filter( $ids, 'wp_attachment_is_image' );
	if ( ! $ids ) {
		return;
	}
	echo '<ul class="gallery-grid ' . esc_attr( $class ) . '" data-lightbox>';
	foreach ( $ids as $id ) {
		$full    = wp_get_attachment_image_src( $id, 'large' );
		$caption = wp_get_attachment_caption( $id );
		$alt     = get_post_meta( $id, '_wp_attachment_image_alt', true );
		printf(
			'<li class="gallery-grid__item reveal"><a href="%1$s" data-caption="%2$s" aria-label="%3$s">%4$s<span class="gallery-grid__zoom">%5$s</span></a></li>',
			esc_url( $full ? $full[0] : '' ),
			esc_attr( $caption ? $caption : $alt ),
			esc_attr( sprintf( /* translators: %s: image description */ __( 'Open image: %s', 'contronic' ), $alt ? $alt : get_the_title( $id ) ) ),
			wp_get_attachment_image( $id, 'ctr-card', false, array( 'loading' => 'lazy', 'decoding' => 'async' ) ),
			ctr_icon( 'expand', array( 'size' => 18 ) ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}
	echo '</ul>';
}

/**
 * Archive pagination.
 */
function ctr_pagination() {
	$links = paginate_links(
		array(
			'type'      => 'list',
			'prev_text' => ctr_icon( 'chevron-left', array( 'label' => __( 'Previous', 'contronic' ), 'size' => 18 ) ),
			'next_text' => ctr_icon( 'chevron-right', array( 'label' => __( 'Next', 'contronic' ), 'size' => 18 ) ),
		)
	);
	if ( $links ) {
		echo '<nav class="pagination" aria-label="' . esc_attr__( 'Pagination', 'contronic' ) . '">' . $links . '</nav>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Taxonomy filter chips for archives.
 *
 * @param string $taxonomy Taxonomy.
 * @param string $type     Post type.
 */
function ctr_term_filter( $taxonomy, $type ) {
	$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => true ) );
	if ( is_wp_error( $terms ) || count( $terms ) < 2 ) {
		return;
	}
	$current = is_tax( $taxonomy ) ? get_queried_object_id() : 0;
	echo '<nav class="chips reveal" aria-label="' . esc_attr__( 'Filter by category', 'contronic' ) . '"><ul>';
	echo '<li><a href="' . esc_url( get_post_type_archive_link( $type ) ) . '"' . ( $current ? '' : ' aria-current="page"' ) . '>' . esc_html__( 'All', 'contronic' ) . '</a></li>';
	foreach ( $terms as $term ) {
		echo '<li><a href="' . esc_url( get_term_link( $term ) ) . '"' . ( $current === $term->term_id ? ' aria-current="page"' : '' ) . '>' . esc_html( $term->name ) . '</a></li>';
	}
	echo '</ul></nav>';
}

/**
 * Inquiry form partial.
 *
 * @param array $args heading, kicker, service (preselect), form (identifier), compact.
 */
function ctr_inquiry_form( $args = array() ) {
	get_template_part( 'template-parts/inquiry-form', null, $args );
}

/**
 * Footer copyright with {year}.
 *
 * @return string
 */
function ctr_copyright() {
	return str_replace( '{year}', gmdate( 'Y' ), ctr_mod( 'footer_copyright' ) );
}

/**
 * Brand mark: an open "C" whose two ends are circuit terminals, joined by a service run.
 *
 * @param int $size Pixel size.
 * @return string
 */
function ctr_brand_mark_svg( $size = 40 ) {
	return '<svg viewBox="0 0 40 40" width="' . (int) $size . '" height="' . (int) $size . '">'
		. '<rect x="1" y="1" width="38" height="38" rx="9" fill="none" stroke="currentColor" stroke-opacity=".35"/>'
		. '<path d="M27.5 12.2A10 10 0 1 0 27.5 27.8" fill="none" style="stroke:var(--c-accent,#19b3a6)" stroke-width="3.4" stroke-linecap="round"/>'
		. '<circle cx="28.6" cy="12" r="2.9" style="fill:var(--c-accent-2,#f2a93b)"/>'
		. '<circle cx="28.6" cy="28" r="2.9" style="fill:var(--c-accent-2,#f2a93b)"/>'
		. '<path d="M28.6 15.5v9" style="stroke:var(--c-accent-2,#f2a93b)" stroke-width="1.6" stroke-dasharray="1.6 2.2"/>'
		. '</svg>';
}

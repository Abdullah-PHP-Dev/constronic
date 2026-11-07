<?php
/**
 * Homepage hero slider (Hero Slider items), with a Customizer fallback slide.
 *
 * @package Contronic
 */

$ctr_slides = array();
foreach ( ctr_items( 'ctr_slide', 6 ) as $ctr_post ) {
	$ctr_slides[] = array(
		'id'        => $ctr_post->ID,
		'kicker'    => ctr_meta( 'kicker', $ctr_post->ID ),
		'heading'   => ctr_meta( 'heading', $ctr_post->ID, get_the_title( $ctr_post ) ),
		'span'      => ctr_meta( 'heading_span', $ctr_post->ID ),
		'text'      => ctr_meta( 'description', $ctr_post->ID ),
		'cta_text'  => ctr_meta( 'cta_text', $ctr_post->ID ),
		'cta_link'  => ctr_meta( 'cta_link', $ctr_post->ID ),
		'cta2_text' => ctr_meta( 'cta2_text', $ctr_post->ID ),
		'cta2_link' => ctr_meta( 'cta2_link', $ctr_post->ID ),
		'image'     => (int) get_post_thumbnail_id( $ctr_post ),
		'mobile'    => (int) ctr_meta( 'image_mobile', $ctr_post->ID ),
	);
}
if ( ! $ctr_slides ) {
	$ctr_slides[] = array(
		'id'        => 0,
		'kicker'    => ctr_mod( 'hero_kicker' ),
		'heading'   => ctr_mod( 'hero_heading' ),
		'span'      => ctr_mod( 'hero_span' ),
		'text'      => ctr_mod( 'hero_text' ),
		'cta_text'  => ctr_mod( 'hero_cta_text' ),
		'cta_link'  => ctr_mod( 'hero_cta_link' ),
		'cta2_text' => '',
		'cta2_link' => '',
		'image'     => (int) ctr_mod( 'hero_image' ),
		'mobile'    => 0,
	);
}
$ctr_count = count( $ctr_slides );
?>
<section class="hero" data-slider aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Highlights', 'contronic' ); ?>">
	<div class="hero__slides">
		<?php foreach ( $ctr_slides as $ctr_i => $ctr_s ) : ?>
			<article class="hero__slide<?php echo 0 === $ctr_i ? ' is-active' : ''; ?>" data-slide role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( sprintf( '%d / %d', $ctr_i + 1, $ctr_count ) ); ?>"<?php echo 0 === $ctr_i ? '' : ' aria-hidden="true"'; ?>>
				<div class="hero__media" aria-hidden="true">
					<?php
					if ( $ctr_s['image'] ) {
						$ctr_attr = array(
							'alt'           => '',
							'loading'       => 0 === $ctr_i ? 'eager' : 'lazy',
							'fetchpriority' => 0 === $ctr_i ? 'high' : 'auto',
							'sizes'         => '100vw',
						);
						if ( $ctr_s['mobile'] ) {
							$ctr_mobile_src = wp_get_attachment_image_url( $ctr_s['mobile'], 'large' );
							echo '<picture><source media="(max-width: 700px)" srcset="' . esc_url( $ctr_mobile_src ) . '">' . wp_get_attachment_image( $ctr_s['image'], 'ctr-hero', false, $ctr_attr ) . '</picture>';
						} else {
							echo wp_get_attachment_image( $ctr_s['image'], 'ctr-hero', false, $ctr_attr );
						}
					} else {
						get_template_part( 'template-parts/hero-art', null, array( 'variant' => $ctr_i ) );
					}
					?>
				</div>
				<div class="hero__tone" aria-hidden="true"></div>
				<div class="hero__grid" aria-hidden="true"></div>
				<div class="container hero__inner">
					<div class="hero__copy">
						<?php ctr_kicker( $ctr_s['kicker'] ); ?>
						<?php ctr_heading( $ctr_s['heading'], $ctr_s['span'], 0 === $ctr_i ? 'h1' : 'h2', 'display display--hero' ); ?>
						<?php if ( $ctr_s['text'] ) : ?>
							<p class="hero__text"><?php echo esc_html( $ctr_s['text'] ); ?></p>
						<?php endif; ?>
						<div class="hero__actions">
							<?php ctr_button( $ctr_s['cta_text'], $ctr_s['cta_link'], 'primary' ); ?>
							<?php ctr_button( $ctr_s['cta2_text'], $ctr_s['cta2_link'], 'ghost' ); ?>
						</div>
					</div>
				</div>
			</article>
		<?php endforeach; ?>
	</div>

	<div class="container hero__ui">
		<?php if ( $ctr_count > 1 ) : ?>
			<div class="hero__controls">
				<button class="icon-btn icon-btn--outline" type="button" data-slider-prev aria-label="<?php esc_attr_e( 'Previous slide', 'contronic' ); ?>"><?php ctr_the_icon( 'chevron-left' ); ?></button>
				<ol class="hero__dots">
					<?php foreach ( $ctr_slides as $ctr_i => $ctr_s ) : ?>
						<li><button type="button" data-slider-dot="<?php echo (int) $ctr_i; ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d slide number */ __( 'Go to slide %d', 'contronic' ), $ctr_i + 1 ) ); ?>"<?php echo 0 === $ctr_i ? ' aria-current="true"' : ''; ?>><span><?php echo esc_html( sprintf( '%02d', $ctr_i + 1 ) ); ?></span><i></i></button></li>
					<?php endforeach; ?>
				</ol>
				<button class="icon-btn icon-btn--outline" type="button" data-slider-next aria-label="<?php esc_attr_e( 'Next slide', 'contronic' ); ?>"><?php ctr_the_icon( 'chevron-right' ); ?></button>
				<button class="icon-btn icon-btn--outline hero__pause" type="button" data-slider-pause aria-label="<?php esc_attr_e( 'Pause slideshow', 'contronic' ); ?>"><svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path class="i-pause" d="M8 5v14M16 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path class="i-play" d="M8 5v14l11-7z" fill="currentColor"/></svg></button>
			</div>
		<?php endif; ?>
		<a class="hero__scroll" href="#intro"><span><?php esc_html_e( 'Scroll', 'contronic' ); ?></span><i aria-hidden="true"></i></a>
	</div>
</section>

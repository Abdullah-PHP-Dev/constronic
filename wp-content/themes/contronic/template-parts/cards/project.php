<?php
/**
 * Project card (image-led overlay).
 *
 * @package Contronic
 */

$ctr_post  = $args['post'];
$ctr_large = ! empty( $args['large'] );
$ctr_terms = get_the_terms( $ctr_post, 'ctr_project_cat' );
$ctr_loc   = ctr_meta( 'location', $ctr_post->ID );
$ctr_year  = ctr_meta( 'year', $ctr_post->ID );
?>
<article class="card card--project reveal">
	<div class="card__media">
		<?php echo ctr_post_media( $ctr_post->ID, $ctr_large ? 'ctr-wide' : 'ctr-card', $ctr_large ? '16:9' : '4:3' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
	<div class="card__overlay">
		<?php if ( $ctr_terms && ! is_wp_error( $ctr_terms ) ) : ?>
			<p class="card__meta"><?php echo esc_html( $ctr_terms[0]->name ); ?></p>
		<?php endif; ?>
		<h3 class="card__title"><a class="card__link" href="<?php echo esc_url( get_permalink( $ctr_post ) ); ?>"><?php echo esc_html( get_the_title( $ctr_post ) ); ?></a></h3>
		<?php if ( $ctr_loc || $ctr_year ) : ?>
			<p class="card__sub"><?php echo esc_html( implode( ' · ', array_filter( array( $ctr_loc, $ctr_year ) ) ) ); ?></p>
		<?php endif; ?>
		<span class="card__arrow" aria-hidden="true"><?php ctr_the_icon( 'arrow-up-right', array( 'size' => 20 ) ); ?></span>
	</div>
</article>

<?php
/**
 * Product card.
 *
 * @package Contronic
 */

$ctr_post  = $args['post'];
$ctr_brand = ctr_meta( 'brand', $ctr_post->ID );
$ctr_terms = get_the_terms( $ctr_post, 'ctr_product_cat' );
?>
<article class="card card--product reveal">
	<div class="card__media">
		<?php echo ctr_post_media( $ctr_post->ID, 'ctr-card', '4:3' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
	<div class="card__body">
		<?php if ( $ctr_brand || ( $ctr_terms && ! is_wp_error( $ctr_terms ) ) ) : ?>
			<p class="card__meta"><?php echo esc_html( $ctr_brand ? $ctr_brand : $ctr_terms[0]->name ); ?></p>
		<?php endif; ?>
		<h3 class="card__title"><a class="card__link" href="<?php echo esc_url( get_permalink( $ctr_post ) ); ?>"><?php echo esc_html( get_the_title( $ctr_post ) ); ?></a></h3>
		<p class="card__text"><?php echo esc_html( ctr_excerpt( $ctr_post->ID, 18 ) ); ?></p>
		<span class="card__more" aria-hidden="true"><?php esc_html_e( 'View details', 'contronic' ); ?> <?php ctr_the_icon( 'arrow', array( 'size' => 16 ) ); ?></span>
	</div>
</article>

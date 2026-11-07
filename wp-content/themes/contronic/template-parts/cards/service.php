<?php
/**
 * Service card.
 *
 * @package Contronic
 */

$ctr_post = $args['post'];
$ctr_i    = isset( $args['index'] ) ? (int) $args['index'] : 0;
$ctr_icon = ctr_meta( 'icon', $ctr_post->ID, 'building' );
$ctr_url  = get_permalink( $ctr_post );
?>
<article class="card card--service reveal" style="--i: <?php echo (int) $ctr_i; ?>">
	<div class="card__media">
		<?php echo ctr_post_media( $ctr_post->ID, 'ctr-card', '4:3' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
	<div class="card__body">
		<div class="card__top">
			<span class="card__icon"><?php ctr_the_icon( $ctr_icon ); ?></span>
			<span class="card__num"><?php echo esc_html( sprintf( '%02d', $ctr_i + 1 ) ); ?></span>
		</div>
		<h3 class="card__title"><a class="card__link" href="<?php echo esc_url( $ctr_url ); ?>"><?php echo esc_html( get_the_title( $ctr_post ) ); ?></a></h3>
		<p class="card__text"><?php echo esc_html( ctr_excerpt( $ctr_post->ID, 20 ) ); ?></p>
		<span class="card__more" aria-hidden="true"><?php esc_html_e( 'View service', 'contronic' ); ?> <?php ctr_the_icon( 'arrow', array( 'size' => 16 ) ); ?></span>
	</div>
</article>

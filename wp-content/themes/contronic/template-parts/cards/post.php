<?php
/**
 * News card.
 *
 * @package Contronic
 */

$ctr_post = $args['post'];
$ctr_cats = get_the_category( $ctr_post->ID );
?>
<article class="card card--post reveal">
	<div class="card__media">
		<?php echo ctr_post_media( $ctr_post->ID, 'ctr-card', '4:3' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
	<div class="card__body">
		<p class="card__meta">
			<time datetime="<?php echo esc_attr( get_the_date( 'c', $ctr_post ) ); ?>"><?php echo esc_html( get_the_date( '', $ctr_post ) ); ?></time>
			<?php if ( $ctr_cats && 'uncategorized' !== $ctr_cats[0]->slug ) : ?>
				· <?php echo esc_html( $ctr_cats[0]->name ); ?>
			<?php endif; ?>
		</p>
		<h3 class="card__title"><a class="card__link" href="<?php echo esc_url( get_permalink( $ctr_post ) ); ?>"><?php echo esc_html( get_the_title( $ctr_post ) ); ?></a></h3>
		<p class="card__text"><?php echo esc_html( ctr_excerpt( $ctr_post->ID, 20 ) ); ?></p>
		<span class="card__more" aria-hidden="true"><?php esc_html_e( 'Read article', 'contronic' ); ?> <?php ctr_the_icon( 'arrow', array( 'size' => 16 ) ); ?></span>
	</div>
</article>

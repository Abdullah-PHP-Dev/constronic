<?php
/**
 * Homepage: verified testimonials only.
 *
 * @package Contronic
 */

$ctr_posts = ctr_verified_items( 'ctr_testimonial', 6 );
if ( ! $ctr_posts ) {
	return;
}
?>
<section class="section section--steel testimonials" id="testimonials">
	<div class="container">
		<?php
		ctr_section_head(
			array(
				'kicker'  => ctr_mod( 'testimonials_kicker' ),
				'heading' => ctr_mod( 'testimonials_heading' ),
			)
		);
		?>
		<ul class="quote-grid">
			<?php foreach ( $ctr_posts as $ctr_post ) : ?>
				<li class="reveal">
					<figure class="quote">
						<?php ctr_the_icon( 'quote', array( 'class' => 'quote__mark', 'size' => 32 ) ); ?>
						<blockquote><?php echo wp_kses_post( wpautop( $ctr_post->post_content ) ); ?></blockquote>
						<figcaption>
							<?php if ( has_post_thumbnail( $ctr_post ) ) : ?>
								<?php echo get_the_post_thumbnail( $ctr_post, 'thumbnail', array( 'class' => 'quote__avatar', 'alt' => '', 'loading' => 'lazy' ) ); ?>
							<?php endif; ?>
							<span>
								<strong><?php echo esc_html( ctr_meta( 'person', $ctr_post->ID ) ); ?></strong>
								<?php echo esc_html( implode( ', ', array_filter( array( ctr_meta( 'role', $ctr_post->ID ), ctr_meta( 'company', $ctr_post->ID ) ) ) ) ); ?>
							</span>
						</figcaption>
					</figure>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

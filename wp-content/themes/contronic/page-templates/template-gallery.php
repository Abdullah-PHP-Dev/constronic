<?php
/**
 * Template Name: Gallery
 *
 * Shows this page's gallery images plus images from all published projects, in a lightbox grid.
 *
 * @package Contronic
 */

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/page-banner', null, array( 'icon' => 'expand' ) );
	$ctr_ids = ctr_gallery_ids();
	?>
	<section class="section section--light">
		<div class="container">
			<?php
			// Intro text only (images in the content are already part of the gallery grid).
			$ctr_intro = trim( wp_strip_all_tags( excerpt_remove_blocks( get_the_content() ) ) );
			if ( $ctr_intro ) :
				?>
				<p class="lead lead--center reveal"><?php echo esc_html( $ctr_intro ); ?></p>
			<?php endif; ?>
			<?php if ( $ctr_ids ) : ?>
				<?php ctr_gallery( $ctr_ids, 'gallery-grid--masonry' ); ?>
			<?php else : ?>
				<?php get_template_part( 'template-parts/empty' ); ?>
			<?php endif; ?>
		</div>
	</section>
	<?php
	get_template_part( 'template-parts/home/cta' );
endwhile;

get_footer();

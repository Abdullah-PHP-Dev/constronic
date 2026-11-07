<?php
/**
 * Template Name: Partners & Brands
 *
 * Lists Partners & Brands items that are ticked as verified, grouped by relationship.
 *
 * @package Contronic
 */

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/page-banner', null, array( 'icon' => 'layers' ) );

	$ctr_all    = ctr_verified_items( 'ctr_partner' );
	$ctr_groups = array(
		'brand'   => array( __( 'Brands we supply & install', 'contronic' ), array() ),
		'partner' => array( __( 'Partners', 'contronic' ), array() ),
		'client'  => array( __( 'Clients', 'contronic' ), array() ),
		''        => array( '', array() ),
	);
	foreach ( $ctr_all as $ctr_p ) {
		$ctr_rel                       = ctr_meta( 'relationship', $ctr_p->ID );
		$ctr_rel                       = isset( $ctr_groups[ $ctr_rel ] ) ? $ctr_rel : '';
		$ctr_groups[ $ctr_rel ][1][] = $ctr_p;
	}
	?>
	<section class="section section--light">
		<div class="container">
			<?php if ( '' !== trim( get_the_content() ) ) : ?>
				<div class="prose prose--page reveal"><?php the_content(); ?></div>
			<?php endif; ?>
			<?php if ( $ctr_all ) : ?>
				<?php foreach ( $ctr_groups as $ctr_group ) : ?>
					<?php if ( $ctr_group[1] ) : ?>
						<div class="block">
							<?php if ( $ctr_group[0] ) : ?>
								<h2 class="block__title"><?php echo esc_html( $ctr_group[0] ); ?></h2>
							<?php endif; ?>
							<?php get_template_part( 'template-parts/partner-logos', null, array( 'posts' => $ctr_group[1], 'names' => true ) ); ?>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php else : ?>
				<?php get_template_part( 'template-parts/empty' ); ?>
			<?php endif; ?>
		</div>
	</section>
	<?php
	get_template_part( 'template-parts/home/cta' );
endwhile;

get_footer();

<?php
/**
 * Products & solutions listing.
 *
 * @package Contronic
 */

get_header();

$ctr_seo  = function_exists( 'ctr_seo_options' ) ? ctr_seo_options() : array();
$ctr_term = is_tax() ? get_queried_object() : null;

ctr_page_hero(
	array(
		'kicker' => ctr_mod( 'products_kicker' ),
		'title'  => $ctr_term ? $ctr_term->name : ( ! empty( $ctr_seo['products_title'] ) ? $ctr_seo['products_title'] : __( 'Products & Solutions', 'contronic' ) ),
		'text'   => $ctr_term ? wp_strip_all_tags( term_description() ) : ( ! empty( $ctr_seo['products_desc'] ) ? $ctr_seo['products_desc'] : ctr_mod( 'products_text' ) ),
		'icon'   => 'layers',
		'seed'   => 'products',
	)
);
?>
<section class="section section--light">
	<div class="container">
		<?php ctr_term_filter( 'ctr_product_cat', 'ctr_product' ); ?>
		<?php if ( have_posts() ) : ?>
			<ul class="card-grid card-grid--3 card-grid--on-light">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<li><?php get_template_part( 'template-parts/cards/product', null, array( 'post' => get_post() ) ); ?></li>
				<?php endwhile; ?>
			</ul>
			<?php ctr_pagination(); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/empty' ); ?>
		<?php endif; ?>
	</div>
</section>
<?php
get_template_part( 'template-parts/home/cta' );
get_footer();

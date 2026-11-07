<?php
/**
 * Services listing.
 *
 * @package Contronic
 */

get_header();

$ctr_seo = function_exists( 'ctr_seo_options' ) ? ctr_seo_options() : array();
$ctr_term = is_tax() ? get_queried_object() : null;

ctr_page_hero(
	array(
		'kicker' => ctr_mod( 'services_kicker' ),
		'title'  => $ctr_term ? $ctr_term->name : ( ! empty( $ctr_seo['services_title'] ) ? $ctr_seo['services_title'] : __( 'Services', 'contronic' ) ),
		'span'   => $ctr_term ? '' : ctr_mod( 'services_span' ),
		'text'   => $ctr_term ? wp_strip_all_tags( term_description() ) : ( ! empty( $ctr_seo['services_desc'] ) ? $ctr_seo['services_desc'] : ctr_mod( 'services_text' ) ),
		'icon'   => 'gear',
		'seed'   => 'services',
	)
);
?>
<section class="section section--light">
	<div class="container">
		<?php ctr_term_filter( 'ctr_service_cat', 'ctr_service' ); ?>
		<?php if ( have_posts() ) : ?>
			<ul class="card-grid card-grid--services card-grid--on-light">
				<?php
				$ctr_i = 0;
				while ( have_posts() ) :
					the_post();
					?>
					<li><?php get_template_part( 'template-parts/cards/service', null, array( 'post' => get_post(), 'index' => $ctr_i++ ) ); ?></li>
				<?php endwhile; ?>
			</ul>
			<?php ctr_pagination(); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/empty' ); ?>
		<?php endif; ?>
	</div>
</section>
<?php
get_template_part( 'template-parts/home/process' );
get_template_part( 'template-parts/home/cta' );
get_footer();

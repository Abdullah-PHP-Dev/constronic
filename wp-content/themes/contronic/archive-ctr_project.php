<?php
/**
 * Projects / portfolio listing.
 *
 * @package Contronic
 */

get_header();

$ctr_seo  = function_exists( 'ctr_seo_options' ) ? ctr_seo_options() : array();
$ctr_term = is_tax() ? get_queried_object() : null;

ctr_page_hero(
	array(
		'kicker' => ctr_mod( 'projects_kicker' ),
		'title'  => $ctr_term ? $ctr_term->name : ( ! empty( $ctr_seo['projects_title'] ) ? $ctr_seo['projects_title'] : __( 'Projects', 'contronic' ) ),
		'text'   => $ctr_term ? wp_strip_all_tags( term_description() ) : ( ! empty( $ctr_seo['projects_desc'] ) ? $ctr_seo['projects_desc'] : ctr_mod( 'projects_text' ) ),
		'icon'   => 'crane',
		'seed'   => 'projects',
	)
);
?>
<section class="section section--dark">
	<div class="container">
		<?php ctr_term_filter( 'ctr_project_cat', 'ctr_project' ); ?>
		<?php if ( have_posts() ) : ?>
			<ul class="card-grid card-grid--3">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<li><?php get_template_part( 'template-parts/cards/project', null, array( 'post' => get_post() ) ); ?></li>
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

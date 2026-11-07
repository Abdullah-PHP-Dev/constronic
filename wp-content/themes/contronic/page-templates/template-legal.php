<?php
/**
 * Template Name: Legal (Privacy, Terms)
 *
 * @package Contronic
 */

get_header();

while ( have_posts() ) :
	the_post();
	ctr_page_hero(
		array(
			'title' => get_the_title(),
			'text'  => sprintf( /* translators: %s: date */ __( 'Last updated %s', 'contronic' ), get_the_modified_date() ),
			'icon'  => 'shield',
			'seed'  => get_the_ID(),
		)
	);
	?>
	<section class="section section--light">
		<div class="container prose prose--legal">
			<?php the_content(); ?>
		</div>
	</section>
	<?php
endwhile;

get_footer();

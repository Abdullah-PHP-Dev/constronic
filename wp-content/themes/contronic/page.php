<?php
/**
 * Default page.
 *
 * @package Contronic
 */

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/page-banner' );
	?>
	<section class="section section--light">
		<div class="container prose prose--page reveal">
			<?php the_content(); ?>
			<?php wp_link_pages(); ?>
		</div>
	</section>
	<?php
endwhile;

get_footer();

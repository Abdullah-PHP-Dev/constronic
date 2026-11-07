<?php
/**
 * Homepage. Every section is managed in Appearance → Customize → Homepage sections,
 * and its items come from the matching admin menu (Hero Slider, Services, Projects…).
 *
 * @package Contronic
 */

get_header();

$ctr_sections = apply_filters(
	'ctr_home_sections',
	array( 'hero', 'intro', 'services', 'strengths', 'products', 'process', 'projects', 'partners', 'testimonials', 'news', 'cta' )
);

foreach ( $ctr_sections as $ctr_section ) {
	if ( 'hero' === $ctr_section || ctr_mod( $ctr_section . '_show' ) ) {
		get_template_part( 'template-parts/home/' . $ctr_section );
	}
}

// Content added to the Home page in the editor appears after the sections.
while ( have_posts() ) {
	the_post();
	if ( '' !== trim( get_the_content() ) ) {
		echo '<section class="section section--light"><div class="container prose">';
		the_content();
		echo '</div></section>';
	}
}

get_footer();

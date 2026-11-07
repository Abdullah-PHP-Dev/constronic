<?php
/**
 * Homepage: selected projects (hidden when none are published).
 *
 * @package Contronic
 */

$ctr_posts = ctr_featured_items( 'ctr_project', 5 );
if ( ! $ctr_posts ) {
	return;
}
?>
<section class="section section--dark projects-section" id="projects">
	<div class="container">
		<?php
		ctr_section_head(
			array(
				'kicker'    => ctr_mod( 'projects_kicker' ),
				'heading'   => ctr_mod( 'projects_heading' ),
				'span'      => ctr_mod( 'projects_span' ),
				'text'      => ctr_mod( 'projects_text' ),
				'link_text' => __( 'All projects', 'contronic' ),
				'link'      => get_post_type_archive_link( 'ctr_project' ),
			)
		);
		?>
		<ul class="project-mosaic">
			<?php foreach ( $ctr_posts as $ctr_i => $ctr_post ) : ?>
				<li class="project-mosaic__item<?php echo 0 === $ctr_i ? ' is-large' : ''; ?>"><?php get_template_part( 'template-parts/cards/project', null, array( 'post' => $ctr_post, 'large' => 0 === $ctr_i ) ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

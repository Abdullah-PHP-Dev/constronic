<?php
/**
 * Homepage: featured services.
 *
 * @package Contronic
 */

$ctr_services = ctr_featured_items( 'ctr_service', max( 1, (int) ctr_mod( 'services_count' ) ) );
if ( ! $ctr_services ) {
	return;
}
?>
<section class="section section--dark services-section" id="services">
	<div class="container">
		<?php
		ctr_section_head(
			array(
				'kicker'    => ctr_mod( 'services_kicker' ),
				'heading'   => ctr_mod( 'services_heading' ),
				'span'      => ctr_mod( 'services_span' ),
				'text'      => ctr_mod( 'services_text' ),
				'link_text' => __( 'All services', 'contronic' ),
				'link'      => get_post_type_archive_link( 'ctr_service' ),
			)
		);
		?>
		<ul class="card-grid card-grid--services">
			<?php foreach ( $ctr_services as $ctr_i => $ctr_post ) : ?>
				<li><?php get_template_part( 'template-parts/cards/service', null, array( 'post' => $ctr_post, 'index' => $ctr_i ) ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

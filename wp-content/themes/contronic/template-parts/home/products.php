<?php
/**
 * Homepage: products & solutions (hidden when none are published).
 *
 * @package Contronic
 */

$ctr_posts = ctr_featured_items( 'ctr_product', 3 );
if ( ! $ctr_posts ) {
	return;
}
?>
<section class="section section--dark" id="products">
	<div class="container">
		<?php
		ctr_section_head(
			array(
				'kicker'    => ctr_mod( 'products_kicker' ),
				'heading'   => ctr_mod( 'products_heading' ),
				'span'      => ctr_mod( 'products_span' ),
				'text'      => ctr_mod( 'products_text' ),
				'link_text' => __( 'All products', 'contronic' ),
				'link'      => get_post_type_archive_link( 'ctr_product' ),
			)
		);
		?>
		<ul class="card-grid card-grid--3">
			<?php foreach ( $ctr_posts as $ctr_post ) : ?>
				<li><?php get_template_part( 'template-parts/cards/product', null, array( 'post' => $ctr_post ) ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

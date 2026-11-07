<?php
/**
 * Homepage: verified partner / brand logos (hidden when none are verified).
 *
 * @package Contronic
 */

$ctr_posts = ctr_verified_items( 'ctr_partner', 24 );
if ( ! $ctr_posts ) {
	return;
}
$ctr_page = get_page_by_path( 'partners' );
?>
<section class="section section--light section--tight partners-strip" id="partners">
	<div class="container">
		<?php
		ctr_section_head(
			array(
				'kicker'    => ctr_mod( 'partners_kicker' ),
				'heading'   => ctr_mod( 'partners_heading' ),
				'link_text' => $ctr_page ? __( 'All partners', 'contronic' ) : '',
				'link'      => $ctr_page ? get_permalink( $ctr_page ) : '',
				'tag'       => 'h2',
			)
		);
		?>
		<?php get_template_part( 'template-parts/partner-logos', null, array( 'posts' => $ctr_posts ) ); ?>
	</div>
</section>

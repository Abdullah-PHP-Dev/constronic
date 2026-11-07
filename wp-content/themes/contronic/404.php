<?php
/**
 * Not found.
 *
 * @package Contronic
 */

get_header();
?>
<section class="page-hero page-hero--full">
	<div class="page-hero__media" aria-hidden="true"><?php get_template_part( 'template-parts/hero-art', null, array( 'variant' => 1 ) ); ?></div>
	<div class="page-hero__tone" aria-hidden="true"></div>
	<div class="container page-hero__inner">
		<p class="kicker"><?php esc_html_e( 'Error 404', 'contronic' ); ?></p>
		<h1 class="display display--page"><?php esc_html_e( 'This page is', 'contronic' ); ?> <span><?php esc_html_e( 'not on the drawings.', 'contronic' ); ?></span></h1>
		<p class="page-hero__text"><?php esc_html_e( 'The page you are looking for may have moved or no longer exists.', 'contronic' ); ?></p>
		<div class="page-hero__actions">
			<?php ctr_button( __( 'Back to home', 'contronic' ), home_url( '/' ), 'primary' ); ?>
			<?php
			$ctr_contact = get_page_by_path( 'contact' );
			if ( $ctr_contact ) {
				ctr_button( __( 'Contact us', 'contronic' ), get_permalink( $ctr_contact ), 'ghost' );
			}
			?>
		</div>
	</div>
</section>
<?php
get_footer();

<?php
/**
 * Template Name: About
 *
 * Page content + the homepage Introduction facts, Why Contronic and Process sections,
 * so the company story is edited once and reused.
 *
 * @package Contronic
 */

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/page-banner', null, array( 'icon' => 'building' ) );
	$ctr_facts   = ctr_lines( ctr_mod( 'intro_facts' ), 2 );
	$ctr_gallery = ctr_ids( ctr_meta( 'gallery' ) );
	?>
	<section class="section section--light">
		<div class="container about">
			<div class="about__lead reveal">
				<?php ctr_kicker( ctr_mod( 'intro_kicker' ) ); ?>
				<?php ctr_heading( ctr_mod( 'intro_heading' ), ctr_mod( 'intro_span' ) ); ?>
			</div>
			<div class="prose reveal">
				<?php the_content(); ?>
			</div>
		</div>
		<?php if ( $ctr_facts ) : ?>
			<div class="container">
				<dl class="facts reveal">
					<?php foreach ( $ctr_facts as $ctr_fact ) : ?>
						<div class="facts__item"><dt><?php echo esc_html( $ctr_fact[0] ); ?></dt><dd><?php echo esc_html( $ctr_fact[1] ); ?></dd></div>
					<?php endforeach; ?>
				</dl>
			</div>
		<?php endif; ?>
	</section>
	<?php
	get_template_part( 'template-parts/home/strengths' );
	get_template_part( 'template-parts/home/process' );

	if ( $ctr_gallery ) :
		?>
		<section class="section section--light section--tight">
			<div class="container"><?php ctr_gallery( $ctr_gallery ); ?></div>
		</section>
		<?php
	endif;

	get_template_part( 'template-parts/home/partners' );
	get_template_part( 'template-parts/home/cta' );
endwhile;

get_footer();

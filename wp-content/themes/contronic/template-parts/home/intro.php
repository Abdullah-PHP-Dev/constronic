<?php
/**
 * Homepage: introduction + key facts.
 *
 * @package Contronic
 */

$ctr_facts = ctr_lines( ctr_mod( 'intro_facts' ), 2 );
?>
<section class="section section--light intro" id="intro">
	<div class="container intro__grid">
		<div class="intro__copy reveal">
			<?php ctr_kicker( ctr_mod( 'intro_kicker' ) ); ?>
			<?php ctr_heading( ctr_mod( 'intro_heading' ), ctr_mod( 'intro_span' ) ); ?>
			<?php if ( ctr_mod( 'intro_text' ) ) : ?>
				<p class="lead"><?php echo esc_html( ctr_mod( 'intro_text' ) ); ?></p>
			<?php endif; ?>
			<?php ctr_button( ctr_mod( 'intro_cta_text' ), ctr_mod( 'intro_cta_link' ), 'line' ); ?>
		</div>
		<figure class="intro__media reveal">
			<?php echo ctr_image_or_art( (int) ctr_mod( 'intro_image' ), 'large', 'crane', '3:4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</figure>
	</div>
	<?php if ( $ctr_facts ) : ?>
		<div class="container">
			<dl class="facts reveal">
				<?php foreach ( $ctr_facts as $ctr_fact ) : ?>
					<div class="facts__item">
						<dt><?php echo esc_html( $ctr_fact[0] ); ?></dt>
						<dd><?php echo esc_html( $ctr_fact[1] ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		</div>
	<?php endif; ?>
</section>

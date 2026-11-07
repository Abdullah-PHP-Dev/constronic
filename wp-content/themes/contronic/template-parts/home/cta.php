<?php
/**
 * Contact call-to-action with inquiry form (homepage and inner pages).
 *
 * @package Contronic
 */

$ctr_c       = ctr_company();
$ctr_service = isset( $args['service'] ) ? $args['service'] : '';
?>
<section class="section section--dark cta-section" id="inquiry">
	<div class="cta-section__glow" aria-hidden="true"></div>
	<div class="container cta-section__grid">
		<div class="cta-section__copy reveal">
			<?php ctr_kicker( ctr_mod( 'cta_kicker' ) ); ?>
			<?php ctr_heading( ctr_mod( 'cta_heading' ), ctr_mod( 'cta_span' ) ); ?>
			<?php if ( ctr_mod( 'cta_text' ) ) : ?>
				<p class="lead"><?php echo esc_html( ctr_mod( 'cta_text' ) ); ?></p>
			<?php endif; ?>
			<ul class="contact-list contact-list--large">
				<?php if ( $ctr_c['phone'] ) : ?>
					<li><?php ctr_the_icon( 'phone' ); ?><a href="<?php echo esc_attr( ctr_tel( $ctr_c['phone'] ) ); ?>"><?php echo esc_html( $ctr_c['phone'] ); ?></a></li>
				<?php endif; ?>
				<?php if ( $ctr_c['email'] ) : ?>
					<li><?php ctr_the_icon( 'mail' ); ?><a href="mailto:<?php echo esc_attr( antispambot( $ctr_c['email'] ) ); ?>"><?php echo esc_html( antispambot( $ctr_c['email'] ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( ctr_whatsapp_url() ) : ?>
					<li><?php ctr_the_icon( 'whatsapp' ); ?><a href="<?php echo esc_url( ctr_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Chat on WhatsApp', 'contronic' ); ?></a></li>
				<?php endif; ?>
				<?php if ( $ctr_c['city'] ) : ?>
					<li><?php ctr_the_icon( 'pin' ); ?><span><?php echo esc_html( $ctr_c['city'] . ', ' . $ctr_c['country'] ); ?></span></li>
				<?php endif; ?>
			</ul>
		</div>
		<div class="cta-section__form reveal">
			<?php ctr_inquiry_form( array( 'service' => $ctr_service, 'form' => 'cta' ) ); ?>
		</div>
	</div>
</section>

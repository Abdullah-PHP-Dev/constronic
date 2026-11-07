<?php
/**
 * Template Name: Contact
 *
 * Contact details come from Appearance → Customize → Company & contact details.
 *
 * @package Contronic
 */

get_header();

$ctr_c = ctr_company();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/page-banner', null, array( 'icon' => 'mail' ) );

	$ctr_cards = array();
	if ( $ctr_c['phone'] || $ctr_c['phone2'] ) {
		$ctr_cards[] = array(
			'icon'  => 'phone',
			'title' => __( 'Call us', 'contronic' ),
			'lines' => array_map(
				static function ( $p ) {
					return '<a href="' . esc_attr( ctr_tel( $p ) ) . '">' . esc_html( $p ) . '</a>';
				},
				array_filter( array( $ctr_c['phone'], $ctr_c['phone2'] ) )
			),
		);
	}
	if ( $ctr_c['email'] || $ctr_c['email2'] ) {
		$ctr_cards[] = array(
			'icon'  => 'mail',
			'title' => __( 'Email us', 'contronic' ),
			'lines' => array_map(
				static function ( $e ) {
					return '<a href="mailto:' . esc_attr( antispambot( $e ) ) . '">' . esc_html( antispambot( $e ) ) . '</a>';
				},
				array_filter( array( $ctr_c['email'], $ctr_c['email2'] ) )
			),
		);
	}
	if ( ctr_whatsapp_url() ) {
		$ctr_cards[] = array(
			'icon'  => 'whatsapp',
			'title' => __( 'WhatsApp', 'contronic' ),
			'lines' => array( '<a href="' . esc_url( ctr_whatsapp_url() ) . '" target="_blank" rel="noopener">' . esc_html__( 'Start a chat', 'contronic' ) . '</a>' ),
		);
	}
	if ( $ctr_c['address'] || $ctr_c['city'] ) {
		$ctr_lines = array( nl2br( esc_html( trim( $ctr_c['address'] . "\n" . $ctr_c['city'] . ( $ctr_c['country'] ? ', ' . $ctr_c['country'] : '' ) ) ) ) );
		if ( $ctr_c['map_link'] ) {
			$ctr_lines[] = '<a href="' . esc_url( $ctr_c['map_link'] ) . '" target="_blank" rel="noopener">' . esc_html__( 'Get directions', 'contronic' ) . '</a>';
		}
		$ctr_cards[] = array(
			'icon'  => 'pin',
			'title' => __( 'Visit us', 'contronic' ),
			'lines' => $ctr_lines,
		);
	}
	if ( $ctr_c['hours'] ) {
		$ctr_cards[] = array(
			'icon'  => 'clock',
			'title' => __( 'Working hours', 'contronic' ),
			'lines' => array( nl2br( esc_html( $ctr_c['hours'] ) ) ),
		);
	}
	?>
	<section class="section section--light">
		<div class="container contact">
			<div class="contact__info">
				<?php if ( '' !== trim( get_the_content() ) ) : ?>
					<div class="prose reveal"><?php the_content(); ?></div>
				<?php endif; ?>
				<?php if ( $ctr_cards ) : ?>
					<ul class="info-cards">
						<?php foreach ( $ctr_cards as $ctr_card ) : ?>
							<li class="info-card reveal">
								<span class="info-card__icon"><?php ctr_the_icon( $ctr_card['icon'] ); ?></span>
								<div>
									<h2 class="info-card__title"><?php echo esc_html( $ctr_card['title'] ); ?></h2>
									<?php foreach ( $ctr_card['lines'] as $ctr_line ) : ?>
										<p><?php echo wp_kses_post( $ctr_line ); ?></p>
									<?php endforeach; ?>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php elseif ( current_user_can( 'edit_theme_options' ) ) : ?>
					<p class="empty-state__admin"><?php esc_html_e( 'Editor note: add phone, email and address in Appearance → Customize → Company & contact details. They will appear here.', 'contronic' ); ?></p>
				<?php endif; ?>
				<?php ctr_social_links( 'social social--dark' ); ?>
			</div>
			<div class="contact__form reveal" id="inquiry">
				<?php ctr_inquiry_form( array( 'form' => 'contact', 'heading' => __( 'Tell us about your project', 'contronic' ) ) ); ?>
			</div>
		</div>
	</section>

	<?php if ( $ctr_c['map_embed'] && 0 === strpos( $ctr_c['map_embed'], 'https://www.google.com/maps/embed' ) ) : ?>
		<section class="map" aria-label="<?php esc_attr_e( 'Office location map', 'contronic' ); ?>">
			<iframe src="<?php echo esc_url( $ctr_c['map_embed'] ); ?>" title="<?php esc_attr_e( 'Office location', 'contronic' ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
		</section>
	<?php endif; ?>
	<?php
endwhile;

get_footer();

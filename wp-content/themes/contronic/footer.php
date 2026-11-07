<?php
/**
 * Site footer.
 *
 * @package Contronic
 */

$ctr_c        = ctr_company();
$ctr_services = ctr_items( 'ctr_service', 8 );
?>
</main>

<footer class="site-footer">
	<div class="site-footer__grid-lines" aria-hidden="true"></div>
	<div class="container">
		<div class="site-footer__top">
			<div class="site-footer__brand">
				<?php ctr_logo( 'footer' ); ?>
				<?php if ( ctr_mod( 'footer_about' ) ) : ?>
					<p><?php echo esc_html( ctr_mod( 'footer_about' ) ); ?></p>
				<?php endif; ?>
				<?php ctr_social_links(); ?>
			</div>

			<?php if ( $ctr_services ) : ?>
				<nav class="site-footer__col" aria-label="<?php echo esc_attr( ctr_mod( 'footer_services_title' ) ); ?>">
					<h2 class="site-footer__title"><?php echo esc_html( ctr_mod( 'footer_services_title' ) ); ?></h2>
					<ul>
						<?php foreach ( $ctr_services as $ctr_service ) : ?>
							<li><a href="<?php echo esc_url( get_permalink( $ctr_service ) ); ?>"><?php echo esc_html( get_the_title( $ctr_service ) ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endif; ?>

			<nav class="site-footer__col" aria-label="<?php echo esc_attr( ctr_mod( 'footer_links_title' ) ); ?>">
				<h2 class="site-footer__title"><?php echo esc_html( ctr_mod( 'footer_links_title' ) ); ?></h2>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => has_nav_menu( 'footer' ) ? 'footer' : 'primary',
						'container'      => false,
						'menu_class'     => 'site-footer__links',
						'depth'          => 1,
						'fallback_cb'    => 'ctr_menu_fallback',
					)
				);
				?>
			</nav>

			<div class="site-footer__col site-footer__contact">
				<h2 class="site-footer__title"><?php esc_html_e( 'Contact', 'contronic' ); ?></h2>
				<ul class="contact-list">
					<?php if ( $ctr_c['address'] || $ctr_c['city'] ) : ?>
						<li><?php ctr_the_icon( 'pin', array( 'size' => 18 ) ); ?><span><?php echo nl2br( esc_html( trim( $ctr_c['address'] . "\n" . $ctr_c['city'] ) ) ); ?></span></li>
					<?php endif; ?>
					<?php foreach ( array_filter( array( $ctr_c['phone'], $ctr_c['phone2'] ) ) as $ctr_phone ) : ?>
						<li><?php ctr_the_icon( 'phone', array( 'size' => 18 ) ); ?><a href="<?php echo esc_attr( ctr_tel( $ctr_phone ) ); ?>"><?php echo esc_html( $ctr_phone ); ?></a></li>
					<?php endforeach; ?>
					<?php foreach ( array_filter( array( $ctr_c['email'], $ctr_c['email2'] ) ) as $ctr_email ) : ?>
						<li><?php ctr_the_icon( 'mail', array( 'size' => 18 ) ); ?><a href="mailto:<?php echo esc_attr( antispambot( $ctr_email ) ); ?>"><?php echo esc_html( antispambot( $ctr_email ) ); ?></a></li>
					<?php endforeach; ?>
					<?php if ( $ctr_c['hours'] ) : ?>
						<li><?php ctr_the_icon( 'clock', array( 'size' => 18 ) ); ?><span><?php echo nl2br( esc_html( $ctr_c['hours'] ) ); ?></span></li>
					<?php endif; ?>
				</ul>
				<?php
				$ctr_contact = get_page_by_path( 'contact' );
				if ( $ctr_contact ) {
					ctr_button( __( 'Send an inquiry', 'contronic' ), get_permalink( $ctr_contact ), 'line' );
				}
				?>
			</div>
		</div>

		<div class="site-footer__bottom">
			<p><?php echo esc_html( ctr_copyright() ); ?></p>
			<?php
			if ( has_nav_menu( 'legal' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'legal',
						'container'      => 'nav',
						'container_class' => 'site-footer__legal',
						'container_aria_label' => __( 'Legal', 'contronic' ),
						'menu_class'     => 'menu',
						'depth'          => 1,
					)
				);
			}
			?>
		</div>
	</div>
</footer>

<?php if ( ctr_whatsapp_url() ) : ?>
	<a class="whatsapp-fab" href="<?php echo esc_url( ctr_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php ctr_the_icon( 'whatsapp', array( 'size' => 26 ) ); ?><span class="screen-reader-text"><?php esc_html_e( 'Chat on WhatsApp', 'contronic' ); ?></span></a>
<?php endif; ?>

<dialog class="lightbox" data-lightbox-dialog aria-label="<?php esc_attr_e( 'Image viewer', 'contronic' ); ?>">
	<figure class="lightbox__figure"><img src="data:image/gif;base64,R0lGODlhAQABAAAAACw=" alt=""><figcaption></figcaption></figure>
	<button class="icon-btn lightbox__close" type="button" data-lightbox-close aria-label="<?php esc_attr_e( 'Close', 'contronic' ); ?>"><?php ctr_the_icon( 'close' ); ?></button>
	<button class="icon-btn lightbox__prev" type="button" data-lightbox-prev aria-label="<?php esc_attr_e( 'Previous image', 'contronic' ); ?>"><?php ctr_the_icon( 'chevron-left' ); ?></button>
	<button class="icon-btn lightbox__next" type="button" data-lightbox-next aria-label="<?php esc_attr_e( 'Next image', 'contronic' ); ?>"><?php ctr_the_icon( 'chevron-right' ); ?></button>
</dialog>

<?php wp_footer(); ?>
</body>
</html>

<?php
/**
 * Site header.
 *
 * @package Contronic
 */

$ctr_c = ctr_company();
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'contronic' ); ?></a>
<div class="scroll-progress" aria-hidden="true"><span></span></div>

<header class="site-header" data-header>
	<?php if ( ctr_mod( 'topbar_show' ) && ( $ctr_c['phone'] || $ctr_c['email'] || $ctr_c['hours'] ) ) : ?>
		<div class="topbar">
			<div class="container topbar__inner">
				<ul class="topbar__contact">
					<?php if ( $ctr_c['phone'] ) : ?>
						<li><a href="<?php echo esc_attr( ctr_tel( $ctr_c['phone'] ) ); ?>"><?php ctr_the_icon( 'phone', array( 'size' => 14 ) ); ?><?php echo esc_html( $ctr_c['phone'] ); ?></a></li>
					<?php endif; ?>
					<?php if ( $ctr_c['email'] ) : ?>
						<li><a href="mailto:<?php echo esc_attr( antispambot( $ctr_c['email'] ) ); ?>"><?php ctr_the_icon( 'mail', array( 'size' => 14 ) ); ?><?php echo esc_html( antispambot( $ctr_c['email'] ) ); ?></a></li>
					<?php endif; ?>
					<?php if ( $ctr_c['hours'] ) : ?>
						<li class="topbar__hours"><?php ctr_the_icon( 'clock', array( 'size' => 14 ) ); ?><?php echo esc_html( strtok( $ctr_c['hours'], "\n" ) ); ?></li>
					<?php endif; ?>
				</ul>
				<?php ctr_social_links( 'social social--small' ); ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="container site-header__bar">
		<?php ctr_logo( 'header' ); ?>

		<nav class="site-nav" id="site-nav" aria-label="<?php esc_attr_e( 'Main', 'contronic' ); ?>" data-nav>
			<div class="site-nav__head">
				<?php ctr_logo( 'drawer' ); ?>
				<button class="icon-btn site-nav__close" type="button" data-nav-close aria-label="<?php esc_attr_e( 'Close menu', 'contronic' ); ?>"><?php ctr_the_icon( 'close' ); ?></button>
			</div>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'menu',
					'depth'          => 2,
					'fallback_cb'    => 'ctr_menu_fallback',
				)
			);
			?>
			<div class="site-nav__foot">
				<?php ctr_button( ctr_mod( 'header_cta_text' ), ctr_mod( 'header_cta_link' ), 'primary' ); ?>
				<?php if ( $ctr_c['phone'] ) : ?>
					<a class="site-nav__phone" href="<?php echo esc_attr( ctr_tel( $ctr_c['phone'] ) ); ?>"><?php ctr_the_icon( 'phone', array( 'size' => 18 ) ); ?><?php echo esc_html( $ctr_c['phone'] ); ?></a>
				<?php endif; ?>
				<?php ctr_social_links(); ?>
			</div>
		</nav>

		<div class="site-header__actions">
			<?php ctr_button( ctr_mod( 'header_cta_text' ), ctr_mod( 'header_cta_link' ), 'primary', array( 'class' => 'btn--sm site-header__cta' ) ); ?>
			<button class="icon-btn site-header__toggle" type="button" aria-controls="site-nav" aria-expanded="false" data-nav-toggle>
				<?php ctr_the_icon( 'menu' ); ?><span class="screen-reader-text"><?php esc_html_e( 'Menu', 'contronic' ); ?></span>
			</button>
		</div>
	</div>
</header>
<div class="nav-scrim" data-nav-close hidden></div>

<main id="main" class="site-main" tabindex="-1">

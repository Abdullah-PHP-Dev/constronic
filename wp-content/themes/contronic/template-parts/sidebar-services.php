<?php
/**
 * Sidebar: other services + contact card.
 *
 * @package Contronic
 */

$ctr_current = isset( $args['current'] ) ? (int) $args['current'] : 0;
$ctr_c       = ctr_company();
$ctr_all     = ctr_items( 'ctr_service' );
?>
<div class="sticky-stack">
	<?php if ( count( $ctr_all ) > 1 ) : ?>
		<nav class="side-card" aria-label="<?php esc_attr_e( 'All services', 'contronic' ); ?>">
			<h2 class="side-card__title"><?php esc_html_e( 'All services', 'contronic' ); ?></h2>
			<ul class="side-nav">
				<?php foreach ( $ctr_all as $ctr_s ) : ?>
					<li>
						<a href="<?php echo esc_url( get_permalink( $ctr_s ) ); ?>"<?php echo $ctr_s->ID === $ctr_current ? ' aria-current="page"' : ''; ?>>
							<?php ctr_the_icon( ctr_meta( 'icon', $ctr_s->ID, 'building' ), array( 'size' => 18 ) ); ?>
							<span><?php echo esc_html( get_the_title( $ctr_s ) ); ?></span>
							<?php ctr_the_icon( 'chevron-right', array( 'size' => 16, 'class' => 'side-nav__chev' ) ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>
	<?php endif; ?>

	<div class="side-card side-card--accent">
		<h2 class="side-card__title"><?php esc_html_e( 'Need a quotation?', 'contronic' ); ?></h2>
		<p><?php esc_html_e( 'Share your drawings or requirements and we will come back with next steps.', 'contronic' ); ?></p>
		<?php ctr_button( __( 'Send an inquiry', 'contronic' ), '#inquiry', 'light' ); ?>
		<?php if ( $ctr_c['phone'] ) : ?>
			<a class="side-card__phone" href="<?php echo esc_attr( ctr_tel( $ctr_c['phone'] ) ); ?>"><?php ctr_the_icon( 'phone', array( 'size' => 18 ) ); ?><?php echo esc_html( $ctr_c['phone'] ); ?></a>
		<?php endif; ?>
	</div>
</div>

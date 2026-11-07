<?php
/**
 * Homepage / About: strengths.
 *
 * @package Contronic
 */

$ctr_items = ctr_lines( ctr_mod( 'strengths_items' ), 2 );
if ( ! $ctr_items ) {
	return;
}
$ctr_icons = array( 'layers', 'shield', 'check', 'gear', 'clipboard', 'bolt' );
?>
<section class="section section--steel strengths" id="why">
	<div class="container strengths__grid">
		<div class="strengths__head reveal">
			<?php ctr_kicker( ctr_mod( 'strengths_kicker' ) ); ?>
			<?php ctr_heading( ctr_mod( 'strengths_heading' ), ctr_mod( 'strengths_span' ) ); ?>
		</div>
		<ol class="strengths__list">
			<?php foreach ( $ctr_items as $ctr_i => $ctr_item ) : ?>
				<li class="strength reveal">
					<span class="strength__icon"><?php ctr_the_icon( $ctr_icons[ $ctr_i % count( $ctr_icons ) ] ); ?></span>
					<span class="strength__num"><?php echo esc_html( sprintf( '%02d', $ctr_i + 1 ) ); ?></span>
					<h3><?php echo esc_html( $ctr_item[0] ); ?></h3>
					<?php if ( $ctr_item[1] ) : ?>
						<p><?php echo esc_html( $ctr_item[1] ); ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>

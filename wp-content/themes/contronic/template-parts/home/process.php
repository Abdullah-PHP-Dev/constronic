<?php
/**
 * Homepage / About: delivery process.
 *
 * @package Contronic
 */

$ctr_steps = ctr_lines( ctr_mod( 'process_steps' ), 2 );
if ( ! $ctr_steps ) {
	return;
}
?>
<section class="section section--light process" id="process">
	<div class="container">
		<?php
		ctr_section_head(
			array(
				'kicker'  => ctr_mod( 'process_kicker' ),
				'heading' => ctr_mod( 'process_heading' ),
				'span'    => ctr_mod( 'process_span' ),
			)
		);
		?>
		<ol class="steps" style="--steps: <?php echo (int) count( $ctr_steps ); ?>">
			<?php foreach ( $ctr_steps as $ctr_i => $ctr_step ) : ?>
				<li class="step reveal" style="--i: <?php echo (int) $ctr_i; ?>">
					<span class="step__num"><?php echo esc_html( sprintf( '%02d', $ctr_i + 1 ) ); ?></span>
					<h3><?php echo esc_html( $ctr_step[0] ); ?></h3>
					<?php if ( $ctr_step[1] ) : ?>
						<p><?php echo esc_html( $ctr_step[1] ); ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>

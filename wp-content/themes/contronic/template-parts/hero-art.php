<?php
/**
 * Original hero illustration: building under construction with live MEP service runs.
 * Used until a real photograph is set on a Hero Slide. Pure SVG, ~10 KB, no requests.
 *
 * @package Contronic
 */

$ctr_variant = isset( $args['variant'] ) ? (int) $args['variant'] % 2 : 0;
$ctr_uid     = 'h' . $ctr_variant . wp_rand( 100, 999 );

// Building geometry.
$ctr_left   = 1030;
$ctr_right  = 1640;
$ctr_ground = 930;
$ctr_floor  = 72;
$ctr_floors = 9;
$ctr_bays   = 6;
$ctr_bay    = ( $ctr_right - $ctr_left ) / $ctr_bays;
$ctr_top    = $ctr_ground - $ctr_floor * $ctr_floors;
$ctr_built  = 6; // Floors with façade.

$ctr_frame = '';
for ( $ctr_f = 0; $ctr_f <= $ctr_floors; $ctr_f++ ) {
	$ctr_y      = $ctr_ground - $ctr_f * $ctr_floor;
	$ctr_frame .= sprintf( '<path d="M%d %dH%d"/>', $ctr_left - 14, $ctr_y, $ctr_right + 14 );
}
for ( $ctr_b = 0; $ctr_b <= $ctr_bays; $ctr_b++ ) {
	$ctr_x      = $ctr_left + $ctr_b * $ctr_bay;
	$ctr_frame .= sprintf( '<path d="M%1$.1f %2$dV%3$d"/>', $ctr_x, $ctr_ground, $ctr_top );
}

// Glazing on the completed floors (some lit).
$ctr_glass = '';
$ctr_lit   = '';
for ( $ctr_f = 0; $ctr_f < $ctr_built; $ctr_f++ ) {
	for ( $ctr_b = 0; $ctr_b < $ctr_bays; $ctr_b++ ) {
		$ctr_x = $ctr_left + $ctr_b * $ctr_bay + 8;
		$ctr_y = $ctr_ground - ( $ctr_f + 1 ) * $ctr_floor + 10;
		$ctr_r = sprintf( '<rect x="%.1f" y="%d" width="%.1f" height="%d"/>', $ctr_x, $ctr_y, $ctr_bay - 16, $ctr_floor - 20 );
		if ( 0 === ( $ctr_f * 7 + $ctr_b * 3 + $ctr_variant ) % 5 ) {
			$ctr_lit .= $ctr_r;
		} else {
			$ctr_glass .= $ctr_r;
		}
	}
}

// Services: risers + floor branches.
$ctr_riser_a = $ctr_left + $ctr_bay * 1.5;
$ctr_riser_b = $ctr_left + $ctr_bay * 4.5;
$ctr_nodes   = '';
$ctr_ducts   = '';
for ( $ctr_f = 1; $ctr_f < $ctr_floors; $ctr_f++ ) {
	$ctr_y      = $ctr_ground - $ctr_f * $ctr_floor + 18;
	$ctr_nodes .= sprintf( '<circle cx="%.1f" cy="%d" r="4"/><circle cx="%.1f" cy="%d" r="4"/>', $ctr_riser_a, $ctr_y, $ctr_riser_b, $ctr_y );
	if ( $ctr_f % 2 ) {
		$ctr_ducts .= sprintf( '<path d="M%.1f %dH%.1f"/>', $ctr_riser_a, $ctr_y, $ctr_riser_b );
	} else {
		$ctr_ducts .= sprintf( '<path d="M%.1f %dH%.1f V%d"/>', $ctr_riser_b, $ctr_y, $ctr_right - 20, $ctr_y + 24 );
		$ctr_ducts .= sprintf( '<path d="M%.1f %dH%.1f V%d"/>', $ctr_riser_a, $ctr_y, $ctr_left + 20, $ctr_y + 24 );
	}
}

// Tower crane.
$ctr_mast_x = 0 === $ctr_variant ? 1760 : 940;
$ctr_mast_t = 150;
$ctr_lattice = '';
for ( $ctr_y = $ctr_ground; $ctr_y > $ctr_mast_t + 30; $ctr_y -= 40 ) {
	$ctr_lattice .= sprintf( 'M%1$d %2$dL%3$d %4$dM%3$d %2$dL%1$d %4$d', $ctr_mast_x - 14, $ctr_y, $ctr_mast_x + 14, $ctr_y - 40 );
}
$ctr_jib_l  = 0 === $ctr_variant ? 1120 : 700;
$ctr_jib_r  = 0 === $ctr_variant ? 1960 : 1500;
$ctr_hook_x = 0 === $ctr_variant ? 1330 : 1290;

$ctr_glow_x = 0 === $ctr_variant ? 1420 : 1260;
?>
<svg class="hero-art hero-art--<?php echo (int) $ctr_variant; ?>" viewBox="0 0 1920 1080" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
	<defs>
		<linearGradient id="<?php echo esc_attr( $ctr_uid ); ?>bg" x1="0" y1="0" x2="0" y2="1">
			<stop offset="0" stop-color="<?php echo 0 === $ctr_variant ? '#0b1c28' : '#0a1822'; ?>"/>
			<stop offset=".7" stop-color="#0c2130"/>
			<stop offset="1" stop-color="#08131c"/>
		</linearGradient>
		<radialGradient id="<?php echo esc_attr( $ctr_uid ); ?>sun" cx="<?php echo (int) $ctr_glow_x; ?>" cy="360" r="760" gradientUnits="userSpaceOnUse">
			<stop offset="0" style="stop-color:var(--c-accent-2,#f2a93b)" stop-opacity=".34"/>
			<stop offset=".45" style="stop-color:var(--c-accent-2,#f2a93b)" stop-opacity=".07"/>
			<stop offset="1" style="stop-color:var(--c-accent-2,#f2a93b)" stop-opacity="0"/>
		</radialGradient>
		<radialGradient id="<?php echo esc_attr( $ctr_uid ); ?>teal" cx="1330" cy="760" r="520" gradientUnits="userSpaceOnUse">
			<stop offset="0" style="stop-color:var(--c-accent,#19b3a6)" stop-opacity=".22"/>
			<stop offset="1" style="stop-color:var(--c-accent,#19b3a6)" stop-opacity="0"/>
		</radialGradient>
		<pattern id="<?php echo esc_attr( $ctr_uid ); ?>grid" width="48" height="48" patternUnits="userSpaceOnUse">
			<path d="M48 0H0V48" fill="none" stroke="#a9cbd6" stroke-opacity=".06"/>
		</pattern>
		<linearGradient id="<?php echo esc_attr( $ctr_uid ); ?>glass" x1="0" y1="0" x2="1" y2="1">
			<stop offset="0" stop-color="#5f8fa3" stop-opacity=".22"/>
			<stop offset="1" stop-color="#1b3a4b" stop-opacity=".12"/>
		</linearGradient>
	</defs>

	<rect width="1920" height="1080" fill="url(#<?php echo esc_attr( $ctr_uid ); ?>bg)"/>
	<rect width="1920" height="1080" fill="url(#<?php echo esc_attr( $ctr_uid ); ?>sun)"/>
	<rect width="1920" height="1080" fill="url(#<?php echo esc_attr( $ctr_uid ); ?>grid)"/>

	<!-- distant skyline -->
	<g fill="#0f2635" opacity=".9">
		<path d="M640 930V700h70v-60h40v60h60V560h90v370Z"/>
		<path d="M1660 930V610h60v-40h50v40h40v320Z"/>
		<path d="M1820 930V720h100v210Z"/>
		<path d="M880 930V780h120v150Z"/>
	</g>

	<!-- façade glazing -->
	<g fill="url(#<?php echo esc_attr( $ctr_uid ); ?>glass)"><?php echo $ctr_glass; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- generated numeric markup. ?></g>
	<g style="fill:var(--c-accent-2,#f2a93b)" fill-opacity=".22"><?php echo $ctr_lit; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></g>

	<!-- structural frame -->
	<g fill="none" stroke="#9fc0cc" stroke-opacity=".38" stroke-width="2"><?php echo $ctr_frame; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></g>
	<rect width="1920" height="1080" fill="url(#<?php echo esc_attr( $ctr_uid ); ?>teal)"/>

	<!-- MEP risers and branches -->
	<g fill="none" stroke-linecap="round">
		<path class="flow flow--slow" d="M<?php echo esc_attr( $ctr_riser_a ); ?> <?php echo (int) $ctr_ground; ?>V<?php echo (int) $ctr_top + 30; ?>" style="stroke:var(--c-accent,#19b3a6)" stroke-width="4"/>
		<path class="flow" d="M<?php echo esc_attr( $ctr_riser_b ); ?> <?php echo (int) $ctr_ground; ?>V<?php echo (int) $ctr_top + 30; ?>" style="stroke:var(--c-accent,#19b3a6)" stroke-width="4"/>
		<g class="flow flow--rev" style="stroke:var(--c-accent-2,#f2a93b)" stroke-width="2.5" stroke-opacity=".85"><?php echo $ctr_ducts; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></g>
	</g>
	<g style="fill:var(--c-accent,#19b3a6)"><?php echo $ctr_nodes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></g>

	<!-- tower crane -->
	<g fill="none" stroke="#c9dbe2" stroke-opacity=".55" stroke-width="2">
		<path d="M<?php echo (int) $ctr_mast_x - 14; ?> <?php echo (int) $ctr_ground; ?>V<?php echo (int) $ctr_mast_t; ?>M<?php echo (int) $ctr_mast_x + 14; ?> <?php echo (int) $ctr_ground; ?>V<?php echo (int) $ctr_mast_t; ?>"/>
		<path stroke-opacity=".3" d="<?php echo esc_attr( $ctr_lattice ); ?>"/>
		<path d="M<?php echo (int) $ctr_jib_l; ?> <?php echo (int) $ctr_mast_t + 20; ?>H<?php echo (int) $ctr_jib_r; ?>M<?php echo (int) $ctr_jib_l; ?> <?php echo (int) $ctr_mast_t + 44; ?>H<?php echo (int) $ctr_jib_r; ?>"/>
		<path d="M<?php echo (int) $ctr_mast_x; ?> <?php echo (int) $ctr_mast_t - 60; ?>L<?php echo (int) $ctr_jib_l; ?> <?php echo (int) $ctr_mast_t + 20; ?>M<?php echo (int) $ctr_mast_x; ?> <?php echo (int) $ctr_mast_t - 60; ?>L<?php echo (int) $ctr_jib_r; ?> <?php echo (int) $ctr_mast_t + 20; ?>M<?php echo (int) $ctr_mast_x - 14; ?> <?php echo (int) $ctr_mast_t; ?>L<?php echo (int) $ctr_mast_x; ?> <?php echo (int) $ctr_mast_t - 60; ?>L<?php echo (int) $ctr_mast_x + 14; ?> <?php echo (int) $ctr_mast_t; ?>"/>
		<path stroke-dasharray="2 8" d="M<?php echo (int) $ctr_hook_x; ?> <?php echo (int) $ctr_mast_t + 44; ?>V<?php echo (int) $ctr_top - 70; ?>"/>
	</g>
	<rect x="<?php echo (int) $ctr_hook_x - 70; ?>" y="<?php echo (int) $ctr_top - 70; ?>" width="140" height="12" rx="2" style="fill:var(--c-accent-2,#f2a93b)" fill-opacity=".85"/>
	<rect x="<?php echo (int) $ctr_mast_x - 30; ?>" y="<?php echo (int) $ctr_mast_t; ?>" width="60" height="34" rx="3" fill="#183444" stroke="#c9dbe2" stroke-opacity=".5"/>

	<!-- annotations -->
	<g fill="none" stroke="#a9cbd6" stroke-opacity=".35" stroke-width="1.2">
		<path d="M<?php echo (int) $ctr_left; ?> <?php echo (int) $ctr_ground + 46; ?>H<?php echo (int) $ctr_right; ?>M<?php echo (int) $ctr_left; ?> <?php echo (int) $ctr_ground + 36; ?>v20M<?php echo (int) $ctr_right; ?> <?php echo (int) $ctr_ground + 36; ?>v20"/>
		<path d="M<?php echo (int) $ctr_right + 50; ?> <?php echo (int) $ctr_ground; ?>V<?php echo (int) $ctr_top; ?>M<?php echo (int) $ctr_right + 40; ?> <?php echo (int) $ctr_ground; ?>h20M<?php echo (int) $ctr_right + 40; ?> <?php echo (int) $ctr_top; ?>h20"/>
	</g>
	<g font-family="ui-monospace, Menlo, monospace" font-size="15" letter-spacing="2" fill="#a9cbd6" fill-opacity=".55">
		<text x="<?php echo (int) $ctr_riser_a + 14; ?>" y="<?php echo (int) $ctr_top + 46; ?>">RISER A · ELEC</text>
		<text x="<?php echo (int) $ctr_riser_b + 14; ?>" y="<?php echo (int) $ctr_top + 46; ?>">RISER B · HVAC</text>
		<text x="<?php echo (int) $ctr_left; ?>" y="<?php echo (int) $ctr_ground + 78; ?>">GRID A — G · L00–L09</text>
	</g>

	<!-- ground -->
	<path d="M0 <?php echo (int) $ctr_ground; ?>H1920" stroke="#a9cbd6" stroke-opacity=".3" stroke-width="2"/>
	<rect y="<?php echo (int) $ctr_ground; ?>" width="1920" height="<?php echo 1080 - (int) $ctr_ground; ?>" fill="#08131c" fill-opacity=".7"/>
</svg>

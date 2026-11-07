<?php
/**
 * Partner logo grid.
 *
 * @package Contronic
 */

$ctr_posts = isset( $args['posts'] ) ? $args['posts'] : array();
$ctr_names = ! empty( $args['names'] );
?>
<ul class="logo-grid<?php echo $ctr_names ? ' logo-grid--named' : ''; ?>">
	<?php foreach ( $ctr_posts as $ctr_post ) : ?>
		<?php
		$ctr_url  = ctr_meta( 'url', $ctr_post->ID );
		$ctr_name = get_the_title( $ctr_post );
		$ctr_tag  = $ctr_url ? 'a' : 'div';
		?>
		<li class="reveal">
			<<?php echo esc_html( $ctr_tag ); ?> class="logo-grid__item"<?php echo $ctr_url ? ' href="' . esc_url( $ctr_url ) . '" target="_blank" rel="noopener"' : ''; ?>>
				<?php if ( has_post_thumbnail( $ctr_post ) ) : ?>
					<?php echo get_the_post_thumbnail( $ctr_post, 'ctr-logo', array( 'alt' => $ctr_name, 'loading' => 'lazy' ) ); ?>
				<?php else : ?>
					<span class="logo-grid__text"><?php echo esc_html( $ctr_name ); ?></span>
				<?php endif; ?>
			</<?php echo esc_html( $ctr_tag ); ?>>
			<?php if ( $ctr_names ) : ?>
				<p class="logo-grid__name"><?php echo esc_html( $ctr_name ); ?></p>
				<?php if ( has_excerpt( $ctr_post ) ) : ?>
					<p class="logo-grid__desc"><?php echo esc_html( get_the_excerpt( $ctr_post ) ); ?></p>
				<?php endif; ?>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>

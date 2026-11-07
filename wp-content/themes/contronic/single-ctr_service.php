<?php
/**
 * Service detail.
 *
 * @package Contronic
 */

get_header();

while ( have_posts() ) :
	the_post();
	$ctr_id      = get_the_ID();
	$ctr_icon    = ctr_meta( 'icon', $ctr_id, 'building' );
	$ctr_scope   = ctr_lines( ctr_meta( 'scope' ) );
	$ctr_process = ctr_lines( ctr_meta( 'process' ), 2 );
	$ctr_faqs    = ctr_lines( ctr_meta( 'faqs' ), 2 );
	$ctr_gallery = ctr_ids( ctr_meta( 'gallery' ) );
	$ctr_terms   = get_the_terms( $ctr_id, 'ctr_service_cat' );
	$ctr_title   = get_the_title();

	ctr_page_hero(
		array(
			'kicker'   => $ctr_terms && ! is_wp_error( $ctr_terms ) ? $ctr_terms[0]->name : __( 'Service', 'contronic' ),
			'title'    => $ctr_title,
			'text'     => ctr_meta( 'tagline', $ctr_id, ctr_excerpt( $ctr_id, 30 ) ),
			'image_id' => (int) get_post_thumbnail_id(),
			'icon'     => $ctr_icon,
			'seed'     => $ctr_id,
			'actions'  => static function () {
				ctr_button( __( 'Request a quote', 'contronic' ), '#inquiry', 'primary' );
			},
		)
	);
	?>
	<section class="section section--light">
		<div class="container layout-sidebar">
			<div class="layout-sidebar__main">
				<div class="prose reveal">
					<?php the_content(); ?>
				</div>

				<?php if ( $ctr_scope ) : ?>
					<div class="block reveal">
						<h2 class="block__title"><?php esc_html_e( 'Scope of work', 'contronic' ); ?></h2>
						<ul class="checklist">
							<?php foreach ( $ctr_scope as $ctr_item ) : ?>
								<li><?php ctr_the_icon( 'check', array( 'size' => 18 ) ); ?><span><?php echo esc_html( $ctr_item ); ?></span></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php if ( $ctr_process ) : ?>
					<div class="block reveal">
						<h2 class="block__title"><?php esc_html_e( 'How we deliver', 'contronic' ); ?></h2>
						<ol class="timeline">
							<?php foreach ( $ctr_process as $ctr_i => $ctr_step ) : ?>
								<li>
									<span class="timeline__num"><?php echo esc_html( sprintf( '%02d', $ctr_i + 1 ) ); ?></span>
									<div>
										<h3><?php echo esc_html( $ctr_step[0] ); ?></h3>
										<?php if ( $ctr_step[1] ) : ?>
											<p><?php echo esc_html( $ctr_step[1] ); ?></p>
										<?php endif; ?>
									</div>
								</li>
							<?php endforeach; ?>
						</ol>
					</div>
				<?php endif; ?>

				<?php if ( $ctr_gallery ) : ?>
					<div class="block reveal">
						<h2 class="block__title"><?php esc_html_e( 'Gallery', 'contronic' ); ?></h2>
						<?php ctr_gallery( $ctr_gallery ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $ctr_faqs ) : ?>
					<div class="block reveal">
						<h2 class="block__title"><?php esc_html_e( 'Frequently asked questions', 'contronic' ); ?></h2>
						<div class="faq">
							<?php foreach ( $ctr_faqs as $ctr_faq ) : ?>
								<details>
									<summary><?php echo esc_html( $ctr_faq[0] ); ?><?php ctr_the_icon( 'plus', array( 'size' => 18 ) ); ?></summary>
									<p><?php echo esc_html( $ctr_faq[1] ); ?></p>
								</details>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<aside class="layout-sidebar__aside">
				<?php get_template_part( 'template-parts/sidebar-services', null, array( 'current' => $ctr_id ) ); ?>
			</aside>
		</div>
	</section>

	<?php
	$ctr_related = ctr_items( 'ctr_project', 3, array( 'meta_query' => array( array( 'key' => '_ctr_services', 'value' => (string) $ctr_id, 'compare' => 'LIKE' ) ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	$ctr_related = array_filter(
		$ctr_related,
		static function ( $p ) use ( $ctr_id ) {
			return in_array( $ctr_id, ctr_ids( get_post_meta( $p->ID, '_ctr_services', true ) ), true );
		}
	);
	if ( $ctr_related ) :
		?>
		<section class="section section--dark">
			<div class="container">
				<?php ctr_section_head( array( 'kicker' => __( 'Related work', 'contronic' ), 'heading' => __( 'Projects', 'contronic' ), 'span' => __( 'using this service.', 'contronic' ) ) ); ?>
				<ul class="card-grid card-grid--3">
					<?php foreach ( $ctr_related as $ctr_p ) : ?>
						<li><?php get_template_part( 'template-parts/cards/project', null, array( 'post' => $ctr_p ) ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
	<?php endif; ?>

	<?php
	get_template_part( 'template-parts/home/cta', null, array( 'service' => $ctr_title ) );
endwhile;

get_footer();

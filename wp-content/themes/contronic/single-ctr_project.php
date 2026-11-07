<?php
/**
 * Project detail.
 *
 * @package Contronic
 */

get_header();

while ( have_posts() ) :
	the_post();
	$ctr_id       = get_the_ID();
	$ctr_gallery  = ctr_ids( ctr_meta( 'gallery' ) );
	$ctr_scope    = ctr_lines( ctr_meta( 'scope' ) );
	$ctr_terms    = get_the_terms( $ctr_id, 'ctr_project_cat' );
	$ctr_statuses = array(
		'completed' => __( 'Completed', 'contronic' ),
		'ongoing'   => __( 'Ongoing', 'contronic' ),
	);
	$ctr_status   = ctr_meta( 'status' );
	$ctr_facts    = array_filter(
		array(
			__( 'Client', 'contronic' )   => ctr_meta( 'client' ),
			__( 'Location', 'contronic' ) => ctr_meta( 'location' ),
			__( 'Year', 'contronic' )     => ctr_meta( 'year' ),
			__( 'Status', 'contronic' )   => isset( $ctr_statuses[ $ctr_status ] ) ? $ctr_statuses[ $ctr_status ] : '',
			__( 'Sector', 'contronic' )   => $ctr_terms && ! is_wp_error( $ctr_terms ) ? implode( ', ', wp_list_pluck( $ctr_terms, 'name' ) ) : '',
		)
	);
	$ctr_services = array_filter( array_map( 'get_post', ctr_ids( ctr_meta( 'services' ) ) ) );
	$ctr_services = array_filter(
		$ctr_services,
		static function ( $p ) {
			return 'ctr_service' === $p->post_type && 'publish' === $p->post_status;
		}
	);

	ctr_page_hero(
		array(
			'kicker'   => __( 'Project', 'contronic' ),
			'title'    => get_the_title(),
			'text'     => ctr_excerpt( $ctr_id, 30 ),
			'image_id' => (int) get_post_thumbnail_id(),
			'icon'     => 'crane',
			'seed'     => $ctr_id,
		)
	);
	?>
	<?php if ( $ctr_facts ) : ?>
		<div class="fact-bar">
			<div class="container">
				<dl class="fact-bar__list">
					<?php foreach ( $ctr_facts as $ctr_label => $ctr_value ) : ?>
						<div><dt><?php echo esc_html( $ctr_label ); ?></dt><dd><?php echo esc_html( $ctr_value ); ?></dd></div>
					<?php endforeach; ?>
				</dl>
			</div>
		</div>
	<?php endif; ?>

	<section class="section section--light">
		<div class="container layout-split">
			<div class="prose reveal"><?php the_content(); ?></div>
			<?php if ( $ctr_scope || $ctr_services ) : ?>
				<aside class="side-card reveal">
					<?php if ( $ctr_scope ) : ?>
						<h2 class="side-card__title"><?php esc_html_e( 'Scope delivered', 'contronic' ); ?></h2>
						<ul class="checklist checklist--compact">
							<?php foreach ( $ctr_scope as $ctr_item ) : ?>
								<li><?php ctr_the_icon( 'check', array( 'size' => 18 ) ); ?><span><?php echo esc_html( $ctr_item ); ?></span></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<?php if ( $ctr_services ) : ?>
						<h2 class="side-card__title"><?php esc_html_e( 'Services', 'contronic' ); ?></h2>
						<ul class="side-nav">
							<?php foreach ( $ctr_services as $ctr_s ) : ?>
								<li><a href="<?php echo esc_url( get_permalink( $ctr_s ) ); ?>"><?php ctr_the_icon( ctr_meta( 'icon', $ctr_s->ID, 'building' ), array( 'size' => 18 ) ); ?><span><?php echo esc_html( get_the_title( $ctr_s ) ); ?></span><?php ctr_the_icon( 'chevron-right', array( 'size' => 16, 'class' => 'side-nav__chev' ) ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</aside>
			<?php endif; ?>
		</div>
		<?php if ( $ctr_gallery ) : ?>
			<div class="container">
				<div class="block reveal">
					<h2 class="block__title"><?php esc_html_e( 'Project gallery', 'contronic' ); ?></h2>
					<?php ctr_gallery( $ctr_gallery ); ?>
				</div>
			</div>
		<?php endif; ?>
	</section>

	<?php
	$ctr_prev = get_adjacent_post( false, '', true );
	$ctr_next = get_adjacent_post( false, '', false );
	if ( $ctr_prev || $ctr_next ) :
		?>
		<nav class="post-nav" aria-label="<?php esc_attr_e( 'More projects', 'contronic' ); ?>">
			<div class="container post-nav__inner">
				<?php if ( $ctr_prev ) : ?>
					<a class="post-nav__link" href="<?php echo esc_url( get_permalink( $ctr_prev ) ); ?>" rel="prev"><span><?php esc_html_e( 'Previous project', 'contronic' ); ?></span><strong><?php echo esc_html( get_the_title( $ctr_prev ) ); ?></strong></a>
				<?php endif; ?>
				<?php if ( $ctr_next ) : ?>
					<a class="post-nav__link post-nav__link--next" href="<?php echo esc_url( get_permalink( $ctr_next ) ); ?>" rel="next"><span><?php esc_html_e( 'Next project', 'contronic' ); ?></span><strong><?php echo esc_html( get_the_title( $ctr_next ) ); ?></strong></a>
				<?php endif; ?>
			</div>
		</nav>
		<?php
	endif;

	get_template_part( 'template-parts/home/cta' );
endwhile;

get_footer();

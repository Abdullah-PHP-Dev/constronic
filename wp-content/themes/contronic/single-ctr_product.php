<?php
/**
 * Product detail.
 *
 * @package Contronic
 */

get_header();

while ( have_posts() ) :
	the_post();
	$ctr_id        = get_the_ID();
	$ctr_specs     = ctr_lines( ctr_meta( 'specs' ), 2 );
	$ctr_features  = ctr_lines( ctr_meta( 'features' ) );
	$ctr_gallery   = ctr_ids( ctr_meta( 'gallery' ) );
	$ctr_datasheet = (int) ctr_meta( 'datasheet' );
	$ctr_brand     = ctr_meta( 'brand' );
	$ctr_model     = ctr_meta( 'model' );
	$ctr_terms     = get_the_terms( $ctr_id, 'ctr_product_cat' );
	$ctr_title     = get_the_title();
	$ctr_images    = array_values( array_unique( array_filter( array_merge( array( (int) get_post_thumbnail_id() ), $ctr_gallery ) ) ) );

	ctr_page_hero(
		array(
			'kicker'  => $ctr_terms && ! is_wp_error( $ctr_terms ) ? $ctr_terms[0]->name : __( 'Product', 'contronic' ),
			'title'   => $ctr_title,
			'text'    => ctr_excerpt( $ctr_id, 30 ),
			'icon'    => ctr_meta( 'icon', $ctr_id, 'layers' ),
			'seed'    => $ctr_id,
		)
	);
	?>
	<section class="section section--light">
		<div class="container product">
			<div class="product__media reveal">
				<?php if ( $ctr_images ) : ?>
					<div class="product__main">
						<?php echo wp_get_attachment_image( $ctr_images[0], 'large', false, array( 'loading' => 'eager', 'data-product-main' => '' ) ); ?>
					</div>
					<?php if ( count( $ctr_images ) > 1 ) : ?>
						<?php ctr_gallery( $ctr_images, 'gallery-grid--thumbs' ); ?>
					<?php endif; ?>
				<?php else : ?>
					<div class="product__main"><?php echo ctr_post_media( $ctr_id, 'large', '4:3' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<?php endif; ?>
			</div>

			<div class="product__info reveal">
				<?php if ( $ctr_brand || $ctr_model ) : ?>
					<dl class="meta-row">
						<?php if ( $ctr_brand ) : ?>
							<div><dt><?php esc_html_e( 'Brand', 'contronic' ); ?></dt><dd><?php echo esc_html( $ctr_brand ); ?></dd></div>
						<?php endif; ?>
						<?php if ( $ctr_model ) : ?>
							<div><dt><?php esc_html_e( 'Model', 'contronic' ); ?></dt><dd><?php echo esc_html( $ctr_model ); ?></dd></div>
						<?php endif; ?>
					</dl>
				<?php endif; ?>

				<div class="prose"><?php the_content(); ?></div>

				<?php if ( $ctr_features ) : ?>
					<h2 class="block__title"><?php esc_html_e( 'Key features', 'contronic' ); ?></h2>
					<ul class="checklist">
						<?php foreach ( $ctr_features as $ctr_f ) : ?>
							<li><?php ctr_the_icon( 'check', array( 'size' => 18 ) ); ?><span><?php echo esc_html( $ctr_f ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<div class="product__actions">
					<?php ctr_button( __( 'Ask about this product', 'contronic' ), '#inquiry', 'primary' ); ?>
					<?php if ( $ctr_datasheet && wp_get_attachment_url( $ctr_datasheet ) ) : ?>
						<a class="btn btn--ghost-dark" href="<?php echo esc_url( wp_get_attachment_url( $ctr_datasheet ) ); ?>" download><span><?php esc_html_e( 'Download datasheet', 'contronic' ); ?></span><?php ctr_the_icon( 'download', array( 'class' => 'btn__icon', 'size' => 18 ) ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<?php if ( $ctr_specs ) : ?>
			<div class="container">
				<div class="block reveal">
					<h2 class="block__title"><?php esc_html_e( 'Specifications', 'contronic' ); ?></h2>
					<div class="table-wrap">
						<table class="spec-table">
							<tbody>
								<?php foreach ( $ctr_specs as $ctr_row ) : ?>
									<tr><th scope="row"><?php echo esc_html( $ctr_row[0] ); ?></th><td><?php echo esc_html( $ctr_row[1] ); ?></td></tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</section>

	<?php
	$ctr_more = ctr_items( 'ctr_product', 3, array( 'post__not_in' => array( $ctr_id ) ) );
	if ( $ctr_more ) :
		?>
		<section class="section section--dark">
			<div class="container">
				<?php ctr_section_head( array( 'kicker' => __( 'More', 'contronic' ), 'heading' => __( 'Other products', 'contronic' ), 'link_text' => __( 'All products', 'contronic' ), 'link' => get_post_type_archive_link( 'ctr_product' ) ) ); ?>
				<ul class="card-grid card-grid--3">
					<?php foreach ( $ctr_more as $ctr_p ) : ?>
						<li><?php get_template_part( 'template-parts/cards/product', null, array( 'post' => $ctr_p ) ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
		<?php
	endif;

	get_template_part( 'template-parts/home/cta', null, array( 'service' => $ctr_title ) );
endwhile;

get_footer();

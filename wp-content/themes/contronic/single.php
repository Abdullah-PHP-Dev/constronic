<?php
/**
 * News article.
 *
 * @package Contronic
 */

get_header();

while ( have_posts() ) :
	the_post();
	$ctr_cats = get_the_category();
	ctr_page_hero(
		array(
			'kicker'   => $ctr_cats && 'uncategorized' !== $ctr_cats[0]->slug ? $ctr_cats[0]->name : ctr_mod( 'news_kicker' ),
			'title'    => get_the_title(),
			'image_id' => (int) get_post_thumbnail_id(),
			'icon'     => 'clipboard',
			'seed'     => get_the_ID(),
			'meta'     => '<p class="page-hero__meta"><time datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . esc_html( get_the_date() ) . '</time> · ' . esc_html( sprintf( /* translators: %d minutes */ _n( '%d min read', '%d min read', max( 1, (int) ceil( str_word_count( wp_strip_all_tags( get_the_content() ) ) / 220 ) ), 'contronic' ), max( 1, (int) ceil( str_word_count( wp_strip_all_tags( get_the_content() ) ) / 220 ) ) ) ) . '</p>',
		)
	);
	?>
	<article class="section section--light">
		<div class="container layout-article">
			<div class="prose prose--article">
				<?php the_content(); ?>
				<?php wp_link_pages(); ?>
				<?php
				$ctr_tags = get_the_tag_list( '<ul class="tag-list"><li>', '</li><li>', '</li></ul>' );
				if ( $ctr_tags && ! is_wp_error( $ctr_tags ) ) {
					echo wp_kses_post( $ctr_tags );
				}
				?>
				<?php
				$ctr_share = rawurlencode( get_permalink() );
				$ctr_ttl   = rawurlencode( get_the_title() );
				?>
				<div class="share">
					<span><?php esc_html_e( 'Share', 'contronic' ); ?></span>
					<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo esc_attr( $ctr_share ); ?>" target="_blank" rel="noopener"><?php ctr_the_icon( 'linkedin', array( 'label' => 'LinkedIn', 'size' => 18 ) ); ?></a>
					<a href="https://wa.me/?text=<?php echo esc_attr( $ctr_ttl . '%20' . $ctr_share ); ?>" target="_blank" rel="noopener"><?php ctr_the_icon( 'whatsapp', array( 'label' => 'WhatsApp', 'size' => 18 ) ); ?></a>
					<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr( $ctr_share ); ?>" target="_blank" rel="noopener"><?php ctr_the_icon( 'facebook', array( 'label' => 'Facebook', 'size' => 18 ) ); ?></a>
					<a href="https://x.com/intent/post?url=<?php echo esc_attr( $ctr_share ); ?>&amp;text=<?php echo esc_attr( $ctr_ttl ); ?>" target="_blank" rel="noopener"><?php ctr_the_icon( 'x', array( 'label' => 'X', 'size' => 18 ) ); ?></a>
				</div>
			</div>
			<?php if ( is_active_sidebar( 'blog' ) ) : ?>
				<aside class="layout-article__aside"><?php dynamic_sidebar( 'blog' ); ?></aside>
			<?php endif; ?>
		</div>
	</article>

	<?php
	$ctr_more = get_posts( array( 'posts_per_page' => 3, 'post__not_in' => array( get_the_ID() ) ) );
	if ( $ctr_more ) :
		?>
		<section class="section section--steel">
			<div class="container">
				<?php ctr_section_head( array( 'kicker' => ctr_mod( 'news_kicker' ), 'heading' => __( 'More articles', 'contronic' ) ) ); ?>
				<ul class="card-grid card-grid--3">
					<?php foreach ( $ctr_more as $ctr_p ) : ?>
						<li><?php get_template_part( 'template-parts/cards/post', null, array( 'post' => $ctr_p ) ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
		<?php
	endif;
	get_template_part( 'template-parts/home/cta' );
endwhile;

get_footer();

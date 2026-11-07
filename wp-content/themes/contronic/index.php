<?php
/**
 * News index and generic fallback.
 *
 * @package Contronic
 */

get_header();

$ctr_blog = (int) get_option( 'page_for_posts' );
if ( is_home() && $ctr_blog ) {
	$ctr_title = get_the_title( $ctr_blog );
	$ctr_text  = get_post_meta( $ctr_blog, '_ctr_hero_text', true );
	$ctr_img   = (int) get_post_thumbnail_id( $ctr_blog );
} elseif ( is_archive() ) {
	$ctr_title = wp_strip_all_tags( get_the_archive_title() );
	$ctr_text  = wp_strip_all_tags( get_the_archive_description() );
	$ctr_img   = 0;
} else {
	$ctr_title = __( 'News & Insights', 'contronic' );
	$ctr_text  = '';
	$ctr_img   = 0;
}

ctr_page_hero(
	array(
		'kicker'   => ctr_mod( 'news_kicker' ),
		'title'    => $ctr_title,
		'text'     => $ctr_text,
		'image_id' => $ctr_img,
		'icon'     => 'clipboard',
		'seed'     => 'news',
	)
);
?>
<section class="section section--light">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<ul class="card-grid card-grid--3 card-grid--on-light">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<li><?php get_template_part( 'template-parts/cards/post', null, array( 'post' => get_post() ) ); ?></li>
				<?php endwhile; ?>
			</ul>
			<?php ctr_pagination(); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/empty' ); ?>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();

<?php
/**
 * Homepage: latest news (hidden when no posts).
 *
 * @package Contronic
 */

$ctr_posts = get_posts( array( 'post_type' => 'post', 'posts_per_page' => 3 ) );
if ( ! $ctr_posts ) {
	return;
}
$ctr_blog = (int) get_option( 'page_for_posts' );
?>
<section class="section section--light" id="news">
	<div class="container">
		<?php
		ctr_section_head(
			array(
				'kicker'    => ctr_mod( 'news_kicker' ),
				'heading'   => ctr_mod( 'news_heading' ),
				'link_text' => $ctr_blog ? __( 'All news', 'contronic' ) : '',
				'link'      => $ctr_blog ? get_permalink( $ctr_blog ) : '',
			)
		);
		?>
		<ul class="card-grid card-grid--3">
			<?php foreach ( $ctr_posts as $ctr_post ) : ?>
				<li><?php get_template_part( 'template-parts/cards/post', null, array( 'post' => $ctr_post ) ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

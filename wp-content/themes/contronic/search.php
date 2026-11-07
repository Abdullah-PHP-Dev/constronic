<?php
/**
 * Search results.
 *
 * @package Contronic
 */

get_header();

ctr_page_hero(
	array(
		'kicker' => __( 'Search', 'contronic' ),
		/* translators: %s: search term */
		'title'  => sprintf( __( 'Results for “%s”', 'contronic' ), get_search_query() ),
		'icon'   => 'search',
		'seed'   => 'search',
	)
);
?>
<section class="section section--light">
	<div class="container prose--page">
		<?php get_search_form(); ?>
		<?php if ( have_posts() ) : ?>
			<ul class="result-list">
				<?php
				while ( have_posts() ) :
					the_post();
					$ctr_type = get_post_type_object( get_post_type() );
					?>
					<li class="reveal">
						<p class="card__meta"><?php echo esc_html( $ctr_type ? $ctr_type->labels->singular_name : '' ); ?></p>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p><?php echo esc_html( ctr_excerpt( get_the_ID(), 30 ) ); ?></p>
					</li>
				<?php endwhile; ?>
			</ul>
			<?php ctr_pagination(); ?>
		<?php else : ?>
			<p class="lead"><?php esc_html_e( 'No results matched your search. Try different keywords.', 'contronic' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();

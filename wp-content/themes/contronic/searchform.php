<?php
/**
 * Search form.
 *
 * @package Contronic
 */

$ctr_id = 'search-' . wp_unique_id();
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $ctr_id ); ?>"><?php esc_html_e( 'Search for:', 'contronic' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $ctr_id ); ?>" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search services, projects…', 'contronic' ); ?>">
	<button type="submit" class="btn btn--primary"><span><?php esc_html_e( 'Search', 'contronic' ); ?></span><?php ctr_the_icon( 'search', array( 'class' => 'btn__icon', 'size' => 18 ) ); ?></button>
</form>

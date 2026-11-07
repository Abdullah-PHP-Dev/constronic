<?php
/**
 * Empty state. Visitors see a short note with a contact link; editors see what to add.
 *
 * @package Contronic
 */

$ctr_contact = get_page_by_path( 'contact' );
?>
<div class="empty-state reveal">
	<?php ctr_the_icon( 'layers', array( 'size' => 40 ) ); ?>
	<h2><?php esc_html_e( 'Nothing to show here yet', 'contronic' ); ?></h2>
	<p><?php esc_html_e( 'This section is being updated. In the meantime, contact us and we will be happy to help.', 'contronic' ); ?></p>
	<?php if ( $ctr_contact ) : ?>
		<?php ctr_button( __( 'Contact us', 'contronic' ), get_permalink( $ctr_contact ), 'primary' ); ?>
	<?php endif; ?>
	<?php if ( current_user_can( 'edit_posts' ) ) : ?>
		<p class="empty-state__admin"><?php esc_html_e( 'Editor note: publish items in the WordPress admin and they will appear here automatically.', 'contronic' ); ?></p>
	<?php endif; ?>
</div>

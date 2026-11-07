<?php
/**
 * Inquiry form. Submits to Contronic Core (admin-post.php) — works with and without JavaScript.
 *
 * @package Contronic
 */

if ( ! function_exists( 'ctr_inquiry_hidden_fields' ) ) {
	if ( current_user_can( 'activate_plugins' ) ) {
		echo '<p class="notice">' . esc_html__( 'Activate the Contronic Core plugin to enable the inquiry form.', 'contronic' ) . '</p>';
	}
	return;
}

$ctr_args     = wp_parse_args(
	$args,
	array(
		'heading' => __( 'Send us an inquiry', 'contronic' ),
		'kicker'  => __( 'Project inquiry', 'contronic' ),
		'service' => '',
		'form'    => 'contact',
	)
);
$ctr_uid      = 'f' . substr( md5( $ctr_args['form'] . wp_rand() ), 0, 6 );
$ctr_settings = ctr_inquiry_settings();
$ctr_services = ctr_items( 'ctr_service' );
$ctr_state    = isset( $_GET['inquiry'] ) ? sanitize_key( wp_unslash( $_GET['inquiry'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
$ctr_privacy  = get_privacy_policy_url();
?>
<form class="inquiry" action="<?php echo esc_url( ctr_inquiry_endpoint() ); ?>" method="post" novalidate data-inquiry>
	<div class="inquiry__head">
		<?php ctr_kicker( $ctr_args['kicker'] ); ?>
		<h3 class="inquiry__title"><?php echo esc_html( $ctr_args['heading'] ); ?></h3>
	</div>

	<div class="inquiry__status" role="status" aria-live="polite" data-inquiry-status<?php echo $ctr_state ? '' : ' hidden'; ?>>
		<?php
		if ( 'sent' === $ctr_state ) {
			echo '<p class="is-success">' . esc_html( $ctr_settings['success_message'] ) . '</p>';
		} elseif ( 'error' === $ctr_state ) {
			echo '<p class="is-error">' . esc_html__( 'Please check the form and try again.', 'contronic' ) . '</p>';
		}
		?>
	</div>

	<div class="inquiry__grid">
		<div class="field">
			<label for="<?php echo esc_attr( $ctr_uid ); ?>-name"><?php esc_html_e( 'Full name', 'contronic' ); ?> <span aria-hidden="true">*</span></label>
			<input id="<?php echo esc_attr( $ctr_uid ); ?>-name" name="ctr_name" type="text" autocomplete="name" required minlength="2" maxlength="100">
			<p class="field__error" data-error-for="ctr_name"></p>
		</div>
		<div class="field">
			<label for="<?php echo esc_attr( $ctr_uid ); ?>-company"><?php esc_html_e( 'Company', 'contronic' ); ?></label>
			<input id="<?php echo esc_attr( $ctr_uid ); ?>-company" name="ctr_company" type="text" autocomplete="organization" maxlength="150">
			<p class="field__error" data-error-for="ctr_company"></p>
		</div>
		<div class="field">
			<label for="<?php echo esc_attr( $ctr_uid ); ?>-email"><?php esc_html_e( 'Email', 'contronic' ); ?> <span aria-hidden="true">*</span></label>
			<input id="<?php echo esc_attr( $ctr_uid ); ?>-email" name="ctr_email" type="email" autocomplete="email" required maxlength="150">
			<p class="field__error" data-error-for="ctr_email"></p>
		</div>
		<div class="field">
			<label for="<?php echo esc_attr( $ctr_uid ); ?>-phone"><?php esc_html_e( 'Phone', 'contronic' ); ?></label>
			<input id="<?php echo esc_attr( $ctr_uid ); ?>-phone" name="ctr_phone" type="tel" autocomplete="tel" inputmode="tel" pattern="[0-9+\-\s().]{7,20}" maxlength="20" placeholder="+971">
			<p class="field__error" data-error-for="ctr_phone"></p>
		</div>
		<?php if ( $ctr_services ) : ?>
			<div class="field field--full">
				<label for="<?php echo esc_attr( $ctr_uid ); ?>-service"><?php esc_html_e( 'Service required', 'contronic' ); ?></label>
				<select id="<?php echo esc_attr( $ctr_uid ); ?>-service" name="ctr_service">
					<option value=""><?php esc_html_e( 'General inquiry', 'contronic' ); ?></option>
					<?php foreach ( $ctr_services as $ctr_s ) : ?>
						<?php $ctr_t = get_the_title( $ctr_s ); ?>
						<option value="<?php echo esc_attr( $ctr_t ); ?>" <?php selected( $ctr_args['service'], $ctr_t ); ?>><?php echo esc_html( $ctr_t ); ?></option>
					<?php endforeach; ?>
					<?php if ( $ctr_args['service'] && ! in_array( $ctr_args['service'], wp_list_pluck( $ctr_services, 'post_title' ), true ) ) : ?>
						<option value="<?php echo esc_attr( $ctr_args['service'] ); ?>" selected><?php echo esc_html( $ctr_args['service'] ); ?></option>
					<?php endif; ?>
				</select>
			</div>
		<?php endif; ?>
		<div class="field field--full">
			<label for="<?php echo esc_attr( $ctr_uid ); ?>-message"><?php esc_html_e( 'Project details', 'contronic' ); ?> <span aria-hidden="true">*</span></label>
			<textarea id="<?php echo esc_attr( $ctr_uid ); ?>-message" name="ctr_message" rows="5" required minlength="10" maxlength="5000" placeholder="<?php esc_attr_e( 'Location, scope, timeline…', 'contronic' ); ?>"></textarea>
			<p class="field__error" data-error-for="ctr_message"></p>
		</div>
		<div class="field field--full field--check">
			<input id="<?php echo esc_attr( $ctr_uid ); ?>-consent" name="ctr_consent" type="checkbox" value="1" required>
			<label for="<?php echo esc_attr( $ctr_uid ); ?>-consent">
				<?php echo esc_html( $ctr_settings['consent_text'] ); ?>
				<?php if ( $ctr_privacy ) : ?>
					<a href="<?php echo esc_url( $ctr_privacy ); ?>"><?php esc_html_e( 'Read the policy', 'contronic' ); ?></a>
				<?php endif; ?>
			</label>
			<p class="field__error" data-error-for="ctr_consent"></p>
		</div>
	</div>

	<?php ctr_inquiry_hidden_fields( $ctr_args['form'] ); ?>

	<button class="btn btn--primary btn--block" type="submit" data-inquiry-submit>
		<span><?php esc_html_e( 'Send inquiry', 'contronic' ); ?></span><?php ctr_the_icon( 'arrow', array( 'class' => 'btn__icon', 'size' => 18 ) ); ?>
	</button>
	<p class="inquiry__note"><?php esc_html_e( 'Fields marked * are required.', 'contronic' ); ?></p>
</form>

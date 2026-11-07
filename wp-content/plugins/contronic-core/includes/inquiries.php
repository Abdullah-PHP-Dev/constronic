<?php
/**
 * Inquiry forms: secure submission handling, storage, notifications, admin history and CSV export.
 *
 * @package ContronicCore
 */

defined( 'ABSPATH' ) || exit;

const CTR_INQUIRY_ACTION = 'ctr_inquiry';

/**
 * Inquiry settings with defaults.
 *
 * @return array
 */
function ctr_inquiry_settings() {
	$defaults = array(
		'recipients'      => get_option( 'admin_email' ),
		'subject'         => 'New website inquiry: {service}',
		'success_message' => 'Thank you. Your inquiry has been received and our team will contact you shortly.',
		'autoreply'       => 1,
		'autoreply_body'  => "Dear {name},\n\nThank you for contacting {site}. We have received your inquiry and a member of our team will be in touch shortly.\n\nKind regards,\n{site}",
		'consent_text'    => 'I agree that my details will be used to respond to this inquiry, as described in the Privacy Policy.',
	);
	return wp_parse_args( (array) get_option( 'ctr_inquiry_settings', array() ), $defaults );
}

/**
 * Status labels.
 *
 * @return array
 */
function ctr_inquiry_statuses() {
	return array(
		'new'         => 'New',
		'in_progress' => 'In progress',
		'replied'     => 'Replied',
		'closed'      => 'Closed',
		'spam'        => 'Spam',
	);
}

add_action(
	'init',
	static function () {
		register_post_type(
			'ctr_inquiry',
			array(
				'labels'          => array_merge(
					ctr_labels( 'Inquiry', 'Inquiries' ),
					array( 'add_new' => false )
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'show_in_rest'    => false,
				'menu_icon'       => 'dashicons-email-alt',
				'menu_position'   => 25,
				'supports'        => array( 'title' ),
				'capability_type' => 'post',
				'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'    => true,
			)
		);
	}
);

/* -------------------------------------------------------------------------
 * Front-end helpers used by the theme.
 * ---------------------------------------------------------------------- */

/**
 * Hidden security inputs every inquiry form must include.
 *
 * @param string $form Form identifier.
 */
function ctr_inquiry_hidden_fields( $form = 'contact' ) {
	$ts = time();
	echo '<input type="hidden" name="action" value="' . esc_attr( CTR_INQUIRY_ACTION ) . '">';
	echo '<input type="hidden" name="ctr_form" value="' . esc_attr( $form ) . '">';
	echo '<input type="hidden" name="ctr_ts" value="' . esc_attr( $ts ) . '">';
	echo '<input type="hidden" name="ctr_sig" value="' . esc_attr( wp_hash( $ts . '|' . $form ) ) . '">';
	echo '<input type="hidden" name="ctr_source" value="' . esc_url( home_url( add_query_arg( array() ) ) ) . '">';
	wp_nonce_field( 'ctr_inquiry_submit', 'ctr_nonce', false );
	// Honeypot: real users never see or fill this.
	echo '<div class="ctr-hp" aria-hidden="true"><label>Leave this field empty<input type="text" name="ctr_website" tabindex="-1" autocomplete="off"></label></div>';
}

/**
 * Form endpoint URL.
 *
 * @return string
 */
function ctr_inquiry_endpoint() {
	return admin_url( 'admin-post.php' );
}

/* -------------------------------------------------------------------------
 * Submission handler.
 * ---------------------------------------------------------------------- */

add_action( 'admin_post_nopriv_' . CTR_INQUIRY_ACTION, 'ctr_handle_inquiry' );
add_action( 'admin_post_' . CTR_INQUIRY_ACTION, 'ctr_handle_inquiry' );

/**
 * Validate, store and notify.
 */
function ctr_handle_inquiry() {
	$wants_json = isset( $_SERVER['HTTP_ACCEPT'] ) && false !== strpos( sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT'] ) ), 'application/json' );
	$source     = isset( $_POST['ctr_source'] ) ? esc_url_raw( wp_unslash( $_POST['ctr_source'] ) ) : home_url( '/' );
	if ( ! $source || wp_parse_url( $source, PHP_URL_HOST ) !== wp_parse_url( home_url(), PHP_URL_HOST ) ) {
		$source = home_url( '/' );
	}

	$respond = static function ( $ok, $message, $errors = array(), $status = 200 ) use ( $wants_json, $source ) {
		if ( $wants_json ) {
			wp_send_json( array( 'ok' => $ok, 'message' => $message, 'errors' => $errors ), $status );
		}
		$url = add_query_arg( 'inquiry', $ok ? 'sent' : 'error', remove_query_arg( 'inquiry', $source ) );
		wp_safe_redirect( $url . '#inquiry' );
		exit;
	};

	// 1. Signed timestamp (server-generated, HMAC'd): proves the form came from this site and lets
	//    us reject instant bot posts. Valid for 7 days so full-page caches don't break the form.
	$form   = isset( $_POST['ctr_form'] ) ? sanitize_key( wp_unslash( $_POST['ctr_form'] ) ) : 'contact';
	$ts     = isset( $_POST['ctr_ts'] ) ? absint( $_POST['ctr_ts'] ) : 0;
	$sig    = isset( $_POST['ctr_sig'] ) ? sanitize_text_field( wp_unslash( $_POST['ctr_sig'] ) ) : '';
	$age    = time() - $ts;
	$sig_ok = $ts && hash_equals( wp_hash( $ts . '|' . $form ), $sig ) && $age < WEEK_IN_SECONDS;

	// 2. Nonce: required for logged-in users (CSRF protection where a session exists).
	//    Anonymous nonces are not meaningful and go stale behind page caches.
	$nonce_ok = ! is_user_logged_in() || ( isset( $_POST['ctr_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ctr_nonce'] ) ), 'ctr_inquiry_submit' ) );

	if ( ! $sig_ok || ! $nonce_ok ) {
		$respond( false, 'Your session expired. Please refresh the page and try again.', array(), 400 );
	}

	// 3. Honeypot + too-fast submissions: pretend success, store nothing.
	if ( ! empty( $_POST['ctr_website'] ) || $age < 3 ) {
		$respond( true, ctr_inquiry_settings()['success_message'] );
	}

	// 4. Rate limit: 5 submissions per 10 minutes per visitor.
	$rl_key = 'ctr_rl_' . substr( ctr_visitor_hash(), 0, 20 );
	$count  = (int) get_transient( $rl_key );
	if ( $count >= 5 ) {
		$respond( false, 'Too many submissions. Please wait a few minutes and try again.', array(), 429 );
	}

	// 5. Collect and validate.
	$data = array(
		'name'    => isset( $_POST['ctr_name'] ) ? sanitize_text_field( wp_unslash( $_POST['ctr_name'] ) ) : '',
		'email'   => isset( $_POST['ctr_email'] ) ? sanitize_email( wp_unslash( $_POST['ctr_email'] ) ) : '',
		'phone'   => isset( $_POST['ctr_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['ctr_phone'] ) ) : '',
		'company' => isset( $_POST['ctr_company'] ) ? sanitize_text_field( wp_unslash( $_POST['ctr_company'] ) ) : '',
		'service' => isset( $_POST['ctr_service'] ) ? sanitize_text_field( wp_unslash( $_POST['ctr_service'] ) ) : '',
		'message' => isset( $_POST['ctr_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['ctr_message'] ) ) : '',
		'consent' => ! empty( $_POST['ctr_consent'] ),
		'form'    => $form,
		'source'  => $source,
	);

	$errors = array();
	if ( mb_strlen( $data['name'] ) < 2 || mb_strlen( $data['name'] ) > 100 ) {
		$errors['ctr_name'] = 'Please enter your name.';
	}
	if ( ! is_email( $data['email'] ) ) {
		$errors['ctr_email'] = 'Please enter a valid email address.';
	}
	if ( '' !== $data['phone'] && ! preg_match( '/^[0-9+\-\s().]{7,20}$/', $data['phone'] ) ) {
		$errors['ctr_phone'] = 'Please enter a valid phone number.';
	}
	if ( mb_strlen( $data['message'] ) < 10 || mb_strlen( $data['message'] ) > 5000 ) {
		$errors['ctr_message'] = 'Please tell us a little more (at least 10 characters).';
	}
	if ( ! $data['consent'] ) {
		$errors['ctr_consent'] = 'Please confirm you agree to be contacted.';
	}
	if ( mb_strlen( $data['company'] ) > 150 || mb_strlen( $data['service'] ) > 150 ) {
		$errors['ctr_company'] = 'Please shorten this field.';
	}
	if ( $errors ) {
		$respond( false, 'Please check the highlighted fields.', $errors, 422 );
	}

	$is_spam = preg_match_all( '#https?://#i', $data['message'] ) > 3;

	set_transient( $rl_key, $count + 1, 10 * MINUTE_IN_SECONDS );

	// 6. Store.
	$post_id = wp_insert_post(
		array(
			'post_type'   => 'ctr_inquiry',
			'post_status' => 'publish',
			'post_title'  => sprintf( '%s — %s', $data['name'], $data['service'] ? $data['service'] : 'General inquiry' ),
		),
		true
	);
	if ( is_wp_error( $post_id ) ) {
		$respond( false, 'Sorry, something went wrong. Please email us directly.', array(), 500 );
	}
	foreach ( array( 'name', 'email', 'phone', 'company', 'service', 'message', 'form', 'source' ) as $key ) {
		update_post_meta( $post_id, '_ctr_inq_' . $key, $data[ $key ] );
	}
	update_post_meta( $post_id, '_ctr_inq_status', $is_spam ? 'spam' : 'new' );
	update_post_meta( $post_id, '_ctr_inq_consent', current_time( 'mysql' ) );

	// 7. Notify.
	if ( ! $is_spam ) {
		ctr_send_inquiry_emails( $post_id, $data );
	}

	do_action( 'ctr_inquiry_received', $post_id, $data );

	$respond( true, ctr_inquiry_settings()['success_message'] );
}

/**
 * Send admin notification and optional auto-reply.
 *
 * @param int   $post_id Inquiry ID.
 * @param array $data    Inquiry data.
 */
function ctr_send_inquiry_emails( $post_id, $data ) {
	$settings = ctr_inquiry_settings();
	$site     = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$replace  = array(
		'{name}'    => $data['name'],
		'{service}' => $data['service'] ? $data['service'] : 'General inquiry',
		'{site}'    => $site,
	);

	$recipients = array_filter( array_map( 'sanitize_email', array_map( 'trim', explode( ',', $settings['recipients'] ) ) ) );
	if ( ! $recipients ) {
		$recipients = array( get_option( 'admin_email' ) );
	}

	$lines = array(
		'Name'    => $data['name'],
		'Email'   => $data['email'],
		'Phone'   => $data['phone'],
		'Company' => $data['company'],
		'Service' => $data['service'],
		'Page'    => $data['source'],
	);
	$body = "A new inquiry was submitted on {$site}.\n\n";
	foreach ( $lines as $label => $value ) {
		if ( '' !== $value ) {
			$body .= "{$label}: {$value}\n";
		}
	}
	$body .= "\nMessage:\n{$data['message']}\n\nView in WordPress: " . admin_url( 'post.php?post=' . $post_id . '&action=edit' ) . "\n";

	wp_mail(
		$recipients,
		strtr( $settings['subject'], $replace ),
		$body,
		array( 'Reply-To: ' . $data['name'] . ' <' . $data['email'] . '>' )
	);

	if ( ! empty( $settings['autoreply'] ) ) {
		wp_mail( $data['email'], 'We received your inquiry — ' . $site, strtr( $settings['autoreply_body'], $replace ) );
	}
}

/* -------------------------------------------------------------------------
 * Admin: list table, detail view, status, settings, CSV export.
 * ---------------------------------------------------------------------- */

add_filter(
	'manage_ctr_inquiry_posts_columns',
	static function () {
		return array(
			'cb'          => '<input type="checkbox">',
			'title'       => 'Inquiry',
			'ctr_email'   => 'Email',
			'ctr_phone'   => 'Phone',
			'ctr_service' => 'Service',
			'ctr_status'  => 'Status',
			'date'        => 'Received',
		);
	}
);

add_action(
	'manage_ctr_inquiry_posts_custom_column',
	static function ( $column, $post_id ) {
		switch ( $column ) {
			case 'ctr_email':
				$email = get_post_meta( $post_id, '_ctr_inq_email', true );
				echo '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
				break;
			case 'ctr_phone':
				echo esc_html( get_post_meta( $post_id, '_ctr_inq_phone', true ) );
				break;
			case 'ctr_service':
				echo esc_html( get_post_meta( $post_id, '_ctr_inq_service', true ) );
				break;
			case 'ctr_status':
				$status   = get_post_meta( $post_id, '_ctr_inq_status', true );
				$statuses = ctr_inquiry_statuses();
				echo '<span class="ctr-badge ctr-badge--' . esc_attr( $status ) . '">' . esc_html( isset( $statuses[ $status ] ) ? $statuses[ $status ] : '—' ) . '</span>';
				break;
		}
	},
	10,
	2
);

// Filter by status.
add_action(
	'restrict_manage_posts',
	static function ( $post_type ) {
		if ( 'ctr_inquiry' !== $post_type ) {
			return;
		}
		$current = isset( $_GET['ctr_status'] ) ? sanitize_key( wp_unslash( $_GET['ctr_status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		echo '<select name="ctr_status"><option value="">All statuses</option>';
		foreach ( ctr_inquiry_statuses() as $key => $label ) {
			printf( '<option value="%s" %s>%s</option>', esc_attr( $key ), selected( $current, $key, false ), esc_html( $label ) );
		}
		echo '</select>';
	}
);

add_action(
	'pre_get_posts',
	static function ( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() || 'ctr_inquiry' !== $query->get( 'post_type' ) ) {
			return;
		}
		$status = isset( $_GET['ctr_status'] ) ? sanitize_key( wp_unslash( $_GET['ctr_status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $status ) {
			$query->set( 'meta_key', '_ctr_inq_status' );
			$query->set( 'meta_value', $status );
		}
	}
);

// Detail + management boxes.
add_action(
	'add_meta_boxes_ctr_inquiry',
	static function () {
		remove_meta_box( 'submitdiv', 'ctr_inquiry', 'side' );
		add_meta_box( 'ctr_inq_details', 'Inquiry details', 'ctr_inquiry_details_box', 'ctr_inquiry', 'normal', 'high' );
		add_meta_box( 'ctr_inq_manage', 'Status & notes', 'ctr_inquiry_manage_box', 'ctr_inquiry', 'side', 'high' );
	}
);

/**
 * Read-only details.
 *
 * @param WP_Post $post Post.
 */
function ctr_inquiry_details_box( $post ) {
	$rows = array(
		'name'    => 'Name',
		'email'   => 'Email',
		'phone'   => 'Phone',
		'company' => 'Company',
		'service' => 'Service',
		'form'    => 'Form',
		'source'  => 'Submitted from',
		'consent' => 'Consent given',
	);
	echo '<table class="widefat striped ctr-inq-table"><tbody>';
	foreach ( $rows as $key => $label ) {
		$value = get_post_meta( $post->ID, '_ctr_inq_' . $key, true );
		if ( '' === $value ) {
			continue;
		}
		if ( 'email' === $key ) {
			$value = '<a href="mailto:' . esc_attr( $value ) . '">' . esc_html( $value ) . '</a>';
		} elseif ( 'phone' === $key ) {
			$value = '<a href="tel:' . esc_attr( preg_replace( '/[^0-9+]/', '', $value ) ) . '">' . esc_html( $value ) . '</a>';
		} elseif ( 'source' === $key ) {
			$value = '<a href="' . esc_url( $value ) . '" target="_blank" rel="noopener">' . esc_html( $value ) . '</a>';
		} else {
			$value = esc_html( $value );
		}
		echo '<tr><th style="width:160px">' . esc_html( $label ) . '</th><td>' . $value . '</td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	}
	echo '</tbody></table>';
	echo '<h4>Message</h4><div class="ctr-inq-message">' . nl2br( esc_html( get_post_meta( $post->ID, '_ctr_inq_message', true ) ) ) . '</div>';
	$email = get_post_meta( $post->ID, '_ctr_inq_email', true );
	if ( $email ) {
		$subject = rawurlencode( 'Re: your inquiry to ' . wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
		echo '<p><a class="button button-primary" href="mailto:' . esc_attr( $email ) . '?subject=' . esc_attr( $subject ) . '">Reply by email</a></p>';
	}
}

/**
 * Status/notes box.
 *
 * @param WP_Post $post Post.
 */
function ctr_inquiry_manage_box( $post ) {
	wp_nonce_field( 'ctr_inq_manage_' . $post->ID, 'ctr_inq_nonce' );
	$status = get_post_meta( $post->ID, '_ctr_inq_status', true );
	echo '<p><label for="ctr_inq_status"><strong>Status</strong></label><select id="ctr_inq_status" name="ctr_inq_status" class="widefat">';
	foreach ( ctr_inquiry_statuses() as $key => $label ) {
		printf( '<option value="%s" %s>%s</option>', esc_attr( $key ), selected( $status, $key, false ), esc_html( $label ) );
	}
	echo '</select></p>';
	echo '<p><label for="ctr_inq_notes"><strong>Internal notes</strong></label><textarea id="ctr_inq_notes" name="ctr_inq_notes" rows="5" class="widefat">' . esc_textarea( get_post_meta( $post->ID, '_ctr_inq_notes', true ) ) . '</textarea></p>';
	echo '<p>Received: ' . esc_html( get_the_date( 'j M Y, H:i', $post ) ) . '</p>';
	submit_button( 'Save', 'primary', 'save', false );
	if ( current_user_can( 'delete_post', $post->ID ) ) {
		echo ' <a class="submitdelete" style="margin-left:10px" href="' . esc_url( get_delete_post_link( $post->ID ) ) . '">Move to Trash</a>';
	}
}

add_action(
	'save_post_ctr_inquiry',
	static function ( $post_id ) {
		if ( ! isset( $_POST['ctr_inq_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ctr_inq_nonce'] ) ), 'ctr_inq_manage_' . $post_id ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$status = isset( $_POST['ctr_inq_status'] ) ? sanitize_key( wp_unslash( $_POST['ctr_inq_status'] ) ) : 'new';
		if ( array_key_exists( $status, ctr_inquiry_statuses() ) ) {
			update_post_meta( $post_id, '_ctr_inq_status', $status );
		}
		update_post_meta( $post_id, '_ctr_inq_notes', isset( $_POST['ctr_inq_notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['ctr_inq_notes'] ) ) : '' );
	}
);

// Opening a new inquiry marks it "in progress".
add_action(
	'load-post.php',
	static function () {
		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $post_id && 'ctr_inquiry' === get_post_type( $post_id ) && current_user_can( 'edit_post', $post_id ) && 'new' === get_post_meta( $post_id, '_ctr_inq_status', true ) ) {
			update_post_meta( $post_id, '_ctr_inq_status', 'in_progress' );
		}
	}
);

// Menu badge with number of new inquiries.
add_action(
	'admin_menu',
	static function () {
		global $menu;
		$new = new WP_Query(
			array(
				'post_type'      => 'ctr_inquiry',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => '_ctr_inq_status', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'     => 'new', // phpcs:ignore WordPress.DB.SlowDBQuery
				'no_found_rows'  => false,
			)
		);
		if ( ! $new->found_posts ) {
			return;
		}
		foreach ( $menu as $i => $item ) {
			if ( 'edit.php?post_type=ctr_inquiry' === $item[2] ) {
				$menu[ $i ][0] .= ' <span class="awaiting-mod"><span class="pending-count">' . (int) $new->found_posts . '</span></span>'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			}
		}
	},
	99
);

// Settings + export page.
add_action(
	'admin_menu',
	static function () {
		add_submenu_page( 'edit.php?post_type=ctr_inquiry', 'Inquiry settings', 'Settings & export', 'manage_options', 'ctr-inquiry-settings', 'ctr_inquiry_settings_page' );
	}
);

add_action(
	'admin_init',
	static function () {
		register_setting(
			'ctr_inquiry',
			'ctr_inquiry_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => static function ( $input ) {
					$input = (array) $input;
					$emails = array_filter( array_map( 'sanitize_email', array_map( 'trim', explode( ',', isset( $input['recipients'] ) ? $input['recipients'] : '' ) ) ) );
					return array(
						'recipients'      => implode( ', ', $emails ),
						'subject'         => sanitize_text_field( isset( $input['subject'] ) ? $input['subject'] : '' ),
						'success_message' => sanitize_text_field( isset( $input['success_message'] ) ? $input['success_message'] : '' ),
						'autoreply'       => empty( $input['autoreply'] ) ? 0 : 1,
						'autoreply_body'  => sanitize_textarea_field( isset( $input['autoreply_body'] ) ? $input['autoreply_body'] : '' ),
						'consent_text'    => sanitize_text_field( isset( $input['consent_text'] ) ? $input['consent_text'] : '' ),
					);
				},
			)
		);
	}
);

/**
 * Render settings page.
 */
function ctr_inquiry_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$s = ctr_inquiry_settings();
	?>
	<div class="wrap">
		<h1>Inquiry settings</h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'ctr_inquiry' ); ?>
			<table class="form-table" role="presentation">
				<tr><th><label for="ctr-recipients">Notification recipients</label></th><td><input id="ctr-recipients" class="regular-text" name="ctr_inquiry_settings[recipients]" value="<?php echo esc_attr( $s['recipients'] ); ?>"><p class="description">Comma-separated email addresses.</p></td></tr>
				<tr><th><label for="ctr-subject">Email subject</label></th><td><input id="ctr-subject" class="regular-text" name="ctr_inquiry_settings[subject]" value="<?php echo esc_attr( $s['subject'] ); ?>"><p class="description">Placeholders: <code>{name}</code> <code>{service}</code> <code>{site}</code></p></td></tr>
				<tr><th><label for="ctr-success">Success message</label></th><td><input id="ctr-success" class="large-text" name="ctr_inquiry_settings[success_message]" value="<?php echo esc_attr( $s['success_message'] ); ?>"></td></tr>
				<tr><th><label for="ctr-consent">Consent checkbox text</label></th><td><input id="ctr-consent" class="large-text" name="ctr_inquiry_settings[consent_text]" value="<?php echo esc_attr( $s['consent_text'] ); ?>"></td></tr>
				<tr><th>Auto-reply</th><td><label><input type="checkbox" name="ctr_inquiry_settings[autoreply]" value="1" <?php checked( $s['autoreply'] ); ?>> Send a confirmation email to the visitor</label><br><textarea class="large-text" rows="6" name="ctr_inquiry_settings[autoreply_body]"><?php echo esc_textarea( $s['autoreply_body'] ); ?></textarea></td></tr>
			</table>
			<?php submit_button(); ?>
		</form>
		<hr>
		<h2>Export</h2>
		<p>Download all inquiries (excluding spam) as a CSV file.</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="ctr_export_inquiries">
			<?php wp_nonce_field( 'ctr_export_inquiries' ); ?>
			<?php submit_button( 'Download CSV', 'secondary', 'submit', false ); ?>
		</form>
		<hr>
		<h2>Email delivery</h2>
		<p>WordPress sends email using the server's mail function, which many hosts block or send to spam. For reliable delivery on the live site, configure SMTP with your email provider (see the project README).</p>
	</div>
	<?php
}

add_action(
	'admin_post_ctr_export_inquiries',
	static function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Not allowed.', 403 );
		}
		check_admin_referer( 'ctr_export_inquiries' );

		$ids = get_posts(
			array(
				'post_type'      => 'ctr_inquiry',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'post_status'    => 'publish',
			)
		);
		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=inquiries-' . gmdate( 'Y-m-d' ) . '.csv' );
		$out  = fopen( 'php://output', 'w' );
		$keys = array( 'name', 'email', 'phone', 'company', 'service', 'message', 'status', 'notes', 'source' );
		fputcsv( $out, array_merge( array( 'date' ), $keys ) );
		foreach ( $ids as $id ) {
			if ( 'spam' === get_post_meta( $id, '_ctr_inq_status', true ) ) {
				continue;
			}
			$row = array( get_the_date( 'Y-m-d H:i', $id ) );
			foreach ( $keys as $key ) {
				$value = (string) get_post_meta( $id, '_ctr_inq_' . $key, true );
				// Neutralise spreadsheet formula injection.
				if ( '' !== $value && in_array( $value[0], array( '=', '+', '-', '@' ), true ) ) {
					$value = "'" . $value;
				}
				$row[] = $value;
			}
			fputcsv( $out, $row );
		}
		fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		exit;
	}
);

<?php
/**
 * One-click site setup: page structure, menus, reading settings and clearly-flagged sample content.
 * Idempotent: existing pages/items (matched by slug) are never overwritten.
 *
 * @package ContronicCore
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'admin_menu',
	static function () {
		add_management_page( 'Contronic setup', 'Contronic setup', 'manage_options', 'ctr-setup', 'ctr_setup_page' );
	}
);

/**
 * Tools → Contronic setup screen.
 */
function ctr_setup_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$log = null;
	if ( isset( $_POST['ctr_run_setup'] ) && check_admin_referer( 'ctr_run_setup' ) ) {
		$log = ctr_run_setup();
	}
	?>
	<div class="wrap">
		<h1>Contronic setup</h1>
		<p>Creates the site structure: pages (About, Contact, Partners, Gallery, News, Privacy Policy, Terms), the main and footer menus, homepage settings, clean permalinks, and a set of <strong>sample services</strong> flagged for review.</p>
		<p>It is safe to run more than once. Anything that already exists (matched by URL slug) is left untouched. No partners, testimonials or projects are published, because those must be real.</p>
		<?php if ( $log ) : ?>
			<div class="notice notice-success"><p><strong>Setup finished.</strong></p><ul style="list-style:disc;margin-left:20px">
			<?php foreach ( $log as $line ) : ?>
				<li><?php echo esc_html( $line ); ?></li>
			<?php endforeach; ?>
			</ul></div>
		<?php endif; ?>
		<form method="post">
			<?php wp_nonce_field( 'ctr_run_setup' ); ?>
			<?php submit_button( 'Run setup', 'primary', 'ctr_run_setup' ); ?>
		</form>
	</div>
	<?php
}

/**
 * Create or find a post by slug.
 *
 * @param array $args   wp_insert_post args (post_name required).
 * @param array $meta   Meta to add on creation.
 * @param bool  $sample Flag as sample content.
 * @param array $log    Log (by reference).
 * @return int Post ID.
 */
function ctr_setup_upsert( $args, $meta, $sample, &$log ) {
	$existing = get_page_by_path( $args['post_name'], OBJECT, $args['post_type'] );
	if ( $existing && 'trash' !== $existing->post_status ) {
		return (int) $existing->ID;
	}
	$id = wp_insert_post( wp_slash( $args ), true );
	if ( is_wp_error( $id ) ) {
		$log[] = 'Could not create ' . $args['post_title'] . ': ' . $id->get_error_message();
		return 0;
	}
	foreach ( $meta as $key => $value ) {
		update_post_meta( $id, $key, wp_slash( $value ) );
	}
	if ( $sample ) {
		update_post_meta( $id, '_ctr_sample', 1 );
	}
	$log[] = sprintf( 'Created %s: %s%s', get_post_type_object( $args['post_type'] )->labels->singular_name, $args['post_title'], $sample ? ' (sample — review)' : '' );
	return (int) $id;
}

/**
 * Run the setup.
 *
 * @return string[] Log lines.
 */
function ctr_run_setup() {
	$log = array();

	// Clean permalinks.
	if ( '/%postname%/' !== get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		$log[] = 'Permalinks set to /%postname%/.';
	}

	// Remove WordPress demo content (moved to Trash, recoverable).
	foreach ( array( array( 'hello-world', 'post' ), array( 'sample-page', 'page' ) ) as $demo ) {
		$p = get_page_by_path( $demo[0], OBJECT, $demo[1] );
		if ( $p && 'trash' !== $p->post_status && false !== strpos( $p->post_content, 'WordPress' ) ) {
			wp_trash_post( $p->ID );
			$log[] = 'Moved WordPress demo "' . $p->post_title . '" to Trash.';
		}
	}

	/* ---------- Pages ---------- */
	$pages = array(
		'home'     => array( 'Home', '', '' ),
		'about'    => array( 'About Us', ctr_setup_about_content(), 'page-templates/template-about.php' ),
		'contact'  => array( 'Contact Us', '<p>Tell us about your project, site or maintenance requirement and the right team member will respond.</p>', 'page-templates/template-contact.php' ),
		'partners' => array( 'Partners & Brands', '<p>The manufacturers, partners and clients we work with. Only relationships approved for publication are shown here.</p>', 'page-templates/template-partners.php' ),
		'gallery'  => array( 'Gallery', '<p>Selected images from our sites and completed works.</p>', 'page-templates/template-gallery.php' ),
		'news'     => array( 'News & Insights', '', '' ),
		'terms'    => array( 'Terms & Conditions', ctr_setup_terms_content(), 'page-templates/template-legal.php' ),
	);
	$ids = array();
	$order = 0;
	foreach ( $pages as $slug => $def ) {
		$meta = $def[2] ? array( '_wp_page_template' => $def[2] ) : array();
		$sample = in_array( $slug, array( 'about', 'terms' ), true );
		$ids[ $slug ] = ctr_setup_upsert(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $def[0],
				'post_name'    => $slug,
				'post_content' => $def[1],
				'menu_order'   => $order++,
			),
			$meta,
			$sample,
			$log
		);
	}

	// Privacy policy: reuse WordPress's own privacy page if present.
	$privacy_id = (int) get_option( 'wp_page_for_privacy_policy' );
	$privacy    = $privacy_id ? get_post( $privacy_id ) : null;
	if ( $privacy && 'trash' !== $privacy->post_status ) {
		if ( 'publish' !== $privacy->post_status ) {
			wp_update_post(
				wp_slash(
					array(
						'ID'           => $privacy_id,
						'post_status'  => 'publish',
						'post_name'    => 'privacy-policy',
						'post_content' => ctr_setup_privacy_content(),
					)
				)
			);
			update_post_meta( $privacy_id, '_wp_page_template', 'page-templates/template-legal.php' );
			update_post_meta( $privacy_id, '_ctr_sample', 1 );
			$log[] = 'Published Privacy Policy (sample — review).';
		}
		$ids['privacy'] = $privacy_id;
	} else {
		$ids['privacy'] = ctr_setup_upsert(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => 'Privacy Policy',
				'post_name'    => 'privacy-policy',
				'post_content' => ctr_setup_privacy_content(),
			),
			array( '_wp_page_template' => 'page-templates/template-legal.php' ),
			true,
			$log
		);
		update_option( 'wp_page_for_privacy_policy', $ids['privacy'] );
	}

	if ( $ids['home'] ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['home'] );
	}
	if ( $ids['news'] ) {
		update_option( 'page_for_posts', $ids['news'] );
	}

	/* ---------- Taxonomy terms ---------- */
	$cats = array();
	foreach ( array( 'Construction', 'MEP', 'Maintenance' ) as $name ) {
		$term = term_exists( $name, 'ctr_service_cat' );
		if ( ! $term ) {
			$term = wp_insert_term( $name, 'ctr_service_cat' );
		}
		$cats[ $name ] = is_array( $term ) ? (int) $term['term_id'] : 0;
	}

	/* ---------- Sample services ---------- */
	foreach ( ctr_setup_services() as $i => $s ) {
		$id = ctr_setup_upsert(
			array(
				'post_type'    => 'ctr_service',
				'post_status'  => 'publish',
				'post_title'   => $s['title'],
				'post_name'    => $s['slug'],
				'post_excerpt' => $s['excerpt'],
				'post_content' => $s['content'],
				'menu_order'   => $i,
			),
			array(
				'_ctr_icon'     => $s['icon'],
				'_ctr_tagline'  => $s['tagline'],
				'_ctr_scope'    => implode( "\n", $s['scope'] ),
				'_ctr_process'  => implode( "\n", $s['process'] ),
				'_ctr_featured' => $i < 6 ? 1 : '',
			),
			true,
			$log
		);
		if ( $id && ! empty( $cats[ $s['cat'] ] ) ) {
			wp_set_object_terms( $id, array( $cats[ $s['cat'] ] ), 'ctr_service_cat' );
		}
	}

	/* ---------- Hero slides (copy only — the theme supplies artwork until photos are uploaded) ---------- */
	$slides = array(
		array( 'Slide 1 — Construction & MEP', 'CONTRONIC UAE · CONSTRUCTION & MEP', 'Built right.', 'Engineered to run.', 'Civil, fit-out and MEP works delivered by one coordinated team — from first drawing to final handover and beyond.', 'Explore our services', 'services', 'Request a quote', 'contact' ),
		array( 'Slide 2 — Maintenance', 'MAINTENANCE & TECHNICAL SUPPORT', 'Systems that keep', 'buildings working.', 'Planned and reactive maintenance for electrical, HVAC, plumbing and fire-protection systems.', 'Maintenance services', 'services/building-maintenance', 'Talk to our team', 'contact' ),
	);
	foreach ( $slides as $i => $s ) {
		ctr_setup_upsert(
			array(
				'post_type'   => 'ctr_slide',
				'post_status' => 'publish',
				'post_title'  => $s[0],
				'post_name'   => sanitize_title( $s[0] ),
				'menu_order'  => $i,
			),
			array(
				'_ctr_kicker'       => $s[1],
				'_ctr_heading'      => $s[2],
				'_ctr_heading_span' => $s[3],
				'_ctr_description'  => $s[4],
				'_ctr_cta_text'     => $s[5],
				'_ctr_cta_link'     => '/' . $s[6] . '/',
				'_ctr_cta2_text'    => $s[7],
				'_ctr_cta2_link'    => '/' . $s[8] . '/',
			),
			true,
			$log
		);
	}

	/* ---------- Draft examples (never public until edited and published) ---------- */
	ctr_setup_upsert(
		array(
			'post_type'    => 'ctr_project',
			'post_status'  => 'draft',
			'post_title'   => '[Example] Project name — replace me',
			'post_name'    => 'example-project',
			'post_excerpt' => 'One-sentence summary of the project and what Contronic delivered.',
			'post_content' => '<p>Describe the project: the client’s requirement, the scope Contronic delivered, the challenges and the outcome. Add a featured image and a gallery of real site photos, then publish.</p>',
		),
		array(
			'_ctr_location' => 'City, UAE',
			'_ctr_year'     => '',
			'_ctr_status'   => 'completed',
			'_ctr_scope'    => "Scope item 1\nScope item 2\nScope item 3",
		),
		true,
		$log
	);
	ctr_setup_upsert(
		array(
			'post_type'    => 'ctr_product',
			'post_status'  => 'draft',
			'post_title'   => '[Example] Product or solution — replace me',
			'post_name'    => 'example-product',
			'post_excerpt' => 'Short summary of the product or solution.',
			'post_content' => '<p>Describe the product or solution, where it is used and why clients choose it. Add specifications, a datasheet PDF and images, then publish.</p>',
		),
		array(
			'_ctr_specs'    => "Specification | Value\nSpecification | Value",
			'_ctr_features' => "Feature one\nFeature two",
		),
		true,
		$log
	);

	/* ---------- Menus ---------- */
	$log = array_merge( $log, ctr_setup_menus( $ids ) );

	flush_rewrite_rules();
	update_option( 'ctr_setup_done', time() );
	$log[] = 'Done. Next: Appearance → Customize → Company & contact details.';

	return $log;
}

/**
 * Create primary + footer menus and assign them.
 *
 * @param array $ids Page IDs.
 * @return string[]
 */
function ctr_setup_menus( $ids ) {
	$log       = array();
	$locations = get_theme_mod( 'nav_menu_locations', array() );

	$build = static function ( $name, $items ) {
		$menu = wp_get_nav_menu_object( $name );
		if ( $menu ) {
			return array( (int) $menu->term_id, false );
		}
		$menu_id = wp_create_nav_menu( $name );
		if ( is_wp_error( $menu_id ) ) {
			return array( 0, false );
		}
		foreach ( $items as $pos => $item ) {
			$args = array(
				'menu-item-status'   => 'publish',
				'menu-item-position' => $pos + 1,
			);
			if ( 'archive' === $item[0] ) {
				$args += array(
					'menu-item-type'   => 'post_type_archive',
					'menu-item-object' => $item[1],
					'menu-item-title'  => $item[2],
				);
			} else {
				$args += array(
					'menu-item-type'      => 'post_type',
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $item[1],
					'menu-item-title'     => $item[2],
				);
			}
			wp_update_nav_menu_item( $menu_id, 0, $args );
		}
		return array( (int) $menu_id, true );
	};

	$primary = $build(
		'Main menu',
		array_values(
			array_filter(
				array(
					array( 'page', $ids['about'], 'About' ),
					array( 'archive', 'ctr_service', 'Services' ),
					array( 'archive', 'ctr_project', 'Projects' ),
					array( 'archive', 'ctr_product', 'Products' ),
					array( 'page', $ids['partners'], 'Partners' ),
					array( 'page', $ids['gallery'], 'Gallery' ),
					array( 'page', $ids['news'], 'News' ),
					array( 'page', $ids['contact'], 'Contact' ),
				),
				static function ( $i ) {
					return ! empty( $i[1] );
				}
			)
		)
	);
	$legal = $build(
		'Footer legal',
		array(
			array( 'page', $ids['privacy'], 'Privacy Policy' ),
			array( 'page', $ids['terms'], 'Terms & Conditions' ),
		)
	);

	if ( $primary[1] ) {
		$log[] = 'Created "Main menu".';
	}
	if ( $legal[1] ) {
		$log[] = 'Created "Footer legal" menu.';
	}
	if ( empty( $locations['primary'] ) && $primary[0] ) {
		$locations['primary'] = $primary[0];
	}
	if ( empty( $locations['legal'] ) && $legal[0] ) {
		$locations['legal'] = $legal[0];
	}
	set_theme_mod( 'nav_menu_locations', $locations );
	return $log;
}

/**
 * Sample service definitions (Construction + MEP). Written as neutral service descriptions —
 * no claims about years, project counts, certifications or clients.
 *
 * @return array
 */
function ctr_setup_services() {
	$deliver = array(
		'Site survey & requirements | We visit the site, review drawings and confirm the scope with you.',
		'Proposal & programme | A clear quotation, method statement and schedule.',
		'Execution | Supervised installation by the right trades, coordinated with other works.',
		'Testing & handover | Testing, commissioning, documentation and handover.',
	);
	return array(
		array(
			'slug'    => 'civil-building-works',
			'title'   => 'Civil & Building Works',
			'cat'     => 'Construction',
			'icon'    => 'building',
			'tagline' => 'Structures, enabling works and building packages.',
			'excerpt' => 'Civil, structural and building works coordinated from setting-out to handover.',
			'scope'   => array( 'Site preparation and enabling works', 'Concrete and blockwork', 'Structural steel and steel fabrication', 'Waterproofing and insulation', 'External works and landscaping', 'Alterations and extensions' ),
			'process' => $deliver,
			'content' => '<p>Civil and building works form the base of every project. We plan resources, sequencing and quality checks so that the structure is ready for the MEP and finishing trades on time.</p><p>Works are carried out to the approved drawings and specifications, with inspections recorded at each stage.</p>',
		),
		array(
			'slug'    => 'electrical-works',
			'title'   => 'Electrical Works',
			'cat'     => 'MEP',
			'icon'    => 'bolt',
			'tagline' => 'Power, lighting and distribution — installed, tested, documented.',
			'excerpt' => 'LV distribution, power, lighting and containment installed and tested to the approved design.',
			'scope'   => array( 'LV distribution boards and sub-mains', 'Power and lighting installation', 'Cable containment — trays, trunking and conduit', 'Earthing and lightning protection', 'Emergency lighting', 'Testing and commissioning' ),
			'process' => $deliver,
			'content' => '<p>Our electrical teams install power and lighting systems for commercial, residential and industrial buildings. Every installation is inspected and tested before energisation and handed over with as-built documentation.</p>',
		),
		array(
			'slug'    => 'hvac-systems',
			'title'   => 'HVAC Systems',
			'cat'     => 'MEP',
			'icon'    => 'fan',
			'tagline' => 'Cooling, ventilation and air distribution for UAE conditions.',
			'excerpt' => 'Supply, installation and commissioning of air-conditioning and ventilation systems.',
			'scope'   => array( 'Split, VRF and package unit installation', 'Ductwork fabrication and installation', 'Chilled-water pipework', 'Ventilation and fresh-air systems', 'Insulation', 'Testing, adjusting and balancing' ),
			'process' => $deliver,
			'content' => '<p>Air-conditioning is the largest building system in the UAE climate. We install HVAC systems with attention to airflow, noise, condensate drainage and access for future maintenance.</p>',
		),
		array(
			'slug'    => 'plumbing-drainage',
			'title'   => 'Plumbing & Drainage',
			'cat'     => 'MEP',
			'icon'    => 'pipe',
			'tagline' => 'Water supply, drainage and pump systems.',
			'excerpt' => 'Water supply, hot water, drainage and pumping systems installed and pressure-tested.',
			'scope'   => array( 'Cold and hot water supply', 'Soil, waste and vent pipework', 'Storm-water drainage', 'Pumps and water tanks', 'Sanitary fixtures', 'Pressure testing and disinfection' ),
			'process' => $deliver,
			'content' => '<p>Reliable plumbing depends on correct materials, gradients and testing. Our teams install and pressure-test water and drainage systems before they are concealed.</p>',
		),
		array(
			'slug'    => 'fire-fighting-protection',
			'title'   => 'Fire Fighting & Protection',
			'cat'     => 'MEP',
			'icon'    => 'flame',
			'tagline' => 'Life-safety systems installed to the approved design.',
			'excerpt' => 'Installation of sprinkler, hydrant and fire-alarm systems to the approved design.',
			'scope'   => array( 'Sprinkler systems', 'Hose reels and hydrants', 'Fire pumps', 'Fire alarm and detection', 'Testing and inspection support' ),
			'process' => $deliver,
			'content' => '<p>Fire-protection systems must be installed exactly to the approved design. We coordinate installation with the other services and support testing and inspection.</p>',
		),
		array(
			'slug'    => 'fit-out-finishing',
			'title'   => 'Fit-Out & Finishing',
			'cat'     => 'Construction',
			'icon'    => 'paint',
			'tagline' => 'Interiors completed with MEP fully coordinated.',
			'excerpt' => 'Interior fit-out with partitions, ceilings, flooring, joinery and integrated MEP.',
			'scope'   => array( 'Gypsum partitions and ceilings', 'Flooring and tiling', 'Painting and wall finishes', 'Joinery and doors', 'MEP modifications for fit-out', 'Snagging and handover' ),
			'process' => $deliver,
			'content' => '<p>Fit-out brings together many trades in a short programme. Because we deliver the MEP as well as the finishes, ceilings, services and lighting are coordinated from the start.</p>',
		),
		array(
			'slug'    => 'building-maintenance',
			'title'   => 'Building Maintenance',
			'cat'     => 'Maintenance',
			'icon'    => 'gear',
			'tagline' => 'Planned and reactive maintenance for building systems.',
			'excerpt' => 'Planned preventive and reactive maintenance for MEP systems and building fabric.',
			'scope'   => array( 'Planned preventive maintenance', 'Reactive and corrective repairs', 'HVAC servicing', 'Electrical and plumbing maintenance', 'Minor civil and finishing repairs', 'Maintenance reporting' ),
			'process' => array(
				'Asset review | We survey your systems and agree a maintenance scope.',
				'Maintenance plan | A schedule of planned visits and response times.',
				'Service visits | Planned maintenance and call-outs by qualified technicians.',
				'Reporting | Records of work done, findings and recommendations.',
			),
			'content' => '<p>Well-maintained systems last longer and cost less to run. We offer planned and reactive maintenance tailored to the building and its systems.</p>',
		),
		array(
			'slug'    => 'elv-building-controls',
			'title'   => 'ELV & Building Controls',
			'cat'     => 'MEP',
			'icon'    => 'cpu',
			'tagline' => 'Low-current systems and controls integration.',
			'excerpt' => 'Structured cabling, CCTV, access control and building-management controls.',
			'scope'   => array( 'Structured data cabling', 'CCTV systems', 'Access control and intercom', 'Public address', 'BMS field devices and controls wiring' ),
			'process' => $deliver,
			'content' => '<p>Extra-low-voltage and control systems connect a building’s services together. We install and terminate ELV systems and coordinate them with the electrical and mechanical works.</p>',
		),
	);
}

/**
 * About page starter content.
 *
 * @return string
 */
function ctr_setup_about_content() {
	return '<!-- wp:paragraph --><p>Contronic UAE is a construction and MEP contractor in the United Arab Emirates. We bring civil, building and mechanical, electrical and plumbing works together under one coordinated team.</p><!-- /wp:paragraph -->'
		. '<!-- wp:paragraph --><p><em>[Sample text — replace with the verified company story: when the company was founded, its licence, locations, team, and the sectors it serves.]</em></p><!-- /wp:paragraph -->';
}

/**
 * Privacy policy starter.
 *
 * @return string
 */
function ctr_setup_privacy_content() {
	return '<p><em>Sample policy — have it reviewed for compliance with UAE Federal Decree-Law No. 45 of 2021 (Personal Data Protection) and any other applicable rules before publishing.</em></p>'
		. '<h2>Who we are</h2><p>This website is operated by Contronic UAE. Our contact details are listed on the Contact page.</p>'
		. '<h2>What we collect</h2><p>When you submit an inquiry form we collect the details you enter: your name, email address, phone number, company, the service you are interested in and your message. We also record the page the form was sent from and the date and time of your consent.</p>'
		. '<h2>How we use it</h2><p>We use your details only to respond to your inquiry and to follow up on it. We do not sell your information or use it for unrelated marketing without your permission.</p>'
		. '<h2>How long we keep it</h2><p>Inquiry records are kept for as long as needed to handle your request and any resulting business relationship, then deleted.</p>'
		. '<h2>Cookies</h2><p>This website uses only the cookies needed for it to work. If analytics or other tracking tools are added, this section will be updated.</p>'
		. '<h2>Your rights</h2><p>You can ask us to access, correct or delete the personal data we hold about you by contacting us using the details on the Contact page.</p>';
}

/**
 * Terms starter.
 *
 * @return string
 */
function ctr_setup_terms_content() {
	return '<p><em>Sample terms — have them reviewed by your legal adviser before publishing.</em></p>'
		. '<h2>Use of this website</h2><p>The information on this website is provided for general information about Contronic UAE and its services. It does not form an offer or contract. Quotations and contracts are issued separately in writing.</p>'
		. '<h2>Accuracy</h2><p>We aim to keep the information accurate and up to date, but we do not guarantee that it is complete or free of errors.</p>'
		. '<h2>Intellectual property</h2><p>The content, design, text and images on this website belong to Contronic UAE or are used with permission. They may not be copied or reused without written consent.</p>'
		. '<h2>Links</h2><p>Links to other websites are provided for convenience. We are not responsible for their content.</p>'
		. '<h2>Governing law</h2><p>These terms are governed by the laws of the United Arab Emirates.</p>';
}

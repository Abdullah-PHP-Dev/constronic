<?php
/**
 * Customizer: header, brand colours, footer and every homepage section.
 * Company/contact details and SEO defaults are registered by the Contronic Core plugin.
 *
 * "Lines" fields take one item per line, with columns separated by "|".
 *
 * @package Contronic
 */

defined( 'ABSPATH' ) || exit;

/**
 * Defaults for every theme mod (single source of truth).
 *
 * @return array
 */
function ctr_mod_defaults() {
	return array(
		// Brand.
		'brand_accent'          => '#19b3a6',
		'brand_accent2'         => '#f2a93b',
		'logo_text'             => 'CONTRONIC',
		'logo_sub'              => 'UAE',
		// Header.
		'header_cta_text'       => 'Request a quote',
		'header_cta_link'       => '/contact/',
		'topbar_show'           => 1,
		// Hero fallback (used when no Hero Slides are published).
		'hero_kicker'           => 'CONTRONIC UAE · CONSTRUCTION & MEP',
		'hero_heading'          => 'Built right.',
		'hero_span'             => 'Engineered to run.',
		'hero_text'             => 'Civil, fit-out and MEP works delivered by one coordinated team.',
		'hero_cta_text'         => 'Explore our services',
		'hero_cta_link'         => '/services/',
		'hero_image'            => 0,
		// Intro.
		'intro_show'            => 1,
		'intro_kicker'          => 'Who we are',
		'intro_heading'         => 'One team for the structure',
		'intro_span'            => 'and everything inside it.',
		'intro_text'            => 'Contronic UAE brings civil, building and MEP works together under one coordinated team. Fewer interfaces, clearer responsibility and systems that work from the day of handover.',
		'intro_facts'           => "UAE | Base of operations\nCivil + MEP | Under one contract\nOne | Accountable point of contact",
		'intro_cta_text'        => 'About Contronic',
		'intro_cta_link'        => '/about/',
		'intro_image'           => 0,
		// Services.
		'services_show'         => 1,
		'services_kicker'       => 'What we do',
		'services_heading'      => 'Services',
		'services_span'         => 'across the build.',
		'services_text'         => 'From the first excavation to the last commissioning sheet.',
		'services_count'        => 6,
		// Strengths.
		'strengths_show'        => 1,
		'strengths_kicker'      => 'Why Contronic',
		'strengths_heading'     => 'Fewer interfaces.',
		'strengths_span'        => 'Clearer accountability.',
		'strengths_items'       => "Integrated delivery | Civil, finishing and MEP trades planned together, so services fit the structure the first time.\nSafety first | Method statements, risk assessments and supervised work on every site.\nQuality checked | Inspections and testing recorded at each stage, with documentation at handover.\nSupport after handover | Maintenance services keep the installed systems working.",
		// Process.
		'process_show'          => 1,
		'process_kicker'        => 'How we work',
		'process_heading'       => 'A clear route',
		'process_span'          => 'from inquiry to handover.',
		'process_steps'         => "Understand | Site visit, drawings and requirements.\nPlan | Quotation, programme and method statements.\nBuild | Coordinated execution by the right trades.\nHand over | Testing, commissioning and documentation.",
		// Products.
		'products_show'         => 1,
		'products_kicker'       => 'Products & solutions',
		'products_heading'      => 'Systems we supply',
		'products_span'         => 'and install.',
		'products_text'         => '',
		// Projects.
		'projects_show'         => 1,
		'projects_kicker'       => 'Our work',
		'projects_heading'      => 'Selected',
		'projects_span'         => 'projects.',
		'projects_text'         => '',
		// Partners.
		'partners_show'         => 1,
		'partners_kicker'       => 'Partners & brands',
		'partners_heading'      => 'Working with trusted names.',
		// Testimonials.
		'testimonials_show'     => 1,
		'testimonials_kicker'   => 'Client feedback',
		'testimonials_heading'  => 'In their words.',
		// News.
		'news_show'             => 1,
		'news_kicker'           => 'News & insights',
		'news_heading'          => 'Latest from Contronic.',
		// Contact CTA.
		'cta_show'              => 1,
		'cta_kicker'            => 'Start a conversation',
		'cta_heading'           => 'Have a project',
		'cta_span'              => 'in mind?',
		'cta_text'              => 'Tell us about the site, the scope and your timeline. We will come back with the next steps.',
		// Footer.
		'footer_about'          => 'Construction and MEP contracting in the United Arab Emirates.',
		'footer_copyright'      => '© {year} Contronic UAE. All rights reserved.',
		'footer_services_title' => 'Services',
		'footer_links_title'    => 'Company',
	);
}

/**
 * Read a theme mod with defaults.
 *
 * @param string $key Key.
 * @return mixed
 */
function ctr_mod( $key ) {
	$defaults = ctr_mod_defaults();
	return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/**
 * Sanitize checkbox.
 *
 * @param mixed $v Value.
 * @return int
 */
function ctr_sanitize_checkbox( $v ) {
	return $v ? 1 : 0;
}

/**
 * Sanitize a link: allows relative (/path/), anchors (#id) and absolute URLs.
 *
 * @param string $v Value.
 * @return string
 */
function ctr_sanitize_link( $v ) {
	$v = trim( (string) $v );
	if ( '' === $v ) {
		return '';
	}
	if ( '#' === $v[0] ) {
		return '#' . sanitize_html_class( substr( $v, 1 ) );
	}
	return esc_url_raw( $v );
}

add_action(
	'customize_register',
	static function ( $wp_customize ) {
		$d = ctr_mod_defaults();

		$add = static function ( $section, $key, $label, $type = 'text', $extra = array() ) use ( $wp_customize, $d ) {
			$sanitize = 'sanitize_text_field';
			switch ( $type ) {
				case 'textarea':
				case 'lines':
					$sanitize = 'sanitize_textarea_field';
					break;
				case 'checkbox':
					$sanitize = 'ctr_sanitize_checkbox';
					break;
				case 'link':
					$sanitize = 'ctr_sanitize_link';
					break;
				case 'number':
					$sanitize = 'absint';
					break;
				case 'color':
					$sanitize = 'sanitize_hex_color';
					break;
				case 'image':
					$sanitize = 'absint';
					break;
			}
			$wp_customize->add_setting(
				$key,
				array(
					'default'           => isset( $d[ $key ] ) ? $d[ $key ] : '',
					'sanitize_callback' => $sanitize,
					'transport'         => 'refresh',
				)
			);
			if ( 'color' === $type ) {
				$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $key, array( 'label' => $label, 'section' => $section ) ) );
				return;
			}
			if ( 'image' === $type ) {
				$wp_customize->add_control(
					new WP_Customize_Media_Control(
						$wp_customize,
						$key,
						array(
							'label'     => $label,
							'section'   => $section,
							'mime_type' => 'image',
						)
					)
				);
				return;
			}
			$args = array_merge(
				array(
					'label'   => $label,
					'section' => $section,
					'type'    => 'lines' === $type ? 'textarea' : ( 'link' === $type ? 'text' : $type ),
				),
				$extra
			);
			if ( 'link' === $type && empty( $args['description'] ) ) {
				$args['description'] = 'A page path like /contact/, an anchor like #services, or a full URL.';
			}
			$wp_customize->add_control( $key, $args );
		};

		/* Brand */
		$wp_customize->add_section( 'ctr_brand', array( 'title' => 'Brand & colours', 'priority' => 22 ) );
		$add( 'ctr_brand', 'brand_accent', 'Primary accent colour', 'color' );
		$add( 'ctr_brand', 'brand_accent2', 'Secondary accent colour', 'color' );
		$add( 'ctr_brand', 'logo_text', 'Text logo (used until a logo image is uploaded in Site Identity)' );
		$add( 'ctr_brand', 'logo_sub', 'Text logo — small suffix' );

		/* Header */
		$wp_customize->add_section( 'ctr_header', array( 'title' => 'Header', 'priority' => 23 ) );
		$add( 'ctr_header', 'topbar_show', 'Show top bar with phone, email and social links', 'checkbox' );
		$add( 'ctr_header', 'header_cta_text', 'Header button text' );
		$add( 'ctr_header', 'header_cta_link', 'Header button link', 'link' );

		/* Homepage */
		$wp_customize->add_panel(
			'ctr_home',
			array(
				'title'       => 'Homepage sections',
				'priority'    => 24,
				'description' => 'Edit, hide or show each homepage section. Lists use one item per line, columns separated by “|”. Sections with no published content (e.g. Projects) hide automatically.',
			)
		);

		$sections = array(
			'hero'         => 'Hero (fallback when no slides)',
			'intro'        => 'Introduction',
			'services'     => 'Services',
			'strengths'    => 'Why Contronic',
			'process'      => 'Process',
			'products'     => 'Products',
			'projects'     => 'Projects',
			'partners'     => 'Partners & brands',
			'testimonials' => 'Testimonials',
			'news'         => 'News',
			'cta'          => 'Contact call-to-action',
		);
		$i = 0;
		foreach ( $sections as $id => $title ) {
			$wp_customize->add_section(
				'ctr_home_' . $id,
				array(
					'title'    => $title,
					'panel'    => 'ctr_home',
					'priority' => 10 + ( $i++ ),
				)
			);
		}

		$s = 'ctr_home_hero';
		$wp_customize->get_section( $s )->description = 'The hero normally shows the published items under <strong>Hero Slider</strong> in the admin menu. These fields are used only if no slides are published.';
		$add( $s, 'hero_kicker', 'Small label' );
		$add( $s, 'hero_heading', 'Heading' );
		$add( $s, 'hero_span', 'Heading — highlighted line' );
		$add( $s, 'hero_text', 'Description', 'textarea' );
		$add( $s, 'hero_cta_text', 'Button text' );
		$add( $s, 'hero_cta_link', 'Button link', 'link' );
		$add( $s, 'hero_image', 'Background image (1920×1080)', 'image' );

		$s = 'ctr_home_intro';
		$add( $s, 'intro_show', 'Show this section', 'checkbox' );
		$add( $s, 'intro_kicker', 'Small label' );
		$add( $s, 'intro_heading', 'Heading' );
		$add( $s, 'intro_span', 'Heading — highlighted line' );
		$add( $s, 'intro_text', 'Text', 'textarea' );
		$add( $s, 'intro_facts', 'Key facts', 'lines', array( 'description' => 'One per line: <code>Value | Label</code>. Only use facts you can verify.' ) );
		$add( $s, 'intro_cta_text', 'Button text' );
		$add( $s, 'intro_cta_link', 'Button link', 'link' );
		$add( $s, 'intro_image', 'Image (portrait works best)', 'image' );

		$s = 'ctr_home_services';
		$add( $s, 'services_show', 'Show this section', 'checkbox' );
		$add( $s, 'services_kicker', 'Small label' );
		$add( $s, 'services_heading', 'Heading' );
		$add( $s, 'services_span', 'Heading — highlighted' );
		$add( $s, 'services_text', 'Text', 'textarea' );
		$add( $s, 'services_count', 'Number of services to show', 'number', array( 'description' => 'Services ticked “Feature on homepage” are shown first.' ) );

		$s = 'ctr_home_strengths';
		$add( $s, 'strengths_show', 'Show this section', 'checkbox' );
		$add( $s, 'strengths_kicker', 'Small label' );
		$add( $s, 'strengths_heading', 'Heading' );
		$add( $s, 'strengths_span', 'Heading — highlighted' );
		$add( $s, 'strengths_items', 'Strengths', 'lines', array( 'description' => 'One per line: <code>Title | Description</code>' ) );

		$s = 'ctr_home_process';
		$add( $s, 'process_show', 'Show this section', 'checkbox' );
		$add( $s, 'process_kicker', 'Small label' );
		$add( $s, 'process_heading', 'Heading' );
		$add( $s, 'process_span', 'Heading — highlighted' );
		$add( $s, 'process_steps', 'Steps', 'lines', array( 'description' => 'One per line: <code>Step | Description</code>' ) );

		foreach ( array( 'products', 'projects' ) as $key ) {
			$s = 'ctr_home_' . $key;
			$add( $s, $key . '_show', 'Show this section (hidden automatically when nothing is published)', 'checkbox' );
			$add( $s, $key . '_kicker', 'Small label' );
			$add( $s, $key . '_heading', 'Heading' );
			$add( $s, $key . '_span', 'Heading — highlighted' );
			$add( $s, $key . '_text', 'Text', 'textarea' );
		}

		foreach ( array( 'partners', 'testimonials', 'news' ) as $key ) {
			$s = 'ctr_home_' . $key;
			$add( $s, $key . '_show', 'Show this section (hidden automatically when nothing is published)', 'checkbox' );
			$add( $s, $key . '_kicker', 'Small label' );
			$add( $s, $key . '_heading', 'Heading' );
		}

		$s = 'ctr_home_cta';
		$add( $s, 'cta_show', 'Show this section', 'checkbox' );
		$add( $s, 'cta_kicker', 'Small label' );
		$add( $s, 'cta_heading', 'Heading' );
		$add( $s, 'cta_span', 'Heading — highlighted' );
		$add( $s, 'cta_text', 'Text', 'textarea' );

		/* Footer */
		$wp_customize->add_section( 'ctr_footer', array( 'title' => 'Footer', 'priority' => 26 ) );
		$add( 'ctr_footer', 'footer_about', 'Short company description', 'textarea' );
		$add( 'ctr_footer', 'footer_services_title', 'Services column title' );
		$add( 'ctr_footer', 'footer_links_title', 'Links column title', 'text', array( 'description' => 'Links come from the “Footer — quick links” menu (Appearance → Menus). If empty, main menu items are used.' ) );
		$add( 'ctr_footer', 'footer_copyright', 'Copyright line', 'text', array( 'description' => '<code>{year}</code> is replaced with the current year.' ) );

		// Customizer descriptions may contain <code>; allow it.
		foreach ( $wp_customize->controls() as $control ) {
			if ( ! empty( $control->description ) ) {
				$control->description = wp_kses( $control->description, array( 'code' => array(), 'strong' => array() ) );
			}
		}
	}
);

<?php
/**
 * Plugin Name:       Contronic Core
 * Description:       Content types, custom fields, inquiry management and technical SEO for the Contronic UAE website. Keeps content independent of the theme.
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Contronic UAE
 * Text Domain:       contronic-core
 * License:           GPL-2.0-or-later
 */

defined( 'ABSPATH' ) || exit;

define( 'CTR_CORE_VERSION', '1.0.0' );
define( 'CTR_CORE_FILE', __FILE__ );
define( 'CTR_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'CTR_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once CTR_CORE_DIR . 'includes/helpers.php';
require_once CTR_CORE_DIR . 'includes/post-types.php';
require_once CTR_CORE_DIR . 'includes/fields.php';
require_once CTR_CORE_DIR . 'includes/meta-boxes.php';
require_once CTR_CORE_DIR . 'includes/inquiries.php';
require_once CTR_CORE_DIR . 'includes/seo.php';
require_once CTR_CORE_DIR . 'includes/admin.php';
require_once CTR_CORE_DIR . 'includes/setup.php';

register_activation_hook(
	__FILE__,
	static function () {
		ctr_register_post_types();
		ctr_register_taxonomies();
		flush_rewrite_rules();
	}
);

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

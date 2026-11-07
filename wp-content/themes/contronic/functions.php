<?php
/**
 * Contronic theme bootstrap.
 *
 * @package Contronic
 */

defined( 'ABSPATH' ) || exit;

define( 'CTR_THEME_VERSION', '1.0.0' );
define( 'CTR_THEME_DIR', get_template_directory() );
define( 'CTR_THEME_URI', get_template_directory_uri() );

require CTR_THEME_DIR . '/inc/compat.php';
require CTR_THEME_DIR . '/inc/setup.php';
require CTR_THEME_DIR . '/inc/customizer.php';
require CTR_THEME_DIR . '/inc/icons.php';
require CTR_THEME_DIR . '/inc/template-tags.php';
require CTR_THEME_DIR . '/inc/menus.php';
require CTR_THEME_DIR . '/inc/performance.php';

<?php
/**
 * Plugin Name:       Gemini Image Generation
 * Description:       A plugin that integrates Gemini AI for generating images based on user prompts, enhancing the content creation experience in WordPress.
 * Plugin URI:        https://github.com/dhananjaykuber/gemini-image-generation-plugin
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Dhananjay Kuber
 * Author URI:        https://dhananjaykuber.in
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       gemini-image-generation
 *
 * @package           gemini-image-generation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GEMINI_IMG_GEN_FEATURES_VERSION', '0.1.0' );
define( 'GEMINI_IMG_GEN_FEATURES_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'GEMINI_IMG_GEN_FEATURES_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

// phpcs:disable WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant
require_once GEMINI_IMG_GEN_FEATURES_PATH . '/inc/helpers/autoloader.php';
require_once GEMINI_IMG_GEN_FEATURES_PATH . '/inc/helpers/custom-functions.php';
// phpcs:enable WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant


/**
 * Load the plugin.
 *
 * @return void
 */
function gemini_img_gen_plugin_loader() {

	\Gemini_Image_Generation\Inc\Plugin::get_instance();
}

add_action( 'plugins_loaded', 'gemini_img_gen_plugin_loader' );

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

/**
 * Enqueue scripts and styles for the plugin.
 *
 * @return void
 */
function gemini_img_gen_enqueue_scripts() {

	wp_enqueue_style(
		'gemini-img-gen-style',
		GEMINI_IMG_GEN_FEATURES_URL . '/src/css/style.css',
		array(),
		GEMINI_IMG_GEN_FEATURES_VERSION
	);

	wp_enqueue_script(
		'gemini-img-gen-script',
		GEMINI_IMG_GEN_FEATURES_URL . '/build/index.js',
		array( 'jquery', 'media-views', 'wp-i18n' ),
		GEMINI_IMG_GEN_FEATURES_VERSION,
		true
	);
}

add_action( 'admin_enqueue_scripts', 'gemini_img_gen_enqueue_scripts' );

/**
 * Print media templates for the plugin.
 *
 * @return void
 */
function gemini_img_gen_print_media_templates() {
	?>
	<script type="text/html" id="tmpl-geminimedia">
		<h3 class="geminimedia-title"><?php esc_html_e( 'Prompt', 'gemini-image-generation' ); ?></h3>
		<textarea rows="6" name="gemini_prompt" id="gemini_prompt" placeholder="<?php esc_attr_e( 'Enter your prompt here...', 'gemini-image-generation' ); ?>"></textarea>
		<button class="button button-primary"><?php esc_html_e( 'Generate', 'gemini-image-generation' ); ?></button>
	</script>
	<?php
}

add_action( 'print_media_templates', 'gemini_img_gen_print_media_templates' );
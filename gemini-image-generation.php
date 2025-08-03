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

/**
 * Render the settings page for the plugin.
 *
 * @return void
 */
function gemini_img_gen_add_settings_menu() {

	add_options_page(
		__( 'Gemini Image Settings', 'gemini-image-generation' ),
		__( 'Gemini Image', 'gemini-image-generation' ),
		'manage_options',
		'gemini-image-settings',
		'gemini_img_gen_render_settings_page'
	);
}
add_action( 'admin_menu', 'gemini_img_gen_add_settings_menu' );

/**
 * Register settings for the plugin.
 *
 * @return void
 */
function gemini_img_gen_render_settings_page() {
	?>

	<div class="wrap">
		<h1><?php esc_html_e( 'Gemini Image Settings', 'gemini-image-generation' ); ?></h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'gemini_settings_group' );
			do_settings_sections( 'gemini-image-settings' );
			submit_button();
			?>
		</form>
	</div>

	<?php
}

/**
 * Register settings for the Gemini Image Generation plugin.
 *
 * @return void
 */
function gemini_img_gen_register_settings() {

	register_setting(
		'gemini_settings_group',
		'gemini_api_key'
	);

	add_settings_section(
		'gemini_settings_section',
		null,
		null,
		'gemini-image-settings'
	);

	add_settings_field(
		'gemini_api_key',
		__( 'Gemini API Key', 'gemini-image-generation' ),
		'gemini_img_gen_api_key_callback',
		'gemini-image-settings',
		'gemini_settings_section'
	);
}
add_action( 'admin_init', 'gemini_img_gen_register_settings' );

/**
 * Callback function to render the API key input field.
 *
 * @return void
 */
function gemini_img_gen_api_key_callback() {

	$api_key    = get_option( 'gemini_api_key', '' );
	$hidden_key = $api_key ? str_repeat( '*', 15 ) : '';

	printf( '<input type="text" name="gemini_api_key" value="%s" class="regular-text">', esc_attr( $hidden_key ) );
	printf( '<p>%s</p>', esc_html__( 'Leave as-is to keep current key. Enter a new one to update.', 'gemini-image-generation' ) );
}
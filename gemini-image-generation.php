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
		GEMINI_IMG_GEN_FEATURES_URL . '/src/css/main.css',
		array(),
		GEMINI_IMG_GEN_FEATURES_VERSION
	);

	wp_enqueue_script(
		'gemini-img-gen-media-frame-script',
		GEMINI_IMG_GEN_FEATURES_URL . '/build/js/media-frame.js',
		array( 'jquery', 'media-views', 'wp-i18n' ),
		GEMINI_IMG_GEN_FEATURES_VERSION,
		true
	);

	wp_enqueue_script(
		'gemini-img-gen-main-script',
		GEMINI_IMG_GEN_FEATURES_URL . '/build/js/main.js',
		array( 'jquery', 'wp-i18n' ),
		GEMINI_IMG_GEN_FEATURES_VERSION,
		true
	);

	wp_localize_script(
		'gemini-img-gen-main-script',
		'geminiImgGen',
		array(
			'ajaxURL' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'gemini_img_gen_nonce' ),
		)
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
		<button class="button button-primary" id="gemini-generate-button"><?php esc_html_e( 'Generate', 'gemini-image-generation' ); ?></button>
		<button class="button button-primary" id="gemini-clear-button"><?php esc_html_e( 'Clear', 'gemini-image-generation' ); ?></button>

		<div id="gemini-preview"></div>
	</script>
	<?php
}

add_action( 'print_media_templates', 'gemini_img_gen_print_media_templates' );

/**
 * Extract image title and alt text from the generated content.
 *
 * @param string $text The generated content text.
 * @return array
 */
function gemini_img_gen_extract_title_and_alt( $text ) {

	$title = '';
	$alt   = '';

	if ( preg_match( '/\*\*Image Title:\*\*\s*(.+)/i', $text, $title_match ) ) {
		$title = sanitize_text_field( trim( $title_match[1] ) );
	}

	if ( preg_match( '/\*\*Alt Text:\*\*\s*(.+)/i', $text, $alt_match ) ) {
		$alt = sanitize_text_field( trim( $alt_match[1] ) );
	}

	return array(
		'title' => $title,
		'alt'   => $alt,
	);
}

/**
 * Handle the AJAX request to generate an image.
 *
 * @return void
 */
function gemini_img_gen_ajax_generate_image() {

	if ( ! check_ajax_referer( 'gemini_img_gen_nonce', 'nonce', false ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid nonce. Please refresh the page and try again.', 'gemini-image-generation' ) ) );
	}

	if ( ! current_user_can( 'upload_files' ) ) {
		wp_send_json_error( array( 'message' => __( 'You do not have permission to upload files.', 'gemini-image-generation' ) ) );
	}

	$prompt = isset( $_POST['prompt'] ) ? sanitize_text_field( wp_unslash( $_POST['prompt'] ) ) : ''; // phpcs:ignore

	if ( empty( $prompt ) ) {
		wp_send_json_error( array( 'message' => __( 'Prompt cannot be empty.', 'gemini-image-generation' ) ) );
	}

	$gemini_api_key = get_option( 'gemini_api_key' );

	if ( empty( $gemini_api_key ) ) {
		wp_send_json_error( array( 'message' => __( 'API key is not set. Please configure the plugin settings.', 'gemini-image-generation' ) ) );
	}

	$prompt = trim( $prompt ) . ' Generate a single image based on the above prompt. Also provide a suitable image title and short alt text.';

	$body = wp_json_encode(
		array(
			'contents'         => array(
				array( 'parts' => array( array( 'text' => $prompt ) ) ),
			),
			'generationConfig' => array(
				'responseModalities' => array( 'IMAGE', 'TEXT' ),
			),
		)
	);

	$response = wp_remote_post(
		'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-preview-image-generation:generateContent',
		array(
			'headers' => array(
				'Content-Type'   => 'application/json',
				'x-goog-api-key' => $gemini_api_key,
			),
			'body'    => $body,
			'timeout' => 60,
		)
	);

	if ( is_wp_error( $response ) ) {
		wp_send_json_error( array( 'message' => __( 'Failed to connect to Gemini API.', 'gemini-image-generation' ) ) );
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( ! isset( $data['candidates'][0]['content']['parts'] ) ) {
		wp_send_json_error( array( 'message' => __( 'No image data returned from Gemini API.', 'gemini-image-generation' ) ) );
	}

	$image_data_uri = null;
	$title          = '';
	$alt            = '';

	foreach ( $data['candidates'][0]['content']['parts'] as $part ) {
		if ( isset( $part['inlineData']['mimeType'] ) && 'image/png' === $part['inlineData']['mimeType'] ) {
			$image_data_uri = 'data:image/png;base64,' . $part['inlineData']['data'];
		}

		if ( isset( $part['text'] ) ) {
			$extracted = gemini_img_gen_extract_title_and_alt( $part['text'] );

			if ( empty( $title ) && ! empty( $extracted['title'] ) ) {
				$title = $extracted['title'];
			}
			if ( empty( $alt ) && ! empty( $extracted['alt'] ) ) {
				$alt = $extracted['alt'];
			}
		}
	}

	if ( ! $image_data_uri ) {
		wp_send_json_error( array( 'message' => __( 'No valid image data found in the response.', 'gemini-image-generation' ) ) );
	}

	wp_send_json_success(
		array(
			'image' => $image_data_uri,
			'title' => $title,
			'alt'   => $alt,
		)
	);
}

add_action( 'wp_ajax_gemini_img_generate_image', 'gemini_img_gen_ajax_generate_image' );

/**
 * Save the generated image to the media library.
 *
 * @param string $base64_image The base64 encoded image data.
 * @param string $title        The title for the image.
 * @param string $alt          The alt text for the image.
 * @return array|WP_Error
 */
function gemini_img_gen_save_image( $base64_image, $title, $alt ) {

	$upload_dir = wp_upload_dir();

	// Extract base64 string and decode.
	$image_data    = preg_replace( '/^data:image\/\w+;base64,/', '', $base64_image );
	$image_data    = str_replace( ' ', '+', $image_data );
	$decoded_image = base64_decode( $image_data ); // phpcs:ignore

	if ( false === $decoded_image ) {
		return new WP_Error( 'image_decoding_error', __( 'Failed to decode the image data.', 'gemini-image-generation' ) );
	}

	// Generate a unique filename.
	$filename  = wp_unique_filename( $upload_dir['path'], $title . '-' . wp_generate_password( 8, true ) . '.jpg' );
	$file_path = $upload_dir['path'] . '/' . $filename;
	$file_url  = $upload_dir['url'] . '/' . $filename;

	$saved = file_put_contents( $file_path, $decoded_image ); // phpcs:ignore

	if ( ! $saved ) {
		return new WP_Error( 'file_save_error', __( 'Failed to save the image file.', 'gemini-image-generation' ) );
	}

	$attachment = array(
		'guid'           => $file_url,
		'post_mime_type' => 'image/jpeg',
		'post_title'     => sanitize_file_name( $title ),
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	$attachment_id = wp_insert_attachment( $attachment, $file_path );

	if ( is_wp_error( $attachment_id ) ) {
		return $attachment_id;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';

	$attachment_data = wp_generate_attachment_metadata( $attachment_id, $file_path );
	wp_update_attachment_metadata( $attachment_id, $attachment_data );

	// Set the alt text.
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $alt ) );

	return array(
		'id'    => $attachment_id,
		'url'   => $file_url,
		'title' => sanitize_text_field( $title ),
		'alt'   => sanitize_text_field( $alt ),
	);
}

/**
 * Handle the AJAX request to upload the generated image.
 *
 * @return void
 */
function gemini_img_gen_ajax_upload_image() {

	if ( ! check_ajax_referer( 'gemini_img_gen_nonce', 'nonce', false ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid nonce. Please refresh the page and try again.', 'gemini-image-generation' ) ) );
	}

	if ( ! current_user_can( 'upload_files' ) ) {
		wp_send_json_error( array( 'message' => __( 'You do not have permission to upload files.', 'gemini-image-generation' ) ) );
	}

	// phpcs:disable
	$image_data = isset( $_POST['image'] ) ? sanitize_text_field( wp_unslash( $_POST['image'] ) ) : '';
	$title 	= isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
	$alt 	= isset( $_POST['alt'] ) ? sanitize_text_field( wp_unslash( $_POST['alt'] ) ) : '';
	// phpcs:enable

	if ( empty( $image_data ) ) {
		wp_send_json_error( array( 'message' => __( 'No image data found.', 'gemini-image-generation' ) ) );
	}

	$result = gemini_img_gen_save_image( $image_data, $title, $alt );

	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ) );
	}

	wp_send_json_success( $result );
}

add_action( 'wp_ajax_gemini_img_upload_image', 'gemini_img_gen_ajax_upload_image' );

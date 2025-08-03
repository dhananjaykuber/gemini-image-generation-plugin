<?php
/**
 * Assets class for managing plugin assets.
 *
 * @package gemini-image-generation
 */

namespace Gemini_Image_Generation\Inc;

use Gemini_Image_Generation\Inc\Traits\Singleton;

/**
 * Class Assets
 */
class Assets {

	use Singleton;

	/**
	 * Construct method.
	 */
	protected function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts and styles for the plugin.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {

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
}

<?php
/**
 * Template class for managing plugin templates.
 *
 * @package gemini-image-generation
 */

namespace Gemini_Image_Generation\Inc;

use Gemini_Image_Generation\Inc\Traits\Singleton;

/**
 * Class Template
 */
class Template {

	use Singleton;

	/**
	 * Construct method.
	 */
	protected function __construct() {

		add_action( 'print_media_templates', array( $this, 'print_media_templates' ) );
	}

	/**
	 * Print media templates for the plugin.
	 *
	 * @return void
	 */
	public function print_media_templates() {
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
}

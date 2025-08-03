<?php
/**
 * Settings class for the Gemini Image Generation plugin.
 *
 * @package gemini-image-generation
 */

namespace Gemini_Image_Generation\Inc;

use Gemini_Image_Generation\Inc\Traits\Singleton;

/**
 * Class Settings
 */
class Settings {

	use Singleton;

	/**
	 * Construct method.
	 */
	protected function __construct() {

		add_action( 'admin_menu', array( $this, 'add_settings_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Render the settings page for the plugin.
	 *
	 * @return void
	 */
	public function add_settings_menu() {

		add_options_page(
			__( 'Gemini Image Settings', 'gemini-image-generation' ),
			__( 'Gemini Image', 'gemini-image-generation' ),
			'manage_options',
			'gemini-image-settings',
			array( $this, 'render_settings_page' )
		);
	}


	/**
	 * Register settings for the plugin.
	 *
	 * @return void
	 */
	public function render_settings_page() {
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
	public function register_settings() {

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
			array( $this, 'api_key_callback' ),
			'gemini-image-settings',
			'gemini_settings_section'
		);
	}


	/**
	 * Callback function to render the API key input field.
	 *
	 * @return void
	 */
	public function api_key_callback() {

		$api_key    = get_option( 'gemini_api_key', '' );
		$hidden_key = $api_key ? str_repeat( '*', 15 ) : '';

		printf( '<input type="text" name="gemini_api_key" value="%s" class="regular-text">', esc_attr( $hidden_key ) );
		printf( '<p>%s</p>', esc_html__( 'Leave as-is to keep current key. Enter a new one to update.', 'gemini-image-generation' ) );
	}
}

<?php
/**
 * Plugin manifest class.
 *
 * @package gemini-image-generation
 */

namespace Gemini_Image_Generation\Inc;

use Gemini_Image_Generation\Inc\Traits\Singleton;

/**
 * Class Plugin
 */
class Plugin {

	use Singleton;

	/**
	 * Construct method.
	 */
	protected function __construct() {

		$this->setup_hooks();
	}

	/**
	 * Setup hooks.
	 *
	 * @return void
	 */
	protected function setup_hooks() {
	}
}

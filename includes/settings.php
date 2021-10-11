<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Mai_Display_Taxonomy_Settings {
	private $post_types;

	/**
	 * Gets it started.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_page' ], 12 );
		add_action( 'admin_init', [ $this, 'register_settings' ], 999 );
	}

	/**
	 * Adds options page.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function add_page() {
		add_submenu_page(
			class_exists( 'Mai_Engine' ) ? 'mai-theme' : 'options-general.php',
			__( 'Mai Display Taxonomy', 'mai-display-taxonomy' ), // page_title.
			__( 'Display Taxonomy', 'mai-display-taxonomy' ), // menu_title.
			'manage_options', // capability.
			'mai-display-taxonomy', // menu_slug.
			[ $this, 'add_page_content' ], // callback.
			null
		);
	}

	/**
	 * Adds options page content.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function add_page_content() {
		?>
		<div class="wrap">
			<h2><?php echo __( 'Mai Display Taxonomy', 'mai-display-taxonomy' ); ?></h2>
			<p><?php echo __( 'Select post types to use with the Display taxonomy.', 'mai-display-taxonomy' ); ?></p>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'mai_display_taxonomy' );
				do_settings_sections( 'maidt-settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Registers settings.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function register_settings() {
		$existing         = maidt_get_post_types();
		$this->post_types = array_values( get_post_types( [ 'public' => true ] ) );

		if ( $this->post_types ) {

			register_setting(
				'mai_display_taxonomy', // option_group.
				'mai_display_taxonomy', // option_name.
				[ $this, 'sanitize' ] // sanitization callback.
			);

			add_settings_section(
				'maidt-post-types', // id.
				'', // title.
				'', // callback.
				'maidt-settings' // page.
			);

			$first = true;

			foreach ( $this->post_types as $post_type ) {
				add_settings_field(
					$post_type, // id.
					$first ? __( 'Post Types', 'mai-display-taxonomy' ) : '', // title.
					function() use ( $existing, $post_type ) {
						printf( '<label for="post-type-%s"><input type="checkbox" id="post-type-%s" name="mai_display_taxonomy[post_types][%s]" value="%s"%s>%s</label>',
							$post_type,
							$post_type,
							$post_type,
							$post_type,
							$existing && in_array( $post_type, $existing ) ? ' checked' : '',
							get_post_type_object( $post_type )->label
						);
					}, // callback.
					'maidt-settings', // page.
					'maidt-post-types' // section.
				);

				$first = false;
			}
		}
	}

	/**
	 * Sanitizes submitted values.
	 * Keeps existing post types that are no longer registered
	 * so you can toggle a plugin off without losing its setting here.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public function sanitize( $input ) {
		$existing            = maidt_get_post_types();
		$input['post_types'] = is_array( $input ) && isset( $input['post_types'] ) ? $input['post_types']: [];
		$input['post_types'] = array_map( 'esc_html', $input['post_types'] );
		$input['post_types'] = array_values( $input['post_types'] );
		$input['post_types'] = array_merge( $input['post_types'], array_values( array_diff( $existing, $this->post_types ) ) );
		$input['post_types'] = array_unique( $input['post_types'] );

		return $input;
	}
}

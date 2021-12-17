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
		add_filter( 'plugin_action_links_mai-display-taxonomy/mai-display-taxonomy.php', [ $this, 'add_settings_link' ], 10, 4 );
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
			<h3><?php echo __( 'Post Types', 'mai-display-taxonomy' ); ?></h3>
			<p><?php echo __( 'Select post types to use with the Display taxonomy.', 'mai-display-taxonomy' ); ?></p>

			<style>
			#maidt-settings-form th {
				display: none;
			}
			#maidt-settings-form td {
				padding: 8px 0;
			}
			</style>

			<form id="maidt-settings-form" method="post" action="options.php">
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
		$post_types       = array_values( get_post_types( [ 'public' => true ] ) );
		$this->post_types = (array) apply_filters( 'mai_display_taxonomy_post_type_choices', $post_types );

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

			foreach ( $this->post_types as $post_type ) {
				add_settings_field(
					$post_type, // id.
					'',
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

	/**
	 * Return the plugin action links.  This will only be called if the plugin is active.
	 *
	 * @since TBD
	 *
	 * @param array  $actions     Associative array of action names to anchor tags
	 * @param string $plugin_file Plugin file name, ie my-plugin/my-plugin.php
	 * @param array  $plugin_data Associative array of plugin data from the plugin file headers
	 * @param string $context     Plugin status context, ie 'all', 'active', 'inactive', 'recently_active'
	 *
	 * @return array Associative array of plugin action links.
	 */
	function add_settings_link( $actions, $plugin_file, $plugin_data, $context ) {
		$url                 = admin_url( sprintf( '%s.php?page=mai-display-taxonomy', class_exists( 'Mai_Engine' ) ? 'admin' : 'options-general' ) );
		$link                = sprintf( '<a href="%s">%s</a>', $url, __( 'Settings', 'mai-display-taxonomy' ) );
		$actions['settings'] = $link;

		return $actions;
	}
}

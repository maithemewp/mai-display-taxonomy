<?php

/**
 * Plugin Name:     Mai Display Taxonomy
 * Plugin URI:      https://bizbudding.com/mai-theme/plugins/mai-display-taxonomy/
 * Description:     Creates a private "Display" taxonomy for use with Mai Post Grid block.
 * Version:         1.3.0
 *
 * Author:          BizBudding
 * Author URI:      https://bizbudding.com
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Must be at the top of the file.
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

/**
 * Main Mai_Display_Taxonomy Class.
 *
 * @since 0.1.0
 */
final class Mai_Display_Taxonomy {

	/**
	 * @var   Mai_Display_Taxonomy The one true Mai_Display_Taxonomy
	 * @since 0.1.0
	 */
	private static $instance;

	/**
	 * Main Mai_Display_Taxonomy Instance.
	 *
	 * Insures that only one instance of Mai_Display_Taxonomy exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since   0.1.0
	 * @static  var array $instance
	 * @uses    Mai_Display_Taxonomy::setup_constants() Setup the constants needed.
	 * @uses    Mai_Display_Taxonomy::includes() Include the required files.
	 * @uses    Mai_Display_Taxonomy::hooks() Activate, deactivate, etc.
	 * @see     Mai_Display_Taxonomy()
	 * @return  object | Mai_Display_Taxonomy The one true Mai_Display_Taxonomy
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup
			self::$instance = new Mai_Display_Taxonomy;
			// Methods
			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @return  void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'mai-display-taxonomy' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @return  void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'mai-display-taxonomy' ), '1.0' );
	}

	/**
	 * Setup plugin constants.
	 *
	 * @access  private
	 * @since   0.1.0
	 * @return  void
	 */
	private function setup_constants() {
		// Plugin version.
		if ( ! defined( 'MAI_DISPLAY_TAXONOMY_VERSION' ) ) {
			define( 'MAI_DISPLAY_TAXONOMY_VERSION', '1.3.0' );
		}

		// Plugin Folder Path.
		if ( ! defined( 'MAI_DISPLAY_TAXONOMY_PLUGIN_DIR' ) ) {
			define( 'MAI_DISPLAY_TAXONOMY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Includes Path.
		if ( ! defined( 'MAI_DISPLAY_TAXONOMY_INCLUDES_DIR' ) ) {
			define( 'MAI_DISPLAY_TAXONOMY_INCLUDES_DIR', MAI_DISPLAY_TAXONOMY_PLUGIN_DIR . 'includes/' );
		}

		// Plugin Folder URL.
		if ( ! defined( 'MAI_DISPLAY_TAXONOMY_PLUGIN_URL' ) ) {
			define( 'MAI_DISPLAY_TAXONOMY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File.
		if ( ! defined( 'MAI_DISPLAY_TAXONOMY_PLUGIN_FILE' ) ) {
			define( 'MAI_DISPLAY_TAXONOMY_PLUGIN_FILE', __FILE__ );
		}

		// Plugin Base Name
		if ( ! defined( 'MAI_DISPLAY_TAXONOMY_BASENAME' ) ) {
			define( 'MAI_DISPLAY_TAXONOMY_BASENAME', dirname( plugin_basename( __FILE__ ) ) );
		}
	}

	/**
	 * Include required files.
	 *
	 * @access  private
	 * @since   0.1.0
	 * @return  void
	 */
	private function includes() {
		// Include vendor libraries.
		require_once __DIR__ . '/vendor/autoload.php';
		// Includes.
		foreach ( glob( MAI_DISPLAY_TAXONOMY_INCLUDES_DIR . '*.php' ) as $file ) { include $file; }
		// Settings.
		if ( is_admin() ) {
			$setting = new Mai_Display_Taxonomy_Settings();
		}
	}

	/**
	 * Run the hooks.
	 *
	 * @since   0.1.0
	 * @return  void
	 */
	public function hooks() {
		add_action( 'plugins_loaded', [ $this, 'updater' ], 12 );
		add_action( 'init',           [ $this, 'register_content_types' ] );

		register_activation_hook( __FILE__, [ $this, 'activate' ] );
		register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
	}

	/**
	 * Setup the updater.
	 *
	 * composer require yahnis-elsts/plugin-update-checker
	 *
	 * @uses    https://github.com/YahnisElsts/plugin-update-checker/
	 *
	 * @return  void
	 */
	public function updater() {
		// Bail if plugin updater is not loaded.
		if ( ! class_exists( 'YahnisElsts\PluginUpdateChecker\v5\PucFactory' ) ) {
			return;
		}

		// Setup the updater.
		$updater = PucFactory::buildUpdateChecker( 'https://github.com/maithemewp/mai-display-taxonomy/', __FILE__, 'mai-display-taxonomy' );

		// Maybe set github api token.
		if ( defined( 'MAI_GITHUB_API_TOKEN' ) ) {
			$updater->setAuthentication( MAI_GITHUB_API_TOKEN );
		}

		// Add icons for Dashboard > Updates screen.
		if ( function_exists( 'mai_get_updater_icons' ) && $icons = mai_get_updater_icons() ) {
			$updater->addResultFilter(
				function ( $info ) use ( $icons ) {
					$info->icons = $icons;
					return $info;
				}
			);
		}
	}

	/**
	 * Register content types.
	 *
	 * @return  void
	 */
	public function register_content_types() {
		$post_types = maidt_get_post_types();

		// Register "Display" taxonomy.
		register_taxonomy( 'mai_display', $post_types, array(
			'exclude_from_search' => true,
			'has_archive'         => false,
			'hierarchical'        => true,
			'labels' => array(
				'name'                       => _x( 'Display', 'taxonomy general name'  , 'mai-display-taxonomy' ),
				'singular_name'              => _x( 'Display', 'taxonomy singular name' , 'mai-display-taxonomy' ),
				'search_items'               => __( 'Search Display'                    , 'mai-display-taxonomy' ),
				'popular_items'              => __( 'Popular Display'                   , 'mai-display-taxonomy' ),
				'all_items'                  => __( 'All Categories'                    , 'mai-display-taxonomy' ),
				'edit_item'                  => __( 'Edit Display'                      , 'mai-display-taxonomy' ),
				'update_item'                => __( 'Update Display'                    , 'mai-display-taxonomy' ),
				'add_new_item'               => __( 'Add New Display'                   , 'mai-display-taxonomy' ),
				'new_item_name'              => __( 'New Display Name'                  , 'mai-display-taxonomy' ),
				'separate_items_with_commas' => __( 'Separate Display with commas'      , 'mai-display-taxonomy' ),
				'add_or_remove_items'        => __( 'Add or remove Display'             , 'mai-display-taxonomy' ),
				'choose_from_most_used'      => __( 'Choose from the most used Display' , 'mai-display-taxonomy' ),
				'not_found'                  => __( 'No Display found.'                 , 'mai-display-taxonomy' ),
				'menu_name'                  => __( 'Display'                           , 'mai-display-taxonomy' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
			),
			'public'            => false,
			'rewrite'           => false,
			'show_admin_column' => false,
			'show_in_menu'      => true,
			'show_in_nav_menus' => false,
			'show_in_rest'      => true,
			'show_tagcloud'     => false,
			'show_ui'           => true,
		) );
	}

	/**
	 * Plugin activation.
	 *
	 * @return  void
	 */
	public function activate() {
		$this->register_content_types();
		flush_rewrite_rules();
	}
}

/**
 * The main function for that returns Mai_Display_Taxonomy
 *
 * The main function responsible for returning the one true Mai_Display_Taxonomy
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $plugin = Mai_Display_Taxonomy(); ?>
 *
 * @since 0.1.0
 *
 * @return object|Mai_Display_Taxonomy The one true Mai_Display_Taxonomy Instance.
 */
function mai_display_taxonomy() {
	return Mai_Display_Taxonomy::instance();
}

// Get Mai_Display_Taxonomy Running.
mai_display_taxonomy();

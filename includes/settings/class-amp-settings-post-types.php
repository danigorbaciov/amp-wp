<?php
/**
 * Post types settings.
 *
 * @package AMP
 */

/**
 * Settings post types class.
 */
class AMP_Settings_Post_Types {

	/**
	 * Settings section id.
	 *
	 * @var string
	 */
	protected $section_id = 'post_types';

	/**
	 * Settings config.
	 *
	 * @var array
	 */
	protected $setting = array();

	/**
	 * List of post types which are always AMPlified.
	 *
	 * @var array
	 */
	protected $always_on = array( 'post' );

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->setting = array(
			'id'          => 'post_types_support',
			'label'       => __( 'Post Types Support', 'amp' ),
			'description' => __( 'Enable/disable AMP post type(s) support', 'amp' ),
		);
	}

	/**
	 * Initialize.
	 */
	public function init() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'update_option_' . AMP_Settings::SETTINGS_KEY, 'flush_rewrite_rules' );
	}

	/**
	 * Register the current page settings.
	 */
	public function register_settings() {
		register_setting( AMP_Settings::SETTINGS_KEY, AMP_Settings::SETTINGS_KEY );
		add_settings_section(
			$this->section_id,
			false,
			'__return_false',
			AMP_Settings::MENU_SLUG
		);
		add_settings_field(
			$this->setting['id'],
			$this->setting['label'],
			array( $this, 'render_setting' ),
			AMP_Settings::MENU_SLUG,
			$this->section_id
		);
	}

	/**
	 * Getter for settings value.
	 *
	 * The value(s) return are not sanitized.
	 *
	 * @param string $post_type The post type name.
	 * @return bool|array Return true if the post type is always on; the setting value otherwise.
	 */
	public function get_settings_value( $post_type = false ) {
		$settings = get_option( AMP_Settings::SETTINGS_KEY, array() );

		if ( false !== $post_type ) {
			// Always return true if the post type is always on.
			if ( true === $this->is_always_on( $post_type ) ) {
				return true;
			}

			if ( isset( $settings[ $this->setting['id'] ][ $post_type ] ) ) {
				return $settings[ $this->setting['id'] ][ $post_type ];
			}

			return false;
		}

		if ( empty( $settings[ $this->setting['id'] ] ) || ! is_array( $settings[ $this->setting['id'] ] ) ) {
			return array();
		}

		return $settings[ $this->setting['id'] ];
	}

	/**
	 * Getter for the supported post types.
	 *
	 * @return object Supported post types list.
	 */
	public function get_supported_post_types() {
		$core = get_post_types( array(
			'name' => 'post',
		), 'objects' );
		$cpt  = get_post_types( array(
			'public'   => true,
			'_builtin' => false,
		), 'objects' );

		return $core + $cpt;
	}

	/**
	 * Getter for the setting HTML input name attribute.
	 *
	 * @param string $post_type The post type name.
	 * @return object The setting HTML input name attribute.
	 */
	public function get_setting_name( $post_type ) {
		$id = $this->setting['id'];

		return AMP_Settings::SETTINGS_KEY . "[{$id}][{$post_type}]";
	}

	/**
	 * Check if a post type is always on.
	 *
	 * @param string $post_type The post type name.
	 * @return bool True if the post type is always on; false otherwise.
	 */
	public function is_always_on( $post_type ) {
		return in_array( $post_type, $this->always_on, true );
	}

	/**
	 * Setting renderer.
	 */
	public function render_setting() {
		require_once AMP__DIR__ . '/templates/admin/settings/fields/checkbox-post-types.php';
	}

	/**
	 * Get the instance of AMP_Settings_Post_Types.
	 *
	 * @return object $instance AMP_Settings_Post_Types instance.
	 */
	public static function get_instance() {
		static $instance;

		if ( ! $instance instanceof AMP_Settings_Post_Types ) {
			$instance = new AMP_Settings_Post_Types();
		}

		return $instance;
	}

}

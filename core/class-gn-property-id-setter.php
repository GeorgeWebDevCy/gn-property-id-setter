<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'Gn_Property_Id_Setter' ) ) :

	/**
	 * Main Gn_Property_Id_Setter Class.
	 *
	 * @package		GNPROPERTY
	 * @subpackage	Classes/Gn_Property_Id_Setter
	 * @since		1.0.0
	 * @author		George Nicolaou
	 */
	final class Gn_Property_Id_Setter {

		/**
		 * The real instance
		 *
		 * @access	private
		 * @since	1.0.0
		 * @var		object|Gn_Property_Id_Setter
		 */
		private static $instance;

		/**
		 * GNPROPERTY helpers object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Gn_Property_Id_Setter_Helpers
		 */
		public $helpers;

		/**
		 * GNPROPERTY settings object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Gn_Property_Id_Setter_Settings
		 */
		public $settings;

		/**
		 * Throw error on object clone.
		 *
		 * Cloning instances of the class is forbidden.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to clone this class.', 'gn-property-id-setter' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to unserialize this class.', 'gn-property-id-setter' ), '1.0.0' );
		}

		/**
		 * Main Gn_Property_Id_Setter Instance.
		 *
		 * Insures that only one instance of Gn_Property_Id_Setter exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @access		public
		 * @since		1.0.0
		 * @static
		 * @return		object|Gn_Property_Id_Setter	The one true Gn_Property_Id_Setter
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Gn_Property_Id_Setter ) ) {
				self::$instance					= new Gn_Property_Id_Setter;
				self::$instance->base_hooks();
				self::$instance->includes();
				self::$instance->helpers		= new Gn_Property_Id_Setter_Helpers();
				self::$instance->settings		= new Gn_Property_Id_Setter_Settings();

				//Fire the plugin logic
				new Gn_Property_Id_Setter_Run();

				/**
				 * Fire a custom action to allow dependencies
				 * after the successful plugin setup
				 */
				do_action( 'GNPROPERTY/plugin_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Include required files.
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function includes() {
			require_once GNPROPERTY_PLUGIN_DIR . 'core/includes/classes/class-gn-property-id-setter-helpers.php';
			require_once GNPROPERTY_PLUGIN_DIR . 'core/includes/classes/class-gn-property-id-setter-settings.php';

			require_once GNPROPERTY_PLUGIN_DIR . 'core/includes/classes/class-gn-property-id-setter-run.php';
		}

		/**
		 * Add base hooks for the core functionality
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function base_hooks() {
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'gn-property-id-setter', FALSE, dirname( plugin_basename( GNPROPERTY_PLUGIN_FILE ) ) . '/languages/' );
		}

	}

endif; // End if class_exists check.
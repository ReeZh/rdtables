<?php
/**
 * Plugin Name: rdtables
 * Plugin URI:  https://rdtables.reezhdesign.com
 * Description: Simply DataTables WordPress Plugin.
 * Version:     1.0.0
 * Author:      ReeZh Design
 * Author URI:  https://reezhdesign.com
 * Donate link: https://reezhdesign.com
 * License:     GPLv2
 * Text Domain: rdtables
 * Domain Path: /languages
 *
 * @link https://reezhdesign.com
 *
 * @package rdtables
 * @version 1.0.0
 */

/**
 * Copyright (c) 2020 ReeZh Design (email : hireus@reezhdesign.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

 defined( 'ABSPATH' ) or die( 'Hey, you can\t access this file. Error Bro!' );

/**
 * Autoloads files with classes when needed
 *
 * @since  1.0.0
 * @param  string $class_name Name of the class being requested.
 * @return void
 */
function rdtables_autoload_classes( $class_name )
{

  if ( 0 !== strpos( $class_name, 'RDT_' ) ) {
      return;
  }

  // Set up our filename
  $filename = strtolower( str_replace( '_', '-', substr( $class_name, strlen( 'RDT_' ) ) ) );

  // Include our file
  rdtables::include_file( 'includes/class-' . $filename );
}
spl_autoload_register( 'rdtables_autoload_classes' );

/**
 * Main initiation class
 *
 * @since  1.0.0
 */
final class RDTables {

    /**
     * Current version
     *
     * @var  string
     * @since  1.0.0
     */
    const VERSION = '1.0.0';

    /**
     * URL of plugin directory
     *
     * @var string
     * @since 1.0.0
     */
    protected $url = '';

    /**
     * Path of plugin directory
     *
     * @var string
     * @since 1.0.0
     */
    protected $path = '';

    /**
     * Plugin basename
     *
     * @var string
     * @since 1.0.0
     */
    protected $basename = '';

    /**
     * Detailed activation error messages
     *
     * @var array
     * @since 1.0.0
     */
    protected $activation_errors = array();

    /**
     * Singleton instance of plugin
     *
     * @var RDT
     * @since  1.0.0
     */
    protected static $single_instance = null;

    /**
     * Instance of RDT_Tables
     *
     * @since 1.0.0
     * @var RDT_Tables
     */
    protected $tables;

    /**
     * Instance of RDT_Widgets
     *
     * @since 1.0.0
     * @var RDT_Widgets
     */
    protected $widgets;

    /**
     * Instance of RDT_Shortcodes
     *
     * @since 1.0.0
     * @var RDT_Shortcodes
     */
    protected $shortcodes;

    /**
     * Instance of RDT_Ajax
     *
     * @since 1.0.0
     * @var RDT_Ajax
     */
    protected $ajax;

    /**
     * Instance of DPD_DataTables
     *
     * @since 1.0.0
     * @var DPD_DataTables
     */
    protected $datatables;

    /**
     * Creates or returns an instance of this class.
     *
     * @since  1.0.0
     * @return RDT A single instance of this class.
     */
    public static function get_instance() {
      if ( null === self::$single_instance ) {
          self::$single_instance = new self();
      }
      return self::$single_instance;
    }

    /**
     * Sets up our plugin
     *
     * @since  1.0.0
     */
    protected function __construct() {
        $this->basename = plugin_basename( __FILE__ );
        $this->url      = plugin_dir_url( __FILE__ );
        $this->path     = plugin_dir_path( __FILE__ );
    }

    /**
     * Attach other plugin classes to the base plugin class.
     *
     * @since  1.0.0
     * @return void
     */
    public function plugin_classes()
    {
      // Attach other plugin classes to the base plugin class.
      $this->tables           = new RDT_Tables( $this );
      $this->widgets          = new RDT_Widgets( $this );
      $this->shortcodes       = new RDT_Shortcodes( $this );
      $this->ajax             = new RDT_Ajax( $this );
      $this->datatables       = new RDT_DataTables( $this );
    } // END OF PLUGIN CLASSES FUNCTION

    /**
     * Add hooks and filters
     *
     * @since 1.0.0
     * @return void
     */
    public function hooks()
    {
      // Priority needs to be:
      // < 10 for CPT_Core,
      // < 5 for Taxonomy_Core,
      // 0 Widgets because widgets_init runs at init priority 1.
      add_action( 'init', array( $this, 'init' ), 0 );
      if ( is_admin() ) {
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
      } else {
        add_action( 'wp_enqueue_scripts', array( $this, 'rdtables_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'rdtables_scripts' ) );
      }

      // load translated strings for plugin
      load_plugin_textdomain( 'rdtables', false, dirname( $this->basename ) . '/languages/' );
    }

    public function rdtables_styles()
    {
      wp_enqueue_style('rdtablescss', plugins_url('/css/rdtables.css', __FILE__ ));
    }

    public function rdtables_scripts()
    {
      wp_deregister_script('jquery');
      wp_register_script( 'rdtablesjs', esc_url( plugins_url( '/js/rdtables.js', __FILE__ ) ), array('jquery'), '', true );
      wp_enqueue_script('rdtablesjs');
    }

    public function admin_styles()
    {
      wp_enqueue_style('rdtablescss', plugins_url('/css/rdtables-admin.css', __FILE__ ));
    }

    public function admin_scripts()
    {
      wp_enqueue_script('rdtablesjs', plugins_url('/js/dapenduk-admin.js', __FILE__ ),array('jquery'),'',true);
    }

    /**
     * Activate the plugin
     *
     * @since 1.0.0
     * @return void
     */
    public function _activate() {
      // Make sure any rewrite functionality has been loaded.
      flush_rewrite_rules();
    }

    /**
     * Deactivate the plugin
     * Uninstall routines should be in uninstall.php
     *
     * @since 1.0.0
     * @return void
     */
    public function _deactivate() {}

    /**
     * Init hooks
     *
     * @since 1.0.0
     * @return void
     */
    public function init() {

        // bail early if requirements aren't met
        if ( ! $this->check_requirements() ) {
            return;
        }

        // initialize plugin classes
        $this->plugin_classes();

    }

    /**
     * Check if the plugin meets requirements and
     * disable it if they are not present.
     *
     * @since 1.0.0
     * @return boolean result of meets_requirements
     */
    public function check_requirements()
    {
        // bail early if pluginmeets requirements
        if ( $this->meets_requirements() )
        {
            return true;
        }

        // Add a dashboard notice.
        add_action( 'all_admin_notices', array( $this, 'requirements_not_met_notice' ) );

        // Deactivate our plugin.
        add_action( 'admin_init', array( $this, 'deactivate_me' ) );

        return false;
    }

    /**
     * Deactivates this plugin, hook this function on admin_init.
     *
     * @since 1.0.0
     * @return void
     */
    public function deactivate_me() {
        // We do a check for deactivate_plugins before calling it, to protect
        // any developers from accidentally calling it too early and breaking things.
        if ( function_exists( 'deactivate_plugins' ) ) {
            deactivate_plugins( $this->basename );
        }
    }

    /**
     * Check that all plugin requirements are met
     *
     * @since 1.0.0
     * @return boolean True if requirements are met.
     */
    public function meets_requirements() {
        // Do checks for required classes / functions
        // function_exists('') & class_exists('').
        // We have met all requirements.
        // Add detailed messages to $this->activation_errors array
        return true;
    }

    /**
     * Adds a notice to the dashboard if the plugin requirements are not met
     *
     * @since 1.0.0
     * @return void
     */
    public function requirements_not_met_notice() {
        // compile default message
        $default_message = sprintf(
            __( 'RDTables WordPress Plugin is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.', 'dapenduk' ),
            admin_url( 'plugins.php' )
        );

        // default details to null
        $details = null;

        // add details if any exist
        if ( ! empty( $this->activation_errors ) && is_array( $this->activation_errors ) ) {
            $details = '<small>' . implode( '</small><br /><small>', $this->activation_errors ) . '</small>';
        }

        // output errors
        ?>
    <div id="message" class="error">
        <p>
            <?php echo $default_message; ?>
        </p>
        <?php echo $details; ?>
    </div>
    <?php
    }

    /**
     * Magic getter for our object.
     *
     * @since 1.0.0
     * @param string $field Field to get.
     * @throws Exception Throws an exception if the field is invalid.
     * @return mixed
     */
    public function __get( $field ) {
      switch ( $field ) {
        case 'version':
          return self::VERSION;
        case 'basename':
        case 'url':
        case 'path':
        case 'tables':
        case 'widgets':
        case 'shortcodes':
        case 'ajax':
        case 'datatables':
            return $this->$field;
        default:
            throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
      }
    }

    /**
     * Include a file from the includes directory
     *
     * @since 1.0.0
     * @param  string $filename Name of the file to be included.
     * @return bool   Result of include call.
     */
    public static function include_file( $filename ) {
        $file = self::dir( $filename . '.php' );
        if ( file_exists( $file ) ) {
            return include_once( $file );
        }
        return false;
    }

    /**
     * This plugin's directory
     *
     * @since 1.0.0
     * @param  string $path (optional) appended path.
     * @return string       Directory and path
     */
    public static function dir( $path = '' ) {
        static $dir;
        $dir = $dir ? $dir : trailingslashit( dirname( __FILE__ ) );
        return $dir . $path;
    }

    /**
     * This plugin's url
     *
     * @since 1.0.0
     * @param  string $path (optional) appended path.
     * @return string       URL and path
     */
    public static function url( $path = '' ) {
        static $url;
        $url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );
        return $url . $path;
    }
}

/**
 * Grab the RDTables object and return it.
 * Wrapper for RDTables::get_instance()
 *
 * @since  1.0.0
 * @return RDTables Singleton instance of plugin class.
 */
function rdt() {
    return RDTables::get_instance();
}

// Kick it off.
add_action( 'plugins_loaded', array( rdt(), 'hooks' ) );

register_activation_hook( __FILE__, array( rdt(), '_activate' ) );
register_deactivation_hook( __FILE__, array( rdt(), '_deactivate' ) );

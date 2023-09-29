<?php
/**
 * GN Property ID Setter
 *
 * @package       GNPROPERTY
 * @author        George Nicolaou
 * @license       gplv2
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   GN Property ID Setter
 * Plugin URI:    https://www.georgenicolaou.me/plugins/gn-property-id-setter
 * Description:   Assigns auto-incremented values to properties and enforces validation.
 * Version:       1.0.0
 * Author:        George Nicolaou
 * Author URI:    https://www.georgenicolaou.me/
 * Text Domain:   gn-property-id-setter
 * Domain Path:   /languages
 * License:       GPLv2
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with GN Property ID Setter. If not, see <https://www.gnu.org/licenses/gpl-2.0.html/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
// Plugin name
define( 'GNPROPERTY_NAME',			'GN Property ID Setter' );

// Plugin version
define( 'GNPROPERTY_VERSION',		'1.0.0' );

// Plugin Root File
define( 'GNPROPERTY_PLUGIN_FILE',	__FILE__ );

// Plugin base
define( 'GNPROPERTY_PLUGIN_BASE',	plugin_basename( GNPROPERTY_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'GNPROPERTY_PLUGIN_DIR',	plugin_dir_path( GNPROPERTY_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'GNPROPERTY_PLUGIN_URL',	plugin_dir_url( GNPROPERTY_PLUGIN_FILE ) );

/**
 * Load the main class for the core functionality
 */
require_once GNPROPERTY_PLUGIN_DIR . 'core/class-gn-property-id-setter.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  George Nicolaou
 * @since   1.0.0
 * @return  object|Gn_Property_Id_Setter
 */
function GNPROPERTY() {
	return Gn_Property_Id_Setter::instance();
}

GNPROPERTY();

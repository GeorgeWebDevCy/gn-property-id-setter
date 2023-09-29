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
require 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
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


function assign_auto_increment_to_properties() {
    // Query for all Property CPT posts.
    $properties = get_posts(array(
        'post_type' => 'property', // Replace 'property' with your CPT name.
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ));

    // Initialize a counter.
    $counter = 1;

    // Loop through each property and assign an auto-incremented value.
    foreach ($properties as $property) {
        // Format the counter with leading zeros to match the pattern.
        $formatted_counter = sprintf('%05d', $counter);

        // Construct the auto-incremented value with the desired pattern.
        $auto_increment_value = '10-' . $formatted_counter;

        // Check if the auto-incremented value already exists in the custom field.
        $existing_value = get_field('internal_property_id', $property->ID);

        // If it exists, increment the counter until a unique value is found.
        while ($existing_value && $existing_value === $auto_increment_value) {
            $counter++;
            $formatted_counter = sprintf('%05d', $counter);
            $auto_increment_value = '10-' . $formatted_counter;
        }

        // Update the custom field with the unique auto-incremented value.
        update_field('internal_property_id', $auto_increment_value, $property->ID);

        // Make the ACF field read-only to prevent user edits.
        acf_update_field(array(
            'key' => 'field_6506dd6fb8fb2', // Replace with the actual ACF field key.
            'read_only' => true,
        ), $property->ID);

        // Increment the counter.
        $counter++;
    }
}

function assign_auto_increment_to_properties_on_save($post_id) {
    // Check if this is a "property" post type.
    if (get_post_type($post_id) === 'property') {
        assign_auto_increment_to_properties();
    }
}

add_action('save_post', 'assign_auto_increment_to_properties_on_save');

function custom_id_validation($valid, $value, $field, $input_name) {
    // Check if the input matches the desired pattern (10-xxxxx).
    if (!preg_match('/^10-\d{5}$/', $value)) {
        $valid = 'Please enter a valid ID in the format 10-00001.';
    }
    return $valid;
}

add_filter('acf/validate_value', 'custom_id_validation', 10, 4);
GNPROPERTY();

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/GeorgeWebDevCy/gn-property-id-setter',
	__FILE__,
	'gn-property-id-setter'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');
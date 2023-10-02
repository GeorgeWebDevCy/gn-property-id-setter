<?php
/**
 * GN Property ID Setter
 *
 * @package       GNPROPERTY
 * @author        George Nicolaou
 * @license       gplv2
 * @version       1.1
 *
 * @wordpress-plugin
 * Plugin Name:   GN Property ID Setter
 * Plugin URI:    https://www.georgenicolaou.me/plugins/gn-property-id-setter
 * Description:   Assigns auto-incremented values to properties and enforces validation.
 * Version:       1.1
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
define( 'GNPROPERTY_NAME', 'GN Property ID Setter' );

// Plugin version
define( 'GNPROPERTY_VERSION', '1.1' );

// Plugin Root File
define( 'GNPROPERTY_PLUGIN_FILE', __FILE__ );

// Plugin base
define( 'GNPROPERTY_PLUGIN_BASE', plugin_basename( GNPROPERTY_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'GNPROPERTY_PLUGIN_DIR', plugin_dir_path( GNPROPERTY_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'GNPROPERTY_PLUGIN_URL', plugin_dir_url( GNPROPERTY_PLUGIN_FILE ) );

/**
 * Load the main class for the core functionality
 */
require_once GNPROPERTY_PLUGIN_DIR . 'core/class-gn-property-id-setter.php';
require 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

/**
 * The main function to load the only instance
 *
 * @since 1.0.0
 * @return object|Gn_Property_Id_Setter
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

    // Array to store existing IDs for uniqueness check.
    $existing_ids = array();

    // Loop through each property and assign an auto-incremented value.
    foreach ($properties as $property) {
        // Get the current ID value.
        $existing_value = get_field('internal_property_id', $property->ID);

        // Ensure the existing ID is unique and matches the desired pattern (10-xxxxx).
        while (empty($existing_value) || !preg_match('/^10-\d{5}$/', $existing_value) || in_array($existing_value, $existing_ids)) {
            // Generate a new auto-incremented value.
            $counter = max(array_map(function($id) {
                return (int)substr($id, 3); // Extract the numeric part.
            }, $existing_ids)) + 1;

            // Format the counter with leading zeros to match the pattern.
            $formatted_counter = sprintf('%05d', $counter);
            $existing_value = '10-' . $formatted_counter;
        }

        // Update the custom field with the unique auto-incremented value.
        update_field('internal_property_id', $existing_value, $property->ID);

        // Check if the post title is already the same as the property ID.
        $post_title = get_the_title($property->ID);
        if ($post_title !== $existing_value) {
            // Update the post title with the same value.
            wp_update_post(array(
                'ID' => $property->ID,
                'post_title' => $existing_value,
            ));
        }

        // Check if the post slug is already the same as the property ID.
        $post_slug = sanitize_title($property->post_name);
        if ($post_slug !== $existing_value) {
            // Update the post slug with the same value.
            wp_update_post(array(
                'ID' => $property->ID,
                'post_name' => $existing_value,
            ));
        }

        // Check if the featured image is not set.
        if (!has_post_thumbnail($property->ID)) {
            // Get the first image from the ACF gallery field.
            $gallery_images = get_field('field_64f8a5fdc9fd7', $property->ID);
            if (!empty($gallery_images)) {
                // Set the first image as the featured image.
                set_post_thumbnail($property->ID, $gallery_images[0]['ID']);
            }
        }

        // Make the ACF field read-only to prevent user edits.
        acf_update_field(array(
            'key' => 'field_6506dd6fb8fb2', // Replace with the actual ACF field key.
            'read_only' => true,
        ), $property->ID);

        // Add the assigned ID to the existing IDs array.
        $existing_ids[] = $existing_value;
    }
}


// Schedule the task to run daily.
function schedule_auto_increment_task() {
    if ( ! wp_next_scheduled( 'assign_auto_increment_task' ) ) {
        wp_schedule_event( time(), 'every_ten_minutes', 'assign_auto_increment_task' );
    }
}
add_action( 'wp', 'schedule_auto_increment_task' );

// Define the function to process a batch of "property" posts.
function process_property_posts_batch() {
    assign_auto_increment_to_properties();
}
add_action( 'assign_auto_increment_task', 'process_property_posts_batch' );

function custom_id_validation( $valid, $value, $field, $input_name ) {
    // Check if the input matches the desired pattern (10-xxxxx).
    if ( ! preg_match( '/^10-\d{5}$/', $value ) ) {
        $valid = 'Please enter a valid ID in the format 10-00001.';
    }
    return $valid;
}

add_filter( 'acf/validate_value/key=field_6506dd6fb8fb2', 'custom_id_validation', 10, 4 );

function gn_acf_read_only_field( $field ) {

	if( 'field_6506dd6fb8fb2' === $field['key'] ) {
	  $field['disabled'] = true;	
	}
  
	return $field;
  
  }
add_filter( 'acf/load_field', 'gn_acf_read_only_field' );
GNPROPERTY();

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/GeorgeWebDevCy/gn-property-id-setter',
    __FILE__,
    'gn-property-id-setter'
);

// Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');

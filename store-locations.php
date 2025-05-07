<?php
/**
 * Plugin Name: Study Site Listing
 * Description: A custom location management system that allows patients to search and view participating study sites near them. [store-locations map=yes]
 * Version: 4.5.2
 * Text Domain: store-location
 * Requires at least: 5.8
 * Requires PHP: 8.0
 * Author: Your Name
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace StudySiteListing;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Plugin version
define('LOCATION_PLUGIN_VERSION', '4.5.2');
define('LOCATION_DIR_URI', plugin_dir_url(__FILE__));
define('LOCATION_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DISTANCE_MILES', 50);

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'StudySiteListing\\';
    $base_dir = __DIR__ . '/includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Activation hook
register_activation_hook(__FILE__, ['\StudySiteListing\Core\Activator', 'activate']);

// Deactivation hook
register_deactivation_hook(__FILE__, ['\StudySiteListing\Core\Deactivator', 'deactivate']);

// Plugin initialization
add_action('plugins_loaded', function() {
    $plugin = new Core\Plugin();
    $plugin->run();
});

// Plugin page links
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links) {
    $plugin_links = [
        '<a href="' . admin_url('admin.php?page=set-store-loc-setting-page') . '">' . __('Settings', 'store-location') . '</a>',
    ];
    return array_merge($plugin_links, $links);
});


<?php

namespace StudySiteListing\Core;

class Activator {
    /**
     * Handle plugin activation
     */
    public static function activate(): void {
        self::create_indexes();
        self::update_version();
    }

    /**
     * Create necessary database indexes
     */
    private static function create_indexes(): void {
        global $wpdb;

        // Check if indexes already exist
        $existing_indexes = $wpdb->get_results("SHOW INDEX FROM {$wpdb->postmeta} WHERE Key_name IN ('location_lat_idx', 'location_lng_idx')");
        
        if (empty($existing_indexes)) {
            // Create indexes for faster location queries
            $wpdb->query("CREATE INDEX location_lat_idx ON {$wpdb->postmeta} (meta_key, meta_value(20)) WHERE meta_key = 'map_lat'");
            $wpdb->query("CREATE INDEX location_lng_idx ON {$wpdb->postmeta} (meta_key, meta_value(20)) WHERE meta_key = 'map_lng'");
        }
    }

    /**
     * Update plugin version in database
     */
    private static function update_version(): void {
        update_option('store_locations_version', LOCATION_PLUGIN_VERSION);
    }
} 
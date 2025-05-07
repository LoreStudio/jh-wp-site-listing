<?php

namespace StudySiteListing\Core;

class Deactivator {
    /**
     * Handle plugin deactivation
     */
    public static function deactivate(): void {
        self::cleanup_cache();
        self::remove_scheduled_events();
    }

    /**
     * Clean up any cached data
     */
    private static function cleanup_cache(): void {
        global $wpdb;

        // Clear all our transients
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $wpdb->options WHERE option_name LIKE %s OR option_name LIKE %s",
                $wpdb->esc_like('_transient_geocode_') . '%',
                $wpdb->esc_like('_transient_nearby_locations_') . '%'
            )
        );

        // Clear object cache if available
        wp_cache_flush();
    }

    /**
     * Remove any scheduled events
     */
    private static function remove_scheduled_events(): void {
        wp_clear_scheduled_hook('store_locations_cleanup_cache');
    }
} 
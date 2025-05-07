<?php

namespace StudySiteListing\Core;

class Plugin {
    /**
     * Initialize the plugin
     */
    public function run(): void {
        $this->setup_scheduled_tasks();
    }

    /**
     * Setup scheduled tasks
     */
    private function setup_scheduled_tasks(): void {
        // Schedule cache cleanup if not already scheduled
        if (!wp_next_scheduled('store_locations_cleanup_cache')) {
            wp_schedule_event(time(), 'daily', 'store_locations_cleanup_cache');
        }

        // Add cleanup handler
        add_action('store_locations_cleanup_cache', [$this, 'cleanup_old_cache']);
    }

    /**
     * Clean up old cached data
     */
    public function cleanup_old_cache(): void {
        global $wpdb;

        // Remove transients older than 30 days
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $wpdb->options 
                WHERE option_name LIKE %s 
                AND option_name LIKE %s 
                AND option_value < %s",
                $wpdb->esc_like('_transient_timeout_geocode_') . '%',
                $wpdb->esc_like('_transient_timeout_nearby_locations_') . '%',
                time() - (30 * DAY_IN_SECONDS)
            )
        );

        // Clear object cache for locations older than 24 hours
        wp_cache_delete('store_locations_last_cleanup');
    }
} 
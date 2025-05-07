<?php

namespace StudySiteListing\Core;

class Plugin {
    /**
     * Initialize the plugin
     */
    public function run(): void {
        sl_debug_log('Study Site Listing: Plugin::run() called');
        
        // Register post type and admin menu
        add_action('init', [$this, 'register_post_type']);
        add_action('admin_menu', [$this, 'register_admin_menu']);
        
        $this->setup_scheduled_tasks();
    }

    /**
     * Register the location post type
     */
    public function register_post_type(): void {
        sl_debug_log('Study Site Listing: Registering post type');
        
        $labels = [
            'name'               => __('Locations', 'store-location'),
            'singular_name'      => __('Location', 'store-location'),
            'menu_name'          => __('Locations', 'store-location'),
            'add_new'           => __('Add New', 'store-location'),
            'add_new_item'      => __('Add New Location', 'store-location'),
            'edit_item'         => __('Edit Location', 'store-location'),
            'view_item'         => __('View Location', 'store-location'),
            'all_items'         => __('All Locations', 'store-location'),
        ];

        $args = [
            'labels'              => $labels,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 20,
            'menu_icon'           => 'dashicons-location',
            'supports'            => ['title', 'editor', 'thumbnail'],
            'has_archive'         => true,
            'rewrite'             => ['slug' => 'locations'],
            'show_in_rest'        => true,
            'capability_type'     => 'post',
        ];

        register_post_type('location', $args);
    }

    /**
     * Register admin menu
     */
    public function register_admin_menu(): void {
        sl_debug_log('Study Site Listing: Registering admin menu');
        
        add_menu_page(
            __('Locations', 'store-location'),
            __('Locations', 'store-location'),
            'edit_posts',
            'edit.php?post_type=location',
            '',
            'dashicons-location',
            20
        );
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
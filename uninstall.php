<?php

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Clean up all plugin data
function store_locations_cleanup() {
    global $wpdb;

    // Remove all location posts
    $posts = get_posts([
        'post_type' => 'locations',
        'numberposts' => -1,
        'post_status' => 'any'
    ]);

    foreach ($posts as $post) {
        wp_delete_post($post->ID, true);
    }

    // Remove all plugin options
    delete_option('store_locations_version');
    delete_option('gmap_api_key');
    delete_option('store-loc-active-languages');

    // Remove all plugin transients
    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM $wpdb->options WHERE option_name LIKE %s OR option_name LIKE %s",
            $wpdb->esc_like('_transient_geocode_') . '%',
            $wpdb->esc_like('_transient_nearby_locations_') . '%'
        )
    );

    // Remove custom database indexes
    $wpdb->query("DROP INDEX IF EXISTS location_lat_idx ON {$wpdb->postmeta}");
    $wpdb->query("DROP INDEX IF EXISTS location_lng_idx ON {$wpdb->postmeta}");

    // Clear any cached data
    wp_cache_flush();
}

store_locations_cleanup(); 
<?php

namespace StudySiteListing\Api;

class Rest_Controller extends \WP_REST_Controller {
    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes(): void {
        $version = '1';
        $namespace = 'store-locations/v' . $version;

        register_rest_route($namespace, '/locations', [
            [
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [$this, 'get_locations'],
                'permission_callback' => [$this, 'get_items_permissions_check'],
                'args' => [
                    'lat' => [
                        'required' => false,
                        'type' => 'number',
                        'description' => 'Latitude for location search'
                    ],
                    'lng' => [
                        'required' => false,
                        'type' => 'number',
                        'description' => 'Longitude for location search'
                    ],
                    'distance' => [
                        'required' => false,
                        'type' => 'integer',
                        'default' => 50,
                        'description' => 'Search radius in miles'
                    ],
                    'language' => [
                        'required' => false,
                        'type' => 'string',
                        'description' => 'Language code to filter results'
                    ]
                ]
            ]
        ]);
    }

    /**
     * Get locations
     */
    public function get_locations($request): \WP_REST_Response {
        $params = $request->get_params();
        
        $args = [
            'post_type' => 'locations',
            'post_status' => 'publish',
            'posts_per_page' => -1
        ];

        // If coordinates provided, search nearby
        if (!empty($params['lat']) && !empty($params['lng'])) {
            $store_locations = new \Store_Locations();
            $locations = $store_locations->get_nearby_locations(
                $params['lat'],
                $params['lng'],
                $params['language'] ?? '',
                $params['distance'] ?? 50
            );

            return new \WP_REST_Response($locations, 200);
        }

        // Otherwise return all locations
        $posts = get_posts($args);
        $locations = array_map(function($post) {
            $location_data = get_post_meta($post->ID, '_location_data', true);
            return array_merge(
                ['id' => $post->ID, 'title' => $post->post_title],
                $location_data ?: []
            );
        }, $posts);

        return new \WP_REST_Response($locations, 200);
    }

    /**
     * Check if a given request has access to get items
     */
    public function get_items_permissions_check($request): bool {
        return true; // Public access for now
    }
} 
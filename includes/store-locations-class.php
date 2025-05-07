<?php
/**
 * The file contains all the plugin-related functions
 */

use StudySiteListing\Config\Location_Config;

if ( ! class_exists( 'Store_Locations' ) ) {

	class Store_Locations {

		private string $plugin_slug;
		private string $version;
		private string $cache_key;
		private bool $cache_allowed;
		private bool $show_direction;

		public function __construct() {

			$this->plugin_slug   = plugin_basename( __DIR__ );
			$this->version       = LOCATION_PLUGIN_VERSION;
			$this->cache_key     = 'store-locations';
			$this->cache_allowed = false;
			$this->show_direction = get_option( 'show_direction_link', 'yes' ) === 'yes';

			$this->init_hooks();

		}

		private function init_hooks(): void {
			// Core functionality
			add_action( 'init', [ $this, 'location_register_post_types' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'store_locations_scripts' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'admin_store_locations_scripts' ] );

			// Admin UI
			add_action( 'admin_menu', [ $this, 'store_loc_setting_page' ] );
			add_action( 'add_meta_boxes', [ $this, 'address_location_custom_box' ] );
			add_action( 'save_post_locations', [ $this, 'save_address_location' ] );

			// AJAX handlers
			add_action( 'wp_ajax_search_location_near', [ $this, 'search_location_near' ] );
			add_action( 'wp_ajax_nopriv_search_location_near', [ $this, 'search_location_near' ] );

			// Admin columns and filters
			add_filter( 'manage_locations_posts_columns', [ $this, 'columns_locations' ] );
			add_action( 'manage_locations_posts_custom_column', [ $this, 'columns_locations_data' ], 10, 2 );
			add_action( 'load-edit.php', [ $this, 'load_edit' ] );
			add_action( 'restrict_manage_posts', [ $this, 'restrict_manage_posts' ] );

			// Shortcodes
			add_shortcode( 'store-locations', [ $this, 'store_locations_detail' ] );
			add_shortcode( 'language', [ $this, 'get_language_shortcode' ] );
		}

		public function location_register_post_types(): void {

			register_post_type( 'locations', [
				'labels' => [
					'name' => _x( 'Locations', '', 'store-location' ),
					'singular_name' => _x( 'Location', '', 'store-location' ),
					'add_new' => _x( 'Add New Location', '', 'store-location' ),
					'add_new_item' => __( 'Add New Location', 'store-location' ),
					'edit_item' => __( 'Edit Location', 'store-location' ),
					'view_item' => __( 'View Location', 'store-location' ),
					'search_items' => __( 'Search Locations', 'store-location' ),
					'not_found' => __( 'No locations found.', 'store-location' ),
					'not_found_in_trash' => __( 'No locations found in Trash.', 'store-location' )
				],
				'public' => true,
				'menu_icon' => 'dashicons-location',
				'publicly_queryable' => false,
				'show_ui' => true,
				'show_in_menu' => true,
				'query_var' => true,
				'rewrite' => [ 'slug' => 'locations' ],
				'capability_type' => 'post',
				'has_archive' => false,
				'hierarchical' => false,
				'menu_position' => 20,
				'supports' => [ 'title' ],
				'show_in_rest' => true,
				'rest_base' => 'locations',
				'rest_controller_class' => 'StudySiteListing\\Api\\Rest_Controller'
			] );

		}

		public function store_locations_scripts(): void {
			if ( !$this->should_load_assets() ) {
				return;
			}

			$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
			
			wp_enqueue_script(
				'store-locations-js',
				LOCATION_DIR_URI . "public/js/store-locations{$suffix}.js",
				[ 'jquery', 'google-maps' ],
				$this->version,
				true
			);

			wp_localize_script( 'store-locations-js', 'storeLocationsData', [
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'store_locations_nonce' ),
				'defaultDistance' => DISTANCE_MILES
			] );

			wp_enqueue_style(
				'store-locations-css',
				LOCATION_DIR_URI . "public/css/store-locations{$suffix}.css",
				[],
				$this->version
			);
		}

		private function should_load_assets(): bool {
			if ( is_singular( 'locations' ) ) {
				return true;
			}

			global $post;
			if ( is_a( $post, 'WP_Post' ) && 
				( has_shortcode( $post->post_content, 'store-locations' ) || 
				 has_block( 'store-locations/map' ) ) ) {
				return true;
			}

			if ( is_admin() && 
				isset( $_GET['post_type'] ) && 
				$_GET['post_type'] === 'locations' ) {
				return true;
			}

			return false;
		}

		public function get_language_shortcode() {
			return apply_filters( 'wpml_current_language', null );
		}

		public function store_locations_detail( $atts = array() ): bool|string {
			wp_enqueue_script( 'store-loc-js' );
			wp_enqueue_script( 'store-google-map' );

			$attr = shortcode_atts( array(
				'map'   => 'yes',
				'study' => '',
			), $atts );

			$language_code = $this->get_language_code();

			ob_start();

			if ( $attr['map'] === 'yes' ) { ?>
                <script>
                    const mapStyle = [<?php echo get_option( 'map_json_style' );?>];
                    const marker_icon = '<?php echo get_option( 'gmap_marker_url' ); ?>';
                </script>
			<?php } ?>
            <div class="store-info-container <?php echo esc_attr( $attr['map'] === 'no' ? 'no-map' : '' ); ?>">
                <div class="store-locations">
                    <div class="store-locations-search OneLinkTx">
                        <form role="search" class="store-search-form">
                            <label for="search-text"></label>
                            <input type="text" value name="search-text" id="search-text"
                                   placeholder="<?php _e( 'Enter a location', 'store-location' ); ?>">
                            <input type="image" id="search-btn" alt="Search" src="<?php echo LOCATION_DIR_URI . 'images/icon-arrow-right.svg' ?>">
                            <input type="hidden" id="place_lat" value="">
                            <input type="hidden" id="place_lng" value="">
                            <input type="hidden" id="show_map" name="show_map"
                                   value="<?php echo esc_attr( $attr['map'] === 'yes' ? 'yes' : 'no' ); ?>">
							<?php if ( $attr['study'] ) { ?>
                                <input type="hidden" id="study" name="study" value="<?php echo esc_attr( $attr['study'] ); ?>">
							<?php } ?>
                            <input type="hidden" id="language_code" value="<?php echo $language_code; ?>">
                        </form>
						<?php if ( $attr['map'] === 'yes' ) { ?>
                            <div class="my-location">
                                <a href="javascript:void(0);" onClick="getuser_location();">
									<?php _e( 'Use my Location', 'store-location' ); ?>
                                </a>
                            </div>
						<?php } ?>
                    </div>
                    <h5 class="store-locations-result-info OneLinkTx">
						<?php _e( 'Showing', 'store-location' ) ?>
                        <span id="found-results"> 0</span> <?php _e( 'Results Near You', 'store-location' ) ?>
                    </h5>
					<?php if ( $attr['map'] === 'no' ) { ?>
                        <div class="store-locations-list-nav">
                            <h4><?php esc_html_e( 'Study Site', 'store-location' ); ?></h4>
                            <h4><?php esc_html_e( 'Study Provider', 'store-location' ); ?></h4>
                            <h4><?php esc_html_e( 'Address', 'store-location' ); ?></h4>
                            <h4><?php esc_html_e( 'Contact', 'store-location' ); ?></h4>
                        </div>
					<?php } ?>
                    <div class="store-locations-list" id="ajax_results_wrapper">
                    </div>
                </div>
				<?php if ( $attr['map'] === 'yes' ) { ?>
                    <div class="store-map-wrapper">
                        <div id="store-map">
                        </div>
                    </div>
				<?php } ?>
            </div>
			<?php

			return ob_get_clean();
		}

		public function address_location_custom_box($post): void {
			$location_data = $this->get_location_data($post->ID);
			$countries = Location_Config::get_countries();
			$languages = Location_Config::get_languages();
			
			wp_nonce_field('location_meta_box', 'location_meta_box_nonce');
			?>
			<div class="location-meta-box">
				<div class="location-section">
					<h3><?php _e('Languages', 'store-location'); ?></h3>
					<div class="language-options">
						<?php foreach ($languages as $code => $lang): ?>
							<label>
								<input type="checkbox" 
									   name="languages[]" 
									   value="<?php echo esc_attr($code); ?>"
									   <?php checked(in_array($code, $location_data['languages'])); ?>>
								<?php echo esc_html($lang['name']); ?>
							</label>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="location-section">
					<h3><?php _e('Address Information', 'store-location'); ?></h3>
					<p>
						<label for="store_address"><?php _e('Address 1', 'store-location'); ?></label>
						<input type="text" id="store_address" name="store_address" 
							   value="<?php echo esc_attr($location_data['address']['street1']); ?>">
					</p>
					<p>
						<label for="address_2"><?php _e('Address 2', 'store-location'); ?></label>
						<input type="text" id="address_2" name="address_2" 
							   value="<?php echo esc_attr($location_data['address']['street2']); ?>">
					</p>
					<p>
						<label for="store_city"><?php _e('City', 'store-location'); ?></label>
						<input type="text" id="store_city" name="store_city" 
							   value="<?php echo esc_attr($location_data['address']['city']); ?>">
					</p>
					<p>
						<label for="store_state"><?php _e('State', 'store-location'); ?></label>
						<input type="text" id="store_state" name="store_state" 
							   value="<?php echo esc_attr($location_data['address']['state']); ?>">
					</p>
					<p>
						<label for="store_zipcode"><?php _e('Zip Code', 'store-location'); ?></label>
						<input type="text" id="store_zipcode" name="store_zipcode" 
							   value="<?php echo esc_attr($location_data['address']['zip']); ?>">
					</p>
					<p>
						<label for="country"><?php _e('Country', 'store-location'); ?></label>
						<select id="country" name="country">
							<option value=""><?php _e('Select Country', 'store-location'); ?></option>
							<?php foreach ($countries as $code => $name): ?>
								<option value="<?php echo esc_attr($code); ?>" 
										<?php selected($code, $location_data['address']['country']); ?>>
									<?php echo esc_html($name); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</p>
				</div>

				<div class="location-section">
					<h3><?php _e('Contact Information', 'store-location'); ?></h3>
					<p>
						<label for="provider_name"><?php _e('Provider Name', 'store-location'); ?></label>
						<input type="text" id="provider_name" name="provider_name" 
							   value="<?php echo esc_attr($location_data['contact']['provider_name']); ?>">
					</p>
					<p>
						<label for="contact_name"><?php _e('Contact Name', 'store-location'); ?></label>
						<input type="text" id="contact_name" name="contact_name" 
							   value="<?php echo esc_attr($location_data['contact']['contact_name']); ?>">
					</p>
					<p>
						<label for="phone_no"><?php _e('Phone', 'store-location'); ?></label>
						<input type="tel" id="phone_no" name="phone_no" 
							   value="<?php echo esc_attr($location_data['contact']['phone']); ?>">
					</p>
					<p>
						<label for="email"><?php _e('Email', 'store-location'); ?></label>
						<input type="email" id="email" name="email" 
							   value="<?php echo esc_attr($location_data['contact']['email']); ?>">
					</p>
					<p>
						<label for="website_url"><?php _e('Website', 'store-location'); ?></label>
						<input type="url" id="website_url" name="website_url" 
							   value="<?php echo esc_url($location_data['contact']['website']); ?>">
					</p>
				</div>

				<div class="location-section">
					<h3><?php _e('Map Information', 'store-location'); ?></h3>
					<p>
						<label for="map_lat"><?php _e('Latitude', 'store-location'); ?></label>
						<input type="text" id="map_lat" name="map_lat" readonly 
							   value="<?php echo esc_attr($location_data['coordinates']['lat']); ?>">
					</p>
					<p>
						<label for="map_lng"><?php _e('Longitude', 'store-location'); ?></label>
						<input type="text" id="map_lng" name="map_lng" readonly 
							   value="<?php echo esc_attr($location_data['coordinates']['lng']); ?>">
					</p>
					<p>
						<button type="button" id="geo_code_btn" class="button">
							<?php _e('Geocode Address', 'store-location'); ?>
						</button>
					</p>
				</div>

				<div class="location-section">
					<h3><?php _e('Additional Information', 'store-location'); ?></h3>
					<p>
						<label for="study"><?php _e('Study', 'store-location'); ?></label>
						<input type="text" id="study" name="study" 
							   value="<?php echo esc_attr($location_data['study']); ?>">
					</p>
				</div>
			</div>
			<?php
		}

		public function save_address_location($post_id): void {
			if (!isset($_POST['location_meta_box_nonce']) || 
				!wp_verify_nonce($_POST['location_meta_box_nonce'], 'location_meta_box')) {
				return;
			}

			if (!current_user_can('edit_post', $post_id)) {
				return;
			}

			try {
				// Save consolidated data
				$this->save_location_data($post_id, $_POST);

				// Geocode if address changed
				$address_parts = array_filter([
					$_POST['store_address'] ?? '',
					$_POST['store_city'] ?? '',
					$_POST['store_state'] ?? '',
					$_POST['country'] ?? '',
					$_POST['store_zipcode'] ?? ''
				]);
				
				if (!empty($address_parts)) {
					$full_address = implode(', ', $address_parts);
					$location = $this->geocode($full_address);
					
					if ($location) {
						$data = $this->get_location_data($post_id);
						$data['coordinates'] = [
							'lat' => $location['lat'],
							'lng' => $location['lng']
						];
						$data['address']['formatted'] = $location['formatted_address'];
						$this->save_location_data($post_id, $data);
					}
				}
			} catch (\Exception $e) {
				error_log('Error saving location: ' . $e->getMessage());
				add_action('admin_notices', function() {
					echo '<div class="error"><p>An error occurred while saving the location. Please try again.</p></div>';
				});
			}
		}

		public function columns_locations($columns): array {
			return [
				'cb' => $columns['cb'],
				'title' => __('Title', 'store-location'),
				'languages' => __('Languages', 'store-location'),
				'address' => __('Address', 'store-location'),
				'contact' => __('Contact', 'store-location'),
				'study' => __('Study', 'store-location')
			];
		}

		public function columns_locations_data($column, $post_id): void {
			$location_data = $this->get_location_data($post_id);
			
			switch ($column) {
				case 'languages':
					$languages = Location_Config::get_languages();
					$active = array_intersect_key($languages, array_flip($location_data['languages']));
					echo implode(', ', array_column($active, 'name'));
					break;
					
				case 'address':
					$address = $location_data['address'];
					$parts = array_filter([
						$address['street1'],
						$address['city'],
						$address['state'],
						$address['zip']
					]);
					echo implode(', ', $parts);
					break;
					
				case 'contact':
					$contact = $location_data['contact'];
					if ($contact['provider_name']) {
						echo '<strong>' . esc_html($contact['provider_name']) . '</strong><br>';
					}
					if ($contact['phone']) {
						echo esc_html($contact['phone']) . '<br>';
					}
					if ($contact['email']) {
						echo '<a href="mailto:' . esc_attr($contact['email']) . '">' . 
							 esc_html($contact['email']) . '</a>';
					}
					break;
					
				case 'study':
					echo esc_html($location_data['study']);
					break;
			}
		}

		/**
		 * Geocode an address with error handling and caching
		 */
		public function geocode($address): ?array {
			if (empty($address)) {
				return null;
			}

			// Generate cache key
			$cache_key = 'geocode_' . md5($address);
			
			// Try to get cached result
			$cached_result = get_transient($cache_key);
			if (false !== $cached_result) {
				return $cached_result;
			}

			// Get API key
			$api_key = get_option('gmap_api_key');
			if (empty($api_key)) {
				error_log('Google Maps API key is not set');
				return null;
			}

			try {
				// Prepare the address for the URL
				$address = urlencode($address);
				
				// Create the URL
				$url = sprintf(
					'https://maps.googleapis.com/maps/api/geocode/json?address=%s&key=%s',
					$address,
					$api_key
				);

				// Make the request
				$response = wp_remote_get($url, [
					'timeout' => 5,
					'headers' => ['Accept' => 'application/json']
				]);

				// Check for errors
				if (is_wp_error($response)) {
					throw new \Exception($response->get_error_message());
				}

				// Parse the response
				$data = json_decode(wp_remote_retrieve_body($response), true);

				// Check response status
				if ($data['status'] !== 'OK') {
					throw new \Exception('Geocoding failed: ' . ($data['error_message'] ?? $data['status']));
				}

				// Extract the location data
				$result = [
					'lat' => $data['results'][0]['geometry']['location']['lat'],
					'lng' => $data['results'][0]['geometry']['location']['lng'],
					'formatted_address' => $data['results'][0]['formatted_address']
				];

				// Cache the result for 30 days
				set_transient($cache_key, $result, 30 * DAY_IN_SECONDS);

				return $result;
			} catch (\Exception $e) {
				error_log('Geocoding error: ' . $e->getMessage());
				return null;
			}
		}

		/**
		 * Save location data
		 */
		private function save_location_data($post_id, array $data): void {
			$location_data = [
				'address' => [
					'street1' => sanitize_text_field($data['store_address'] ?? ''),
					'street2' => sanitize_text_field($data['address_2'] ?? ''),
					'city' => sanitize_text_field($data['store_city'] ?? ''),
					'state' => sanitize_text_field($data['store_state'] ?? ''),
					'zip' => sanitize_text_field($data['store_zipcode'] ?? ''),
					'country' => sanitize_text_field($data['country'] ?? ''),
					'formatted' => sanitize_text_field($data['formatted_address'] ?? '')
				],
				'contact' => [
					'phone' => sanitize_text_field($data['phone_no'] ?? ''),
					'email' => sanitize_email($data['email'] ?? ''),
					'website' => esc_url_raw($data['website_url'] ?? ''),
					'provider_name' => sanitize_text_field($data['provider_name'] ?? ''),
					'contact_name' => sanitize_text_field($data['contact_name'] ?? '')
				],
				'coordinates' => [
					'lat' => (float)($data['map_lat'] ?? 0),
					'lng' => (float)($data['map_lng'] ?? 0)
				],
				'languages' => array_map('sanitize_text_field', (array)($data['languages'] ?? ['en'])),
				'study' => sanitize_text_field($data['study'] ?? ''),
				'updated_at' => current_time('mysql')
			];

			update_post_meta($post_id, '_location_data', $location_data);
		}

		/**
		 * Get location data
		 */
		private function get_location_data($post_id): array {
			$default_data = [
				'address' => [
					'street1' => '',
					'street2' => '',
					'city' => '',
					'state' => '',
					'zip' => '',
					'country' => '',
					'formatted' => ''
				],
				'contact' => [
					'phone' => '',
					'email' => '',
					'website' => '',
					'provider_name' => '',
					'contact_name' => ''
				],
				'coordinates' => [
					'lat' => 0,
					'lng' => 0
				],
				'languages' => ['en'],
				'study' => '',
				'updated_at' => ''
			];

			$data = get_post_meta($post_id, '_location_data', true);
			return is_array($data) ? array_merge($default_data, $data) : $default_data;
		}

		public function search_location_near(): void {

			$lat       = ! empty( $_POST['pos_lat'] ) ? $_POST['pos_lat'] : '';
			$lng       = ! empty( $_POST['pos_lng'] ) ? $_POST['pos_lng'] : '';
			$lang      = ! empty( $_POST['lang'] ) ? $_POST['lang'] : '';
			$show_map  = $_POST['show_map'];
			$study     = ! empty( $_POST['study'] ) ? $_POST['study'] : '';
			$locations = [];

			if ( ! empty( $lat ) && ! empty( $lng ) ) {
				$locations_result = $this->get_nearby_locations( $lat, $lng, $lang, DISTANCE_MILES );

				if ( ! empty ( $locations_result ) ) {
					foreach ( $locations_result as $loc ) {
						if ( ! empty( $lang ) ) {
							$language_code = explode( ',', get_post_meta( $loc['post_id'], 'languages', true ) );
							if ( ! in_array( $lang, $language_code ) ) {
								continue;
							}
						}
						// add post_status filter
						if ( 'publish' != get_post_status( $loc['post_id'] ) ) {
							continue;
						}
						$post_id     = $loc['post_id'];
						$locations[] = array(
							'post_id'       => $loc['post_id'],
							'post_title'    => $loc['post_title'],
							'lat'           => $loc['lat'],
							'lng'           => $loc['lng'],
							'distance'      => 0,
							'meta_key'      => 'map_lat',
							'store_address' => get_post_meta( $post_id, 'store_address', true ),
							'address_2'     => get_post_meta( $post_id, 'address_2', true ),
							'store_city'    => get_post_meta( $post_id, 'store_city', true ),
							'store_state'   => get_post_meta( $post_id, 'store_state', true ),
							'store_zipcode' => get_post_meta( $post_id, 'store_zipcode', true ),
						);
					}
				}
			} else {
				$args = array(
					'post_type'      => 'locations',
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
					'meta_query'     => array()
				);

				if ( $lang ) {
					$args['meta_query'][] = array(
						'key'     => 'languages',
						'value'   => $lang,
						'compare' => 'like'
					);
				}
				if ( $study ) {
					// Remove " or " from the study string
					$study = str_replace( array( '"', '"', '"' ), '', $study );

					// Remove any white space from the study string
					$study = trim( $study );

					// Check if the study has a comma
					if ( str_contains( $study, ',' ) ) {
						$study = explode( ',', $study );

						$args['meta_query'][] = array(
							array(
								'relation' => 'OR',
								array(
									'key'     => 'study',
									'value'   => $study[0],
									'compare' => 'LIKE',
								),
								array(
									'key'     => 'study',
									'value'   => $study[1],
									'compare' => 'LIKE',
								),
							),
						);
					} else {
						$args['meta_query'][] = array(
							'key'     => 'study',
							'value'   => $study,
							'compare' => 'LIKE'
						);
					}
				}

				$the_query = new WP_Query( $args );
				if ( $the_query->have_posts() ) {
					while ( $the_query->have_posts() ) {
						$the_query->the_post();
						$post_id = get_the_ID();

						// If the lang is set, then check if the post has the lang
						if ( ! empty( $lang ) ) {
							$language_code = explode( ',', get_post_meta( $post_id, 'languages', true ) );
							if ( ! in_array( $lang, $language_code ) ) {
								continue;
							}
						}

						$locations[] = array(
							'post_id'       => $post_id,
							'post_title'    => get_the_title(),
							'lat'           => get_post_meta( $post_id, 'map_lat', true ),
							'lng'           => get_post_meta( $post_id, 'map_lng', true ),
							'distance'      => 0,
							'meta_key'      => 'map_lat',
							'store_address' => get_post_meta( $post_id, 'store_address', true ),
							'address_2'     => get_post_meta( $post_id, 'address_2', true ),
							'store_city'    => get_post_meta( $post_id, 'store_city', true ),
							'store_state'   => get_post_meta( $post_id, 'store_state', true ),
							'store_zipcode' => get_post_meta( $post_id, 'store_zipcode', true ),
						);
					}
				}
				wp_reset_postdata();
				wp_reset_query();

			}

			if ( ! empty( $locations ) ) {
				$total_found   = count( $locations );
				$map_locations = [];

				if ( $show_map === 'yes' ) {
					$map_locations = $this->build_map_locations( $locations );
				}

				$list_locations = $this->build_locations_list( $locations, $show_map );
				$all_data       = array(
					'status'        => true,
					'all_locations' => $map_locations,
					'content'       => $list_locations,
					'lang'          => $lang,
					'total_found'   => $total_found,
					'args'          => $args,
					'show_map'      => $show_map,
					'locations'     => $locations
				);

				wp_send_json( $all_data );
			} else {
				$not_found = __( 'Sorry no data found.', 'store-location' );
				$res       = array(
					'status'   => false,
					'args'     => $args,
					'lang'     => $lang,
					'show_map' => $show_map,
					'message'  => $not_found
				);
				wp_send_json( $res );
			}
		}

		public function build_map_locations( $locations ): array {
			$response_loc = [];
			$i            = 1;
			if ( count( $locations ) > 0 ) {
				foreach ( $locations as $location ) {
					$lat = $location['lat'];
					$lng = $location['lng'];
					if ( $lat == '' || $lng == '' ) {
						continue;
					}

					$address_parts = [];

					$loc_id    = $location['post_id'];
					$address_1 = get_post_meta( $loc_id, "store_address", true );
					$address_2 = get_post_meta( $loc_id, "address_2", true );
					$city      = get_post_meta( $loc_id, "store_city", true );
					$state     = get_post_meta( $loc_id, "store_state", true );
					$zipcode   = get_post_meta( $loc_id, "store_zipcode", true );

					if ( $address_1 ) {
						$address_parts[] = $address_1;
					}

					if ( $address_2 ) {
						$address_parts[] = $address_2;
					}

					if ( $city ) {
						$address_parts[] = $city;
					}

					$state_zip = '';

					if ( $state ) {
						$state_zip .= $state;
					}

					if ( $zipcode ) {
						$state_zip .= ( $state ? ' ' : '' ) . $zipcode;
					}

					if ( $state_zip ) {
						$address_parts[] = $state_zip;
					}

					$address = implode( ', ', $address_parts );

					$phone         = get_post_meta( $loc_id, "phone_no", true ) ? get_post_meta( $loc_id, "phone_no", true ) : '';
					$email         = get_post_meta( $loc_id, "email", true ) ? get_post_meta( $loc_id, "email", true ) : '';
					$provider_name = get_post_meta( $loc_id, "provider_name", true ) ? get_post_meta( $loc_id, "provider_name", true ) : '';
					$contact_name  = get_post_meta( $loc_id, "contact_name", true ) ? get_post_meta( $loc_id, "contact_name", true ) : '';

					$response_loc[] = array(
						'ID'             => $i,
						'name'           => $location['post_title'],
						'address'        => $address,
						'latitude'       => $lat,
						'longitude'      => $lng,
						'phone'          => $phone,
						'email'          => $email,
						'show_direction' => $this->show_direction,
						'provider_name'  => $provider_name,
						'contact_name'   => $contact_name,
						'direction'      => __( 'Get directions', 'store-location' )
					);
					$i ++;
				}
			}

			return $response_loc;
		}

		public function build_locations_list( $locations, $show_map = 'yes' ): bool|string {
			ob_start();

			if ( count( $locations ) > 0 ) {
				$j = 1;
				foreach ( $locations as $location ) {
					$lat = $location['lat'];
					$lng = $location['lng'];

					if ( $lat == '' || $lng == '' ) {
						continue;
					}

					$loc_id        = $location['post_id'];
					$phone         = get_post_meta( $loc_id, "phone_no", true ) ? get_post_meta( $loc_id, "phone_no", true ) : '';
					$email         = get_post_meta( $loc_id, "email", true ) ? get_post_meta( $loc_id, "email", true ) : '';
					$provider_name = get_post_meta( $loc_id, "provider_name", true ) ? get_post_meta( $loc_id, "provider_name", true ) : '';
					$contact_name  = get_post_meta( $loc_id, "contact_name", true ) ? get_post_meta( $loc_id, "contact_name", true ) : '';

					$address_parts = [];

					$address_1 = $location['store_address'];
					$address_2 = $location['address_2'];
					$city      = $location['store_city'];
					$state     = $location['store_state'];
					$zipcode   = $location['store_zipcode'];

					if ( $address_1 ) {
						$address_parts[] = $address_1;
					}

					if ( $address_2 ) {
						$address_parts[] = $address_2;
					}

					if ( $city ) {
						$address_parts[] = $city;
					}

					$state_zip = '';

					if ( $state ) {
						$state_zip .= $state;
					}

					if ( $zipcode ) {
						$state_zip .= ( $state ? ' ' : '' ) . $zipcode;
					}

					if ( $state_zip ) {
						$address_parts[] = $state_zip;
					}

					$address = implode( ', ', $address_parts );

					$directions = $address ?: $lat . ',' . $lng;

					if ( $show_map == 'no' ) { ?>
                        <div class="vc_row row-internal row-container">
                            <div class="row row-child">
                                <div class="wpb_row row-inner" style="height: 102px;">
                                    <div class="wpb_column pos-top pos-center align_left column_child col-lg-3 single-internal-gutter OneLinkNoTx">
                                        <div class="uncol style-light">
                                            <div class="uncoltable">
                                                <div class="uncell no-block-padding">
                                                    <div class="uncont">
                                                        <div class="uncode_text_column">
                                                            <p>
                                                                <strong>
																	<?php echo $location['post_title']; ?>
                                                                </strong>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="wpb_column pos-top pos-center align_left column_child col-lg-3 single-internal-gutter OneLinkNoTx">
                                        <div class="uncol style-light">
                                            <div class="uncoltable">
                                                <div class="uncell no-block-padding">
                                                    <div class="uncont">
                                                        <div class="uncode_text_column">
                                                            <p>
																<?php if ( ! empty( $provider_name ) ) {
																	echo $provider_name;
																} ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="wpb_column pos-top pos-center align_left column_child col-lg-3 single-internal-gutter OneLinkNoTx">
                                        <div class="uncol style-light">
                                            <div class="uncoltable">
                                                <div class="uncell no-block-padding">
                                                    <div class="uncont">
                                                        <div class="uncode_text_column">
                                                            <p>
																<?php echo $address_1 . ' ' . $address_2; ?>
                                                                <br>
																<?php echo $city; ?>, <?php echo $state; ?> <?php echo $zipcode; ?>
                                                            </p>

															<?php if ( $this->show_direction ) { ?>
                                                                <p>
                                                                    <a href="https://www.google.com/maps?q=<?php echo $directions; ?>"
                                                                       target="_blank">
																		<?php _e( 'Get Directions', 'store-location' ); ?>
                                                                    </a>
                                                                </p>
															<?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="wpb_column pos-top pos-center align_left column_child col-lg-3 single-internal-gutter OneLinkNoTx">
                                        <div class="uncol style-light">
                                            <div class="uncoltable">
                                                <div class="uncell no-block-padding">
                                                    <div class="uncont">
                                                        <div class="uncode_text_column">
                                                            <p>
																<?php if ( ! empty ( $contact_name ) ) { ?>
                                                                    <span>
																		<?php echo $contact_name; ?>
																	</span><br/>
																<?php }
																if ( ! empty( $email ) ) { ?>
                                                                    <a href="mailto:<?php echo esc_html( $email ); ?>">
																		<?php echo esc_html( $email ); ?>
                                                                    </a><br/>
																<?php }
																if ( ! empty( $phone ) ) { ?>
                                                                    <a href="tel:<?php echo preg_replace( "/[^0-9]/", "", $phone ) ?>">
																		<?php echo $phone; ?>
                                                                    </a>
																<?php } ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="divider-wrapper result-diviser">
                            <hr class="separator-no-padding">
                        </div>
					<?php } else { ?>
                        <div class="store-location loc-row" id="loc-<?php echo $j; ?>">
                            <div class="OneLinkNoTx">
                                <h5 class="store-location-name">
									<?php echo $location['post_title']; ?>
                                </h5>
								<?php if ( ! empty( $provider_name ) ) { ?>
                                    <div class="store-location-provider">
									<span>
										<?php echo $provider_name; ?>
									</span>
                                    </div>
								<?php } ?>
                                <address class="store-location-address">
									<?php echo $address; ?>
                                </address>
								<?php if ( ! empty ( $contact_name ) ) { ?>
                                    <div class="store-location-phone">
									<span>
										<?php echo $contact_name; ?>
									</span>
                                    </div>
								<?php } ?>
								<?php if ( ! empty ( $phone ) ) { ?>
                                    <div class="store-location-phone">
                                        <a href="tel:<?php echo $phone; ?>">
											<?php echo $phone; ?>
                                        </a>
                                    </div>
								<?php } ?>
								<?php if ( ! empty ( $email ) ) { ?>
                                    <div class="store-location-email">
                                        <a href="mail-to:<?php echo esc_html( $email ); ?>">
											<?php echo esc_html( $email ); ?>
                                        </a>
                                    </div>
								<?php } ?>
                            </div>
                            <div class="store-location-btn-wrapper OneLinkTx">
                                <a href="javascript:void(0);" data-id="<?php echo $j; ?>" data-lat="<?php echo $lat; ?>"
                                   data-lng="<?php echo $lng; ?>" class="store-location-btn">
									<?php _e( 'View On Map', 'store-location' ); ?>
                                </a>
                            </div>
                        </div>
					<?php }
					$j ++;
				}
			}

			return ob_get_clean();
		}

		/**
		 * Get nearby locations with optimized query and caching
		 */
		public function get_nearby_locations($lat, $lng, $lang = '', $distance = 50): array {
			global $wpdb;
			
			// Generate cache key
			$cache_key = "nearby_locations_{$lat}_{$lng}_{$lang}_{$distance}";
			$cached_results = wp_cache_get($cache_key);
			
			if (false !== $cached_results) {
				return $cached_results;
			}

			// Sanitize inputs
			$lat = floatval($lat);
			$lng = floatval($lng);
			$distance = intval($distance);

			// Get locations with coordinates
			$locations = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT 
						p.ID as post_id,
						p.post_title,
						pm.meta_value as location_data
					FROM 
						{$wpdb->posts} p
						INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
					WHERE 
						p.post_type = 'locations'
						AND p.post_status = 'publish'
						AND pm.meta_key = '_location_data'",
					[]
				),
				ARRAY_A
			);

			$nearby_locations = [];
			foreach ($locations as $location) {
				$data = maybe_unserialize($location['location_data']);
				if (!is_array($data) || empty($data['coordinates'])) {
					continue;
				}

				$loc_lat = $data['coordinates']['lat'];
				$loc_lng = $data['coordinates']['lng'];

				// Calculate distance using Haversine formula
				$distance_calc = $this->calculate_distance($lat, $lng, $loc_lat, $loc_lng);
				
				if ($distance_calc <= $distance) {
					$nearby_locations[] = [
						'post_id' => $location['post_id'],
						'post_title' => $location['post_title'],
						'lat' => $loc_lat,
						'lng' => $loc_lng,
						'distance' => $distance_calc,
						'data' => $data
					];
				}
			}

			// Sort by distance
			usort($nearby_locations, function($a, $b) {
				return $a['distance'] <=> $b['distance'];
			});

			// Cache results for 1 hour
			wp_cache_set($cache_key, $nearby_locations, '', HOUR_IN_SECONDS);
			
			return $nearby_locations;
		}

		/**
		 * Calculate distance between two points using Haversine formula
		 */
		private function calculate_distance($lat1, $lon1, $lat2, $lon2): float {
			$theta = $lon1 - $lon2;
			$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
			$dist = acos($dist);
			$dist = rad2deg($dist);
			$miles = $dist * 60 * 1.1515;
			
			return round($miles, 2);
		}

		//****************************************************************************/
		// Admin Custom Columns Languages and Country
		//****************************************************************************/
		function load_edit(): void {
			global $typenow;

			// Adjust the Post Type
			if ( 'locations' !== $typenow ) {
				return;
			}

			add_filter( 'posts_where', [ $this, 'posts_where' ] );
		}

		function posts_where( $where ) {
			global $wpdb;

			if ( ! empty( $_GET['store-language'] ) ) {
				$meta  = esc_sql( $_GET['store-language'] );
				$where .= " AND ID IN (SELECT post_id FROM $wpdb->postmeta WHERE meta_value LIKE '%$meta%' AND $wpdb->postmeta.meta_key = 'languages' )";
			}

			if ( ! empty( $_GET['store-country'] ) ) {
				$meta = esc_sql( $_GET['store-country'] );

				if ( 'not-set' == $_GET['store-country'] ) {
					$where .= " AND ( 
			        					NOT EXISTS (SELECT * FROM $wpdb->postmeta WHERE $wpdb->postmeta.meta_key = 'country' AND $wpdb->postmeta.post_id=$wpdb->posts.ID ) 
			        					OR
			        					ID IN (SELECT post_id FROM $wpdb->postmeta WHERE $wpdb->postmeta.meta_key = 'country' AND meta_value = ' ' )
			        )";
				} else {
					$where .= " AND ID IN (SELECT post_id FROM $wpdb->postmeta WHERE meta_value LIKE '$meta' )";
				}
			}
			if ( ! empty( $_GET['store-study'] ) ) {
				$meta = esc_sql( $_GET['store-study'] );

				if ( 'not-set' == $_GET['store-study'] ) {
					$where .= " AND ( 
			        					NOT EXISTS (SELECT * FROM $wpdb->postmeta WHERE $wpdb->postmeta.meta_key = 'study' AND $wpdb->postmeta.post_id=$wpdb->posts.ID ) 
			        					OR
			        					ID IN (SELECT post_id FROM $wpdb->postmeta WHERE $wpdb->postmeta.meta_key = 'study' AND meta_value = ' ' )
			        )";
				} else {
					$where .= " AND ID IN (SELECT post_id FROM $wpdb->postmeta WHERE meta_value LIKE '$meta' )";
				}
			}

			return $where;
		}

		function restrict_manage_posts( $post_type ): void {
			global $wpdb;

			if ( 'locations' !== $post_type ) {
				return;
			}

			// languages
			$active_languages = get_option( 'store-loc-active-languages' );

			$active_languages_array = array();
			if ( ! empty( $active_languages ) ) {
				$active_languages_array = explode( ',', $active_languages );
			}

			// countries
			$countries_res = $wpdb->get_results( "SELECT DISTINCT meta_value FROM $wpdb->postmeta pm, $wpdb->posts p WHERE meta_key = 'country' and pm.post_id=p.ID  and p.post_type='locations' ", ARRAY_A );
			$countries     = array( 'not-set' );

			if ( is_array( $countries_res ) ) {
				foreach ( $countries_res as $value ) {
					if ( ! empty( $value['meta_value'] ) ) {
						$countries[] = $value['meta_value'];
					}
				}
			}

			// Study
			$studies_res = $wpdb->get_results( "SELECT DISTINCT meta_value FROM $wpdb->postmeta pm, $wpdb->posts p WHERE meta_key = 'study' and pm.post_id=p.ID  and p.post_type='locations' ", ARRAY_A );
			$studies     = array( 'not-set' );

			if ( is_array( $studies_res ) ) {
				foreach ( $studies_res as $value ) {
					if ( ! empty( $value['meta_value'] ) ) {
						$studies[] = $value['meta_value'];
					}
				}
			}

			if ( ! empty( $active_languages ) ) { ?>
                <label>
                    <select name="store-language">
                        <option value="">
							<?php _e( 'All Languages', 'store-location' ); ?>
                        </option>
						<?php $current_lang = $_GET['store-language'] ?? '';
						foreach ( $active_languages_array as $value ) {
							$selected       = selected( $value, $current_lang, false );
							$language_label = $this->flags_array[ $value ];
							?>
                            <option value="<?php echo $value ?>" <?php echo $selected ?>>
								<?php echo $language_label[2]; ?> ( <?php echo $language_label[0]; ?> )
                            </option>
						<?php } ?>
                    </select>
                </label>
			<?php } ?>

			<?php if ( is_array( $countries ) ) { ?>
                <label>
                    <select name="store-country">
                        <option value="">
							<?php _e( 'All Countries', 'store-location' ); ?>
                        </option>
						<?php $current_country = $_GET['store-country'] ?? '';
						foreach ( $countries as $value ) {
							$selected = selected( $value, $current_country, false );
							if ( 'not-set' == $value ) { ?>
                                <option value="not-set" <?php echo $selected ?> >Not Set</option>
							<?php } else { ?>
                                <option value="<?php echo $value ?>" <?php echo $selected ?>>
									<?php echo $this->country_list[ strtoupper( $value ) ]; ?>
                                </option>
							<?php } ?>
						<?php } ?>
                    </select>
                </label>
			<?php } ?>

			<?php if ( is_array( $studies ) ) { ?>
                <label>
                    <select name="store-study">
                        <option value="">
							<?php _e( 'All Studies', 'store-location' ); ?>
                        </option>
						<?php $current_study = $_GET['store-study'] ?? ''; ?>
						<?php foreach ( $studies as $value ) {
							$selected = selected( $value, $current_study, false );
							if ( 'not-set' === $value ) { ?>
                                <option value="not-set" <?php echo $selected ?>>Not Set</option>
							<?php } else { ?>
                                <option value="<?php echo $value ?>" <?php echo $selected ?>>
									<?php echo $value; ?>
                                </option>
							<?php }
						} ?>
                    </select>
                </label>
			<?php }
		}

		public function update_request() {

			$remote = get_transient( $this->cache_key );

			if ( false === $remote ) {

				$remote = wp_remote_get( 'https://raw.githubusercontent.com/LoreStudio/jh-wp-site-listing/main/store-locations-info.json', [
					'timeout' => 10,
					'headers' => [
						'Accept' => 'application/json'
					]
				] );

				if ( is_wp_error( $remote ) || 200 !== wp_remote_retrieve_response_code( $remote ) || empty( wp_remote_retrieve_body( $remote ) ) ) {
					return false;
				}

				set_transient( $this->cache_key, $remote, 120 );

			}

			return json_decode( wp_remote_retrieve_body( $remote ) );

		}

		function update_info( $response, $action, $args ) {

			// do nothing if you're not getting plugin information right now
			if ( 'plugin_information' !== $action ) {
				return $response;
			}

			// do nothing if it is not our plugin
			if ( empty( $args->slug ) || $this->plugin_slug !== $args->slug ) {
				return $response;
			}

			// get updates
			$remote = $this->update_request();

			if ( ! $remote ) {
				return $response;
			}

			$response = new \stdClass();

			$response->name          = $remote->name;
			$response->slug          = $remote->slug;
			$response->version       = $remote->version;
			$response->download_link = $remote->download_url;
			$response->trunk         = $remote->download_url;
			$response->last_updated  = $remote->last_updated;

			$response->sections = [
				'description'  => $remote->sections->description,
				'installation' => $remote->sections->installation,
				'changelog'    => $remote->sections->changelog
			];


			return $response;

		}

		public function plugin_update( $transient ) {

			if ( empty( $transient->checked ) ) {
				return $transient;
			}

			$remote = $this->update_request();

			if ( $remote && version_compare( $this->version, $remote->version, '<' ) ) {
				$response              = new \stdClass();
				$response->slug        = $this->plugin_slug;
				$response->plugin      = "jh-wp-site-listing-main/store-locations.php";
				$response->new_version = $remote->version;
				$response->package     = $remote->download_url;

				$transient->response[ $response->plugin ] = $response;

			}

			return $transient;
		}

		public function update_purge( $upgrader, $options ) {
			if ( $this->cache_allowed && 'update' === $options['action'] && 'plugin' === $options['type'] ) {
				// clean the cache when new plugin version is installed
				delete_transient( $this->cache_key );
			}
		}

		function location_import_page(): void {
			add_submenu_page( 'edit.php?post_type=locations', __( 'Import Locations', 'store-location' ), __( 'Import Locations', 'store-location' ), 'manage_options', 'import-location', [
				$this,
				'location_import_page_html'
			] );
		} // end of upload page

		function location_import_page_html(): void {
			?>
            <div class="wrap">
                <h1 class="page-title">Import Locations</h1>
				<?php
				if ( isset( $_POST['action_type'] ) && $_POST['action_type'] == 'location_upload_csv' ) {
					if ( ( $file = fopen( $_FILES['filename']['tmp_name'], "r" ) ) !== false ) {
						$header = fgetcsv( $file );
						while ( ( $line = fgetcsv( $file ) ) !== false ) {
							$line = array_combine( $header, $line );

							if ( $line["title"] !== '' ) {
								$post_id       = ( isset( $line["post_id"] ) && $line["post_id"] ) ? $line["post_id"] : 0;
								$title         = $line["title"] ?: "";
								$address1      = $line["address1"] ?: "";
								$address2      = $line["address2"] ?: "";
								$city          = $line["city"] ?: "";
								$state         = $line["state"] ?: "";
								$zip           = $line["zip_code"] ?: "";
								$country       = $line["country"] ?: "";
								$website       = $line["website"] ?: "";
								$phone         = $line["phone"] ?: "";
								$email         = $line["email"] ?: "";
								$provider_name = $line["provider_name"] ?: "";
								$contact_name  = $line["contact_name"] ?: "";
								$study         = $line["study"] ?: "";

								$lat  = 0;
								$long = 0;

								if ( $address1 !== '' ) {
									$geo_address = $address1;
									if ( $address2 !== '' ) {
										$geo_address .= " $address2";
									}
									if ( $city !== '' && ! str_contains( strtolower( $geo_address ), strtolower( $city ) ) ) {
										$geo_address .= " $city";
									}
									if ( $state !== '' && ! str_contains( strtolower( $geo_address ), strtolower( $state ) ) ) {
										$geo_address .= ", $state";
									}
									if ( $zip !== '' && ! str_contains( strtolower( $geo_address ), strtolower( $zip ) ) ) {
										$geo_address .= ", $zip";
									}

									$geo_code = $this->geocode( $geo_address );

									if ( $geo_code && is_array( $geo_code ) ) {
										$lat  = $geo_code[0];
										$long = $geo_code[1];
									}
								}

								$post_exists = $post_id ? get_post( $post_id ) : null;

								if ( $post_exists && $post_exists->post_type === 'locations' ) {
									wp_update_post( array(
										'ID'         => $post_id,
										'post_title' => $title,
									) );
								} else {
									$post_id = wp_insert_post( array(
										'post_title'   => $title,
										'post_type'    => 'locations',
										'post_content' => '',
										'post_status'  => 'publish'
									) );
								}

								if ( $post_id ) {
									update_post_meta( $post_id, 'language_code', 'en' );
									update_post_meta( $post_id, 'languages', 'en' );
									update_post_meta( $post_id, 'country', sanitize_text_field( $country ) );
									update_post_meta( $post_id, 'store_address', sanitize_text_field( $address1 ) );
									update_post_meta( $post_id, 'address_2', sanitize_text_field( $address2 ) );
									update_post_meta( $post_id, 'store_city', sanitize_text_field( $city ) );
									update_post_meta( $post_id, 'store_state', sanitize_text_field( $state ) );
									update_post_meta( $post_id, 'store_zipcode', sanitize_text_field( $zip ) );
									update_post_meta( $post_id, 'website_url', sanitize_text_field( $website ) );
									update_post_meta( $post_id, 'phone_no', sanitize_text_field( $phone ) );
									update_post_meta( $post_id, 'email', sanitize_text_field( $email ) );
									update_post_meta( $post_id, 'provider_name', sanitize_text_field( $provider_name ) );
									update_post_meta( $post_id, 'contact_name', sanitize_text_field( $contact_name ) );
									update_post_meta( $post_id, 'study', sanitize_text_field( $study ) );

									if ( $lat && $long ) {
										update_post_meta( $post_id, 'map_lat', $lat );
										update_post_meta( $post_id, 'map_lng', $long );
									}
								}
							}
						}
						echo "<h4 style='color:green;'>Upload Complete!</h4>";
					} else {
						echo "<h4 style='color:red;'>Please upload a valid file.</h4>";
					}
				}
				?>
                <p class="form-description">Import locations by clicking the button below. The file must be a CSV file and once uploaded, the import
                    will begin automatically.</p>
                <form method="POST" enctype='multipart/form-data' class="import-location-form">
                    <input type="hidden" name="action_type" value="location_upload_csv">
                    <label for="import-locations-file" style="display: none;">Select file to being import</label>
                    <input id="import-locations-file" type="file" class="filestyle" name="filename" data-iconName="glyphicon-inbox" accept=".csv"
                           style="display: none;" aria-label="Select file to being import" required aria-required="true">
                    <button class="import-btn">Import</button>
                </form>
            </div>
			<?php
		} // End of Location import page callback	

		function location_export_request(): void {
			if ( isset( $_GET['location-csv-export'] ) ) {

				$export_args = array(
					'post_type'      => 'locations',
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
				);

				$export_locations = get_posts( $export_args );

				if ( $export_locations ) {
					header( 'Content-Type: application/csv' );
					header( 'Content-Disposition: attachment; filename=location-export.csv' );
					$f = fopen( 'php://output', 'w' );
					fputcsv( $f, [
						"post_id",
						"title",
						"address1",
						"address2",
						"city",
						"state",
						"zip_code",
						"country",
						"website",
						"phone",
						"email",
						"provider_name",
						"contact_name",
						"study"
					], "," );

					foreach ( $export_locations as $location ) {
						$post_id       = $location->ID;
						$title         = $location->post_title;
						$address1      = get_post_meta( $post_id, 'store_address', true );
						$address2      = get_post_meta( $post_id, 'address_2', true );
						$city          = get_post_meta( $post_id, 'store_city', true );
						$state         = get_post_meta( $post_id, 'store_state', true );
						$zip           = get_post_meta( $post_id, 'store_zipcode', true );
						$country       = get_post_meta( $post_id, 'country', true );
						$website       = get_post_meta( $post_id, 'website_url', true );
						$phone         = get_post_meta( $post_id, 'phone_no', true );
						$email         = get_post_meta( $post_id, 'email', true );
						$provider_name = get_post_meta( $post_id, 'provider_name', true );
						$contact_name  = get_post_meta( $post_id, 'contact_name', true );
						$study         = get_post_meta( $post_id, 'study', true );

						fputcsv( $f, [
							$post_id,
							$title,
							$address1,
							$address2,
							$city,
							$state,
							$zip,
							$country,
							$website,
							$phone,
							$email,
							$provider_name,
							$contact_name,
							$study
						], "," );
					}

					fclose( $f );
					exit();
				}
			}
		}

		function location_export_page(): void {
			add_submenu_page( 'edit.php?post_type=locations', __( 'Export Locations', 'store-location' ), __( 'Export Locations', 'store-location' ), 'manage_options', 'export-location', [
				$this,
				'location_export_page_html'
			] );
		} // end of upload page

		function location_export_page_html() {
			?>
            <div class="wrap">
                <h1 class="page-title">Export Locations</h1>
                <p class="form-description">Click the button below to export all locations.</p>
                <form method='get' action="" class="export-location-form">
                    <input type="hidden" name="post_type" value="locations"/>
                    <input type="hidden" name="page" value="export-location"/>
                    <input type="hidden" name='location-csv-export' id="csvExport" value="Export"/>
                    <button class="export-btn">Export</button>
                </form>
            </div>
			<?php
		} // End of Location export page callback		

		/**
		 * Add custom field to quick edit screen.
		 */
		function quick_edit_custom_box( $column_name ): void {
			if ( $column_name == 'lang' ) {
				wp_nonce_field( 'store_location_bulk_edit_nonce', 'store_location_bulk_edit_nonce' );

				global $wpdb;

				$active_languages = $wpdb->get_results( "SELECT code, english_name FROM {$wpdb->prefix}icl_languages WHERE active = 1", ARRAY_A );
				?>
                <!-- loop through all active languages -->
                <fieldset class="inline-edit-col-right">
                    <div class="inline-edit-col column-<?php echo esc_attr( $column_name ); ?>">
                        <label class="inline-edit-group">
							<?php esc_html_e( 'Languages: ', 'store-location' ); ?>

							<?php foreach ( $active_languages as $language ) { ?>
                                <label>
                                    <input type="checkbox" name="languages[]" value="<?php echo $language['code']; ?>"/>
									<?php echo $language['english_name']; ?>
                                </label>
							<?php } ?>
                        </label>
                    </div>
                </fieldset>
				<?php
			}
		}

		/**
		 * Determine if we should load plugin assets
		 */
		private function should_load_assets(): bool {
			// Check if we're on a locations page
			if (is_singular('locations')) {
				return true;
			}

			// Check if we're on a page with the shortcode
			global $post;
			if (is_a($post, 'WP_Post') && 
				(has_shortcode($post->post_content, 'store-locations') || 
				 has_block('store-locations/map'))) {
				return true;
			}

			// Check if we're on the admin locations page
			if (is_admin() && 
				isset($_GET['post_type']) && 
				$_GET['post_type'] === 'locations') {
				return true;
			}

			return false;
		}
	}
}

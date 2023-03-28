<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
/**
 * The file contains all the plugin related functions
 */
if ( !class_exists( 'Store_Locations' ) ) {
	class Store_Locations {
		var $show_direction = false;		
		public function __construct() {	
			
			if( 'yes' == esc_attr( get_option( 'show_direction_link' ) ) ){
				$this->show_direction = true;
			}
			add_action( 'init',              		[$this, 'location_register_post_types'] );
			add_action( 'admin_enqueue_scripts', 	[$this, 'admin_store_locations_scripts'] );
			add_action( 'wp_enqueue_scripts', 		[$this, 'store_locations_scripts'] );
			add_action( 'admin_menu', 				[$this, 'store_loc_setting_page'] );

			add_shortcode( 'language', 				[$this, 'get_language_shortcode'] );

			add_shortcode( 'store-locations', 		[$this, 'store_locations_detail'] );

			add_action( 'add_meta_boxes', 			[$this, 'address_location_custom_box'] );
			add_action( 'save_post_locations', 		[$this, 'save_address_location'] );

			add_action( 'wp_ajax_search_location_near', [ $this, 'search_location_near'] );
			add_action( 'wp_ajax_nopriv_search_location_near', [ $this, 'search_location_near'] );
			
		}
		// front end enque scripts
		public function store_locations_scripts(){
			wp_enqueue_script('jquery');
			wp_enqueue_style( 'store-loc-style',  LOCATION_DIR_URI. 'includes/css/store-loc-plugin.css', array(), '1.0.0', 'all');	
			//wp_enqueue_script( 'store-loc-js', LOCATION_DIR_URI . 'includes/js/store-loc.js', array(), '1.0.0', true );	
			wp_register_script("store-loc-js",LOCATION_DIR_URI . 'includes/js/store-loc.js', array(), "1.0", false);
			$apikey = esc_attr(get_option('gmap_api_key'));
			if ( ! empty( $apikey ) ) {
				$map_api = '//maps.googleapis.com/maps/api/js?key=' . $apikey.'&callback=initgMap&libraries=places';
				wp_register_script( 'store-google-map', $map_api );                
			}
			// define some local variable
			$local_variables = [
				'ajax_url' 	=> admin_url( 'admin-ajax.php' ),
				'img_dir' 	=> LOCATION_DIR_URI.'images/',
			];
			wp_localize_script( 'store-loc-js', 'front_object', $local_variables );	
		}
		// only admin scripts
		public function admin_store_locations_scripts( ){
			$apikey = esc_attr(get_option('gmap_api_key'));

			if ( ! empty( $apikey ) ) {
				$map_api = '//maps.googleapis.com/maps/api/js?key=' . $apikey;
				wp_register_script( 'store-google-map', $map_api );
				wp_enqueue_script( 'store-google-map' );
			}				
			wp_enqueue_script( 'admin-store-js', LOCATION_DIR_URI . 'admin/js/admin-store.js', array(), '1.0.0', true );		
		}

		function get_language_shortcode() {
		    return apply_filters( 'wpml_current_language', null );
		}

		public function location_register_post_types( ) {
			
			$labels = array(
				'name' 					=> _x( 'Locations', '', 'store-location' ),
				'singular_name' 		=> _x( 'Locations', '', 'store-location' ),
				'menu_name' 			=> _x( 'Locations',  '', 'store-location' ),
				'name_admin_bar' 		=> _x( 'Locations',  '', 'store-location' ),
				'add_new' 				=> _x( 'Add New Location', '', 'store-location' ),
				'add_new_item' 			=> __( 'Add New Location', 'store-location' ),
				'new_item' 				=> __( 'New Location', 'store-location' ),
				'edit_item' 			=> __( 'Edit Location', 'store-location' ),
				'view_item' 			=> __( 'View Location', 'store-location' ),
				'all_items' 			=> __( 'All Location', 'store-location' ),
				'search_items' 			=> __( 'Search Location', 'store-location' ),
				'parent_item_colon' 	=> __( 'Parent Location:', 'store-location' ),
				'not_found' 			=> __( 'No Location found.', 'store-location' ),
				'not_found_in_trash' 	=> __( 'No Locations found in Trash.', 'store-location')
			);
		
			$args = array(
				'labels' 				=> $labels,
				'description' 			=> __( 'Description.', 'store-location' ),
				'public' 				=> true,
				'menu_icon' 			=> 'dashicons-location',
				'publicly_queryable' 	=> false,
				'show_ui' 				=> true,
				'show_in_menu' 			=> true,
				'query_var' 			=> true,
				'rewrite' 				=> array( 'slug' => '' ),
				'capability_type' 		=> 'post',
				'has_archive' 			=> true,
				'hierarchical' 			=> false,
				'menu_position' 		=> null,
				'exclude_from_search' 	=> true,
				'supports' 				=> array( 'title' )
			);
			register_post_type('locations', $args);
		}
		
		// this is menu setting page
		public function store_loc_setting_page(){
			
			add_menu_page( 'Location Settings', 'Location Settings', 'administrator',  'set-store-loc-setting-page', array($this,'set_store_loc_setting_page'), 'dashicons-admin-site', 59.96);
		
		}
		// set the hours setting for store
		public function set_store_loc_setting_page(){
			// check capability
			if ( current_user_can( 'manage_options' ) ) {
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/store-loc-settings.php';

			} else {
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/restricted-access.php';

			}
		}

		public function is_wpml_active( ) {

			$is_wpml_active = false;

			if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
				$is_wpml_active = true;
			}

			return $is_wpml_active;
		}

		public function get_language_code( ) {

			// include_once ABSPATH . 'wp-admin/includes/plugin.php';
			$language_code = 'en';
			if ( $this->is_wpml_active( ) ) {
				$language_code = apply_filters( 'wpml_current_language', NULL );
				// var_dump_pre( 'wpml active' );
			} else {
				$language_code = isset( $_GET['lang'] ) && ! empty( $_GET['lang'] ) ? sanitize_text_field( $_GET['lang'] ) : $language_code;
				// var_dump_pre( 'no wpml' );
			}
			// var_dump_pre( $language_code );

			return $language_code;

		}

		public function store_locations_detail( $atts = array( ) ){				
				
			$attr = shortcode_atts( 
				array(
					'map' => 'yes',
				), $atts 
			);

			$language_code = $this->get_language_code( );

			ob_start();	
			// var_dump_pre( $language_code );
			?>
			<?php if( esc_attr( $attr['map'] ) == 'yes' ) : ?>
				<script>
					var mapStyle =[
						<?php echo get_option('map_json_style');?>
					 ];
					var marker_icon = '<?php echo get_option( 'gmap_marker_url' ); ?>';
				</script>
			<?php endif; ?>
			<div class="store-info-container <?php echo (esc_attr($attr['map']) == 'no') ? 'no-map':'';?>">
				<div class="store-locations">
					<div class="store-locations-search">
						<form role="search" class="store-search-form">
							<input type="text" value name="search-text" id="search-text" placeholder="<?php _e( 'Enter a location', 'store-location' ); ?>">
							<input type="image" id="search-btn" alt="Search" src="<?php echo LOCATION_DIR_URI.'images/icon-arrow-right.svg'?>">
							<input type="hidden" id="place_lat" value="">
							<input type="hidden" id="place_lng" value="">
							<input type="hidden" id="show_map" name="show_map" value="<?php echo (esc_attr($attr['map']) == 'yes') ? 'yes':'no';?>">
							<input type="hidden" id="language_code" value="<?php echo $language_code; ?>">
						</form>
						<?php  if( esc_attr( $attr['map']) == 'yes' ) : ?>
							<div class="my-location">
								<a href="javascript:void(0);" onClick="getuser_location();">
									<?php _e( 'Use my Location', 'store-location' ); ?>
								</a>
							</div>
					   <?php endif; ?>
					</div>
					<h5 class="store-locations-result-info">
						<?php _e( 'Showing', 'store-location' )?> 
						<span id="found-results"> 0</span> <?php _e( 'Results Near You', 'store-location' )?>
					</h5>
					<div class="store-locations-list" id="ajax_results_wrapper">
					</div>
				</div>
				<?php if( esc_attr( $attr['map'] ) == 'yes' ) : ?>
					<div class="store-map-wrapper">
						<div id="store-map">
						</div>	
					</div>
				<?php endif; ?>
			</div>
			<?php 

			wp_enqueue_script( 'store-loc-js' );
			wp_enqueue_script( 'store-google-map' );

			return ob_get_clean();			
		}
		
		public function address_location_custom_box() {
			$screens = [ 'locations'];
			foreach ( $screens as $screen ) {
				add_meta_box(
					'store_location_box_id',                 // Unique ID
					'Address Information',      // Box title
					[ $this, 'address_custom_box_html' ],  // Content callback, must be of type callable
					$screen                            // Post type
				);
			}
		}

		public function address_custom_box_html( $post ) {
			global $post;
			?>
			<style>
				.form-table td{
					padding:0px;
				}
			</style>
			
			<table class="form-table" role="presentation">
				<tbody>
					<tr class="form-field form-required">
						<th scope="row">
							<label for="store_language">
								<?php esc_html_e( 'Language', 'store-location' );?>
							</label>
						</th>
						<td>
							<!-- wpml language dropdown -->
							<?php $language_code = get_post_meta( $post->ID, 'language_code', true );?>
							<?php if ( $this->is_wpml_active( ) ) : ?>
								<select name="language_code">
									<?php if ( function_exists( 'icl_get_languages' ) ) : ?>
										<?php 
											$langs = icl_get_languages( 'skip_missing=0' ); 
											$language_code = get_post_meta( $post->ID, 'language_code', true );
										?>
										<?php foreach ( $langs as $lang ) : ?>
											<option value="<?php echo $lang['language_code']; ?>" <?php if ( $language_code == $lang['language_code'] ) { echo 'selected="selected"'; } ?>>
												<?php echo $lang["native_name"]; ?> ( <?php echo $lang['language_code']; ?> )
											</option> 
										<?php endforeach; ?>
									<?php endif; ?>
								</select>
							<?php else : ?>
								<input name="language_code" type="text" id="language_code" value="<?php echo $language_code; ?>">
							<?php endif; ?>
						</td>
					</tr>
					<tr class="form-field form-required">
						<th scope="row">
							<label for="store_address">
								<?php esc_html_e( 'Address 1', 'store-location' );?>
							</label>
						</th>
						<td>
							<input name="store_address" type="text" id="store_address" value="<?php echo get_post_meta( $post->ID, 'store_address', true );?>">
						</td>
					</tr>
					<tr class="form-field form-required">
						<th scope="row">
							<label for="address_2">
								<?php esc_html_e( 'Address 2', 'store-location') ;?> 
							</label>
						</th>
						<td>
							<input name="address_2" type="text" id="address_2" value="<?php echo get_post_meta( $post->ID, 'address_2', true );?>">
						</td>
					</tr>
					<tr class="form-field">
						<th scope="row">
							<label for="store_city">
								<?php esc_html_e( 'City', 'store-location' );?> 
							</label>
						</th>
						<td>
							<input name="store_city" type="text" id="store_city" value="<?php echo get_post_meta( $post->ID, 'store_city', true );?>">
						</td>
					</tr>
					<tr class="form-field">
						<th scope="row">
							<label for="store_state">
								<?php esc_html_e( 'State', 'store-location' );?> 
							</label>
						</th>
						<td>
							<input name="store_state" type="text" id="store_state" value="<?php echo get_post_meta( $post->ID, 'store_state', true );?>">
						</td>
					</tr>
					<tr class="form-field">
						<th scope="row">
							<label for="store_zipcode">
								<?php esc_html_e( 'Zip Code', 'store-location' );?> 
							</label>
						</th>
						<td>
							<input name="store_zipcode" type="text" id="store_zipcode" value="<?php echo get_post_meta( $post->ID, 'store_zipcode', true );?>">
						</td>
					</tr>
					<tr class="form-field">
						<th scope="row">
							<label for="map_lat">
								<?php esc_html_e('Latitude', 'store-location') ;?> 
							</label>
						</th>
						<td>
							<input name="map_lat" type="text" id="store_lat" value="<?php echo get_post_meta( $post->ID, 'map_lat', true );?>" readonly><p>Automatically will be filled as click on geocode button</p>
						</td>
					</tr>
					<tr class="form-field">
						<th scope="row">
							<label for="map_lng">
								<?php esc_html_e('Longitude', 'store-location') ;?> 
							</label>
						</th>
						<td>
							<input name="map_lng" type="text" id="store_long" value="<?php echo get_post_meta( $post->ID, 'map_lng', true );?>" readonly><p>Automatically will be filled as click on geocode button</p>
						</td>
					</tr>
					<tr class="form-field">
						<th scope="row">
							<label for="getlat-lng">
								<?php esc_html_e('Get Lat/Long', 'store-location' );?>
							</label>
						</th>
						<td>
							<button type="button" id="geo_code_btn">
								<?php esc_html_e('Geocode Address', 'store-location' );?>
							</button>
						</td>
					</tr>
					
					<tr class="form-field">
						<th scope="row">
							<label for="website_url">
								<?php esc_html_e('Website', 'store-location' );?>
							</label>
						</th>
						<td>
							<input name="website_url" type="text" id="website_url" class="code" value="<?php echo get_post_meta( $post->ID, 'website_url', true );?>">
						</td>
					</tr>
					<tr class="form-field">
						<th scope="row">
							<label for="phone_no">
								<?php esc_html_e('Phone Number', 'store-location' );?>
							</label>
						</th>
						<td>
							<input name="phone_no" type="text" id="phone_no"  value="<?php echo get_post_meta( $post->ID, 'phone_no', true );?>">
						</td>
					</tr>
					<tr class="form-field">
						<th scope="row">
							<label for="provider_name">
								<?php esc_html_e('Provider Name', 'store-location' );?>
							</label>
						</th>
						<td>
							<input name="provider_name" type="text" id="provider_name"  value="<?php echo get_post_meta( $post->ID, 'provider_name', true );?>">
						</td>
					</tr>
					
				</tbody>
			</table>              
		<?php
		}
		
		public function save_address_location( $post_id ){

			// var_dump_pre( $_POST );die();
			update_post_meta( $post_id, 'language_code', 	strtolower( sanitize_text_field ( $_POST['language_code'] ) ) );
			update_post_meta( $post_id, 'store_address', 	sanitize_text_field ( $_POST['store_address'] ) );
			update_post_meta( $post_id, 'address_2', 		sanitize_text_field ( $_POST['address_2'] ) );
			update_post_meta( $post_id, 'store_city', 		sanitize_text_field ( $_POST['store_city'] ) );
			update_post_meta( $post_id, 'store_state', 		sanitize_text_field ( $_POST['store_state'] ) );
			update_post_meta( $post_id, 'store_zipcode',	sanitize_text_field ( $_POST['store_zipcode'] ) );
			update_post_meta( $post_id, 'map_lat', 			$_POST['map_lat'] );
			update_post_meta( $post_id, 'map_lng', 			$_POST['map_lng'] );
			update_post_meta( $post_id, 'website_url', 		sanitize_text_field ( $_POST['website_url'] ) );
			update_post_meta( $post_id, 'phone_no', 		sanitize_text_field ( $_POST['phone_no'] ) );
			update_post_meta( $post_id, 'provider_name', 	sanitize_text_field ( $_POST['provider_name'] ) );
		}
		
		public function search_location_near( ) {

			$lat 		= ! empty( $_POST['pos_lat'] ) ? $_POST['pos_lat'] : '';
			$lng 		= ! empty( $_POST['pos_lng'] ) ? $_POST['pos_lng'] : '';
			$lang 		= ! empty( $_POST['lang'] ) ? $_POST['lang'] : '';
			$show_map 	= $_POST['show_map'];

			if ( ! empty( $lat ) && ! empty( $lng ) ) {
				$locations = []; 
				$locations_result = $this->get_nearby_locations( $lat, $lng, $lang,  DISTANCE_MILES );
				if( !empty( $locations_result )){
					foreach($locations_result as $loc){	
						if( !empty( $lang )){
							$language_code = get_post_meta( $loc['post_id'], 'language_code', true );
							if( $language_code != $lang){
								continue;
							}
						}
						// add post_status filter
						if ( 'publish' != get_post_status( $loc['post_id'] ) ) {
							continue;
						}
						$post_id = $loc['post_id'];
						$locations[] = array (
							'post_id'		=> $loc['post_id'],
							'post_title'	=> $loc['post_title'],
							'lat'			=> $loc['lat'],
							'lng'			=> $loc['lng'],
							'distance'		=> 0,
							'meta_key'		=> 'map_lat',
							'store_address'	=> get_post_meta( $post_id, 'store_address', true ),
							'address_2'		=> get_post_meta( $post_id, 'address_2', true ),
							'store_city'	=> get_post_meta( $post_id, 'store_city', true ),
							'store_state'	=> get_post_meta( $post_id, 'store_state', true ),
							'store_zipcode'	=> get_post_meta( $post_id, 'store_zipcode', true ),
						);
					}
				}
			} else {
				$locations = [];
				$args = array (
					'post_type'			=> 'locations',
					'post_status'		=> 'publish',
					'posts_per_page'	=> -1,
				);
				if ( $lang ) {
					$args['meta_query'] = array(
						array(
							'key' 		=> 'language_code',
							'value' 	=> $lang,
							'compare' 	=> '='
						),
					);
				}
				$the_query  = new WP_Query( $args );
				if( $the_query->have_posts( ) ) {
					while( $the_query->have_posts( ) ) {
						$the_query->the_post( );
						$post_id = get_the_ID( );
						$locations[] = array (
							'post_id'		=> $post_id,
							'post_title'	=> get_the_title( ),
							'lat'			=> get_post_meta( $post_id, 'map_lat', true ),
							'lng'			=> get_post_meta( $post_id, 'map_lng', true ),
							'distance'		=> 0,
							'meta_key'		=> 'map_lat',
							'store_address'	=> get_post_meta( $post_id, 'store_address', true ),
							'address_2'		=> get_post_meta( $post_id, 'address_2', true ),
							'store_city'	=> get_post_meta( $post_id, 'store_city', true ),
							'store_state'	=> get_post_meta( $post_id, 'store_state', true ),
							'store_zipcode'	=> get_post_meta( $post_id, 'store_zipcode', true ),
						);
					}
				}
				wp_reset_postdata();
				wp_reset_query( );
				
			}
			if ( ! empty( $locations ) ) {				
				$total_found = count( $locations );
				$map_locations 	= [];
				if( $show_map == 'yes' ) {
					$map_locations 	= $this->build_map_locations( $locations );
				}
				$list_locations 	= $this->build_locations_list( $locations, $show_map );				
				$all_data = array( 
					'status'		=> true, 
					'all_locations'	=> $map_locations,  
					'content'		=> $list_locations, 
					'lang'			=> $lang, 
					'total_found'	=> $total_found,
					'args'			=> $args,
					'show_map' 		=> $show_map,
					'locations' 	=> $locations
				);
				wp_send_json( $all_data );
			} else {
				$not_found = __( 'Sorry no data found.', 'store-location' );
				$res = array( 
					'status' 	=> false, 
					'args'		=> $args,
					'lang'		=> $lang, 
					'show_map' 	=> $show_map,
					'message' 	=> $not_found 
				);
				wp_send_json( $res );
			}
		}
		
		public function build_map_locations( $locations ){
			$response_loc = [];
			$i = 1;
			if ( count ( $locations ) > 0 ) {
				foreach ( $locations as $location ) {              
					$lat =  $location['lat'];
					$lng =  $location['lng'];
					if( $lat == '' || $lng == '' ) {
						continue;
					}
					
					$loc_id = 	$location['post_id'];
					$address = 	get_post_meta( $loc_id, "store_address", true );
					$address2 = get_post_meta( $loc_id, "address_2", true );
					$city = 	get_post_meta( $loc_id, "store_city", true );
					$state = 	get_post_meta( $loc_id, "store_state", true );
					$zipcode = 	get_post_meta( $loc_id, "store_zipcode", true );

					if( $address2 ){
						$address .=' '.$address2;
					}
					if( $city ){
						$address .= ( $state ) ? ' '.$city : ' '.$city;
					}
					if( $state ){
						$address .= ', '.$state;
					}
					if( $zipcode ){
						$address .= ' '.$zipcode;
					}

					$directions = $address ? $address : $lat . ',' . $lng;
					
					$phone = ( get_post_meta( $loc_id, "phone_no", true ) ) ? get_post_meta( $loc_id, "phone_no", true ) : '';
					$provider_name = ( get_post_meta( $loc_id, "provider_name",  true ) ) ? get_post_meta( $loc_id, "provider_name", true ) : '';
					$response_loc[] = array(
						'ID'				=> $i,
						'name' 				=> $location['post_title'],
						'address' 			=> $address,
						'latitude' 			=> $lat,
						'longitude' 		=> $lng,
						'phone' 			=> $phone,
						'show_direction'	=> $this->show_direction,
						'provider_name'		=> $provider_name,
						'direction'			=> __( 'Get directions', 'store-location' )
					);
					$i++;
				}
			}
			return $response_loc;
		}
		
		public function build_locations_list( $locations , $show_map = 'yes' ) {
			ob_start( );
			?>
			<?php if ( count( $locations ) > 0 ) : ?>
				<?php $j = 1; ?>
				<?php foreach ( $locations as $location ) : ?> 
					<?php
						// var_dump_pre( $location );
						// store_address address_2 store_city store_state store_state store_zipcode

						$lat =  $location['lat'];
						$lng =  $location['lng'];
						if( $lat == '' || $lng == '' ) {
							continue;
						}
						$loc_id 		= $location['post_id'];					
						$phone 			= ( get_post_meta ( $loc_id, "phone_no", true ) ) ? get_post_meta ( $loc_id, "phone_no", true ) : '';
						$provider_name 	= ( get_post_meta ( $loc_id, "provider_name", true ) ) ? get_post_meta ( $loc_id, "provider_name", true ) : '';
						
						$address 		= '';
						$address_1 		= $location['store_address'];
						$address_2 		= $location['address_2'];
						$city 			= $location['store_city'];
						$state 			= $location['store_state'];
						$zipcode 		= $location['store_zipcode'];

						if( $address_1 ){
							$address .= ' ' . $address_1;
						}
						if( $address_2 ){
							$address .= ' ' . $address_2;
						}
						if( $city ){
							$address .= ( $state ) ? ' ' . $city : ' ' . $city;
						}
						if( $state ){
							$address .= ', ' . $state;
						}
						if( $zipcode ){
							$address .= ' ' . $zipcode;
						}

						// var_dump_pre( $location );
						// var_dump_pre( $address );

						$directions = $address ? $address : $lat . ',' . $lng;
						// var_dump_pre( $directions );
					?>
					<?php if ( $show_map == 'no' ) : ?>
						<div class="vc_row row-internal row-container">
							<div class="row row-child">
								<div class="wpb_row row-inner" style="height: 102px;">
									<div class="wpb_column pos-top pos-center align_left column_child col-lg-3 single-internal-gutter">
										<div class="uncol style-light">
											<div class="uncoltable">
												<div class="uncell no-block-padding">
													<div class="uncont">
														<div class="uncode_text_column">
															<p>
																<strong>
																	<?php echo $location['post_title'];?>
																</strong>
															</p>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="wpb_column pos-top pos-center align_left column_child col-lg-3 single-internal-gutter">
										<div class="uncol style-light">
											<div class="uncoltable">
												<div class="uncell no-block-padding">
													<div class="uncont">
														<div class="uncode_text_column">
															<p>
																<?php if ( ! empty( $provider_name ) ) :?>
																	<?php echo $provider_name; ?>
																<?php endif; ?>
															</p>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="wpb_column pos-top pos-center align_left column_child col-lg-3 single-internal-gutter">
										<div class="uncol style-light">
											<div class="uncoltable">
												<div class="uncell no-block-padding">
													<div class="uncont">
														<div class="uncode_text_column">
															<p>
																<?php echo $address_1 . ' ' . $address_2; ?>
																<br>
																<?php echo $city;?>, <?php echo $state;?> <?php echo $zipcode;?>
															</p>
															<p>
																<a href="https://www.google.com/maps?q=<?php echo $directions; ?>" target="_blank">
																	<?php _e( 'Get Directions', 'store-location' );?>
																</a>
															</p>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="wpb_column pos-top pos-center align_left column_child col-lg-3 single-internal-gutter">
										<div class="uncol style-light">
											<div class="uncoltable">
												<div class="uncell no-block-padding">
													<div class="uncont">
														<div class="uncode_text_column">
															<p>
															  	<?php if ( ! empty( $phone ) ) : ?>
																  	<a href="tel:<?php echo preg_replace("/[^0-9]/", "", $phone)?>">
																		<?php echo $phone;?>
																	</a>
																<?php endif; ?>
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
					<?php else: ?>                    
						<div class="store-location loc-row" id="loc-<?php echo $j;?>">
							<h5 class="store-location-name">
								<?php echo $location['post_title'];?>
							</h5>
							<?php if ( ! empty( $provider_name ) ) : ?>
								<div class="store-location-provider">
									<span>
										<?php echo $provider_name;?>
									</span>
								</div>
							<?php endif; ?>
							<address class="store-location-address">
								<?php echo $address;?>
							</address>
							<?php if ( ! empty ( $phone ) ) : ?>
								<div class="store-location-phone">
									<a href="tel:<?php echo $phone;?>">
										<?php echo $phone;?>
									</a>
								</div>
							<?php endif; ?>
							<div class="store-location-btn-wrapper">
								<a href="javascript:void(0);" data-id="<?php echo $j;?>" data-lat="<?php echo $lat;?>" data-lng="<?php echo $lng;?>" class="store-location-btn">
									<?php _e( 'View On Map', 'store-location' );?>
								</a>
							</div>
						</div>
					<?php endif; ?>
					<?php $j++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			<?php 
			$info = ob_get_clean();
			return $info;
		} 

		public function get_nearby_locations( $lat, $long, $lang, $distance = 50 ) {
			global $wpdb;
			$nearbyLocations = $wpdb->get_results( 
			"SELECT DISTINCT    
				map_lat.post_id,
				map_lat.meta_key,
				map_lat.meta_value as lat,
				map_lng.meta_value as lng,
				((ACOS(SIN($lat * PI() / 180) * SIN(map_lat.meta_value * PI() / 180) + COS($lat * PI() / 180) * COS(map_lat.meta_value * PI() / 180) * COS(($long - map_lng.meta_value) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance,
				wp_posts.post_title
			FROM 
				$wpdb->postmeta AS map_lat
				LEFT JOIN $wpdb->postmeta as map_lng ON map_lat.post_id = map_lng.post_id
				INNER JOIN wp_posts ON $wpdb->posts.ID = map_lat.post_id  
				WHERE map_lat.meta_key = 'map_lat' AND map_lng.meta_key = 'map_lng'
				HAVING distance < $distance
				ORDER BY distance ASC;", ARRAY_A);

			if($nearbyLocations){
				return $nearbyLocations;
			}
			return array();
		}

	} 
}
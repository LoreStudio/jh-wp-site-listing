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

		public $plugin_slug;
		public $version;
		public $cache_key;
		public $cache_allowed;
		
		var $country_list = array(
			"AF" => "Afghanistan",
			"AL" => "Albania",
			"DZ" => "Algeria",
			"AS" => "American Samoa",
			"AD" => "Andorra",
			"AO" => "Angola",
			"AI" => "Anguilla",
			"AQ" => "Antarctica",
			"AG" => "Antigua and Barbuda",
			"AR" => "Argentina",
			"AM" => "Armenia",
			"AW" => "Aruba",
			"AU" => "Australia",
			"AT" => "Austria",
			"AZ" => "Azerbaijan",
			"BS" => "Bahamas",
			"BH" => "Bahrain",
			"BD" => "Bangladesh",
			"BB" => "Barbados",
			"BY" => "Belarus",
			"BE" => "Belgium",
			"BZ" => "Belize",
			"BJ" => "Benin",
			"BM" => "Bermuda",
			"BT" => "Bhutan",
			"BO" => "Bolivia",
			"BA" => "Bosnia and Herzegovina",
			"BW" => "Botswana",
			"BV" => "Bouvet Island",
			"BR" => "Brazil",
			"BQ" => "British Antarctic Territory",
			"IO" => "British Indian Ocean Territory",
			"VG" => "British Virgin Islands",
			"BN" => "Brunei",
			"BG" => "Bulgaria",
			"BF" => "Burkina Faso",
			"BI" => "Burundi",
			"KH" => "Cambodia",
			"CM" => "Cameroon",
			"CA" => "Canada",
			"CT" => "Canton and Enderbury Islands",
			"CV" => "Cape Verde",
			"KY" => "Cayman Islands",
			"CF" => "Central African Republic",
			"TD" => "Chad",
			"CL" => "Chile",
			"CN" => "China",
			"CX" => "Christmas Island",
			"CC" => "Cocos [Keeling] Islands",
			"CO" => "Colombia",
			"KM" => "Comoros",
			"CG" => "Congo - Brazzaville",
			"CD" => "Congo - Kinshasa",
			"CK" => "Cook Islands",
			"CR" => "Costa Rica",
			"HR" => "Croatia",
			"CU" => "Cuba",
			"CY" => "Cyprus",
			"CZ" => "Czech Republic",
			"CI" => "Côte d’Ivoire",
			"DK" => "Denmark",
			"DJ" => "Djibouti",
			"DM" => "Dominica",
			"DO" => "Dominican Republic",
			"NQ" => "Dronning Maud Land",
			"DD" => "East Germany",
			"EC" => "Ecuador",
			"EG" => "Egypt",
			"SV" => "El Salvador",
			"GQ" => "Equatorial Guinea",
			"ER" => "Eritrea",
			"EE" => "Estonia",
			"ET" => "Ethiopia",
			"FK" => "Falkland Islands",
			"FO" => "Faroe Islands",
			"FJ" => "Fiji",
			"FI" => "Finland",
			"FR" => "France",
			"GF" => "French Guiana",
			"PF" => "French Polynesia",
			"TF" => "French Southern Territories",
			"FQ" => "French Southern and Antarctic Territories",
			"GA" => "Gabon",
			"GM" => "Gambia",
			"GE" => "Georgia",
			"DE" => "Germany",
			"GH" => "Ghana",
			"GI" => "Gibraltar",
			"GR" => "Greece",
			"GL" => "Greenland",
			"GD" => "Grenada",
			"GP" => "Guadeloupe",
			"GU" => "Guam",
			"GT" => "Guatemala",
			"GG" => "Guernsey",
			"GN" => "Guinea",
			"GW" => "Guinea-Bissau",
			"GY" => "Guyana",
			"HT" => "Haiti",
			"HM" => "Heard Island and McDonald Islands",
			"HN" => "Honduras",
			"HK" => "Hong Kong SAR China",
			"HU" => "Hungary",
			"IS" => "Iceland",
			"IN" => "India",
			"ID" => "Indonesia",
			"IR" => "Iran",
			"IQ" => "Iraq",
			"IE" => "Ireland",
			"IM" => "Isle of Man",
			"IL" => "Israel",
			"IT" => "Italy",
			"JM" => "Jamaica",
			"JP" => "Japan",
			"JE" => "Jersey",
			"JT" => "Johnston Island",
			"JO" => "Jordan",
			"KZ" => "Kazakhstan",
			"KE" => "Kenya",
			"KI" => "Kiribati",
			"KW" => "Kuwait",
			"KG" => "Kyrgyzstan",
			"LA" => "Laos",
			"LV" => "Latvia",
			"LB" => "Lebanon",
			"LS" => "Lesotho",
			"LR" => "Liberia",
			"LY" => "Libya",
			"LI" => "Liechtenstein",
			"LT" => "Lithuania",
			"LU" => "Luxembourg",
			"MO" => "Macau SAR China",
			"MK" => "Macedonia",
			"MG" => "Madagascar",
			"MW" => "Malawi",
			"MY" => "Malaysia",
			"MV" => "Maldives",
			"ML" => "Mali",
			"MT" => "Malta",
			"MH" => "Marshall Islands",
			"MQ" => "Martinique",
			"MR" => "Mauritania",
			"MU" => "Mauritius",
			"YT" => "Mayotte",
			"FX" => "Metropolitan France",
			"MX" => "Mexico",
			"FM" => "Micronesia",
			"MI" => "Midway Islands",
			"MD" => "Moldova",
			"MC" => "Monaco",
			"MN" => "Mongolia",
			"ME" => "Montenegro",
			"MS" => "Montserrat",
			"MA" => "Morocco",
			"MZ" => "Mozambique",
			"MM" => "Myanmar [Burma]",
			"NA" => "Namibia",
			"NR" => "Nauru",
			"NP" => "Nepal",
			"NL" => "Netherlands",
			"AN" => "Netherlands Antilles",
			"NT" => "Neutral Zone",
			"NC" => "New Caledonia",
			"NZ" => "New Zealand",
			"NI" => "Nicaragua",
			"NE" => "Niger",
			"NG" => "Nigeria",
			"NU" => "Niue",
			"NF" => "Norfolk Island",
			"KP" => "North Korea",
			"VD" => "North Vietnam",
			"MP" => "Northern Mariana Islands",
			"NO" => "Norway",
			"OM" => "Oman",
			"PC" => "Pacific Islands Trust Territory",
			"PK" => "Pakistan",
			"PW" => "Palau",
			"PS" => "Palestinian Territories",
			"PA" => "Panama",
			"PZ" => "Panama Canal Zone",
			"PG" => "Papua New Guinea",
			"PY" => "Paraguay",
			"YD" => "People's Democratic Republic of Yemen",
			"PE" => "Peru",
			"PH" => "Philippines",
			"PN" => "Pitcairn Islands",
			"PL" => "Poland",
			"PT" => "Portugal",
			"PR" => "Puerto Rico",
			"QA" => "Qatar",
			"RO" => "Romania",
			"RU" => "Russia",
			"RW" => "Rwanda",
			"RE" => "Réunion",
			"BL" => "Saint Barthélemy",
			"SH" => "Saint Helena",
			"KN" => "Saint Kitts and Nevis",
			"LC" => "Saint Lucia",
			"MF" => "Saint Martin",
			"PM" => "Saint Pierre and Miquelon",
			"VC" => "Saint Vincent and the Grenadines",
			"WS" => "Samoa",
			"SM" => "San Marino",
			"SA" => "Saudi Arabia",
			"SN" => "Senegal",
			"RS" => "Serbia",
			"CS" => "Serbia and Montenegro",
			"SC" => "Seychelles",
			"SL" => "Sierra Leone",
			"SG" => "Singapore",
			"SK" => "Slovakia",
			"SI" => "Slovenia",
			"SB" => "Solomon Islands",
			"SO" => "Somalia",
			"ZA" => "South Africa",
			"GS" => "South Georgia and the South Sandwich Islands",
			"KR" => "South Korea",
			"ES" => "Spain",
			"LK" => "Sri Lanka",
			"SD" => "Sudan",
			"SR" => "Suriname",
			"SJ" => "Svalbard and Jan Mayen",
			"SZ" => "Swaziland",
			"SE" => "Sweden",
			"CH" => "Switzerland",
			"SY" => "Syria",
			"ST" => "São Tomé and Príncipe",
			"TW" => "Taiwan",
			"TJ" => "Tajikistan",
			"TZ" => "Tanzania",
			"TH" => "Thailand",
			"TL" => "Timor-Leste",
			"TG" => "Togo",
			"TK" => "Tokelau",
			"TO" => "Tonga",
			"TT" => "Trinidad and Tobago",
			"TN" => "Tunisia",
			"TR" => "Turkey",
			"TM" => "Turkmenistan",
			"TC" => "Turks and Caicos Islands",
			"TV" => "Tuvalu",
			"UM" => "U.S. Minor Outlying Islands",
			"PU" => "U.S. Miscellaneous Pacific Islands",
			"VI" => "U.S. Virgin Islands",
			"UG" => "Uganda",
			"UA" => "Ukraine",
			"SU" => "Union of Soviet Socialist Republics",
			"AE" => "United Arab Emirates",
			"GB" => "United Kingdom",
			"US" => "United States",
			"ZZ" => "Unknown or Invalid Region",
			"UY" => "Uruguay",
			"UZ" => "Uzbekistan",
			"VU" => "Vanuatu",
			"VA" => "Vatican City",
			"VE" => "Venezuela",
			"VN" => "Vietnam",
			"WK" => "Wake Island",
			"WF" => "Wallis and Futuna",
			"EH" => "Western Sahara",
			"YE" => "Yemen",
			"ZM" => "Zambia",
			"ZW" => "Zimbabwe",
			"AX" => "Åland Islands",
		);

		var $flags_array = array ( 
			'sq' => array(
				'sq',
				'sq.svg',
				'Albanian'
			),
			'ar' => array(
				'ar',
				'ar.svg',
				'Arabic'
			),
			'hy' => array(
				'hy',
				'hy.svg',
				'Armenian'
			),
			'az' => array(
				'az',
				'az.svg',
				'Azerbaijani'
			),
			'eu' => array(
				'eu',
				'eu.svg',
				'Basque'
			),
			'bn' => array(
				'bn',
				'bn.svg',
				'Bengali'
			),
			'bs' => array(
				'bs',
				'bs.svg',
				'Bosnian'
			),
			'bg' => array(
				'bg',
				'bg.svg',
				'Bulgarian'
			),
			'ca' => array(
				'ca',
				'ca.svg',
				'Catalan'
			),
			'zh-hans' => array(
				'zh-hans',
				'zh-hans.svg',
				'Chinese (Simplified)'
			),
			'zh-hant' => array(
				'zh-hant',
				'zh-hant.svg',
				'Chinese (Traditional)'
			),
			'hr' => array(
				'hr',
				'hr.svg',
				'Croatian'
			),
			'cs' => array(
				'cs',
				'cs.svg',
				'Czech'
			),
			'da' => array(
				'da',
				'da.svg',
				'Danish'
			),
			'nl' => array(
				'nl',
				'nl.svg',
				'Dutch'
			),
			'en' => array(
				'en',
				'us.png',
				'English'
			),
			'eo' => array(
				'eo',
				'eo.svg',
				'Esperanto'
			),
			'et' => array(
				'et',
				'et.svg',
				'Estonian'
			),
			'fi' => array(
				'fi',
				'fi.svg',
				'Finnish'
			),
			'fr' => array(
				'fr',
				'fr.svg',
				'French	'
			),
			'gl' => array(
				'gl',
				'gl.svg',
				'Galician'
			),
			'de' => array(
				'de',
				'de.svg',
				'German	'
			),
			'el' => array(
				'el',
				'el.svg',
				'Greek	'
			),
			'he' => array(
				'he',
				'he.svg',
				'Hebrew	'
			),
			'hi' => array(
				'hi',
				'hi.svg',
				'Hindi	'
			),
			'hu' => array(
				'hu',
				'hu.svg',
				'Hungarian'
			),
			'is' => array(
				'is',
				'is.svg',
				'Icelandic'
			),
			'id' => array(
				'id',
				'id.svg',
				'Indonesian'
			),
			'ga' => array(
				'ga',
				'ga.svg',
				'Irish'
			),
			'it' => array(
				'it',
				'it.svg',
				'Italian'
			),
			'ja' => array(
				'ja',
				'ja.svg',
				'Japanese'
			),
			'ko' => array(
				'ko',
				'ko.svg',
				'Korean'
			),
			'ku' => array(
				'ku',
				'ku.svg',
				'Kurdish'
			),
			'lv' => array(
				'lv',
				'lv.svg',
				'Latvian'
			),
			'lt' => array(
				'lt',
				'lt.svg',
				'Lithuanian'
			),
			'mk' => array(
				'mk',
				'mk.svg',
				'Macedonian'
			),
			'ms' => array(
				'ms',
				'ms.svg',
				'Malay'
			),
			'mt' => array(
				'mt',
				'mt.svg',
				'Maltese'
			),
			'mn' => array(
				'mn',
				'mn.svg',
				'Mongolian'
			),
			'ne' => array(
				'ne',
				'ne.svg',
				'Nepali'
			),
			'no' => array(
				'no',
				'no.svg',
				'Norwegian Bokmål'
			),
			'fa' => array(
				'fa',
				'fa.svg',
				'Persian'
			),
			'pl' => array(
				'pl',
				'pl.svg',
				'Polish	'
			),
			'pt-br' => array(
				'pt-br',
				'pt-br.svg',
				'Portuguese (Brazil)'
			),
			'pt-pt' => array(
				'pt-pt',
				'pt-pt.svg',
				'Portuguese (Portugal)'
			),
			'pa' => array(
				'pa',
				'pa.svg',
				'Punjabi'
			),
			'qu' => array(
				'qu',
				'qu.svg',
				'Quechua'
			),
			'ro' => array(
				'ro',
				'ro.svg',
				'Romanian'
			),
			'ru' => array(
				'ru',
				'ru.svg',
				'Russian'
			),
			'sr' => array(
				'sr',
				'sr.svg',
				'Serbian'
			),
			'sk' => array(
				'sk',
				'sk.svg',
				'Slovak'
			),
			'sl' => array(
				'sl',
				'sl.svg',
				'Slovenian'
			),
			'so' => array(
				'so',
				'so.svg',
				'Somali'
			),
			'es' => array(
				'es',
				'es.svg',
				'Spanish'
			),
			'es-us' => array(
				'es-us',
				'us.png',
				'Spanish (US)'
			),
			'sv' => array(
				'sv',
				'sv.svg',
				'Swedish'
			),
			'ta' => array(
				'ta',
				'ta.svg',
				'Tamil'
			),
			'th' => array(
				'th',
				'th.svg',
				'Thai'
			),
			'tr' => array(
				'tr',
				'tr.svg',
				'Turkish'
			),
			'uk' => array(
				'uk',
				'uk.svg',
				'Ukrainian'
			),
			'ur' => array(
				'ur',
				'ur.svg',
				'Urdu'
			),
			'uz' => array(
				'uz',
				'uz.svg',
				'Uzbek'
			),
			'vi' => array(
				'vi',
				'vi.svg',
				'Vietnamese'
			),
			'cy' => array(
				'cy',
				'cy.svg',
				'Welsh'
			),
			'yi' => array(
				'yi',
				'yi.svg',
				'Yiddish'
			),
			'zu' => array(
				'zu',
				'zu.svg',
				'Zulu'
			),
			// Add english canadian
			'en-ca' => array(
				'en-ca',
				'en-ca.svg',
				'English (Canada)'
			),
			// Add french canadian
			'fr-ca' => array(
				'fr-ca',
				'fr-ca.svg',
				'French (Canada)'
			),
		);

		public function __construct ( ) {	
			
			if( 'yes' == esc_attr( get_option( 'show_direction_link' ) ) ){
				$this->show_direction = true;
			}

			$this->plugin_slug   = plugin_basename( __DIR__ );
			$this->version       = LOCATION_PLUGIN_VERSION;
			$this->cache_key     = 'store-locations';
			$this->cache_allowed = false;
			
			add_action( 'init',              		[$this, 'location_register_post_types'] );

			add_action( 'admin_enqueue_scripts', 	[$this, 'admin_store_locations_scripts'] );
			add_action( 'admin_enqueue_scripts', 	[$this, 'admin_store_locations_styles'] );

			add_action( 'wp_enqueue_scripts', 		[$this, 'store_locations_scripts'] );

			add_action( 'admin_menu', 				[$this, 'store_loc_setting_page'] );

			add_shortcode( 'language', 				[$this, 'get_language_shortcode'] );

			add_shortcode( 'store-locations', 		[$this, 'store_locations_detail'] );

			add_action( 'add_meta_boxes', 			[$this, 'address_location_custom_box'] );
			add_action( 'save_post_locations', 		[$this, 'save_address_location'] );

			add_action( 'wp_ajax_search_location_near', [ $this, 'search_location_near'] );
			add_action( 'wp_ajax_nopriv_search_location_near', [ $this, 'search_location_near'] );

			add_filter( 'manage_locations_posts_columns', [$this, 'columns_locations'] );
			add_action( 'manage_locations_posts_custom_column', [$this, 'columns_locations_data'], 10, 2 );

			add_action( 'load-edit.php', 			[$this, 'load_custom_filter_wpse_94630'] );
			add_action( 'restrict_manage_posts', 	[$this, 'wpse45436_admin_posts_filter_restrict_manage_posts'], 10, 1 );

			// Update functions
			add_filter( 'plugins_api', [ $this, 'update_info' ], 20, 3 );
			add_filter( 'site_transient_update_plugins', [ $this, 'plugin_update' ] );
			add_action( 'upgrader_process_complete', [ $this, 'update_purge' ], 10, 2 );

			// Import Page
			add_action( 'admin_menu', [$this, 'location_import_page'] );
			add_action( 'admin_menu', [$this, 'location_export_page'] );
			add_action( 'admin_init', [$this, 'location_export_request'] );

			// Add language feild to bulk edit
			add_action( 'bulk_edit_custom_box', array( $this, 'quick_edit_custom_box' ), 10, 2 );
		}
		// front end enque scripts
		public function store_locations_scripts ( ) {

			wp_enqueue_script('jquery');
			wp_enqueue_style( 'store-loc-style',  LOCATION_DIR_URI. 'includes/css/store-loc-plugin.css', array(), '1.0.0', 'all' );	
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
		// admin scripts
		public function admin_store_locations_scripts ( ) {
			$apikey = esc_attr(get_option('gmap_api_key'));

			if ( ! empty( $apikey ) ) {
				$map_api = '//maps.googleapis.com/maps/api/js?key=' . $apikey;
				wp_register_script( 'store-google-map', $map_api );
				wp_enqueue_script( 'store-google-map' );
			}				
			wp_enqueue_script( 'admin-store-js', LOCATION_DIR_URI . 'admin/js/admin-store.js', array(), '1.0.0', true );		
		}
		// admin styles
		public function admin_store_locations_styles ( ) {
			wp_enqueue_style( 'store-loc-admin-style',  LOCATION_DIR_URI . 'admin/css/styles.css', array(), '1.0.0', 'all' );	
		}

		function get_language_shortcode ( ) {
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
					<div class="store-locations-search OneLinkTx">
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
					<h5 class="store-locations-result-info OneLinkTx">
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
					<?php /*
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
					*/ ?>
					<tr class="form-field form-required">
						<th scope="row">
							<label for="store_language">
								<?php esc_html_e( 'Language', 'store-location' );?>
							</label>
						</th>
						<td>
							<!-- wpml language dropdown -->
							<?php $language_code = get_post_meta( $post->ID, 'languages', true );?>
							<?php if ( $this->is_wpml_active ( ) ) : ?>
								<?php if ( function_exists ( 'icl_get_languages' ) ) : ?>
									<?php 
										$langs = icl_get_languages ( 'skip_missing=0' ); 
										// var_dump_pre( $langs );

										$language_code = explode( ',', get_post_meta ( $post->ID, 'languages', true ) );
										// var_dump_pre( $language_code );
									?>
									<ul class="available-languages">
										<?php foreach ( $langs as $lang ) : ?>
											<li class="<?php if ( in_array ( $lang['language_code'], $language_code ) ) { echo '_active'; } ?>">
												<label for="wpml-language-<?php echo $lang['language_code'];?>">
													<input type="checkbox" name="languages[]" id="wpml-language-<?php echo $lang['language_code'];?>" value="<?php echo $lang['language_code']; ?>" <?php if ( in_array ( $lang['language_code'], $language_code ) ) { echo 'checked="checked"'; } ?>>
													<img width="18" height="12" src="<?php echo $lang['country_flag_url']; ?>" alt="Flag for <?php echo $lang['language_code'];?>">
													<?php echo $lang["native_name"]; ?> ( <?php echo $lang['language_code']; ?> )
												</label>
											</li>
										<?php endforeach ?>
									</ul>
								<?php endif; ?>
							<?php else : ?>
								<?php if ( ! $this->is_wpml_active( ) ) : ?>
									<?php  
										$flags_dir_url = LOCATION_DIR_URI . 'images/flags/';

										$active_languages = get_option( 'store-loc-active-languages' );
										// var_dump_pre( $active_languages );

										$active_languages_array = explode( ',', $active_languages );
										// var_dump_pre( $active_languages_array );

										$language_code = explode( ',', get_post_meta( $post->ID, 'languages', true ) );
										// var_dump_pre( $language_code );

									?>
									<ul class="available-languages">
										<?php foreach ( $this->flags_array as $value ) : ?>
											<?php if ( ! in_array ( $value[0], $active_languages_array ) ) { continue; } ?>
											<li class="<?php if ( in_array ( $value[0] , $language_code ) ) { echo '_active'; } ?>">
												<label for="wpml-language-<?php echo $value[0];?>">
													<input type="checkbox" name="languages[]" id="wpml-language-<?php echo $value[0];?>" value="<?php echo $value[0];?>" <?php if ( in_array ( $value[0] , $language_code ) ) { echo 'checked'; } ?>>
													<img width="18" height="12" src="<?php echo $flags_dir_url.$value[1];?>" alt="Flag for <?php echo $value[0];?>">
													<?php echo $value[2]; ?> ( <?php echo $value[0]; ?> )
												</label>
											</li>
										<?php endforeach ?>
									</ul>
								<?php endif ?>	
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
							<label for="store_zipcode">
								<?php esc_html_e( 'Country', 'store-location' );?> 
							</label>
						</th>
						<td>
							<?php  
								$country = get_post_meta( $post->ID, 'country', true );
								// var_dump_pre( $country );
							?>
							<select name="country" id="store-country">
								<option value=""  <?php if ( in_array( $country, $this->country_list ) ) { echo 'selected'; } ?>>
									Select Country
								</option>
								<?php foreach ( $this->country_list as $key => $value ) : ?>
									<?php $key = strtolower ( $key ); ?>
									<option value="<?php echo $key;?>" <?php if ( $key == $country ) { echo 'selected'; } ?> >
										<?php echo $value; ?>
									</option>
								<?php endforeach ?>
							</select>
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
							<label for="email">
								<?php esc_html_e('Email', 'store-location' );?>
							</label>
						</th>
						<td>
							<input name="email" type="text" id="email"  value="<?php echo get_post_meta( $post->ID, 'email', true );?>">
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

			// var_dump_pre( $_POST );
			// var_dump_pre( implode( ',', $_POST['languages'] ) );
			// die();

			// If we are on the bulk edit screen, update post languages
			if ( isset( $_REQUEST['bulk_edit'] ) ) {
				
				$languages = implode( ',', $_REQUEST['languages'] );
				update_post_meta( $post_id, 'languages', $languages );
				return;
			}

			update_post_meta( $post_id, 'language_code', 	isset( $_POST['language_code'] ) ? strtolower( sanitize_text_field ( $_POST['language_code'] ) ) : '' );
			update_post_meta( $post_id, 'languages', 		isset( $_POST['language_code'] ) ? implode( ',', (array)$_POST['languages'] ) : '' );
			update_post_meta( $post_id, 'country', 			isset( $_POST['country'] ) ? sanitize_text_field ( $_POST['country'] ) : '' );
			update_post_meta( $post_id, 'store_address', 	isset( $_POST['store_address'] ) ? sanitize_text_field ( $_POST['store_address'] ) : '' );
			update_post_meta( $post_id, 'address_2', 		isset( $_POST['address_2'] ) ? sanitize_text_field ( $_POST['address_2'] ) : '' );
			update_post_meta( $post_id, 'store_city', 		isset( $_POST['store_city'] ) ? sanitize_text_field ( $_POST['store_city'] ) : '' );
			update_post_meta( $post_id, 'store_state', 		isset( $_POST['store_state'] ) ? sanitize_text_field ( $_POST['store_state'] ) : '' );
			update_post_meta( $post_id, 'store_zipcode',	isset( $_POST['store_zipcode'] ) ? sanitize_text_field ( $_POST['store_zipcode'] ) : '' );
			update_post_meta( $post_id, 'map_lat', 			isset( $_POST['map_lat'] ) ? $_POST['map_lat'] : '' );
			update_post_meta( $post_id, 'map_lng', 			isset( $_POST['map_lng'] ) ? $_POST['map_lng'] : '' );
			update_post_meta( $post_id, 'website_url', 		isset( $_POST['website_url'] ) ? sanitize_text_field ( $_POST['website_url'] ) : '' );
			update_post_meta( $post_id, 'phone_no', 		isset( $_POST['phone_no'] ) ? sanitize_text_field ( $_POST['phone_no'] ) : '' );
			update_post_meta( $post_id, 'email', 		    isset( $_POST['email'] ) ? sanitize_text_field ( $_POST['email'] ) : '' );
			update_post_meta( $post_id, 'provider_name', 	isset( $_POST['provider_name'] ) ? sanitize_text_field ( $_POST['provider_name'] ) : '' );

		}
		
		public function search_location_near ( ) {

			$lat 		= ! empty( $_POST['pos_lat'] ) ? $_POST['pos_lat'] : '';
			$lng 		= ! empty( $_POST['pos_lng'] ) ? $_POST['pos_lng'] : '';
			$lang 		= ! empty( $_POST['lang'] ) ? $_POST['lang'] : '';
			$show_map 	= $_POST['show_map'];

			if ( ! empty( $lat ) && ! empty( $lng ) ) {
				$locations = []; 
				$locations_result = $this->get_nearby_locations( $lat, $lng, $lang,  DISTANCE_MILES );
				if( ! empty ( $locations_result ) ) {
					foreach ( $locations_result as $loc ) {	
						if ( !empty( $lang ) ) {
							$language_code = explode ( ',', get_post_meta ( $loc['post_id'], 'languages', true ) );
							if( ! in_array( $lang, $language_code ) ) {
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
							'key' 		=> 'languages',
							'value' 	=> $lang,
							'compare' 	=> 'like'
						),
					);
				}
				$the_query  = new WP_Query( $args );
				if( $the_query->have_posts( ) ) {
					while( $the_query->have_posts( ) ) {
						$the_query->the_post( );
						$post_id = get_the_ID( );

						// If the lang is set, then check if the post has the lang
						if ( !empty( $lang ) ) {
							$language_code = explode ( ',', get_post_meta ( $post_id, 'languages', true ) );
							if( ! in_array( $lang, $language_code ) ) {
								continue;
							}
						}

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
					$email = ( get_post_meta( $loc_id, "email", true ) ) ? get_post_meta( $loc_id, "email", true ) : '';
					$provider_name = ( get_post_meta( $loc_id, "provider_name",  true ) ) ? get_post_meta( $loc_id, "provider_name", true ) : '';
					$response_loc[] = array(
						'ID'				=> $i,
						'name' 				=> $location['post_title'],
						'address' 			=> $address,
						'latitude' 			=> $lat,
						'longitude' 		=> $lng,
						'phone' 			=> $phone,
						'email' 			=> $email,
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
						$email 			= ( get_post_meta ( $loc_id, "email", true ) ) ? get_post_meta ( $loc_id, "email", true ) : '';
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
									<div class="wpb_column pos-top pos-center align_left column_child col-lg-3 single-internal-gutter OneLinkNoTx">
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
									<div class="wpb_column pos-top pos-center align_left column_child col-lg-3 single-internal-gutter OneLinkNoTx">
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
									<div class="wpb_column pos-top pos-center align_left column_child col-lg-3 single-internal-gutter OneLinkNoTx">
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
									<div class="wpb_column pos-top pos-center align_left column_child col-lg-3 single-internal-gutter OneLinkNoTx">
										<div class="uncol style-light">
											<div class="uncoltable">
												<div class="uncell no-block-padding">
													<div class="uncont">
														<div class="uncode_text_column">
															<p>
																<?php if ( ! empty( $email ) ) : ?>
																	<a href="mailto:<?php echo esc_html( $email ); ?>">
																		<?php echo esc_html( $email ); ?>
																	</a><br/>
																<?php endif; ?>
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
							<div class="OneLinkNoTx">
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
							<?php if ( ! empty ( $email ) ) : ?>
								<div class="store-location-email">
									<a href="mail-to:<?php echo esc_html( $email ); ?>">
										<?php echo esc_html( $email ); ?>
									</a>
								</div>
							<?php endif; ?>
							</div>
							<div class="store-location-btn-wrapper OneLinkTx">
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
				$wpdb->posts.post_title
			FROM 
				$wpdb->postmeta AS map_lat
				LEFT JOIN $wpdb->postmeta as map_lng ON map_lat.post_id = map_lng.post_id
				INNER JOIN $wpdb->posts ON $wpdb->posts.ID = map_lat.post_id  
				WHERE map_lat.meta_key = 'map_lat' AND map_lng.meta_key = 'map_lng'
				HAVING distance < $distance
				ORDER BY distance ASC;", ARRAY_A);

			if($nearbyLocations){
				return $nearbyLocations;
			}
			return array();
		}

		//****************************************************************************/
		// Admin Custom Columns Languages and Country
		//****************************************************************************/
		public function columns_locations ( $columns ) {

		    $columns['lang'] = 'Languages';
		    $columns['country'] = 'Country';

		    return $columns;
		}

		public function columns_locations_data ( $column, $post_id ) {
		    switch( $column ) {
		        case 'lang':
			        $language_codes = get_post_meta ( $post_id, 'languages', true );
		            // var_dump_pre( $language_codes ); 
		        	if ( $language_codes ) {
		        		// echo str_replace ( ',', ', ', $language_code );
		        		$lang_array = explode( ',', $language_codes );
		        		$res = array ( );
		        		foreach ( $lang_array as  $value ) {
			        		$res[] = $this->flags_array[trim( $value )][2] . ' (' . $value . ')';
		        		}
		        		echo implode( ', ', $res );
		        	}
		        	else{
		        		echo '<span style="color:red">not set</span>';
		        	}
		        break;
		        case 'country':
			        $country = get_post_meta ( $post_id, 'country', true );
		            // var_dump_pre( $country ); 
		        	if ( $country ) {
		        		echo $this->country_list[strtoupper( $country )];
		        	}
		        	else{
		        		echo '<span style="color:red">not set</span>';
		        	}
		        break;
		    }
		}

		//****************************************************************************/
		// Admin Languages and Country filters for Location CPT
		//****************************************************************************/
		function load_custom_filter_wpse_94630( ) {

		    global $typenow;

		    // Adjust the Post Type
		    if( 'locations' != $typenow )
		        return;

		    add_filter( 'posts_where' , [$this, 'posts_where_wpse_94630'] );
		}

		function posts_where_wpse_94630( $where ) {

		    global $wpdb;       

		    if ( isset( $_GET[ 'store-language' ] ) && !empty( $_GET[ 'store-language' ] ) ) {
		        $meta = esc_sql( $_GET['store-language'] );
		        $where .= " AND ID IN (SELECT post_id FROM $wpdb->postmeta WHERE meta_value LIKE '%$meta%' AND $wpdb->postmeta.meta_key = 'languages' )";
		    }
		    if ( isset( $_GET[ 'store-country' ] ) && !empty( $_GET[ 'store-country' ] ) ) {
		        $meta = esc_sql( $_GET['store-country'] );
		        if ( 'not-set' == $_GET[ 'store-country' ] ) {
			        $where .= " AND ( 
			        					NOT EXISTS (SELECT * FROM $wpdb->postmeta WHERE $wpdb->postmeta.meta_key = 'country' AND $wpdb->postmeta.post_id=$wpdb->posts.ID ) 
			        					OR
			        					ID IN (SELECT post_id FROM $wpdb->postmeta WHERE $wpdb->postmeta.meta_key = 'country' AND meta_value = ' ' )
			        )";
		        }
		        else{
			        $where .= " AND ID IN (SELECT post_id FROM $wpdb->postmeta WHERE meta_value LIKE '$meta' )";
			        // $where .= " AND ID IN (SELECT post_id FROM $wpdb->postmeta WHERE $wpdb->postmeta.meta_key = 'country' AND meta_value = ' ' )";
		        }
		    }

		    // var_dump_pre( $where );
		    return $where;
		}

	    function wpse45436_admin_posts_filter_restrict_manage_posts ( $post_type ) {

			global $wpdb, $post;

			if ( 'locations' !== $post_type ) {
				return;
			}

            // languages
            $active_languages = get_option( 'store-loc-active-languages' );
			// var_dump_pre( $active_languages );
			
			$active_languages_array = array ( );
            if ( ! empty( $active_languages ) ) {
				$active_languages_array = explode( ',', $active_languages );
            }
			// var_dump_pre( $active_languages_array );

			// countries
			$countries_res = $wpdb->get_results ( "SELECT DISTINCT meta_value FROM $wpdb->postmeta pm, $wpdb->posts p WHERE meta_key = 'country' and pm.post_id=p.ID  and p.post_type='locations' ", ARRAY_A );
			// var_dump_pre( $countries_res );

			if ( is_array ( $countries_res ) ){
				$countries = array( 'not-set' );
				foreach ( $countries_res as $value ) {
					if ( !empty($value['meta_value']) ) {
						$countries[] = $value['meta_value'];
					}
				}
				// var_dump_pre( $countries );
			}

        ?>
	        <?php if ( ! empty( $active_languages ) ) : ?>
	            <select name="store-language">
		            <option value="">
		            	<?php _e( 'All Languages', 'store-location' ); ?>
	            	</option>
		            <?php $current_lang = isset( $_GET['store-language'] ) ? $_GET['store-language'] : ''; ?>
	                <?php foreach ( $active_languages_array as $value ) : ?>
	                	<?php 
	                		$selected = $value == $current_lang ? ' selected="selected"' : '';
	                		$language_label = $this->flags_array[$value];
                		?>
                        <option value="<?php echo $value ?>" <?php echo $selected ?> >
	                		<?php echo $language_label[2]; ?> ( <?php echo $language_label[0]; ?> )
                        </option>
		            <?php endforeach; ?>
	            </select>
	        <?php endif ?> 
	        <?php if ( is_array ( $countries ) ) : ?>
	            <select name="store-country">
		            <option value="">
		            	<?php _e( 'All Countries', 'store-location' ); ?>
	            	</option>
		            <?php $current_country = isset( $_GET['store-country'] ) ? $_GET['store-country'] : ''; ?>
	                <?php foreach ( $countries as $value ) : ?>
	                	<?php $selected = $value == $current_country ? ' selected="selected"' : '' ?>
	                	<?php if ( 'not-set' == $value ): ?>
		                	<option value="not-set" <?php echo $selected ?> >
	                        	Not Set
	                        </option>
	                	<?php else : ?>	
	                        <option value="<?php echo $value ?>" <?php echo $selected ?> >
	                        	<?php echo $this->country_list[strtoupper( $value )]; ?>
	                        </option>
	                	<?php endif ?>
		            <?php endforeach; ?>
	            </select>
	        <?php endif ?>

            <?php
	    }
		public function update_request() {
		
			$remote = get_transient( $this->cache_key );
			
			if( false === $remote ) {

				$remote = wp_remote_get( 'https://raw.githubusercontent.com/LoreStudio/jh-wp-site-listing/main/store-locations-info.json', [
					'timeout' => 10,
			    		'headers' => [
						'Accept' => 'application/json'
			    		]
				  ]);
			
				if ( is_wp_error( $remote ) || 200 !== wp_remote_retrieve_response_code( $remote ) || empty( wp_remote_retrieve_body( $remote ) ) ) {
					return false;
				}
			
				set_transient( $this->cache_key, $remote, 120 );
			
			}
			
			$remote = json_decode( wp_remote_retrieve_body( $remote ) );
			
			return $remote;
		
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
			
			$response->name           = $remote->name;
			$response->slug           = $remote->slug;
			$response->version        = $remote->version;
			$response->download_link  = $remote->download_url;
			$response->trunk          = $remote->download_url;
			$response->last_updated   = $remote->last_updated;
			
			$response->sections = [
			    'description'  => $remote->sections->description,
			    'installation' => $remote->sections->installation,
			    'changelog'    => $remote->sections->changelog
			];
			
			
			return $response;
		
		}

		public function plugin_update( $transient ) {
		
			if ( empty($transient->checked ) ) {
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
			if ( $this->cache_allowed && 'update' === $options['action'] && 'plugin' === $options[ 'type' ] ) {
		    		// clean the cache when new plugin version is installed
		    		delete_transient( $this->cache_key );
			}		
		}
		function location_import_page() {
			    add_submenu_page(
				'edit.php?post_type=locations',
				__( 'Import Locations', 'textdomain' ),
				__( 'Import Locations', 'textdomain' ),
				'manage_options',
				'import-location',
				[$this,'location_import_page_html'],
			);
		} // end of upload page
		
		function location_import_page_html() {
		?>
			<div class="wrap">
				<h1 class="page-title">Import Locations</h1>
				<?php 			
					if ( isset($_POST['action_type'] ) && $_POST['action_type'] == 'location_upload_csv' )  {
						    if(($file = fopen($_FILES['filename']['tmp_name'], "r")) !== FALSE ){        
        						$header = fgetcsv($file);
        				    	while (($line = fgetcsv($file)) !== FALSE) {
                    				$line = array_combine($header,$line);
							        if($line["title"] != '') {
										$post_id = ( isset($line["post_id"]) && $line["post_id"] ) ? $line["post_id"] : 0;									
										$title = ($line["title"]) ? $line["title"] : "";
										$address1 = ($line["address1"]) ? $line["address1"] : "";
										$address2 = ($line["address2"]) ? $line["address2"] : "";
										$city = ($line["city"]) ? $line["city"] : "";
										$state = ($line["state"]) ? $line["state"] : "";
										$zip = ($line["zip_code"]) ? $line["zip_code"] : "";
										$country = ($line["country"]) ? $line["country"] : "";  // Always should be lowercase and 2 letters representing country
										$website = ($line["website"]) ? $line["website"] : ""; 
										$phone = ($line["phone"]) ? $line["phone"] : ""; 
										$email = ($line["email"]) ? $line["email"] : ""; 
										$provider_name = ($line["provider_name"]) ? $line["provider_name"] : ""; 

										$lat = 0;
										$long = 0;
										
										if( $address1 != ''){
											$geoaddress = $address1;
											if( $address2 != ''){
												$geoaddress .= " $address2";
											}
											if( $city != '' && !str_contains( strtolower($geoaddress), strtolower($city) ) ){
												$geoaddress .= " $city";
											}
											if( $state != '' && !str_contains( strtolower($geoaddress), strtolower($state) ) ){
												$geoaddress .= ", $state";
											}
											if( $zip != '' && !str_contains( strtolower($geoaddress), strtolower($zip) ) ){
												$geoaddress .= ", $zip";
											}
											
											$geocode = $this->geocode($geoaddress);
											if($geocode && is_array($geocode)) {
												$lat = $geocode[0];
        										$long = $geocode[1];
											}
										}									
										
								/* Data that can be used for debugging upload		
										echo $title . "<br />";
										echo $address1 . $address2 . "<br />";
										echo $city . "<br />";
										echo $state . "<br />";
										echo $zip . "<br />";
										echo $country . "<br />";
										echo $website . "<br />";
										echo $phone . "<br />";
										echo $email . "<br />";
										echo $provider_name . "<br />";
								*/
										
										if($post_id && $title != '') {
											$post_args = array(
											  'ID'           => $post_id,
											  'post_title'   => $title,											  
										  	);
											wp_update_post($post_args);
										}
										
										if(!$post_id) {
											$post_id = wp_insert_post(array(
										  		'post_title'=> $title, 
										  		'post_type'=> 'locations', 
										  		'post_content'=>'',
												'post_status' => 'publish'
											));				
										}
									
										if($post_id) {
											update_post_meta( $post_id, 'language_code', 	'en' );
											update_post_meta( $post_id, 'languages', 		'en' );											
											update_post_meta( $post_id, 'country', 			sanitize_text_field ( $country ) );
											update_post_meta( $post_id, 'store_address', 	sanitize_text_field ( $address1 ) );
											update_post_meta( $post_id, 'address_2', 		sanitize_text_field ( $address2 ) );
											update_post_meta( $post_id, 'store_city', 		sanitize_text_field ( $city ) );
											update_post_meta( $post_id, 'store_state', 		sanitize_text_field ( $state ) );
											update_post_meta( $post_id, 'store_zipcode',	sanitize_text_field ( $zip ) );
											update_post_meta( $post_id, 'website_url', 		sanitize_text_field ( $website ) );
											update_post_meta( $post_id, 'phone_no', 		sanitize_text_field ( $phone ) );
											update_post_meta( $post_id, 'email', 		    sanitize_text_field ( $email ) );
											update_post_meta( $post_id, 'provider_name', 	sanitize_text_field ( $provider_name ) );	

											if($lat && $long) {
												update_post_meta( $post_id, 'map_lat', 			$lat );
												update_post_meta( $post_id, 'map_lng', 			$long );
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
				<p class="form-description">Import locations by clicking the button below. The file must be a CSV file and once uploaded, the import will begin automatically.</p>
				<form method="POST" enctype='multipart/form-data' class="import-location-form">					
					<input type="hidden" name="action_type" value="location_upload_csv">
					<label for="import-locations-file" style="display: none;">Select file to being import</label> 
					<input id="import-locations-file" type="file" class="filestyle" name="filename" data-iconName="glyphicon-inbox" accept=".csv" style="display: none;" aria-label="Select file to being import" required aria-required="true">
					<button class="import-btn">Import</button>
				</form>
			</div>
		<?php						
		} // End of Location import page callback	

		function location_export_request(){
			if ( isset( $_GET['location-csv-export'] )) {
				
					$expargs = array (
						'post_type'		=> 'locations',
						'post_status'		=> 'publish',
						'posts_per_page'	=> -1,
					);
				
					$explocations = get_posts($expargs);
				
					if($explocations) {
						
						header('Content-Type: application/csv');
						header('Content-Disposition: attachment; filename=location-export.csv');
						$f = fopen('php://output', 'w');	
						fputcsv($f, ["post_id","title","address1","address2","city","state","zip_code","country","website","phone","email","provider_name"], ",");
						foreach($explocations as $location) {
							$post_id = $location->ID;
							$title = $location->post_title;																
							$address1 = get_post_meta($post_id, 'store_address', true);
							$address2 = get_post_meta($post_id, 'address_2', true);
							$city = get_post_meta($post_id, 'store_city', true);
							$state = get_post_meta($post_id, 'store_state', true);
							$zip = get_post_meta($post_id, 'store_zipcode', true);
							$country = get_post_meta($post_id, 'country', true);  
							$website = get_post_meta($post_id, 'website_url', true); 
							$phone = get_post_meta($post_id, 'phone_no', true);
							$email = get_post_meta($post_id, 'email', true); 
							$provider_name = get_post_meta($post_id, 'provider_name', true); 
							
							//fputcsv($f,[$post_id,"Test"]);
							fputcsv($f, [$post_id,$title,$address1,$address2,$city,$state,$zip,$country,$website,$phone,$email,$provider_name], ",");
						}
						
						fclose($f);
						exit();
					}								
			}
		}		
		
		function location_export_page() {
			    add_submenu_page(
				'edit.php?post_type=locations',
				__( 'Export Locations', 'textdomain' ),
				__( 'Export Locations', 'textdomain' ),
				'manage_options',
				'export-location',
				[$this,'location_export_page_html'],
			);
		} // end of upload page
		
		function location_export_page_html() {
		?>
			<div class="wrap">
				<h1 class="page-title">Export Locations</h1>			
				<p class="form-description">Click the button below to export all locations.</p>
				<form method='get' action="" class="export-location-form">
					<input type="hidden" name="post_type" value="locations" />
					<input type="hidden" name="page" value="export-location" />
					<input type="hidden" name='location-csv-export' id="csvExport" value="Export" />
					<button class="export-btn">Export</button>
				</form>
			</div>
		<?php						
		} // End of Location export page callback		
				
		function geocode($address){

			$apikey = esc_attr(get_option('gmap_api_key'));
			if ( empty( $apikey ) ) {
				return false;
			}

			// url encode the address
			$address = urlencode($address);

			// google map geocode api url
			$url = "https://maps.googleapis.com/maps/api/geocode/json?address=$address&key=$apikey";

			// get the json response
			$resp_json = file_get_contents($url);

			// decode the json
			$resp = json_decode($resp_json, true);

			// response status will be 'OK', if able to geocode given address 
			if($resp['status']=='OK'){

				// get the important data
				$lat = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
				$long = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";        

				// verify if data is complete
				if($lat && $long){

					// put the data in the array
					$data_arr = array();            

					array_push(
						$data_arr, 
							$lat, 
							$long                     
						);

					return $data_arr;

				}else{
					return false;
				}

			} else {        
				return false;
			}
		} // End of geocode function
		
		/**
		 * Add custom field to quick edit screen.
		 */
		function quick_edit_custom_box( $column_name ) {
			switch ( $column_name ) {
				case 'lang':
					wp_nonce_field( 'store_location_bulk_edit_nonce', 'store_location_bulk_edit_nonce' );
					?>
					<?php 
						global $wpdb;

						// Get all active wpml languages from wp_icl_languages table
						$active_languages = $wpdb->get_results( "SELECT code, english_name FROM {$wpdb->prefix}icl_languages WHERE active = 1", ARRAY_A );
					?>
					<!-- loop through all active languages -->
					<fieldset class="inline-edit-col-right">
						<div class="inline-edit-col column-<?php echo esc_attr( $column_name ); ?>">
							<label class="inline-edit-group">
									<?php esc_html_e( 'Languages: ', 'store-location' ); ?>

									<?php foreach ( $active_languages as $language ) : ?>
										<label>
											<input type="checkbox" name="languages[]" value="<?php echo $language['code']; ?>" />
											<?php echo $language['english_name']; ?>
										</label>
									<?php endforeach; ?>
							</label>
						</div>
					</fieldset>
					
					<?php
					break;
			}
		}
	} 
}

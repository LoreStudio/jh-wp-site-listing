<?php 
	$error = '';
	if ( isset($_POST['save_settings'] ) && !empty( $_POST['save_settings'] ) ) { 

		// validate nounce field	
		if ( ! isset( $_POST['validate_post_data'] ) || ! wp_verify_nonce( $_POST['validate_post_data'], 'validate_settings_data' ) ) {
			$error = __( 'Sorry, your nonce did not verify.', 'store-location' );
		} else {

			// var_dump_pre( $_POST );

			// save settings
			update_option( 'gmap_api_key', sanitize_text_field( $_POST['gmap_api_key'] ) );

			if( isset( $_POST["show_direction_link"] ) &&  'yes' == $_POST["show_direction_link"] ) {
				$show_link = 'yes';
			} else {
				$show_link = 'no';
			}

			update_option( 'show_direction_link', $show_link );
			update_option( 'map_json_style', stripslashes( sanitize_text_field( $_POST['map_json_style'] ) ) );
			update_option( 'gmap_marker_url', sanitize_text_field( $_POST['gmap_marker_url'] ) );

			if ( isset ( $_POST['languages'] ) ) {
				if ( is_array ( $_POST['languages'] ) ) {
					$languages = implode ( ',', $_POST['languages'] );
					update_option ( 'store-loc-active-languages', $languages );
				}
			}

			$error = __( 'Successfully Saved', 'store-location' );
		}
	}
?>
<style>
	.txt-input {
		width: 350px !important;
	}
	.form-field label{
		width:100%;
		display:block;
		margin-bottom: 5px;
	}
</style>

<div class="wrap">

	<h1 id="add-new-user">
		<?php _e( 'Store Location Settings', 'store-location' );?>
	</h1>
	<p>
		<?php _e( 'You can set all your setting here.', 'store-location' );?> 
		<?php _e( 'Use shortcode: ', 'store-location' );?><strong>[store-locations]</strong>
	</p>

	<!-- wp notices -->
	<?php if( ! empty( $error ) ) : ?>
		<div class="notice notice-success is-dismissible">
			<p>
				<?php echo esc_html( $error );?>
			</p>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text">
					<?php _e( 'Dismiss this notice.', 'store-location' );?>
				</span>
			</button>
		</div>
	<?php endif; ?>

	<form method="post" name="update-settings" action="" id="update-settings">
		<?php wp_nonce_field( 'validate_settings_data', 'validate_post_data' ); ?>
		<table class="form-table">
			<tbody> 
				<?php if ( ! $this->is_wpml_active( ) ) : ?>
					<tr class="form-field">	
						<td>
							<label for="career-slug">
								<strong>
									<?php esc_html_e( 'Active languages', 'store-location' );?>
								</strong>
							</label> 
								<?php  
									$flags_dir_url = LOCATION_DIR_URI . 'images/flags/';

									$flags_array = array ( 
										array(
											'sq',
											'sq.svg',
											'Albanian'
										),
										array(
											'ar',
											'ar.svg',
											'Arabic'
										),
										array(
											'hy',
											'hy.svg',
											'Armenian'
										),
										array(
											'az',
											'az.svg',
											'Azerbaijani'
										),
										array(
											'eu',
											'eu.svg',
											'Basque'
										),
										array(
											'bn',
											'bn.svg',
											'Bengali'
										),
										array(
											'bs',
											'bs.svg',
											'Bosnian'
										),
										array(
											'bg',
											'bg.svg',
											'Bulgarian'
										),
										array(
											'ca',
											'ca.svg',
											'Catalan'
										),
										array(
											'zh-hans',
											'zh-hans.svg',
											'Chinese (Simplified)'
										),
										array(
											'zh-hant',
											'zh-hant.svg',
											'Chinese (Traditional)'
										),
										array(
											'hr',
											'hr.svg',
											'Croatian'
										),
										array(
											'cs',
											'cs.svg',
											'Czech'
										),
										array(
											'da',
											'da.svg',
											'Danish'
										),
										array(
											'nl',
											'nl.svg',
											'Dutch'
										),
										array(
											'en',
											'us.png',
											'English'
										),
										array(
											'eo',
											'eo.svg',
											'Esperanto'
										),
										array(
											'et',
											'et.svg',
											'Estonian'
										),
										array(
											'fi',
											'fi.svg',
											'Finnish'
										),
										array(
											'fr',
											'fr.svg',
											'French	'
										),
										array(
											'gl',
											'gl.svg',
											'Galician'
										),
										array(
											'de',
											'de.svg',
											'German	'
										),
										array(
											'el',
											'el.svg',
											'Greek	'
										),
										array(
											'he',
											'he.svg',
											'Hebrew	'
										),
										array(
											'hi',
											'hi.svg',
											'Hindi	'
										),
										array(
											'hu',
											'hu.svg',
											'Hungarian'
										),
										array(
											'is',
											'is.svg',
											'Icelandic'
										),
										array(
											'id',
											'id.svg',
											'Indonesian'
										),
										array(
											'ga',
											'ga.svg',
											'Irish'
										),
										array(
											'it',
											'it.svg',
											'Italian'
										),
										array(
											'ja',
											'ja.svg',
											'Japanese'
										),
										array(
											'ko',
											'ko.svg',
											'Korean'
										),
										array(
											'ku',
											'ku.svg',
											'Kurdish'
										),
										array(
											'lv',
											'lv.svg',
											'Latvian'
										),
										array(
											'lt',
											'lt.svg',
											'Lithuanian'
										),
										array(
											'mk',
											'mk.svg',
											'Macedonian'
										),
										array(
											'ms',
											'ms.svg',
											'Malay'
										),
										array(
											'mt',
											'mt.svg',
											'Maltese'
										),
										array(
											'mn',
											'mn.svg',
											'Mongolian'
										),
										array(
											'ne',
											'ne.svg',
											'Nepali'
										),
										array(
											'no',
											'no.svg',
											'Norwegian Bokmål'
										),
										array(
											'fa',
											'fa.svg',
											'Persian'
										),
										array(
											'pl',
											'pl.svg',
											'Polish	'
										),
										array(
											'pt-br',
											'pt-br.svg',
											'Portuguese (Brazil)'
										),
										array(
											'pt-pt',
											'pt-pt.svg',
											'Portuguese (Portugal)'
										),
										array(
											'pa',
											'pa.svg',
											'Punjabi'
										),
										array(
											'qu',
											'qu.svg',
											'Quechua'
										),
										array(
											'ro',
											'ro.svg',
											'Romanian'
										),
										array(
											'ru',
											'ru.svg',
											'Russian'
										),
										array(
											'sr',
											'sr.svg',
											'Serbian'
										),
										array(
											'sk',
											'sk.svg',
											'Slovak'
										),
										array(
											'sl',
											'sl.svg',
											'Slovenian'
										),
										array(
											'so',
											'so.svg',
											'Somali'
										),
										array(
											'es',
											'es.svg',
											'Spanish'
										),
										array(
											'es-us',
											'us.png',
											'Spanish (US)'
										),
										array(
											'sv',
											'sv.svg',
											'Swedish'
										),
										array(
											'ta',
											'ta.svg',
											'Tamil'
										),
										array(
											'th',
											'th.svg',
											'Thai'
										),
										array(
											'tr',
											'tr.svg',
											'Turkish'
										),
										array(
											'uk',
											'uk.svg',
											'Ukrainian'
										),
										array(
											'ur',
											'ur.svg',
											'Urdu'
										),
										array(
											'uz',
											'uz.svg',
											'Uzbek'
										),
										array(
											'vi',
											'vi.svg',
											'Vietnamese'
										),
										array(
											'cy',
											'cy.svg',
											'Welsh'
										),
										array(
											'yi',
											'yi.svg',
											'Yiddish'
										),
										array(
											'zu',
											'zu.svg',
											'Zulu'
										)
									);
									$active_languages = get_option( 'store-loc-active-languages' );
									// var_dump_pre( $active_languages );

									$active_languages_array = explode( ',', $active_languages );
									// var_dump_pre( $active_languages_array );

								?>
								<ul class="available-languages">
									<?php foreach ( $flags_array as $value ) : ?>
										<li class="<?php if ( in_array ( $value[0] , $active_languages_array ) ) { echo '_active'; } ?>">
											<label for="wpml-language-<?php echo $value[0];?>">
												<input type="checkbox" name="languages[]" id="wpml-language-<?php echo $value[0];?>" value="<?php echo $value[0];?>" <?php if ( in_array ( $value[0] , $active_languages_array ) ) { echo 'checked'; } ?>>
												<img width="18" height="12" src="<?php echo $flags_dir_url.$value[1];?>" alt="Flag for <?php echo $value[0];?>">
												<?php echo $value[2];?>
											</label>
										</li>
									<?php endforeach ?>
								</ul>
						</td>
					</tr>
				<?php endif ?>
				<tr class="form-field">	
					<td>
						<label for="career-slug">
							<strong>
								<?php esc_html_e( 'Maps API Key', 'store-location' );?>
							</strong>
						</label> 
						<input type="text" name="gmap_api_key" id="gmap_api_key" value="<?php echo esc_attr( get_option( 'gmap_api_key' ) );?>" class="txt-input">
					</td>
				</tr>
				<tr class="form-field">	
					<td>
						<label for="career-slug">
							<strong>
								<?php esc_html_e( 'Marker Icon URL', 'store-location' );?>
							</strong>
						</label> 
						<input type="text" name="gmap_marker_url" id="gmap_marker_url" value="<?php echo esc_attr( get_option( 'gmap_marker_url' ) );?>" class="txt-input">
					</td>
				</tr>
				<tr class="form-field">	
					<td>
						<label for="career-slug">
							<strong>
								<?php esc_html_e( 'Show Directions Link', 'store-location' );?> 
							</strong> 
						</label> 
						<input type="checkbox" name="show_direction_link" id="show_direction_link" value="yes"  <?php if ( 'yes' == get_option( 'show_direction_link' ) ) echo 'checked="checked"'; ?>>
					</td>
				</tr>
				<tr class="form-field">	
					<td>
						<label for="career-slug">
							<strong>
								<?php esc_html_e( 'Map Json Style', 'store-location' );?> 
							</strong> <?php esc_html_e( 'E.g', 'store-location' );?>  
							<code>
								{
									"elementType": "geometry",
									"stylers": [
										{
											"color": "#242f3e"
										}
									]
								},
							</code>
						</label> 
						<textarea name="map_json_style" style="height:200px; width:600px;">
							<?php echo get_option( 'map_json_style' );?>
						</textarea>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<input class="button button-primary" name="save_settings" value="Save Settings" type="submit">
			<span class="acf-spinner">
			</span>
		</p>
	</form>
</div>

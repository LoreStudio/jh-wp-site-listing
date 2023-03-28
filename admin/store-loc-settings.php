<?php 
	$error = '';
	if ( isset($_POST['save_settings'] ) && !empty( $_POST['save_settings'] ) ) { 

		// validate nounce field	

		if ( ! isset( $_POST['validate_post_data'] ) || ! wp_verify_nonce( $_POST['validate_post_data'], 'validate_settings_data' ) ) {
			$error = __( 'Sorry, your nonce did not verify.', 'store-location' );
		} else {
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
		<?php _e( 'Use shortcode :', 'store-location' );?><strong>[store-locations]</strong>
	</p>
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

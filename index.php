<?php
/*
Plugin Name: Button Color Customizer for WPBakery Composer
Plugin URI: https://greenvillewebworks.com/how-to-customize-and-add-colors-of-wp-bakery-default-buttons/
Description: Easily Customize WPBakery Button Colors by removing defaults and adding your own custom colors.
Version: 1.0
Author: Greenville Webworks
Author URI: https://greenvillewebworks.com
*/


if ( ! defined( 'ABSPATH' ) ) exit;

$wpbcc_default_colors = array('Classic_Grey', 'Classic_Blue', 'Classic_Turquoise', 'Classic_Green', 'Classic_Orange', 'Classic_Red', 'Classic_Black', 'Blue', 'Turquoise', 'Pink', 'Violet', 'Peacoc', 'Chino', 'Mulled_Wine', 'Vista_Blue', 'Black', 'Grey', 'Orange', 'Sky', 'Green', 'Juicy_pink', 'Sandy_brown', 'Purple', 'White');

	
add_action( 'admin_menu', 'register_wpbcc_menu' );
function register_wpbcc_menu(){ add_submenu_page('vc-general','Button Customizer','Button Customizer','manage_options','wpbcc_options','wpbcc_options'); }


add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'wpbcc_add_plugin_page_settings_link');
function wpbcc_add_plugin_page_settings_link( $links ) {
	$links[] = '<a href="' .
		admin_url( 'admin.php?page=wpbcc_options' ) .
		'">' . __('Settings') . '</a>';
	return $links;
}

// Add Custom Colors to VC Button Dropdown.
add_action('admin_head', 'vc_custom_buttons');
function vc_custom_buttons() {
  
  echo "<style>\r\n";

  		$customs = get_option('wpb_custom_colors');
		foreach ($customs as $key=>$value){
			$name = $value[0]; $rgb = $value[1]; $text = $value[2];
			$name = str_replace(" ", "", $name);
			echo ".vc_colored-dropdown .$name { background-color: $rgb!important; color: $text!important; }\r\n";
		
		} // end foreach
		
  echo "</style>\r\n";

} // end vc_custom_buttons


function wpbcc_options(){

	
	global $wpbcc_default_colors;
	
	if ($_POST['submit']=='Update Default Colors') wpbcc_update_defaults();
	if ($_POST['submit']=='Add Color') wpbcc_add_color();
	if (isset($_GET['delete'])) wpbcc_delete_color();
	
	$saved_default_colors = get_option('wpb_default_buttons'); ?>
	
	<div class='wrap'><h1>WPB Button Customizer</h1>
	
	<h2>Custom Buttons</h2>
	<table>
		<tr>
			<th>Color Name</th>
			<th>Background Color</th>
			<th>Text Color</th>
			<th></th>
		</tr>
	<?php
	
		$customs = get_option('wpb_custom_colors');
		foreach ($customs as $key=>$value){
			$name = $value[0]; $rgb = $value[1]; $text = $value[2];

			echo "<tr><td>$name</td><td><div class='colorsample' style='background-color: $rgb'></div> $rgb</td><td>$text</td><td><a class='button button-secondary' href='/wp-admin/admin.php?page=wpbcc_options&delete=$key'>Delete</a></td></tr>";
		}
		
	?>
	<tr>
		<td colspan="4"><hr></td>
	</tr>
	<tr>
			<th>Color Name</th>
			<th>Background Color</th>
			<th>Text Color</th>
			<th></th>
		</tr>
	<tr><td><form action="/wp-admin/admin.php?page=wpbcc_options" method="POST">
		<input type="text" name="colorname" placeholder="Light Grey"></td>
		
	<td><input type="text" name="colorcode" placeholder="#efefef"></td>
	<td><input type="text" name="textcode" placeholder="#000000"></td>
	<td><input type="submit" name="submit" class="button button-primary" value="Add Color">	
	</form></td>
	</tr>
	
	</table>
	<br>
	<hr>
	<h2 class='title'>Default Button Colors</h2>
	

	<style>
		th { text-align: left; }
		td { padding-right: 20px; }
		table { margin-bottom: 20px; }
		.colorsample { width: 10px; height: 10px; border: 1px solid #999; display: inline-block; }
	</style><?php 
		
	echo '<form action="/wp-admin/admin.php?page=wpbcc_options" method="POST"><table>';
	
				foreach ($wpbcc_default_colors as $color){
				$pcolor = str_replace("_", " ", $color);
					
				echo "<tr><td>$pcolor</td>\r\n";
				if ($saved_default_colors[$color]=='disabled')
				echo "<td><input type='radio' name='$color' class='def_enable' value='enabled'> Enabled</td><td><input type='radio' name='$color' value='disabled' class='def_disable' checked='checked'> Disabled</td></tr>";
				else echo "<td><input type='radio' class='def_enable' name='$color' value='enabled' checked='checked'> Enabled</td><td><input type='radio' name='$color' class='def_disable' value='disabled'> Disabled</td></tr>";
				} // end foreach default colors
		
		
	?>
	<tr>
		<td></td>
		<td><a id="enable_all" class="button button-secondary">Enable All</a></td>
		<td><a id="disable_all" class="button button-secondary">Disable All</a></td>
	</tr>

	</table>
	
	<input type="submit" name="submit" id="submit" class="button button-primary" value="Update Default Colors">
	</form>
	
	<script type="text/javascript">
		jQuery( document ).ready(function() {
			jQuery( "#enable_all" ).click(function(e) { e.preventDefault(); jQuery(".def_enable").attr('checked', true); });
			jQuery( "#disable_all" ).click(function(e) { e.preventDefault(); jQuery(".def_disable").attr('checked', true); });
		});
	</script>

	</div>
	
	<?php
} // end wpbcc_options


add_action( 'vc_after_init', 'change_wpb_button_colors' );

function wpb_custom_button_styles() {
	
	// Inject the CSS for the new button styles into the header.
	
	$customs = get_option('wpb_custom_colors');
		
	echo "<style>\r\n";
		foreach ($customs as $custom){
			$name = $custom[0];
			$rgb = $custom[1];
			$text = $custom[2];
			$shortname = str_replace(" ", "", $name);
			
			echo '.vc_btn3-color-'.$shortname.' { background-color: '.$rgb.'!important; color: '.$text.'!important; }'."\r\n";
		}
	echo "</style>\r\n";
}
add_filter('wp_head', 'wpb_custom_button_styles');

 
function change_wpb_button_colors() {
	
		$param = WPBMap::getParam( 'vc_btn', 'color' );
		
		$customs = get_option('wpb_custom_colors');
		
		foreach ($customs as $custom){
			$name = $custom[0];
			$shortname = str_replace(" ", "", $name);
			$param['value'][__( $name, 'my-text-domain' )] = $shortname;
		}
		
		$saved_default_colors = get_option('wpb_default_buttons');
		
		foreach ($saved_default_colors as $key=>$status){
			if ($status=='disabled'){
				
				$colorname = str_replace("_", " ", $key);
				unset($param['value'][$colorname]);
				
			}
		}
			
		vc_update_shortcode_param( 'vc_btn', $param );
}


function wpbcc_update_defaults(){
	
	global $wpbcc_default_colors;
	$save_defaults = array();
	foreach ($wpbcc_default_colors as $color){ $save_defaults[$color] = sanitize_text_field($_POST[$color]); }
	update_option('wpb_default_buttons', $save_defaults);
	
}

function wpbcc_add_color(){
	
	$customs = get_option('wpb_custom_colors');
	$colorname = sanitize_text_field($_POST['colorname']);
	$colorname = preg_replace('/[^a-zA-Z0-9\s]/', '', $colorname);
	$colorcode = sanitize_text_field($_POST['colorcode']);
	$textcode = sanitize_text_field($_POST['textcode']);
	$customs[] = array($colorname, $colorcode, $textcode);
	update_option('wpb_custom_colors', $customs);
	
}

function wpbcc_delete_color(){
	
	$customs = get_option('wpb_custom_colors');
	$delid = sanitize_text_field($_GET['delete']);
	unset($customs[$delid]);
	update_option('wpb_custom_colors', $customs);
	
}

<?php
/*
Plugin Name: Similar post-title checker
Plugin URI: http://wp-parsi.com
Description: This plugin provides similar posts title to prevent duplicate post title and publish unique post title when adding new post in admin area.
Version: 1.0.0
Author: WP-Parsi team
Author URI: http://wp-parsi.com
Text Domain: sp-post-title
Domain Path: /languages/
*/

add_action( 'plugins_loaded', 'sp_load_textdomain' );
function sp_load_textdomain() {
	load_plugin_textdomain( 'sp-post-title', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
}

//Enqueue style, script and use wp_ajax

function load_sp_wp_admin_scripts($hook_pageuse) {
	if($hook_pageuse == 'post.php' || $hook_pageuse == 'post-new.php') {
        wp_register_style( 'sp_wp_admin_css', plugin_dir_url( __FILE__ ) . 'asset/css/admin.css', false, '1.0.0' );
        wp_enqueue_style( 'sp_wp_admin_css' );
        wp_enqueue_script( 'sp-ajax-handle', plugin_dir_url( __FILE__ ) . 'asset/js/ajax.js', array( 'jquery' ), '1.0.0', true );
    }
}
add_action( 'admin_enqueue_scripts', 'load_sp_wp_admin_scripts' );
add_action( 'wp_ajax_sp_ajax_hook_sc', 'sp_process_sc');
add_action( 'wp_ajax_sp_ajax_hook', 'sp_process');

/**
 * Main process
 * @return string
 */

function sp_process(){
	$post_types = get_post_types();
	global $wpdb;
	if($_POST['sptitle'] != ''){
		$sptitle = $_POST['sptitle'];
		$splimit = get_option( 'sp_screen_options_limit', 10);
		$spminchar = get_option( 'sp_screen_options_minchar', 3);
		
		$splen = mb_strlen($sptitle);
		if($splen >= $spminchar){
			$results = $wpdb->get_results( "select * from ".$wpdb->prefix."posts where post_title like '$sptitle%' and post_status = 'publish' limit 0,$splimit" );
			#echo "<xmp>".print_r($results, true)."</xmp>";
			$out = '';
			if(!empty($results)){
				$out .= "<ul class='postbox'>";
				foreach($results as $result){
					if(in_array($result->post_type, $post_types)){
						$out .=  "<li><a href='".home_url()."/wp-admin/post.php?post=".$result->ID."&action=edit' target='_blank'>".$result->post_title."</a> [".$result->post_type."]</li>";
					}
				}
				$out .= "</ul>";
			}
			echo $out;
		}
	}
}

//Hook option to screen-option

add_filter('set-screen-option', 'sp_set_option', 10, 3);
function sp_set_option($status, $option, $value) {
    return $value;
}
add_filter('screen_settings', 'sp_show_screen_options', 10, 2 );
function sp_show_screen_options( $status, $args ) {
	$return = $status;
        global $pagenow;
        if ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) {    
			$return .= "
			<h5>".__( 'Similar posts options', 'sp-post-title' )."</h5>
			<div class='metabox-prefs'>
			<div class='sp_custom_fields'>
			    <label for='sp_limit'><input type='number' name='sp_screen_options_limit' id='sp_screen_options_limit' step='1' min='1' class='small-text' value='".get_option( 'sp_screen_options_limit', 10)."' /> ".__( 'Results limit', 'sp-post-title' )." </label>
			    <label for='sp_minchar'><input type='number' name='sp_screen_options_minchar' id='sp_screen_options_minchar' step='1' min='1' class='small-text' value='".get_option( 'sp_screen_options_minchar', 3)."' /> ".__( 'Limit input character to search', 'sp-post-title' )." </label>
			    <input type='button' name='sp-screen-options-apply' id='sp-screen-options-apply' class='button' value='".__( 'Save Options', 'sp-post-title' )."'/> <span class='msg success'>".__( 'Options saved', 'sp-post-title' )."</span><span class='msg error'>".__( 'Error occurred', 'sp-post-title' )."</span>
			</div>
			</div>";
        }
        return $return;
}

/**
 * Save option process
 * @return string
 */

function sp_process_sc(){
	if ( isset($_POST['splimit']) ) { 
		update_option( 'sp_screen_options_limit', $_POST['splimit'] );
		update_option( 'sp_screen_options_minchar', $_POST['spminchar'] );
	}
}
?>
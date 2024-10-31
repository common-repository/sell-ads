<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Plugin Name:       Sell Ads
 * Plugin URI:        https://wpflamingo.com/
 * Description:       The easiest way to sell adspace on your WordPress Website
 * Version:           1.5.2
 * Requires at least: 5.2
 * Requires PHP:      7.0
 * Author:            WPflamingo
 * Author URI:        https://wpflamingo.com/our-developers/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wpsap_ads_plugin
 * Domain Path:       /languages
 */


define("WPSAP_Version",'1.5.2');
define('WPSAP_Plugin_Path',plugin_dir_path( __FILE__ ));
define('WPSAP_Plugin_Dir',plugin_dir_url( __FILE__ ));

require WPSAP_Plugin_Path.'inc/helper.php';
require WPSAP_Plugin_Path.'inc/ajax.php';
require WPSAP_Plugin_Path.'inc/settings.php';
require WPSAP_Plugin_Path.'inc/hooks.php';
require WPSAP_Plugin_Path.'inc/campaign_fields.php';
require WPSAP_Plugin_Path.'inc/shortcode.php';


function wpsap_plugin_scripts(){
    wp_enqueue_style( 'wpsap_css',WPSAP_Plugin_Dir.'css/style.css', array(),WPSAP_Version, 'all');
    wp_enqueue_script( 'wpsap_js',  WPSAP_Plugin_Dir.'js/script.js', array ( 'jquery' ),WPSAP_Version, true);
    wp_localize_script( 'wpsap_js', 'wpsapAjax', array('ajaxurl' => admin_url( 'admin-ajax.php' ),'security' => wp_create_nonce( 'wpsap_dddstring' )));    
    if(isset($_GET['confirm'])){
       wp_enqueue_style( 'wpsap_css',WPSAP_Plugin_Dir.'inc/page/style.css', array(),WPSAP_Version, 'all');  
       wp_enqueue_script( 'wpsap_confirm_js',  WPSAP_Plugin_Dir.'inc/page/wp-sap.js', array ( 'jquery','clipboard.js' ),WPSAP_Version, true);
    }
}
add_action( 'wp_enqueue_scripts', 'wpsap_plugin_scripts' );
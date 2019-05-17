<?php
/*
Plugin name:    CF7-Dynamic-progression
Description:    Ease users to fill up a ponderous form on your website. Save progression in the middle of a survey. WPTNL Dynamic CF7 plugin is an add-on for the Contact Form 7 plugin.
Author:         Etienne Pradillon
License :       GPL2
License URI :   https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
Text Domain: 
Domain Path:    /languages
Version:        1.0.0
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

// Initialize constants.
define( 'CF7DP_VERSION', '1.0.0' );
define( 'CF7DP_PLUGIN', __FILE__ );
define( 'CF7DP_DIR',  plugin_dir_url ( __FILE__ ) );
define( 'CF7DP_PATH', plugin_dir_path( __FILE__ ) );

// Dependencies.
require_once( CF7DP_PATH . 'inc/DynamicProgressionData.php' );
require_once( CF7DP_PATH . 'inc/DynamicProgressionSelect.php' );
require_once( CF7DP_PATH . 'inc/DynamicProgressionCheckboxes.php' );
require_once( CF7DP_PATH . 'inc/DynamicProgressionTextArea.php' );
require_once( CF7DP_PATH . 'inc/DynamicProgressionAcceptance.php' );
require_once( CF7DP_PATH . 'inc/HookManager.php' );

// Initialize the plugin the Wordpress' way.
add_action( 'plugins_loaded', 'dynamicProgrogressionStart', 10 );

/**
 * Initialize CF7-Dynamic-progression.
 */
function dynamicProgrogressionStart() {
	global $wpdb;
	$user = wp_get_current_user();
	$cf7dp_datas = new DynamicProgressionData( $wpdb, $user );	
	$hookManager = new HookManager(); //call Dynamic-Progression class to register functionnalities into wordpress via hooks

	//$newProgressionSelect = new DynamicProgressionSelect( $hookManager, $cf7dp_datas );	//new DynamicProgressionSelect();
	//$newProgressionSelect->addProgressionSelect();

	//$newProgressionSelect = new DynamicProgressionAcceptance( $hookManager, $cf7dp_datas );	//new DynamicProgressionAcceptance();
	//$newProgressionSelect->addProgressionAcceptance();

	new DynamicProgressionCheckboxes();
	new DynamicProgressionTextArea();

	/**
	 * WordPress Shortcode designed to be used with the [Contact Form 7-Dynamic Text Extension] plugin.
	 * 
	 * The shortcode return the previous user's answer or null if no previous answer. 
	 * Used as the dynamic-value option from [dynamic text] balise with the same patern.
	 * 
	 * @param string Shortcode tag to be searched in the WordPress post content
	 * @param 
	 * 
	 * @return string the answer to atts'question for current user | null if has never been answered.
	 */
	add_shortcode( 'dynamicProgression_get_answer', function( array $atts ) use ( $cf7dp_datas ) {
		$atts = shortcode_atts( [
			'question' => ''
		], $atts );
		return $cf7dp_datas->dynamicProgGetResults( $atts['question'] );
	});

	/**
	 * Enqueue script for this plugin
	 * 
	 * The cached version is verified with the last modification the file.
	 * Require jquery to be loaded.
	 * 
	 * @see https://developer.wordpress.org/reference/functions/wp_enqueue_script/ | wp_enqueue_script() documentation.
	 */
	add_action('wpcf7_contact_form', function() {		
		if (is_admin()) {
			return; //No use for this script in th admin panel of wordpress.
		}		
		$js_version = date("ymd-Gis", filemtime( CF7DP_PATH . 'js/front.js' )); //Script version is made from its last update.
		wp_enqueue_script('dynamicProgScripts', plugins_url('js/front.js', __FILE__), ['jquery'], $js_version, true);
	}, 10, 1);
}

/**
 * TO DO :
 * Create a shortcode estimating user completion of current survey / Contact-Form.
 */
//add_shortcode( '', );



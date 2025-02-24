<?php
/**
 * Plugin Name: MB Multivendor
 * Description: its a extension That Transforms Your WooCommerce Site into a Marketplace with multivendor.
 * Author: MAGE BAZAR
 * Version: 1.0
 * Author URI: https://www.magebazar.com/
 * Text Domain: MB-multivendor
 * Domain Path: /languages/
 */




define('WCMb_PLUGIN_TOKEN', 'wcmb');

define('WCMb_TEXT_DOMAIN', 'MB-multivendor');

define('WCMb_PLUGIN_VERSION', '1.0');

define('WCMB_SCRIPT_DEBUG', false);



if (!class_exists('WC_Dependencies_Product_Vendor')) {
    require_once 'includes/class-wcmb-dependencies.php';
}
require_once 'includes/wcmb-core-functions.php';
//require_once 'wc_mb_config.php';
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
if (!defined('WCMb_PLUGIN_TOKEN')) {
    exit;
}
if (!defined('WCMb_TEXT_DOMAIN')) {
    exit;
}

/* Check whether another multi vendor plugin exist */
register_activation_hook(__FILE__, 'wcmb_check_if_another_vendor_plugin_exits');
/* Plugin activation hook */
register_activation_hook(__FILE__, 'activate_wcmb_plugin');
/* Plugin deactivation hook */
register_deactivation_hook(__FILE__, 'deactivate_wcmb_plugin');
/* Remove rewrite rules and then recreate rewrite rules. */
register_activation_hook(__FILE__, 'flush_rewrite_rules');

//add_action('init', 'wcmb_plugin_init');
//add_action('admin_init', 'wcmb_delete_woocomerce_transient_redirect_to_wcmb_setup', 5);
/**
 * Load setup class 
 */
//function wcmb_plugin_init() {
    //$current_page = filter_input(INPUT_GET, 'page');
    //if ($current_page && $current_page == 'wcmb-setup') {
      //  include_once(dirname( __FILE__ ) . '/admin/class-wcmb-admin-setup-wizard.php');
  //  }
//}
/**
 * Delete WooCommerce activation redirect transient
 */
/*function wcmb_delete_woocomerce_transient_redirect_to_wcmb_setup(){
    if ( get_transient( '_wc_activation_redirect' ) ) {
        delete_transient( '_wc_activation_redirect' );
        return;
    }
    if ( get_transient( '_wcmb_activation_redirect' ) ) {
        delete_transient( '_wcmb_activation_redirect' );
        if ( ( ! empty( $_GET['page'] ) && in_array( $_GET['page'], array( 'wcmb-setup' ) ) ) || is_network_admin() || isset( $_GET['activate-multi'] ) || apply_filters( 'wcmb_prevent_automatic_wizard_redirect', false ) ) {
                return;
        }
        wp_safe_redirect( admin_url( 'index.php?page=wcmb-setup' ) );
	exit;
    }
}*/

if (!class_exists('WCMb') && WC_Dependencies_Product_Vendor::is_woocommerce_active()) {
    global $WCMb;
    require_once( 'classes/class-wcmb.php' );
    /* recheck plugin install */
    add_action('plugins_loaded', 'activate_wcmb_plugin');
    /* Initiate plugin main class */
    $WCMb = new WCMb(__FILE__);
    $GLOBALS['WCMb'] = $WCMb;
    if (is_admin() && !defined('DOING_AJAX')) {
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'WCMb_action_links');
    }
} else {
    add_action('admin_notices', 'wcmb_admin_notice');

    function wcmb_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e('MB plugin requires <a href="https://wordpress.org/plugins/woocommerce/">WooCommerce</a> plugins to be active!', 'MB-multivendor'); ?></p>
        </div>
        <?php
    }

}

function wcmb_namespace_approve( $value ) {
	
	$rest_prefix = trailingslashit( rest_get_url_prefix() );
	
	// Allow third party plugins use our authentication methods.
	$wcmb_support = ( false !== strpos( $_SERVER['REQUEST_URI'], $rest_prefix . 'wcmb' ) );
	
	if($value || $wcmb_support) $return = true;
	else $return = false;
	
	return $return;
}

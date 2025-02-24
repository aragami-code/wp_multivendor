<?php

/**
 */
class WCMb_Vendor_Dashboard_Shortcode {

    public function __construct() {
        
    }

    /**
     * Output the vendor dashboard shortcode.
     *
     * @access public
     * @param array $atts
     * @return void
     */
    public static function output($attr) {
        global $WCMb, $wp;
        $WCMb->nocache();
        if (!defined('WCMB_DASHBAOARD')) {
            define('WCMB_DASHBAOARD', true);
        }
        if (!is_user_logged_in()) {
            if (( 'no' === get_option('woocommerce_registration_generate_password') && !is_user_logged_in())) {
                wp_enqueue_script('wc-password-strength-meter');
            }
            // Remove default registration form from wcmb dashboard endpoint for logged out vendor
            update_option( 'woocommerce_enable_myaccount_registration', 'no' );
            echo '<div class="wcmb-dashboard woocommerce">';
            wc_get_template('myaccount/form-login.php');
            echo '</div>';
            // Undo wcmb changes
            update_option( 'woocommerce_enable_myaccount_registration', 'yes' );
        } else if (!is_user_wcmb_vendor(get_current_vendor_id())) {
        	$user = wp_get_current_user();
        	
        	if ($user && in_array('dc_pending_vendor', $user->roles)) {
        		$WCMb->template->get_template('shortcode/pending_vendor_dashboard.php');
        	} else if ($user && in_array('dc_rejected_vendor', $user->roles)) {
        		$WCMb->template->get_template('shortcode/rejected_vendor_dashboard.php');
        	} else {
        		$WCMb->template->get_template('shortcode/non_vendor_dashboard.php');
            }
        } else {
            do_action('wcmb_dashboard_setup');
            $WCMb->template->get_template('shortcode/vendor_dashboard.php');
        }
    }

}

<?php

/**
 */
class WCMb_Vendor_Coupon_Shortcode {

    public function __construct() {
        
    }

    /**
     * Output the vendor coupon shortcode.
     *
     * @access public
     * @param array $atts
     * @return void
     */
    public static function output($attr) {
        global $WCMb;
        $WCMb->nocache();
        $coupon_arr = array();
        if (!defined('WCMB_DASHBAOARD')) {
            define('WCMB_DASHBAOARD', true);
        }
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            if (is_user_wcmb_vendor($user->ID)) {
                $vendor = get_wcmb_vendor($user->ID);
                if ($vendor) {
                    $args = array(
                        'posts_per_page' => -1,
                        'post_type' => 'shop_coupon',
                        'author' => $user->ID,
                        'post_status' => 'any'
                    );
                    $coupons = get_posts($args);
                    if (!empty($coupons)) {
                        foreach ($coupons as $coupon) {
                            $coupon_arr[] += $coupon->ID;
                        }
                    }
                }
                $WCMb->template->get_template('shortcode/vendor_coupon.php', array('coupons' => $coupon_arr));
            }
        }
    }

}

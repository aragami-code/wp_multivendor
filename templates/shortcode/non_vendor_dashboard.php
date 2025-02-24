<?php
/**

 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
global $woocommerce, $WCMb;
$user = wp_get_current_user();
if ($user && !in_array('dc_pending_vendor', $user->roles) && !in_array('administrator', $user->roles)) {
    add_filter('wcmb_vendor_registration_submit', function ($text) {
        return __('Apply to become a vendor', 'MB-multivendor');
    });
    echo '<div class="woocommerce">';
    echo do_shortcode('[vendor_registration]');
    echo '</div>';
}

if ($user && in_array('administrator', $user->roles)) {
    ?>
    <div class="container">
        <div class="well text-center wcmb-non-vendor-notice">
            <p><?php echo sprintf(__('You have logged in as Administrator. Please <a href="%s">log out</a> and then view this page.', 'MB-multivendor'), wc_logout_url()); ?></p>
        </div>
    </div>
    <?php
}
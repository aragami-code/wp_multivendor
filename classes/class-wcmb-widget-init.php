<?php

/**
 */
class WCMb_Widget_Init {

    public function __construct() {
        add_action('widgets_init', array($this, 'product_vendor_register_widgets'));
        add_action('wp_dashboard_setup', array($this, 'wcmb_rm_meta_boxes'));
    }

    /**
     * Add vendor widgets
     */
    public function product_vendor_register_widgets() {
        include_once ('widgets/class-wcmb-widget-vendor-info.php');
        require_once ('widgets/class-wcmb-widget-vendor-list.php');
        require_once ('widgets/class-wcmb-widget-vendor-quick-info.php');
        require_once ('widgets/class-wcmb-widget-vendor-location.php');
        require_once ('widgets/class-wcmb-widget-vendor-product-categories.php');
        require_once ('widgets/class-wcmb-widget-vendor-top-rated-products.php');
        register_widget('DC_Widget_Vendor_Info');
        register_widget('DC_Widget_Vendor_List');
        register_widget('DC_Widget_Quick_Info_Widget');
        register_widget('DC_Woocommerce_Store_Location_Widget');
        register_widget('WCMb_Widget_Vendor_Product_Categories');
        register_widget('WCMb_Widget_Vendor_Top_Rated_Products');
    }

    /**
     * Removing woocommerce widget from vendor dashboard
     */
    public function wcmb_rm_meta_boxes() {
        if (is_user_wcmb_vendor(get_current_vendor_id())) {
            remove_meta_box('woocommerce_dashboard_status', 'dashboard', 'normal');
        }
    }

}

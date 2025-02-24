<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 
 */
class WCMb_Capabilities {

    public $capability;
    public $general_cap;
    public $vendor_cap;
    public $frontend_cap;
    public $payment_cap;
    public $wcmb_capability = array();

    public function __construct() {
        $this->wcmb_capability = array_merge(
                $this->wcmb_capability
                , (array) get_option('wcmb_general_settings_name', array())
                , (array) get_option('wcmb_capabilities_product_settings_name', array())
                , (array) get_option('wcmb_capabilities_order_settings_name', array())
                , (array) get_option('wcmb_capabilities_miscellaneous_settings_name', array())
        );
        $this->frontend_cap = get_option("wcmb_frontend_settings_name");
        $this->payment_cap = get_option("wcmb_payment_settings_name");

        add_filter('product_type_selector', array(&$this, 'wcmb_product_type_selector'), 10, 1);
        add_filter('product_type_options', array(&$this, 'wcmb_product_type_options'), 10);
        add_filter('wc_product_sku_enabled', array(&$this, 'wcmb_wc_product_sku_enabled'), 30);

        add_action('woocommerce_get_item_data', array(&$this, 'add_sold_by_text_cart'), 30, 2);
        add_action('woocommerce_new_order_item', array(&$this, 'order_item_meta_2'), 20, 3);
        add_action('woocommerce_after_shop_loop_item', array($this, 'wcmb_after_add_to_cart_form'), 6);
        /* for single product */
        add_action('woocommerce_product_meta_start', array($this, 'wcmb_after_add_to_cart_form'), 25);
        add_action('update_option_wcmb_capabilities_product_settings_name', array(&$this, 'update_wcmb_vendor_role_capability'), 10);
    }

    /**
     * Vendor Capability from Product Settings 
     *
     * @param capability
     * @return boolean 
     */
    public function vendor_can($cap) {
        if (is_array($this->wcmb_capability) && array_key_exists($cap, $this->wcmb_capability)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Vendor Capability from General Settings 
     *
     * @param capability
     * @return boolean 
     */
    public function vendor_general_settings($cap) {
        if (is_array($this->wcmb_capability) && array_key_exists($cap, $this->wcmb_capability)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Vendor Capability from Capability Settings 
     *
     * @param capability
     * @return boolean 
     */
    public function vendor_capabilities_settings($cap, $default = array()) {
        $this->wcmb_capability = !empty($default) ? $default : $this->wcmb_capability;
        if (is_array($this->wcmb_capability) && array_key_exists($cap, $this->wcmb_capability)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Vendor Capability from Capability Settings 
     *
     * @param capability
     * @return boolean 
     */
    public function vendor_payment_settings($cap) {
        if (is_array($this->payment_cap) && array_key_exists($cap, $this->payment_cap)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get Vendor Product Types
     *
     * @param product_types
     * @return product_types 
     */
    public function wcmb_product_type_selector($product_types) {
        $user = wp_get_current_user();
        if (is_user_wcmb_vendor($user) && $product_types) {
            foreach ($product_types as $product_type => $value) {
                $vendor_can = $this->vendor_can($product_type);
                if (!$vendor_can) {
                    unset($product_types[$product_type]);
                }
            }
        }
        return apply_filters('wcmb_product_type_selector', $product_types);
    }

    /**
     * Get Vendor Product Types Options
     *
     * @param product_type_options
     * @return product_type_options 
     */
    public function wcmb_product_type_options($product_type_options) {
        $user = wp_get_current_user();
        if (is_user_wcmb_vendor($user) && $product_type_options) {
            foreach ($product_type_options as $product_type_option => $value) {
                $vendor_can = $this->vendor_can($product_type_option);
                if (!$vendor_can) {
                    unset($product_type_options[$product_type_option]);
                }
            }
        }
        return $product_type_options;
    }

    /**
     * Check if Vendor Product SKU Enable
     *
     * @param state
     * @return boolean 
     */
    public function wcmb_wc_product_sku_enabled($state) {
        $user = wp_get_current_user();
        if ( is_user_wcmb_vendor($user) ) {
            return apply_filters( 'wcmb_vendor_product_sku_enabled', true , $user->ID );
        }
        return $state;
    }

    /**
     * Add Sold by Vendor text
     *
     * @param array, cart_item
     * @return array 
     */
    public function add_sold_by_text_cart($array, $cart_item) {
        if ('Enable' === get_wcmb_vendor_settings('sold_by_catalog', 'general') && apply_filters('wcmb_sold_by_text_in_cart_checkout', true, $cart_item['product_id'])) {
            $sold_by_text = apply_filters('wcmb_sold_by_text', __('Sold By', 'MB-multivendor'), $cart_item['product_id']);
            $vendor = get_wcmb_product_vendors($cart_item['product_id']);
            if ($vendor) {
                $array = array_merge($array, array(array('name' => $sold_by_text, 'value' => $vendor->page_title)));
                do_action('after_sold_by_text_cart_page', $vendor);
            }
        }
        return $array;
    }

    /**
     * Add Sold by Vendor text
     *
     * @return void 
     */
    public function wcmb_after_add_to_cart_form() {
        global $post;
        if ('Enable' === get_wcmb_vendor_settings('sold_by_catalog', 'general') && apply_filters('wcmb_sold_by_text_after_products_shop_page', true, $post->ID)) {
            $vendor = get_wcmb_product_vendors($post->ID);
            if ($vendor) {
                $sold_by_text = apply_filters('wcmb_sold_by_text', __('Sold By', 'MB-multivendor'), $post->ID);
                echo '<a class="by-vendor-name-link" style="display: block;" href="' . $vendor->permalink . '">' . $sold_by_text . ' ' . $vendor->page_title . '</a>';
                do_action('after_sold_by_text_shop_page', $vendor);
            }
        }
    }

    /**
     * Save sold by text in database
     *
     * @param item_id, cart_item
     * @return void 
     */
    public function order_item_meta_2($item_id, $item, $order_id) { 
        if ('Enable' === get_wcmb_vendor_settings('sold_by_catalog', 'general') && apply_filters('sold_by_cart_and_checkout', true)) {
            $general_cap = apply_filters('wcmb_sold_by_text', __('Sold By', 'MB-multivendor'));
            $vendor = get_wcmb_product_vendors($item['product_id']);
            if ($vendor) {
                wc_add_order_item_meta($item_id, $general_cap, $vendor->page_title);
                wc_add_order_item_meta($item_id, '_vendor_id', $vendor->id);
            }
        }
    }

    public function update_wcmb_vendor_role_capability() {
        global $wp_roles;

        if (!class_exists('WP_Roles')) {
            return;
        }

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        $capabilities = $this->get_vendor_caps();
        foreach ($capabilities as $cap => $is_enable) {
            $wp_roles->add_cap('dc_vendor', $cap, $is_enable);
        }
        do_action('wcmb_after_update_vendor_role_capability', $capabilities, $wp_roles);
    }

    /**
     * Set up array of vendor admin capabilities
     * 
     * @since 2.7.6
     * @access public
     * @return arr Vendor capabilities
     */
    public function get_vendor_caps() {
        $caps = array();
        $capability = get_option('wcmb_capabilities_product_settings_name', array());
        if ($this->vendor_capabilities_settings('is_upload_files', $capability)) {
            $caps['upload_files'] = true;
        } else {
            $caps['upload_files'] = false;
        }
        if ($this->vendor_capabilities_settings('is_submit_product', $capability)) {
            $caps['edit_product'] = true;
            $caps['delete_product'] = true;
            $caps['edit_products'] = true;
            $caps['delete_products'] = true;
            if ($this->vendor_capabilities_settings('is_published_product', $capability)) {
                $caps['publish_products'] = true;
            } else {
                $caps['publish_products'] = false;
            }
            if ($this->vendor_capabilities_settings('is_edit_delete_published_product', $capability)) {
                $caps['edit_published_products'] = true;
                $caps['delete_published_products'] = true;
            } else {
                $caps['edit_published_products'] = false;
                $caps['delete_published_products'] = false;
            }
        } else {
            $caps['edit_product'] = false;
            $caps['delete_product'] = false;
            $caps['edit_products'] = false;
            $caps['delete_products'] = false;
            $caps['publish_products'] = false;
            $caps['edit_published_products'] = false;
            $caps['delete_published_products'] = false;
        }

        if ($this->vendor_capabilities_settings('is_submit_coupon', $capability)) {
            $caps['edit_shop_coupon'] = true;
            $caps['edit_shop_coupons'] = true;
            $caps['delete_shop_coupon'] = true;
            $caps['delete_shop_coupons'] = true;
            if ($this->vendor_capabilities_settings('is_published_coupon', $capability)) {
                $caps['publish_shop_coupons'] = true;
            } else {
                $caps['publish_shop_coupons'] = false;
            }
            if ($this->vendor_capabilities_settings('is_edit_delete_published_coupon', $capability)) {
                $caps['edit_published_shop_coupons'] = true;
                $caps['delete_published_shop_coupons'] = true;
            } else {
                $caps['edit_published_shop_coupons'] = false;
                $caps['delete_published_shop_coupons'] = false;
            }
        } else {
            $caps['edit_shop_coupon'] = false;
            $caps['edit_shop_coupons'] = false;
            $caps['delete_shop_coupon'] = false;
            $caps['delete_shop_coupons'] = false;
            $caps['publish_shop_coupons'] = false;
            $caps['edit_published_shop_coupons'] = false;
            $caps['delete_published_shop_coupons'] = false;
        }
        return apply_filters('wcmb_vendor_capabilities', $caps);
    }

}

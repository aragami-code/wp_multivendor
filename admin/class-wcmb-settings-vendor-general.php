<?php

class WCMb_Settings_Vendor_General {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;
    private $subsection;

    /**
     * Start up
     */
    public function __construct($tab, $subsection) {
        $this->tab = $tab;
        $this->subsection = $subsection;
        $this->options = get_option("wcmb_{$this->tab}_{$this->subsection}_settings_name");
        $this->settings_page_init();
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMb;
        $pages = get_pages();
        $woocommerce_pages = array(wc_get_page_id('shop'), wc_get_page_id('cart'), wc_get_page_id('checkout'), wc_get_page_id('myaccount'));
        $pages_array = array();
        if($pages){
            foreach ($pages as $page) {
                if (!in_array($page->ID, $woocommerce_pages)) {
                    $pages_array[$page->ID] = $page->post_title;
                }
            }
        }
        $settings_tab_options = array("tab" => "{$this->tab}",
            "ref" => &$this,
            "subsection" => "{$this->subsection}",
            "sections" => array(
                "wcmb_pages_section" => array("title" => __('MB Pages', 'MB-multivendor'), // Section one
                    "fields" => array(
                        "wcmb_vendor" => array('title' => __('Vendor Dashboard', 'MB-multivendor'), 'type' => 'select', 'id' => 'wcmb_vendor', 'label_for' => 'wcmb_vendor', 'name' => 'wcmb_vendor', 'options' => $pages_array, 'hints' => __('Choose your preferred page for vendor dashboard', 'MB-multivendor')), // Select
                        "vendor_registration" => array('title' => __('Vendor Registration', 'MB-multivendor'), 'type' => 'select', 'id' => 'vendor_registration', 'label_for' => 'vendor_registration', 'name' => 'vendor_registration', 'options' => $pages_array, 'hints' => __('Choose your preferred page for vendor registration', 'MB-multivendor')), // Select
                    ),
                ),
                "wcmb_vendor_general_settings_endpoint_ssection" => array(
                    "title" => __("MB Vendor Dashboard Endpoints", 'MB-multivendor')
                    , "fields" => array(
                        //'wcmb_vendor_announcements_endpoint' => array('title' => __('Vendor Announcements', 'MB-multivendor'), 'type' => 'text', 'id' => 'wcmb_vendor_announcements_endpoint', 'label_for' => 'wcmb_vendor_announcements_endpoint', 'name' => 'wcmb_vendor_announcements_endpoint', 'hints' => __('Set endpoint for vendor announcements page', 'MB-multivendor'), 'placeholder' => 'vendor-announcements'),
                        'wcmb_store_settings_endpoint' => array('title' => __('Storefront', 'MB-multivendor'), 'type' => 'text', 'id' => 'wcmb_store_settings_endpoint', 'label_for' => 'wcmb_store_settings_endpoint', 'name' => 'wcmb_store_settings_endpoint', 'hints' => __('Set endpoint for shopfront page', 'MB-multivendor'), 'placeholder' => 'storefront'),
                        'wcmb_profile_endpoint' => array('title' => __('Vendor Profile', 'MB-multivendor'), 'type' => 'text', 'id' => 'wcmb_profile_endpoint', 'label_for' => 'wcmb_profile_endpoint', 'name' => 'wcmb_profile_endpoint', 'hints' => __('Set endpoint for vendor profile management page', 'MB-multivendor'), 'placeholder' => 'profile'),
                        'wcmb_vendor_policies_endpoint' => array('title' => __('Vendor Policies', 'MB-multivendor'), 'type' => 'text', 'id' => 'wcmb_vendor_policies_endpoint', 'label_for' => 'wcmb_vendor_policies_endpoint', 'name' => 'wcmb_vendor_policies_endpoint', 'hints' => __('Set endpoint for vendor policies page', 'MB-multivendor'), 'placeholder' => 'vendor-policies'),
                        'wcmb_vendor_billing_endpoint' => array('title' => __('Vendor Billing', 'MB-multivendor'), 'type' => 'text', 'id' => 'wcmb_vendor_billing_endpoint', 'label_for' => 'wcmb_vendor_billing_endpoint', 'name' => 'wcmb_vendor_billing_endpoint', 'hints' => __('Set endpoint for vendor billing page', 'MB-multivendor'), 'placeholder' => 'vendor-billing'),
                        'wcmb_vendor_shipping_endpoint' => array('title' => __('Vendor Shipping', 'MB-multivendor'), 'type' => 'text', 'id' => 'wcmb_vendor_shipping_endpoint', 'label_for' => 'wcmb_vendor_shipping_endpoint', 'name' => 'wcmb_vendor_shipping_endpoint', 'hints' => __('Set endpoint for vendor shipping page', 'MB-multivendor'), 'placeholder' => 'vendor-shipping'),
                        'wcmb_vendor_report_endpoint' => array('title' => __('Vendor Report', 'MB-multivendor'), 'type' => 'text', 'id' => 'wcmb_vendor_report_endpoint', 'label_for' => 'wcmb_vendor_report_endpoint', 'name' => 'wcmb_vendor_report_endpoint', 'hints' => __('Set endpoint for vendor report page', 'MB-multivendor'), 'placeholder' => 'vendor-report'),
                        
                        'wcmb_add_product_endpoint' => array('title' => __('Add Product', 'MB-multivendor'), 'type' => 'text', 'id' => 'wcmb_add_product_endpoint', 'label_for' => 'wcmb_add_product_endpoint', 'name' => 'wcmb_add_product_endpoint', 'hints' => __('Set endpoint for add new product page', 'MB-multivendor'), 'placeholder' => 'add-product'),
                        'wcmb_edit_product_endpoint' => array('title' => __('Edit Product', 'MB-multivendor'), 'type' => 'text', 'id' => 'wcmb_edit_product_endpoint', 'label_for' => 'wcmb_edit_product_endpoint', 'name' => 'wcmb_edit_product_endpoint', 'hints' => __('Set endpoint for edit product page', 'MB-multivendor'), 'placeholder' => 'edit-product'),
                        'wcmb_products_endpoint' => array('title' => __('Products List', 'MB-multivendor'), 'type' => 'text', 'id' => 'wcmb_products_endpoint', 'label_for' => 'wcmb_products_endpoint', 'name' => 'wcmb_products_endpoint', 'hints' => __('Set endpoint for products list page', 'MB-multivendor'), 'placeholder' => 'products'),
                        'wcmb_add_coupon_endpoint' => array('title' => __('Add Coupon', 'MB-multivendor'), 'type' => 'text', 'id' => 'wcmb_add_coupon_endpoint', 'label_for' => 'wcmb_add_coupon_endpoint', 'name' => 'wcmb_add_coupon_endpoint', 'hints' => __('Set endpoint for add new coupon page', 'MB-multivendor'), 'placeholder' => 'add-coupon'),
                        'wcmb_coupons_endpoint' => array('title' => __('Coupons List', 'MB-multivendor'), 'type' => 'text', 'id' => 'wcmb_coupons_endpoint', 'label_for' => 'wcmb_coupons_endpoint', 'name' => 'wcmb_coupons_endpoint', 'hints' => __('Set endpoint for coupons list page', 'MB-multivendor'), 'placeholder' => 'coupons'),
                        
                        "wcmb_vendor_orders_endpoint" => array('title' => __('Vendor Orders', 'MB-multivendor'), 'type' => 'text', 'id' => 'wcmb_vendor_orders_endpoint', 'label_for' => 'wcmb_vendor_orders_endpoint', 'name' => 'wcmb_vendor_orders_endpoint', 'hints' => __('Set endpoint for vendor orders page', 'MB-multivendor'), 'placeholder' => 'vendor-orders'),
                        'wcmb_vendor_withdrawal_endpoint' => array('title' => __('Vendor Widthdrawals', 'MB-multivendor'), 'type' => 'text', 'id' => 'wcmb_vendor_withdrawal_endpoint', 'label_for' => 'wcmb_vendor_withdrawal_endpoint', 'name' => 'wcmb_vendor_withdrawal_endpoint', 'hints' => __('Set endpoint for vendor widthdrawals page', 'MB-multivendor'), 'placeholder' => 'vendor-withdrawal'),
                        'wcmb_transaction_details_endpoint' => array('title' => __('Transaction Details', 'MB-multivendor'), 'type' => 'text', 'id' => 'wcmb_transaction_details_endpoint', 'label_for' => 'wcmb_transaction_details_endpoint', 'name' => 'wcmb_transaction_details_endpoint', 'hints' => __('Set endpoint for transaction details page', 'MB-multivendor'), 'placeholder' => 'transaction-details'),
                       // 'wcmb_vendor_knowledgebase_endpoint' => array('title' => __('Vendor Knowledgebase', 'MB-multivendor'), 'type' => 'text', 'id' => 'wcmb_vendor_knowledgebase_endpoint', 'label_for' => 'wcmb_vendor_knowledgebase_endpoint', 'name' => 'wcmb_vendor_knowledgebase_endpoint', 'hints' => __('Set endpoint for vendor knowledgebase page', 'MB-multivendor'), 'placeholder' => 'vendor-knowledgebase'),
                        'wcmb_vendor_tools_endpoint' => array('title' => __('Vendor Tools', 'MB-multivendor'), 'type' => 'text', 'id' => 'wcmb_vendor_tools_endpoint', 'label_for' => 'wcmb_vendor_tools_endpoint', 'name' => 'wcmb_vendor_tools_endpoint', 'hints' => __('Set endpoint for vendor tools page', 'MB-multivendor'), 'placeholder' => 'vendor-tools'),
                        'wcmb_vendor_products_qnas_endpoint' => array('title' => __('Vendor Products Q&As', 'MB-multivendor'), 'type' => 'text', 'id' => 'wcmb_vendor_products_qnas_endpoint', 'label_for' => 'wcmb_vendor_products_qnas_endpoint', 'name' => 'wcmb_vendor_products_qnas_endpoint', 'hints' => __('Set endpoint for vendor products Q&As page', 'MB-multivendor'), 'placeholder' => 'products-qna'),
                    )
                )
            ),
        );

        $WCMb->admin->settings->settings_field_withsubtab_init(apply_filters("settings_{$this->tab}_{$this->subsection}_tab_options", $settings_tab_options));
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function wcmb_vendor_general_settings_sanitize($input) {
        global $WCMb;
        $new_input = array();
        $hasError = false;
        
        if(isset($input['wcmb_vendor'])){
            $new_input['wcmb_vendor'] = $input['wcmb_vendor'];
        }
        if(isset($input['vendor_registration'])){
            $new_input['vendor_registration'] = $input['vendor_registration'];
        }
        if (isset($input['wcmb_vendor_announcements_endpoint']) && !empty($input['wcmb_vendor_announcements_endpoint'])) {
            $new_input['wcmb_vendor_announcements_endpoint'] = sanitize_text_field($input['wcmb_vendor_announcements_endpoint']);
        }
        if (isset($input['wcmb_store_settings_endpoint']) && !empty($input['wcmb_store_settings_endpoint'])) {
            $new_input['wcmb_store_settings_endpoint'] = sanitize_text_field($input['wcmb_store_settings_endpoint']);
        }
        if (isset($input['wcmb_profile_endpoint']) && !empty($input['wcmb_profile_endpoint'])) {
            $new_input['wcmb_profile_endpoint'] = sanitize_text_field($input['wcmb_profile_endpoint']);
        }
        if (isset($input['wcmb_vendor_billing_endpoint']) && !empty($input['wcmb_vendor_billing_endpoint'])) {
            $new_input['wcmb_vendor_billing_endpoint'] = sanitize_text_field($input['wcmb_vendor_billing_endpoint']);
        }
        if (isset($input['wcmb_vendor_policies_endpoint']) && !empty($input['wcmb_vendor_policies_endpoint'])) {
            $new_input['wcmb_vendor_policies_endpoint'] = sanitize_text_field($input['wcmb_vendor_policies_endpoint']);
        }
        if (isset($input['wcmb_vendor_shipping_endpoint']) && !empty($input['wcmb_vendor_shipping_endpoint'])) {
            $new_input['wcmb_vendor_shipping_endpoint'] = sanitize_text_field($input['wcmb_vendor_shipping_endpoint']);
        }
        if (isset($input['wcmb_vendor_report_endpoint']) && !empty($input['wcmb_vendor_report_endpoint'])) {
            $new_input['wcmb_vendor_report_endpoint'] = sanitize_text_field($input['wcmb_vendor_report_endpoint']);
        }
        if (isset($input['wcmb_vendor_orders_endpoint']) && !empty($input['wcmb_vendor_orders_endpoint'])) {
            $new_input['wcmb_vendor_orders_endpoint'] = sanitize_text_field($input['wcmb_vendor_orders_endpoint']);
        }
        if (isset($input['wcmb_vendor_withdrawal_endpoint']) && !empty($input['wcmb_vendor_withdrawal_endpoint'])) {
            $new_input['wcmb_vendor_withdrawal_endpoint'] = sanitize_text_field($input['wcmb_vendor_withdrawal_endpoint']);
        }
        if (isset($input['wcmb_transaction_details_endpoint']) && !empty($input['wcmb_transaction_details_endpoint'])) {
            $new_input['wcmb_transaction_details_endpoint'] = sanitize_text_field($input['wcmb_transaction_details_endpoint']);
        }
        if (isset($input['wcmb_vendor_knowledgebase_endpoint']) && !empty($input['wcmb_vendor_knowledgebase_endpoint'])) {
            $new_input['wcmb_vendor_knowledgebase_endpoint'] = sanitize_text_field($input['wcmb_vendor_knowledgebase_endpoint']);
        }
        if (isset($input['wcmb_vendor_tools_endpoint']) && !empty($input['wcmb_vendor_tools_endpoint'])) {
            $new_input['wcmb_vendor_tools_endpoint'] = sanitize_text_field($input['wcmb_vendor_tools_endpoint']);
        }
        
        if (isset($input['wcmb_add_product_endpoint']) && !empty($input['wcmb_add_product_endpoint'])) {
            $new_input['wcmb_add_product_endpoint'] = sanitize_text_field($input['wcmb_add_product_endpoint']);
        }
        if (isset($input['wcmb_edit_product_endpoint']) && !empty($input['wcmb_edit_product_endpoint'])) {
            $new_input['wcmb_edit_product_endpoint'] = sanitize_text_field($input['wcmb_edit_product_endpoint']);
        }
        if (isset($input['wcmb_products_endpoint']) && !empty($input['wcmb_products_endpoint'])) {
            $new_input['wcmb_products_endpoint'] = sanitize_text_field($input['wcmb_products_endpoint']);
        }
        if (isset($input['wcmb_add_coupon_endpoint']) && !empty($input['wcmb_add_coupon_endpoint'])) {
            $new_input['wcmb_add_coupon_endpoint'] = sanitize_text_field($input['wcmb_add_coupon_endpoint']);
        }
        if (isset($input['wcmb_coupons_endpoint']) && !empty($input['wcmb_coupons_endpoint'])) {
            $new_input['wcmb_coupons_endpoint'] = sanitize_text_field($input['wcmb_coupons_endpoint']);
        }
        if (isset($input['wcmb_vendor_products_qnas_endpoint']) && !empty($input['wcmb_vendor_products_qnas_endpoint'])) {
            $new_input['wcmb_vendor_products_qnas_endpoint'] = sanitize_text_field($input['wcmb_vendor_products_qnas_endpoint']);
        }
        if (!$hasError) {
            add_settings_error(
                    "wcmb_{$this->tab}_{$this->subsection}_settings_name", esc_attr("wcmb_{$this->tab}_{$this->subsection}_settings_admin_updated"), __('Vendor Settings Updated', 'MB-multivendor'), 'updated'
            );
        }
        flush_rewrite_rules();
        return apply_filters("settings_{$this->tab}_{$this->subsection}_tab_new_input", $new_input, $input);
    }

}

<?php

class WCMb_Settings_Vendor_Dashboard {

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
        foreach ($pages as $page) {
            if (!in_array($page->ID, $woocommerce_pages)) {
                $pages_array[$page->ID] = $page->post_title;
            }
        }
         //$WCMb->plugin_url.'assets/images/template3.png';
        $template_options = apply_filters('wcmb_vendor_shop_template_options', array('template1' => $WCMb->plugin_url.'assets/images/template1.png', 'template2' => $WCMb->plugin_url.'assets/images/template2.png',
             'template3' => $WCMb->plugin_url.'assets/images/template3.png',
         'template4' => $WCMb->plugin_url.'assets/images/template4.png'));
        $settings_tab_options = array("tab" => "{$this->tab}",
            "ref" => &$this,
            "subsection" => "{$this->subsection}",
            "sections" => array(
                "wcmb_vendor_dashboard_settings" => array("title" => __('Dashboard Settings', 'MB-multivendor'), // Section one
                    "fields" => array(
                       // "wcmb_dashboard_site_logo" => array('title' => __('Site Logo', 'MB-multivendor'), 'type' => 'upload', 'id' => 'wcmb_dashboard_site_logo', 'label_for' => 'wcmb_dashboard_site_logo', 'name' => 'wcmb_dashboard_site_logo', 'hints' => __('Used as site logo on vendor dashboard pages', 'MB-multivendor')),
                        "google_api_key" => array('title' => __('Google Map API key', 'MB-multivendor'), 'type' => 'text', 'id' => 'google_api_key', 'label_for' => 'google_api_key', 'name' => 'google_api_key', 'hints' => __('Used for vendor store maps','MB-multivendor'), 'desc' => __('<a href="https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key" target="_blank">Click here to generate key</a>','MB-multivendor')),

                        "vendor_color_scheme_picker" => array('title' => __('Dashboard Color Scheme', 'MB-multivendor'), 'class' => 'vendor_color_scheme_picker', 'type' => 'color_scheme_picker', 'id' => 'vendor_color_scheme_picker', 'label_for' => 'vendor_color_scheme_picker', 'name' => 'vendor_color_scheme_picker', 'dfvalue' => 'outer_space_blue', 'options' => array('outer_space_blue' => array('label' => __('Outer Space', 'MB-multivendor'), 'color' => array('#202528', '#333b3d','#3f85b9', '#316fa8')), 'green_lagoon' => array('label' => __('Green Lagoon', 'MB-multivendor'), 'color' => array('#171717', '#212121', '#009788','#00796a')), 'old_west' => array('label' => __('Old West', 'MB-multivendor'), 'color' => array('#46403c', '#59524c', '#c7a589', '#ad8162')), 'wild_watermelon' => array('label' => __('Wild Watermelon', 'MB-multivendor'), 'color' => array('#181617', '#353130', '#fd5668', '#fb3f4e'))))
                    ),
                ),
                'wcmb_vendor_shop_template' => array(
                    'title' => __('Vendor Shop', 'MB-multivendor'),
                    'fields' => array(
                        "wcmb_vendor_shop_template" => array('title' => __('Vendor Shop Template', 'MB-multivendor'),'label' => __('you need to choose a template for a shop (the default template is a 1)','MB-multivendor'), 'type' => 'select', 'id' => 'wcmb_vendor_shop_template', 'label_for' => 'wcmb_vendor_shop_template', 'name' => 'wcmb_vendor_shop_template', 'dfvalue' => 'vendor', 'options' => $template_options, 'value' => 'template1', 'desc' => ''),


                         // Radio
                        'wcmb_vendor_dashboard_custom_css' => array('title' => __('Custom CSS', 'MB-multivendor'), 'type' => 'textarea', 'name' => 'wcmb_vendor_dashboard_custom_css', 'id' => 'wcmb_vendor_dashboard_custom_css', 'label_for' => 'wcmb_vendor_dashboard_custom_css', 'rows' => 4, 'cols' => 40, 'raw_value' => true, 'hints' => __('Will be applicable on vendor frontend', 'MB-multivendor'))
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
    public function wcmb_vendor_dashboard_settings_sanitize($input) {
        $new_input = array();
        $hasError = false;
        
        if (isset($input['wcmb_dashboard_site_logo'])) {
            $new_input['wcmb_dashboard_site_logo'] = $input['wcmb_dashboard_site_logo'];
        }
        if(isset($input['google_api_key'])){
            $new_input['google_api_key'] = $input['google_api_key'];
        }
        if(isset($input['vendor_color_scheme_picker'])){
            $new_input['vendor_color_scheme_picker'] = sanitize_text_field($input['vendor_color_scheme_picker']);
        }
        if(isset($input['wcmb_vendor_shop_template'])){
            $new_input['wcmb_vendor_shop_template'] = sanitize_text_field($input['wcmb_vendor_shop_template']);
        }
        if(isset($input['can_vendor_edit_shop_template'])){
            $new_input['can_vendor_edit_shop_template'] = sanitize_text_field($input['can_vendor_edit_shop_template']);
        }
        
        if(isset($input['wcmb_vendor_dashboard_custom_css'])){
            $new_input['wcmb_vendor_dashboard_custom_css'] = wp_unslash($input['wcmb_vendor_dashboard_custom_css']);
        }
        
        if (!$hasError) {
            add_settings_error(
                    "wcmb_{$this->tab}_{$this->subsection}_settings_name", esc_attr("wcmb_{$this->tab}_{$this->subsection}_settings_admin_updated"), __('Vendor Settings Updated', 'MB-multivendor'), 'updated'
            );
        }
        return apply_filters("settings_{$this->tab}_{$this->subsection}_tab_new_input", $new_input, $input);
    }

}

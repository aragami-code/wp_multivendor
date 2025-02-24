<?php

class WCMb_Settings_General_Customer_support_Details {

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

        $settings_tab_options = array("tab" => "{$this->tab}",
            "ref" => &$this,
            "subsection" => "{$this->subsection}",
            "sections" => array(
                "vendor_return_address" => array("title" => __('Return Address & Customer Support Details ', 'MB-multivendor'), // Section one
                    "fields" => array(
                        "csd_email" => array('title' => __('Email', 'MB-multivendor'), 'type' => 'text', 'id' => 'csd_email', 'label_for' => 'csd_email', 'name' => 'csd_email'), // text 
                        "csd_phone" => array('title' => __('Phone Number', 'MB-multivendor'), 'type' => 'text', 'id' => 'csd_phone', 'label_for' => 'csd_phone', 'name' => 'csd_phone'), // text 
                        "csd_return_address_1" => array('title' => __('Address Line 1', 'MB-multivendor'), 'type' => 'text', 'id' => 'csd_return_address_1', 'label_for' => 'csd_return_address_1', 'name' => 'csd_return_address_1'), // text 
                        "csd_return_address_2" => array('title' => __('Address Line 2', 'MB-multivendor'), 'type' => 'text', 'id' => 'csd_return_address_2', 'label_for' => 'csd_return_address_2', 'name' => 'csd_return_address_2'), // text 
                        "csd_return_state" => array('title' => __('State', 'MB-multivendor'), 'type' => 'text', 'id' => 'csd_return_state', 'label_for' => 'csd_return_state', 'name' => 'csd_return_state'), // text 
                        "csd_return_city" => array('title' => __('City', 'MB-multivendor'), 'type' => 'text', 'id' => 'csd_return_city', 'label_for' => 'csd_return_city', 'name' => 'csd_return_city'), // text 
                        "csd_return_country" => array('title' => __('Country', 'MB-multivendor'), 'type' => 'text', 'id' => 'csd_return_country', 'label_for' => 'csd_return_country', 'name' => 'csd_return_country'), // text 
                        "csd_return_zipcode" => array('title' => __('Zip Code', 'MB-multivendor'), 'type' => 'text', 'id' => 'csd_return_zipcode', 'label_for' => 'csd_return_zipcode', 'name' => 'csd_return_zipcode'), // text 
                        
                    )
                ),
//                "vendor_customer_support" => array(
//                    "title" => __('Vendor customer support', 'MB-multivendor'),
//                    'fields' => array(
//                        "can_vendor_add_customer_support_details" => array('title' => __('Vendor Shop Support', 'MB-multivendor'), 'type' => 'checkbox', 'id' => 'can_vendor_add_customer_support_details', 'label_for' => 'can_vendor_add_customer_support_details', 'name' => 'can_vendor_add_customer_support_details', 'value' => 'Enable', 'text' => __('Allow vendors to add vendor shop specific customer support details. If left blank by the vendor, the site wide customer support details would be on display.', 'MB-multivendor')), // Checkbox
//                    )
//                )
            ),
        );

        $WCMb->admin->settings->settings_field_withsubtab_init(apply_filters("settings_{$this->tab}_{$this->subsection}_tab_options", $settings_tab_options));
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function wcmb_general_customer_support_details_settings_sanitize($input) {
        global $WCMb;
        $new_input = array();

        $hasError = false;

//        if (isset($input['can_vendor_add_customer_support_details']))
//            $new_input['can_vendor_add_customer_support_details'] = sanitize_text_field($input['can_vendor_add_customer_support_details']);

        if (isset($input['csd_email']))
            $new_input['csd_email'] = sanitize_text_field($input['csd_email']);

        if (isset($input['csd_phone']))
            $new_input['csd_phone'] = sanitize_text_field($input['csd_phone']);

        if (isset($input['csd_return_address_1']))
            $new_input['csd_return_address_1'] = sanitize_text_field($input['csd_return_address_1']);

        if (isset($input['csd_return_address_2']))
            $new_input['csd_return_address_2'] = sanitize_text_field($input['csd_return_address_2']);

        if (isset($input['csd_return_state']))
            $new_input['csd_return_state'] = sanitize_text_field($input['csd_return_state']);

        if (isset($input['csd_return_city']))
            $new_input['csd_return_city'] = sanitize_text_field($input['csd_return_city']);

        if (isset($input['csd_return_country']))
            $new_input['csd_return_country'] = sanitize_text_field($input['csd_return_country']);

        if (isset($input['csd_return_zipcode']))
            $new_input['csd_return_zipcode'] = sanitize_text_field($input['csd_return_zipcode']);

        if (!$hasError) {
            add_settings_error(
                    "wcmb_{$this->tab}_{$this->subsection}_settings_name", esc_attr("wcmb_{$this->tab}_{$this->subsection}_settings_admin_updated"), __('Customer Support Details Settings Updated', 'MB-multivendor'), 'updated'
            );
        }
        return apply_filters("settings_{$this->tab}_{$this->subsection}_tab_new_input", $new_input, $input);
    }

    /**
     * Print the Section text
     */
    public function vendor_return_address_info() {
        echo '<div class="wcmb-section-info">';
        _e('Enter the storewide customer support details here.', 'MB-multivendor');
        echo '</div>';
    }

}

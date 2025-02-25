<?php

class WCMb_Settings_Capabilities_Miscellaneous {

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
                "vendor_messages" => array("title" => __('', 'MB-multivendor'), // Section one
                    "fields" => array(
                        "can_vendor_add_message_on_email_and_thankyou_page" => array('title' => __('Message to buyer', 'MB-multivendor'), 'type' => 'checkbox', 'id' => 'can_vendor_add_message_on_email_and_thankyou_page', 'label_for' => 'can_vendor_add_message_on_email_and_thankyou_page', 'name' => 'can_vendor_add_message_on_email_and_thankyou_page', 'value' => 'Enable', 'text' => __('Allow vendors to add vendor shop specific message in "Thank you" page and order mail.', 'MB-multivendor')), // Checkbox
                        "is_vendor_add_external_url" => array('title' => __('Enable store url', 'MB-multivendor'), 'type' => 'checkbox', 'id' => 'is_vendor_add_external_url', 'label_for' => 'is_vendor_add_external_url', 'name' => 'is_vendor_add_external_url', 'text' => __('Vendor can add external store url.', 'MB-multivendor'), 'value' => 'Enable'), // Checkbox
                        "is_hide_option_show" => array('title' => __('Enable hide option for vendor', 'MB-multivendor'), 'type' => 'checkbox', 'id' => 'is_hide_option_show', 'label_for' => 'is_hide_option_show', 'name' => 'is_hide_option_show', 'text' => __('Vendor can hide some details from shop.', 'MB-multivendor'), 'value' => 'Enable'), // Checkbox
                    )
                )
            )
        );

        $WCMb->admin->settings->settings_field_withsubtab_init(apply_filters("settings_{$this->tab}_{$this->subsection}_tab_options", $settings_tab_options));
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function wcmb_capabilities_miscellaneous_settings_sanitize($input) {
        global $WCMb;
        $new_input = array();

        $hasError = false;

        if (isset($input['can_vendor_add_message_on_email_and_thankyou_page'])) {
            $new_input['can_vendor_add_message_on_email_and_thankyou_page'] = sanitize_text_field($input['can_vendor_add_message_on_email_and_thankyou_page']);
        }

        if (isset($input['is_vendor_add_external_url'])) {
            $new_input['is_vendor_add_external_url'] = sanitize_text_field($input['is_vendor_add_external_url']);
        }

        if (isset($input['is_hide_option_show'])) {
            $new_input['is_hide_option_show'] = sanitize_text_field($input['is_hide_option_show']);
        }


        if (!$hasError) {
            add_settings_error(
                    "wcmb_{$this->tab}_{$this->subsection}_settings_name", esc_attr("wcmb_{$this->tab}_{$this->subsection}_settings_admin_updated"), __('Vendor Settings Updated', 'MB-multivendor'), 'updated'
            );
        }
        return apply_filters("settings_{$this->tab}_{$this->subsection}_tab_new_input", $new_input, $input);
    }

}

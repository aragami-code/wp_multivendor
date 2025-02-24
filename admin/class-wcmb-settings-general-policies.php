<?php

class WCMb_Settings_General_Policies {

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
                "wcmb_general_policies_settings_section" => array(
                    "title" => __('Policy Settings', 'MB-multivendor'),
                    "fields" => array(
//                        "policy_tab_title" => array('title' => __('Policy Tab Title', 'MB-multivendor'), 'type' => 'text', 'id' => 'policy_tab_title', 'label_for' => 'policy_tab_title', 'name' => 'policy_tab_title'), // text
//                        "can_vendor_edit_policy_tab_label" => array('title' => __('Vendor Can Edit', 'MB-multivendor'), 'type' => 'checkbox', 'id' => 'can_vendor_edit_policy_tab_label', 'label_for' => 'can_vendor_edit_policy_tab_label', 'name' => 'can_vendor_edit_policy_tab_label', 'value' => 'Enable', 'text' => __('Allow vendors to edit the policy tab label.', 'MB-multivendor')), // Checkbox
                        "shipping_policy" => array('title' => __('Shipping Policy Content', 'MB-multivendor'), 'type' => 'wpeditor', 'id' => 'shipping_policy', 'label_for' => 'shipping_policy', 'name' => 'shipping_policy', 'cols' => 50, 'rows' => 6), // Textarea
                        "refund_policy" => array('title' => __('Refund Policy Content', 'MB-multivendor'), 'type' => 'wpeditor', 'id' => 'refund_policy', 'label_for' => 'refund_policy', 'name' => 'refund_policy', 'cols' => 50, 'rows' => 6), // Textarea
                        "cancellation_policy" => array('title' => __('Cancellation / Return / Exchange Policy Content', 'MB-multivendor'), 'type' => 'wpeditor', 'id' => 'cancellation_policy', 'label_for' => 'cancellation_policy', 'name' => 'cancellation_policy', 'cols' => 50, 'rows' => 6), // Textarea
                    )
                ),
//                "wcmb_shipping_policies_settings_section" => array(
//                    "title" => __('Shipping Policy', 'MB-multivendor'),
//                    "fields" => array(
//                        "is_shipping_on" => array('title' => __('Enable Shipping Policy', 'MB-multivendor'), 'type' => 'checkbox', 'id' => 'is_shipping_on', 'label_for' => 'is_shipping_on', 'name' => 'is_shipping_on', 'value' => 'Enable'), // Checkbox
//                        "is_shipping_product_level_on" => array('title' => __('Product Wise', 'MB-multivendor'), 'type' => 'checkbox', 'id' => 'is_shipping_product_level_on', 'label_for' => 'is_shipping_product_level_on', 'name' => 'is_shipping_product_level_on', 'value' => 'Enable'), // Checkbox
//                        "can_vendor_edit_shipping_policy" => array('title' => __('Vendor Can Edit', 'MB-multivendor'), 'type' => 'checkbox', 'id' => 'can_vendor_edit_shipping_policy', 'label_for' => 'can_vendor_edit_shipping_policy', 'name' => 'can_vendor_edit_shipping_policy', 'value' => 'Enable', 'text' => __('Allow vendors to edit the shipping policy.', 'MB-multivendor')), // Checkbox
//                        "shipping_policy_label" => array('title' => __('Label', 'MB-multivendor'), 'type' => 'text', 'id' => 'shipping_policy_label', 'label_for' => 'shipping_policy_label', 'name' => 'shipping_policy_label'), // text
//                        "shipping_policy" => array('title' => __('Policy Content', 'MB-multivendor'), 'type' => 'wpeditor', 'id' => 'shipping_policy', 'label_for' => 'shipping_policy', 'name' => 'shipping_policy', 'cols' => 50, 'rows' => 6), // Textarea
//                    )
//                ),
//                "wcmb_refund_policies_settings_section" => array(
//                    "title" => __('Refund Policy', 'MB-multivendor'),
//                    "fields" => array(
//                        "is_refund_on" => array('title' => __('Enable Refund Policy', 'MB-multivendor'), 'type' => 'checkbox', 'id' => 'is_refund_on', 'label_for' => 'is_refund_on', 'name' => 'is_refund_on', 'value' => 'Enable'), // Checkbox
//                        "is_refund_product_level_on" => array('title' => __('Product Wise', 'MB-multivendor'), 'type' => 'checkbox', 'id' => 'is_refund_product_level_on', 'label_for' => 'is_refund_product_level_on', 'name' => 'is_refund_product_level_on', 'value' => 'Enable'), // Checkbox
//                        "can_vendor_edit_refund_policy" => array('title' => __('Vendor Can Edit', 'MB-multivendor'), 'type' => 'checkbox', 'id' => 'can_vendor_edit_refund_policy', 'label_for' => 'can_vendor_edit_refund_policy', 'name' => 'can_vendor_edit_refund_policy', 'value' => 'Enable', 'text' => __('Allow vendors to edit the refund policy.', 'MB-multivendor')), // Checkbox
//                        "refund_policy_label" => array('title' => __('Label', 'MB-multivendor'), 'type' => 'text', 'id' => 'refund_policy_label', 'label_for' => 'refund_policy_label', 'name' => 'refund_policy_label'), // text
//                        "refund_policy" => array('title' => __('Policy Content', 'MB-multivendor'), 'type' => 'wpeditor', 'id' => 'refund_policy', 'label_for' => 'refund_policy', 'name' => 'refund_policy', 'cols' => 50, 'rows' => 6), // Textarea
//                    )
//                ),
//                "wcmb_store_policies_settings_section" => array("title" => __('Cancellation / Return / Exchange Policy', 'MB-multivendor'), // Section one
//                    "fields" => array(
//                        "is_cancellation_on" => array('title' => __('Enable Cancellation / Return / Exchange Policy', 'MB-multivendor'), 'type' => 'checkbox', 'id' => 'is_cancellation_on', 'label_for' => 'is_cancellation_on', 'name' => 'is_cancellation_on', 'value' => 'Enable'), // Checkbox
//                        "is_cancellation_product_level_on" => array('title' => __('Product Wise', 'MB-multivendor'), 'type' => 'checkbox', 'id' => 'is_cancellation_product_level_on', 'label_for' => 'is_cancellation_product_level_on', 'name' => 'is_cancellation_product_level_on', 'value' => 'Enable'), // Checkbox
//                        "can_vendor_edit_cancellation_policy" => array('title' => __('Vendor Can Edit', 'MB-multivendor'), 'type' => 'checkbox', 'id' => 'can_vendor_edit_cancellation_policy', 'label_for' => 'can_vendor_edit_cancellation_policy', 'name' => 'can_vendor_edit_cancellation_policy', 'value' => 'Enable', 'text' => __('Allow vendors to edit the cancellation / return / exchange policy.', 'MB-multivendor')), // Checkbox
//                        "cancellation_policy_label" => array('title' => __('Label', 'MB-multivendor'), 'type' => 'text', 'id' => 'cancellation_policy_label', 'label_for' => 'cancellation_policy_label', 'name' => 'cancellation_policy_label', 'cols' => 50, 'rows' => 6), // text
//                        "cancellation_policy" => array('title' => __('Policy Content', 'MB-multivendor'), 'type' => 'wpeditor', 'id' => 'cancellation_policy', 'label_for' => 'cancellation_policy', 'name' => 'cancellation_policy', 'cols' => 50, 'rows' => 6), // Textarea
//                    ),
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
    public function wcmb_general_policies_settings_sanitize($input) {
        global $WCMb;
        $new_input = array();

        $hasError = false;
//        if (isset($input['is_cancellation_on']))
//            $new_input['is_cancellation_on'] = sanitize_text_field($input['is_cancellation_on']);
//        if (isset($input['is_cancellation_product_level_on']))
//            $new_input['is_cancellation_product_level_on'] = sanitize_text_field($input['is_cancellation_product_level_on']);
//        if (isset($input['can_vendor_edit_cancellation_policy']))
//            $new_input['can_vendor_edit_cancellation_policy'] = sanitize_text_field($input['can_vendor_edit_cancellation_policy']);
//        if (isset($input['is_refund_on']))
//            $new_input['is_refund_on'] = sanitize_text_field($input['is_refund_on']);
//        if (isset($input['is_refund_product_level_on']))
//            $new_input['is_refund_product_level_on'] = sanitize_text_field($input['is_refund_product_level_on']);
//        if (isset($input['can_vendor_edit_refund_policy']))
//            $new_input['can_vendor_edit_refund_policy'] = sanitize_text_field($input['can_vendor_edit_refund_policy']);
//        if (isset($input['is_shipping_on']))
//            $new_input['is_shipping_on'] = sanitize_text_field($input['is_shipping_on']);
//        if (isset($input['is_shipping_product_level_on']))
//            $new_input['is_shipping_product_level_on'] = sanitize_text_field($input['is_shipping_product_level_on']);
//        if (isset($input['can_vendor_edit_shipping_policy']))
//            $new_input['can_vendor_edit_shipping_policy'] = sanitize_text_field($input['can_vendor_edit_shipping_policy']);
//        if (isset($input['can_vendor_edit_policy_tab_label']))
//            $new_input['can_vendor_edit_policy_tab_label'] = sanitize_text_field($input['can_vendor_edit_policy_tab_label']);
        if (isset($input['cancellation_policy'])) {
            $new_input['cancellation_policy'] = $input['cancellation_policy'];
        }
        if (isset($input['refund_policy'])) {
            $new_input['refund_policy'] = $input['refund_policy'];
        }
        if (isset($input['shipping_policy'])) {
            $new_input['shipping_policy'] = $input['shipping_policy'];
        }
//        if (isset($input['cancellation_policy_label']))
//            $new_input['cancellation_policy_label'] = $input['cancellation_policy_label'];
//        if (isset($input['refund_policy_label']))
//            $new_input['refund_policy_label'] = $input['refund_policy_label'];
//        if (isset($input['shipping_policy_label']))
//            $new_input['shipping_policy_label'] = $input['shipping_policy_label'];
//        if (isset($input['policy_tab_title']))
//            $new_input['policy_tab_title'] = sanitize_text_field($input['policy_tab_title']);



        if (!$hasError) {
            add_settings_error(
                    "wcmb_{$this->tab}_{$this->subsection}_settings_name", esc_attr("wcmb_{$this->tab}_{$this->subsection}_settings_admin_updated"), __('Policies Settings Updated', 'MB-multivendor'), 'updated'
            );
        }
        return apply_filters("settings_{$this->tab}_{$this->subsection}_tab_new_input", $new_input, $input);
    }

}

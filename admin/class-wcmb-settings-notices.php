<?php

class WCMb_Settings_Notices {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;

    /**
     * Start up
     */
    public function __construct($tab) {
        $this->tab = $tab;
        $this->options = get_option("wcmb_{$this->tab}_settings_name");
        $this->settings_page_init();
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMb;
        $settings_tab_options = array("tab" => "{$this->tab}",
            "ref" => &$this,
            "sections" => array(
                "default_settings_section" => array("title" => '', // Section one
                    "fields" => array(
                        "is_notices_on" => array('title' => __('Notices Enable/Disable :', 'MB-multivendor'), 'type' => 'checkbox', 'id' => 'is_notices_on', 'label_for' => 'is_notices_on', 'name' => 'is_notices_on', 'value' => 'Enable') // Checkbox
                    ),
                )
            )
        );

        $WCMb->admin->settings->settings_field_init(apply_filters("settings_{$this->tab}_tab_options", $settings_tab_options));
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function wcmb_notices_settings_sanitize($input) {
        global $WCMb;
        $new_input = array();
        $hasError = false;

        if (isset($input['is_notices_on']))
            $new_input['is_notices_on'] = sanitize_text_field($input['is_notices_on']);

        if (isset($input['notices']))
            $new_input['notices'] = $input['notices'];

        if (!$hasError) {
            add_settings_error(
                    "wcmb_{$this->tab}_settings_name", esc_attr("wcmb_{$this->tab}_settings_admin_updated"), __('Page Settings Updated', 'MB-multivendor'), 'updated'
            );
        }
        return apply_filters("settings_{$this->tab}_tab_new_input", $new_input, $input);
    }
  
}

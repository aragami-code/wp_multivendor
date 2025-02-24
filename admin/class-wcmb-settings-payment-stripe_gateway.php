<?php

class WCMb_Settings_Payment_Stripe_Connect {

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
                "default_settings_section" => array("title" => __('', 'MB-multivendor'), // Section one
                    "fields" => array(
                        "testmode" => array('title' => __(' Enable test mode', 'MB-multivendor'), 'type' => 'checkbox', 'id' => 'testmode', 'label_for' => 'testmode', 'name' => 'testmode', 'value' => 'Enable'), // Text   
                        "config_redirect_uri" => array('title' => __('Config redirect URI', 'MB-multivendor'), 'type' => 'label', 'id' => 'config_redirect_uri', 'label_for' => 'config_redirect_uri', 'name' => 'config_redirect_uri', 'value' => admin_url('admin-ajax.php') . "?action=marketplace_stripe_authorize", 'desc' => '<a href="https://dashboard.stripe.com/account/applications/settings" target="_blank">'.__('Copy the URI and configured stripe redirect URI with above.', 'MB-multivendor').'</a>'),
                    ),
                ),
                "client_settings_section" => array("title" => __('Client data', 'MB-multivendor'), // Section one
                    "fields" => array(
                        "test_client_id" => array('title' => __('Test client id', 'MB-multivendor'), 'type' => 'password', 'id' => 'test_client_id', 'label_for' => 'test_client_id', 'name' => 'test_client_id', 'hints' => __('Get your development Client id from your stripe account', 'MB-multivendor'), 'placeholder' => __('Development client id', 'MB-multivendor')),
                        "live_client_id" => array('title' => __('Live client id', 'MB-multivendor'), 'type' => 'password', 'id' => 'live_client_id', 'label_for' => 'live_client_id', 'name' => 'live_client_id', 'hints' => __('Get your production Client id from your stripe account', 'MB-multivendor'), 'placeholder' => __('Production client id', 'MB-multivendor')),
                    ),
                ),
                "api_settings_section" => array("title" => __('API keys', 'MB-multivendor'), // Section one
                    "fields" => array(
                        "test_publishable_key" => array('title' => __('Test Publishable key', 'MB-multivendor'), 'type' => 'password', 'id' => 'test_publishable_key', 'label_for' => 'test_publishable_key', 'name' => 'test_publishable_key', 'hints' => __('Publishable Key, you will get this credential from stripe account apikeys.', 'MB-multivendor')), // Text
                        "test_secret_key" => array('title' => __('Test Secret key', 'MB-multivendor'), 'type' => 'password', 'id' => 'test_secret_key', 'label_for' => 'test_secret_key', 'name' => 'test_secret_key', 'hints' => __('Secret key, you will get this credential from stripe account apikeys.', 'MB-multivendor')), // Text
                        "live_publishable_key" => array('title' => __('Live Publishable key', 'MB-multivendor'), 'type' => 'password', 'id' => 'live_publishable_key', 'label_for' => 'live_publishable_key', 'name' => 'live_publishable_key', 'hints' => __('Publishable Key, you will get this credential from stripe account apikeys.', 'MB-multivendor')), // Text
                        "live_secret_key" => array('title' => __('Live Secret key', 'MB-multivendor'), 'type' => 'password', 'id' => 'live_secret_key', 'label_for' => 'live_secret_key', 'name' => 'live_secret_key', 'hints' => __('Secret key, you will get this credential from stripe account apikeys.', 'MB-multivendor')), // Text
                    ),
                ),
            )
        );

        $WCMb->admin->settings->settings_field_withsubtab_init(apply_filters("settings_{$this->tab}_{$this->subsection}_tab_options", $settings_tab_options));
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function wcmb_payment_stripe_gateway_settings_sanitize($input) {
        $new_input = array();

        $hasError = false;
        if(isset($input['testmode'])){
            $new_input['testmode'] = sanitize_text_field($input['testmode']);
        }
        
        if(isset($input['test_client_id'])){
            $new_input['test_client_id'] = sanitize_text_field($input['test_client_id']);
        }
        
        if(isset($input['live_client_id'])){
            $new_input['live_client_id'] = sanitize_text_field($input['live_client_id']);
        }
        
        if(isset($input['test_publishable_key'])){
            $new_input['test_publishable_key'] = sanitize_text_field($input['test_publishable_key']);
        }
        
        if(isset($input['test_secret_key'])){
            $new_input['test_secret_key'] = sanitize_text_field($input['test_secret_key']);
        }
        
        if(isset($input['live_publishable_key'])){
            $new_input['live_publishable_key'] = sanitize_text_field($input['live_publishable_key']);
        }
        
        if(isset($input['live_secret_key'])){
            $new_input['live_secret_key'] = sanitize_text_field($input['live_secret_key']);
        }

        if (!$hasError) {
            add_settings_error(
                    "wcmb_{$this->tab}_{$this->subsection}_settings_name", esc_attr("wcmb_{$this->tab}_{$this->subsection}_settings_admin_updated"), __('Stripe Gateway Settings Updated', 'MB-multivendor'), 'updated'
            );
        }
        return apply_filters("settings_{$this->tab}_{$this->subsection}_tab_new_input", $new_input, $input);
    }

    /**
     * Print the Section text
     */
    public function default_settings_section_info() {
    }

    /**
     * Print the Section text
     */
    public function WCMb_Stripe_Gateway_store_policies_admin_details_section_info() {
    }

}

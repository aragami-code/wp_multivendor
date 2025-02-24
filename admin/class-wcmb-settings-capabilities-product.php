<?php

class WCMb_Settings_Capabilities_Product {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;
    private $subsection;

    /**
     * Start up
     */
    public function __construct( $tab, $subsection ) {
        $this->tab = $tab;
        $this->subsection = $subsection;
        $this->options = get_option( "wcmb_{$this->tab}_{$this->subsection}_settings_name" );
        $this->settings_page_init();
        $this->get_product_type_selector();
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMb;
        $settings_tab_options = array( "tab"        => "{$this->tab}",
            "ref"        => &$this,
            "subsection" => "{$this->subsection}",
            "sections"   => array(
                "products_capability"                  => array(
                    "title"  => __( 'Products Capability', 'MB-multivendor' ),
                    "fields" => array(
                        "is_submit_product"                => array( 'title' => __( 'Submit Products', 'MB-multivendor' ), 'type' => 'checkbox', 'id' => 'is_submit_product', 'label_for' => 'is_submit_product', 'text' => __( 'Allow vendors to submit products for approval/publishing.', 'MB-multivendor' ), 'name' => 'is_submit_product', 'value' => 'Enable' ), // Checkbox
                        "is_published_product"             => array( 'title' => __( 'Publish Products', 'MB-multivendor' ), 'type' => 'checkbox', 'id' => 'is_published_product', 'label_for' => 'is_published_product', 'name' => 'is_published_product', 'text' => __( 'If checked, products uploaded by vendors will be directly published without admin approval.', 'MB-multivendor' ), 'value' => 'Enable' ), // Checkbox
                        "is_edit_delete_published_product" => array( 'title' => __( 'Edit Published Products', 'MB-multivendor' ), 'type' => 'checkbox', 'id' => 'is_edit_delete_published_product', 'label_for' => 'is_edit_delete_published_product', 'name' => 'is_edit_delete_published_product', 'text' => __( 'Allow vendors to edit published products.', 'MB-multivendor' ), 'value' => 'Enable' ), // Checkbox
                        "is_submit_coupon"                 => array( 'title' => __( 'Submit Coupons', 'MB-multivendor' ), 'type' => 'checkbox', 'id' => 'is_submit_coupon', 'label_for' => 'is_submit_coupon', 'name' => 'is_submit_coupon', 'text' => __( 'Allow vendors to create coupons.', 'MB-multivendor' ), 'value' => 'Enable' ), // Checkbox
                        "is_published_coupon"              => array( 'title' => __( 'Publish Coupons', 'MB-multivendor' ), 'type' => 'checkbox', 'id' => 'is_published_coupon', 'label_for' => 'is_published_coupon', 'name' => 'is_published_coupon', 'text' => __( 'If checked, coupons added by vendors will be directly published without admin approval.', 'MB-multivendor' ), 'value' => 'Enable' ), // Checkbox
                        "is_edit_delete_published_coupon"  => array( 'title' => __( 'Edit Published Coupons', 'MB-multivendor' ), 'type' => 'checkbox', 'id' => 'is_edit_delete_published_coupon', 'label_for' => 'is_edit_delete_published_coupon', 'name' => 'is_edit_delete_published_coupon', 'text' => __( 'Allow vendor to edit/delete published shop coupons.', 'MB-multivendor' ), 'value' => 'Enable' ), // Checkbox
                        "is_upload_files"                  => array( 'title' => __( 'Upload Media Files', 'MB-multivendor' ), 'type' => 'checkbox', 'id' => 'is_upload_files', 'label_for' => 'is_upload_files', 'name' => 'is_upload_files', 'text' => __( 'Allow vendors to upload media files.', 'MB-multivendor' ), 'value' => 'Enable' ), // Checkbox
                    )
                ),
                "default_settings_section_types"       => array( "title"  => __( 'Product Types ', 'MB-multivendor' ), // Section one
                    "fields" => apply_filters( "wcmb_vendor_product_types", array(
                        "simple"   => array( 'title' => __( 'Simple', 'MB-multivendor' ), 'type' => 'checkbox', 'id' => 'simple', 'label_for' => 'simple', 'name' => 'simple', 'value' => 'Enable', 'text' => __( 'Both frontend and back-end', 'MB-multivendor' ) ), // Checkbox
                        "variable" => array( 'title' => __( 'Variable', 'MB-multivendor' ), 'type' => 'checkbox', 'id' => 'variable', 'label_for' => 'variable', 'name' => 'variable', 'value' => 'Enable', 'text' => __( 'Back-end only', 'MB-multivendor' ) ), // Checkbox
                        "grouped"  => array( 'title' => __( 'Grouped', 'MB-multivendor' ), 'type' => 'checkbox', 'id' => 'grouped', 'label_for' => 'grouped', 'name' => 'grouped', 'value' => 'Enable', 'text' => __( 'Back-end only', 'MB-multivendor' ) ), // Checkbox
                        "external" => array( 'title' => __( 'External / Affiliate', 'MB-multivendor' ), 'type' => 'checkbox', 'id' => 'external', 'label_for' => 'external', 'name' => 'external', 'value' => 'Enable', 'text' => __( 'Back-end only', 'MB-multivendor' ) ), // Checkbox
                        )
                    )
                ),
                "default_settings_section_type_option" => array( "title"  => __( 'Type Options ', 'MB-multivendor' ), // Section one
                    "fields" => apply_filters( "wcmb_vendor_product_type_options", array(
                        "virtual"      => array( 'title' => __( 'Virtual', 'MB-multivendor' ), 'type' => 'checkbox', 'id' => 'virtual', 'label_for' => 'virtual', 'name' => 'virtual', 'value' => 'Enable' ), // Checkbox
                        "downloadable" => array( 'title' => __( 'Downloadable', 'MB-multivendor' ), 'type' => 'checkbox', 'id' => 'downloadable', 'label_for' => 'downloadable', 'name' => 'downloadable', 'value' => 'Enable' ), // Checkbox
                        )
                    )
                )
            )
        );

        $WCMb->admin->settings->settings_field_withsubtab_init( apply_filters( "settings_{$this->tab}_{$this->subsection}_tab_options", $settings_tab_options ) );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function wcmb_capabilities_product_settings_sanitize( $input ) {
        $new_input = array();

        $hasError = false;

        if ( isset( $input['is_upload_files'] ) ) {
            $new_input['is_upload_files'] = sanitize_text_field( $input['is_upload_files'] );
        }

        if ( isset( $input['is_published_product'] ) ) {
            $new_input['is_published_product'] = sanitize_text_field( $input['is_published_product'] );
        }

        if ( isset( $input['is_edit_delete_published_product'] ) ) {
            $new_input['is_edit_delete_published_product'] = $input['is_edit_delete_published_product'];
        }

        if ( isset( $input['is_submit_product'] ) ) {
            $new_input['is_submit_product'] = sanitize_text_field( $input['is_submit_product'] );
        }

        if ( isset( $input['is_published_coupon'] ) ) {
            $new_input['is_published_coupon'] = sanitize_text_field( $input['is_published_coupon'] );
        }

        if ( isset( $input['is_submit_coupon'] ) ) {
            $new_input['is_submit_coupon'] = sanitize_text_field( $input['is_submit_coupon'] );
        }

        if ( isset( $input['is_edit_delete_published_coupon'] ) ) {
            $new_input['is_edit_delete_published_coupon'] = $input['is_edit_delete_published_coupon'];
        }
        if ( isset( $input['simple'] ) ) {
            $new_input['simple'] = sanitize_text_field( $input['simple'] );
        }
        if ( isset( $input['variable'] ) ) {
            $new_input['variable'] = sanitize_text_field( $input['variable'] );
        }
        if ( isset( $input['grouped'] ) ) {
            $new_input['grouped'] = sanitize_text_field( $input['grouped'] );
        }
        if ( isset( $input['external'] ) ) {
            $new_input['external'] = sanitize_text_field( $input['external'] );
        }
        if ( isset( $input['virtual'] ) ) {
            $new_input['virtual'] = sanitize_text_field( $input['virtual'] );
        }
        if ( isset( $input['downloadable'] ) ) {
            $new_input['downloadable'] = sanitize_text_field( $input['downloadable'] );
        }
        if ( ! $hasError ) {
            add_settings_error(
                "wcmb_{$this->tab}_{$this->subsection}_settings_name", esc_attr( "wcmb_{$this->tab}_{$this->subsection}_settings_admin_updated" ), __( 'Vendor Settings Updated', 'MB-multivendor' ), 'updated'
            );
        }
        return apply_filters( "settings_{$this->tab}_{$this->subsection}_tab_new_input", $new_input, $input );
    }

    public function get_product_type_selector() {
        wc_get_product_types();
        $product_types = array();
        foreach ( wc_get_product_types() as $type => $name ) {
            $product_types[$type] = array( 'title' => $name, 'type' => 'checkbox', 'id' => $type, 'label_for' => $type, 'name' => $type, 'value' => 'Enable' );
        }
        return apply_filters( 'wcmb_vendor_product_types', $product_types );
    }

    public function default_settings_section_types_info() {
        if ( ! class_exists( 'WCMb_Frontend_Product_Manager' ) || ! class_exists( 'WCMb_AFM' ) ) {
            
            ?>
            <style type="text/css">
                .frontend_manager_promo {
                    display: inline-block;
                    padding: 10px;
                    background: #ffffff;
                    color: #333;
                    font-style: italic;
                    max-width: 300px;
                    position: absolute;
                    right: 20px;
                    z-index: 9;
                }
                @media (max-width: 960px){
                    .frontend_manager_promo {
                        position: relative;
                        right: auto;
                    }
                }
            </style>
            <?php

        }
    }

}

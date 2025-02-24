<?php
/*
 
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $WCMb;
$vendor = get_current_vendor();
if (!$vendor) {
    return;
}
$vendor_hide_description = get_user_meta($vendor->id, '_vendor_hide_description', true);
$vendor_hide_email = get_user_meta($vendor->id, '_vendor_hide_email', true);
$vendor_hide_address = get_user_meta($vendor->id, '_vendor_hide_address', true);
$vendor_hide_phone = get_user_meta($vendor->id, '_vendor_hide_phone', true);

$field_type = (apply_filters('wcmb_vendor_storefront_wpeditor_enabled', true, $vendor->id)) ? 'wpeditor' : 'textarea';
$_wp_editor_settings = array('tinymce' => true);
if (!$WCMb->vendor_caps->vendor_can('is_upload_files')) {
    $_wp_editor_settings['media_buttons'] = false;
}
$_wp_editor_settings = apply_filters('wcmb_vendor_storefront_wp_editor_settings', $_wp_editor_settings);
?>
<style>
    .store-map-address{
        margin-top: 10px;
        border: 1px solid transparent;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        height: 29px;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    }
    #searchStoreAddress {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 60%;
    }
</style>
<div class="col-md-12">

 <b>  <center> to configure the store front you need to go your settings and click on wordpress backend and you choose profile.</center></b>
    </div>

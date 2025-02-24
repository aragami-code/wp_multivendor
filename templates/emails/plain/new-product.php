<?php
/**

 */
 
if ( !defined( 'ABSPATH' ) ) exit; 
global  $WCMb;

echo "= " . $email_heading . " =\n\n";

echo sprintf( __( "Hi there! This is to notify that a new product has been submitted in %s.",  'MB-multivendor' ), get_option( 'blogname' ) ); 
echo '\n'; 
echo sprintf(  __( "Product title: %s",  'MB-multivendor' ), $product_name ); 
echo '\n'; 
echo sprintf(  __( "Submitted by: %s",  'MB-multivendor' ), $vendor_name ); 
echo '\n'; 
$product_link = apply_filters( 'wcmb_email_vendor_new_product_link', esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_edit_product_endpoint', 'vendor', 'general', 'edit-product'), $post_id)));
echo sprintf(  __( "Edit product: %s",  'MB-multivendor' ), $product_link ); 
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo apply_filters( 'wcmb_email_footer_text', get_option( 'wcmb_email_footer_text' ) );

?>
<?php
/**
 
 */


if ( !defined( 'ABSPATH' ) ) exit; 
global  $WCMb;

if($post_type == 'shop_coupon') $title = __( 'Coupon', 'MB-multivendor' );
else  $title = __( 'Product', 'MB-multivendor' );
	
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

	<p><?php printf( __( "Hi there! This is a notification about a new %s on %s.",  'MB-multivendor' ), $title, get_option( 'blogname' ) ); ?></p>

	<p>
		<?php printf( __( "%s title: %s",  'MB-multivendor' ), $title, $product_name ); ?><br/>
		<?php printf( __( "Submitted by: %s",  'MB-multivendor' ), $vendor_name ); ?><br/>
		<?php 
                $product_link = apply_filters( 'wcmb_email_vendor_new_product_link', esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_edit_product_endpoint', 'vendor', 'general', 'edit-product'), $post_id)));
                printf( __( "Edit %s: %s",  'MB-multivendor' ), $title, $product_link ); ?>
		<br/>
	</p>

<?php do_action( 'wcmb_email_footer' ); ?>
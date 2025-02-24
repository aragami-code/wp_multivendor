<?php
/**

 */
 
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $WCMb;
$vendor = get_wcmb_vendor( absint( $vendor_id ) );
echo $email_heading . "\n\n";

echo sprintf( __( 'A new order was received and marked as completed from %s. Their order is as follows:',  'MB-multivendor' ), $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ) . "\n\n";

echo "****************************************************\n\n";

do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text );

echo sprintf( __( 'Order Number: %s',  'MB-multivendor'), $order->get_order_number() ) . "\n";
echo sprintf( __( 'Order Link: %s',  'MB-multivendor'), admin_url( 'post.php?post=' . $order->get_id() . '&action=edit' ) ) . "\n";
echo sprintf( __( 'Order Date: %s',  'MB-multivendor'), date_i18n( __( 'jS F Y',  'MB-multivendor' ), strtotime( $order->get_date_created() ) ) ) . "\n";

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text );


$vendor->plain_vendor_order_item_table($order, $vendor->term_id); 

echo "----------\n\n";
if(apply_filters('show_cust_order_calulations_field', true, $vendor->id)) {
    $totals = $vendor->wcmb_vendor_get_order_item_totals($order, $vendor->term_id);
	if ( $totals ) {
		foreach ( $totals as $total ) {
			echo $total['label'] . "\t " . $total['value'] . "\n";
		}
	}
}

echo "\n****************************************************\n\n";

do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text );
if(apply_filters('show_cust_address_field', true, $vendor->id)) {
	echo __( 'Customer Details',  'MB-multivendor' ) . "\n";

	if ( $order->get_billing_email() )
		echo __( 'Email:',  'MB-multivendor' ); echo $order->get_billing_email() . "\n";

	if ( $order->get_billing_phone() )
		echo __( 'Telephone:',  'MB-multivendor' ); ?> <?php echo $order->get_billing_phone() . "\n";
}

if(apply_filters('show_cust_billing_address_field', true, $vendor->id)) {
	echo "\n" . __( 'Billing Address',  'MB-multivendor' ) . ":\n";
	echo $order->get_formatted_billing_address() . "\n\n";
}
if(apply_filters('show_cust_shipping_address_field', true, $vendor->id)) {
	if ( get_option( 'woocommerce_ship_to_billing_address_only' ) == 'no' && ( $shipping = $order->get_formatted_shipping_address() ) ) {
	
		echo __( 'Shipping Address',  'MB-multivendor' ) . ":\n";
	
		echo $shipping . "\n\n";
	
	}
}

echo "\n****************************************************\n\n";

echo apply_filters( 'wcmb_email_footer_text', get_option( 'wcmb_email_footer_text' ) );
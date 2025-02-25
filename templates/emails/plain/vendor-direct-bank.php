<?php
/**

 */

 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $WCMb;
echo $email_heading . "\n\n"; 
$amount = get_post_meta($transaction_id, 'amount', true) - get_post_meta($transaction_id, 'transfer_charge', true) - get_post_meta($transaction_id, 'gateway_charge', true);		
echo apply_filters( 'wcmb_thankyou_transaction_received_text', sprintf(__( 'Hello,<br>We have received a new withdrawal request for %s from you and your request is being processed.The order details are as follows:', 'MB-multivendor'), get_woocommerce_currency().$amount), $transaction_id );

echo "****************************************************\n\n";




$commission_details  = $WCMb->transaction->get_transaction_item_details($transaction_id); 
if(!empty($commission_details['body'])) {
	foreach ( $commission_details['body'] as $commission_detail ) {	
		foreach($commission_detail as $details) {
			foreach($details as $detail_key => $detail) {
					echo $detail_key .' : '. $detail.'\n'; 
			}
		}
	}
}
echo "----------\n\n";
if ( $totals =  $WCMb->transaction->get_transaction_item_totals($transaction_id, $vendor) ) {
	foreach ( $totals as $total ) {
		echo $total['label'] .' : '. $total['value'].'\n';
	}
}
echo "\n****************************************************\n\n";
echo apply_filters( 'wcmb_email_footer_text', get_option( 'wcmb_email_footer_text' ) );
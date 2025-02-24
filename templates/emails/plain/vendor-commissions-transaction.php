<?php
/**
 
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $WCMb;
echo $email_heading . "\n\n"; 
$amount = get_post_meta($transaction_id, 'amount', true) - get_post_meta($transaction_id, 'transfer_charge', true) - get_post_meta($transaction_id, 'gateway_charge', true);
$transaction_mode = get_post_meta($transaction_id, 'transaction_mode', true);
if($transaction_mode == 'paypal_masspay') {
	printf(__( 'Hello,<br>You have successfully completed a withdrawal of $%s on %s through Paypal. The order details are as follows:', 'MB-multivendor' ),  $amount,  get_post_meta($transaction_id, 'paid_date', true));
} else if($transaction_mode == 'direct_bank') { 
	printf(__( 'Hello,<br>This is to notify you that your withdrawal request for $%s on %s has been successfully processed. The order details are as follows:  ', 'MB-multivendor' ),  $amount,  get_post_meta($transaction_id, 'paid_date', true));
}

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
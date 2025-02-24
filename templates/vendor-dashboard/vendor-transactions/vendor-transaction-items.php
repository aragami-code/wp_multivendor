<?php
/**

 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $woocommerce, $WCMb;
if(!empty($transactions)) { 
	foreach($transactions as $transaction_id) {
		$order_ids = $commssion_ids = '';
		$commission_details = get_post_meta($transaction_id, 'commission_detail', true);
		$transfer_charge = get_post_meta($transaction_id, 'transfer_charge', true);
		$transaction_amt = get_post_meta($transaction_id, 'amount', true) - get_post_meta($transaction_id, 'transfer_charge', true) - get_post_meta($transaction_id, 'gateway_charge', true);	
		?>
		<tr>
			<td align="center"  width="20" >
				<span class="input-group-addon beautiful">
					<input name="transaction_ids[]" value="<?php echo $transaction_id; ?>"  class="select_transaction" type="checkbox" >
				</span>
			</td>
			<td align="center" ><?php echo get_the_date('d/m', $transaction_id); ?></td>
                        <td align="center" ><a href="<?php echo  esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_vendor_orders_endpoint', 'vendor', 'general', 'vendor-withdrawal'), $transaction_id));?>">#<?php echo $transaction_id; ?></a></td>
                        <td align="center" ><?php echo '#'.  implode(', #', $commission_details) ?> </td>
                        <td align="center" ><?php echo isset($transfer_charge) ? wc_price($transfer_charge) : wc_price(0); ?></td>
                        <td align="center" valign="middle" ><?php echo wc_price($transaction_amt); ?></td>
    </tr>
		<?php 
	} 
}	
?>
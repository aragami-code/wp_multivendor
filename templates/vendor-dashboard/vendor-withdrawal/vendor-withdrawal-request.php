<?php
/**

 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $WCMb;
?>
<div class="col-md-12">
    <div class="panel panel-default">
        <h3 class="panel-heading"><?php echo apply_filters('wcmb_thankyou_transaction_received_text', sprintf(__('Withdrawal #%s details', 'MB-multivendor'), $transaction_id), $transaction_id); ?></h3>
        <div class="panel-body">
            <?php $transaction = get_post($transaction_id);
            $amount = (float) get_post_meta($transaction_id, 'amount', true) - (float) get_post_meta($transaction_id, 'transfer_charge', true) - (float) get_post_meta($transaction_id, 'gateway_charge', true);
            if (isset($transaction->post_type) && $transaction->post_type == 'wcmb_transaction') {
                $vendor = get_wcmb_vendor_by_term($transaction->post_author) ? get_wcmb_vendor_by_term($transaction->post_author) : get_wcmb_vendor($transaction->post_author);
                $commission_details = $WCMb->transaction->get_transaction_item_details($transaction_id);
            ?>
            <table class="table table-bordered">
                <?php if (!empty($commission_details['header'])) { 
                    echo '<thead><tr>';
                    foreach ($commission_details['header'] as $header_val) {
                        echo '<th>'.$header_val.'</th>';
                    }
                    echo '</tr></thead>';
                }
                echo '<tbody>';
                if (!empty($commission_details['body'])) {
                    
                    foreach ($commission_details['body'] as $commission_detail) {
                        echo '<tr>';
                        foreach ($commission_detail as $details) {
                            foreach ($details as $detail_key => $detail) {
                                echo '<td>'.$detail.'</td>';
                            }
                        }
                        echo '</tr>';
                    }
                    
                }
                if ($totals = $WCMb->transaction->get_transaction_item_totals($transaction_id, $vendor)) {
                    foreach ($totals as $total) {
                        echo '<tr><td colspan="3" >'.$total['label'].'</td><td>'.$total['value'].'</td></tr>';
                    }
                }
                echo '</tbody>';
                ?>
            </table>
        <?php } else { ?>
            <p class="wcmb_headding3"><?php printf(__('Hello,<br>Unfortunately your request for withdrawal amount could not be completed. You may try again later, or check you PayPal settings in your account page, or contact the admin at <b>%s</b>', 'MB-multivendor'), get_option('admin_email')); ?></p>
        <?php } ?>
        </div>
    </div>
</div>
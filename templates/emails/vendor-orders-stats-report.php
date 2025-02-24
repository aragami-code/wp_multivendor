<?php
/**
 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $WCMb;
$text_align = is_rtl() ? 'right' : 'left';
$t_sale = isset($report_data['stats']['sales_total']) ? $report_data['stats']['sales_total'] : 0;
$t_earning = isset($report_data['stats']['earning']) ? $report_data['stats']['earning'] : 0;
$t_withdrawal = isset($report_data['stats']['withdrawal']) ? $report_data['stats']['withdrawal'] : 0;
$t_orders_no = isset($report_data['stats']['orders_no']) ? $report_data['stats']['orders_no'] : 0;
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<p><?php printf(__( 'Hello %s,<br>Your %s store orders report stats are as follows:', 'MB-multivendor' ),  $vendor->page_title, $report_data['period']); ?></p>
<div style="margin-bottom: 40px;">
    <table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
        <thead>
            <tr>
                <th class="td" scope="col" style="text-align:<?php echo $text_align; ?>;"><?php printf(__( '%s sale:', 'MB-multivendor' ), ucfirst($report_data['period'])); ?></th>
                <th class="td" scope="col" style="text-align:<?php echo $text_align; ?>;"><?php printf(__( '%s earning:', 'MB-multivendor' ), ucfirst($report_data['period'])); ?></th>
                <th class="td" scope="col" style="text-align:<?php echo $text_align; ?>;"><?php printf(__( '%s withdrawal:', 'MB-multivendor' ), ucfirst($report_data['period'])); ?></th>
            </tr>
        </thead>
        <tbody>
            <td class="td" scope="col" style="text-align:<?php echo $text_align; ?>;font-size:28px;font-weight:bold;"><?php echo wc_price($t_sale); ?></td>
            <td class="td" scope="col" style="text-align:<?php echo $text_align; ?>;font-size:28px;font-weight:bold;"><?php echo wc_price($t_earning); ?></td>
            <td class="td" scope="col" style="text-align:<?php echo $text_align; ?>;font-size:28px;font-weight:bold;"><?php echo wc_price($t_withdrawal); ?></td>
        </tbody>
        <tfoot>
            <tr>
                <th class="td" scope="row" colspan="2" style="text-align:<?php echo $text_align; ?>;"><?php printf(__( '%s no of orders:', 'MB-multivendor' ), ucfirst($report_data['period'])); ?></th>
                <td class="td" style="text-align:<?php echo $text_align; ?>;"><?php echo $t_orders_no; ?></td>
            </tr>
            <tr>
                <th class="td" scope="row" colspan="2" style="text-align:<?php echo $text_align; ?>;"><?php _e( 'Period', 'MB-multivendor' ); ?></th>
                <td class="td" style="text-align:<?php echo $text_align; ?>;"><?php echo isset($report_data['period']) ? ucfirst($report_data['period']) : ''; ?></td>
            </tr>
            <tr>
                <th class="td" scope="row" colspan="2" style="text-align:<?php echo $text_align; ?>;"><?php _e( 'From Date', 'MB-multivendor' ); ?></th>
                <td class="td" style="text-align:<?php echo $text_align; ?>;"><?php echo isset($report_data['start_date']) ? $report_data['start_date'] : ''; ?></td>
            </tr>
            <tr>
                <th class="td" scope="row" colspan="2" style="text-align:<?php echo $text_align; ?>;"><?php _e( 'To Date', 'MB-multivendor' ); ?></th>
                <td class="td" style="text-align:<?php echo $text_align; ?>;"><?php echo isset($report_data['end_date']) ? $report_data['end_date'] : ''; ?></td>
            </tr>
        </tfoot>
    </table>
</div>
<br>
<?php if($attachments && count($attachments) > 0 && $report_data['order_data'] && count($report_data['order_data']) > 0 ){ ?>
<p><?php echo __( 'Please find your report attachment', 'MB-multivendor' ); ?></p>
<?php }else{ ?>
<p><?php echo __( 'There is no stats report available.', 'MB-multivendor' ); ?></p>   
<?php } ?>
<?php do_action( 'wcmb_email_footer' ); ?>

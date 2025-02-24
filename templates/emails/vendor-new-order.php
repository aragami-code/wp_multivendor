<?php
/**

 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly 
global $WCMb;
$vendor = get_wcmb_vendor(absint($vendor_id));
do_action( 'woocommerce_email_header', $email_heading, $email );
?>

<p><?php printf(__('A new order was received and marked as processing from %s. Their order is as follows:', 'MB-multivendor'), $order->get_billing_first_name() . ' ' . $order->get_billing_last_name()); ?></p>

<?php do_action('woocommerce_email_before_order_table', $order, true, false); ?>
<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
    <thead>
        <tr>
            <?php do_action('wcmb_before_vendor_order_table_header', $order, $vendor->term_id); ?>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Product', 'MB-multivendor'); ?></th>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Quantity', 'MB-multivendor'); ?></th>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Commission', 'MB-multivendor'); ?></th>
            <?php do_action('wcmb_after_vendor_order_table_header', $order, $vendor->term_id); ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $vendor->vendor_order_item_table($order, $vendor->term_id);

        ?>
    </tbody>
</table>
<?php
if (apply_filters('show_cust_order_calulations_field', true, $vendor->id)) {
    ?>
    <table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
        <?php
        $totals = $vendor->wcmb_vendor_get_order_item_totals($order, $vendor->term_id);
        if ($totals) {
            foreach ($totals as $total_key => $total) {
                ?><tr>
                    <th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee;"><?php echo $total['label']; ?></th>
                    <td style="text-align:left; border: 1px solid #eee;"><?php echo $total['value']; ?></td>
                </tr><?php
            }
        }
        ?>
    </table>
    <?php
}
if (apply_filters('show_cust_address_field', true, $vendor->id)) {
    ?>
    <h2><?php _e('Customer Details', 'MB-multivendor'); ?></h2>
    <?php if ($order->get_billing_email()) { ?>
        <p><strong><?php _e('Customer Name:', 'MB-multivendor'); ?></strong> <?php echo $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(); ?></p>
        <p><strong><?php _e('Email:', 'MB-multivendor'); ?></strong> <?php echo $order->get_billing_email(); ?></p>
    <?php } ?>
    <?php if ($order->get_billing_phone()) { ?>
        <p><strong><?php _e('Telephone:', 'MB-multivendor'); ?></strong> <?php echo $order->get_billing_phone(); ?></p>
    <?php
    }
}
if (apply_filters('show_cust_billing_address_field', true, $vendor->id)) {
    ?>
    <table cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top;" border="0">
        <tr>
            <td valign="top" width="50%">
                <h3><?php _e('Billing Address', 'MB-multivendor'); ?></h3>
                <p><?php echo $order->get_formatted_billing_address(); ?></p>
            </td>
        </tr>
    </table>
    <?php }
?>

<?php if (apply_filters('show_cust_shipping_address_field', true, $vendor->id)) { ?> 
    <?php if (( $shipping = $order->get_formatted_shipping_address())) { ?>
        <table cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top;" border="0">
            <tr>
                <td valign="top" width="50%">
                    <h3><?php _e('Shipping Address', 'MB-multivendor'); ?></h3>
                    <p><?php echo $shipping; ?></p>
                </td>
            </tr>
        </table>
    <?php
    }
}
?>



<?php do_action('wcmb_email_footer'); ?>
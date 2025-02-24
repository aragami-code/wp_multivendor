<?php
/**
 
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly    
    exit;
}
global $woocommerce, $WCMb;
$vendor = get_current_vendor();
$order = wc_get_order($order_id);
if (!$order) {
    ?>
    <div class="col-md-12">
        <div class="panel panel-default">
            <?php _e('Invalid order', 'MB-multivendor'); ?>
        </div>
    </div>
    <?php
    return;
}
$vendor_shipping_method = get_wcmb_vendor_order_shipping_method($order->get_id(), $vendor->id);
$vendor_items = get_wcmb_vendor_orders(array('order_id' => $order->get_id(), 'vendor_id' => $vendor->id));
$vendor_order_amount = get_wcmb_vendor_order_amount(array('order_id' => $order->get_id(), 'vendor_id' => $vendor->id));
//print_r($vendor_order_amount);die;
$subtotal = 0;
?>





<div class="col-md-12">
    <div class="icon-header">
        <span><i class="wcmb-font ico-order-details-icon"></i></span>
        <h2><?php _e('Order #', 'MB-multivendor'); ?><?php echo $order->get_id(); ?></h2>
        <h3><?php _e('was placed on', 'MB-multivendor'); ?> <?php echo wcmb_date($order->get_date_created()); ?> <?php _e('and is currently', 'MB-multivendor'); ?> <span class="<?php echo $order->get_status(); ?>" style="float:none;"><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>.</span></h3>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-default pannel-outer-heading mt-0">
                <div class="panel-heading"><h3><?php _e('Order Details', 'MB-multivendor'); ?></h3></div>
                <div class="panel-body panel-content-padding">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><?php _e('Product', 'MB-multivendor'); ?></th>
                                <th><?php _e('Total', 'MB-multivendor'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vendor_items as $item): 
                                $item_obj = $order->get_item($item->order_item_id); 
                                $edit_product_link = '';
                                if (current_user_can('edit_published_products') && get_wcmb_vendor_settings('is_edit_delete_published_product', 'capabilities', 'product') == 'Enable') {
                                    $edit_product_link = esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_edit_product_endpoint', 'vendor', 'general', 'edit-product'), $item->product_id));
                                } ?>
                                <tr>
                                    <td><?php echo $edit_product_link ? '<a href="' . $edit_product_link . '" class="wcmb-order-item-link">' . esc_html( $item_obj->get_name() ) . '</a>' : esc_html( $item_obj->get_name() ); ?> <small class="times">&times;</small> <?php echo esc_html( $item_obj->get_quantity() ); ?></td>
                                    <td><?php echo wc_price( $item_obj->get_total(), array( 'currency' => $order->get_currency() ) ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><?php _e('Commission:', 'MB-multivendor'); ?></td>
                                <td><?php echo wc_price($vendor_order_amount['commission_amount']); ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('Shipping:', 'MB-multivendor'); ?></td>
                                <td><?php echo wc_price($vendor_order_amount['shipping_amount']); ?><?php if($vendor_shipping_method) echo __(' via ', 'MB-multivendor') . $vendor_shipping_method->get_name(); ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('All Tax:', 'MB-multivendor'); ?></td>
                                <td><?php echo wc_price($vendor_order_amount['tax_amount'] + $vendor_order_amount['shipping_tax_amount']); ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('Payment method:', 'MB-multivendor'); ?></td>
                                <td><?php echo $order->get_payment_method_title(); ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('Total Earning:', 'MB-multivendor'); ?></td>
                                <td><?php echo wc_price($vendor_order_amount['total']); ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('Customer Note:', 'MB-multivendor'); ?></td>
                                <td><?php echo $order->get_customer_note(); ?></td>
                            </tr>
                            <?php do_action( 'wcmb_vendor_dashboard_order_details_table_info', $order, $vendor ); ?>
                        </tfoot>
                    </table>
                </div>
            </div>  
            <?php if(apply_filters('is_vendor_can_see_order_billing_address', true, $vendor->id) || apply_filters('is_vendor_can_see_order_shipping_address', true, $vendor->id)) :?>
            <div class="panel panel-default pannel-outer-heading wcmb-billing-shipping-wrap">
                <div class="panel-heading wcmb-billing-shipping-lbl">
                    <h3><?php _e('Billing &amp; Shipping address', 'MB-multivendor'); ?></h3>
                </div>
                <div class="panel-body panel-content-padding address-holder">
                    <div class="row">
                        <?php if(apply_filters('is_vendor_can_see_order_billing_address', true, $vendor->id)) :?>
                        <div class="col-xs-6">
                            <h2><?php _e('Billing address', 'MB-multivendor'); ?></h2>
                            <address>
                                <?php echo ( $address = $order->get_formatted_billing_address() ) ? $address : __('N/A', 'MB-multivendor'); ?>
                                <?php if ($order->get_billing_phone() || apply_filters('show_customer_billing_phone_for_vendor', true)) : ?>
                                    <p class="woocommerce-customer-details-phone"><?php echo esc_html($order->get_billing_phone()); ?></p>
                                <?php endif; ?>
                                <?php if ($order->get_billing_email() || apply_filters('show_customer_billing_email_for_vendor', true)) : ?>
                                    <p class="woocommerce-customer-details-email"><?php echo esc_html($order->get_billing_email()); ?></p>
                                <?php endif; 
                                do_action( 'wcmb_vendor_dashboard_order_details_billing_address', $order, $vendor ); ?>
                            </address>
                        </div>
                        <?php endif; ?>
                        <?php if(apply_filters('is_vendor_can_see_order_shipping_address', true, $vendor->id)) : ?>
                        <div class="col-xs-6">
                            <h2><?php _e('Shipping address', 'MB-multivendor'); ?></h2>
                            <address>
                            <?php echo ( $address = $order->get_formatted_shipping_address() ) ? $address : __('N/A', 'MB-multivendor'); 
                            do_action( 'wcmb_vendor_dashboard_order_details_shipping_address', $order, $vendor ); ?>
                            </address>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>  
            <?php endif; 
            do_action( 'wcmb_vendor_dashboard_after_order_details', $order, $vendor ); ?>
        </div>
        <div class="col-md-4">
            <?php
            $vendor_comments = $order->get_customer_order_notes();
            if (apply_filters('is_vendor_can_view_order_notes', true, $vendor->id)) { ?>
            <h3><?php _e('Order notes :', 'MB-multivendor'); ?></h3>
            <ul class="list-group">
                <?php  
                    if($vendor_comments){         
                        foreach ($vendor_comments as $comment) {
                        $comment_vendor = get_comment_meta($comment->comment_ID, '_vendor_id', true);
                        if ($comment_vendor && $comment_vendor != $vendor->id) {
                            continue;
                        }
                        $last_added = human_time_diff(strtotime($comment->comment_date), current_time('timestamp'));
                        ?>
                        <li class="list-group-item list-group-item-action flex-column align-items-start">
                            <p><?php printf(__('Added %s ago', 'MB-multivendor'), $last_added); ?>
                            <?php
                            if ( 'WooCommerce' !== $comment->comment_author ) :
                                /* translators: %s: note author */
                                printf( ' ' . __( 'by %s', 'MB-multivendor' ), $comment->comment_author );
                            endif;
                            ?></p>
                            <p><?php echo $comment->comment_content; ?></p>
                        </li>
                    <?php
                        } } 
                    ?>
                <li class="list-group-item list-group-item-action flex-column align-items-start">
                    <?php if(apply_filters('is_vendor_can_add_order_notes', true, $vendor->id)) :?>
                    <?php endif; ?>  
                    <form method="post" name="add_comment">
                        <?php wp_nonce_field('dc-vendor-add-order-comment', 'vendor_add_order_nonce'); ?>
                        <div class="form-group">
                            <textarea placeholder="<?php _e('Add Note', 'MB-multivendor'); ?>" required class="form-control" name="comment_text"></textarea>
                            <input type="hidden" name="order_id" value="<?php echo $order->get_id(); ?>">
                        </div>
                        <input class="btn btn-default wcmb-add-order-note" type="submit" name="wcmb_submit_comment" value="<?php _e('Submit', 'MB-multivendor'); ?>">
                    </form>              
                </li>
            </ul>
            <?php } ?>
        </div>
    </div>
</div>
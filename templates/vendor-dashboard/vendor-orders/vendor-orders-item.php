<?php
/**
 
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
global $woocommerce, $WCMb;

if (!empty($orders)) {
    foreach ($orders as $order) {
        $order_obj = new WC_Order($order);
        //$order_obj->get_id()
        $mark_ship = $WCMb->vendor_dashboard->is_order_shipped($order, get_wcmb_vendor(get_current_vendor_id()));
        $user_id = get_current_vendor_id();
        $user_id = apply_filters('wcmb_shipping_vendor', $user_id);
        ?>
        <tr>
            <td align="center"  width="20" ><span class="input-group-addon beautiful">
                    <input type="checkbox" class="select_<?php echo $order_status; ?>" name="select_<?php echo $order_status; ?>[<?php echo $order; ?>]" >
                </span></td>
            <td align="center" ><?php echo $order; ?> </td>
            <td align="center" ><?php echo date('d/m', strtotime($order_obj->get_date_created())); ?></td>
            <td class="no_display" align="center" >
                <?php
                //$vendor_share = $vendor->wcmb_get_vendor_part_from_order($order_obj, $vendor->term_id);
                $vendor_share = get_wcmb_vendor_order_amount(array('vendor_id' => $vendor->id, 'order_id' => $order_obj->get_id()));
                if (!isset($vendor_share['total'])) {
                    $vendor_share['total'] = 0;
                }
                echo wc_price($vendor_share['total']);
                ?>
            </td>
            <td class="no_display" align="center" ><?php echo $order_obj->get_status(); ?></td>
            <td align="center" valign="middle" >
                <?php
                $actions = array();
                $is_shipped = get_post_meta($order, 'dc_pv_shipped', true);
                if ($is_shipped) {
                    $mark_ship_title = __('Shipped', 'MB-multivendor');
                } else {
                    $mark_ship_title = __('Mark as shipped', 'MB-multivendor');
                }
                $actions['view'] = array(
                    'url' => esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_vendor_orders_endpoint', 'vendor', 'general', 'vendor-orders'), $order)),
                    'img' => $WCMb->plugin_url . 'assets/images/view.png',
                    'title' => __('View', 'MB-multivendor'),
                );

                $actions['wcmb_vendor_csv_download_per_order'] = array(
                    'url' => admin_url('admin-ajax.php?action=wcmb_vendor_csv_download_per_order&order_id=' . $order . '&nonce=' . wp_create_nonce('wcmb_vendor_csv_download_per_order')),
                    'img' => $WCMb->plugin_url . 'assets/images/download.png',
                    'title' => __('Download', 'MB-multivendor'),
                );
                if ($vendor->is_shipping_enable()) {
                    $actions['mark_ship'] = array(
                        'url' => '#',
                        'title' => $mark_ship_title,
                    );
                }

                $actions = apply_filters('wcmb_my_account_my_orders_actions', $actions, $order);

                if ($actions) {
                    foreach ($actions as $key => $action) {
                        ?>
                        <?php if ($key == 'view') { ?> 
                            <a title="<?php echo $action['title']; ?>" href="<?php echo $action['url']; ?>"><i><img src="<?php echo $action['img']; ?>" alt=""></i></a>&nbsp; 
                        <?php } elseif ($key == 'mark_ship') { ?>
                            <a id="popup-window" data-popup-target="#inline-<?php echo $order_status; ?>-<?php echo $order; ?>" href="javascript:void(0);" data-id="<?php echo $order; ?>" data-user="<?php echo $user_id; ?>" class="fancybox mark_ship_<?php echo $order; ?>" <?php if ($mark_ship) { ?> title="Shipped" style="pointer-events: none; cursor: default;" <?php } else { ?> title="mark as shipped" <?php } ?> ><i><img src="<?php if (!$mark_ship)
                        echo $WCMb->plugin_url . 'assets/images/roket_deep.png';
                    else
                        echo $WCMb->plugin_url . 'assets/images/roket-green.png';
                    ?>"  alt=""></i></a>                                                                                                                                
                            <input type="hidden" name="shipping_tracking_url" id="shipping_tracking_url_<?php echo $order; ?>" >
                            <input type="hidden" name="shipping_tracking_id" id="shipping_tracking_id_<?php echo $order; ?>" >
                            <div id="inline-<?php echo $order_status; ?>-<?php echo $order; ?>" class="popup">
                                <div class="popup-body"> 
                                    <span class="popup-exit"></span>    
                                    <div class="popup-content">
                                        <div class="shipping_msg_<?php echo $order; ?>" style="color: green;"></div>
                                        <div class="wcmb_headding2"><?php _e('Shipment Tracking Details', 'MB-multivendor'); ?></div>
                                        <p><?php _e('Enter Tracking Url', 'MB-multivendor'); ?> *</p>
                                        <input  class="long" onkeyup="geturlvalue(this, '<?php echo $order; ?>')" required type="text" name="shipping_tracking_url" placeholder="<?php _e('http://example.com/tracking/', 'MB-multivendor'); ?>">
                                        <p><?php _e('Enter Tracking ID', 'MB-multivendor'); ?> *</p>
                                        <input  class="long" onkeyup="getidvalue(this, '<?php echo $order; ?>')" required type="text" name="shipping_tracking_id" placeholder="<?php _e('XXXXXXXXXXXXX', 'MB-multivendor'); ?>">
                                        <div class="action_div_space"> </div>
                                        <div class="action_div">
                                            <button class="wcmb_orange_btn submit_tracking" name="submit_tracking" data-id="<?php echo $order; ?>" id="submit_tracking"><?php _e('Submit', 'MB-multivendor'); ?></button>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <a title="<?php echo $action['title']; ?>" href="<?php echo $action['url']; ?>" data-id="<?php echo $order; ?>" class="<?php echo sanitize_html_class($key); ?>" href="#"><i><img src="<?php echo $action['img']; ?>" alt=""></i></a>&nbsp;
                            <?php
                        }
                    }
                }
                ?>
            </td>
        </tr>
        <?php
    }
}
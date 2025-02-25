<?php

/**

 */
class WCMb_Calculate_Commission {

    public $completed_statuses;
    public $reverse_statuses;

    public function __construct() {
        
        // WC order complete statues
        $this->completed_statuses = apply_filters('wcmb_completed_commission_statuses', array('completed', 'processing'));

        // WC order reverse statues
        $this->reverse_statuses = apply_filters('wcmb_reversed_commission_statuses', array('pending', 'refunded', 'cancelled', 'failed'));

        $this->wcmb_order_reverse_action();
        $this->wcmb_order_complete_action();
        // support of WooCommerce subscription plugin
        add_filter('wcs_renewal_order_meta_query', array(&$this, 'wcs_renewal_order_meta_query'), 10, 1);
    }

    /**
     * Remove meta key from renewal order
     * Support WooCommerce subscription plugin
     * @param string $meta_query
     * @return string
     */
    public function wcs_renewal_order_meta_query($meta_query) {
        $meta_query .= " AND `meta_key` NOT LIKE '_wcmb_order_processed' AND `meta_key` NOT LIKE '_commissions_processed' ";
        return $meta_query;
    }

    /**
     * Add action hook when an order is reversed
     *
     * @author WC Marketplace
     * @return void
     */
    public function wcmb_order_reverse_action() {
        foreach ($this->completed_statuses as $cmpltd) {
            foreach ($this->reverse_statuses as $revsed) {
                add_action("woocommerce_order_status_{$cmpltd}_to_{$revsed}", array($this, 'wcmb_due_commission_reverse'));
            }
        }
    }

    /**
     * wcmb reverse vendor due commission for an order
     *
     * @param int $order_id
     */
    public function wcmb_due_commission_reverse($order_id) {
        $args = array(
            'post_type' => 'dc_commission',
            'post_status' => array('publish', 'private'),
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_commission_order_id',
                    'value' => absint($order_id),
                    'compare' => '='
                )
            )
        );
        $commissions = get_posts($args);
        if ($commissions) {
            foreach ($commissions as $commission) {
                update_post_meta($commission->ID, '_paid_status', 'reverse');
            }
        }
    }

    /**
     * Add action hook only when an order manually updated
     *
     * @author WC Marketplace
     * @return void
     */
    public function wcmb_order_complete_action() {
        foreach ($this->completed_statuses as $cmpltd) {
            add_action('woocommerce_order_status_' . $cmpltd, array($this, 'wcmb_process_commissions'));
        }
    }

    /**
     * Process commission
     * @param  int $order_id ID of order for commission
     * @return void
     */
    public function wcmb_process_commissions($order_id) {
        global $wpdb;
        // Only process commissions once
        $order = wc_get_order($order_id);
        $processed = get_post_meta($order_id, '_commissions_processed', true);
        $order_processed = get_post_meta($order_id, '_wcmb_order_processed', true);
        if (!$order_processed) {
            wcmb_process_order($order_id, $order);
        }
        $commission_ids = get_post_meta($order_id, '_commission_ids', true) ? get_post_meta($order_id, '_commission_ids', true) : array();
        if (!$processed) {
            $vendor_array = array();
            $items = $order->get_items('line_item');
            foreach ($items as $item_id => $item) {
                $vendor_id = wc_get_order_item_meta($item_id, '_vendor_id', true);
                if (!$vendor_id) {
                    $is_vendor_product = get_wcmb_product_vendors($item['product_id']);
                    if (!$is_vendor_product) {
                        continue;
                    }
                }
                $product_id = $item['product_id'];
                $variation_id = isset($item['variation_id']) && !empty($item['variation_id']) ? $item['variation_id'] : 0;
                if ($vendor_id) {
                    $vendor_obj = get_wcmb_vendor($vendor_id);
                } else {
                    $vendor_obj = get_wcmb_product_vendors($product_id);
                }
                if (in_array($vendor_obj->term_id, $vendor_array)) {
                    if ($variation_id) {
                        $query_id = $variation_id;
                    } else {
                        $query_id = $product_id;
                    }
                    $commission = $vendor_obj->get_vendor_commissions_by_product($order_id, $query_id);
                    $previous_ids = get_post_meta($commission[0], '_commission_product', true);
                    if (is_array($previous_ids)) {
                        array_push($previous_ids, $query_id);
                    }
                    update_post_meta($commission[0], '_commission_product', $previous_ids);

                    $item_commission = $this->get_item_commission($product_id, $variation_id, $item, $order_id, $item_id);

                    $wpdb->query("UPDATE `{$wpdb->prefix}wcmb_vendor_orders` SET commission_id = " . $commission[0] . ", commission_amount = '" . $item_commission . "' WHERE order_id =" . $order_id . " AND order_item_id = " . $item_id . " AND product_id = " . $product_id);
                } else {
                    $vendor_id = wc_get_order_item_meta($item_id, '_vendor_id', true);
                    if ($product_id) {
                        $commission_id = $this->record_commission($product_id, $order_id, $variation_id, $order, $vendor_obj, $item_id, $item);
                        if ($commission_id) {
                            $commission_ids[] = $commission_id;
                            update_post_meta($order_id, '_commission_ids', $commission_ids);
                        }
                        $vendor_array[] = $vendor_obj->term_id;
                    }
                }
            }
            $email_admin = WC()->mailer()->emails['WC_Email_Vendor_New_Order'];
            $email_admin->trigger($order_id);
        }
        // Mark commissions as processed
        update_post_meta($order_id, '_commissions_processed', 'yes');
        if (!empty($commission_ids) && is_array($commission_ids)) {
            foreach ($commission_ids as $commission_id) {
                $commission_amount = get_wcmb_vendor_order_amount(array('commission_id' => $commission_id, 'order_id' => $order_id));
                update_post_meta($commission_id, '_commission_amount', (float) $commission_amount['commission_amount']);
            }
        }
    }

    /**
     * Record individual commission
     * @param  int $product_id ID of product for commission
     * @param  int $line_total Line total of product
     * @return void
     */
    public function record_commission($product_id = 0, $order_id = 0, $variation_id = 0, $order, $vendor, $item_id = 0, $item) {
        if ($product_id > 0) {
            if ($vendor) {
                $vendor_due = $vendor->wcmb_get_vendor_part_from_order($order, $vendor->term_id);
                return $this->create_commission($vendor->term_id, $product_id, $vendor_due, $order_id, $variation_id, $item_id, $item, $order);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Create new commission post
     *
     * @param  int $vendor_id  ID of vendor for commission
     * @param  int $product_id ID of product for commission
     * @param  int $amount     Commission total
     * @return void
     */
    public function create_commission($vendor_id = 0, $product_id = 0, $amount = 0, $order_id = 0, $variation_id = 0, $item_id = 0, $item, $order) {
        global $wpdb;
        if ($vendor_id == 0) {
            return false;
        }
        $commission_data = array(
            'post_type' => 'dc_commission',
            'post_title' => sprintf(__('Commission - %s', 'MB-multivendor'), strftime(_x('%B %e, %Y @ %I:%M %p', 'Commission date parsed by strftime', 'MB-multivendor'), current_time( 'timestamp' ))),
            'post_status' => 'private',
            'ping_status' => 'closed',
            'post_excerpt' => '',
            'post_author' => 1
        );
        $commission_id = wp_insert_post($commission_data);
        // Add meta data
        if ($vendor_id > 0) {
            update_post_meta($commission_id, '_commission_vendor', $vendor_id);
        }
        if ($variation_id > 0) {
            update_post_meta($commission_id, '_commission_product', array($variation_id));
        } else {
            update_post_meta($commission_id, '_commission_product', array($product_id));
        }
        $shipping = (float) $amount['shipping'];
        $tax = (float) ($amount['tax'] + $amount['shipping_tax']);
        update_post_meta($commission_id, '_shipping', $shipping);
        update_post_meta($commission_id, '_tax', $tax);
        if ($order_id > 0) {
            update_post_meta($commission_id, '_commission_order_id', $order_id);
        }
        // Mark commission as unpaid
        update_post_meta($commission_id, '_paid_status', 'unpaid');
        $item_commission = $this->get_item_commission($product_id, $variation_id, $item, $order_id, $item_id);
        $wpdb->query("UPDATE `{$wpdb->prefix}wcmb_vendor_orders` SET commission_id = " . $commission_id . ", commission_amount = '" . $item_commission . "' WHERE order_id =" . $order_id . " AND order_item_id = " . $item_id . " AND product_id = " . $product_id);
        do_action('wcmb_vendor_commission_created', $commission_id);
        return $commission_id;
    }

    /**
     * Get vendor commission per item for an order
     *
     * @param int $product_id
     * @param int $variation_id
     * @param array $item
     * @param int $order_id
     *
     * @return $commission_amount
     */
    public function get_item_commission($product_id, $variation_id, $item, $order_id, $item_id = '') {
        global $WCMb;
        $order = wc_get_order($order_id);
        $amount = 0;
        $commission = array();
        $product_value_total = 0;
        if (isset($WCMb->vendor_caps->payment_cap['commission_include_coupon'])) {
            $line_total = $order->get_item_total($item, false, false) * $item['qty'];
        } else {
            $line_total = $order->get_item_subtotal($item, false, false) * $item['qty'];
        }
        if ($product_id) {
            $vendor_id = wc_get_order_item_meta($item_id, '_vendor_id', true);
            if ($vendor_id) {
                $vendor = get_wcmb_vendor($vendor_id);
            } else {
                $vendor = get_wcmb_product_vendors($product_id);
            }
            if ($vendor) {
                $commission = $this->get_commission_amount($product_id, $vendor->term_id, $variation_id, $item_id, $order);
                $commission = apply_filters('wcmb_get_commission_amount', $commission, $product_id, $vendor->term_id, $variation_id, $item_id, $order);
                if (!empty($commission)) {
                    if ($WCMb->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage') {
                        $amount = (float) $line_total * ( (float) $commission['commission_val'] / 100 ) + (float) $commission['commission_fixed'];
                    } else if ($WCMb->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage_qty') {
                        $amount = (float) $line_total * ( (float) $commission['commission_val'] / 100 ) + ((float) $commission['commission_fixed'] * $item['qty']);
                    } else if ($WCMb->vendor_caps->payment_cap['commission_type'] == 'percent') {
                        $amount = (float) $line_total * ( (float) $commission['commission_val'] / 100 );
                    } else if ($WCMb->vendor_caps->payment_cap['commission_type'] == 'fixed') {
                        $amount = (float) $commission['commission_val'] * $item['qty'];
                    }
                    if (isset($WCMb->vendor_caps->payment_cap['revenue_sharing_mode'])) {
                        if ($WCMb->vendor_caps->payment_cap['revenue_sharing_mode'] == 'admin') {
                            $amount = (float) $line_total - (float) $amount;
                            if ($amount < 0) {
                                $amount = 0;
                            }
                        }
                    }
                    if ($variation_id == 0 || $variation_id == '') {
                        $product_id_for_value = $product_id;
                    } else {
                        $product_id_for_value = $variation_id;
                    }

                    $product_value_total += $item->get_total();
                    if ($amount > $product_value_total) {
                        $amount = $product_value_total;
                    }
                    return apply_filters('vendor_commission_amount', $amount, $product_id, $variation_id, $item, $order_id, $item_id);
                }
            }
        }
        return apply_filters('vendor_commission_amount', $amount, $product_id, $variation_id, $item, $order_id, $item_id);
    }

    /**
     * Get assigned commission percentage
     *
     * @param  int $product_id ID of product
     * @param  int $vendor_id  ID of vendor
     * @return int             Relevent commission percentage
     */
    public function get_commission_amount($product_id = 0, $vendor_id = 0, $variation_id = 0, $item_id = '', $order = array()) {
        global $WCMb;

        $data = array();
        if ($product_id > 0 && $vendor_id > 0) {
            $vendor_idd = wc_get_order_item_meta($item_id, '_vendor_id', true);
            if ($vendor_idd) {
                $vendor = get_wcmb_vendor($vendor_idd);
            } else {
                $vendor = get_wcmb_product_vendors($product_id);
            }
            if ($vendor->term_id == $vendor_id) {

                if ($WCMb->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage') {

                    if ($variation_id > 0) {
                        $data['commission_val'] = get_post_meta($variation_id, '_product_vendors_commission_percentage', true);
                        $data['commission_fixed'] = get_post_meta($variation_id, '_product_vendors_commission_fixed_per_trans', true);
                        if (empty($data)) {
                            $data['commission_val'] = get_post_meta($product_id, '_commission_percentage_per_product', true);
                            $data['commission_fixed'] = get_post_meta($product_id, '_commission_fixed_with_percentage', true);
                        }
                    } else {
                        $data['commission_val'] = get_post_meta($product_id, '_commission_percentage_per_product', true);
                        $data['commission_fixed'] = get_post_meta($product_id, '_commission_fixed_with_percentage', true);
                    }
                    if (!empty($data['commission_val'])) {
                        return $data; // Use product commission percentage first
                    } else {
                        $category_wise_commission = $this->get_category_wise_commission($product_id);
                        if($category_wise_commission->commission_percentage || $category_wise_commission->fixed_with_percentage){
                            return array('commission_val' => $category_wise_commission->commission_percentage, 'commission_fixed' => $category_wise_commission->fixed_with_percentage);
                        }
                        $vendor_commission_percentage = 0;
                        $vendor_commission_percentage = get_user_meta($vendor->id, '_vendor_commission_percentage', true);
                        $vendor_commission_fixed_with_percentage = 0;
                        $vendor_commission_fixed_with_percentage = get_user_meta($vendor->id, '_vendor_commission_fixed_with_percentage', true);
                        if ($vendor_commission_percentage > 0) {
                            return array('commission_val' => $vendor_commission_percentage, 'commission_fixed' => $vendor_commission_fixed_with_percentage); // Use vendor user commission percentage 
                        } else {
                            if (isset($WCMb->vendor_caps->payment_cap['default_percentage'])) {
                                return array('commission_val' => $WCMb->vendor_caps->payment_cap['default_percentage'], 'commission_fixed' => $WCMb->vendor_caps->payment_cap['fixed_with_percentage']);
                            } else
                                return false;
                        }
                    }
                } else if ($WCMb->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage_qty') {

                    if ($variation_id > 0) {
                        $data['commission_val'] = get_post_meta($variation_id, '_product_vendors_commission_percentage', true);
                        $data['commission_fixed'] = get_post_meta($variation_id, '_product_vendors_commission_fixed_per_qty', true);
                        if (!$data) {
                            $data['commission_val'] = get_post_meta($product_id, '_commission_percentage_per_product', true);
                            $data['commission_fixed'] = get_post_meta($product_id, '_commission_fixed_with_percentage_qty', true);
                        }
                    } else {
                        $data['commission_val'] = get_post_meta($product_id, '_commission_percentage_per_product', true);
                        $data['commission_fixed'] = get_post_meta($product_id, '_commission_fixed_with_percentage_qty', true);
                    }
                    if (!empty($data['commission_val'])) {
                        return $data; // Use product commission percentage first
                    } else {
                        $category_wise_commission = $this->get_category_wise_commission($product_id);
                        if($category_wise_commission->commission_percentage || $category_wise_commission->fixed_with_percentage_qty){
                            return array('commission_val' => $category_wise_commission->commission_percentage, 'commission_fixed' => $category_wise_commission->fixed_with_percentage_qty);
                        }
                        $vendor_commission_percentage = 0;
                        $vendor_commission_fixed_with_percentage = 0;
                        $vendor_commission_percentage = get_user_meta($vendor->id, '_vendor_commission_percentage', true);
                        $vendor_commission_fixed_with_percentage = get_user_meta($vendor->id, '_vendor_commission_fixed_with_percentage_qty', true);
                        if ($vendor_commission_percentage > 0) {
                            return array('commission_val' => $vendor_commission_percentage, 'commission_fixed' => $vendor_commission_fixed_with_percentage); // Use vendor user commission percentage 
                        } else {
                            if (isset($WCMb->vendor_caps->payment_cap['default_percentage'])) {
                                return array('commission_val' => $WCMb->vendor_caps->payment_cap['default_percentage'], 'commission_fixed' => $WCMb->vendor_caps->payment_cap['fixed_with_percentage_qty']);
                            } else
                                return false;
                        }
                    }
                } else {
                    if ($variation_id > 0) {
                        $data['commission_val'] = get_post_meta($variation_id, '_product_vendors_commission', true);
                        if (!$data) {
                            $data['commission_val'] = get_post_meta($product_id, '_commission_per_product', true);
                        }
                    } else {
                        $data['commission_val'] = get_post_meta($product_id, '_commission_per_product', true);
                    }
                    if (!empty($data['commission_val'])) {
                        return $data; // Use product commission percentage first
                    } else {
                        if($category_wise_commission = $this->get_category_wise_commission($product_id)->commision){
                            return array('commission_val' => $category_wise_commission);
                        }
                        $vendor_commission = get_user_meta($vendor->id, '_vendor_commission', true);
                        if ($vendor_commission) {
                            return array('commission_val' => $vendor_commission); // Use vendor user commission percentage 
                        } else {
                            return isset($WCMb->vendor_caps->payment_cap['default_commission']) ? array('commission_val' => $WCMb->vendor_caps->payment_cap['default_commission']) : false; // Use default commission
                        }
                    }
                }
            }
        }
        return false;
    }
    /**
     * Fetch category wise commission
     * @param id $product_id
     * @return Object
     */
    public function get_category_wise_commission($product_id = 0) {
        $terms = get_the_terms($product_id, 'product_cat');
        $category_wise_commission = new stdClass();
        $category_wise_commission->commision = 0;
        $category_wise_commission->commission_percentage = 0;
        $category_wise_commission->fixed_with_percentage = 0;
        $category_wise_commission->fixed_with_percentage_qty = 0;
        if ($terms) {
            if (1 == count($terms)) {
                $category_wise_commission->commision = get_woocommerce_term_meta($terms[0]->term_id, 'commision', true) ? get_woocommerce_term_meta($terms[0]->term_id, 'commision', true) : 0;
                $category_wise_commission->commission_percentage = get_woocommerce_term_meta($terms[0]->term_id, 'commission_percentage', true) ? get_woocommerce_term_meta($terms[0]->term_id, 'commission_percentage', true) : 0;
                $category_wise_commission->fixed_with_percentage = get_woocommerce_term_meta($terms[0]->term_id, 'fixed_with_percentage', true) ? get_woocommerce_term_meta($terms[0]->term_id, 'fixed_with_percentage', true) : 0;
                $category_wise_commission->fixed_with_percentage_qty = get_woocommerce_term_meta($terms[0]->term_id, 'fixed_with_percentage_qty', true) ? get_woocommerce_term_meta($terms[0]->term_id, 'fixed_with_percentage_qty', true) : 0;
            }
        }
        return apply_filters('wcmb_category_wise_commission', $category_wise_commission, $product_id);
    }

}

<?php

/**
 
 */
Class WCMb_Admin_Dashboard {

    private $wcmb_vendor_order_page;

    function __construct() {

        // Add Shop Settings page 
        add_action('admin_menu', array($this, 'vendor_dashboard_pages'));

        add_action('woocommerce_product_options_shipping', array($this, 'wcmb_product_options_shipping'), 5);

        add_action('wp_before_admin_bar_render', array($this, 'remove_admin_bar_links'));

        add_action('wp_footer', 'wcmb_remove_comments_section_from_vendor_dashboard');

        add_action('wcmb_dashboard_setup', array(&$this, 'wcmb_dashboard_setup'), 5);
        add_action('wcmb_dashboard_widget', array(&$this, 'do_wcmb_dashboard_widget'));
        // Vendor store updater info
        add_action('wcmb_dashboard_setup', array(&$this, 'wcmb_dashboard_setup_updater'), 6);
        // Vendor save product
        if ( current_user_can( 'edit_products' ) ) {
            add_action( 'template_redirect', array( &$this, 'save_product' ), 90 );
        }
        if ( current_vendor_can( 'edit_shop_coupon' ) ) {
            add_action( 'template_redirect', array( &$this, 'save_coupon' ), 90 );
        }
        
        add_filter( 'wcmb_vendor_dashboard_add_product_url', array( &$this, 'wcmb_vendor_dashboard_add_product_url' ), 10 );

        // Init export functions
        $this->export_csv();

        // Init submit comment
        $this->submit_comment();

        $this->vendor_withdrawl();

        $this->export_vendor_orders_csv();
        // vendor tools handler
        $this->vendor_tools_handler();
        // vendor updater handler
        $this->vendor_updater_handler();
        // save shipping data
        $this->backend_shipping_handler();
    }

    function remove_admin_bar_links() {
        global $wp_admin_bar;
        if (!current_user_can('manage_options')) {
            $wp_admin_bar->remove_menu('new-post');
            $wp_admin_bar->remove_menu('new-dc_commission');
            $wp_admin_bar->remove_menu('comments');
        }
    }

    /**
     * Vendor Commission withdrawl
     */
    public function vendor_withdrawl() {
        global $WCMb;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['vendor_get_paid'])) {
                $vendor = get_wcmb_vendor(get_current_vendor_id());
                $commissions = isset($_POST['commissions']) ? $_POST['commissions'] : array();
                if (!empty($commissions)) {
                    $payment_method = get_user_meta($vendor->id, '_vendor_payment_mode', true);
                    if ($payment_method) {
                        if (array_key_exists($payment_method, $WCMb->payment_gateway->payment_gateways)) {
                            $response = $WCMb->payment_gateway->payment_gateways[$payment_method]->process_payment($vendor, $commissions, 'manual');
                            if ($response) {
                                if (isset($response['transaction_id'])) {
                                    $redirect_url = wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_transaction_details_endpoint', 'vendor', 'general', 'transaction-details'), $response['transaction_id']);
                                    $notice = $this->get_wcmb_transaction_notice($response['transaction_id']);
                                    if (isset($notice['type'])) {
                                        wc_add_notice($notice['message'], $notice['type']);
                                    }
                                    wp_safe_redirect($redirect_url);
                                    exit;
                                } else {
                                    foreach ($response as $message) {
                                        wc_add_notice($message['message'], $message['type']);
                                    }
                                }
                            } else {
                                wc_add_notice(__('Oops! Something went wrong please try again later', 'MB-multivendor'), 'error');
                            }
                        } else {
                            wc_add_notice(__('Invalid payment method', 'MB-multivendor'), 'error');
                        }
                    } else {
                        wc_add_notice(__('No payment method has been selected for commission withdrawal', 'MB-multivendor'), 'error');
                    }
                } else {
                    wc_add_notice(__('Please select atleast one or more commission.', 'MB-multivendor'), 'error');
                }
            }
        }
    }

    public function get_wcmb_transaction_notice($transaction_id) {
        $transaction = get_post($transaction_id);
        $notice = array();
        switch ($transaction->post_status) {
            case 'wcmb_processing':
                $notice = array('type' => 'success', 'message' => __('Your withdrawal request has been sent to the admin and your commission will be disbursed shortly!', 'MB-multivendor'));
                break;
            case 'wcmb_completed':
                $notice = array('type' => 'success', 'message' => __('Congrats! You have successfully received your commission amount.', 'MB-multivendor'));
                break;
            case 'wcmb_canceled':
                $notice = array('type' => 'error', 'message' => __('Oops something went wrong! Your commission withdrawal request was declined!', 'MB-multivendor'));
                break;
            default :
                break;
        }
        return apply_filters('wcmb_get_transaction_status_notice', $notice, $transaction);
    }

    /**
     * Export CSV from vendor dasboard page
     *
     * @access public
     * @return void
     */
    public function export_csv() {
        global $WCMb;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (isset($_POST['export_transaction'])) {
                $transaction_details = array();
                if (!empty($_POST['transaction_ids'])) {
                    $date = date('Y-m-d');
                    $filename = 'TransactionReport-' . $date . '.csv';
                    header("Pragma: public");
                    header("Expires: 0");
                    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                    header("Content-Type: application/force-download");
                    header("Content-Type: application/octet-stream");
                    header("Content-Type: application/download");
                    header("Content-Disposition: attachment;filename={$filename}");
                    header("Content-Transfer-Encoding: binary");
                    header("Content-Type: charset=UTF-8");

                    $headers = array(
                        'date' => __('Date', 'MB-multivendor'),
                        'trans_id' => __('Transaction ID', 'MB-multivendor'),
                        'commission_ids' => __('Commission IDs', 'MB-multivendor'),
                        'mode' => __('Mode', 'MB-multivendor'),
                        'commission' => __('Commission', 'MB-multivendor'),
                        'fee' => __('Fee', 'MB-multivendor'),
                        'credit' => __('Credit', 'MB-multivendor'),
                    );
                    if (!empty($_POST['transaction_ids'])) {
                        foreach ($_POST['transaction_ids'] as $transaction_id) {
                            $commission_details = get_post_meta($transaction_id, 'commission_detail', true);
                            $transfer_charge = get_post_meta($transaction_id, 'transfer_charge', true) + get_post_meta($transaction_id, 'gateway_charge', true);
                            $transaction_amt = get_post_meta($transaction_id, 'amount', true) - get_post_meta($transaction_id, 'transfer_charge', true) - get_post_meta($transaction_id, 'gateway_charge', true);
                            $transaction_commission = get_post_meta($transaction_id, 'amount', true);

                            $mode = get_post_meta($transaction_id, 'transaction_mode', true);
                            if ($mode == 'paypal_masspay' || $mode == 'paypal_payout') {
                                $transaction_mode = __('PayPal', 'MB-multivendor');
                            } else if ($mode == 'stripe_masspay') {
                                $transaction_mode = __('Stripe', 'MB-multivendor');
                            } else if ($mode == 'direct_bank') {
                                $transaction_mode = __('Direct Bank Transfer', 'MB-multivendor');
                            } else {
                                $transaction_mode = $mode;
                            }

                            $order_datas[] = array(
                                'date' => get_the_date('Y-m-d', $transaction_id),
                                'trans_id' => '#' . $transaction_id,
                                'order_ids' => '#' . implode(', #', $commission_details),
                                'mode' => $transaction_mode,
                                'commission' => $transaction_commission,
                                'fee' => $transfer_charge,
                                'credit' => $transaction_amt,
                            );
                        }
                    }


                    // Initiate output buffer and open file
                    ob_start();
                    $file = fopen("php://output", 'w');

                    // Add headers to file
                    fputcsv($file, $headers);
                    // Add data to file
                    if (!empty($order_datas)) {
                        foreach ($order_datas as $order_data) {
                            fputcsv($file, $order_data);
                        }
                    } else {
                        fputcsv($file, array(__('Sorry. no transaction data is available upon your selection', 'MB-multivendor')));
                    }

                    // Close file and get data from output buffer
                    fclose($file);
                    $csv = ob_get_clean();

                    // Send CSV to browser for download
                    echo $csv;
                    die();
                } else {
                    wc_add_notice(__('Please select atleast one and more transactions.', 'MB-multivendor'), 'error');
                }
            }
            $user = wp_get_current_user();
            $vendor = get_wcmb_vendor($user->ID);
            if (isset($_POST['wcmb_stat_export']) && !empty($_POST['wcmb_stat_export']) && $vendor && apply_filters('can_wcmb_vendor_export_orders_csv', true, $vendor->id)) {
                $vendor = apply_filters('wcmb_order_details_export_vendor', $vendor);
                $start_date = isset($_POST['wcmb_stat_start_dt']) ? $_POST['wcmb_stat_start_dt'] : date('Y-m-01');
                $end_date = isset($_POST['wcmb_stat_end_dt']) ? $_POST['wcmb_stat_end_dt'] : date('Y-m-d');
                $start_date = strtotime('-1 day', strtotime($start_date));
                $end_date = strtotime('+1 day', strtotime($end_date));
                $query = array(
                    'date_query' => array(
                        array(
                            'after' => array('year' => date('Y', $start_date), 'month' => date('m', $start_date), 'day' => date('d', $start_date)),
                            'before' => array('year' => date('Y', $end_date), 'month' => date('m', $end_date), 'day' => date('d', $end_date)),
                            'inclusive' => true,
                        )
                    )
                );
                $records = $vendor->get_orders(false, false, $query);
                if (!empty($records) && is_array($records)) {
                    $vendor_orders = array_unique($records);
                    if (!empty($vendor_orders))
                        $this->generate_csv($vendor_orders, $vendor);
                }
            }
        }
    }

    public function generate_csv($customer_orders, $vendor, $args = array()) {
        global $WCMb;
        $order_datas = array();
        $index = 0;
        $date = date('Y-m-d');
        $default = array(
            'filename' => 'SalesReport-' . $date . '.csv',
            'iostream' => 'php://output',
            'buffer' => 'w',
            'action' => 'download',
        );
        $args = wp_parse_args($args, $default);

        $filename = $args['filename'];
        if ($args['action'] == 'download') {
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header("Content-Disposition: attachment;filename={$filename}");
            header("Content-Transfer-Encoding: binary");
        }

        $headers = apply_filters('wcmb_vendor_order_generate_csv_headers', array(
            'order' => __('Order', 'MB-multivendor'),
            'date_of_purchase' => __('Date of Purchase', 'MB-multivendor'),
            'time_of_purchase' => __('Time Of Purchase', 'MB-multivendor'),
            'vendor_name' => __('Vendor Name', 'MB-multivendor'),
            'product' => __('Items bought', 'MB-multivendor'),
            'qty' => __('Quantity', 'MB-multivendor'),
            'discount_used' => __('Discount Used', 'MB-multivendor'),
            'tax' => __('Tax', 'MB-multivendor'),
            'shipping' => __('Shipping', 'MB-multivendor'),
            'commission_share' => __('Earning', 'MB-multivendor'),
            'payment_system' => __('Payment System', 'MB-multivendor'),
            'buyer_name' => __('Customer Name', 'MB-multivendor'),
            'buyer_email' => __('Customer Email', 'MB-multivendor'),
            'buyer_contact' => __('Customer Contact', 'MB-multivendor'),
            'billing_address' => __('Billing Address Details', 'MB-multivendor'),
            'shipping_address' => __('Shipping Address Details', 'MB-multivendor'),
            'order_status' => __('Order Status', 'MB-multivendor'),
        ));

        if (!apply_filters('show_customer_details_in_export_orders', true, $vendor->id)) {
            unset($headers['buyer_name']);
            unset($headers['buyer_email']);
            unset($headers['buyer_contact']);
        }
        if (!apply_filters('show_customer_billing_address_in_export_orders', true, $vendor->id)) {
            unset($headers['billing_address']);
        }
        if (!apply_filters('show_customer_shipping_address_in_export_orders', true, $vendor->id)) {
            unset($headers['shipping_address']);
        }

        if ($vendor) {
            if (!empty($customer_orders)) {
                foreach ($customer_orders as $commission_id => $customer_order) {
                    $order = new WC_Order($customer_order);
                    $vendor_items = $vendor->get_vendor_items_from_order($customer_order, $vendor->term_id);
                    $item_names = $item_qty = array();
                    if (sizeof($vendor_items) > 0) {
                        foreach ($vendor_items as $item) {
                            $item_names[] = $item['name'];
                            $item_qty[] = $item['quantity'];
                        }

                        //coupons count
                        $coupon_used = '';
                        $coupons = $order->get_items('coupon');
                        foreach ($coupons as $coupon_item_id => $item) {
                            $coupon = new WC_Coupon(trim($item['name']));
                            $coupon_post = get_post($coupon->get_id());
                            $author_id = $coupon_post->post_author;
                            if ($vendor->id == $author_id) {
                                $coupon_used .= $item['name'] . ', ';
                            }
                        }

                        // Formatted Addresses
                        $formatted_billing_address = apply_filters('woocommerce_order_formatted_billing_address', array(
                            'address_1' => $order->get_billing_address_1(),
                            'address_2' => $order->get_billing_address_2(),
                            'city' => $order->get_billing_city(),
                            'state' => $order->get_billing_state(),
                            'postcode' => $order->get_billing_postcode(),
                            'country' => $order->get_billing_country()
                                ), $order);
                        $formatted_billing_address = WC()->countries->get_formatted_address($formatted_billing_address);

                        $formatted_shipping_address = apply_filters('woocommerce_order_formatted_shipping_address', array(
                            'address_1' => $order->get_shipping_address_1(),
                            'address_2' => $order->get_shipping_address_2(),
                            'city' => $order->get_shipping_city(),
                            'state' => $order->get_shipping_state(),
                            'postcode' => $order->get_shipping_postcode(),
                            'country' => $order->get_shipping_country()
                                ), $order);
                        $formatted_shipping_address = WC()->countries->get_formatted_address($formatted_shipping_address);

                        $customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
                        $customer_email = $order->get_billing_email();
                        $customer_phone = $order->get_billing_phone();

                        $order_datas[$index] = apply_filters('wcmb_vendor_order_generate_csv_data', array(
                            'order' => '#' . $customer_order,
                            'date_of_purchase' => date_i18n('Y-m-d', strtotime($order->get_date_created())),
                            'time_of_purchase' => date_i18n('H', strtotime($order->get_date_created())) . ' : ' . date_i18n('i', strtotime($order->get_date_created())),
                            'vendor_name' => $vendor->page_title,
                            'product' => implode(', ', $item_names),
                            'qty' => implode(', ', $item_qty),
                            'discount_used' => apply_filters('wcmb_export_discount_used_in_order', $coupon_used),
                            'tax' => get_post_meta($commission_id, '_tax', true),
                            'shipping' => get_post_meta($commission_id, '_shipping', true),
                            'commission_share' => get_post_meta($commission_id, '_commission_amount', true),
                            'payment_system' => $order->get_payment_method_title(),
                            'buyer_name' => $customer_name,
                            'buyer_email' => $customer_email,
                            'buyer_contact' => $customer_phone,
                            'billing_address' => str_replace('<br/>', ', ', $formatted_billing_address),
                            'shipping_address' => str_replace('<br/>', ', ', $formatted_shipping_address),
                            'order_status' => $order->get_status(),
                                ), $customer_order, $vendor);
                        if (!apply_filters('show_customer_details_in_export_orders', true, $vendor->id)) {
                            unset($order_datas[$index]['buyer_name']);
                            unset($order_datas[$index]['buyer_email']);
                            unset($order_datas[$index]['buyer_contact']);
                        }
                        if (!apply_filters('show_customer_billing_address_in_export_orders', true, $vendor->id)) {
                            unset($order_datas[$index]['billing_address']);
                        }
                        if (!apply_filters('show_customer_shipping_address_in_export_orders', true, $vendor->id)) {
                            unset($order_datas[$index]['shipping_address']);
                        }
                        $index++;
                    }
                }
            }
        }
        // Initiate output buffer and open file
        ob_start();
        if ($args['action'] == 'download' && $args['iostream'] == 'php://output') {
            $file = fopen($args['iostream'], $args['buffer']);
        } elseif ($args['action'] == 'temp' && $args['filename']) {
            $filename = sys_get_temp_dir() . '/' . $args['filename'];
            $file = fopen($filename, $args['buffer']);
        }
        // Add headers to file
        fputcsv($file, $headers);
        // Add data to file
        foreach ($order_datas as $order_data) {
            if (!$WCMb->vendor_caps->vendor_capabilities_settings('is_order_show_email') || apply_filters('is_not_show_email_field', true)) {
                unset($order_data['buyer']);
            }
            fputcsv($file, $order_data);
        }

        // Close file and get data from output buffer
        fclose($file);
        $csv = ob_get_clean();
        if ($args['action'] == 'temp') {
            return $filename;
        } else {
            // Send CSV to browser for download
            echo $csv;
            die();
        }
    }

    /**
     * Submit Comment 
     *
     * @access public
     * @return void
     */
    public function submit_comment() {
        global $WCMb;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!empty($_POST['wcmb_submit_comment'])) {
                // verify nonce
                if ($_POST['vendor_add_order_nonce'] && !wp_verify_nonce($_POST['vendor_add_order_nonce'], 'dc-vendor-add-order-comment'))
                    return false;
                $vendor = get_current_vendor();
                // Don't submit empty comments
                if (empty($_POST['comment_text']))
                    return false;
                // Only submit if the order has the product belonging to this vendor
                $order = wc_get_order($_POST['order_id']);
                $comment = esc_textarea($_POST['comment_text']);
                $comment_id = $order->add_order_note($comment, 1);
                // update comment author & email
                wp_update_comment(array('comment_ID' => $comment_id, 'comment_author' => $vendor->page_title, 'comment_author_email' => $vendor->user_data->user_email));
                add_comment_meta($comment_id, '_vendor_id', $vendor->id);
                wp_redirect(esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_vendor_orders_endpoint', 'vendor', 'general', 'vendor-orders'), $order->get_id())));
                die();
            }
        }
    }

    /**
     * Vendor tools handler 
     *
     * @access public
     * @return void
     */
    public function vendor_tools_handler() {
        $vendor = get_current_vendor();
        $wpnonce = isset($_REQUEST['_wpnonce']) ? $_REQUEST['_wpnonce'] : '';
        $tools_action = isset($_REQUEST['tools_action']) ? $_REQUEST['tools_action'] : '';
        if ($wpnonce && wp_verify_nonce($wpnonce, 'wcmb_clear_vendor_transients') && $tools_action && $tools_action == 'clear_all_transients') {
            if (current_user_can('delete_published_products')) {
                if ($vendor->clear_all_transients($vendor->id)) {
                    wc_add_notice(__('Vendor transients cleared!', 'MB-multivendor'), 'success');
                }
                wp_redirect(esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_vendor_tools_endpoint', 'vendor', 'general', 'vendor-tools'))));
                die();
            }
        }
        // 
        do_action('wcmb_vendor_tools_handler', $tools_action, $wpnonce);
    }

    public function vendor_dashboard_pages() {
        $user = wp_get_current_user();
        $vendor = get_wcmb_vendor($user->ID);
        $vendor = apply_filters('wcmb_vendor_dashboard_pages_vendor', $vendor);
        if ($vendor) {
            $order_page = apply_filters('wcmb_vendor_view_order_page', true);
            if ($order_page) {
                $hook = add_menu_page(__('Orders', 'MB-multivendor'), __('Orders', 'MB-multivendor'), 'read', 'dc-vendor-orders', array($this, 'wcmb_vendor_orders_page'));
                add_action("load-$hook", array($this, 'add_order_page_options'));
            }

            $shipping_page = apply_filters('wcmb_vendor_view_shipping_page', true);
            if ($vendor->is_shipping_enable() && $shipping_page) {
                $shipping_hook = add_menu_page(__('Shipping', 'MB-multivendor'), __('Shipping', 'MB-multivendor'), 'read', 'dc-vendor-shipping', array($this, 'shipping_page'));
                add_action("load-$shipping_hook", array($this, 'load_wcmb_shipping_handlers'));
            }
        }
    }

    /**
     * HTML setup for the Orders Page 
     */
    public static function shipping_page() {
        global $WCMb;
        $zone_id = isset($_REQUEST['zone_id']) ? absint($_REQUEST['zone_id']) : 0;
        $zones = array();
        
        $vendor_user_id = apply_filters('wcmb_dashboard_shipping_vendor', get_current_vendor_id());

        ?>
        <div class="wrap">
            <div id="icon-woocommerce" class="icon32 icon32-woocommerce-reports"><br/></div>
            <h2><?php _e('Shipping', 'MB-multivendor'); ?></h2>
            <form name="vendor_shipping_form" method="post">
                <?php wp_nonce_field( 'backend_vendor_shipping_data', 'vendor_shipping_data' ); ?>
                <?php 
                if ($zone_id) {
                    if( !class_exists( 'WCMB_Shipping_Zone' ) ) {
                        $WCMb->load_vendor_shipping();
                    }
                    $zones = WCMB_Shipping_Zone::get_zone($zone_id);
                    if ($zones)
                        $zone = WC_Shipping_Zones::get_zone(absint($zone_id));
                    // Load scripts
                    $WCMb->localize_script('wcmb_vendor_shipping');
                    wp_enqueue_script('wcmb_vendor_shipping');

                if (!$zones) {
                    ?>
                    <p><?php _e('No shipping zone found for configuration. Please contact with admin for manage your store shipping', 'MB-multivendor'); ?></p>
                    <?php
                } elseif ($zones) {
                    // for specific zone shipping methods settings

                    $show_post_code_list = $show_state_list = $show_post_code_list = false;

                    $zone_id = $zones['data']['id'];
                    $zone_locations = $zones['data']['zone_locations'];

                    $zone_location_types = array_column(array_map('wcmb_convert_to_array', $zone_locations), 'type', 'code');

                    $selected_continent_codes = array_keys($zone_location_types, 'continent');

                    if (!$selected_continent_codes) {
                        $selected_continent_codes = array();
                    }

                    $selected_country_codes = array_keys($zone_location_types, 'country');
                    $all_states = WC()->countries->get_states();

                    $state_key_by_country = array();
                    $state_key_by_country = array_intersect_key($all_states, array_flip($selected_country_codes));

                    array_walk($state_key_by_country, 'wcmb_state_key_alter');

                    $state_key_by_country = call_user_func_array('array_merge', $state_key_by_country);

                    $show_limit_location_link = apply_filters('show_limit_location_link', (!in_array('postcode', $zone_location_types)));
                    $vendor_shipping_methods = $zones['shipping_methods'];

                    if ($show_limit_location_link) {
                        if (in_array('state', $zone_location_types)) {
                            $show_city_list = apply_filters('wcmb_city_select_dropdown_enabled', false);
                            $show_post_code_list = true;
                        } elseif (in_array('country', $zone_location_types)) {
                            $show_state_list = true;
                            $show_city_list = apply_filters('wcmb_city_select_dropdown_enabled', false);
                            $show_post_code_list = true;
                        }
                    }

                    $want_to_limit_location = !empty($zones['locations']);
                    $countries = $states = $cities = array();
                    $postcodes = '';
                    if ($want_to_limit_location) {
                        $postcodes = array();
                        foreach ($zones['locations'] as $each_location) {
                            switch ($each_location['type']) {
                                case 'state':
                                    $states[] = $each_location['code'];
                                    break;
                                case 'postcode':
                                    $postcodes[] = $each_location['code'];
                                    break;
                                default:
                                    break;
                            }
                        }
                        
                        $postcodes = implode(',', $postcodes);
                    }
                    
                    ?>
                    <input id="zone_id" class="form-control" type="hidden" name="<?php echo 'wcmb_shipping_zone[' . $zone_id . '][_zone_id]'; ?>" value="<?php echo $zone_id; ?>">
                    <table class="form-table wcmb-shipping-zone-settings wc-shipping-zone-settings">
                        <tbody>
                            <tr valign="top" class="">
                                <th scope="row" class="titledesc">
                                    <label for="">
                                        <?php _e('Zone Name', 'MB-multivendor'); ?>
                                    </label>
                                </th>
                                <td class="forminp"><?php _e($zones['data']['zone_name'], 'MB-multivendor'); ?></td>
                            </tr>
                            <tr valign="top" class="">
                                <th scope="row" class="titledesc">
                                    <label for="">
                                        <?php _e('Zone region', 'MB-multivendor'); ?>
                                    </label>
                                </th>
                                <td class="forminp"><?php _e($zones['formatted_zone_location'], 'MB-multivendor'); ?></td>
                            </tr>
                            <?php if ($show_limit_location_link && $zone_id !== 0) { ?>
                                <tr valign="top" class="">
                                    <th scope="row" class="titledesc">
                                        <label for="">
                                            <?php _e('Limit Zone Location', 'MB-multivendor'); ?>
                                        </label>
                                    </th>
                                    <td class="forminp"><input id="limit_zone_location" class="form-control" type="checkbox" name="<?php echo 'wcmb_shipping_zone[' . $zone_id . '][_limit_zone_location]'; ?>" value="1" <?php checked($want_to_limit_location, 1); ?>></td>
                                </tr>
                            <?php } ?>
                            <?php if ($show_state_list) { ?>
                                <tr valign="top" class="hide_if_zone_not_limited">
                                    <th scope="row" class="titledesc">
                                        <label for="">
                                            <?php _e('Select specific states', 'MB-multivendor'); ?>
                                        </label>
                                    </th>
                                    <td class="forminp">
                                        <select id="select_zone_states" class="form-control" name="<?php echo 'wcmb_shipping_zone[' . $zone_id . '][_select_zone_states][]'; ?>" multiple>
                                            <?php foreach ($state_key_by_country as $key => $value) { ?>
                                                <option value="<?php echo $key; ?>" <?php selected(in_array($key, $states), true); ?>><?php echo $value; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if ($show_post_code_list) { ?>
                                <tr valign="top" class="hide_if_zone_not_limited">
                                    <th scope="row" class="titledesc">
                                        <label for="">
                                            <?php _e('Set your postcode', 'MB-multivendor'); ?>
                                        </label>
                                    </th>
                                    <td class="forminp">
                                        <input id="select_zone_postcodes" class="form-control" type="text" name="<?php echo 'wcmb_shipping_zone[' . $zone_id . '][_select_zone_postcodes]'; ?>" value="<?php echo $postcodes; ?>" placholder="<?php _e('Postcodes need to be comma separated', 'MB-multivendor'); ?>">
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr valign="top" class="">
                                <th scope="row" class="titledesc">
                                    <label>
                                        <?php _e('Shipping methods', 'MB-multivendor'); ?>
                                        <?php echo wc_help_tip(__('Add your shipping method for appropiate zone', 'MB-multivendor')); // @codingStandardsIgnoreLine  ?>
                                    </label>
                                </th>
                                <td class="">
                                    <table class="wcmb-shipping-zone-methods wc-shipping-zone-methods widefat">
                                        <thead>
                                            <tr>   
                                                <th class="wcmb-title wc-shipping-zone-method-title"><?php _e('Title', 'MB-multivendor'); ?></th>
                                                <th class="wcmb-enabled wc-shipping-zone-method-enabled"><?php _e('Enabled', 'MB-multivendor'); ?></th> 
                                                <th class="wcmb-description wc-shipping-zone-method-description"><?php _e('Description', 'MB-multivendor'); ?></th>
                                                <th class="wcmb-action"><?php _e('Action', 'MB-multivendor'); ?></th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4">
                                                    <button type="submit" class="button wcmb-shipping-zone-show-method wc-shipping-zone-add-method" value="<?php esc_attr_e('Add shipping method', 'woocommerce'); ?>"><?php esc_html_e('Add shipping method', 'woocommerce'); ?></button>
                                                </td>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            <?php if (empty($vendor_shipping_methods)) { ?> 
                                                <tr>
                                                    <td colspan="4"><?php _e('You can add multiple shipping methods within this zone. Only customers within the zone will see them.', 'MB-multivendor'); ?></td>
                                                </tr>
                                                <?php
                                            } else { 
                                                foreach ($vendor_shipping_methods as $vendor_shipping_method) {
                                                    ?>
                                                    <tr class="wcmb-shipping-zone-method">
                                                        <td><?php _e($vendor_shipping_method['title'], 'wcmb'); ?>
                                                            <div data-instance_id="<?php echo $vendor_shipping_method['instance_id']; ?>" data-method_id="<?php echo $vendor_shipping_method['id']; ?>" data-method-settings='<?php echo json_encode($vendor_shipping_method); ?>' class="row-actions edit_del_actions">
                                                            </div>
                                                        </td>
                                                        <td class="wcmb-shipping-zone-method-enabled wc-shipping-zone-method-enabled"> 
                                                            <span class="wcmb-input-toggle woocommerce-input-toggle woocommerce-input-toggle--<?php echo ($vendor_shipping_method['enabled'] == "yes") ? 'enabled' : 'disabled'; ?>">
                                                                <input id="method_status" class="input-checkbox method-status" type="checkbox" name="method_status" value="<?php echo $vendor_shipping_method['instance_id']; ?>" <?php checked(( $vendor_shipping_method['enabled'] == "yes"), true); ?>>
                                                            </span>
                                                        </td>
                                                        <td><?php _e($vendor_shipping_method['settings']['description'], 'MB-multivendor'); ?></td>
                                                        <td>
                                                            <div class="col-actions edit_del_actions" data-instance_id="<?php echo $vendor_shipping_method['instance_id']; ?>" data-method_id="<?php echo $vendor_shipping_method['id']; ?>" data-method-settings='<?php echo json_encode($vendor_shipping_method); ?>'>
                                                                <span class="edit"><a href="javascript:void(0);" class="edit-shipping-method" data-zone_id="<?php echo $zone_id; ?>" data-method_id="<?php echo $vendor_shipping_method['id']; ?>" data-instance_id="<?php echo $vendor_shipping_method['instance_id']; ?>" title="<?php _e('Edit', 'MB-multivendor') ?>"><?php _e('Edit', 'MB-multivendor') ?></a>
                                                                </span>|
                                                                <span class="delete"><a class="delete-shipping-method" href="javascript:void(0);" data-method_id="<?php echo $vendor_shipping_method['id']; ?>" data-instance_id="<?php echo $vendor_shipping_method['instance_id']; ?>" title="<?php _e('Delete', 'MB-multivendor') ?>"><?php _e('Delete', 'MB-multivendor') ?></a></span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                        
                        <script type="text/template" id="tmpl-wcmb-modal-add-shipping-method">
                            <div class="wc-backbone-modal wcmb-modal-add-shipping-method-modal">
                            <div class="wc-backbone-modal-content">
                            <section class="wc-backbone-modal-main" role="main">
                            <header class="wc-backbone-modal-header">
                            <h1><?php esc_html_e('Add shipping method', 'MB-multivendor'); ?></h1>
                            <button class="modal-close modal-close-link dashicons dashicons-no-alt">
                            <span class="screen-reader-text"><?php esc_html_e('Close modal panel', 'MB-multivendor'); ?></span>
                            </button>
                            </header>
                            <article>
                            <form action="" method="post">
                            <input type="hidden" name="zone_id" value="<?php echo $zone_id; ?>"/>
                            <div class="wc-shipping-zone-method-selector">
                            <p><?php esc_html_e('Choose the shipping method you wish to add. Only shipping methods which support zones are listed.', 'MB-multivendor'); ?></p>
                            <?php $shipping_methods = wcmb_get_shipping_methods(); ?>
                            <select id="shipping_method" class="form-control mt-15" name="wcmb_shipping_method">
                            <?php foreach ($shipping_methods as $key => $method) { ?>
                                <option data-description="<?php echo esc_attr( wp_kses_post( wpautop( $method->get_method_description() ) ) ); ?>" value="<?php echo esc_attr( $method->id ); ?>"><?php echo esc_attr( $method->get_method_title() ); ?></option>
                            <?php } ?>
                            </select>
                            <div class="wc-shipping-zone-method-description"></div>
                            </div>
                            </form>
                            </article>
                            <footer>
                            <div class="inner">
                            <button id="btn-ok" class="button button-primary button-large wcmb-shipping-zone-add-method" data-zone_id="<?php echo $zone_id; ?>"><?php esc_html_e('Add shipping method', 'MB-multivendor'); ?></button>
                            </div>
                            </footer>
                            </section>
                            </div>
                            </div>
                            <div class="wc-backbone-modal-backdrop modal-close"></div>
                        </script>
                        <script type="text/template" id="tmpl-wcmb-modal-update-shipping-method">
                            <?php
                            global $WCMb;

                            $is_method_taxable_array = array(
                                'none' => __('None', 'MB-multivendor'),
                                'taxable' => __('Taxable', 'MB-multivendor')
                            );

                            $calculation_type = array(
                                'class' => __('Per class: Charge shipping for each shipping class individually', 'MB-multivendor'),
                                'order' => __('Per order: Charge shipping for the most expensive shipping class', 'MB-multivendor'),
                            );
                            ?>
                            <div class="wc-backbone-modal wcmb-modal-add-shipping-method-modal">
                            <div class="wc-backbone-modal-content">
                            <section class="wc-backbone-modal-main" role="main">
                            <header class="wc-backbone-modal-header">
                            <h1><?php _e( 'Edit Shipping Methods', 'MB-multivendor' ); ?></h1>
                            <button class="modal-close modal-close-link dashicons dashicons-no-alt">
                            <span class="screen-reader-text"><?php esc_html_e('Close modal panel', 'MB-multivendor'); ?></span>
                            </button>
                            </header>
                            <article class="wcmb-shipping-methods">
                            <form action="" method="post">
                            <input id="instance_id_selected" class="form-control" type="hidden" name="zone_id" value="<?php echo $zone_id; ?>"> 
                            <input id="method_id_selected" class="form-control" type="hidden" name="method_id" value="{{{ data.methodId }}}"> 
                            <input id="instance_id_selected" class="form-control" type="hidden" name="instance_id" value="{{{ data.instanceId }}}"> 
                            {{{ data.config_settings }}}
                 
                            </form>
                            </article>
                            <footer>
                            <div class="inner">
                            <button id="btn-ok" class="button button-primary button-large wcmb-shipping-zone-add-method" data-zone_id="<?php echo $zone_id; ?>"><?php esc_html_e('Save changes', 'MB-multivendor'); ?></button>
                            </div>
                            </footer>
                            </section>
                            </div>
                            </div>
                            <div class="wc-backbone-modal-backdrop modal-close"></div>
                        </script>
                    </table>
                <?php }
                    
                } else { ?>
                    <table class="wcmb-shipping-zones wc-shipping-zones widefat">
                            <thead>
                                <tr>
                                    <th><?php _e('Zone name', 'MB-multivendor'); ?></th> 
                                    <th><?php _e('Region(s)', 'MB-multivendor'); ?></th> 
                                    <th><?php _e('Shipping method(s)', 'MB-multivendor'); ?></th>
                                    <th><?php _e('Actions', 'MB-multivendor'); ?></th>
                                </tr>
                            </thead>
                            <tbody class="wcmb-shipping-zone-rows wc-shipping-zone-rows">
                    <?php $vendor_all_shipping_zones = wcmb_get_shipping_zone();
                    if (!empty($vendor_all_shipping_zones)) {
                        foreach ($vendor_all_shipping_zones as $key => $vendor_shipping_zones) {
                    ?>
                                        <tr data-id="0" class="wc-shipping-zone-worldwide">
                                            <td class="wc-shipping-zone-name">
                                                <a href="<?php echo esc_url(admin_url('admin.php?page=dc-vendor-shipping&zone_id=' . $vendor_shipping_zones['zone_id'])); ?>" data-zone-id="<?php echo $vendor_shipping_zones['zone_id']; ?>" class="vendor_edit_zone modify-shipping-methods"><?php _e($vendor_shipping_zones['zone_name'], 'MB-multivendor'); ?></a> 
                                            </td>
                                            <td class="wc-shipping-zone-region"><?php _e($vendor_shipping_zones['formatted_zone_location'], 'MB-multivendor'); ?></td>
                                            <td class="wc-shipping-zone-methods">
                                                <ul class="wcmb-shipping-zone-methods">
                    <?php
                    $vendor_shipping_methods = $vendor_shipping_zones['shipping_methods'];
                    $vendor_shipping_methods_titles = array();
                    if ($vendor_shipping_methods) :
                        foreach ($vendor_shipping_methods as $key => $shipping_method) {
                            $class_name = 'yes' === $shipping_method['enabled'] ? 'method_enabled' : 'method_disabled';
                            $vendor_shipping_methods_titles[] = "<li class='wcmb-shipping-zone-method wc-shipping-zone-method $class_name'>" . $shipping_method['title'] . "</li>";
                        }
                    endif;
                    //$vendor_shipping_methods_titles = array_column($vendor_shipping_methods, 'title');
                    $vendor_shipping_methods_titles = implode('', $vendor_shipping_methods_titles);

                    if (empty($vendor_shipping_methods)) {
                        ?>
                                                        <li class="wcmb-shipping-zone-method wc-shipping-zone-method"><?php _e('No shipping methods offered to this zone.', 'MB-multivendor'); ?> </li>
                                                    <?php } else { ?>
                                                        <?php _e($vendor_shipping_methods_titles, 'MB-multivendor'); ?>
                                                    <?php } ?>
                                                </ul>
                                            </td>
                                            <td>
                                                <div class="col-actions">
                                                    <span class="view">
                                                        <a href="<?php echo esc_url(admin_url('admin.php?page=dc-vendor-shipping&zone_id=' . $vendor_shipping_zones['zone_id'])); ?>" data-zone-id="<?php echo $vendor_shipping_zones['zone_id']; ?>" class="vendor_edit_zone modify-shipping-methods" title="<?php _e('Edit', 'MB-multivendor'); ?>"><?php _e('Edit', 'MB-multivendor'); ?></a>
                                                    </span> 
                                                </div>
                                            </td>
                                        </tr>
                    <?php
                }
            } else {
                ?>
                                    <tr>
                                        <td colspan="3"><?php _e('No shipping zone found for configuration. Please contact with admin for manage your store shipping', 'MB-multivendor'); ?></td>
                                    </tr>
            <?php }
            ?>
                            </tbody>
                        </table>
                <?php }
                ?>
                    <?php do_action('wcmb_vendor_shipping_settings'); ?>
                <?php if(isset($_GET['zone_id'])) submit_button(); ?>
            </form>

            <br class="clear"/>
        </div>
        <?php
    }
    
    public function backend_shipping_handler(){
        global $WCMb;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ( isset( $_POST['vendor_shipping_data'] ) && wp_verify_nonce( $_POST['vendor_shipping_data'], 'backend_vendor_shipping_data' ) ) {
                $all_allowed_countries = WC()->countries->get_allowed_countries();
                $location = array();
                $zone_id = 0;
                if (!empty($_POST['wcmb_shipping_zone'])) {
                    foreach ($_POST['wcmb_shipping_zone'] as $shipping_zone) {
                        if (isset($shipping_zone['_zone_id']) && $shipping_zone['_zone_id'] != 0) {
                            $zone_id = $shipping_zone['_zone_id'];

                            if (isset($shipping_zone['_limit_zone_location']) && $shipping_zone['_limit_zone_location']) {
                                if (!empty($shipping_zone['_select_zone_states'])) {
                                    $state_array = array();
                                    foreach ($shipping_zone['_select_zone_states'] as $zone_state) {
                                        $state_array[] = array(
                                            'code' => $zone_state,
                                            'type' => 'state'
                                        );
                                    }

                                    $location = array_merge($location, $state_array);
                                }

                                if (!empty($shipping_zone['_select_zone_postcodes'])) {
                                    $postcode_array = array();
                                    $zone_postcodes = array_map('trim', explode(',', $shipping_zone['_select_zone_postcodes']));
                                    foreach ($zone_postcodes as $zone_postcode) {
                                        $postcode_array[] = array(
                                            'code' => $zone_postcode,
                                            'type' => 'postcode'
                                        );
                                    }

                                    $location = array_merge($location, $postcode_array);
                                }
                            }
                        }
                    }
                }
                if( !class_exists( 'WCMb_Shipping_Zone' ) ) {
                    $WCMb->load_vendor_shipping();
                }
                WCMB_Shipping_Zone::save_location($location, $zone_id);

                $WCMb->load_class('shipping-gateway');
                WCMb_Shipping_Gateway::load_class('shipping-method');
                $vendor_shipping = new WCMB_Vendor_Shipping_Method();
                $vendor_shipping->process_admin_options();
                // clear shipping transient
                WC_Cache_Helper::get_transient_version('shipping', true);
                echo '<div class="updated settings-error notice is-dismissible"><p><strong>' . __("Shipping Data Updated", 'MB-multivendor') . '</strong></p></div>';
            }
            
        }
    }

    /**
     *
     *
     * @param unknown $status
     * @param unknown $option
     * @param unknown $value
     *
     * @return unknown
     */
    public static function set_table_option($status, $option, $value) {
        if ($option == 'orders_per_page') {
            return $value;
        }
    }

    /**
     * Add order page options
     * Defined cores in Vendor Order Page class
     */
    public function add_order_page_options() {
        global $WCMb;
        $args = array(
            'label' => 'Rows',
            'default' => 10,
            'option' => 'orders_per_page'
        );
        add_screen_option('per_page', $args);

        $WCMb->load_class('vendor-order-page');
        $this->wcmb_vendor_order_page = new WCMb_Vendor_Order_Page();
    }

    public function load_wcmb_shipping_handlers() {
        
    }

    /**
     * Generate Orders Page view 
     */
    public function wcmb_vendor_orders_page() {
        $this->wcmb_vendor_order_page->wcmb_prepare_order_page_items();
        ?>
        <div class="wrap">

            <div id="icon-woocommerce" class="icon32 icon32-woocommerce-reports"><br/></div>
            <h2><?php _e('Orders', 'MB-multivendor'); ?></h2>

            <form id="posts-filter" method="get">

                <input type="hidden" name="page" value="dc-vendor-orders"/>
        <?php $this->wcmb_vendor_order_page->display(); ?>

            </form>
            <div id="ajax-response"></div>
            <br class="clear"/>
        </div>
        <?php
    }

    function wcmb_product_options_shipping() {
        global $post;
        if (!is_user_wcmb_vendor(get_current_user_id())) {
            return;
        }
        $product_object = wc_get_product($post->ID);
        $args = array(
            'taxonomy' => 'product_shipping_class',
            'hide_empty' => 0,
            'meta_query' => array(
                array(
                    'key' => 'vendor_id',
                    'value' => get_current_vendor_id(),
                    'compare' => '='
                )
            ),
            'show_option_none' => __('No shipping class', 'MB-multivendor'),
            'name' => 'product_shipping_class',
            'id' => 'product_shipping_class',
            'selected' => $product_object->get_shipping_class_id('edit'),
            'class' => 'select short',
        );
        ?>
        <p class="form-field dimensions_field">
            <label for="product_shipping_class"><?php _e('Shipping class', 'MB-multivendor'); ?></label>
        <?php wp_dropdown_categories($args); ?>
        <?php echo wc_help_tip(__('Shipping classes are used by certain shipping methods to group similar products.', 'MB-multivendor')); ?>
        </p>
        <script type="text/javascript">
            jQuery('#product_shipping_class').closest("p").remove();
        </script>
        <?php
    }

    public function export_vendor_orders_csv() {
        global $wpdb;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['wcmb_download_vendor_order_csv'])) {
                $vendor = get_current_vendor();
                $order_data = array();
                $order_ids = isset($_POST['selected_orders']) ? $_POST['selected_orders'] : array();
                if ($order_ids && count($order_ids) > 0) {
                    foreach ($order_ids as $order_id) {
                        $vendor_orders = $wpdb->get_results("SELECT DISTINCT commission_id from `{$wpdb->prefix}wcmb_vendor_orders` where vendor_id = " . $vendor->id . " AND order_id = " . $order_id, ARRAY_A);
                        $commission_id = $vendor_orders[0]['commission_id'];
                        $order_data[$commission_id] = $order_id;
                    }
                    if (!empty($order_data)) {
                        $this->generate_csv($order_data, $vendor);
                    }
                } else {
                    wc_add_notice(__('Please select atleast one and more order.', 'MB-multivendor'), 'error');
                }
            }
        }
    }

    public function is_order_shipped($order_id, $vendor) {
        global $WCMb, $wpdb;
        $shipping_status = $wpdb->get_results("SELECT DISTINCT shipping_status from `{$wpdb->prefix}wcmb_vendor_orders` where vendor_id = " . $vendor->id . " AND order_id = " . $order_id, ARRAY_A);
        $shipping_status = $shipping_status[0]['shipping_status'];
        if ($shipping_status == 0)
            return false;
        if ($shipping_status == 1)
            return true;
    }

    public function save_store_settings($user_id, $post) {
        global $WCMb;
        $vendor = get_wcmb_vendor($user_id);
        $fields = $WCMb->user->get_vendor_fields($user_id);
        foreach ($fields as $fieldkey => $value) {

            if (isset($post[$fieldkey])) {
                if ($fieldkey == "vendor_page_slug" && !empty($post[$fieldkey])) {
                    if ($vendor && !$vendor->update_page_slug(wc_clean($_POST[$fieldkey]))) {
                        if (is_admin()) {
                            echo _e('Slug already exists', 'MB-multivendor');
                        } else {
                            $err_msg = __('Slug already exists', 'MB-multivendor');
                            return $err_msg;
                        }
                    } else {
                        update_user_meta($user_id, '_' . $fieldkey, wc_clean($post[$fieldkey]));
                    }
                    continue;
                }
                if ($fieldkey == "vendor_page_slug" && empty($post[$fieldkey])) {
                    if (is_admin()) {
                        echo _e('Slug can not be empty', 'MB-multivendor');
                    } else {
                        $err_msg = __('Slug can not be empty', 'MB-multivendor');
                        return $err_msg;
                    }
                }

                if ($fieldkey == 'vendor_description') {
                    update_user_meta($user_id, '_' . $fieldkey, $post[$fieldkey]);
                } elseif ($fieldkey == 'vendor_country') {
                    $country_code = $post[$fieldkey];
                    $country_data = WC()->countries->get_countries();
                    $country_name = ( isset($country_data[$country_code]) ) ? $country_data[$country_code] : $country_code; //To get country name by code
                    update_user_meta($user_id, '_' . $fieldkey, $country_name);
                    update_user_meta($user_id, '_' . $fieldkey . '_code', $country_code);
                } elseif ($fieldkey == 'vendor_state') {
                    $country_code = $post['vendor_country'];
                    $state_code = $post[$fieldkey];
                    $state_data = WC()->countries->get_states($country_code);
                    $state_name = ( isset($state_data[$state_code]) ) ? $state_data[$state_code] : $state_code; //to get State name by state code
                    update_user_meta($user_id, '_' . $fieldkey, $state_name);
                    update_user_meta($user_id, '_' . $fieldkey . '_code', $state_code);
                } else {
                    // social url validation
                    if (in_array($fieldkey, array('vendor_fb_profile', 'vendor_twitter_profile', 'vendor_google_plus_profile', 'vendor_linkdin_profile', 'vendor_youtube', 'vendor_instagram'))) {
                        if (!empty($post[$fieldkey]) && filter_var($post[$fieldkey], FILTER_VALIDATE_URL)) {
                            update_user_meta($user_id, '_' . $fieldkey, $post[$fieldkey]);
                        } else {
                            update_user_meta($user_id, '_' . $fieldkey, '');
                        }
                    } else {
                        update_user_meta($user_id, '_' . $fieldkey, $post[$fieldkey]);
                    }
                }
                if ($fieldkey == 'vendor_page_title' && empty($post[$fieldkey])) {
                    if (is_admin()) {
                        echo _e('Shop Title can not be empty', 'MB-multivendor');
                    } else {
                        $err_msg = __('Shop Title can not be empty', 'MB-multivendor');
                        return $err_msg;
                    }
                }
                if ($fieldkey == 'vendor_page_title') {
                    if (!$vendor->update_page_title(wc_clean($post[$fieldkey]))) {
                        if (is_admin()) {
                            echo _e('Shop Title Update Error', 'MB-multivendor');
                        } else {
                            $err_msg = __('Shop Title Update Error', 'MB-multivendor');
                            return $err_msg;
                        }
                    } else {
                        if (apply_filters('wcmb_update_user_display_name_with_vendor_store_name', false, $user_id)) {
                            wp_update_user(array('ID' => $user_id, 'display_name' => $post[$fieldkey]));
                        }
                    }
                }
            }
        }
        if (isset($_POST['_shop_template']) && !empty($_POST['_shop_template'])) {
            update_user_meta($user_id, '_shop_template', $_POST['_shop_template']);
        }
        if (isset($_POST['_store_location']) && !empty($_POST['_store_location'])) {
            update_user_meta($user_id, '_store_location', $_POST['_store_location']);
        }
        if (isset($_POST['store_address_components']) && !empty($_POST['store_address_components'])) {
            $address_components = wcmb_get_geocoder_components(json_decode(stripslashes($_POST['store_address_components']), true));
            if (isset($_POST['_store_location']) && !empty($_POST['_store_location'])) {
                $address_components['formatted_address'] = $_POST['_store_location'];
            }
            if (isset($_POST['_store_lat']) && !empty($_POST['_store_lat'])) {
                $address_components['latitude'] = $_POST['_store_lat'];
            }
            if (isset($_POST['_store_lng']) && !empty($_POST['_store_lng'])) {
                $address_components['longitude'] = $_POST['_store_lng'];
            }
            update_user_meta($user_id, '_store_address_components', $address_components);
        }
        if (isset($_POST['_store_lat']) && !empty($_POST['_store_lat'])) {
            update_user_meta($user_id, '_store_lat', $_POST['_store_lat']);
        }
        if (isset($_POST['_store_lng']) && !empty($_POST['_store_lng'])) {
            update_user_meta($user_id, '_store_lng', $_POST['_store_lng']);
        }
        if (isset($_POST['timezone_string']) && !empty($_POST['timezone_string'])) {
            if (!empty($_POST['timezone_string']) && preg_match('/^UTC[+-]/', $_POST['timezone_string'])) {
                $_POST['gmt_offset'] = $_POST['timezone_string'];
                $_POST['gmt_offset'] = preg_replace('/UTC\+?/', '', $_POST['gmt_offset']);
                $_POST['timezone_string'] = '';
            } else {
                $_POST['gmt_offset'] = 0;
            }
            update_user_meta($user_id, 'timezone_string', $_POST['timezone_string']);
            update_user_meta($user_id, 'gmt_offset', $_POST['gmt_offset']);
        }
    }

    /**
     * Save Vendor Shipping data
     * @global type $wcmb
     * @param type $vendor_user_id
     * @param type $post
     */
    public function save_vendor_shipping($vendor_user_id, $post) {
        global $WCMb;
        $all_allowed_countries = WC()->countries->get_allowed_countries();
        $location = array();
        $zone_id = 0;
        if (!empty($_POST['wcmb_shipping_zone'])) {
            foreach ($_POST['wcmb_shipping_zone'] as $shipping_zone) {
                if (isset($shipping_zone['_zone_id']) && $shipping_zone['_zone_id'] != 0) {
                    $zone_id = $shipping_zone['_zone_id'];

                    if (isset($shipping_zone['_limit_zone_location']) && $shipping_zone['_limit_zone_location']) {
                        if (!empty($shipping_zone['_select_zone_states'])) {
                            $state_array = array();
                            foreach ($shipping_zone['_select_zone_states'] as $zone_state) {
                                $state_array[] = array(
                                    'code' => $zone_state,
                                    'type' => 'state'
                                );
                            }

                            $location = array_merge($location, $state_array);
                        }

                        if (!empty($shipping_zone['_select_zone_postcodes'])) {
                            $postcode_array = array();
                            $zone_postcodes = array_map('trim', explode(',', $shipping_zone['_select_zone_postcodes']));
                            foreach ($zone_postcodes as $zone_postcode) {
                                $postcode_array[] = array(
                                    'code' => $zone_postcode,
                                    'type' => 'postcode'
                                );
                            }

                            $location = array_merge($location, $postcode_array);
                        }
                    }
                }
            }
        }
        if( !class_exists( 'WCMB_Shipping_Zone' ) ) {
            $WCMb->load_vendor_shipping();
        }
        WCMB_Shipping_Zone::save_location($location, $zone_id);

        $WCMb->load_class('shipping-gateway');
        WCMb_Shipping_Gateway::load_class('shipping-method');
        $vendor_shipping = new WCMB_Vendor_Shipping_Method();
        $vendor_shipping->process_admin_options();

        // clear shipping transient
        WC_Cache_Helper::get_transient_version('shipping', true);

    }

    /**
     * Save Vendor Profile data
     * @since 3.1.0
     * @global type $wcmb
     * @param type $vendor_user_id
     * @param type $post
     */
    public function save_vendor_profile($vendor_user_id, $post) {
        global $WCMb;
        if (isset($_POST['vendor_profile_data'])) {
            // preventing auth cookies from actually being sent to the client.
            add_filter('send_auth_cookies', '__return_false');

            $current_user = get_user_by('id', $vendor_user_id);
            $has_error = false;
            $userdata = array(
                'ID' => $vendor_user_id,
                //'user_email' => $_POST['vendor_profile_data']['user_email'],
                'first_name' => $_POST['vendor_profile_data']['first_name'],
                'last_name' => $_POST['vendor_profile_data']['last_name'],
            );

            $pass_cur = !empty( $_POST['vendor_profile_data']['password_current'] ) ? $_POST['vendor_profile_data']['password_current'] : '';
            $pass1 = !empty( $_POST['vendor_profile_data']['password_1'] ) ? $_POST['vendor_profile_data']['password_1'] : '';
            $pass2 = !empty( $_POST['vendor_profile_data']['password_2'] ) ? $_POST['vendor_profile_data']['password_2'] : '';
            $email = !empty( $_POST['vendor_profile_data']['user_email'] ) ? $_POST['vendor_profile_data']['user_email'] : '';
            $save_pass = true;
            
            if ( $email ) {
                $account_email = sanitize_email( $email );
                if ( ! is_email( $account_email ) ) {
                    $has_error = true;
                    wc_add_notice( __( 'Please provide a valid email address.', 'woocommerce' ), 'error' );
                } elseif ( email_exists( $account_email ) && $account_email !== $current_user->user_email ) {
                    $has_error = true;
                    wc_add_notice( __( 'This email address is already registered.', 'woocommerce' ), 'error' );
                }
                $userdata['user_email'] = $account_email;
            }

            if (!empty($pass_cur) && empty($pass1) && empty($pass2)) {
                $has_error = true;
                wc_add_notice( __('Please fill out all password fields.', 'MB-multivendor'), 'error' );
                $save_pass = false;
            } elseif (!empty($pass1) && empty($pass_cur)) {
                $has_error = true;
                wc_add_notice( __('Please enter your current password.', 'MB-multivendor'), 'error' );
                $save_pass = false;
            } elseif (!empty($pass1) && empty($pass2)) {
                $has_error = true;
                wc_add_notice( __('Please re-enter your password.', 'MB-multivendor'), 'error' );
                $save_pass = false;
            } elseif ((!empty($pass1) || !empty($pass2) ) && $pass1 !== $pass2) {
                $has_error = true;
                wc_add_notice( __('New passwords do not match.', 'MB-multivendor'), 'error' );
                $save_pass = false;
            } elseif (!empty($pass1) && !wp_check_password($pass_cur, $current_user->user_pass, $current_user->ID)) {
                $has_error = true;
                wc_add_notice( __('Your current password is incorrect.', 'MB-multivendor'), 'error' );
                $save_pass = false;
            }
            
            if( $has_error ) return;

            if ($pass1 && $save_pass) {
                $userdata['user_pass'] = $pass1;
            }

            $user_id = wp_update_user($userdata);

            $profile_updt = update_user_meta($vendor_user_id, '_vendor_profile_image', $_POST['vendor_profile_data']['vendor_profile_image']);

            if ($profile_updt || $user_id) {
                wc_add_notice( __('Profile Data Updated', 'MB-multivendor'), 'success' );
            }
        }
    }

    /**
     * Add vendor dashboard header navigation
     * @since 3.0.0
     */
    public function dashboard_header_nav() {
        $vendor = get_current_vendor();
        $header_nav = array(
            'shop-link' => array(
                'label' => __('My Shop', 'MB-multivendor')
                , 'url' => apply_filters('wcmb_vendor_shop_permalink', esc_url($vendor->permalink))
                , 'class' => ''
                , 'capability' => true
                , 'position' => 0
                , 'link_target' => '_blank'
                , 'nav_icon' => 'wcmb-font ico-my-shop-icon'
            ),


              
           
          

                 'dashboard'            => array(
                'label'       => __( 'Dashboard', 'MB-multivendor' )
                , 'url'         => esc_url(wcmb_get_vendor_dashboard_endpoint_url( 'dashboard' ))
                , 'capability'  =>  true 
                , 'position'    => 0
                ,'class' => true
                //, 'submenu'     => array()
                , 'link_target' => '_self'
                , 'nav_icon'    => 'wcmb-font ico-dashboard-icon'
            ),


            // 'profile' => array(
              //  'label' => __('Profile management', 'MB-multivendor')
                //, 'url' => esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_profile_endpoint', 'vendor', 'general', 'profile')))
                //, 'class' => ''
                //, 'capability' => true
                //, 'position' => 20
                //, 'link_target' => '_self'
                //, 'nav_icon' => 'wcmb-font ico-user-icon'
            //),
                   
                      
                    'vendor-policies' => array(
                        'label'       => __( 'Policies', 'MB-multivendor' )
                        , 'url'         => esc_url(wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_vendor_policies_endpoint', 'vendor', 'general', 'vendor-policies' ) ))
                        , 'capability'  => true
                        , 'position'    => 30
                         , 'class' => ''
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'wcmb-font ico-policies-icon'
                    ),
                    'vendor-billing'  => array(
                        'label'       => __( 'Billing', 'MB-multivendor' )
                        , 'url'         => esc_url(wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_vendor_billing_endpoint', 'vendor', 'general', 'vendor-billing' ) ))
                        , 'capability'  =>  true 
                        , 'position'    => 40
                         , 'class' => ''
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'wcmb-font ico-billing-icon'
                    ),
                   // 'vendor-shipping' => array(
                     //   'label'       => __( 'Shipping', 'MB-multivendor' )
                       // , 'url'         => esc_url(wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_vendor_shipping_endpoint', 'vendor', 'general', 'vendor-shipping' ) ))
                        //, 'capability'  => false
                        //, 'position'    => 50
                        // , 'class' => ''
                        //, 'link_target' => '_self'
                        //, 'nav_icon'    => 'wcmb-font ico-shippingnew-icon'
                    //),


                    'products'    => array(
                        'label'       => __( 'Products', 'MB-multivendor' )
                        , 'url'         => apply_filters( 'wcmb_vendor_products',esc_url(wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_products_endpoint', 'vendor', 'general', 'products' ) ) ))
                        , 'capability'  => true
                        , 'position'    => 50
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'wcmb-font ico-product-manager-icon'
                    ),
                    //'add-product' => array(
                    //    'label'       => __( 'Add Product', 'MB-multivendor' )
                    //    , 'url'         => apply_filters( 'wcmb_vendor_dashboard_add_product_url', esc_url(wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_add_product_endpoint', 'vendor', 'general', 'add-product' ) ) ))
                    //    , 'capability'  => true
                    //    , 'position'    => 80
                    //    , 'link_target' => '_self'
                    //    , 'nav_icon'    => 'wcmb-font ico-add-product-icon'
                   // ),
                
                
            
            
                
                    'coupons'    => array(
                        'label'       => __( 'Coupons', 'MB-multivendor' )
                        , 'url'         => apply_filters( 'wcmb_vendor_coupons', esc_url(wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_coupons_endpoint', 'vendor', 'general', 'coupons' ) ) ))
                        , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_vendor_coupons_capability', 'edit_shop_coupons' )
                        , 'position'    => 60
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'wcmb-font ico-coupons-icon'
                    ),
                    //'add-coupon' => array(
                      //  'label'       => __( 'Add Coupon', 'MB-multivendor' )
                        //, 'url'         => apply_filters( 'wcmb_vendor_submit_coupon', esc_url(wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_add_coupon_endpoint', 'vendor', 'general', 'add-coupon' ) ) ))
                        //, 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_add_coupon_capability', 'edit_shop_coupons' )
                        //, 'position'    => 70
                        //, 'link_target' => '_self'
                        //, 'nav_icon'    => 'wcmb-font ico-addcoupon-icon'
                    //),
            

                    'vendor-report' => array(
                        'label'       => __( 'Overview', 'MB-multivendor' )
                        , 'url'         => esc_url(wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_vendor_report_endpoint', 'vendor', 'general', 'vendor-report' ) ))
                        , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_vendor_report_capability', true )
                        , 'position'    => 80
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'wcmb-font ico-reports-icon'
                    ),
                
               
            'vendor-orders'        => array(
                'label'       => __( 'Orders', 'MB-multivendor' )
                , 'url'         => esc_url(wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_vendor_orders_endpoint', 'vendor', 'general', 'vendor-orders' ) ))
                , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_vendor_orders_capability', true )
                , 'position'    => 90
                , 'submenu'     => array()
                , 'link_target' => '_self'
                , 'nav_icon'    => 'wcmb-font ico-orders-icon'
            ),
            'vendor-payments'      => array(
                'label'       => __( 'Payments', 'MB-multivendor' )
                , 'url'         => '#'
                , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_vendor_payments_capability', true )
                , 'position'    => 100),
                
                    'vendor-withdrawal'   => array(
                        'label'       => __( 'Withdrawal', 'MB-multivendor' )
                        , 'url'         => esc_url(wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_vendor_withdrawal_endpoint', 'vendor', 'general', 'vendor-withdrawal' ) ))
                        , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_vendor_withdrawal_capability', false )
                        , 'position'    => 110
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'wcmb-font ico-revenue-icon'
                    ),
                    'transaction-details' => array(
                        'label'       => __( 'History', 'MB-multivendor' )
                        , 'url'         => esc_url(wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_transaction_details_endpoint', 'vendor', 'general', 'transaction-details' ) ))
                        , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_transaction_details_capability', true )
                        , 'position'    => 120
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'wcmb-font ico-history-icon'
                    ),
            
            //'vendor-knowledgebase' => array(
              //  'label'       => __( 'Knowledgebase', 'MB-multivendor' )
               // , 'url'         => wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_vendor_knowledgebase_endpoint', 'vendor', 'general', 'vendor-knowledgebase' ) )
               // , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_vendor_knowledgebase_capability', true )
               // , 'position'    => 70
              //  , 'submenu'     => array()
                //, 'link_target' => '_self'
                //, 'nav_icon'    => 'wcmb-font ico-knowledgebase-icon'
            //),
           
            
           // 'logout' => array(
             //   'label' => __('Logout', 'MB-multivendor')
               // , 'url' => esc_url(wp_logout_url(get_permalink(wcmb_vendor_dashboard_page_id())))
                //, 'class' => ''
                //, 'capability' => true
                //, 'position' => 40
                //, 'link_target' => '_self'
                //, 'nav_icon' => 'wcmb-font ico-logout-icon'
            //)
           // 'add-product' => array(
             //   'label' => __('Add Product', 'MB-multivendor')
               // , 'url' => apply_filters('wcmb_vendor_submit_product', esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_add_product_endpoint', 'vendor', 'general', 'add-product'))))
                //, 'class' => ''
                //, 'capability' => apply_filters('wcmb_vendor_dashboard_menu_add_product_capability', 'edit_products')
                //, 'position' => 20
                //, 'link_target' => '_self'
                //, 'nav_icon' => 'wcmb-font ico-product-icon'
            //),
            //'orders' => array(
              //  'label' => __('Orders', 'MB-multivendor')
               // , 'url' => esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_vendor_orders_endpoint', 'vendor', 'general', 'vendor-orders')))
                //, 'class' => ''
                //, 'capability' => true
                //, 'position' => 30
                //, 'link_target' => '_self'
                //, 'nav_icon' => 'wcmb-font ico-orders-icon'
            //),
            //'announcement' => array(
              //  'label' => __('Announcement', 'MB-multivendor')
                //, 'url' => esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_vendor_announcements_endpoint', 'vendor', 'general', 'vendor-announcements')))
               // , 'class' => ''
                //, 'capability' => apply_filters('wcmb_show_vendor_announcements', true)
                //, 'position' => 40
                //, 'link_target' => '_self'
                //, 'nav_icon' => 'wcmb-font ico-announcement-icon'
            //)
        );
        return apply_filters('wcmb_vendor_dashboard_header_nav', $header_nav);
    }

    /**
     * Add vendor dashboard header right panel navigation
     * @since 3.0.0
     */
    public function dashboard_header_right_panel_nav() {
        $panel_nav = array(
            /*'storefront' => array(
                'label' => __('Storefront', 'MB-multivendor')
                , 'url' => esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_store_settings_endpoint', 'vendor', 'general', 'storefront')))
                , 'class' => ''
                , 'capability' => true
                , 'position' => 10
                , 'link_target' => '_self'
                , 'nav_icon' => 'wcmb-font ico-storefront-icon'
            ),
            'profile' => array(
                'label' => __('Profile management', 'MB-multivendor')
                , 'url' => esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_profile_endpoint', 'vendor', 'general', 'profile')))
                , 'class' => ''
                , 'capability' => true
                , 'position' => 20
                , 'link_target' => '_self'
                , 'nav_icon' => 'wcmb-font ico-user-icon'
            ),*/
             'storefront'      => array(
                        'label'       => __( 'Storefront', 'MB-multivendor' )
                        , 'url'         => esc_url(wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_store_settings_endpoint', 'vendor', 'general', 'storefront' ) ))
                        , 'capability'  => true 
                        , 'position'    => 20
                        , 'class' => ''
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'wcmb-font ico-storefront-icon'
                    ),
            'wp-admin' => array(
                'label' => __('WordPress backend', 'MB-multivendor')
                , 'url' => esc_url(admin_url())
                , 'class' => ''
                , 'capability' => true
                , 'position' => 10
                , 'link_target' => '_self'
                , 'nav_icon' => 'wcmb-font ico-wp-backend-icon'
            ),
             'vendor-tools'         => array(
                'label'       => __( 'others', 'MB-multivendor' )
                , 'url'         => esc_url(wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_vendor_tools_endpoint', 'vendor', 'general', 'vendor-tools' ) ))
                , 'capability'  =>  true 
                , 'class' => ''
                , 'position'    => 10
                , 'link_target' => '_self'
                , 'nav_icon'    => 'wcmb-font ico-tools-icon'
            ),
            'logout' => array(
                'label' => __('Logout', 'MB-multivendor')
                , 'url' => esc_url(wp_logout_url(get_permalink(wcmb_vendor_dashboard_page_id())))
                , 'class' => ''
                , 'capability' => true
                , 'position' => 20
                , 'link_target' => '_self'
                , 'nav_icon' => 'wcmb-font ico-logout-icon'
            ),




















                
               
            

        );
        return apply_filters('wcmb_vendor_dashboard_header_right_panel_nav', $panel_nav);
    }

    /**
     * Add vendor dashboard widgets
     * @since 3.0.0
     */
    public function wcmb_dashboard_setup() {
        $vendor = get_wcmb_vendor(get_current_user_id());
        $this->wcmb_add_dashboard_widget('wcmb_vendor_stats_reports', '', array(&$this, 'wcmb_vendor_stats_reports'), 'full');
        $trans_details_widget_args = array();
        if (apply_filters('wcmb_vendor_dashboard_menu_vendor_withdrawal_capability', false)) {
            $trans_details_widget_args['action'] = array('title' => __('Withdrawal', 'MB-multivendor'), 'link' => esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_vendor_withdrawal_endpoint', 'vendor', 'general', 'vendor-withdrawal'))));
        }
        $this->wcmb_add_dashboard_widget('wcmb_vendor_transaction_details', __('Transaction Details', 'MB-multivendor'), array(&$this, 'wcmb_vendor_transaction_details'), 'side', array(), $trans_details_widget_args);
        $visitor_map_filter_attr = apply_filters('wcmb_vendor_visitors_map_filter_attr', array(
            '7' => __('Last 7 days', 'MB-multivendor'),
            '30' => __('Last 30 days', 'MB-multivendor'),
        ));
        $visitor_map_filter = '<div class="widget-action-area pull-right">
            <select id="wcmb_visitor_stats_date_filter" class="form-control">';
        if ($visitor_map_filter_attr) {
            foreach ($visitor_map_filter_attr as $key => $value) {
                $visitor_map_filter .= '<option value="' . $key . '">' . $value . '</option>';
            }
        }
        $visitor_map_filter .= '</select>
        </div>';
        $this->wcmb_add_dashboard_widget('wcmb_vendor_visitors_map', __('Visitors Map', 'MB-multivendor'), array(&$this, 'wcmb_vendor_visitors_map'), 'normal', '', array('action' => array('html' => $visitor_map_filter)));
        if ($vendor->is_shipping_enable()):
            $this->wcmb_add_dashboard_widget('wcmb_vendor_pending_shipping', __('Pending Shipping', 'MB-multivendor'), array(&$this, 'wcmb_vendor_pending_shipping'));
        endif;
        if (current_user_can('edit_products')) {
            $this->wcmb_add_dashboard_widget('wcmb_vendor_product_stats', __('Product Stats', 'MB-multivendor'), array(&$this, 'wcmb_vendor_product_stats'), 'side', '', array('action' => array('title' => __('Add Product', 'MB-multivendor'), 'link' => esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_add_product_endpoint', 'vendor', 'general', 'add-product'))))));
            $this->wcmb_add_dashboard_widget('wcmb_vendor_product_sales_report', __('Product Sales Report', 'MB-multivendor'), array(&$this, 'wcmb_vendor_product_sales_report'));
        }
        if (get_wcmb_vendor_settings('is_sellerreview', 'general') == 'Enable') {
            $this->wcmb_add_dashboard_widget('wcmb_customer_reviews', __('Reviews', 'MB-multivendor'), array(&$this, 'wcmb_customer_review'));
        }
        $this->wcmb_add_dashboard_widget('wcmb_vendor_products_cust_qna', __('Customer Questions', 'MB-multivendor'), array(&$this, 'wcmb_vendor_products_cust_qna'), 'side', '', array('action' => array('title' => __('Show All Q&As', 'MB-multivendor'), 'link' => esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_vendor_products_qnas_endpoint', 'vendor', 'general', 'products-qna'))))));
    }

    /**
     * Register new vendor dashboard widget
     * @global array $wcmb_dashboard_widget
     * @param string $widget_id
     * @param string $widget_title
     * @param callable $callback
     * @param string $context
     * @param int $priority
     * @param array $callback_args
     * @since 3.0.0
     */
    public function wcmb_add_dashboard_widget($widget_id, $widget_title, $callback, $context = 'normal', $callback_args = null, $args = array()) {
        global $wcmb_dashboard_widget;
        if (!is_user_wcmb_vendor(get_current_vendor_id())) {
            return;
        }
        if (!isset($wcmb_dashboard_widget)) {
            $wcmb_dashboard_widget = array();
        }
        if (!isset($wcmb_dashboard_widget[$context])) {
            $wcmb_dashboard_widget[$context] = array();
        }
        $wcmb_dashboard_widget[$context][$widget_id] = array(
            'id' => $widget_id,
            'title' => $widget_title,
            'callback' => $callback,
            'calback_args' => $callback_args,
            'args' => $args
        );
    }

    /**
     * Output vendor dashboard widgets
     * @global array $wcmb_dashboard_widget
     * @since 3.0.0
     */
    public function do_wcmb_dashboard_widget($place) {
        global $wcmb_dashboard_widget;
        if (!$wcmb_dashboard_widget) {
            return;
        }
        $wcmb_dashboard_widget = apply_filters('before_wcmb_dashboard_widget', $wcmb_dashboard_widget);
        if ($wcmb_dashboard_widget) {
            foreach ($wcmb_dashboard_widget as $context => $dashboard_widget) {
                if ($place == $context) {
                    foreach ($dashboard_widget as $widget_id => $widget) {
                        echo '<div class="panel panel-default pannel-outer-heading wcmb-dash-widget ' . $widget_id . '">';
                        $this->build_widget_header($widget['title'], $widget['args']);
                        echo '<div class="panel-body">';
                        call_user_func($widget['callback'], $widget['calback_args']);
                        echo '</div>';
                        $this->build_widget_footer($widget['args']);
                        echo '</div>';
                    }
                }
            }
        }
    }

    public function build_widget_header($title, $args = array()) {
        $default = array(
            'icon' => '',
            'action' => array()
        );
        $args = array_merge($default, $args);
        if (!empty($title)) {
            ?>
            <div class="panel-heading">
                <h3 class="pull-left">
            <?php if (!empty($args['icon'])) : ?>
                        <span class="icon_stand dashicons-before <?php echo $args['icon']; ?>"></span>
                    <?php endif; ?>
                    <?php echo $title; ?>
                </h3>
            </div>
            <div class="clearfix"></div>
            <?php
        }
    }

    public function build_widget_footer($args = array()) {
        $default = array(
            'icon' => '',
            'action' => array()
        );
        $args = array_merge($default, $args);
        if (!empty($args['action'])) {
            ?>
            <div class="panel-footer">
            <?php if (isset($args['action']['link']) && isset($args['action']['title'])) { ?>
                    <a href="<?php echo $args['action']['link']; ?>" class="footer-link">
                    <?php
                    if (isset($args['action']['icon'])) {
                        echo '<span class="icon_stand dashicons-before ' . $args['action']['icon'] . '"></span>';
                    }
                    ?>
                        <?php echo $args['action']['title']; ?>
                        <i class="wcmb-font ico-right-arrow-icon"></i>
                    </a>
                        <?php
                    } if (isset($args['action']['html'])) {
                        echo $args['action']['html'];
                    }
                    ?>
            </div>
            <div class="clearfix"></div>
                <?php
            }
        }

        public function wcmb_vendor_stats_reports($args = array()) {
            global $WCMb;
            $vendor = get_current_vendor();
            $vendor_report_data = get_wcmb_vendor_dashboard_stats_reports_data();
            $default_data = array();
            $default_data['stats_reports_periods'] = apply_filters('wcmb_vendor_stats_reports_periods', array(
                '7' => __('Last 7 days', 'MB-multivendor'),
                '30' => __('Last 30 days', 'MB-multivendor'),
            ));
            $default_data['vendor_report_data'] = $vendor_report_data;
            $default_data['payment_mode'] = ucwords(str_replace('_', ' ', $vendor->payment_mode));
            $WCMb->template->get_template('vendor-dashboard/dashboard-widgets/wcmb_vendor_stats_reports.php', $default_data);
        }

        public function wcmb_vendor_pending_shipping($args = array()) {
            global $WCMb;
            $vendor = get_wcmb_vendor(get_current_user_id());
            $today = @date('Y-m-d 00:00:00', strtotime("+1 days"));
            $last_seven_day_date = date('Y-m-d H:i:s', strtotime('-7 days'));
            // Mark as shipped
            if (isset($_POST['wcmb-submit-mark-as-ship'])) {
                $order_id = $_POST['order_id'];
                $tracking_id = $_POST['tracking_id'];
                $tracking_url = $_POST['tracking_url'];
                $vendor->set_order_shipped($order_id, $tracking_id, $tracking_url);
            }

            $default_headers = apply_filters('wcmb_vendor_pending_shipping_table_header', array(
                'order_id' => __('Order ID', 'MB-multivendor'),
                'products_name' => __('Product', 'MB-multivendor'),
                'order_date' => __('Order Date', 'MB-multivendor'),
                'shipping_address' => __('Address', 'MB-multivendor'),
                'shipping_amount' => __('Charges', 'MB-multivendor'),
                'action' => __('Action', 'MB-multivendor'),
            ));
            $WCMb->template->get_template('vendor-dashboard/dashboard-widgets/wcmb_vendor_pending_shipping.php', array('default_headers' => $default_headers));
        }

        public function wcmb_customer_review() {
            global $WCMb;
            $WCMb->template->get_template('vendor-dashboard/dashboard-widgets/wcmb_customer_review.php');
        }

        public function wcmb_vendor_product_stats($args = array()) {
            global $WCMb;
            $publish_products_count = 0;
            $pending_products_count = 0;
            $draft_products_count = 0;
            $trashed_products_count = 0;

            $user_id = get_current_user_id();

            $args = array('post_status' => array('publish', 'pending', 'draft', 'trash'));
            $vendor = get_wcmb_vendor(absint($user_id));
            $product_stats = array();
            $products = $vendor->get_products($args);
            $product_stats['total_products'] = count($products);
            foreach ($products as $key => $value) {
                $product_id = $value->ID;
                $product = wc_get_product($product_id);
                $vendor = get_wcmb_product_vendors($product_id);
                if (!empty($vendor) && $vendor->id == $user_id) {
                    if ($value->post_status == 'publish')
                        $publish_products_count += 1;
                    if ($value->post_status == 'pending')
                        $pending_products_count += 1;
                    if ($value->post_status == 'draft')
                        $draft_products_count += 1;
                    if ($value->post_status == 'trash') {
                        $trashed_products_count += 1;
                    }
                }
            }
            $product_stats['publish_products_count'] = $publish_products_count;
            $product_stats['pending_products_count'] = $pending_products_count;
            $product_stats['draft_products_count'] = $draft_products_count;
            $product_stats['trashed_products_count'] = $trashed_products_count;

            $product_stats['product_page_url'] = wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_products_endpoint', 'vendor', 'general', 'products'));

// variables to send $product_page_url $publish_products_count $pending_products_count $trashed_products_count
            //require_once(plugin_dir_path( __FILE__ ) . "wcmb_vendor_published_pending_trashed_products.php");
            $WCMb->template->get_template('vendor-dashboard/dashboard-widgets/wcmb_vendor_product_stats.php', $product_stats);
        }

        public function wcmb_vendor_product_sales_report() {
            global $WCMb;
            $WCMb->template->get_template('vendor-dashboard/dashboard-widgets/wcmb_vendor_product_sales_report.php');
        }

        function wcmb_vendor_transaction_details() {
            global $WCMb;
            $total_amount = 0;
            $transaction_display_array = array();
            $vendor = get_wcmb_vendor(get_current_vendor_id());
            $requestData = $_REQUEST;
            $vendor = apply_filters('wcmb_transaction_vendor', $vendor);
            $start_date = isset($requestData['from_date']) ? $requestData['from_date'] : date('01-m-Y');
            $end_date = isset($requestData['to_date']) ? $requestData['to_date'] : date('t-m-Y');
            $transaction_details = $WCMb->transaction->get_transactions($vendor->term_id);
            $unpaid_orders = get_wcmb_vendor_order_amount(array('commission_status' => 'unpaid'), $vendor->id);
            $count = 0; // varible for counting 5 transaction details
            foreach ($transaction_details as $transaction_id => $details) {
                $count++;
                if ($count <= 5) {
                    //$transaction_display_array[$transaction_id] = $details['total_amount'];
                    //$transaction_display_array['id'] = $transaction_id;
                    $transaction_display_array[$transaction_id]['transaction_date'] = wcmb_date($details['post_date']);
                    $transaction_display_array[$transaction_id]['total_amount'] = $details['total_amount'];
                }

                $total_amount = $total_amount + $details['total_amount'];
            }
            //print_r($total_amount);
            $WCMb->template->get_template('vendor-dashboard/dashboard-widgets/wcmb_vendor_transaction_details.php', array('total_amount' => $unpaid_orders['total'], 'transaction_display_array' => $transaction_display_array));
        }

        public function wcmb_vendor_products_cust_qna() {
            global $WCMb;
            $WCMb->template->get_template('vendor-dashboard/dashboard-widgets/wcmb_vendor_products_cust_qna.php');
        }

        public function wcmb_vendor_visitors_map() {
            global $WCMb;
            $WCMb->library->load_jqvmap_script_lib();
            $vendor = get_current_vendor();
            $visitor_map_stats = get_wcmb_vendor_dashboard_visitor_stats_data($vendor->id);
            $visitor_map_stats['init'] = array('map' => 'world_en', 'background_color' => false, 'color' => '#a0a0a0', 'hover_color' => false, 'hover_opacity' => 0.7);
            //wp_enqueue_script('wcmb_gchart_loader', '//www.gstatic.com/charts/loader.js');
            wp_enqueue_script('wcmb_visitor_map_data', $WCMb->plugin_url . 'assets/frontend/js/wcmb_vendor_map_widget_data.js', apply_filters('wcmb_vendor_visitors_map_script_dependancies', array('jquery', 'wcmb-vmap-world-script')));
            wp_localize_script('wcmb_visitor_map_data', 'visitor_map_stats', apply_filters('wcmb_vendor_visitors_map_script_data', $visitor_map_stats));
            $WCMb->template->get_template('vendor-dashboard/dashboard-widgets/wcmb_vendor_visitors_map.php');
        }

        public function wcmb_dashboard_setup_updater() {
            global $WCMb;
            $has_updated_store_addresses = get_user_meta(get_current_user_id(), '_vendor_store_country_state_updated', true);
            $has_rejected_store_updater = get_user_meta(get_current_user_id(), '_vendor_rejected_store_country_state_update', true);
            $has_country = get_user_meta(get_current_user_id(), '_vendor_country', true);
            $has_country_code = get_user_meta(get_current_user_id(), '_vendor_country_code', true);
            if ($has_country && !$has_country_code && !$has_updated_store_addresses && !$has_rejected_store_updater && !$WCMb->endpoints->get_current_endpoint()) {
                ?>
            <div class="modal fade" id="vendor-setuo-updater-info-modal" role="dialog" data-backdrop="static" data-keyboard="false" aria-hidden="true">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <form method="post">
                            <div class="modal-header">
                                <h4 class="modal-title"><?php _e("Update your store country and state.", 'MB-multivendor'); ?></h4>
                            </div>
                            <div class="modal-body">
            <?php wp_nonce_field('wcmb-vendor-store-updater'); ?>
                                <div class="form-group">
                                    <label><?php _e('Store Country', 'MB-multivendor'); ?></label>
                                    <select name="vendor_country" id="vendor_country" class="country_to_state user-profile-fields form-control inp-btm-margin regular-select" rel="vendor_country">
                                        <option value=""><?php _e('Select a country&hellip;', 'MB-multivendor'); ?></option>
            <?php
            $country_code = get_user_meta(get_current_user_id(), '_vendor_country_code', true);
            foreach (WC()->countries->get_shipping_countries() as $key => $value) {
                echo '<option value="' . esc_attr($key) . '"' . selected(esc_attr($country_code), esc_attr($key), false) . '>' . esc_html($value) . '</option>';
            }
            ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><?php _e('Store state', 'MB-multivendor'); ?></label>
            <?php
            $country_code = get_user_meta(get_current_user_id(), '_vendor_country_code', true);
            $states = WC()->countries->get_states($country_code);
            ?>
                                    <select name="vendor_state" id="vendor_state" class="state_select user-profile-fields form-control inp-btm-margin regular-select" rel="vendor_state">
                                        <option value=""><?php esc_html_e('Select a state&hellip;', 'MB-multivendor'); ?></option>
                                    <?php
                                    $state_code = get_user_meta(get_current_user_id(), '_vendor_state_code', true);
                                    if ($states):
                                        foreach ($states as $ckey => $cvalue) {
                                            echo '<option value="' . esc_attr($ckey) . '" ' . selected($state_code, $ckey, false) . '>' . esc_html($cvalue) . '</option>';
                                        }
                                    endif;
                                    ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="submit" class="update btn btn-default" name="do_update_store_address" value="<?php _e("Update", 'MB-multivendor'); ?>"/>
                                <input type="submit" class="skip btn btn-secondary" name="do_reject_store_updater" value="<?php _e("Skip", 'MB-multivendor'); ?>"/>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    //this remove the close button on top if you need
                    $('#vendor-setuo-updater-info-modal').find('.close').remove();
                    //this unbind the event click on the shadow zone
                    $('#vendor-setuo-updater-info-modal').unbind('click');
                    $("#vendor-setuo-updater-info-modal").modal('show');
                });
            </script>
            <?php
        }
    }

    public function vendor_updater_handler() {
        $wpnonce = isset($_REQUEST['_wpnonce']) ? $_REQUEST['_wpnonce'] : '';
        if ($wpnonce && wp_verify_nonce($wpnonce, 'wcmb-vendor-store-updater')) {
            $do_update = filter_input(INPUT_POST, 'do_update_store_address');
            $do_skip = filter_input(INPUT_POST, 'do_reject_store_updater');
            $country_code = filter_input(INPUT_POST, 'vendor_country');
            $state_code = filter_input(INPUT_POST, 'vendor_state');

            if ($do_update) {
                $country_data = WC()->countries->get_countries();
                $state_data = WC()->countries->get_states($country_code);
                $country_name = ( isset($country_data[$country_code]) ) ? $country_data[$country_code] : $country_code; //To get country name by code
                $state_name = ( isset($state_data[$state_code]) ) ? $state_data[$state_code] : $state_code; //to get State name by state code

                update_user_meta(get_current_user_id(), '_vendor_country', $country_name);
                update_user_meta(get_current_user_id(), '_vendor_country_code', $country_code);
                update_user_meta(get_current_user_id(), '_vendor_state', $state_name);
                update_user_meta(get_current_user_id(), '_vendor_state_code', $state_code);
                update_user_meta(get_current_user_id(), '_vendor_store_country_state_updated', true);
            } elseif ($do_skip) {
                update_user_meta(get_current_user_id(), '_vendor_rejected_store_country_state_update', true);
            }
            wp_redirect(esc_url_raw(get_permalink(wcmb_vendor_dashboard_page_id())));
            die();
        }
    }
    
    /**
     * Save product
     * @ since version 3.2.3
     */
    public function save_product() {
        global $WCMb;
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $current_endpoint_key = $WCMb->endpoints->get_current_endpoint();
            // retrive the actual endpoint name in case admn changes that from settings
            $current_endpoint = get_wcmb_vendor_settings( 'wcmb_' . str_replace( '-', '_', $current_endpoint_key ) . '_endpoint', 'vendor', 'general', $current_endpoint_key );
            // retrive edit-product endpoint name in case admn changes that from settings
            $edit_product_endpoint = get_wcmb_vendor_settings( 'wcmb_edit_product_endpoint', 'vendor', 'general', 'edit-product' );
            //Return if not edit product endpoint
            if ( $current_endpoint !== $edit_product_endpoint || ! isset( $_POST['wcmb_product_nonce'] ) ) {
                return;
            }
            
            $vendor_id = get_current_user_id();

            if ( !is_user_wcmb_vendor($vendor_id) || ! current_user_can( 'edit_products' ) || empty( $_POST['post_ID'] ) || ! wp_verify_nonce( $_POST['wcmb_product_nonce'], 'wcmb-product' ) ) {
                wp_die( -1 );
            }
            $errors = array();
            $product_id = intval( $_POST['post_ID'] );
            $post_object = get_post( $product_id );
            $product = wc_get_product( $product_id );

            if ( ! $product->get_id() || ! $post_object || 'product' !== $post_object->post_type ) {
                wp_die( __( 'Invalid product.', 'woocommerce' ) );
            }

            if ( ! $product->get_date_created( 'edit' ) ) {
                $product->set_date_created( current_time( 'timestamp', true ) );
            }

            $title = ( is_product_wcmb_spmv($product_id) && isset( $_POST['original_post_title'] ) ) ? wc_clean( $_POST['original_post_title'] ) : isset( $_POST['post_title'] ) ? wc_clean( $_POST['post_title'] ) : '';

            if ( isset( $_POST['status'] ) && $_POST['status'] === 'draft' ) {
                $status = 'draft';
            } elseif ( isset( $_POST['status'] ) && $_POST['status'] === 'publish' ) {
                if ( ! current_user_can( 'publish_products' ) ) {
                    $status = 'pending';
                } else {
                    $status = 'publish';
                }
            } else {
                wp_die( __( 'Invalid product status.', 'MB-multivendor' ) );
            }

            $post_data = apply_filters( 'wcmb_submitted_product_data', array(
                'ID'            => $product_id,
                'post_title'    => $title,
                'post_content'  => stripslashes( html_entity_decode( $_POST['product_description'], ENT_QUOTES, get_bloginfo( 'charset' ) ) ),
                'post_excerpt'  => stripslashes( html_entity_decode( $_POST['product_excerpt'], ENT_QUOTES, get_bloginfo( 'charset' ) ) ),
                'post_status'   => $status,
                'post_type'     => 'product',
                'post_author'   => $vendor_id,
                'post_date'     => gmdate( 'Y-m-d H:i:s', $product->get_date_created( 'edit' )->getOffsetTimestamp() ),
                'post_date_gmt' => gmdate( 'Y-m-d H:i:s', $product->get_date_created( 'edit' )->getTimestamp() ),
                ), $_POST );

            do_action( 'wcmb_before_post_update' );

            $post_id = wp_update_post( $post_data, true );

            if ( $post_id && ! is_wp_error( $post_id ) ) {

                // Set Product Featured Image
                $featured_img = ! empty( $_POST['featured_img'] ) ? wc_clean( absint( $_POST['featured_img'] ) ) : '';
                if ( $featured_img ) {
                    set_post_thumbnail( $post_id, $featured_img );
                } else {
                    delete_post_thumbnail( $post_id );
                }

                // Set Product Image Gallery
                $attachment_ids = isset( $_POST['product_image_gallery'] ) ? explode( ',', wc_clean( $_POST['product_image_gallery'] ) ) : array();

                $attachment_ids = array_filter( $attachment_ids, function( $attachment_id ) {
                    //image validity check
                    $attachment = wp_get_attachment_image( $attachment_id );
                    return ! empty( $attachment );
                } );

                update_post_meta( $post_id, '_product_image_gallery', implode( ',', $attachment_ids ) );

                // Policy tab data save
                if ( get_wcmb_vendor_settings( 'is_policy_on', 'general' ) == 'Enable' && apply_filters( 'wcmb_vendor_can_overwrite_policies', true ) ) {
                    if ( apply_filters( 'can_vendor_edit_shipping_policy_field', true ) && isset( $_POST['_wcmb_shipping_policy'] ) ) {
                        update_post_meta( $post_id, '_wcmb_shipping_policy', stripslashes( html_entity_decode( $_POST['_wcmb_shipping_policy'], ENT_QUOTES, get_bloginfo( 'charset' ) ) ) );
                    }
                    if ( apply_filters( 'can_vendor_edit_refund_policy_field', true ) && isset( $_POST['_wcmb_refund_policy'] ) ) {
                        update_post_meta( $post_id, '_wcmb_refund_policy', stripslashes( html_entity_decode( $_POST['_wcmb_refund_policy'], ENT_QUOTES, get_bloginfo( 'charset' ) ) ) );
                    }
                    if ( apply_filters( 'can_vendor_edit_cancellation_policy_field', true ) && isset( $_POST['_wcmb_cancallation_policy'] ) ) {
                        update_post_meta( $post_id, '_wcmb_cancallation_policy', stripslashes( html_entity_decode( $_POST['_wcmb_cancallation_policy'], ENT_QUOTES, get_bloginfo( 'charset' ) ) ) );
                    }
                }
                
                // Process product type first so we have the correct class to run setters.
                $product_type = empty( $_POST['product-type'] ) ? WC_Product_Factory::get_product_type( $post_id ) : sanitize_title( stripslashes( $_POST['product-type'] ) );

                wp_set_object_terms( $post_id, $product_type, 'product_type' );

                // Set Product Catagories
                $catagories = isset( $_POST['tax_input']['product_cat'] ) ? array_filter( array_map( 'intval', (array) $_POST['tax_input']['product_cat'] ) ) : array();
                wp_set_object_terms( $post_id, $catagories, 'product_cat' );
                // if product has different multi level categories hierarchy, save the default
                if( isset( $_POST['_default_cat_hierarchy_term_id'] ) && in_array( $_POST['_default_cat_hierarchy_term_id'], $catagories ) ){
                    update_post_meta( $post_id, '_default_cat_hierarchy_term_id', absint( $_POST['_default_cat_hierarchy_term_id'] ) );
                }else{
                    delete_post_meta( $post_id, '_default_cat_hierarchy_term_id' );
                }
                // Set Product Tags
                $tags = isset( $_POST['tax_input']['product_tag'] ) ? wp_parse_id_list( $_POST['tax_input']['product_tag'] ) : array();
                wp_set_object_terms( $post_id, $tags, 'product_tag' );

                $custom_terms = isset( $_POST['tax_input'] ) ? array_diff_key( $_POST['tax_input'], array_flip( array( 'product_cat', 'product_tag' ) ) ) : array();
                // Set Product Custom Terms
                if ( ! empty( $custom_terms ) ) {
                    foreach ( $custom_terms as $term => $value ) {
                        $custom_term = isset( $_POST['tax_input'][$term] ) ? array_filter( array_map( 'intval', (array) $_POST['tax_input'][$term] ) ) : array();
                        wp_set_object_terms( $post_id, $custom_term, $term );
                    }
                }
                
                // Set Product GTIN
                if( isset( $_POST['_wcmb_gtin_type'] ) && !empty( $_POST['_wcmb_gtin_type'] ) ){
                    $term = get_term( $_POST['_wcmb_gtin_type'], $WCMb->taxonomy->wcmb_gtin_taxonomy );
                    if ($term && !is_wp_error( $term )) {
                        wp_delete_object_term_relationships( $post_id, $WCMb->taxonomy->wcmb_gtin_taxonomy );
                        wp_set_object_terms( $post_id, $term->term_id, $WCMb->taxonomy->wcmb_gtin_taxonomy, true );
                    }
                }
                if ( isset( $_POST['_wcmb_gtin_code'] ) ) {
                    update_post_meta( $post_id, '_wcmb_gtin_code', wc_clean( wp_unslash( $_POST['_wcmb_gtin_code'] ) ) );
                }

                //get the correct class
                $classname = WC_Product_Factory::get_product_classname( $post_id, $product_type ? $product_type : 'simple' );
                $product = new $classname( $post_id );
                $attributes = isset( $_POST['wc_attributes'] ) ? wcmb_woo()->prepare_attributes( $_POST['wc_attributes'] ) : array();
                $stock = null;
                // Handle stock changes.
                if ( isset( $_POST['_stock'] ) ) {
                    if ( isset( $_POST['_original_stock'] ) && wc_stock_amount( $product->get_stock_quantity( 'edit' ) ) !== wc_stock_amount( $_POST['_original_stock'] ) ) {
                        $error_msg = sprintf( __( 'The stock has not been updated because the value has changed since editing. Product %1$d has %2$d units in stock.', 'woocommerce' ), $product->get_id(), $product->get_stock_quantity( 'edit' ) );
                        $errors[] = $error_msg;
                    } else {
                        $stock = wc_stock_amount( $_POST['_stock'] );
                    }
                }
                // Group Products
                $grouped_products = isset( $_POST['grouped_products'] ) ? array_filter( array_map( 'intval', (array) $_POST['grouped_products'] ) ) : array();

                // file paths will be stored in an array keyed off md5(file path)
                $downloads = array();
                if ( isset( $_POST['_downloadable'] ) && isset( $_POST['_wc_file_urls'] ) ) {
                    $file_urls = $_POST['_wc_file_urls'];
                    $file_names = isset( $_POST['_wc_file_names'] ) ? $_POST['_wc_file_names'] : array();
                    $file_hashes = isset( $_POST['_wc_file_hashes'] ) ? $_POST['_wc_file_hashes'] : array();

                    $file_url_size = sizeof( $file_urls );
                    for ( $i = 0; $i < $file_url_size; $i ++ ) {
                        if ( ! empty( $file_urls[$i] ) ) {
                            $downloads[] = array(
                                'name'        => wc_clean( $file_names[$i] ),
                                'file'        => wp_unslash( trim( $file_urls[$i] ) ),
                                'download_id' => wc_clean( $file_hashes[$i] ),
                            );
                        }
                    }
                }

                $error = $product->set_props(
                    array(
                        'virtual'            => isset( $_POST['_virtual'] ),
                        'downloadable'       => isset( $_POST['_downloadable'] ),
                        'featured'           => isset( $_POST['_featured'] ),
                        'catalog_visibility' => wc_clean( wp_unslash( $_POST['_visibility'] ) ),
                        'product_url'        => isset( $_POST['_product_url'] ) ? esc_url_raw( $_POST['_product_url'] ) : null,
                        'button_text'        => isset( $_POST['_button_text'] ) ? wc_clean( $_POST['_button_text'] ) : null,
                        'children'           => 'grouped' === $product_type ? $grouped_products : null,
                        'regular_price'      => isset( $_POST['_regular_price'] ) ? wc_clean( $_POST['_regular_price'] ) : null,
                        'sale_price'         => isset( $_POST['_sale_price'] ) ? wc_clean( $_POST['_sale_price'] ) : null,
                        'date_on_sale_from'  => isset( $_POST['_sale_price_dates_from'] ) ? wc_clean( $_POST['_sale_price_dates_from'] ) : null,
                        'date_on_sale_to'    => isset( $_POST['_sale_price_dates_to'] ) ? wc_clean( $_POST['_sale_price_dates_to'] ) : null,
                        'download_limit'     => empty( $_POST['_download_limit'] ) ? '' : absint( $_POST['_download_limit'] ),
                        'download_expiry'    => empty( $_POST['_download_expiry'] ) ? '' : absint( $_POST['_download_expiry'] ),
                        'downloads'          => $downloads,
                        'tax_status'         => isset( $_POST['_tax_status'] ) ? wc_clean( $_POST['_tax_status'] ) : null,
                        'tax_class'          => isset( $_POST['_tax_class'] ) ? wc_clean( $_POST['_tax_class'] ) : null,
                        'sku'                => isset( $_POST['_sku'] ) ? wc_clean( $_POST['_sku'] ) : null,
                        'manage_stock'       => ! empty( $_POST['_manage_stock'] ),
                        'stock_quantity'     => $stock,
                        'low_stock_amount'   => isset( $_POST['_low_stock_amount'] ) && '' !== $_POST['_low_stock_amount'] ? wc_stock_amount( wp_unslash( $_POST['_low_stock_amount'] ) ) : '',
                        'backorders'         => isset( $_POST['_backorders'] ) ? wc_clean( $_POST['_backorders'] ) : null,
                        'stock_status'       => isset( $_POST['_stock_status'] ) ? wc_clean( $_POST['_stock_status'] ) : null,
                        'sold_individually'  => ! empty( $_POST['_sold_individually'] ),
                        'weight'             => isset( $_POST['_weight'] ) ? wc_clean( $_POST['_weight'] ) : null,
                        'length'             => isset( $_POST['_length'] ) ? wc_clean( $_POST['_length'] ) : null,
                        'width'              => isset( $_POST['_width'] ) ? wc_clean( $_POST['_width'] ) : null,
                        'height'             => isset( $_POST['_height'] ) ? wc_clean( $_POST['_height'] ) : null,
                        'shipping_class_id'  => isset( $_POST['product_shipping_class'] ) ? absint( $_POST['product_shipping_class'] ) : null,
                        'upsell_ids'         => isset( $_POST['upsell_ids'] ) ? array_map( 'intval', (array) $_POST['upsell_ids'] ) : array(),
                        'cross_sell_ids'     => isset( $_POST['crosssell_ids'] ) ? array_map( 'intval', (array) $_POST['crosssell_ids'] ) : array(),
                        'purchase_note'      => isset( $_POST['_purchase_note'] ) ? wp_kses_post( stripslashes( $_POST['_purchase_note'] ) ) : null,
                        'menu_order'         => isset( $_POST['menu_order'] ) ? wc_clean( $_POST['menu_order'] ) : null,
                        'reviews_allowed'    => ! empty( $_POST['comment_status'] ) && 'open' === $_POST['comment_status'],
                        'attributes'         => $attributes,
                        'default_attributes' => wcmb_woo()->prepare_set_attributes( $attributes, 'default_attribute_', $_POST ),
                    )
                );

                if ( is_wp_error( $error ) ) {
                    $errors[] = $error->get_error_message();
                }

                do_action( 'wcmb_process_product_object', $product, $_POST );

                $product->save();

                if ( $product->is_type( 'variable' ) ) {
                    $product->get_data_store()->sync_variation_names( $product, wc_clean( $_POST['original_post_title'] ), wc_clean( $_POST['post_title'] ) );
                    $error = wcmb_woo()->save_product_variations( $post_id, $_POST );
                    $errors = array_merge( $errors, $error );
                }

                // Notify Admin on New Product Creation
                if ( ( ! $_POST['is_update'] || $_POST['original_post_status'] == 'draft' ) && $status != 'draft' ) {
                    $WCMb->product->on_all_status_transitions( $status, '', get_post( $post_id ) );
                }

                do_action( 'wcmb_process_product_meta_' . $product_type, $post_id, $_POST );

                foreach ( $errors as $error ) {
                    wc_add_notice( $error, 'error' );
                }
                $status_msg = '';
                switch ( $status ) {
                    case 'draft': $status_msg = __( 'Product is successfully drafted', 'MB-multivendor' );
                        break;
                    case 'pending': $status_msg = __( 'Product is successfully submitted for review', 'MB-multivendor' );
                        break;
                    case 'publish': $status_msg = sprintf( __( 'Product updated and live. <a href="%s" target="_blank">View Product</a>', 'MB-multivendor' ), esc_attr( get_permalink( $post_id ) ) );
                        break;
                }
                wc_add_notice( $status_msg, 'success' );
                wp_redirect( apply_filters( 'wcmb_vendor_save_product_redirect_url', wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_edit_product_endpoint', 'vendor', 'general', 'edit-product' ), $post_id ) ) );
                exit;
            } else {
                wc_add_notice( $post_id->get_error_message(), 'error' );
            }
        }
    }
    
    public function save_coupon() {
        global $WCMb;
        $current_endpoint_key = $WCMb->endpoints->get_current_endpoint();
        // retrive the actual endpoint name in case admn changes that from settings
        $current_endpoint = get_wcmb_vendor_settings( 'wcmb_' . str_replace( '-', '_', $current_endpoint_key ) . '_endpoint', 'vendor', 'general', $current_endpoint_key );
        // retrive add-coupon endpoint name in case admn changes that from settings
        $add_coupon_endpoint = get_wcmb_vendor_settings( 'wcmb_add_coupon_endpoint', 'vendor', 'general', 'add-coupon' );
        //Return if not add coupon endpoint
        if ( $current_endpoint !== $add_coupon_endpoint || ! isset( $_POST['wcmb_afm_coupon_nonce'] ) ) {
            return;
        }

        $vendor_id = get_current_user_id();

        if ( ! $vendor_id || ! current_vendor_can( 'edit_shop_coupon' ) || empty( $_POST['post_ID'] ) || ! wp_verify_nonce( $_POST['wcmb_afm_coupon_nonce'], 'wcmb-afm-coupon' ) ) {
            wp_die( -1 );
        }

        if ( empty( $_POST['post_title'] ) || empty( $_POST['product_ids'] ) ) {
            if ( empty( $_POST['post_title'] ) ) {
                wc_add_notice( __( "Coupon code can't be empty.", 'MB-multivendor' ), 'error' );
            }
            if ( empty( $_POST['product_ids'] ) ) {
                wc_add_notice( __( 'Select atleast one product.', 'MB-multivendor' ), 'error' );
            }
            return;
        }

        $post_id = absint( $_POST['post_ID'] );
        $post = get_post( $post_id );
        $coupon = new WC_Coupon( $post_id );
        // Check for dupe coupons.
        $coupon_code = wc_format_coupon_code( $_POST['post_title'] );
        $id_from_code = wc_get_coupon_id_by_code( $coupon_code, $post_id );

        if ( $id_from_code ) {
            if ( is_current_vendor_coupon( $id_from_code ) ) {
                wc_add_notice( __( 'Coupon code already exists - customers will use the latest coupon with this code.', 'woocommerce' ), 'error' );
            } else {
                wc_add_notice( __( 'Coupon code already exists - provide a different coupon code.', 'MB-multivendor' ), 'error' );
                return;
            }
        }

        if ( isset( $_POST['status'] ) && $_POST['status'] === 'draft' ) {
            $status = 'draft';
        } elseif ( isset( $_POST['status'] ) && $_POST['status'] === 'publish' ) {
            if ( ! current_vendor_can( 'publish_shop_coupons' ) ) {
                $status = 'pending';
            } else {
                $status = 'publish';
            }
        } else {
            wp_die( __( 'Invalid coupon status.', 'MB-multivendor' ) );
        }

        if ( ! $coupon->get_date_created( 'edit' ) ) {
            $coupon->set_date_created( current_time( 'timestamp', true ) );
        }

        $title = ( isset( $_POST['post_title'] ) ) ? wc_clean( $_POST['post_title'] ) : '';

        $post_data = apply_filters( 'afm_submitted_coupon_data', array(
            'ID'            => $post_id,
            'post_title'    => $title,
            'post_excerpt'  => stripslashes( html_entity_decode( $_POST['coupon_description'], ENT_QUOTES, get_bloginfo( 'charset' ) ) ),
            'post_status'   => $status,
            'post_type'     => 'shop_coupon',
            'post_author'   => $vendor_id,
            'post_date'     => gmdate( 'Y-m-d H:i:s', $coupon->get_date_created( 'edit' )->getOffsetTimestamp() ),
            'post_date_gmt' => gmdate( 'Y-m-d H:i:s', $coupon->get_date_created( 'edit' )->getTimestamp() ),
            ), $_POST );

        do_action( 'wcmb_afm_before_coupon_post_update' );

        $post_id = wp_update_post( $post_data, true );

        if ( $post_id && ! is_wp_error( $post_id ) ) {
            $product_categories = isset( $_POST['product_categories'] ) ? (array) $_POST['product_categories'] : array();
            $exclude_product_categories = isset( $_POST['exclude_product_categories'] ) ? (array) $_POST['exclude_product_categories'] : array();

            $errors = array();
            $coupon = new WC_Coupon( $post_id );
            $error = $coupon->set_props(
                array(
                    'code'                        => $title,
                    'discount_type'               => wc_clean( $_POST['discount_type'] ),
                    'amount'                      => wc_format_decimal( $_POST['coupon_amount'] ),
                    'date_expires'                => wc_clean( $_POST['expiry_date'] ),
                    'individual_use'              => isset( $_POST['individual_use'] ),
                    'product_ids'                 => isset( $_POST['product_ids'] ) ? array_filter( array_map( 'intval', (array) $_POST['product_ids'] ) ) : array(),
                    'excluded_product_ids'        => isset( $_POST['exclude_product_ids'] ) ? array_filter( array_map( 'intval', (array) $_POST['exclude_product_ids'] ) ) : array(),
                    'usage_limit'                 => absint( $_POST['usage_limit'] ),
                    'usage_limit_per_user'        => absint( $_POST['usage_limit_per_user'] ),
                    'limit_usage_to_x_items'      => absint( $_POST['limit_usage_to_x_items'] ),
                    'free_shipping'               => isset( $_POST['free_shipping'] ),
                    'product_categories'          => array_filter( array_map( 'intval', $product_categories ) ),
                    'excluded_product_categories' => array_filter( array_map( 'intval', $exclude_product_categories ) ),
                    'exclude_sale_items'          => isset( $_POST['exclude_sale_items'] ),
                    'minimum_amount'              => wc_format_decimal( $_POST['minimum_amount'] ),
                    'maximum_amount'              => wc_format_decimal( $_POST['maximum_amount'] ),
                    'email_restrictions'          => array_filter( array_map( 'trim', explode( ',', wc_clean( $_POST['customer_email'] ) ) ) ),
                )
            );
            if ( is_wp_error( $error ) ) {
                $errors[] = $error->get_error_message();
            }
            $coupon->save();
            do_action( 'wcmb_afm_coupon_options_save', $post_id, $coupon );

            foreach ( $errors as $error ) {
                wc_add_notice( $error, 'error' );
            }
            $status_msg = '';
            switch ( $status ) {
                case 'draft': $status_msg = __( 'Coupon is successfully drafted', 'MB-multivendor' );
                    break;
                case 'pending': $status_msg = __( 'Coupon is successfully submitted for review', 'MB-multivendor' );
                    break;
                case 'publish': $status_msg = __( 'Coupon updated and live.', 'MB-multivendor' );
                    break;
            }
            wc_add_notice( $status_msg, 'success' );

            wp_redirect( apply_filters( 'wcmb_vendor_save_coupon_redirect_url', wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_add_coupon_endpoint', 'vendor', 'general', 'add-coupon' ), $post_id ) ) );
            exit;
        } else {
            wc_add_notice( $post_id->get_error_message(), 'error' );
        }
    }
    
    public function wcmb_vendor_dashboard_add_product_url( $url ) {
        if( !get_wcmb_vendor_settings('is_singleproductmultiseller', 'general') == 'Enable' && get_wcmb_vendor_settings('is_disable_marketplace_plisting', 'general') == 'Enable' ){
            return esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_edit_product_endpoint', 'vendor', 'general', 'edit-product')));
        }
        return $url;
    }

}

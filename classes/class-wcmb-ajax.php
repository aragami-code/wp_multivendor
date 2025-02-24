<?php

/**
 
 */
class WCMb_Ajax {

    public function __construct() {
        //$general_singleproductmultisellersettings = get_option('wcmb_general_singleproductmultiseller_settings_name');
        add_action('wp_ajax_woocommerce_json_search_vendors', array(&$this, 'woocommerce_json_search_vendors'));
        add_action('wp_ajax_activate_pending_vendor', array(&$this, 'activate_pending_vendor'));
        add_action('wp_ajax_reject_pending_vendor', array(&$this, 'reject_pending_vendor'));
        add_action('wp_ajax_wcmb_suspend_vendor', array(&$this, 'wcmb_suspend_vendor'));
        add_action('wp_ajax_wcmb_activate_vendor', array(&$this, 'wcmb_activate_vendor'));
        add_action('wp_ajax_send_report_abuse', array(&$this, 'send_report_abuse'));
        add_action('wp_ajax_nopriv_send_report_abuse', array(&$this, 'send_report_abuse'));
        add_action('wp_ajax_dismiss_vendor_to_do_list', array(&$this, 'dismiss_vendor_to_do_list'));
        add_action('wp_ajax_get_more_orders', array(&$this, 'get_more_orders'));
        add_action('wp_ajax_withdrawal_more_orders', array(&$this, 'withdrawal_more_orders'));
        add_action('wp_ajax_show_more_transaction', array(&$this, 'show_more_transaction'));
        add_action('wp_ajax_nopriv_get_more_orders', array(&$this, 'get_more_orders'));
        add_action('wp_ajax_order_mark_as_shipped', array(&$this, 'order_mark_as_shipped'));
//        add_action('wp_ajax_nopriv_order_mark_as_shipped', array(&$this, 'order_mark_as_shipped'));
        add_action('wp_ajax_transaction_done_button', array(&$this, 'transaction_done_button'));
        add_action('wp_ajax_wcmb_vendor_csv_download_per_order', array(&$this, 'wcmb_vendor_csv_download_per_order'));
        add_filter('ajax_query_attachments_args', array(&$this, 'show_current_user_attachments'), 10, 1);
        add_filter('wp_ajax_vendor_report_sort', array($this, 'vendor_report_sort'));
        add_filter('wp_ajax_vendor_search', array($this, 'search_vendor_data'));
        add_filter('wp_ajax_product_report_sort', array($this, 'product_report_sort'));
        add_filter('wp_ajax_product_search', array($this, 'search_product_data'));
        // woocommerce product enquiry form support
        if (WC_Dependencies_Product_Vendor::woocommerce_product_enquiry_form_active_check()) {
            add_filter('product_enquiry_send_to', array($this, 'send_enquiry_to_vendor'), 10, 2);
        }

        // Unsign vendor from product
        add_action('wp_ajax_unassign_vendor', array($this, 'unassign_vendor'));
        add_action('wp_ajax_wcmb_frontend_sale_get_row', array(&$this, 'wcmb_frontend_sale_get_row_callback'));
        add_action('wp_ajax_nopriv_wcmb_frontend_sale_get_row', array(&$this, 'wcmb_frontend_sale_get_row_callback'));
        add_action('wp_ajax_wcmb_frontend_pending_shipping_get_row', array(&$this, 'wcmb_frontend_pending_shipping_get_row_callback'));
        add_action('wp_ajax_nopriv_wcmb_frontend_pending_shipping_get_row', array(&$this, 'wcmb_frontend_pending_shipping_get_row_callback'));

        add_action('wp_ajax_wcmb_vendor_announcements_operation', array($this, 'wcmb_vendor_messages_operation'));
        add_action('wp_ajax_nopriv_wcmb_vendor_announcements_operation', array($this, 'wcmb_vendor_messages_operation'));
        add_action('wp_ajax_wcmb_announcements_refresh_tab_data', array($this, 'wcmb_msg_refresh_tab_data'));
        add_action('wp_ajax_nopriv_wcmb_announcements_refresh_tab_data', array($this, 'wcmb_msg_refresh_tab_data'));
        add_action('wp_ajax_wcmb_dismiss_dashboard_announcements', array($this, 'wcmb_dismiss_dashboard_message'));
        add_action('wp_ajax_nopriv_wcmb_dismiss_dashboard_announcements', array($this, 'wcmb_dismiss_dashboard_message'));

        if (get_wcmb_vendor_settings('is_singleproductmultiseller', 'general') == 'Enable') {
            // Product auto suggestion
            add_action('wp_ajax_wcmb_auto_search_product', array($this, 'wcmb_auto_suggesion_product'));
            add_action('wp_ajax_nopriv_wcmb_auto_search_product', array($this, 'wcmb_auto_suggesion_product'));
            // Product duplicate
            add_action('wp_ajax_wcmb_copy_to_new_draft', array($this, 'wcmb_copy_to_new_draft'));
            add_action('wp_ajax_nopriv_wcmb_copy_to_new_draft', array($this, 'wcmb_copy_to_new_draft'));
            add_action('wp_ajax_get_loadmorebutton_single_product_multiple_vendors', array($this, 'wcmb_get_loadmorebutton_single_product_multiple_vendors'));
            add_action('wp_ajax_nopriv_get_loadmorebutton_single_product_multiple_vendors', array($this, 'wcmb_get_loadmorebutton_single_product_multiple_vendors'));
            add_action('wp_ajax_single_product_multiple_vendors_sorting', array($this, 'single_product_multiple_vendors_sorting'));
            add_action('wp_ajax_nopriv_single_product_multiple_vendors_sorting', array($this, 'single_product_multiple_vendors_sorting'));

            add_action('wp_ajax_wcmb_create_duplicate_product', array(&$this, 'wcmb_create_duplicate_product'));
        }
        add_action('wp_ajax_wcmb_add_review_rating_vendor', array($this, 'wcmb_add_review_rating_vendor'));
        add_action('wp_ajax_nopriv_wcmb_add_review_rating_vendor', array($this, 'wcmb_add_review_rating_vendor'));
        // load more vendor review
        add_action('wp_ajax_wcmb_load_more_review_rating_vendor', array($this, 'wcmb_load_more_review_rating_vendor'));
        add_action('wp_ajax_nopriv_wcmb_load_more_review_rating_vendor', array($this, 'wcmb_load_more_review_rating_vendor'));

        add_action('wp_ajax_wcmb_save_vendor_registration_form', array(&$this, 'wcmb_save_vendor_registration_form_callback'));

        add_action('wp_ajax_dismiss_wcmb_servive_notice', array(&$this, 'dismiss_wcmb_servive_notice'));
        // search filter vendors from widget
        add_action('wp_ajax_vendor_list_by_search_keyword', array($this, 'vendor_list_by_search_keyword'));
        add_action('wp_ajax_nopriv_vendor_list_by_search_keyword', array($this, 'vendor_list_by_search_keyword'));

        add_action('wp_ajax_wcmb_product_tag_add', array(&$this, 'wcmb_product_tag_add'));

        //add_action('wp_ajax_generate_variation_attributes', array(&$this, 'generate_variation_attributes'));

        add_action('wp_ajax_delete_fpm_product', array(&$this, 'delete_fpm_product'));

        // Vendor dashboard product list
        add_action('wp_ajax_wcmb_vendor_product_list', array(&$this, 'wcmb_vendor_product_list'));
        // Vendor dashboard withdrawal list
        add_action('wp_ajax_wcmb_vendor_unpaid_order_vendor_withdrawal_list', array(&$this, 'wcmb_vendor_unpaid_order_vendor_withdrawal_list'));
        // Vendor dashboard transactions list
        add_action('wp_ajax_wcmb_vendor_transactions_list', array(&$this, 'wcmb_vendor_transactions_list'));
        // Vendor dashboard coupon list
        add_action('wp_ajax_wcmb_vendor_coupon_list', array(&$this, 'wcmb_vendor_coupon_list'));

        add_action('wp_ajax_wcmb_datatable_get_vendor_orders', array(&$this, 'wcmb_datatable_get_vendor_orders'));
        // Customer Q & A
        add_action('wp_ajax_wcmb_customer_ask_qna_handler', array(&$this, 'wcmb_customer_ask_qna_handler'));
        add_action('wp_ajax_nopriv_wcmb_customer_ask_qna_handler', array(&$this, 'wcmb_customer_ask_qna_handler'));
        // dashboard vendor reviews widget
        add_action('wp_ajax_wcmb_vendor_dashboard_reviews_data', array(&$this, 'wcmb_vendor_dashboard_reviews_data'));
        // dashboard customer questions widget
        add_action('wp_ajax_wcmb_vendor_dashboard_customer_questions_data', array(&$this, 'wcmb_vendor_dashboard_customer_questions_data'));
        // vendor products Q&As list
        add_action('wp_ajax_wcmb_vendor_products_qna_list', array(&$this, 'wcmb_vendor_products_qna_list'));
        // vendor pending shipping widget
        add_action('wp_ajax_wcmb_widget_vendor_pending_shipping', array(&$this, 'wcmb_widget_vendor_pending_shipping'));
        // vendor product sales report widget
        add_action('wp_ajax_wcmb_widget_vendor_product_sales_report', array(&$this, 'wcmb_widget_vendor_product_sales_report'));

        // vendor management tab under wcmb
        add_action('wp_ajax_wcmb_get_vendor_details', array(&$this, 'wcmb_get_vendor_details'));
        // Image crop for vendor banner and logo
        add_action('wp_ajax_wcmb_crop_image', array(&$this, 'wcmb_crop_image'));
        // wcmb shipping
        add_action('wp_ajax_wcmb-get-shipping-methods-by-zone', array($this, 'wcmb_get_shipping_methods_by_zone'));
        add_action('wp_ajax_wcmb-add-shipping-method', array($this, 'wcmb_add_shipping_method'));
        add_action('wp_ajax_wcmb-update-shipping-method', array($this, 'wcmb_update_shipping_method'));
        add_action('wp_ajax_wcmb-delete-shipping-method', array($this, 'wcmb_delete_shipping_method'));
        add_action('wp_ajax_wcmb-toggle-shipping-method', array($this, 'wcmb_toggle_shipping_method'));
        add_action('wp_ajax_wcmb-configure-shipping-method', array($this, 'wcmb_configure_shipping_method'));
        
        // product add new listing
        add_action('wp_ajax_wcmb_product_classify_next_level_list_categories', array($this, 'wcmb_product_classify_next_level_list_categories'));
        add_action('wp_ajax_wcmb_product_classify_search_category_level', array($this, 'wcmb_product_classify_search_category_level'));
        add_action('wp_ajax_show_product_classify_next_level_from_searched_term', array($this, 'show_product_classify_next_level_from_searched_term'));
        add_action('wp_ajax_wcmb_list_a_product_by_name_or_gtin', array($this, 'wcmb_list_a_product_by_name_or_gtin'));
        add_action('wp_ajax_wcmb_set_classified_product_terms', array($this, 'wcmb_set_classified_product_terms'));
        //ajax call to get the product attributes
        add_action( 'wp_ajax_wcmb_edit_product_attribute', array( $this, 'edit_product_attribute_callback' ) );
        add_action( 'wp_ajax_wcmb_product_save_attributes', array( $this, 'save_product_attributes_callback' ) );
        
    }

    /**
     * Ajax callback
     * creates a new attachment from the cropped image
     * basically taken from the custom-header class
     * send prepared attachment back to the client
     */
    public function wcmb_crop_image() {
        $attachment_id = absint($_POST['id']);

        check_ajax_referer('image_editor-' . $attachment_id, 'nonce');

        if (empty($attachment_id)) {
            wp_send_json_error();
        }

        $crop_details = apply_filters('before_wcmb_crop_image_cropDetails_data', $_POST['cropDetails'], $attachment_id);
        $crop_options = apply_filters('before_wcmb_crop_image_cropOptions_data', $_POST['cropOptions'], $attachment_id);

        $cropped = wp_crop_image(
                $attachment_id, (int) $crop_details['x1'], (int) $crop_details['y1'], (int) $crop_details['width'], (int) $crop_details['height'], $crop_options['maxWidth'], $crop_options['maxHeight']
        );

        if (!$cropped || is_wp_error($cropped)) {
            wp_send_json_error(array('message' => __('Image could not be processed. Please go back and try again.', 'MB-multivendor')));
        }

        /** This filter is documented in wp-admin/custom-header.php */
        $cropped = apply_filters('wcmb_create_file_in_uploads', $cropped, $attachment_id, $crop_details, $crop_options); // For replication

        $parent = get_post($attachment_id);
        $parent_url = $parent->guid;
        $url = str_replace(basename($parent_url), basename($cropped), $parent_url);

        $size = @getimagesize($cropped);
        $image_type = ( $size ) ? $size['mime'] : 'image/jpeg';

        $object = array(
            'ID' => $attachment_id,
            'post_title' => basename($cropped),
            'post_content' => $url,
            'post_mime_type' => $image_type,
            'guid' => $url
        );
        // Its override actual image with cropped one
        if( !apply_filters( 'wcmb_crop_image_override_with_original', false, $attachment_id, $_POST ) ) unset($object['ID']); 

        $attachment_id = wp_insert_attachment($object, $cropped);

        $metadata = wp_generate_attachment_metadata($attachment_id, $cropped);
        /**
         * Filter the header image attachment metadata.
         * @since 3.1.2
         * @see wp_generate_attachment_metadata()
         * @param array $metadata Attachment metadata.
         */
        $metadata = apply_filters('wcmb_header_image_attachment_metadata', $metadata);
        wp_update_attachment_metadata($attachment_id, $metadata);

        $pre = wp_prepare_attachment_for_js($attachment_id);

        wp_send_json_success($pre);
    }

    public function wcmb_datatable_get_vendor_orders() {
        global $wpdb, $WCMb;
        $requestData = $_REQUEST;
        $start_date = date('Y-m-d G:i:s', $_POST['start_date']);
        $end_date = date('Y-m-d G:i:s', $_POST['end_date']);
        $vendor = get_current_vendor();
        $vendor_all_orders = $wpdb->get_results("SELECT DISTINCT order_id from `{$wpdb->prefix}wcmb_vendor_orders` where commission_id > 0 AND vendor_id = '" . $vendor->id . "' AND (`created` >= '" . $start_date . "' AND `created` <= '" . $end_date . "') and `is_trashed` != 1 ORDER BY `created` DESC", ARRAY_A);
        $vendor_all_orders = apply_filters('wcmb_datatable_get_vendor_all_orders', $vendor_all_orders, $requestData, $_POST);
        $vendor_all_orders = apply_filters('wcmb_datatable_get_vendor_all_orders_id', wp_list_pluck($vendor_all_orders, 'order_id'));
        if (isset($requestData['order_status']) && $requestData['order_status'] != 'all' && $requestData['order_status'] != '') {
            foreach ($vendor_all_orders as $key => $value) {
                if (wc_get_order($value)->get_status() != $requestData['order_status']) {
                    unset($vendor_all_orders[$key]);
                }
            }
        }
        $vendor_orders = array_slice($vendor_all_orders, $requestData['start'], $requestData['length']);
        $data = array();

        foreach ($vendor_orders as $order_id) {
            $order = wc_get_order($order_id);
            if ($order) {
                $actions = array();
                $is_shipped = (array) get_post_meta($order->get_id(), 'dc_pv_shipped', true);
                if (!in_array($vendor->id, $is_shipped)) {
                    $mark_ship_title = __('Mark as shipped', 'MB-multivendor');
                } else {
                    $mark_ship_title = __('Shipped', 'MB-multivendor');
                }
                $actions['view'] = array(
                    'url' => esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_vendor_orders_endpoint', 'vendor', 'general', 'vendor-orders'), $order->get_id())),
                    'icon' => 'ico-eye-icon action-icon',
                    'title' => __('View', 'MB-multivendor'),
                );
                if (apply_filters('can_wcmb_vendor_export_orders_csv', true, get_current_vendor_id())) :
                    $actions['wcmb_vendor_csv_download_per_order'] = array(
                        'url' => admin_url('admin-ajax.php?action=wcmb_vendor_csv_download_per_order&order_id=' . $order->get_id() . '&nonce=' . wp_create_nonce('wcmb_vendor_csv_download_per_order')),
                        'icon' => 'ico-download-icon action-icon',
                        'title' => __('Download', 'MB-multivendor'),
                    );
                endif;
                if ($vendor->is_shipping_enable()) {
                    $vendor_shipping_method = get_wcmb_vendor_order_shipping_method($order->get_id(), $vendor->id);
                    // hide shipping for local pickup
                    if ($vendor_shipping_method && !in_array($vendor_shipping_method->get_method_id(), apply_filters('hide_shipping_icon_for_vendor_order_on_methods', array('local_pickup')))) {
                        $actions['mark_ship'] = array(
                            'url' => '#',
                            'title' => $mark_ship_title,
                            'icon' => 'ico-shippingnew-icon action-icon'
                        );
                    }
                }
                $actions = apply_filters('wcmb_my_account_my_orders_actions', $actions, $order->get_id());
                $action_html = '';
                foreach ($actions as $key => $action) {
                    if ($key == 'mark_ship' && !in_array($vendor->id, $is_shipped)) {
                        $action_html .= '<a href="javascript:void(0)" title="' . $mark_ship_title . '" onclick="wcmbMarkeAsShip(this,' . $order->get_id() . ')"><i class="wcmb-font ' . $action['icon'] . '"></i></a> ';
                    } else if ($key == 'mark_ship') {
                        $action_html .= '<i title="' . $mark_ship_title . '" class="wcmb-font ' . $action['icon'] . '"></i> ';
                    } else {
                        $action_html .= '<a href="' . $action['url'] . '" title="' . $action['title'] . '"><i class="wcmb-font ' . $action['icon'] . '"></i></a> ';
                    }
                }
                $data[] = apply_filters('wcmb_datatable_order_list_row_data', array(
                    'select_order' => '<input type="checkbox" class="select_' . $order->get_status() . '" name="selected_orders[' . $order->get_id() . ']" value="' . $order->get_id() . '" />',
                    'order_id' => $order->get_id(),
                    'order_date' => wcmb_date($order->get_date_created()),
                    'vendor_earning' => wc_price(get_wcmb_vendor_order_amount(array('vendor_id' => $vendor->id, 'order_id' => $order->get_id()))['total']),
                    'order_status' => esc_html(wc_get_order_status_name($order->get_status())), //ucfirst($order->get_status()),
                    'action' => apply_filters('wcmb_vendor_orders_row_action_html', $action_html, $actions)
                        ), $order);
            }
        }
        $json_data = array(
            "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal" => intval(count($vendor_all_orders)), // total number of records
            "recordsFiltered" => intval(count($vendor_all_orders)), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data" => $data   // total data array
        );
        wp_send_json($json_data);
    }

    public function wcmb_save_vendor_registration_form_callback() {
        $form_data = json_decode(stripslashes_deep($_REQUEST['form_data']), true);
        if (!empty($form_data) && is_array($form_data)) {
            foreach ($form_data as $key => $value) {
                $form_data[$key]['hidden'] = true;
            }
        }

        update_option('wcmb_vendor_registration_form_data', $form_data);
        die;
    }

    function single_product_multiple_vendors_sorting() {
        global $WCMb;
        $sorting_value = $_POST['sorting_value'];
        $attrid = $_POST['attrid'];
        $more_products = $WCMb->product->get_multiple_vendors_array_for_single_product($attrid);
        $more_product_array = $more_products['more_product_array'];
        $results = $more_products['results'];
        $WCMb->template->get_template('single-product/multiple_vendors_products_body.php', array('more_product_array' => $more_product_array, 'sorting' => $sorting_value));
        die;
    }

    function wcmb_get_loadmorebutton_single_product_multiple_vendors() {
        global $WCMb;
        $WCMb->template->get_template('single-product/load-more-button.php');
        die;
    }

    function wcmb_load_more_review_rating_vendor() {
        global $WCMb, $wpdb;

        if (!empty($_POST['pageno']) && !empty($_POST['term_id'])) {
            $vendor = get_wcmb_vendor_by_term($_POST['term_id']);
            $vendor_id = $vendor->id;
            $offset = $_POST['postperpage'] * $_POST['pageno'];
            $reviews_lists = $vendor->get_reviews_and_rating($offset);
            $WCMb->template->get_template('review/wcmb-vendor-review.php', array('reviews_lists' => $reviews_lists, 'vendor_term_id' => $_POST['term_id']));
        }
        die;
    }

    function wcmb_add_review_rating_vendor() {
        global $WCMb, $wpdb;
        $review = $_POST['comment'];
        $rating = isset($_POST['rating']) ? $_POST['rating'] : false;
        $comment_parent = isset($_POST['comment_parent']) ? $_POST['comment_parent'] : 0;
        $vendor_id = $_POST['vendor_id'];
        $current_user = wp_get_current_user();
        $comment_approve_by_settings = get_option('comment_moderation') ? 0 : 1;
        if (!empty($review)) {
            $time = current_time('mysql');
            if ($current_user->ID > 0) {
                $data = array(
                    'comment_post_ID' => wcmb_vendor_dashboard_page_id(),
                    'comment_author' => $current_user->display_name,
                    'comment_author_email' => $current_user->user_email,
                    'comment_author_url' => $current_user->user_url,
                    'comment_content' => $review,
                    'comment_type' => 'wcmb_vendor_rating',
                    'comment_parent' => $comment_parent,
                    'user_id' => $current_user->ID,
                    'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
                    'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
                    'comment_date' => $time,
                    'comment_approved' => $comment_approve_by_settings,
                );
                $comment_id = wp_insert_comment($data);
                if (!is_wp_error($comment_id)) {
                    // delete transient
                    if (get_transient('wcmb_dashboard_reviews_for_vendor_' . $vendor_id)) {
                        delete_transient('wcmb_dashboard_reviews_for_vendor_' . $vendor_id);
                    }
                    // mark as replied
                    if ($comment_parent != 0 && $vendor_id) {
                        update_comment_meta($comment_parent, '_mark_as_replied', 1);
                    }
                    if ($rating && !empty($rating)) {
                        update_comment_meta($comment_id, 'vendor_rating', $rating);
                    }
                    $is_updated = update_comment_meta($comment_id, 'vendor_rating_id', $vendor_id);
                    if ($is_updated) {
                        echo 1;
                    }
                }
            }
        } else {
            echo 0;
        }
        die;
    }

    function wcmb_copy_to_new_draft() {
        $post_id = $_POST['postid'];
        $post = get_post($post_id);
        echo wp_nonce_url(admin_url('edit.php?post_type=product&action=duplicate_product&post=' . $post->ID), 'woocommerce-duplicate-product_' . $post->ID);
        die;
    }

    public function wcmb_create_duplicate_product() {
        global $WCMb;
        $product_id = $_POST['product_id'];
        $parent_post = get_post($product_id);
        $redirect_url = isset($_POST['redirect_url']) ? $_POST['redirect_url'] : esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_edit_product_endpoint', 'vendor', 'general', 'edit-product')));
        $product = wc_get_product($product_id);
        if (!function_exists('duplicate_post_plugin_activation')) {
            include_once( WC_ABSPATH . 'includes/admin/class-wc-admin-duplicate-product.php' );
        }
        $duplicate_product_class = new WC_Admin_Duplicate_Product();
        $duplicate_product = $duplicate_product_class->product_duplicate($product);
        $response = array('status' => false);
        if ($duplicate_product && is_user_wcmb_vendor(get_current_user_id())) {
            // if Product title have Copy string
            $title = str_replace(" (Copy)","",$parent_post->post_title);
            wp_update_post(array('ID' => $duplicate_product->get_id(), 'post_author' => get_current_vendor_id(), 'post_title' => $title));
            wp_set_object_terms($duplicate_product->get_id(), absint(get_current_vendor()->term_id), $WCMb->taxonomy->taxonomy_name);

            // Add GTIN, if exists
            $gtin_data = wp_get_post_terms($product->get_id(), $WCMb->taxonomy->wcmb_gtin_taxonomy);
            if ($gtin_data) {
                $gtin_type = isset($gtin_data[0]->term_id) ? $gtin_data[0]->term_id : '';
                wp_set_object_terms($duplicate_product->get_id(), $gtin_type, $WCMb->taxonomy->wcmb_gtin_taxonomy, true);
            }
            $gtin_code = get_post_meta($product->get_id(), '_wcmb_gtin_code', true);
            if ($gtin_code)
                update_post_meta($duplicate_product->get_id(), '_wcmb_gtin_code', $gtin_code);

            $has_wcmb_spmv_map_id = get_post_meta($product->get_id(), '_wcmb_spmv_map_id', true);
            if ($has_wcmb_spmv_map_id) {
                $data = array('product_id' => $duplicate_product->get_id(), 'product_map_id' => $has_wcmb_spmv_map_id);
                update_post_meta($duplicate_product->get_id(), '_wcmb_spmv_map_id', $has_wcmb_spmv_map_id);
                wcmb_spmv_products_map($data, 'insert');
            } else {
                $data = array('product_id' => $duplicate_product->get_id());
                $map_id = wcmb_spmv_products_map($data, 'insert');

                if ($map_id) {
                    update_post_meta($duplicate_product->get_id(), '_wcmb_spmv_map_id', $map_id);
                    // Enroll in SPMV parent product too 
                    $data = array('product_id' => $product->get_id(), 'product_map_id' => $map_id);
                    wcmb_spmv_products_map($data, 'insert');
                    update_post_meta($product->get_id(), '_wcmb_spmv_map_id', $map_id);
                }
                update_post_meta($product->get_id(), '_wcmb_spmv_product', true);
            }
            update_post_meta($duplicate_product->get_id(), '_wcmb_spmv_product', true);
            $duplicate_product->save();
            do_action('wcmb_create_duplicate_product', $duplicate_product);
            $permalink_structure = get_option('permalink_structure');
            if (!empty($permalink_structure)) {
                $redirect_url .= $duplicate_product->get_id();
            } else {
                $redirect_url .= '=' . $duplicate_product->get_id();
            }
            $response['status'] = true;
            $response['redirect_url'] = htmlspecialchars_decode($redirect_url);
        }
        wp_send_json($response);
    }

    function wcmb_auto_suggesion_product() {
        global $WCMb, $wpdb;
        check_ajax_referer('search-products', 'security');
        $user = wp_get_current_user();
        $term = wc_clean(empty($term) ? stripslashes($_REQUEST['protitle']) : $term);
        $is_admin = $_REQUEST['is_admin'];

        if (empty($term)) {
            wp_die();
        }

        $data_store = WC_Data_Store::load('product');
        $ids = $data_store->search_products($term, '', false);

        $include = array();
        foreach ($ids as $id) {
//            $_product = wc_get_product($id);
//            if ($_product && !$_product->get_parent_id()) {
//                $include[] = $_product->get_id();
//            }
            $product_map_id = get_post_meta($id, '_wcmb_spmv_map_id', true);
            if ($product_map_id) {
                $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wcmb_products_map WHERE product_map_id=%d", $product_map_id));
                $product_ids = wp_list_pluck($results, 'product_id');
                $first_inserted_map_pro_key = array_search(min(wp_list_pluck($results, 'ID')), wp_list_pluck($results, 'ID'));
                if (isset($product_ids[$first_inserted_map_pro_key])) {
                    $include[] = $product_ids[$first_inserted_map_pro_key];
                }
            } else {
                $include[] = $id;
            }
        }

        if ($include) {
            $ids = array_slice(array_intersect($ids, $include), 0, 10);
        } else {
            $ids = array();
        }
        $product_objects = apply_filters('wcmb_auto_suggesion_product_objects', array_map('wc_get_product', $ids), $user);
        $html = '';
        if (count($product_objects) > 0) {
            $html .= "<ul>";
            foreach ($product_objects as $product_object) {
                if ($product_object) {
                    if (is_user_wcmb_vendor($user) && $WCMb->vendor_caps->vendor_can($product_object->get_type())) {
                        if ($is_admin == 'false') {
                            $html .= "<li><a data-product_id='{$product_object->get_id()}' href='javascript:void(0)'>" . rawurldecode($product_object->get_formatted_name()) . "</a></li>";
                        } else {
                            $html .= "<li data-element='{$product_object->get_id()}'><a href='" . wp_nonce_url(admin_url('edit.php?post_type=product&action=duplicate_product&singleproductmultiseller=1&post=' . $product_object->get_id()), 'woocommerce-duplicate-product_' . $product_object->get_id()) . "'>" . rawurldecode($product_object->get_formatted_name()) . "</a></li>";
                        }
                    } elseif (!is_user_wcmb_vendor($user) && current_user_can('edit_products')) {
                        $html .= "<li data-element='{$product_object->get_id()}'><a href='" . wp_nonce_url(admin_url('edit.php?post_type=product&action=duplicate_product&singleproductmultiseller=1&post=' . $product_object->get_id()), 'woocommerce-duplicate-product_' . $product_object->get_id()) . "'>" . rawurldecode($product_object->get_formatted_name()) . "</a></li>";
                    }
                }
            }
            $html .= "</ul>";
        } else {
            $html .= "<ul><li class='wcmb_no-suggesion'>" . __('No Suggestion found', 'MB-multivendor') . "</li></ul>";
        }

        wp_send_json(array('html' => $html, 'results_count' => count($product_objects)));
    }

    public function wcmb_dismiss_dashboard_message() {
        global $wpdb, $WCMb;
        $post_id = $_POST['post_id'];
        $current_user = wp_get_current_user();
        $current_user_id = $current_user->ID;
        $data_msg_deleted = get_user_meta($current_user_id, '_wcmb_vendor_message_deleted', true);
        if (!empty($data_msg_deleted)) {
            $data_arr = explode(',', $data_msg_deleted);
            $data_arr[] = $post_id;
            $data_str = implode(',', $data_arr);
        } else {
            $data_arr[] = $post_id;
            $data_str = implode(',', $data_arr);
        }
        $is_updated = update_user_meta($current_user_id, '_wcmb_vendor_message_deleted', $data_str);
        if ($is_updated) {
            $dismiss_notices_ids_array = array();
            $dismiss_notices_ids = get_user_meta($current_user_id, '_wcmb_vendor_message_deleted', true);
            if (!empty($dismiss_notices_ids)) {
                $dismiss_notices_ids_array = explode(',', $dismiss_notices_ids);
            } else {
                $dismiss_notices_ids_array = array();
            }
            $args_msg = array(
                'posts_per_page' => 1,
                'offset' => 0,
                'post__not_in' => $dismiss_notices_ids_array,
                'orderby' => 'date',
                'order' => 'DESC',
                'post_type' => 'wcmb_vendor_notice',
                'post_status' => 'publish',
                'suppress_filters' => true
            );
            $msgs_array = get_posts($args_msg);
            if (is_array($msgs_array) && !empty($msgs_array) && count($msgs_array) > 0) {
                $msg = $msgs_array[0];
                ?>
                <h2><?php echo __('Admin Message:', 'MB-multivendor'); ?> </h2>
                <span> <?php echo $msg->post_title; ?> </span><br/>
                <span class="mormaltext" style="font-weight:normal;"> <?php
                    echo $short_content = substr(stripslashes(strip_tags($msg->post_content)), 0, 155);
                    if (strlen(stripslashes(strip_tags($msg->post_content))) > 155) {
                        echo '...';
                    }
                    ?> </span><br/>
                <a href="<?php echo get_permalink(get_option('wcmb_product_vendor_messages_page_id')); ?>"><button><?php echo __('DETAILS', 'MB-multivendor'); ?></button></a>
                <div class="clear"></div>
                <a href="#" id="cross-admin" data-element = "<?php echo $msg->ID; ?>"  class="wcmb_cross wcmb_delate_message_dashboard"><i class="fa fa-times-circle"></i></a>
                    <?php
                } else {
                    ?>
                <h2><?php echo __('No Messages Found:', 'MB-multivendor'); ?> </h2>
                <?php
            }
        } else {
            ?>
            <h2><?php echo __('Error in process:', 'MB-multivendor'); ?> </h2>
            <?php
        }
        die;
    }

    public function wcmb_msg_refresh_tab_data() {
        global $wpdb, $WCMb;
        $tab = $_POST['tabname'];
        $WCMb->template->get_template('vendor-dashboard/vendor-announcements/vendor-announcements' . str_replace("_", "-", $tab) . '.php');
        die;
    }

    public function wcmb_vendor_messages_operation() {
        global $wpdb, $WCMb;
        $current_user = wp_get_current_user();
        $current_user_id = $current_user->ID;
        $post_id = $_POST['msg_id'];
        $actionmode = $_POST['actionmode'];
        if ($actionmode == "mark_delete") {
            $data_msg_deleted = get_user_meta($current_user_id, '_wcmb_vendor_message_deleted', true);
            if (!empty($data_msg_deleted)) {
                $data_arr = explode(',', $data_msg_deleted);
                $data_arr[] = $post_id;
                $data_str = implode(',', $data_arr);
            } else {
                $data_arr[] = $post_id;
                $data_str = implode(',', $data_arr);
            }
            if (update_user_meta($current_user_id, '_wcmb_vendor_message_deleted', $data_str)) {
                echo 1;
            } else {
                echo 0;
            }
        } elseif ($actionmode == "mark_read") {
            $data_msg_readed = get_user_meta($current_user_id, '_wcmb_vendor_message_readed', true);
            if (!empty($data_msg_readed)) {
                $data_arr = explode(',', $data_msg_readed);
                $data_arr[] = $post_id;
                $data_str = implode(',', $data_arr);
            } else {
                $data_arr[] = $post_id;
                $data_str = implode(',', $data_arr);
            }
            if (update_user_meta($current_user_id, '_wcmb_vendor_message_readed', $data_str)) {
                echo __('Mark Unread', 'MB-multivendor');
            } else {
                echo 0;
            }
        } elseif ($actionmode == "mark_unread") {
            $data_msg_readed = get_user_meta($current_user_id, '_wcmb_vendor_message_readed', true);
            if (!empty($data_msg_readed)) {
                $data_arr = explode(',', $data_msg_readed);
                if (is_array($data_arr)) {
                    if (($key = array_search($post_id, $data_arr)) !== false) {
                        unset($data_arr[$key]);
                    }
                }
                $data_str = implode(',', $data_arr);
            }
            if (update_user_meta($current_user_id, '_wcmb_vendor_message_readed', $data_str)) {
                echo __('Mark Read', 'MB-multivendor');
            } else {
                echo 0;
            }
        } elseif ($actionmode == "mark_restore") {
            $data_msg_deleted = get_user_meta($current_user_id, '_wcmb_vendor_message_deleted', true);
            if (!empty($data_msg_deleted)) {
                $data_arr = explode(',', $data_msg_deleted);
                if (is_array($data_arr)) {
                    if (($key = array_search($post_id, $data_arr)) !== false) {
                        unset($data_arr[$key]);
                    }
                }
                $data_str = implode(',', $data_arr);
            }
            if (update_user_meta($current_user_id, '_wcmb_vendor_message_deleted', $data_str)) {
                echo __('Mark Restore', 'MB-multivendor');
            } else {
                echo 0;
            }
        }
        die;
    }

    public function wcmb_frontend_sale_get_row_callback() {
        global $wpdb, $WCMb;
        $user = wp_get_current_user();
        $vendor = get_wcmb_vendor($user->ID);
        $today_or_weekly = $_POST['today_or_weekly'];
        $current_page = $_POST['current_page'];
        $next_page = $_POST['next_page'];
        $total_page = $_POST['total_page'];
        $perpagedata = $_POST['perpagedata'];
        if ($next_page <= $total_page) {
            if ($next_page > 1) {
                $start = ($next_page - 1) * $perpagedata;
                $WCMb->template->get_template('vendor-dashboard/dashboard/vendor-dashboard-sales-item.php', array('vendor' => $vendor, 'today_or_weekly' => $today_or_weekly, 'start' => $start, 'to' => $perpagedata));
            }
        } else {
            echo "<tr><td colspan='5'>" . __('no more data found', 'MB-multivendor') . "</td></tr>";
        }
        die;
    }

    public function wcmb_frontend_pending_shipping_get_row_callback() {
        global $wpdb, $WCMb;
        $user = wp_get_current_user();
        $vendor = get_wcmb_vendor($user->ID);
        $today_or_weekly = $_POST['today_or_weekly'];
        $current_page = $_POST['current_page'];
        $next_page = $_POST['next_page'];
        $total_page = $_POST['total_page'];
        $perpagedata = $_POST['perpagedata'];
        if ($next_page <= $total_page) {
            if ($next_page > 1) {
                $start = ($next_page - 1) * $perpagedata;
                $WCMb->template->get_template('vendor-dashboard/dashboard/vendor-dasboard-pending-shipping-items.php', array('vendor' => $vendor, 'today_or_weekly' => $today_or_weekly, 'start' => $start, 'to' => $perpagedata));
            }
        } else {
            echo "<tr><td colspan='5'>" . __('no more data found', 'MB-multivendor') . "</td></tr>";
        }
        die;
    }

    function show_more_transaction() {
        global $WCMb;
        $data_to_show = $_POST['data_to_show'];
        $WCMb->template->get_template('vendor-dashboard/vendor-transactions/vendor-transaction-items.php', array('transactions' => $data_to_show));
        die;
    }

    function withdrawal_more_orders() {
        global $WCMb;
        $user = wp_get_current_user();
        $vendor = get_wcmb_vendor($user->ID);
        $offset = $_POST['offset'];
        $meta_query['meta_query'] = array(
            array(
                'key' => '_paid_status',
                'value' => 'unpaid',
                'compare' => '='
            ),
            array(
                'key' => '_commission_vendor',
                'value' => absint($vendor->term_id),
                'compare' => '='
            )
        );
        $customer_orders = $vendor->get_orders(6, $offset, $meta_query);
        $WCMb->template->get_template('vendor-dashboard/vendor-withdrawal/vendor-withdrawal-items.php', array('vendor' => $vendor, 'commissions' => $customer_orders));
        die;
    }

    function wcmb_vendor_csv_download_per_order() {
        global $WCMb, $wpdb;

        if (isset($_GET['action']) && isset($_GET['order_id']) && isset($_GET['nonce'])) {
            $action = $_GET['action'];
            $order_id = $_GET['order_id'];
            $nonce = $_REQUEST["nonce"];

            if (!wp_verify_nonce($nonce, $action))
                die('Invalid request');

            $vendor = get_wcmb_vendor(get_current_vendor_id());
            $vendor = apply_filters('wcmb_csv_download_per_order_vendor', $vendor);
            if (!$vendor)
                die('Invalid request');
            $order_data = array();
            $customer_orders = $wpdb->get_results("SELECT DISTINCT commission_id from `{$wpdb->prefix}wcmb_vendor_orders` where vendor_id = " . $vendor->id . " AND order_id = " . $order_id, ARRAY_A);
            if (!empty($customer_orders)) {
                $commission_id = $customer_orders[0]['commission_id'];
                $order_data[$commission_id] = $order_id;
                $WCMb->vendor_dashboard->generate_csv($order_data, $vendor);
            }
            die;
        }
    }

    /**
     * Unassign vendor from a product
     */
    function unassign_vendor() {
        global $WCMb;

        $product_id = $_POST['product_id'];
        $vendor = get_wcmb_product_vendors($product_id);
        $admin_id = get_current_user_id();
        if (current_user_can('administrator')) {
            $_product = wc_get_product($product_id);
            $orders = array();
            if ($_product->is_type('variable')) {
                $get_children = $_product->get_children();
                if (!empty($get_children)) {
                    foreach ($get_children as $child) {
                        $orders = array_merge($orders, $vendor->get_vendor_orders_by_product($vendor->term_id, $child));
                    }
                    $orders = array_unique($orders);
                }
            } else {
                $orders = array_unique($vendor->get_vendor_orders_by_product($vendor->term_id, $product_id));
            }

            foreach ($orders as $order_id) {
                $order = new WC_Order($order_id);
                $items = $order->get_items('line_item');
                foreach ($items as $item_id => $item) {
                    wc_add_order_item_meta($item_id, '_vendor_id', $vendor->id);
                }
            }

            wp_delete_object_term_relationships($product_id, $WCMb->taxonomy->taxonomy_name);
            wp_delete_object_term_relationships($product_id, 'product_shipping_class');
            wp_update_post(array('ID' => $product_id, 'post_author' => $admin_id));
            delete_post_meta($product_id, '_commission_per_product');
            delete_post_meta($product_id, '_commission_percentage_per_product');
            delete_post_meta($product_id, '_commission_fixed_with_percentage_qty');
            delete_post_meta($product_id, '_commission_fixed_with_percentage');

            $product_obj = wc_get_product($product_id);
            if ($product_obj->is_type('variable')) {
                $child_ids = $product_obj->get_children();
                if (isset($child_ids) && !empty($child_ids)) {
                    foreach ($child_ids as $child_id) {
                        delete_post_meta($child_id, '_commission_fixed_with_percentage');
                        delete_post_meta($child_id, '_product_vendors_commission_percentage');
                        delete_post_meta($child_id, '_product_vendors_commission_fixed_per_trans');
                        delete_post_meta($child_id, '_product_vendors_commission_fixed_per_qty');
                    }
                }
            }
        }

        die;
    }

    /**
     * wcmb Product Report sorting
     */
    function product_report_sort() {
        global $WCMb;

        $sort_choosen = isset($_POST['sort_choosen']) ? $_POST['sort_choosen'] : '';
        $report_array = isset($_POST['report_array']) ? $_POST['report_array'] : array();
        $report_bk = isset($_POST['report_bk']) ? $_POST['report_bk'] : array();
        $max_total_sales = isset($_POST['max_total_sales']) ? $_POST['max_total_sales'] : 0;
        $total_sales_sort = isset($_POST['total_sales_sort']) ? $_POST['total_sales_sort'] : array();
        $admin_earning_sort = isset($_POST['admin_earning_sort']) ? $_POST['admin_earning_sort'] : array();
        ;

        $i = 0;
        $max_value = 10;
        $report_sort_arr = array();

        if ($sort_choosen == 'total_sales_desc') {
            arsort($total_sales_sort);
            foreach ($total_sales_sort as $product_id => $value) {
                if ($i++ < $max_value) {
                    $report_sort_arr[$product_id]['total_sales'] = $report_bk[$product_id]['total_sales'];
                    $report_sort_arr[$product_id]['admin_earning'] = $report_bk[$product_id]['admin_earning'];
                }
            }
        } else if ($sort_choosen == 'total_sales_asc') {
            asort($total_sales_sort);
            foreach ($total_sales_sort as $product_id => $value) {
                if ($i++ < $max_value) {
                    $report_sort_arr[$product_id]['total_sales'] = $report_bk[$product_id]['total_sales'];
                    $report_sort_arr[$product_id]['admin_earning'] = $report_bk[$product_id]['admin_earning'];
                }
            }
        } else if ($sort_choosen == 'admin_earning_desc') {
            arsort($admin_earning_sort);
            foreach ($admin_earning_sort as $product_id => $value) {
                if ($i++ < $max_value) {
                    $report_sort_arr[$product_id]['total_sales'] = $report_bk[$product_id]['total_sales'];
                    $report_sort_arr[$product_id]['admin_earning'] = $report_bk[$product_id]['admin_earning'];
                }
            }
        } else if ($sort_choosen == 'admin_earning_asc') {
            asort($admin_earning_sort);
            foreach ($admin_earning_sort as $product_id => $value) {
                if ($i++ < $max_value) {
                    $report_sort_arr[$product_id]['total_sales'] = $report_bk[$product_id]['total_sales'];
                    $report_sort_arr[$product_id]['admin_earning'] = $report_bk[$product_id]['admin_earning'];
                }
            }
        }

        $report_chart = $report_html = '';

        if (sizeof($report_sort_arr) > 0) {
            foreach ($report_sort_arr as $product_id => $sales_report) {
                $width = ( $sales_report['total_sales'] > 0 ) ? ( round($sales_report['total_sales']) / round($max_total_sales) ) * 100 : 0;
                $width2 = ( $sales_report['admin_earning'] > 0 ) ? ( round($sales_report['admin_earning']) / round($max_total_sales) ) * 100 : 0;

                $product = new WC_Product($product_id);
                $product_url = admin_url('post.php?post=' . $product_id . '&action=edit');

                $report_chart .= '<tr><th><a href="' . $product_url . '">' . $product->get_title() . '</a></th>
                    <td width="1%"><span>' . wc_price($sales_report['total_sales']) . '</span><span class="alt">' . wc_price($sales_report['admin_earning']) . '</span></td>
                    <td class="bars">
                        <span style="width:' . esc_attr($width) . '%">&nbsp;</span>
                        <span class="alt" style="width:' . esc_attr($width2) . '%">&nbsp;</span>
                    </td></tr>';
            }

            $report_html = '
                <h4>' . __("Sales and Earnings", 'MB-multivendor') . '</h4>
                <div class="bar_indecator">
                    <div class="bar1">&nbsp;</div>
                    <span class="">' . __("Gross Sales", 'MB-multivendor') . '</span>
                    <div class="bar2">&nbsp;</div>
                    <span class="">' . __("My Earnings", 'MB-multivendor') . '</span>
                </div>
                <table class="bar_chart">
                    <thead>
                        <tr>
                            <th>' . __("Month", 'MB-multivendor') . '</th>
                            <th colspan="2">' . __("Sales Report", 'MB-multivendor') . '</th>
                        </tr>
                    </thead>
                    <tbody>
                        ' . $report_chart . '
                    </tbody>
                </table>
            ';
        } else {
            $report_html = '<tr><td colspan="3">' . __('No product was sold in the given period.', 'MB-multivendor') . '</td></tr>';
        }

        echo $report_html;

        die;
    }

    function send_enquiry_to_vendor($send_to, $product_id) {
        global $WCMb;
        $vendor = get_wcmb_product_vendors($product_id);
        if ($vendor) {
            $send_to = $vendor->user_data->data->user_email;
        }
        return $send_to;
    }

    /**
     * wcmb Product Data Searching
     */
    function search_product_data() {
        global $WCMb;

        $product_id = $_POST['product_id'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        $report_chart = $report_html = '';

        if ($product_id) {

            $total_sales = $admin_earnings = array();
            $max_total_sales = 0;

            $product_orders = get_wcmb_vendor_orders(array('product_id' => $product_id));

            if (!empty($product_orders)) {

                $gross_sales = $my_earning = $vendor_earning = 0;
                foreach ($product_orders as $order_obj) {
                    $order = new WC_Order($order_obj->order_id);

                    if (strtotime($order->get_date_created()) > $start_date && strtotime($order->get_date_created()) < $end_date) {
                        // Get date
                        $date = date('Ym', strtotime($order->get_date_created()));

                        $item = new WC_Order_Item_Product($order_obj->order_item_id);
                        $gross_sales += $item->get_subtotal();
                        $total_sales[$date] = isset($total_sales[$date]) ? ( $total_sales[$date] + $item->get_subtotal() ) : $item->get_subtotal();
                        $vendors_orders_amount = get_wcmb_vendor_order_amount(array('order_id' => $order->get_id(), 'product_id' => $order_obj->product_id));

                        $vendor_earning = $vendors_orders_amount['commission_amount'];
                        if ($vendor = get_wcmb_vendor(get_current_vendor_id()))
                            $admin_earnings[$date] = isset($admin_earnings[$date]) ? ( $admin_earnings[$date] + $vendor_earning ) : $vendor_earning;
                        else
                            $admin_earnings[$date] = isset($admin_earnings[$date]) ? ( $admin_earnings[$date] + $item->get_subtotal() - $vendor_earning ) : $item->get_subtotal() - $vendor_earning;

                        if ($total_sales[$date] > $max_total_sales)
                            $max_total_sales = $total_sales[$date];
                    }
                }
            }


            if (sizeof($total_sales) > 0) {
                foreach ($total_sales as $date => $sales) {
                    $width = ( $sales > 0 ) ? ( round($sales) / round($max_total_sales) ) * 100 : 0;
                    $width2 = ( $admin_earnings[$date] > 0 ) ? ( round($admin_earnings[$date]) / round($max_total_sales) ) * 100 : 0;

                    $report_chart .= '<tr><th>' . date_i18n('F', strtotime($date . '01')) . '</th>
                        <td width="1%"><span>' . wc_price($sales) . '</span><span class="alt">' . wc_price($admin_earnings[$date]) . '</span></td>
                        <td class="bars">
                            <span style="width:' . esc_attr($width) . '%">&nbsp;</span>
                            <span class="alt" style="width:' . esc_attr($width2) . '%">&nbsp;</span>
                        </td></tr>';
                }

                $report_html = '
                    <h4>' . __("Sales and Earnings", 'MB-multivendor') . '</h4>
                    <div class="bar_indecator">
                        <div class="bar1">&nbsp;</div>
                        <span class="">' . __("Gross Sales", 'MB-multivendor') . '</span>
                        <div class="bar2">&nbsp;</div>
                        <span class="">' . __("My Earnings", 'MB-multivendor') . '</span>
                    </div>
                    <table class="bar_chart">
                        <thead>
                            <tr>
                                <th>' . __("Month", 'MB-multivendor') . '</th>
                                <th colspan="2">' . __("Sales Report", 'MB-multivendor') . '</th>
                            </tr>
                        </thead>
                        <tbody>
                            ' . $report_chart . '
                        </tbody>
                    </table>
                ';
            } else {
                $report_html = '<tr><td colspan="3">' . __('This product was not sold in the given period.', 'MB-multivendor') . '</td></tr>';
            }

            echo $report_html;
        } else {
            echo '<tr><td colspan="3">' . __('Please select a product.', 'MB-multivendor') . '</td></tr>';
        }

        die;
    }

    /**
     * wcmb Vendor Data Searching
     */
    function search_vendor_data() {
        global $WCMb, $wpdb;

        $chosen_product_ids = $vendor_id = $vendor = false;
        $gross_sales = $my_earning = $vendor_earning = 0;
        $vendor_term_id = $_POST['vendor_id'];
        $vendor = get_wcmb_vendor_by_term($vendor_term_id);
        $vendor_id = $vendor->id;
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        if ($vendor_id) {
            if ($vendor)
                $products = $vendor->get_products();
            if (!empty($products)) {
                foreach ($products as $product) {
                    $chosen_product_ids[] = $product->ID;
                }
            }
        }

        if ($vendor_id && empty($products)) {
            $no_vendor = '<h4>' . __("Sales and Earnings", 'MB-multivendor') . '</h4>
            <table class="bar_chart">
                <thead>
                    <tr>
                        <th>' . __("Month", 'MB-multivendor') . '</th>
                        <th colspan="2">' . __("Sales", 'MB-multivendor') . '</th>
                    </tr>
                </thead>
                <tbody> 
                    <tr><td colspan="3">' . __("No Sales :(", 'MB-multivendor') . '</td></tr>
                </tbody>
            </table>';

            echo $no_vendor;
            die;
        }

        $args = array(
            'post_type' => 'shop_order',
            'posts_per_page' => -1,
            'post_status' => array('wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed', 'wc-cancelled', 'wc-refunded', 'wc-failed'),
            'meta_query' => array(
                array(
                    'key' => '_commissions_processed',
                    'value' => 'yes',
                    'compare' => '='
                )
            ),
            'date_query' => array(
                'inclusive' => true,
                'after' => array(
                    'year' => date('Y', $start_date),
                    'month' => date('n', $start_date),
                    'day' => date('j', $start_date),
                ),
                'before' => array(
                    'year' => date('Y', $end_date),
                    'month' => date('n', $end_date),
                    'day' => date('j', $end_date),
                ),
            )
        );

        $qry = new WP_Query($args);

        $orders = apply_filters('wcmb_filter_orders_report_vendor', $qry->get_posts());

        if (!empty($orders)) {

            $total_sales = $admin_earning = array();
            $max_total_sales = 0;

            foreach ($orders as $order_obj) {
                $order = new WC_Order($order_obj->ID);
                $vendors_orders = get_wcmb_vendor_orders(array('order_id' => $order->get_id()));
                $vendors_orders_amount = get_wcmb_vendor_order_amount(array('order_id' => $order->get_id()), $vendor_id);
                $current_vendor_orders = wp_list_filter($vendors_orders, array('vendor_id' => $vendor_id));
                $gross_sales += $vendors_orders_amount['total'] - $vendors_orders_amount['commission_amount'];
                $vendor_earning += $vendors_orders_amount['total'];

                foreach ($current_vendor_orders as $key => $vendor_order) {
                    $item = new WC_Order_Item_Product($vendor_order->order_item_id);
                    $gross_sales += $item->get_subtotal();
                }
                // Get date
                $date = date('Ym', strtotime($order->get_date_created()));

                // Set values
                $total_sales[$date] = $gross_sales;
                $admin_earning[$date] = $gross_sales - $vendor_earning;

                if ($total_sales[$date] > $max_total_sales)
                    $max_total_sales = $total_sales[$date];
            }

            $report_chart = $report_html = '';
            if (count($total_sales) > 0) {
                foreach ($total_sales as $date => $sales) {
                    $width = ( $sales > 0 ) ? ( round($sales) / round($max_total_sales) ) * 100 : 0;
                    $width2 = ( $admin_earning[$date] > 0 ) ? ( round($admin_earning[$date]) / round($max_total_sales) ) * 100 : 0;

                    $orders_link = admin_url('edit.php?s&post_status=all&post_type=shop_order&action=-1&s=' . urlencode(implode(' ', $chosen_product_titles)) . '&m=' . date('Ym', strtotime($date . '01')) . '&shop_order_status=' . implode(",", apply_filters('woocommerce_reports_order_statuses', array('completed', 'processing', 'on-hold'))));
                    $orders_link = apply_filters('woocommerce_reports_order_link', $orders_link, $chosen_product_ids, $chosen_product_titles);

                    $report_chart .= '<tr><th><a href="' . esc_url($orders_link) . '">' . date_i18n('F', strtotime($date . '01')) . '</a></th>
                        <td width="1%"><span>' . wc_price($sales) . '</span><span class="alt">' . wc_price($admin_earning[$date]) . '</span></td>
                        <td class="bars">
                            <span class="main" style="width:' . esc_attr($width) . '%">&nbsp;</span>
                            <span class="alt" style="width:' . esc_attr($width2) . '%">&nbsp;</span>
                        </td></tr>';
                }

                $report_html = '
                    <h4>' . $vendor_title . '</h4>
                    <div class="bar_indecator">
                        <div class="bar1">&nbsp;</div>
                        <span class="">' . __("Gross Sales", 'MB-multivendor') . '</span>
                        <div class="bar2">&nbsp;</div>
                        <span class="">' . __("My Earnings", 'MB-multivendor') . '</span>
                    </div>
                    <table class="bar_chart">
                        <thead>
                            <tr>
                                <th>' . __("Month", 'MB-multivendor') . '</th>
                                <th colspan="2">' . __("Vendor Earnings", 'MB-multivendor') . '</th>
                            </tr>
                        </thead>
                        <tbody>
                            ' . $report_chart . '
                        </tbody>
                    </table>
                ';
            } else {
                $report_html = '<tr><td colspan="3">' . __('This vendor did not generate any sales in the given period.', 'MB-multivendor') . '</td></tr>';
            }
        }

        echo $report_html;

        die;
    }

    /**
     * wcmb Vendor Report sorting
     */
    function vendor_report_sort() {
        global $WCMb;

        $dropdown_selected = isset($_POST['sort_choosen']) ? $_POST['sort_choosen'] : '';
        $vendor_report = isset($_POST['report_array']) ? $_POST['report_array'] : array();
        $report_bk = isset($_POST['report_bk']) ? $_POST['report_bk'] : array();
        $max_total_sales = isset($_POST['max_total_sales']) ? $_POST['max_total_sales'] : 0;
        $total_sales_sort = isset($_POST['total_sales_sort']) ? $_POST['total_sales_sort'] : array();
        $admin_earning_sort = isset($_POST['admin_earning_sort']) ? $_POST['admin_earning_sort'] : array();
        $report_sort_arr = array();
        $chart_arr = '';
        $i = 0;
        $max_value = 10;

        if ($dropdown_selected == 'total_sales_desc') {
            arsort($total_sales_sort);
            foreach ($total_sales_sort as $key => $value) {
                if ($i++ < $max_value) {
                    $report_sort_arr[$key]['total_sales'] = $report_bk[$key]['total_sales'];
                    $report_sort_arr[$key]['admin_earning'] = $report_bk[$key]['admin_earning'];
                }
            }
        } else if ($dropdown_selected == 'total_sales_asc') {
            asort($total_sales_sort);
            foreach ($total_sales_sort as $key => $value) {
                if ($i++ < $max_value) {
                    $report_sort_arr[$key]['total_sales'] = $report_bk[$key]['total_sales'];
                    $report_sort_arr[$key]['admin_earning'] = $report_bk[$key]['admin_earning'];
                }
            }
        } else if ($dropdown_selected == 'admin_earning_desc') {
            arsort($admin_earning_sort);
            foreach ($admin_earning_sort as $key => $value) {
                if ($i++ < $max_value) {
                    $report_sort_arr[$key]['total_sales'] = $report_bk[$key]['total_sales'];
                    $report_sort_arr[$key]['admin_earning'] = $report_bk[$key]['admin_earning'];
                }
            }
        } else if ($dropdown_selected == 'admin_earning_asc') {
            asort($admin_earning_sort);
            foreach ($admin_earning_sort as $key => $value) {
                if ($i++ < $max_value) {
                    $report_sort_arr[$key]['total_sales'] = $report_bk[$key]['total_sales'];
                    $report_sort_arr[$key]['admin_earning'] = $report_bk[$key]['admin_earning'];
                }
            }
        }

        if (sizeof($report_sort_arr) > 0) {
            foreach ($report_sort_arr as $vendor_id => $sales_report) {
                $total_sales_width = ( $sales_report['total_sales'] > 0 ) ? $sales_report['total_sales'] / round($max_total_sales) * 100 : 0;
                $admin_earning_width = ( $sales_report['admin_earning'] > 0 ) ? ( $sales_report['admin_earning'] / round($max_total_sales) ) * 100 : 0;

                $user = get_userdata($vendor_id);
                $user_name = $user->data->display_name;

                $chart_arr .= '<tr><th><a href="user-edit.php?user_id=' . $vendor_id . '">' . $user_name . '</a></th>
                <td width="1%"><span>' . wc_price($sales_report['total_sales']) . '</span><span class="alt">' . wc_price($sales_report['admin_earning']) . '</span></td>
                <td class="bars">
                    <span class="main" style="width:' . esc_attr($total_sales_width) . '%">&nbsp;</span>
                    <span class="alt" style="width:' . esc_attr($admin_earning_width) . '%">&nbsp;</span>
                </td></tr>';
            }

            $html_chart = '
                <h4>' . __("Sales and Earnings", 'MB-multivendor') . '</h4>
                <div class="bar_indecator">
                    <div class="bar1">&nbsp;</div>
                    <span class="">' . __("Gross Sales", 'MB-multivendor') . '</span>
                    <div class="bar2">&nbsp;</div>
                    <span class="">' . __("My Earnings", 'MB-multivendor') . '</span>
                </div>
                <table class="bar_chart">
                    <thead>
                        <tr>
                            <th>' . __("Vendors", 'MB-multivendor') . '</th>
                            <th colspan="2">' . __("Sales Report", 'MB-multivendor') . '</th>
                        </tr>
                    </thead>
                    <tbody>
                        ' . $chart_arr . '
                    </tbody>
                </table>
            ';
        } else {
            $html_chart = '<tr><td colspan="3">' . __('Any vendor did not generate any sales in the given period.', 'MB-multivendor') . '</td></tr>';
        }

        echo $html_chart;

        die;
    }

    /**
     * wcmb Transaction complete mark
     */
    function transaction_done_button() {
        global $WCMb;
        $transaction_id = $_POST['trans_id'];
        $vendor_id = $_POST['vendor_id'];
        update_post_meta($transaction_id, 'paid_date', date("Y-m-d H:i:s"));
        $commission_detail = get_post_meta($transaction_id, 'commission_detail', true);
        if ($commission_detail && is_array($commission_detail)) {
            foreach ($commission_detail as $commission_id) {
                wcmb_paid_commission_status($commission_id);
            }
            $email_admin = WC()->mailer()->emails['WC_Email_Vendor_Commission_Transactions'];
            $email_admin->trigger($transaction_id, $vendor_id);
            update_post_meta($transaction_id, '_dismiss_to_do_list', 'true');
            wp_update_post(array('ID' => $transaction_id, 'post_status' => 'wcmb_completed'));
        }
        die;
    }

    /**
     * wcmb get more orders
     */
    function get_more_orders() {
        global $WCMb;
        $data_to_show = isset($_POST['data_to_show']) ? $_POST['data_to_show'] : '';
        $order_status = isset($_POST['order_status']) ? $_POST['order_status'] : '';
        $vendor = get_wcmb_vendor(get_current_vendor_id());
        $WCMb->template->get_template('vendor-dashboard/vendor-orders/vendor-orders-item.php', array('vendor' => $vendor, 'orders' => $data_to_show, 'order_status' => $order_status));
        die;
    }

    /**
     * wcmb dismiss todo list
     */
    function dismiss_vendor_to_do_list() {
        global $WCMb;

        $id = $_POST['id'];
        $type = $_POST['type'];
        if ($type == 'user') {
            update_user_meta($id, '_dismiss_to_do_list', 'true');
        } else if ($type == 'shop_coupon') {
            update_post_meta($id, '_dismiss_to_do_list', 'true');
        } else if ($type == 'product') {
            update_post_meta($id, '_dismiss_to_do_list', 'true');
        } else if ($type == 'dc_commission') {
            update_post_meta($id, '_dismiss_to_do_list', 'true');
            wp_update_post(array('ID' => $id, 'post_status' => 'wcmb_canceled'));
        }
        die();
    }

    /**
     * wcmb current user attachment
     */
    function show_current_user_attachments($query = array()) {
        $user_id = get_current_vendor_id();
        if (is_user_wcmb_vendor($user_id)) {
            $query['author'] = $user_id;
        }
        return $query;
    }

    /**
     * Search vendors via AJAX
     *
     * @return void
     */
    function woocommerce_json_search_vendors() {
        global $WCMb;

        //check_ajax_referer( 'search-vendors', 'security' );

        header('Content-Type: application/json; charset=utf-8');

        $term = urldecode(stripslashes(strip_tags($_GET['term'])));

        if (empty($term))
            die();

        $found_vendors = array();

        $args1 = array(
            'search' => '*' . $term . '*',
            'search_columns' => array('user_login', 'display_name', 'user_email')
        );
        $args2 = array(
            'meta_key' => '_vendor_page_title',
            'meta_value' => esc_attr($term),
            'meta_compare' => 'LIKE',
        );
        $vendors1 = get_wcmb_vendors($args1);
        $vendors2 = get_wcmb_vendors($args2);
        $vendors = array_unique(array_merge($vendors1, $vendors2), SORT_REGULAR);

        if (!empty($vendors) && is_array($vendors)) {
            foreach ($vendors as $vendor) {
                $vendor_term = get_term($vendor->term_id);
                $found_vendors[$vendor->term_id] = $vendor_term->name;
            }
        }

        echo json_encode($found_vendors);
        die();
    }

    /**
     * Activate Pending Vendor via AJAX
     *
     * @return void
     */
    function activate_pending_vendor() {
        $user_id = filter_input(INPUT_POST, 'user_id');
        $redirect = filter_input(INPUT_POST, 'redirect');
        $custom_note = filter_input(INPUT_POST, 'custom_note');
        $note_by = filter_input(INPUT_POST, 'note_by');

        if ($user_id) {
            $user = new WP_User(absint($user_id));
            $user->set_role('dc_vendor');
            $user_dtl = get_userdata(absint($user_id));
            $email = WC()->mailer()->emails['WC_Email_Approved_New_Vendor_Account'];
            $email->trigger($user_id, $user_dtl->user_pass);

            if (isset($custom_note) && $custom_note != '') {
                $wcmb_vendor_rejection_notes = unserialize(get_user_meta($user_id, 'wcmb_vendor_rejection_notes', true));
                $wcmb_vendor_rejection_notes[time()] = array(
                    'note_by' => $note_by,
                    'note' => $custom_note);
                update_user_meta($user_id, 'wcmb_vendor_rejection_notes', serialize($wcmb_vendor_rejection_notes));
            }
        }

        if (isset($redirect) && $redirect)
            wp_send_json(array('redirect' => true, 'redirect_url' => wp_get_referer() ? wp_get_referer() : admin_url('admin.php?page=vendors')));
        exit;
    }

    /**
     * Reject Pending Vendor via AJAX
     *
     * @return void
     */
    function reject_pending_vendor() {
        $user_id = filter_input(INPUT_POST, 'user_id');
        $redirect = filter_input(INPUT_POST, 'redirect');
        $custom_note = filter_input(INPUT_POST, 'custom_note');
        $note_by = filter_input(INPUT_POST, 'note_by');

        if ($user_id) {
            $user = new WP_User(absint($user_id));
            $user->set_role('dc_rejected_vendor');

            if (isset($custom_note) && $custom_note != '') {
                $wcmb_vendor_rejection_notes = unserialize(get_user_meta($user_id, 'wcmb_vendor_rejection_notes', true));
                $wcmb_vendor_rejection_notes[time()] = array(
                    'note_by' => $note_by,
                    'note' => $custom_note);
                update_user_meta($user_id, 'wcmb_vendor_rejection_notes', serialize($wcmb_vendor_rejection_notes));
            }
        }

        if (isset($redirect) && $redirect)
            wp_send_json(array('redirect' => true, 'redirect_url' => wp_get_referer() ? wp_get_referer() : admin_url('admin.php?page=vendors')));
        exit;
    }

    /**
     * Suspend Vendor via AJAX
     *
     * @return void
     */
    function wcmb_suspend_vendor() {
        $user_id = filter_input(INPUT_POST, 'user_id');
        $redirect = filter_input(INPUT_POST, 'redirect');
        if ($user_id) {
            $user = new WP_User(absint($user_id));
            if (is_user_wcmb_vendor($user)) {
                update_user_meta($user_id, '_vendor_turn_off', 'Enable');
            }
        }
        if (isset($redirect) && $redirect)
            wp_send_json(array('redirect' => true, 'redirect_url' => wp_get_referer() ? wp_get_referer() : admin_url('admin.php?page=vendors')));
        exit;
    }

    /**
     * Activate Vendor via AJAX from Suspend state
     *
     * @return void
     */
    function wcmb_activate_vendor() {
        $user_id = filter_input(INPUT_POST, 'user_id');
        $redirect = filter_input(INPUT_POST, 'redirect');
        if ($user_id) {
            $user = new WP_User(absint($user_id));
            if (is_user_wcmb_vendor($user)) {
                delete_user_meta($user_id, '_vendor_turn_off');
            }
        }
        if (isset($redirect) && $redirect)
            wp_send_json(array('redirect' => true, 'redirect_url' => wp_get_referer() ? wp_get_referer() : admin_url('admin.php?page=vendors')));
        exit;
    }

    /**
     * Report Abuse Vendor via AJAX
     *
     * @return void
     */
    function send_report_abuse() {
        global $WCMb;
        $check = false;
        $name = sanitize_text_field($_POST['name']);
        $from_email = sanitize_email($_POST['email']);
        $user_message = sanitize_text_field($_POST['msg']);
        $product_id = sanitize_text_field($_POST['product_id']);

        $check = !empty($name) && !empty($from_email) && !empty($user_message);

        if ($check) {
            $product = get_post(absint($product_id));
            $vendor = get_wcmb_product_vendors($product_id);
            $vendor_term = get_term($vendor->term_id);
            $subject = __('Report an abuse for product', 'MB-multivendor') . get_the_title($product_id);

            $to = sanitize_email(get_option('admin_email'));
            $from_email = sanitize_email($from_email);
            $headers = "From: {$name} <{$from_email}>" . "\r\n";

            $message = sprintf(__("User %s (%s) is reporting an abuse on the following product: \n", 'MB-multivendor'), $name, $from_email);
            $message .= sprintf(__("Product details: %s (ID: #%s) \n", 'MB-multivendor'), $product->post_title, $product->ID);

            $message .= sprintf(__("Vendor shop: %s \n", 'MB-multivendor'), $vendor_term->name);

            $message .= sprintf(__("Message: %s\n", 'MB-multivendor'), $user_message);
            $message .= "\n\n\n";

            $message .= sprintf(__("Product page:: %s\n", 'MB-multivendor'), get_the_permalink($product->ID));

            /* === Send Mail === */
            $response = wp_mail($to, $subject, $message, $headers);
        }
        die();
    }

    /**
     * Set a flag while dismiss wcmb service notice
     */
    public function dismiss_wcmb_servive_notice() {
        $updated = update_option('_is_dismiss_service_notice', true);
        echo $updated;
        die();
    }

    function vendor_list_by_search_keyword() {
        global $WCMb;
        // check vendor_search_nonce
        if (!isset($_POST['vendor_search_nonce']) || !wp_verify_nonce($_POST['vendor_search_nonce'], 'wcmb_widget_vendor_search_form')) {
            die();
        }
        $html = '';
        if (isset($_POST['s']) && sanitize_text_field($_POST['s'])) {
            $args1 = array(
                'search' => '*' . esc_attr($_POST['s']) . '*',
                'search_columns' => array('display_name', 'user_login', 'user_nicename'),
            );
            $args2 = array(
                'meta_key' => '_vendor_page_title',
                'meta_value' => esc_attr($_POST['s']),
                'meta_compare' => 'LIKE',
            );
            $vendors1 = get_wcmb_vendors($args1);
            $vendors2 = get_wcmb_vendors($args2);
            $vendors = array_unique(array_merge($vendors1, $vendors2), SORT_REGULAR);

            if ($vendors) {
                foreach ($vendors as $vendors_key => $vendor) {
                    $vendor_term = get_term($vendor->term_id);
                    $vendor->image = $vendor->get_image() ? $vendor->get_image() : $WCMb->plugin_url . 'assets/images/WP-stdavatar.png';
                    $html .= '<div style=" width: 100%; margin-bottom: 5px; clear: both; display: block;">
                    <div style=" width: 25%;  display: inline;">        
                    <img width="50" height="50" class="vendor_img" style="display: inline;" src="' . $vendor->image . '" id="vendor_image_display">
                    </div>
                    <div style=" width: 75%;  display: inline;  padding: 10px;">
                            <a href="' . esc_attr($vendor->permalink) . '">
                                ' . $vendor_term->name . '
                            </a>
                    </div>
                </div>';
                }
            } else {
                $html .= '<div style=" width: 100%; margin-bottom: 5px; clear: both; display: block;">
                    <div style="display: inline;  padding: 10px;">
                        ' . __('No Vendor Matched!', 'MB-multivendor') . '
                    </div>
                </div>';
            }
        } else {
            $vendors = get_wcmb_vendors();
            if ($vendors) {
                foreach ($vendors as $vendors_key => $vendor) {
                    $vendor_term = get_term($vendor->term_id);
                    $vendor->image = $vendor->get_image() ? $vendor->get_image() : $WCMb->plugin_url . 'assets/images/WP-stdavatar.png';
                    $html .= '<div style=" width: 100%; margin-bottom: 5px; clear: both; display: block;">
                    <div style=" width: 25%;  display: inline;">        
                    <img width="50" height="50" class="vendor_img" style="display: inline;" src="' . $vendor->image . '" id="vendor_image_display">
                    </div>
                    <div style=" width: 75%;  display: inline;  padding: 10px;">
                            <a href="' . esc_attr($vendor->permalink) . '">
                                ' . $vendor_term->name . '
                            </a>
                    </div>
                </div>';
                }
            }
        }
        echo $html;
        die();
    }

    public function generate_variation_attributes() {


        $product_manager_form_data = array();
        parse_str($_POST['product_manager_form'], $product_manager_form_data);

        if (isset($product_manager_form_data['attributes']) && !empty($product_manager_form_data['attributes'])) {
            $pro_attributes = '{';
            $attr_first = true;
            foreach ($product_manager_form_data['attributes'] as $attributes) {
                if (isset($attributes['is_variation'])) {
                    if (!empty($attributes['name']) && !empty($attributes['value'])) {
                        if (!$attr_first)
                            $pro_attributes .= ',';
                        if ($attr_first)
                            $attr_first = false;

                        if ($attributes['is_taxonomy']) {
                            $pro_attributes .= '"' . $attributes['tax_name'] . '": {';
                            if (!is_array($attributes['value'])) {
                                $att_values = explode("|", $attributes['value']);
                                $is_first = true;
                                foreach ($att_values as $att_value) {
                                    if (!$is_first)
                                        $pro_attributes .= ',';
                                    if ($is_first)
                                        $is_first = false;
                                    $pro_attributes .= '"' . sanitize_title($att_value) . '": "' . trim($att_value) . '"';
                                }
                            } else {
                                $att_values = $attributes['value'];
                                $is_first = true;
                                foreach ($att_values as $att_value) {
                                    if (!$is_first)
                                        $pro_attributes .= ',';
                                    if ($is_first)
                                        $is_first = false;
                                    $att_term = get_term(absint($att_value));
                                    if ($att_term) {
                                        $pro_attributes .= '"' . $att_term->slug . '": "' . $att_term->name . '"';
                                    } else {
                                        $pro_attributes .= '"' . sanitize_title($att_value) . '": "' . trim($att_value) . '"';
                                    }
                                }
                            }
                            $pro_attributes .= '}';
                        } else {
                            $pro_attributes .= '"' . $attributes['name'] . '": {';
                            $att_values = explode("|", $attributes['value']);
                            $is_first = true;
                            foreach ($att_values as $att_value) {
                                if (!$is_first)
                                    $pro_attributes .= ',';
                                if ($is_first)
                                    $is_first = false;
                                $pro_attributes .= '"' . trim($att_value) . '": "' . trim($att_value) . '"';
                            }
                            $pro_attributes .= '}';
                        }
                    }
                }
            }
            $pro_attributes .= '}';
            echo $pro_attributes;
        }

        die();
    }

    public function delete_fpm_product() {

        $proid = $_POST['proid'];

        if ($proid) {
            if (wp_delete_post($proid)) {
                //echo 'success';
                echo '{"status": "success", "shop_url": "' . get_permalink(wc_get_page_id('shop')) . '"}';
                die;
            }
            die;
        }
    }

    function fpm_get_image_id($attachment_url) {
        global $wpdb;
        $upload_dir_paths = wp_upload_dir();

        if (class_exists('WPH')) {
            global $wph;
            $new_upload_path = $wph->functions->get_module_item_setting('new_upload_path');
            $attachment_url = str_replace($new_upload_path, 'wp-content/uploads', $attachment_url);
        }

        // If this is the URL of an auto-generated thumbnail, get the URL of the original image
        if (false !== strpos($attachment_url, $upload_dir_paths['baseurl'])) {
            $attachment_url = preg_replace('/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url);

            // Remove the upload path base directory from the attachment URL
            $attachment_url = str_replace($upload_dir_paths['baseurl'] . '/', '', $attachment_url);

            // Finally, run a custom database query to get the attachment ID from the modified attachment URL
            $attachment_id = $wpdb->get_var($wpdb->prepare("SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url));
        }
        return $attachment_id;
    }

    public function wcmb_vendor_product_list() {
        global $WCMb;
        if (is_user_logged_in() && is_user_wcmb_vendor(get_current_user_id())) {
            $vendor = get_current_vendor();
            $enable_ordering = apply_filters('wcmb_vendor_dashboard_product_list_table_orderable_columns', array('name', 'date'));
            $products_table_headers = array(
                'select_product' => '',
                'image' => '',
                'name' => __('Product', 'MB-multivendor'),
                'price' => __('Price', 'MB-multivendor'),
                'stock' => __('Stock', 'MB-multivendor'),
                'categories' => __('Categories', 'MB-multivendor'),
                'date' => __('Date', 'MB-multivendor'),
                'status' => __('Status', 'MB-multivendor'),
                'actions' => __('Actions', 'MB-multivendor'),
            );
            $products_table_headers = apply_filters('wcmb_vendor_dashboard_product_list_table_headers', $products_table_headers);
            // storing columns keys for ordering
            $columns = array();
            foreach ($products_table_headers as $key => $value) {
                $columns[] = $key;
            }

            $requestData = $_REQUEST;
            $filterActionData = array();
            parse_str($requestData['products_filter_action'], $filterActionData);
            do_action('before_wcmb_products_list_query_bind', $filterActionData, $requestData);
            $notices = array();
            // Do bulk handle
            if (isset($requestData['bulk_action']) && $requestData['bulk_action'] != '' && isset($filterActionData['selected_products']) && is_array($filterActionData['selected_products'])) {
                if ($requestData['bulk_action'] === 'trash') {
                    // Trash products
                    foreach ($filterActionData['selected_products'] as $id) {
                        wp_trash_post($id);
                    }
                    $notices[] = array(
                        'message' => ((count($filterActionData['selected_products']) > 1) ? sprintf( __( '%s products', 'MB-multivendor' ), count($filterActionData['selected_products'])) : sprintf( __( '%s product', 'MB-multivendor' ), count($filterActionData['selected_products']))) .' '. __( 'moved to the Trash.', 'MB-multivendor' ),
                        'type' => 'success'
                        );
                } elseif ($requestData['bulk_action'] === 'untrash') {
                    // Untrash products
                    foreach ($filterActionData['selected_products'] as $id) {
                        wp_untrash_post($id);
                    }
                    $notices[] = array(
                        'message' => ((count($filterActionData['selected_products']) > 1) ? sprintf( __( '%s products', 'MB-multivendor' ), count($filterActionData['selected_products'])) : sprintf( __( '%s product', 'MB-multivendor' ), count($filterActionData['selected_products']))) .' '. __( 'restored from the Trash.', 'MB-multivendor' ),
                        'type' => 'success'
                        );
                } elseif ($requestData['bulk_action'] === 'delete') {
                    if(current_user_can('delete_published_products')) {
                        // delete products
                        foreach ($filterActionData['selected_products'] as $id) {
                            wp_delete_post($id);
                        }
                        $notices[] = array(
                            'message' => ((count($filterActionData['selected_products']) > 1) ? sprintf( __( '%s products', 'MB-multivendor' ), count($filterActionData['selected_products'])) : sprintf( __( '%s product', 'MB-multivendor' ), count($filterActionData['selected_products']))) .' '. __( 'deleted from the Trash.', 'MB-multivendor' ),
                            'type' => 'success'
                            );
                    }else{
                        $notices[] = array(
                            'message' => __('Sorry! You do not have this permission.', 'MB-multivendor' ),
                            'type' => 'error'
                            );
                    }
                } else {
                    do_action('wcmb_products_list_do_handle_bulk_actions', $vendor->get_products(), $filterActionData['bulk_actions'], $filterActionData['selected_products'], $filterActionData, $requestData);
                }
            }
            $df_post_status = apply_filters('wcmb_vendor_dashboard_default_product_list_statues', array('publish', 'pending', 'draft'), $requestData, $vendor);
            if (isset($requestData['post_status']) && $requestData['post_status'] != 'all') {
                $df_post_status = $requestData['post_status'];
            }
            $args = array(
                'posts_per_page' => -1,
                'offset' => 0,
                'category' => '',
                'category_name' => '',
                'orderby' => 'date',
                'order' => 'DESC',
                'include' => '',
                'exclude' => '',
                'meta_key' => '',
                'meta_value' => '',
                'post_type' => 'product',
                'post_mime_type' => '',
                'post_parent' => '',
                'author' => get_current_vendor_id(),
                'post_status' => $df_post_status,
                'suppress_filters' => true
            );
            $tax_query = array();
            if (isset($filterActionData['product_cat']) && $filterActionData['product_cat'] != '') {
                $tax_query[] = array('taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $filterActionData['product_cat']);
            }
            if (isset($filterActionData['product_type']) && $filterActionData['product_type'] != '') {
                if ('downloadable' === $filterActionData['product_type']) {
                    $args['meta_value'] = 'yes';
                    $query_vars['meta_key'] = '_downloadable';
                } elseif ('virtual' === $filterActionData['product_types']) {
                    $query_vars['meta_value'] = 'yes';
                    $query_vars['meta_key'] = '_virtual';
                } else {
                    $tax_query[] = array('taxonomy' => 'product_type', 'field' => 'slug', 'terms' => $filterActionData['product_type']);
                }
            }
            if ($tax_query):
                $args['tax_query'] = $tax_query;
            endif;

            $total_products_array = $vendor->get_products(apply_filters('wcmb_products_list_total_products_query_args', $args, $filterActionData, $requestData));
            // filter/ordering data
            if (!empty($requestData['search_keyword'])) {
                $args['s'] = $requestData['search_keyword'];
            }
            if (isset($columns[$requestData['order'][0]['column']]) && in_array($columns[$requestData['order'][0]['column']], $enable_ordering)) {
                $args['orderby'] = $columns[$requestData['order'][0]['column']];
                $args['order'] = $requestData['order'][0]['dir'];
            }
            if (isset($requestData['post_status']) && $requestData['post_status'] != 'all') {
                $args['post_status'] = $requestData['post_status'];
            }
            $args['offset'] = $requestData['start'];
            $args['posts_per_page'] = $requestData['length'];

            $args = apply_filters('wcmb_datatable_product_list_query_args', $args, $filterActionData, $requestData);

            $data = array();
            $products_array = $vendor->get_products($args);
            if (!empty($products_array)) {
                foreach ($products_array as $product_single) {
                    $row = array();
                    $product = wc_get_product($product_single->ID);
                    $edit_product_link = '';
                    if ((current_user_can('edit_published_products') && get_wcmb_vendor_settings('is_edit_delete_published_product', 'capabilities', 'product') == 'Enable') || in_array($product->get_status(), apply_filters('wcmb_enable_edit_product_options_for_statuses', array('draft', 'pending')))) {
                        $edit_product_link = esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_edit_product_endpoint', 'vendor', 'general', 'edit-product'), $product->get_id()));
                    }
                    $edit_product_link = apply_filters('wcmb_vendor_product_list_product_edit_link', $edit_product_link, $product);
                    // Get actions
                    $onclick = "return confirm('" . __('Are you sure want to delete this product?', 'MB-multivendor') . "')";
                    $view_title = __('View', 'MB-multivendor');
                    if (in_array($product->get_status(), array('draft', 'pending'))) {
                        $view_title = __('Preview', 'MB-multivendor');
                    }
                    $actions = array(
                        'id' => sprintf(__('ID: %d', 'MB-multivendor'), $product->get_id()),
                    );
                    // Add GTIN if have
                    if (get_wcmb_vendor_settings('is_gtin_enable', 'general') == 'Enable') {
                        $gtin_terms = wp_get_post_terms( $product->get_id(), $WCMb->taxonomy->wcmb_gtin_taxonomy);
                        $gtin_label = '';
                        if($gtin_terms && isset($gtin_terms[0])){
                            $gtin_label = $gtin_terms[0]->name;
                        }
                        $gtin_code = get_post_meta( $product->get_id(), '_wcmb_gtin_code', true );

                        if( $gtin_code ){
                            $actions['gtin'] = ( $gtin_label ) ? $gtin_label . ': ' . $gtin_code : __( 'GTIN', 'MB-multivendor' ) . ': ' . $gtin_code;
                        }
                    }
                    
                    $actions_col = array(
                        'view' => '<a href="' . esc_url($product->get_permalink()) . '" target="_blank" title="' . $view_title . '"><i class="wcmb-font ico-eye-icon"></i></a>',
                        'edit' => '<a href="' . esc_url($edit_product_link) . '" title="' . __('Edit', 'MB-multivendor') . '"><i class="wcmb-font ico-edit-pencil-icon"></i></a>',
                        'restore' => '<a href="' . esc_url(wp_nonce_url(add_query_arg(array('product_id' => $product->get_id()), wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_products_endpoint', 'vendor', 'general', 'products'))), 'wcmb_untrash_product')) . '" title="' . __('Restore from the Trash', 'MB-multivendor') . '"><i class="wcmb-font ico-reply-icon"></i></a>',
                        'trash' => '<a class="productDelete" href="' . esc_url(wp_nonce_url(add_query_arg(array('product_id' => $product->get_id()), wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_products_endpoint', 'vendor', 'general', 'products'))), 'wcmb_trash_product')) . '" title="' . __('Move to the Trash', 'MB-multivendor') . '"><i class="wcmb-font ico-delete-icon"></i></a>',
                        'delete' => '<a class="productDelete" href="' . esc_url(wp_nonce_url(add_query_arg(array('product_id' => $product->get_id()), wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_products_endpoint', 'vendor', 'general', 'products'))), 'wcmb_delete_product')) . '" onclick="' . $onclick . '" title="' . __('Delete Permanently', 'MB-multivendor') . '"><i class="wcmb-font ico-delete-icon"></i></a>',
                    );
                    if ($product->get_status() == 'trash') {
                        $edit_product_link = '';
                        unset($actions_col['edit']);
                        unset($actions_col['trash']);
                        unset($actions_col['view']);
                    } else {
                        unset($actions_col['restore']);
                        unset($actions_col['delete']);
                    }

                    if (!current_user_can('edit_published_products') && get_wcmb_vendor_settings('is_edit_delete_published_product', 'capabilities', 'product') != 'Enable' && !in_array($product->get_status(), apply_filters('wcmb_enable_edit_product_options_for_statuses', array('draft', 'pending')))) {
                        unset($actions_col['edit']);
                        if ($product->get_status() != 'trash')
                            unset($actions_col['delete']);
                    }

                    $actions = apply_filters('wcmb_vendor_product_list_row_actions', $actions, $product);
                    $actions_col = apply_filters('wcmb_vendor_product_list_row_actions_column', $actions_col, $product);
                    $row_actions = array();
                    foreach ($actions as $action => $link) {
                        $row_actions[] = '<span class="' . esc_attr($action) . '">' . $link . '</span>';
                    }
                    $row_actions_col = array();
                    foreach ($actions_col as $action => $link) {
                        $row_actions_col[] = '<span class="' . esc_attr($action) . '">' . $link . '</span>';
                    }
                    $action_html = '<div class="row-actions">' . implode(' <span class="divider">|</span> ', $row_actions) . '</div>';
                    $actions_col_html = '<div class="col-actions">' . implode(' <span class="divider">|</span> ', $row_actions_col) . '</div>';
                    // is in stock
                    if ($product->is_in_stock()) {
                        $stock_html = '<span class="label label-success instock">' . __('In stock', 'MB-multivendor');
                        if ($product->managing_stock()) {
                            $stock_html .= ' (' . wc_stock_amount($product->get_stock_quantity()) . ')';
                        }
                        $stock_html .= '</span>';
                    } else {
                        $stock_html = '<span class="label label-danger outofstock">' . __('Out of stock', 'MB-multivendor') . '</span>';
                    }
                    // product cat
                    $product_cats = '';
                    $termlist = array();
                    $terms = get_the_terms($product->get_id(), 'product_cat');
                    if (!$terms ) {
                        $product_cats = '<span class="na">&ndash;</span>';
                    } else {
                        $terms = apply_filters( 'wcmb_vendor_product_list_row_product_categories', $terms, $product );
                        foreach ($terms as $term) {
                            $termlist[] = $term->name;
                        }
                    }
                    if ($termlist) {
                        $product_cats = implode(' | ', $termlist );
                    }
                    $date = '&ndash;';
                    if ($product->get_status() == 'publish') {
                        $status = __('Published', 'MB-multivendor');
                        $date = wcmb_date($product->get_date_created('edit'));
                    } elseif ($product->get_status() == 'pending') {
                        $status = __('Pending', 'MB-multivendor');
                    } elseif ($product->get_status() == 'draft') {
                        $status = __('Draft', 'MB-multivendor');
                    } elseif ($product->get_status() == 'private') {
                        $status = __('Private', 'MB-multivendor');
                    } elseif ($product->get_status() == 'trash') {
                        $status = __('Trash', 'MB-multivendor');
                    } else {
                        $status = ucfirst($product->get_status());
                    }
                    $row ['select_product'] = '<input type="checkbox" class="select_' . $product->get_status() . '" name="selected_products[' . $product->get_id() . ']" value="' . $product->get_id() . '" data-title="' . $product->get_title() . '" data-sku="' . $product->get_sku() . '"/>';
                    $row ['image'] = '<td>' . $product->get_image(apply_filters('wcmb_vendor_product_list_image_size', array(40, 40))) . '</td>';
                    $row ['name'] = '<td><a href="' . esc_url($edit_product_link) . '">' . $product->get_title() . '</a>' . $action_html . '</td>';
                    $row ['price'] = '<td>' . $product->get_price_html() . '</td>';
                    $row ['stock'] = '<td>' . $stock_html . '</td>';
                    $row ['categories'] = '<td>' . $product_cats . '</td>';
                    $row ['date'] = '<td>' . $date . '</td>';
                    $row ['status'] = '<td>' . $status . '</td>';
                    $row ['actions'] = '<td>' . $actions_col_html . '</td>';
                    $data[] = apply_filters('wcmb_vendor_dashboard_product_list_table_row_data', $row, $product, $filterActionData, $requestData);
                }
            }

            $json_data = apply_filters('wcmb_datatable_product_list_result_data', array(
                "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
                "recordsTotal" => intval(count($total_products_array)), // total number of records
                "recordsFiltered" => intval(count($total_products_array)), // total number of records after searching, if there is no searching then totalFiltered = totalData
                "data" => $data,   // total data array
                "notices" => $notices   // set messages or motices
                    ), $filterActionData, $requestData);
            wp_send_json($json_data);
            die;
        }
    }

    public function wcmb_vendor_unpaid_order_vendor_withdrawal_list() {
        global $WCMb;
        if (is_user_logged_in() && is_user_wcmb_vendor(get_current_vendor_id())) {
            $vendor = get_wcmb_vendor(get_current_vendor_id());
            $requestData = $_REQUEST;
            $meta_query['meta_query'] = array(
                array(
                    'key' => '_paid_status',
                    'value' => 'unpaid',
                    'compare' => '='
                ),
                array(
                    'key' => '_commission_vendor',
                    'value' => absint($vendor->term_id),
                    'compare' => '='
                )
            );
            $vendor_unpaid_total_orders = $vendor->get_orders(false, false, $meta_query);
//            if (isset($requestData['start']) && isset($requestData['length'])) {
//                $vendor_unpaid_orders = $vendor->get_orders($requestData['length'], $requestData['start'], $meta_query);
//            }
            $data = array();
            $commission_threshold_time = isset($WCMb->vendor_caps->payment_cap['commission_threshold_time']) && !empty($WCMb->vendor_caps->payment_cap['commission_threshold_time']) ? $WCMb->vendor_caps->payment_cap['commission_threshold_time'] : 0;
            if ($vendor_unpaid_total_orders) {
                foreach ($vendor_unpaid_total_orders as $commission_id => $order_id) {
                    $order = wc_get_order($order_id);
                    $vendor_share = get_wcmb_vendor_order_amount(array('vendor_id' => $vendor->id, 'order_id' => $order->get_id()));
                    if (!isset($vendor_share['total'])) {
                        $vendor_share['total'] = 0;
                    }
                    $commission_create_date = get_the_date('U', $commission_id);
                    $current_date = date('U');
                    $diff = intval(($current_date - $commission_create_date) / (3600 * 24));
                    if ($diff < $commission_threshold_time) {
                        continue;
                    }

                    if (is_commission_requested_for_withdrawals($commission_id)) {
                        $disabled_reqested_withdrawals = 'disabled';
                    } else {
                        $disabled_reqested_withdrawals = '';
                    }
                    //skip withdrawal for COD order and vendor end shipping
                    if ($order->get_payment_method() == 'cod' && $vendor->is_shipping_enable())
                        continue;

                    $row = array();
                    $row ['select_withdrawal'] = '<input name="commissions[]" value="' . $commission_id . '" class="select_withdrawal" type="checkbox" ' . $disabled_reqested_withdrawals . '>';
                    $row ['order_id'] = $order->get_id();
                    $row ['commission_amount'] = wc_price($vendor_share['commission_amount']);
                    $row ['shipping_amount'] = wc_price($vendor_share['shipping_amount']);
                    $row ['tax_amount'] = wc_price($vendor_share['tax_amount']);
                    $row ['total'] = wc_price($vendor_share['total']);
                    $data[] = apply_filters('wcmb_vendor_withdrawal_list_row_data', $row, $commission_id);
                }
            }
            $total_array = $data;
            $data = array_slice( $data, $requestData['start'], $requestData['length'] );

            $json_data = array(
                "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
                "recordsTotal" => intval(count($total_array)), // total number of records
                "recordsFiltered" => intval(count($total_array)), // total number of records after searching, if there is no searching then totalFiltered = totalData
                "data" => $data   // total data array
            );
            wp_send_json($json_data);
            die;
        }
    }

    public function wcmb_vendor_coupon_list() {
        if (is_user_logged_in() && is_user_wcmb_vendor(get_current_vendor_id())) {
            $vendor = get_wcmb_vendor(get_current_vendor_id());
            $requestData = $_REQUEST;
            $args = array(
                'posts_per_page' => -1,
                'offset' => 0,
                'category' => '',
                'category_name' => '',
                'orderby' => 'date',
                'order' => 'DESC',
                'include' => '',
                'exclude' => '',
                'meta_key' => '',
                'meta_value' => '',
                'post_type' => 'shop_coupon',
                'post_mime_type' => '',
                'post_parent' => '',
                'author' => get_current_vendor_id(),
                'post_status' => array('publish', 'pending', 'draft', 'trash'),
                'suppress_filters' => true
            );
            $vendor_total_coupons = get_posts($args);
            $args['offset'] = $requestData['start'];
            $args['posts_per_page'] = $requestData['length'];
            $vendor_coupons = get_posts($args);
            $data = array();
            if ($vendor_coupons) {
                foreach ($vendor_coupons as $coupon_single) {
                    $edit_coupon_link = '';
                    if (current_user_can('edit_published_shop_coupons') && get_wcmb_vendor_settings('is_edit_delete_published_coupon', 'capabilities', 'product') == 'Enable') {
                        $edit_coupon_link = esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_add_coupon_endpoint', 'vendor', 'general', 'add-coupon'), $coupon_single->ID));
                    }
                    // Get actions
                    $onclick = "return confirm('" . __('Are you sure want to delete this coupon?', 'MB-multivendor') . "')";
                    $actions = array(
                        'id' => sprintf(__('ID: %d', 'MB-multivendor'), $coupon_single->ID),
                    );
                    $actions_col = array(
                        'edit' => '<a href="' . esc_url($edit_coupon_link) . '" title="' . __('Edit', 'MB-multivendor') . '"><i class="wcmb-font ico-edit-pencil-icon"></i></a>',
                        'restore' => '<a href="' . esc_url(wp_nonce_url(add_query_arg(array('coupon_id' => $coupon_single->ID), wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_coupons_endpoint', 'vendor', 'general', 'coupons'))), 'wcmb_untrash_coupon')) . '" title="' . __('Restore from the Trash', 'MB-multivendor') . '"><i class="wcmb-font ico-reply-icon"></i></a>',
                        'trash' => '<a class="couponDelete" href="' . esc_url(wp_nonce_url(add_query_arg(array('coupon_id' => $coupon_single->ID), wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_coupons_endpoint', 'vendor', 'general', 'coupons'))), 'wcmb_trash_coupon')) . '" title="' . __('Move to the Trash', 'MB-multivendor') . '"><i class="wcmb-font ico-delete-icon"></i></a>',
                        'delete' => '<a class="couponDelete" href="' . esc_url(wp_nonce_url(add_query_arg(array('coupon_id' => $coupon_single->ID), wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_coupons_endpoint', 'vendor', 'general', 'coupons'))), 'wcmb_delete_coupon')) . '" onclick="' . $onclick . '" title="' . __('Delete Permanently', 'MB-multivendor') . '"><i class="wcmb-font ico-delete-icon"></i></a>',
                    );
                    if ($coupon_single->post_status == 'trash') {
                        unset($actions_col['edit']);
                        unset($actions_col['trash']);
                    } else {
                        unset($actions_col['restore']);
                        unset($actions_col['delete']);
                    }
                    if (!current_user_can('edit_published_shop_coupons') || get_wcmb_vendor_settings('is_edit_delete_published_coupon', 'capabilities', 'product') != 'Enable') {
                        unset($actions['edit']);
                        unset($actions['delete']);
                    }
                    $actions = apply_filters('wcmb_vendor_coupon_list_row_actions', $actions, $coupon_single);
                    $actions_col = apply_filters('wcmb_vendor_coupon_list_row_actions_col', $actions_col, $coupon_single);
                    $row_actions = array();
                    foreach ($actions as $action => $link) {
                        $row_actions[] = '<span class="' . esc_attr($action) . '">' . $link . '</span>';
                    }
                    $action_html = '<div class="row-actions">' . implode(' | ', $row_actions) . '</div>';
                    $row_actions_cols = array();
                    foreach ($actions_col as $action => $link) {
                        $row_actions_cols[] = '<span class="' . esc_attr($action) . '">' . $link . '</span>';
                    }
                    $actions_col_html = '<div class="col-actions">' . implode(' | ', $row_actions_cols) . '</div>';
                    $coupon = new WC_Coupon($coupon_single->ID);
                    $usage_count = $coupon->get_usage_count();
                    $usage_limit = $coupon->get_usage_limit();
                    $usage_limit = $usage_limit ? $usage_limit : '&infin;';

                    if ($coupon->get_date_expires()) {
                        $expiry_date = wcmb_date($coupon->get_date_expires());
                    } else {
                        $expiry_date = '&ndash;';
                    }

                    $row = array();
                    $row ['coupons'] = '<a href="' . esc_url($edit_coupon_link) . '">' . get_the_title($coupon_single->ID) . '</a>' . $action_html;
                    $row ['type'] = esc_html(wc_get_coupon_type($coupon->get_discount_type()));
                    $row ['amount'] = $coupon->get_amount();
                    $row ['uses_limit'] = $usage_count . ' / ' . $usage_limit;
                    $row ['expiry_date'] = $expiry_date;
                    $row ['actions'] = $actions_col_html;
                    $data[] = apply_filters('wcmb_vendor_coupon_list_row_data', $row, $coupon);
                }
            }

            $json_data = array(
                "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
                "recordsTotal" => intval(count($vendor_total_coupons)), // total number of records
                "recordsFiltered" => intval(count($vendor_total_coupons)), // total number of records after searching, if there is no searching then totalFiltered = totalData
                "data" => $data   // total data array
            );
            wp_send_json($json_data);
            die;
        }
    }

    public function wcmb_vendor_transactions_list() {
        global $WCMb;
        if (is_user_logged_in() && is_user_wcmb_vendor(get_current_vendor_id())) {
            $vendor = get_wcmb_vendor(get_current_vendor_id());
            $requestData = $_REQUEST;
            $vendor = apply_filters('wcmb_transaction_vendor', $vendor);
            $start_date = isset($requestData['from_date']) ? $requestData['from_date'] : date('Y-m-01');
            $end_date = isset($requestData['to_date']) ? $requestData['to_date'] : date('Y-m-d');
            $transaction_details = $WCMb->transaction->get_transactions($vendor->term_id, $start_date, $end_date, array('wcmb_processing', 'wcmb_completed'));

            $data = array();
            if (!empty($transaction_details)) {
                foreach ($transaction_details as $transaction_id => $detail) {
                    $trans_post = get_post($transaction_id);
                    $order_ids = $commssion_ids = '';
                    $commission_details = get_post_meta($transaction_id, 'commission_detail', true);
                    $transfer_charge = get_post_meta($transaction_id, 'transfer_charge', true);
                    $transaction_amt = get_post_meta($transaction_id, 'amount', true) - get_post_meta($transaction_id, 'transfer_charge', true) - get_post_meta($transaction_id, 'gateway_charge', true);
                    $row = array();
                    $row ['select_transaction'] = '<input name="transaction_ids[]" value="' . $transaction_id . '"  class="select_transaction" type="checkbox" >';
                    $row ['date'] = wcmb_date($trans_post->post_date);
                    $row ['transaction_id'] = '<a href="' . esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_transaction_details_endpoint', 'vendor', 'general', 'transaction-details'), $transaction_id)) . '">#' . $transaction_id . '</a>';
                    $row ['commission_ids'] = '#' . implode(', #', $commission_details);
                    $row ['fees'] = isset($transfer_charge) ? wc_price($transfer_charge) : wc_price(0);
                    $row ['net_earning'] = wc_price($transaction_amt);
                    $data[] = apply_filters('wcmb_vendor_transaction_list_row_data', $row, $transaction_id);
                }
            }
            $json_data = array(
                "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
                "recordsTotal" => intval(count($transaction_details)), // total number of records
                "recordsFiltered" => intval(count($transaction_details)), // total number of records after searching, if there is no searching then totalFiltered = totalData
                "data" => $data   // total data array
            );
            wp_send_json($json_data);
            die;
        }
    }

    /**
     * Customer Questions and Answers data handler
     */
    public function wcmb_customer_ask_qna_handler() {
        global $WCMb, $wpdb;
        $handler = isset($_POST['handler']) ? $_POST['handler'] : '';
        $msg = '';
        $no_data = '';
        $qna_data = '';
        $remain_data = '';
        $redirect = '';

        if ($handler == 'submit') {
            $qna_form_data = array();
            parse_str($_POST['customer_qna_data'], $qna_form_data);
            $wpnonce = isset($qna_form_data['cust_qna_nonce']) ? $qna_form_data['cust_qna_nonce'] : '';
            $product_id = isset($qna_form_data['product_ID']) ? (int) $qna_form_data['product_ID'] : 0;
            $cust_id = isset($qna_form_data['cust_ID']) ? (int) $qna_form_data['cust_ID'] : 0;
            $cust_question = isset($qna_form_data['cust_question']) ? sanitize_text_field($qna_form_data['cust_question']) : '';
            $vendor = get_wcmb_product_vendors($product_id);
            $redirect = get_permalink($product_id);
            $customer = wp_get_current_user();
            $cust_qna = array();
            if ($wpnonce && wp_verify_nonce($wpnonce, 'wcmb_customer_qna_form_submit') && $product_id && $cust_question) {
                $result = $WCMb->product_qna->createQuestion(array(
                    'product_ID' => $product_id,
                    'ques_details' => sanitize_text_field($cust_question),
                    'ques_by' => $cust_id,
                    'ques_created' => date('Y-m-d H:i:s', current_time('timestamp')),
                    'ques_vote' => ''
                ));
                if ($result) {
                    //delete transient
                    if (get_transient('wcmb_customer_qna_for_vendor_' . $vendor->id)) {
                        delete_transient('wcmb_customer_qna_for_vendor_' . $vendor->id);
                    }
                    $no_data = 0;
                    $msg = __("Your question submitted successfully!", 'MB-multivendor');
                    wc_add_notice($msg, 'success');
                    do_action('wcmb_product_qna_after_question_submitted', $product_id, $cust_id, $cust_question);
                }
            }
        } elseif ($handler == 'search') {
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';
            $product_id = isset($_POST['product_ID']) ? $_POST['product_ID'] : 0;
            $product = wc_get_product($product_id);
            if ($product) {
                //$vendor = get_wcmb_product_vendors( $product->get_id() );
                $qnas_data = $WCMb->product_qna->get_Product_QNA($product->get_id(), array('sortby' => 'vote'));
                if ($keyword) {
                    $qnas_data = array_filter($qnas_data, function($data) use ($keyword) {
                        return ( strpos(strtolower($data->ques_details), $keyword) !== false );
                    });
                }
                if ($qnas_data) {
                    foreach ($qnas_data as $qna) {
                        $vendor = get_wcmb_vendor($qna->ans_by);
                        if ($vendor) {
                            $vendor_term = get_term($vendor->term_id);
                            $ans_by = $vendor_term->name;
                        } else {
                            $ans_by = get_userdata($qna->ans_by)->display_name;
                        }
                        $qna_data .= '<div class="qna-item-wrap item-' . $qna->ques_ID . '">
                        <div class="qna-block">
                            <div class="qna-vote">';
                        $count = 0;
                        $ans_vote = maybe_unserialize($qna->ans_vote);
                        if (is_array($ans_vote)) {
                            $count = array_sum($ans_vote);
                        }
                        $qna_data .= '<div class="vote">';
                        if (is_user_logged_in()) {
                            if ($ans_vote && array_key_exists(get_current_user_id(), $ans_vote)) {
                                if ($ans_vote[get_current_user_id()] > 0) {
                                    $qna_data .= '<a href="javascript:void(0)" title="' . __('You already gave a thumbs up.', 'MB-multivendor') . '" class="give-up-vote" data-vote="up" data-ans="' . $qna->ans_ID . '"><i class="vote-sprite vote-sprite-like"></i></a>
                                    <span class="vote-count">' . $count . '</span>
                                    <a href="" title="' . __('Give a thumbs down', 'MB-multivendor') . '" class="give-vote-btn give-down-vote" data-vote="down" data-ans="' . $qna->ans_ID . '"><i class="vote-sprite vote-sprite-dislike"></i></a>';
                                } else {
                                    $qna_data .= '<a href="" title="' . __('Give a thumbs up', 'MB-multivendor') . '" class="give-vote-btn give-up-vote" data-vote="up" data-ans="' . $qna->ans_ID . '"><i class="vote-sprite vote-sprite-like"></i></a>
                                    <span class="vote-count">' . $count . '</span>
                                    <a href="javascript:void(0)" title="' . __('You already gave a thumbs down.', 'MB-multivendor') . '" class="give-vote-btn give-down-vote" data-vote="down" data-ans="' . $qna->ans_ID . '"><i class="vote-sprite vote-sprite-dislike"></i></a>';
                                }
                            } else {
                                $qna_data .= '<a href="" title="' . __('Give a thumbs up', 'MB-multivendor') . '" class="give-vote-btn give-up-vote" data-vote="up" data-ans="' . $qna->ans_ID . '"><i class="vote-sprite vote-sprite-like"></i></a>
                                    <span class="vote-count">' . $count . '</span>
                                    <a href="" title="' . __('Give a thumbs down', 'MB-multivendor') . '" class="give-vote-btn give-down-vote" data-vote="down" data-ans="' . $qna->ans_ID . '"><i class="vote-sprite vote-sprite-dislike"></i></a>';
                            }
                        } else {
                            $qna_data .= '<a href="javascript:void(0)" class="non_loggedin"><i class="vote-sprite vote-sprite-like"></i></a><span class="vote-count">' . $count . '</span><a href="javascript:void(0)" class="non_loggedin"><i class="vote-sprite vote-sprite-dislike"></i></a>';
                        }
                        $qna_data .= '</div></div>'
                                . '<div class="qtn-content">'
                                . '<div class="qtn-row">'
                                . '<p class="qna-question">'
                                . '<span>' . __('Q: ', 'MB-multivendor') . ' </span>' . $qna->ques_details . '</p>'
                                . '</div>'
                                . '<div class="qtn-row">'
                                . '<p class="qna-answer">'
                                . '<span>' . __('A: ', 'MB-multivendor') . ' </span>' . $qna->ans_details . '</p>'
                                . '</div>'
                                . '<div class="bottom-qna">'
                                . '<ul class="qna-info">';

                        $qna_data .= '<li class="qna-user">' . $ans_by . '</li>'
                                . '<li class="qna-date">' . date_i18n(wc_date_format(), strtotime($qna->ans_created)) . '</li>'
                                . '</ul>'
                                . '</div>'
                                . '</div></div></div>';
                    }
                    if (count($qnas_data) > 4) {
                        $qna_data .= '<div class="qna-item-wrap load-more-qna"><a href="" class="load-more-btn button" style="width:100%;text-align:center;">' . __('Load More', 'MB-multivendor') . '</a></div>';
                    }
                }
            }
            if (empty($qna_data)) {
                if (!is_user_logged_in()) {
                    $msg = __("You are not logged in.", 'MB-multivendor');
                }
                $no_data = 1;
            }
        } elseif ($handler == 'answer') {
            $ques_ID = isset($_POST['key']) ? $_POST['key'] : '';
            $reply = isset($_POST['reply']) ? sanitize_textarea_field($_POST['reply']) : '';
            $vendor = get_wcmb_vendor(get_current_user_id());
            if ($vendor && $reply && $ques_ID) {
                $_is_answer_given = $WCMb->product_qna->get_Answers($ques_ID);
                if (isset($_is_answer_given[0]) && count($_is_answer_given[0]) > 0) {
                    $result = $WCMb->product_qna->updateAnswer($_is_answer_given[0]->ans_ID, array('ans_details' => sanitize_textarea_field($reply)));
                } else {
                    $result = $WCMb->product_qna->createAnswer(array(
                        'ques_ID' => $ques_ID,
                        'ans_details' => sanitize_textarea_field($reply),
                        'ans_by' => $vendor->id,
                        'ans_created' => date('Y-m-d H:i:s', current_time('timestamp')),
                        'ans_vote' => ''
                    ));
                }
                if ($result) {
                    //delete transient
                    if (get_transient('wcmb_customer_qna_for_vendor_' . $vendor->id)) {
                        delete_transient('wcmb_customer_qna_for_vendor_' . $vendor->id);
                    }
                    $remain_data = count($WCMb->product_qna->get_Vendor_Questions($vendor));
                    if ($remain_data == 0) {
                        $msg = __('No more customer query found.', 'MB-multivendor');
                    } else {
                        $msg = '';
                    }
                    do_action('wcmb_product_qna_after_answer_submitted', $ques_ID, $vendor, $reply);
                    $qna_data = '';
                    $no_data = 0;
                } else {
                    $no_data = 1;
                }
            }
        } elseif ($handler == 'vote_answer') {
            $ans_ID = isset($_POST['ans_ID']) ? (int) $_POST['ans_ID'] : '';
            $vote_type = isset($_POST['vote']) ? $_POST['vote'] : '';
            $ans_row = $WCMb->product_qna->get_Answer($ans_ID);
            $ques_row = $WCMb->product_qna->get_Question($ans_row->ques_ID);
            $vote = maybe_unserialize($ans_row->ans_vote);
            $redirect = get_permalink($ques_row->product_ID);
            if (!$vote) {
                $vote = array();
            }
            if ($ans_ID && $vote_type && is_user_logged_in()) {
                if ($vote_type == 'up') {
                    $vote[get_current_user_id()] = +1;
                } else {
                    $vote[get_current_user_id()] = -1;
                }
                $result = $WCMb->product_qna->updateAnswer($ans_ID, array('ans_vote' => maybe_serialize($vote)));
                if ($result) {
                    $qna_data = '';
                    $msg = __("Thanks for your vote!", 'MB-multivendor');
                    $no_data = 0;
                    wc_add_notice($msg, 'success');
                    do_action('wcmb_product_qna_after_vote_submitted', $ans_ID, $vote_type);
                } else {
                    $no_data = 1;
                }
            }
        } elseif ($handler == 'update_answer') {
            $result = false;
            $ans_ID = isset($_POST['key']) ? (int) $_POST['key'] : '';
            $answer = isset($_POST['answer']) ? $_POST['answer'] : '';
            if ($ans_ID) {
                $result = $WCMb->product_qna->updateAnswer($ans_ID, array('ans_details' => sanitize_textarea_field($answer)));
            }
            if ($result) {
                $qna_data = '';
                $msg = __("Answer updated successfully!", 'MB-multivendor');
                $no_data = 0;
                wc_add_notice($msg, 'success');
                do_action('wcmb_product_qna_after_update_answer_submitted', $ans_ID, $answer);
            } else {
                $no_data = 1;
            }
        }
        wp_send_json(array('no_data' => $no_data, 'message' => $msg, 'data' => $qna_data, 'remain_data' => $remain_data, 'redirect' => $redirect, 'is_user' => is_user_logged_in()));
        die();
    }

    public function wcmb_vendor_dashboard_reviews_data() {
        $vendor = get_current_vendor();
        $requestData = $_REQUEST;
        $data = array();
        $vendor_reviews_total = array();
        if (get_transient('wcmb_dashboard_reviews_for_vendor_' . $vendor->id)) {
            $vendor_reviews_total = get_transient('wcmb_dashboard_reviews_for_vendor_' . $vendor->id);
        } else {
            $query = array('meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'vendor_rating_id',
                        'value' => $vendor->id,
                        'compare' => '=',
                    ),
                    array(
                        'key' => '_mark_as_replied',
                        'value' => 1,
                        'compare' => 'NOT EXISTS',
                    )
            ));
            $vendor_reviews_total = $vendor->get_reviews_and_rating(0, '', $query);
            set_transient('wcmb_dashboard_reviews_for_vendor_' . $vendor->id, $vendor_reviews_total);
        }
        //$vendor_reviews_total = $vendor->get_reviews_and_rating(0, -1, $query);
        //$vendor_reviews = $vendor->get_reviews_and_rating($requestData['start'], $requestData['length'], $query);
        if ($vendor_reviews_total) {
            $vendor_reviews = array_slice($vendor_reviews_total, $requestData['start'], $requestData['length']);
            foreach ($vendor_reviews as $comment) :
                $vendor = get_wcmb_vendor($comment->user_id);
                if ($vendor) {
                    $vendor_term = get_term($vendor->term_id);
                    $comment_by = $vendor_term->name;
                } else {
                    $comment_by = get_userdata($comment->user_id)->display_name;
                }
                $row = '';
                $row = '<div class="media-left pull-left">   
                        <a href="#">' . get_avatar($comment->user_id, 50, '', '') . '</a>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">' . $comment_by . ' -- <small>' . human_time_diff(strtotime($comment->comment_date)) . __(' ago', 'MB-multivendor') . '</small></h4>
                        <p>' . wp_trim_words($comment->comment_content, 250, '...') . '</p>
                        <a data-toggle="modal" data-target="#commient-modal-' . $comment->comment_ID . '">' . __('Reply', 'MB-multivendor') . '</a>
                        <!-- Modal -->
                        <div class="modal fade" id="commient-modal-' . $comment->comment_ID . '" role="dialog">
                            <div class="modal-dialog">

                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">' . __('Reply to ', 'MB-multivendor') . $comment_by . '</h4>
                                    </div>
                                    <div class="wcmb-widget-modal modal-body">
                                            <textarea class="form-control" rows="5" id="comment-content-' . $comment->comment_ID . '" placeholder="' . __('Enter reply...', 'MB-multivendor') . '"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" data-comment_id="' . $comment->comment_ID . '" data-vendor_id="' . get_current_vendor_id() . '" class="btn btn-default wcmb-comment-reply">' . __('Comment', 'MB-multivendor') . '</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>';

                $data[] = array($row);
            endforeach;
        }
        $json_data = array(
            "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal" => intval(count($vendor_reviews_total)), // total number of records
            "recordsFiltered" => intval(count($vendor_reviews_total)), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data" => $data   // total data array
        );
        wp_send_json($json_data);
        die;
    }

    public function wcmb_vendor_dashboard_customer_questions_data() {
        global $WCMb;
        $vendor = get_current_vendor();
        $requestData = $_REQUEST;
        $data_html = array();
        $active_qna_total = array();
        if (get_transient('wcmb_customer_qna_for_vendor_' . $vendor->id)) {
            $active_qna_total = get_transient('wcmb_customer_qna_for_vendor_' . $vendor->id);
        } else {
            $active_qna_total = $WCMb->product_qna->get_Vendor_Questions($vendor);
            set_transient('wcmb_customer_qna_for_vendor_' . $vendor->id, $active_qna_total);
        }
        if ($active_qna_total) {
            $active_qna = array_slice($active_qna_total, $requestData['start'], $requestData['length']);
            if ($active_qna) {
                foreach ($active_qna as $key => $data) :
                    $product = wc_get_product($data->product_ID);
                    if ($product) {
                        $row = '';
                        $row = '<article id="reply-item-' . $data->ques_ID . '" class="reply-item">
                        <div class="media">
                            <!-- <div class="media-left">' . $product->get_image() . '</div> -->
                            <div class="media-body">
                                <h4 class="media-heading qna-question">' . wp_trim_words($data->ques_details, 160, '...') . '</h4>
                                <time class="qna-date">
                                    <span>' . wcmb_date($data->ques_created) . '</span>
                                </time>
                                <a data-toggle="modal" data-target="#qna-reply-modal-' . $data->ques_ID . '" >' . __('Reply', 'MB-multivendor') . '</a>
                                <!-- Modal -->
                                <div class="modal fade" id="qna-reply-modal-' . $data->ques_ID . '" role="dialog">
                                    <div class="modal-dialog">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title">' . __('Product - ', 'MB-multivendor') . ' ' . $product->get_formatted_name() . '</h4>
                                            </div>
                                            <div class="wcmb-widget-modal modal-body">
                                                    <label class="qna-question">' . stripslashes($data->ques_details) . '</label>
                                                    <textarea class="form-control" rows="5" id="qna-reply-' . $data->ques_ID . '" placeholder="' . __('Post your answer...', 'MB-multivendor') . '"></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" data-key="' . $data->ques_ID . '" class="btn btn-default wcmb-add-qna-reply">' . __('Add', 'MB-multivendor') . '</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>';

                        $data_html[] = array($row);
                    }
                endforeach;
            }
        }

        $json_data = array(
            "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal" => intval(count($active_qna_total)), // total number of records
            "recordsFiltered" => intval(count($active_qna_total)), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data" => $data_html   // total data array
        );
        wp_send_json($json_data);
        die;
    }

    public function wcmb_vendor_products_qna_list() {
        global $WCMb;
        $requestData = $_REQUEST;
        $vendor = get_current_vendor();
        // filter by status
        if (isset($requestData['qna_status']) && $requestData['qna_status'] == 'all' && $requestData['qna_status'] != '') {
            $vendor_questions_n_answers = $WCMb->product_qna->get_Vendor_Questions($vendor, false);
        } else {
            $vendor_questions_n_answers = $WCMb->product_qna->get_Vendor_Questions($vendor, true);
        }
        // filter by products
        if (isset($requestData['qna_products']) && is_array($requestData['qna_products'])) {
            if ($vendor_questions_n_answers) {
                foreach ($vendor_questions_n_answers as $key => $qna_ques) {
                    if (!in_array($qna_ques->product_ID, $requestData['qna_products'])) {
                        unset($vendor_questions_n_answers[$key]);
                    }
                }
            }
        }
        $vendor_qnas = array_slice($vendor_questions_n_answers, $requestData['start'], $requestData['length']);
        $data = array();

        if ($vendor_qnas) {
            // filter by vote
            if ($requestData['order'][0]['dir'] != 'asc') {
                $votes = array();
                foreach ($vendor_qnas as $key => $qna_ques) {
                    $count = 0;
                    $have_answer = $WCMb->product_qna->get_Answers($qna_ques->ques_ID);
                    if (isset($have_answer[0]) && count($have_answer[0]) > 0) {
                        $ans_vote = maybe_unserialize($have_answer[0]->ans_vote);
                        if (is_array($ans_vote)) {
                            $count = array_sum($ans_vote);
                        }
                        $vendor_qnas[$key]->vote_count = $count;
                        $votes[$key] = $count;
                    } else {
                        $vendor_qnas[$key]->vote_count = $count;
                        $votes[$key] = $count;
                    }
                }
                array_multisort($votes, SORT_DESC, $vendor_qnas);
            }

            foreach ($vendor_qnas as $question) {
                $product = wc_get_product($question->product_ID);
                if ($product) {
                    $have_answer = $WCMb->product_qna->get_Answers($question->ques_ID);
                    $details = '';
                    $status = '';
                    $vote = '&ndash;';
                    if (!isset($have_answer[0])) {
                        $status = '<span class="unanswered label label-default">' . __('Unanswered', 'MB-multivendor') . '</span>';
                        $details .= '<div class="wcmb-question-details-modal modal-body">
                                        <textarea class="form-control" rows="5" id="qna-reply-' . $question->ques_ID . '" placeholder="' . __('Post your answer...', 'MB-multivendor') . '"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" data-key="' . $question->ques_ID . '" class="btn btn-default wcmb-add-qna-reply">' . __('Add', 'MB-multivendor') . '</button>
                                    </div>';
                    } else {
                        $status = '<span class="answered label label-success">' . __('Answered', 'MB-multivendor') . '</span>';
                        $ans_vote = maybe_unserialize($have_answer[0]->ans_vote);
                        if (is_array($ans_vote)) {
                            $vote = array_sum($ans_vote);
                            if ($vote > 0) {
                                $vote = '<span class="label label-success">' . $vote . '</span>';
                            } else {
                                $vote = '<span class="label label-danger">' . $vote . '</span>';
                            }
                        }
                        if (apply_filters('wcmb_vendor_can_modify_qna_answer', false)) {
                            $details .= '<div class="wcmb-question-details-modal modal-body">
                                        <textarea class="form-control" rows="5" id="qna-answer-' . $have_answer[0]->ans_ID . '">' . stripslashes($have_answer[0]->ans_details) . '</textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" data-key="' . $have_answer[0]->ans_ID . '" class="btn btn-default wcmb-update-qna-answer">' . __('Edit', 'MB-multivendor') . '</button>
                                    </div>';
                        } else {
                            $details .= '<div class="wcmb-question-details-modal modal-body">
                                        <textarea class="form-control" rows="5" id="qna-answer-' . $have_answer[0]->ans_ID . '" disabled>' . stripslashes($have_answer[0]->ans_details) . '</textarea>
                                    </div>';
                        }
                    }
                    $data[] = array(
                        'qnas' => '<a data-toggle="modal" data-target="#question-details-modal-' . $question->ques_ID . '" data-ques="' . $question->ques_ID . '" class="question-details">' . wp_trim_words(stripslashes($question->ques_details), 160, '...') . '</a>'
                        . '<!-- Modal -->
                                <div class="modal fade" id="question-details-modal-' . $question->ques_ID . '" role="dialog">
                                    <div class="modal-dialog">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title">' . stripslashes($question->ques_details) . '</h4>
                                            </div>
                                            ' . $details . '
                                        </div>
                                    </div>
                                </div>',
                        'product' => $product->get_title(),
                        'date' => wcmb_date($question->ques_created),
                        'vote' => $vote,
                        'status' => $status
                    );
                }
            }
        }
        $json_data = array(
            "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal" => intval(count($vendor_questions_n_answers)), // total number of records
            "recordsFiltered" => intval(count($vendor_questions_n_answers)), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data" => $data   // total data array
        );
        wp_send_json($json_data);
    }

    function wcmb_get_vendor_details() {
        global $WCMb;

        if (!isset($_GET['vendor_id'])) {
            wp_die(__('No Vendor ID found', 'MB-multivendor'));
        }

        if (isset($_GET['vendor_id']) && isset($_GET['vendor_id']) && isset($_GET['nonce'])) {
            $vendor_id = $_GET['vendor_id'];
            $nonce = $_REQUEST["nonce"];

            if (!wp_verify_nonce($nonce, 'wcmb-vendors'))
                wp_die(__('Invalid request', 'MB-multivendor'));

            $vendor = get_wcmb_vendor($vendor_id);
            $product_count = 0;
            $user_info['status'] = '';
            $user_info['status_name'] = '';
            if ($vendor) {
                $vendor_term_id = get_user_meta($vendor_id, '_vendor_term_id', true);

                $vendor_products = $vendor->get_products();

                $vendor_review_info = wcmb_get_vendor_review_info($vendor_term_id);
                if (isset($vendor_review_info['total_rating'])) {
                    $user_info['total_rating'] = $vendor_review_info['total_rating'];
                    $user_info['avg_rating'] = number_format(floatval($vendor_review_info['avg_rating']), 1);
                }

                $vendor_report_data = get_wcmb_vendor_dashboard_stats_reports_data($vendor);
                if (isset($vendor_report_data[30]) && is_array($vendor_report_data[30])) {
                    $user_info['last_30_days_earning'] = $vendor_report_data[30]['_wcmb_stats_table']['current_earning'];
                    $user_info['last_30_days_sales_total'] = $vendor_report_data[30]['_wcmb_stats_table']['current_sales_total'];
                    $user_info['last_30_days_withdrawal'] = $vendor_report_data[30]['_wcmb_stats_table']['current_withdrawal'];
                    $user_info['last_30_days_orders_no'] = $vendor_report_data[30]['_wcmb_stats_table']['current_orders_no'];
                }

                $unpaid_orders = get_wcmb_vendor_order_amount(array('commission_status' => 'unpaid'), $vendor->id);
                if (isset($unpaid_orders['total']) && $unpaid_orders['total'] > 0)
                    $user_info['withdrawable_balance'] = wc_price($unpaid_orders['total']);
                else
                    $user_info['withdrawable_balance'] = wc_price(0);

                $vendor_profile_image = get_user_meta($vendor_id, '_vendor_profile_image', true);
                if (isset($vendor_profile_image) && $vendor_profile_image > 0)
                    $user_info['profile_image'] = wp_get_attachment_url($vendor_profile_image);
                else
                    $user_info['profile_image'] = get_avatar_url($vendor_id, array('size' => 120));

                $user_info['products'] = count($vendor_products);
                $user_info['shop_title'] = $vendor->page_title;
                $user_info['shop_url'] = $vendor->permalink;
                $user_info['address_1'] = $vendor->address_1;
                $user_info['address_2'] = $vendor->address_2;
                $user_info['city'] = $vendor->city;
                $user_info['state'] = $vendor->state;
                $user_info['country'] = $vendor->country;
                $user_info['postcode'] = $vendor->postcode;
                $user_info['phone'] = $vendor->phone;
                $user_info['description'] = $vendor->description;

                $user_info['facebook'] = $vendor->fb_profile;
                $user_info['twitter'] = $vendor->twitter_profile;
                $user_info['google_plus'] = $vendor->google_plus_profile;
                $user_info['linkdin'] = $vendor->linkdin_profile;
                $user_info['youtube'] = $vendor->youtube;
                $user_info['instagram'] = $vendor->instagram;

                $user_info['payment_mode'] = $vendor->payment_mode;
                $user_info['gateway_logo'] = isset($WCMb->payment_gateway->payment_gateways[$vendor->payment_mode]) ? $WCMb->payment_gateway->payment_gateways[$vendor->payment_mode]->gateway_logo() : '';

                $vendor_progress = wcmb_get_vendor_profile_completion($vendor->id);

                if (isset($vendor_progress['progress']))
                    $user_info['profile_progress'] = $vendor_progress['progress'];
            }

            $user = get_user_by("ID", $vendor_id);
            $user_info['ID'] = $user->data->ID;
            $user_info['display_name'] = $user->data->display_name;
            $user_info['email'] = $user->data->user_email;
            $user_info['registered'] = $user->data->user_registered;

            $actions_html = '';

            if (in_array('dc_vendor', $user->roles)) {
                $is_block = get_user_meta($vendor_id, '_vendor_turn_off', true);
                if ($is_block) {
                    $user_info['status_name'] = __('Suspended', 'MB-multivendor');
                    $user_info['status'] = 'suspended';
                    $actions['activate'] = array(
                        'ID' => $user_info['ID'],
                        'ajax_action' => 'wcmb_activate_vendor',
                        'url' => '#',
                        'name' => __('Activate', 'MB-multivendor'),
                        'action' => 'activate',
                    );
                } else {
                    $user_info['status_name'] = __('Approved', 'MB-multivendor');
                    $user_info['status'] = 'approved';
                    $actions['suspend'] = array(
                        'ID' => $user_info['ID'],
                        'ajax_action' => 'wcmb_suspend_vendor',
                        'url' => '#',
                        'name' => __('Suspend', 'MB-multivendor'),
                        'action' => 'suspend',
                    );
                }
            } else if (in_array('dc_rejected_vendor', $user->roles)) {
                $user_info['status_name'] = __('Rejected', 'MB-multivendor');
                $user_info['status'] = 'rejected';
            } else if (in_array('dc_pending_vendor', $user->roles)) {
                $user_info['status_name'] = __('Pending', 'MB-multivendor');
                $user_info['status'] = 'pending';
                $actions['approve'] = array(
                    'ID' => $user_info['ID'],
                    'ajax_action' => 'activate_pending_vendor',
                    'url' => '#',
                    'name' => __('Approve', 'MB-multivendor'),
                    'action' => 'approve',
                );
                $actions['reject'] = array(
                    'ID' => $user_info['ID'],
                    'ajax_action' => 'reject_pending_vendor',
                    'url' => '#',
                    'name' => __('Reject', 'MB-multivendor'),
                    'action' => 'reject',
                );
            }

            if (isset($actions) && is_array($actions)) {
                foreach ($actions as $action) {
                    $actions_html .= sprintf('<a class="button button-primary button-large wcmb-action-button wcmb-action-button-%1$s %1$s-vendor" href="%2$s" aria-label="%3$s" title="%3$s" data-vendor-id="%4$s" data-ajax-action="%5$s">%6$s</a>', esc_attr($action['action']), esc_url($action['url']), esc_attr(isset($action['title']) ? $action['title'] : $action['name']), $action['ID'], $action['ajax_action'], esc_html($action['name']));
                }
                $user_info['actions_html'] = $actions_html;
            }

            if (in_array('dc_pending_vendor', $user->roles) || in_array('dc_rejected_vendor', $user->roles)) {
                // Add Vendor Application data
                $vendor_application_data = get_user_meta($user_info['ID'], 'wcmb_vendor_fields', true);
                $vendor_application_data_string = '';
                if (!empty($vendor_application_data) && is_array($vendor_application_data)) {
                    foreach ($vendor_application_data as $key => $value) {
                        if ($value['type'] == 'recaptcha')
                            continue;
                        $vendor_application_data_string .= '<div class="wcmb-form-field">';
                        $vendor_application_data_string .= '<label>' . html_entity_decode($value['label']) . ':</label>';
                        if ($value['type'] == 'file') {
                            if (!empty($value['value']) && is_array($value['value'])) {
                                foreach ($value['value'] as $attacment_id) {
                                    $vendor_application_data_string .= '<span> <a href="' . wp_get_attachment_url($attacment_id) . '" download>' . get_the_title($attacment_id) . '</a> </span>';
                                }
                            }
                        } else {
                            if (is_array($value['value'])) {
                                $vendor_application_data_string .= '<span> ' . implode(', ', $value['value']) . '</span>';
                            } else {
                                $vendor_application_data_string .= '<span> ' . $value['value'] . '</span>';
                            }
                        }
                        $vendor_application_data_string .= '</div>';
                    }
                }
                $user_info['vendor_application_data'] = $vendor_application_data_string;

                $wcmb_vendor_rejection_notes = unserialize(get_user_meta($user_info['ID'], 'wcmb_vendor_rejection_notes', true));

                $wcmb_vendor_custom_notes_html = '';
                if ($wcmb_vendor_rejection_notes) :
                    foreach ($wcmb_vendor_rejection_notes as $time => $notes) {
                        $author_info = get_userdata($notes['note_by']);
                        $wcmb_vendor_custom_notes_html .= '<div class="note-clm"><p class="note-description">' . $notes['note'] . '</p><p class="note_time note-meta">On ' . date("Y-m-d", $time) . '</p><p class="note_owner note-meta">By ' . $author_info->display_name . '</p></div>';
                    }
                endif;

                $user_info['vendor_custom_notes'] = $wcmb_vendor_custom_notes_html;
            }

            wp_send_json_success($user_info);
        }
        return 0;
    }

    /**
     * Ajax handler for tag add.
     *
     * @since 3.0.6
     */
    function wcmb_product_tag_add() {
        $taxonomy = apply_filters('wcmb_product_tag_add_taxonomy', 'product_tag');
        $tax = get_taxonomy($taxonomy);
        $tag_name = '';
        $message = '';
        $status = false;
        if (!apply_filters('wcmb_vendor_can_add_product_tag', true, get_current_user_id())) {
            $message = __("You don't have permission to add product tags", 'MB-multivendor');
            wp_send_json(array('status' => $status, 'tag_name' => $tag_name, 'message' => $message));
            die;
        }

        $tag = wp_insert_term($_POST['new_tag'], $taxonomy, array());

        if (!$tag || is_wp_error($tag) || (!$tag = get_term($tag['term_id'], $taxonomy))) {
            $message = __('An error has occurred. Please reload the page and try again.', 'MB-multivendor');
            if (is_wp_error($tag) && $tag->get_error_message())
                $message = $tag->get_error_message();
        }else {
            $tag_name = $tag->name;
            $status = true;
        }
        wp_send_json(array('status' => $status, 'tag' => $tag, 'tag_name' => $tag_name, 'message' => $message));
        die;
    }

    function wcmb_widget_vendor_pending_shipping() {
        if (is_user_logged_in() && is_user_wcmb_vendor(get_current_vendor_id())) {

            $vendor = get_wcmb_vendor(get_current_vendor_id());
            $requestData = $_REQUEST;
            $today = @date('Y-m-d 00:00:00', strtotime("+1 days"));
            $days_range = apply_filters( 'wcmb_widget_vendor_pending_shipping_days_range', 7, $requestData, $vendor );
            $last_seven_day_date = date('Y-m-d H:i:s', strtotime("-$days_range days"));

            $args = apply_filters('wcmb_vendor_pending_shipping_args', array(
                'start_date' => $last_seven_day_date,
                'end_date' => $today
            ));
            $pending_shippings = $vendor->get_vendor_orders_reports_of('pending_shipping', $args);
            $pending_shippings_arr = array();
            if ($pending_shippings) {
                foreach ($pending_shippings as $pending_orders_item) {
                    $order = wc_get_order($pending_orders_item->order_id);
                    // hide shipping for local pickup
                    $vendor_shipping_method = get_wcmb_vendor_order_shipping_method($order->get_id(), $vendor->id);
                    if ($vendor_shipping_method && in_array($vendor_shipping_method->get_method_id(), apply_filters('hide_shipping_icon_for_vendor_order_on_methods', array('local_pickup'))))
                        continue;

                    $pending_shippings_arr[] = $pending_orders_item;
                }
            }
            $data = array();
            if ($pending_shippings_arr) {
                foreach ($pending_shippings_arr as $pending_orders_item) {
                    try {
                        $order = wc_get_order($pending_orders_item->order_id);
                        $pending_shipping_products = get_wcmb_vendor_orders(array('vendor_id' => $vendor->id, 'order_id' => $order->get_id(), 'shipping_status' => 0, 'is_trashed' => ''));
                        $pending_shipping_amount = get_wcmb_vendor_order_amount(array('vendor_id' => $vendor->id, 'order_id' => $order->get_id(), 'shipping_status' => 0));
                        $product_sku = array();
                        $product_name = array();
                        //$product_dimention = array();
                        foreach ($pending_shipping_products as $pending_shipping_product) {
                            $product = wc_get_product($pending_shipping_product->product_id);
                            if ($product && $product->needs_shipping()) {
                                $product_sku[] = $product->get_sku() ? $product->get_sku() : '<span class="na">&ndash;</span>';
                                $product_name[] = $product->get_title();
                                if ($pending_shipping_product->variation_id != 0) {
                                    $product = wc_get_product($pending_shipping_product->variation_id);
                                }
                            }
                        }
                        if (empty($product_name))
                            continue;

                        $action_html = '';
                        if ($vendor->is_shipping_enable()) {
                            $is_shipped = (array) get_post_meta($order->get_id(), 'dc_pv_shipped', true);
                            if (!in_array($vendor->id, $is_shipped)) {
                                $action_html .= '<a href="javascript:void(0)" title="' . __('Mark as shipped', 'MB-multivendor') . '" onclick="wcmbMarkeAsShip(this,' . $order->get_id() . ')"><i class="wcmb-font ico-shippingnew-icon action-icon"></i></a> ';
                            } else {
                                $action_html .= '<i title="' . __('Shipped', 'MB-multivendor') . '" class="wcmb-font ico-shipping-icon"></i> ';
                            }
                        }
                        $action_html = apply_filters('wcmb_dashboard_pending_shipping_widget_data_actions', $action_html, $order->get_id());
                        $row = array();
                        $row ['order_id'] = '<a href="' . esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_vendor_orders_endpoint', 'vendor', 'general', 'vendor-orders'), $order->get_id())) . '">#' . $order->get_id() . '</a>';
                        $row ['products_name'] = implode(' , ', $product_name);
                        $row ['order_date'] = wcmb_date($order->get_date_created());
                        $row ['shipping_address'] = $order->get_formatted_shipping_address();
                        $row ['shipping_amount'] = wc_price($pending_shipping_amount['shipping_amount']);
                        $row ['action'] = $action_html;
                        $data[] = apply_filters('wcmb_widget_vendor_pending_shipping_row_data', $row, $pending_orders_item, $order);
                    } catch (Exception $ex) {
                        
                    }
                }
            }
            $json_data = array(
                "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
                "recordsTotal" => intval(count($pending_shippings_arr)), // total number of records
                "recordsFiltered" => intval(count($pending_shippings_arr)), // total number of records after searching, if there is no searching then totalFiltered = totalData
                "data" => $data   // total data array
            );
            wp_send_json($json_data);
            die;
        }
    }

    function wcmb_widget_vendor_product_sales_report() {
        global $wpdb;
        if (is_user_logged_in() && is_user_wcmb_vendor(get_current_vendor_id())) {

            $vendor = get_wcmb_vendor(get_current_vendor_id());
            $requestData = $_REQUEST;
            $today = @date('Y-m-d 00:00:00', strtotime("+1 days"));
            $days_range = apply_filters( 'wcmb_widget_vendor_product_sales_report_days_range', 7, $requestData, $vendor );
            $last_seven_day_date = date('Y-m-d H:i:s', strtotime("-$days_range days"));

            $sale_results = $wpdb->get_results(
                    $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wcmb_vendor_orders WHERE commission_id != 0 AND vendor_id=%d AND `created` BETWEEN %s AND %s", $vendor->id, $last_seven_day_date, $today
                    )
            );
            $sold_product_list = array();
            if ($sale_results) :
                foreach ($sale_results as $key => $value) {
                    if (array_key_exists($value->product_id, $sold_product_list)) {
                        $sold_product_list[$value->product_id]['qty'] += $value->quantity;
                    } else {
                        $sold_product_list[$value->product_id]['qty'] = $value->quantity;
                        $sold_product_list[$value->product_id]['item_id'] = $value->order_item_id;
                        $sold_product_list[$value->product_id]['order_id'] = $value->order_id;
                    }
                }
            endif;
            arsort($sold_product_list);
            $data = array();
            foreach ($sold_product_list as $product_id => $value) {
                $product = wc_get_product($product_id);
                $row = array();
                if ($product) {
                    $row ['product'] = '<a href="' . $product->get_permalink($product_id) . '">' . $product->get_image(array(40, 40)) . ' ' . wp_trim_words($product->get_name(), 60, '...') . '</a>';
                    $row ['revenue'] = wc_price($product->get_price('edit') * $value['qty']);
                    $row ['unique_purchase'] = $value['qty'];
                } else {
                    $row ['product'] = __('This product does not exists', 'MB-multivendor');
                    $row ['revenue'] = '-';
                    $row ['unique_purchase'] = $value['qty'];
                }
                $data[] = apply_filters('wcmb_widget_vendor_product_sales_report_row_data', $row, $product_id, $value);
            }

            $json_data = array(
                "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
                "recordsTotal" => intval(count($sold_product_list)), // total number of records
                "recordsFiltered" => intval(count($sold_product_list)), // total number of records after searching, if there is no searching then totalFiltered = totalData
                "data" => $data   // total data array
            );
            wp_send_json($json_data);
            die;
        }
    }

    public function wcmb_get_shipping_methods_by_zone() {
        global $WCMb;

        $zones = array();
        
        if (isset($_POST['zoneID'])) {
            if( !class_exists( 'WCMB_Shipping_Zone' ) ) {
                $WCMb->load_vendor_shipping();
            }
            $zones = WCMB_Shipping_Zone::get_zone($_POST['zoneID']);
        }
        
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

        if ($want_to_limit_location) {
            $countries = $states = $cities = $postcodes = array();

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

        ob_start();
        $template_data = array(
            'zones'                     => $zones,
            'zone_id'                   => $zone_id,
            'want_to_limit_location'    => $want_to_limit_location,
            'show_limit_location_link'  => $show_limit_location_link,
            'show_state_list'           => $show_state_list,
            'countries'                 => $countries,
            'states'                    => $states,
            'state_key_by_country'      => $state_key_by_country,
            'show_post_code_list'       => $show_post_code_list,
            'postcodes'                 => $postcodes,
            'vendor_shipping_methods'   => $vendor_shipping_methods,
        );
        $WCMb->template->get_template('vendor-dashboard/vendor-shipping/vendor-shipping-zone-settings.php', $template_data);
        $zone_html['html'] = ob_get_clean();
        $zone_html['states'] = json_encode($states);

        wp_send_json_success($zone_html);
    }

    public function wcmb_add_shipping_method() {
        global $WCMb;
        $data = array(
            'zone_id' => $_POST['zoneID'],
            'method_id' => $_POST['method']
        );
        if( !class_exists( 'WCMB_Shipping_Zone' ) ) {
            $WCMb->load_vendor_shipping();
        }
        $result = WCMB_Shipping_Zone::add_shipping_methods($data);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message(), 'wcmb');
        }

        wp_send_json_success(__('Shipping method added successfully', 'MB-multivendor'));
    }

    public function wcmb_update_shipping_method() {
        global $WCMb;
        $args = $_POST['args'];
        $posted_data = isset($_POST['posted_data']) ? $_POST['posted_data'] : array();
        $args['settings'] = apply_filters('wcmb_before_update_shipping_method_settings', array_merge($args['settings'] + $posted_data), $_POST);
        if (empty($args['settings']['title'])) {
            wp_send_json_error(__('Shipping title must be required', 'MB-multivendor'));
        }
        
        if( !class_exists( 'WCMB_Shipping_Zone' ) ) {
            $WCMb->load_vendor_shipping();
        }
        
        $result = WCMB_Shipping_Zone::update_shipping_method($args);
        
        $WCMb->load_class( 'shipping-gateway' );
        WCMb_Shipping_Gateway::load_class( 'shipping-method' );
        $vendor_shipping = new WCMB_Vendor_Shipping_Method();
        $vendor_shipping->set_post_data( $args['settings'] );
        $vendor_shipping->process_admin_options();
        
        // clear shipping transient
        WC_Cache_Helper::get_transient_version( 'shipping', true );
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message(), 'wcmb');
        }

        wp_send_json_success(__('Shipping method updated', 'MB-multivendor'));
    }

    public function wcmb_delete_shipping_method() {
        global $WCMb;
        $data = array(
            'zone_id' => $_POST['zoneID'],
            'instance_id' => $_POST['instance_id']
        );
        
        if( !class_exists( 'WCMB_Shipping_Zone' ) ) {
            $WCMB->load_vendor_shipping();
        }
        
        $result = WCMB_Shipping_Zone::delete_shipping_methods($data);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message(), 'wcmb');
        }

        wp_send_json_success(__('Shipping method deleted', 'MB-multivendor'));
    }

    public function wcmb_toggle_shipping_method() {
        global $WCMb;
        $data = array(
            'instance_id' => $_POST['instance_id'],
            'zone_id' => $_POST['zoneID'],
            'checked' => ( $_POST['checked'] == 'true' ) ? 1 : 0
        );
        if( !class_exists( 'WCMB_Shipping_Zone' ) ) {
            $WCMb->load_vendor_shipping();
        }
        $result = WCMB_Shipping_Zone::toggle_shipping_method($data);
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        $message = $data['checked'] ? __('Shipping method enabled successfully', 'MB-multivendor') : __('Shipping method disabled successfully', 'MB-multivendor');
        wp_send_json_success($message);
    }
    
    public function wcmb_configure_shipping_method(){
        global $WCMb;
        $zone_id = isset($_POST['zoneId']) ? absint($_POST['zoneId']) : 0;
        $method_id = isset($_POST['methodId']) ? $_POST['methodId'] : '';
        if ($zone_id) {
            if( !class_exists( 'WCMB_Shipping_Zone' ) ) {
                $WCMb->load_vendor_shipping();
            }
            $zones = WCMB_Shipping_Zone::get_zone($zone_id);
            $vendor_shipping_methods = $zones['shipping_methods'];
            $config_settings = array();
            $is_method_taxable_array = array(
                'none' => __('None', 'MB-multivendor'),
                'taxable' => __('Taxable', 'MB-multivendor')
            );

            $calculation_type = array(
                'class' => __('Per class: Charge shipping for each shipping class individually', 'MB-multivendor'),
                'order' => __('Per order: Charge shipping for the most expensive shipping class', 'MB-multivendor'),
            );
            $settings_html = '';
            foreach ($vendor_shipping_methods as $vendor_shipping_method) {
                if($vendor_shipping_method['id'] == 'free_shipping'){
                    $settings_html = '<!-- Free shipping -->'
                            . '<div class="shipping_form" id="'.$vendor_shipping_method['id'].'">'
                            . '<div class="form-group">'
                            . '<label for="" class="control-label col-sm-3 col-md-3">'.__( 'Method Title', 'MB-multivendor' ).'</label>'
                            . '<div class="col-md-9 col-sm-9">'
                            . '<input id="method_title_fs" class="form-control" type="text" name="method_title" value="'.$vendor_shipping_method['title'].'" placholder="'.__( 'Enter method title', 'MB-multivendor' ).'">'
                            . '</div></div>'
                            . '<div class="form-group">'
                            . '<label for="" class="control-label col-sm-3 col-md-3">'.__( 'Minimum order amount for free shipping', 'MB-multivendor' ).'</label>'
                            . '<div class="col-md-9 col-sm-9">'
                            . '<input id="minimum_order_amount_fs" class="form-control" type="text" name="minimum_order_amount" value="'.$vendor_shipping_method['settings']['min_amount'].'" placholder="'.__( '0.00', 'MB-multivendor' ).'">'
                            . '</div></div>'
                            . '<input type="hidden" id="method_description_fs" name="method_description" value="'.$vendor_shipping_method['settings']['description'].'" />'
                            . '<!--div class="form-group">'
                            . '<label for="" class="control-label col-sm-3 col-md-3">'.__( 'Description', 'MB-multivendor' ).'</label>'
                            . '<div class="col-md-9 col-sm-9">'
                            . '<textarea id="method_description_fs" class="form-control" name="method_description">'.$vendor_shipping_method['settings']['description'].'</textarea>'
                            . '</div></div--></div>';
                }elseif($vendor_shipping_method['id'] == 'local_pickup'){
                    $settings_html = '<!-- Local Pickup -->'
                            . '<div class="shipping_form " id="'.$vendor_shipping_method['id'].'">'
                            . '<div class="form-group">'
                            . '<label for="" class="control-label col-sm-3 col-md-3">'.__( 'Method Title', 'MB-multivendor' ).'</label>'
                            . '<div class="col-md-9 col-sm-9">'
                            . '<input id="method_title_fs" class="form-control" type="text" name="method_title" value="'.$vendor_shipping_method['title'].'" placholder="'.__( 'Enter method title', 'MB-multivendor' ).'">'
                            . '</div></div>'
                            . '<div class="form-group">'
                            . '<label for="" class="control-label col-sm-3 col-md-3">'.__( 'Cost', 'MB-multivendor' ).'</label>'
                            . '<div class="col-md-9 col-sm-9">'
                            . '<input id="method_cost_lp" class="form-control" type="text" name="method_cost" value="'.$vendor_shipping_method['settings']['cost'].'" placholder="'.__( '0.00', 'MB-multivendor' ).'">'
                            . '</div></div>';
                            if( apply_filters( 'wcmb_show_shipping_zone_tax', true ) ) {
                            $settings_html .= '<div class="form-group">'
                                    . '<label for="" class="control-label col-sm-3 col-md-3">'.__( 'Tax Status', 'MB-multivendor' ).'</label>'
                                    . '<div class="col-md-9 col-sm-9">'
                                    . '<select id="method_tax_status_lp" class="form-control" name="method_tax_status">';
                                foreach( $is_method_taxable_array as $key => $value ) { 
                                    $settings_html .= '<option value="'.$key.'">'.$value.'</option>';
                                 } 
                            $settings_html .= '</select></div></div>';
                            }
                    $settings_html .= '<input type="hidden" id="method_description_lp" name="method_description" value="'.$vendor_shipping_method['settings']['description'].'" />'
                            . '<!--div class="form-group">'
                            . '<label for="" class="control-label col-sm-3 col-md-3">'.__( 'Description', 'MB-multivendor' ).'</label>'
                            . '<div class="col-md-9 col-sm-9">'
                            . '<textarea id="method_description_lp" class="form-control" name="method_description">'.$vendor_shipping_method['settings']['description'].'</textarea>'
                            . '</div></div--></div>';
                }elseif($vendor_shipping_method['id'] == 'flat_rate'){
                    $settings_html = '<!-- Flat rate -->'
                            . '<div class="shipping_form" id="'.$vendor_shipping_method['id'].'">'
                            . '<div class="form-group">'
                            . '<label for="" class="control-label col-sm-3 col-md-3">'.__( 'Method Title', 'MB-multivendor' ).'</label>'
                            . '<div class="col-md-9 col-sm-9">'
                            . '<input id="method_title_fs" class="form-control" type="text" name="method_title" value="'.$vendor_shipping_method['title'].'" placholder="'.__( 'Enter method title', 'MB-multivendor' ).'">'
                            . '</div></div>'
                            . '<div class="form-group">'
                            . '<label for="" class="control-label col-sm-3 col-md-3">'.__( 'Cost', 'MB-multivendor' ).'</label>'
                            . '<div class="col-md-9 col-sm-9">'
                            . '<input id="method_cost_fr" class="form-control" type="text" name="method_cost" value="'.$vendor_shipping_method['settings']['cost'].'" placholder="'.__( '0.00', 'MB-multivendor' ).'">'
                            . '</div></div>';
                            if( apply_filters( 'wcmb_show_shipping_zone_tax', true ) ) { 
                            $settings_html .= '<div class="form-group">'
                                    . '<label for="" class="control-label col-sm-3 col-md-3">'.__( 'Tax Status', 'MB-multivendor' ).'</label>'
                                    . '<div class="col-md-9 col-sm-9">'
                                    . '<select id="method_tax_status_fr" class="form-control" name="method_tax_status">';
                                foreach( $is_method_taxable_array as $key => $value ) { 
                                    $settings_html .= '<option value="'.$key.'">'.$value.'</option>';
                                } 
                            $settings_html .= '</select></div></div>';
                            }
                            $settings_html .= '<input type="hidden" id="method_description_fr" name="method_description" value="'.$vendor_shipping_method['settings']['description'].'" />'
                                    . '<!--div class="form-group">'
                                    . '<label for="" class="control-label col-sm-3 col-md-3">'.__( 'Description', 'MB-multivendor' ).'</label>'
                                    . '<div class="col-md-9 col-sm-9">'
                                    . '<textarea id="method_description_fr" class="form-control" name="method_description">'.$vendor_shipping_method['settings']['description'].'</textarea>'
                                    . '</div--></div>';
                            if (!apply_filters( 'wcmb_hide_vendor_shipping_classes', false )) { 
                            $settings_html .= '<div class="wcmb_shipping_classes"><hr>'
                                    . '<h2>'.__('Shipping Class Cost', 'MB-multivendor').'</h2>'
                                    . '<div class="description mb-15">'.__('These costs can be optionally entered based on the shipping class set per product( This cost will be added with the shipping cost above).', 'MB-multivendor').'</div>';
      
                            $shipping_classes = get_vendor_shipping_classes();

                            if(empty($shipping_classes)) {
                            $settings_html .= '<div class="no_shipping_classes">' . __("No Shipping Classes set by Admin", 'MB-multivendor') . '</div>';
                            } else {
                                foreach ($shipping_classes as $shipping_class ) {
                                    $settings_html .= '<div class="form-group">'
                                            . '<label for="" class="control-label col-sm-3 col-md-3">'.__( 'Cost of Shipping Class:', 'MB-multivendor' ) .' '. $shipping_class->name .'</label>'
                                            . '<div class="col-md-9 col-sm-9">'
                                            . '<input type="hidden" name="shipping_class_id" value="'.$shipping_class->term_id.'" />'
                                            . '<input id="'.$shipping_class->slug.'" class="form-control sc_vals" type="text" name="shipping_class_cost" value="'.$vendor_shipping_method['settings']['class_cost_'.$shipping_class->term_id].'" placholder="'.__( 'N/A', 'MB-multivendor' ).'" data-shipping_class_id="'. $shipping_class->term_id.'">'
                                            . '<div class="description">'.__( 'Enter a cost (excl. tax) or sum, e.g. <code>10.00 * [qty]</code>.', 'MB-multivendor' ) . '<br/><br/>' . __( 'Use <code>[qty]</code> for the number of items, <br/><code>[cost]</code> for the total cost of items, and <code>[fee percent="10" min_fee="20" max_fee=""]</code> for percentage based fees.', 'MB-multivendor' ).'</div>'
                                            . '</div></div>';
                                }
                            $settings_html .= '<div class="form-group">'
                                    . '<label for="" class="control-label col-sm-3 col-md-3">'.__( 'Calculation type', 'MB-multivendor' ).'</label>'
                                    . '<div class="col-md-9 col-sm-9">'
                                    . '<select id="calculation_type" class="form-control" name="calculation_type">';
                                foreach( $calculation_type as $key => $value ) {
                                    $settings_html .= '<option value="'.$key.'">'.$value.'</option>';
                                } 
                            $settings_html .= '</select></div></div>';
                            }
                            $settings_html .= '</div>';
                            } 
                            $settings_html .= '</div>';
                }else{
                    $settings_html = apply_filters( 'wcmb_vendor_backend_shipping_methods_edit_form_fields', $settings_html, get_current_user_id(), $zone_id, $vendor_shipping_method );
                }
                $config_settings[$vendor_shipping_method['id']] = $settings_html;
            }
            $html_settings = isset($config_settings[$method_id]) ? $config_settings[$method_id] : '';
            wp_send_json($html_settings);
        }
    }
    
    
    public function wcmb_product_classify_next_level_list_categories() {
        $term_id = isset($_POST['term_id']) ? (int) $_POST['term_id'] : 0;
        $taxonomy = isset($_POST['taxonomy']) ? $_POST['taxonomy'] : '';
        $cat_level = isset($_POST['cat_level']) ? $_POST['cat_level'] : 0;
        $term = get_term( $term_id , $taxonomy );
        $child_terms = get_term_children( $term_id, $taxonomy );
        $html_level = '';
        $level = $cat_level + 1;
        $final = false;
        $hierarchy = get_ancestors( $term_id, $taxonomy );
        $crumb = array();
        foreach ( array_reverse($hierarchy) as $id ) {
            $h_term = get_term( $id, $taxonomy );
            $crumb[] = $h_term->name;
        }
        $crumb[] = $term->name;
        $html_hierarchy = implode( ' <i class="wcmb-font ico-right-arrow-icon"></i> ', $crumb );
        if($child_terms) {  
            $html_level .= '<ul class="wcmb-product-categories '.$level.'-level" data-cat-level="'.$level.'">';
            $html_level .= wcmb_list_categories( apply_filters( "wcmb_vendor_product_classify_{$level}_level_categories", array(
                            'taxonomy' => 'product_cat', 
                            'hide_empty' => false,
                            'html_list' => true,
                            'parent' => $term_id,
                            'cat_link'  => '#',
                            ) ) );
            $html_level .= '</ul>';
        } else {
            $final = true;
            //$level = 'final';
            $html_level .= '<div class="final-cat-button">'
                        . '<p>'.$term->name.'<p>'
                        . '<button class="classified-pro-cat-btn btn btn-default" data-term-id="'.$term->term_id.'" data-taxonomy="'.$taxonomy.'">'. strtoupper(__( 'Select', 'MB-multivendor' )).'</button>'
                        . '</div>';
        }
        wp_send_json( array( 'html_level' => $html_level, 'level' => $level, 'is_final' => $final, 'hierarchy' => $html_hierarchy ) );
        die;
    }
    
    public function show_product_classify_next_level_from_searched_term(){
        $term_id = isset($_POST['term_id']) ? (int) $_POST['term_id'] : 0;
        $taxonomy = isset($_POST['taxonomy']) ? $_POST['taxonomy'] : '';
        $hierarchy = get_ancestors( $term_id, $taxonomy );
        $html_level = $html_hierarchy = '';
        //print_r($hierarchy);die;
        $level = 1;
        $parent = 0;
        if($hierarchy){
            foreach ( array_reverse($hierarchy) as $id ) {
                $html_level .= '<div class="wcmb-product-cat-level '.$level.'-level-cat cat-column" data-level="'.$level.'">'
                    . '<ul class="wcmb-product-categories '.$level.'-level" data-cat-level="'.$level.'">';
                $html_level .= wcmb_list_categories( apply_filters( 'wcmb_vendor_product_classify_'.$level.'_level_categories', array(
                                        'taxonomy' => 'product_cat', 
                                        'hide_empty' => false, 
                                        'html_list' => true,
                                        'parent' => $parent,
                                        'cat_link'  => '#',
                                        'selected' => $id,
                                        ) ) );
                $html_level .= '</ul></div>';
                $level++;
                $parent = $id;
            }
        }
        $html_level .= '<div class="wcmb-product-cat-level '.$level.'-level-cat cat-column" data-level="'.$level.'">'
            . '<ul class="wcmb-product-categories '.$level.'-level" data-cat-level="'.$level.'">';
        $html_level .= wcmb_list_categories( apply_filters( 'wcmb_vendor_product_classify_1_level_categories', array(
                                'taxonomy' => 'product_cat', 
                                'hide_empty' => false, 
                                'html_list' => true,
                                'parent' => $parent,
                                'cat_link'  => '#',
                                'selected' => $term_id,
                                ) ) );
        $html_level .= '</ul></div>';
        // add final level step
        $level = $level + 1;
        $h_term = get_term( $term_id, $taxonomy );
        $html_level .= '<div class="wcmb-product-cat-level '.$level.'-level-cat cat-column select-cat-button-holder" data-level="'.$level.'">'
                .'<div class="final-cat-button">'
                . '<p>'.$h_term->name.'<p>'
                . '<button class="classified-pro-cat-btn btn btn-default" data-term-id="'.$h_term->term_id.'" data-taxonomy="'.$taxonomy.'">'. strtoupper(__( 'Select', 'MB-multivendor' )).'</button>'
                . '</div></div>';
            
        wp_send_json( array( 'html_level' => $html_level ) );
        die;
    }
    
    public function wcmb_product_classify_search_category_level(){
        global $wcmb, $wpdb;
        $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';
        if(!empty($keyword)){
            $query = apply_filters( "wcmb_product_classify_search_category_level_args", array(
                'taxonomy' => 'product_cat', 
                'search'    => $keyword,
                'hide_empty' => false, 
                'parent' => '',
                'fields'    => 'ids',     
            ) );
            $search_terms = wcmb_list_categories( $query );
            $html_search_result = '';
            if( $search_terms ){
                foreach ( $search_terms as $term_id ) {
                    $term = get_term( $term_id, $query['taxonomy'] );
                    $hierarchy = get_ancestors( $term_id, $query['taxonomy'] );
                    $hierarchy = array_reverse($hierarchy);
                    $hierarchy[] = $term_id;
                    $html_search_result .= '<li class="list-group-item" data-term-id="'.$term->term_id.'" data-taxonomy="'.$query['taxonomy'].'">'
                        . '<p><strong>' . $term->name . '</strong></p>'
                        . '<ul class="breadcrumb">';
                    foreach ($hierarchy as $id) {
                        $h_term = get_term( $id, $query['taxonomy'] );
                        $html_search_result .= '<li>' . $h_term->name . '</li>';
                    }
                    $html_search_result .= '</ul></li>';
                    
                    //$html_search_result .= '<a class="list-group-item"><span class="add-term" data-term-id="'.$term_id.'">&plus;</span>&nbsp;&nbsp;'.implode( ' <i class="wcmb-font ico-right-arrow-icon"></i> ', $term_crumb ).'</a>';
                }
     
            }else{
                $html_search_result .= '<li class="list-group-item"><p>'.__('No results found', 'MB-multivendor').'</p></li>';
            }
            wp_send_json( array('results' => $html_search_result) );
            die;
        }
    }
    
    public function wcmb_list_a_product_by_name_or_gtin() {
        global $WCMb, $wpdb;
        $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';
        $html = '';
        if(!empty($keyword)){
            $ids = array();
            $posts = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_wcmb_gtin_code' AND meta_value LIKE %s;", esc_sql( '%'.$keyword.'%' ) ) );
            if ( ! $posts ) {
                $data_store = WC_Data_Store::load('product');
                $ids = $data_store->search_products($keyword, '', false);
                $include = array();
                foreach ($ids as $id) {
                    $product = wc_get_product($id);
                    $product_map_id = get_post_meta($id, '_wcmb_spmv_map_id', true);
                    if( $product && $product_map_id ){
                        $results = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wcmb_products_map WHERE product_map_id=%d", $product_map_id) );
                        $product_ids = wp_list_pluck($results, 'product_id');
                        $first_inserted_map_pro_key = array_search(min(wp_list_pluck($results, 'ID')), wp_list_pluck($results, 'ID'));
                        if(isset($product_ids[$first_inserted_map_pro_key])){
                            $include[] = $product_ids[$first_inserted_map_pro_key];
                        }
                    }elseif($product) {
                        $include[] = $id;
                    }
                }

                if ($include) {
                    $ids = array_slice(array_intersect($ids, $include), 0, 10);
                } else {
                    $ids = array();
                }
            }else{
                $unique_gtin_arr = array();
                foreach ($posts as $post_id) {
                    $unique_gtin_arr[$post_id] = get_post_meta($post_id, '_wcmb_gtin_code', true);
                }
                $ids = array_keys(array_unique($unique_gtin_arr));
            }
            
            $product_objects = apply_filters( 'wcmb_list_a_products_objects',array_map('wc_get_product', $ids) );
            $user_id = get_current_user_id();
            
            if (count($product_objects) > 0) {
                foreach ($product_objects as $product_object) {
                    if ($product_object) {
                        $gtin_code = get_post_meta($product_object->get_id(), '_wcmb_gtin_code', true);
                        if (is_user_wcmb_vendor($user_id) && $WCMb->vendor_caps->vendor_can($product_object->get_type())) {
                            // product cat
                            $product_cats = '';
                            $termlist = array();
                            //$terms = wp_get_post_terms( $product_object->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
                            $terms = get_the_terms($product_object->get_id(), 'product_cat');
                            if (!$terms) {
                                $product_cats = '<span class="na">&ndash;</span>';
                            } else {
                                $terms_arr = array();
                                $terms = apply_filters( 'wcmb_vendor_product_list_row_product_categories', $terms, $product_object );
                                foreach ($terms as $term) {
                                    //$h_term = get_term_by('term_id', $term_id, 'product_cat');
                                    $terms_arr[] = $term->name;
                                }
                                $product_cats = implode(' | ', $terms_arr);
                            }

                            $html .= '<div class="search-result-clm">'
                            .  $product_object->get_image(apply_filters('wcmb_searched_name_gtin_product_list_image_size', array(98, 98)))
                            . '<div class="result-content">'
                            . '<p><strong>'.rawurldecode($product_object->get_formatted_name()).'</strong></p>'
                            . '<p>'.$product_object->get_price_html().'</p>'
                            . '<p>'.$product_cats.'</p>'
                            . '</div>'
                            . '<a href="javascript:void(0)" data-product_id="'.$product_object->get_id().'" class="wcmb-create-pro-duplicate-btn btn btn-default item-sell">'.__('Sell yours', 'MB-multivendor').'</a>'
                            . '</div>';
                            
                        } else {
                            
                        }
                    }
                }
                
            } else {
                $html .= '<div class="search-result-clm"><div class="result-content">' . __('No Suggestions found', 'MB-multivendor') . "</div></div>";
            }
        }else{
            $html .= '<div class="search-result-clm"><div class="result-content">' . __('Empty search field! Enter a text to search.', 'MB-multivendor') . "</div></div>";
        }
        wp_send_json( array( 'results' => $html ) );
        die;
    }
    
    public function wcmb_set_classified_product_terms(){
        $term_id = isset($_POST['term_id']) ? (int) $_POST['term_id'] : 0;
        $taxonomy = isset($_POST['taxonomy']) ? $_POST['taxonomy'] : '';
        $user_id = get_current_user_id();
        $url = '';
        if(is_user_wcmb_vendor($user_id)){
            $data = array(
                'term_id'   => $term_id,
                'taxonomy'  => $taxonomy,
            );
            set_transient( 'classified_product_terms_vendor'.$user_id , $data, HOUR_IN_SECONDS );
            $url = esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_edit_product_endpoint', 'vendor', 'general', 'edit-product')));
        }
        wp_send_json( array( 'url' => $url ) );
        die;
    }
    
    /**
     * Add an attribute row.
     */
    public function edit_product_attribute_callback() {
        global $WCMb;
        ob_start();

        check_ajax_referer( 'add-attribute', 'security' );

        if ( ! current_user_can( 'edit_products' ) || ( ! apply_filters( 'wcmb_vendor_can_add_custom_attribute', true ) && empty( sanitize_text_field( $_POST['taxonomy'] ) ) ) ) {
            wp_die( -1 );
        }

        $i = absint( $_POST['i'] );
        $metabox_class = array();
        $attribute = new WC_Product_Attribute();

        $attribute->set_id( wc_attribute_taxonomy_id_by_name( sanitize_text_field( $_POST['taxonomy'] ) ) );
        $attribute->set_name( sanitize_text_field( $_POST['taxonomy'] ) );
        $attribute->set_visible( apply_filters( 'woocommerce_attribute_default_visibility', 1 ) );
        $attribute->set_variation( apply_filters( 'woocommerce_attribute_default_is_variation', 0 ) );

        if ( $attribute->is_taxonomy() ) {
            $metabox_class[] = 'taxonomy';
            $metabox_class[] = $attribute->get_name();
        }

        include( $WCMb->plugin_path . 'templates/vendor-dashboard/product-manager/views/html-product-attribute.php' );
        wp_die();
    }
    
    /**
     * Save attributes
     */
    public function save_product_attributes_callback() {
        check_ajax_referer( 'save-attributes', 'security' );

        if ( ! current_user_can( 'edit_products' ) ) {
            wp_die( -1 );
        }

        parse_str( $_POST['data'], $data );

        $attr_data = isset( $data['wc_attributes'] ) ? $data['wc_attributes'] : array();

        $attributes = wcmb_woo()->prepare_attributes( $attr_data );
        $product_id = absint( $_POST['post_id'] );
        $product_type = ! empty( $_POST['product_type'] ) ? wc_clean( $_POST['product_type'] ) : 'simple';
        $classname = WC_Product_Factory::get_product_classname( $product_id, $product_type );
        $product = new $classname( $product_id );

        $product->set_attributes( $attributes );
        $product->save();
        wp_die();
    }

}

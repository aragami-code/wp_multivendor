<?php
if (!defined('ABSPATH'))
    exit;

/**
 
 */
class WCMb_Vendor {

    public $id;
    public $taxonomy;
    public $term;
    public $user_data;
    public $shipping_class_id;

    /**
     * Get the vendor if UserID is passed, otherwise the vendor is new and empty.
     *
     * @access public
     * @param string $id (default: '')
     * @return void
     */
    public function __construct($id = '') {

        $this->taxonomy = 'dc_vendor_shop';

        $this->term = false;

        if ($id > 0) {
            $this->get_vendor($id);
        }
    }

    public function get_reviews_and_rating($offset = 0, $posts_per_page = 0, $args = array()) {
        global $WCMb, $wpdb;
        $vendor_id = $this->id;
        $posts_per_page = $posts_per_page ? $posts_per_page : get_option('posts_per_page');
        if (empty($vendor_id) || $vendor_id == '' || $vendor_id == 0) {
            return 0;
        } else {
            $args_default = array(
                'status' => 'approve',
                'type' => 'wcmb_vendor_rating',
                'count' => false,
                'number' => $posts_per_page,
                'offset' => $offset,
                'meta_key' => 'vendor_rating_id',
                'meta_value' => $vendor_id,
                'author__not_in' => array($this->id)
            );
            $args_default = wp_parse_args($args, $args_default);
            $args = apply_filters('wcmb_vendor_review_rating_args_to_fetch', $args_default, $this);
            return get_comments($args);
        }
    }

    public function get_review_count() {
        global $WCMb, $wpdb;
        $vendor_id = $this->id;
        if (empty($vendor_id) || $vendor_id == '' || $vendor_id == 0) {
            return 0;
        } else {
            $args_default = array(
                'status' => 'approve',
                'type' => 'wcmb_vendor_rating',
                'count' => true,
                'meta_key' => 'vendor_rating_id',
                'meta_value' => $vendor_id,
                'author__not_in' => array($this->id)
            );
            $args = apply_filters('wcmb_vendor_review_rating_args_to_fetch', $args_default, $this);
            return get_comments($args);
        }
    }

    /**
     * Gets an Vendor User from the database.
     *
     * @access public
     * @param int $id (default: 0)
     * @return bool
     */
    public function get_vendor($id = 0) {
        if (!$id) {
            return false;
        }

        if (!is_user_wcmb_vendor($id)) {
            return false;
        }

        if ($result = get_userdata($id)) {
            $this->populate($result);
            return true;
        }
        return false;
    }

    /**
     * Populates an Vendor from the loaded user data.
     *
     * @access public
     * @param mixed $result
     * @return void
     */
    public function populate($result) {

        $this->id = $result->ID;
        $this->user_data = $result;
    }

    /**
     * __isset function.
     *
     * @access public
     * @param mixed $key
     * @return bool
     */
    public function __isset($key) {
        global $WCMb;

        if (!$this->id) {
            return false;
        }

        if (in_array($key, array('term_id', 'page_title', 'page_slug', 'link'))) {
            if ($term_id = get_user_meta($this->id, '_vendor_term_id', true)) {
                return term_exists(absint($term_id), $WCMb->taxonomy->taxonomy_name);
            } else {
                return false;
            }
        }

        return metadata_exists('user', $this->id, '_' . $key);
    }

    /**
     * __get function.
     *
     * @access public
     * @param mixed $key
     * @return mixed
     */
    public function __get($key) {
        if (!$this->id) {
            return false;
        }

        if ($key == 'page_title') {

            $value = $this->get_page_title();
        } elseif ($key == 'page_slug') {

            $value = $this->get_page_slug();
        } elseif ($key == 'permalink') {

            $value = $this->get_permalink();
        } else {
            // Get values or default if not set
            $value = get_user_meta($this->id, '_vendor_' . $key, true);
        }

        return $value;
    }

    /**
     * generate_term function
     * @access public
     * @return void
     */
    public function generate_term() {
        global $WCMb;
        if (!$this->term_id) {
            $term = wp_insert_term($this->user_data->user_login, $WCMb->taxonomy->taxonomy_name);
            if (!is_wp_error($term)) {
                update_user_meta($this->id, '_vendor_term_id', $term['term_id']);
                // insert page_title meta @ initial term generate
                update_user_meta($this->id, '_vendor_page_title', $this->user_data->user_login);
                update_woocommerce_term_meta($term['term_id'], '_vendor_user_id', $this->id);
                $this->term_id = $term['term_id'];
            } else if ($term->get_error_code() == 'term_exists') {
                update_user_meta($this->id, '_vendor_term_id', $term->get_error_data());
                update_woocommerce_term_meta($term->get_error_data(), '_vendor_user_id', $this->id);
                $this->term_id = $term->get_error_data();
            }
        }
    }

    public function generate_shipping_class() {
        if (!$this->shipping_class_id && apply_filters('wcmb_add_vendor_shipping_class', true)) {
            $shipping_term = wp_insert_term($this->user_data->user_login . '-' . $this->id, 'product_shipping_class');
            if (!is_wp_error($shipping_term)) {
                update_user_meta($this->id, 'shipping_class_id', $shipping_term['term_id']);
                add_woocommerce_term_meta($shipping_term['term_id'], 'vendor_id', $this->id);
                add_woocommerce_term_meta($shipping_term['term_id'], 'vendor_shipping_origin', get_option('woocommerce_default_country'));
            } else if ($shipping_term->get_error_code() == 'term_exists') {
                update_user_meta($this->id, 'shipping_class_id', $shipping_term->get_error_data());
                add_woocommerce_term_meta($shipping_term->get_error_data(), 'vendor_id', $this->id);
                add_woocommerce_term_meta($shipping_term->get_error_data(), 'vendor_shipping_origin', get_option('woocommerce_default_country'));
            }
        }
    }

    /**
     * update_page_title function
     * @access public
     * @param $title
     * @return boolean
     */
    public function update_page_title($title = '') {
        global $WCMb;
        $this->term_id = get_user_meta($this->id, '_vendor_term_id', true);
        if (!$this->term_id) {
            $this->generate_term();
        }
        if (!empty($title) && isset($this->term_id)) {
            if (!is_wp_error(wp_update_term($this->term_id, $WCMb->taxonomy->taxonomy_name, array('name' => $title)))) {
                return true;
            }
        }
        return false;
    }

    /**
     * update_page_slug function
     * @access public
     * @param $slug
     * @return boolean
     */
    public function update_page_slug($slug = '') {
        global $WCMb;
        $this->term_id = get_user_meta($this->id, '_vendor_term_id', true);
        if (!$this->term_id) {
            $this->generate_term();
        }
        if (!empty($slug) && isset($this->term_id)) {
            if (!is_wp_error(wp_update_term($this->term_id, $WCMb->taxonomy->taxonomy_name, array('slug' => $slug)))) {
                return true;
            }
        }
        return false;
    }

    /**
     * set_term_data function
     * @access public
     * @return void
     */
    public function set_term_data() {
        global $WCMb;
        //return if term is already set
        if ($this->term)
            return;

        if (isset($this->term_id)) {
            $term = get_term($this->term_id, $WCMb->taxonomy->taxonomy_name);
            if (!is_wp_error($term)) {
                $this->term = $term;
            }
        }
    }

    /**
     * get_page_title function
     * @access public
     * @return string
     */
    public function get_page_title() {
        $this->set_term_data();
        if ($this->term) {
            return $this->term->name;
        } else {
            return '';
        }
    }

    /**
     * get_page_slug function
     * @access public
     * @return string
     */
    public function get_page_slug() {
        $this->set_term_data();
        if ($this->term) {
            return $this->term->slug;
        } else {
            return '';
        }
    }

    /**
     * get_permalink function
     * @access public
     * @return string
     */
    public function get_permalink() {
        global $WCMb;

        $link = '';
        if (isset($this->term_id)) {
            $link = get_term_link(absint($this->term_id), $WCMb->taxonomy->taxonomy_name);
        }

        return $link;
    }

    /**
     * Get all products belonging to vendor
     * @param  $args (default=array())
     * @return arr Array of product post objects
     */
    public function get_products($args = array()) {
        global $WCMb;
        $default = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'author' => $this->id,
            'tax_query' => array(
                array(
                    'taxonomy' => $WCMb->taxonomy->taxonomy_name,
                    'field' => 'term_id',
                    'terms' => absint($this->term_id)
                )
            )
        );

        $args = wp_parse_args($args, $default);
        return get_posts($args);
    }

    /**
     * get_orders function
     * @access public
     * @return array with order id
     */
    public function get_orders($no_of = false, $offset = false, $more_args = false) {
        if (!$no_of) {
            $no_of = -1;
        }
        $vendor_id = $this->term_id;
        $commissions = false;
        $order_id = array();
        if ($vendor_id > 0) {
            $args = array(
                'post_type' => 'dc_commission',
                'post_status' => array('publish', 'private'),
                'posts_per_page' => (int) $no_of,
                'meta_query' => array(
                    array(
                        'key' => '_commission_vendor',
                        'value' => absint($vendor_id),
                        'compare' => '='
                    )
                )
            );
            if ($offset) {
                $args['offset'] = $offset;
            }
            if ($more_args) {
                $args = wp_parse_args($more_args, $args);
            }
            $commissions = get_posts($args);
        }

        if ($commissions) {
            $order_id = array();
            foreach ($commissions as $commission) {
                $order_id[$commission->ID] = get_post_meta($commission->ID, '_commission_order_id', true);
            }
        }
        return $order_id;
    }

    /**
     * get_vendor_items_from_order function get items of a order belongs to a vendor
     * @access public
     * @param order_id , vendor term id 
     * @return array with order item detail
     */
    public function get_vendor_items_from_order($order_id, $term_id) {
        $item_dtl = array();
        $order = new WC_Order($order_id);
        if ($order) {
            $items = $order->get_items('line_item');
            if ($items) {
                foreach ($items as $item_id => $item) {
                    $product_id = wc_get_order_item_meta($item_id, '_product_id', true);

                    if ($product_id) {
                        if ($term_id > 0) {
                            $product_vendors = get_wcmb_product_vendors($product_id);
                            if (!empty($product_vendors) && $product_vendors->term_id == $term_id) {
                                $item_dtl[$item_id] = $item;
                            }
                        }
                    }
                }
            }
        }
        return $item_dtl;
    }

    /**
     * get_vendor_items_from_order function get items of a order belongs to a vendor
     * @access public
     * @param order_id , vendor term id 
     * @return array with order item detail
     */
    public function get_vendor_shipping_from_order($order_id, $term_id) {
        $order = new WC_Order($order_id);
        if ($order) {
            $items = $order->get_items('shipping');
        }
        return $items;
    }

    /**
     * get_vendor_orders_by_product function to get orders belongs to a vendor and a product
     * @access public
     * @param product id , vendor term id 
     * @return array with order id
     */
    public function get_vendor_orders_by_product($vendor_term_id, $product_id) {
        $order_dtl = array();
        if ($product_id && $vendor_term_id) {
            $commissions = false;
            $args = array(
                'post_type' => 'dc_commission',
                'post_status' => array('publish', 'private'),
                'posts_per_page' => -1,
                'order' => 'asc',
                'meta_query' => array(
                    array(
                        'key' => '_commission_vendor',
                        'value' => absint($vendor_term_id),
                        'compare' => '='
                    ),
                    array(
                        'key' => '_commission_product',
                        'value' => absint($product_id),
                        'compare' => 'LIKE'
                    ),
                ),
            );
            $commissions = get_posts($args);
            if (!empty($commissions)) {
                foreach ($commissions as $commission) {
                    $order_dtl[] = get_post_meta($commission->ID, '_commission_order_id', true);
                }
            }
        }
        return $order_dtl;
    }

    /**
     * get_vendor_commissions_by_product function to get orders belongs to a vendor and a product
     * @access public
     * @param product id , vendor term id 
     * @return array with order id
     */
    public function get_vendor_commissions_by_product($order_id, $product_id) {
        $order_dtl = array();
        if ($product_id && $order_id) {
            $commissions = false;
            $args = array(
                'post_type' => 'dc_commission',
                'post_status' => array('publish', 'private'),
                'posts_per_page' => -1,
                'order' => 'asc',
                'meta_query' => array(
                    array(
                        'key' => '_commission_order_id',
                        'value' => absint($order_id),
                        'compare' => '='
                    ),
                    array(
                        'key' => '_commission_vendor',
                        'value' => absint($this->term_id),
                        'compare' => '='
                    ),
                ),
            );
            $commissions = get_posts($args);

            if (!empty($commissions)) {
                foreach ($commissions as $commission) {
                    $order_dtl[] = $commission->ID;
                }
            }
        }
        return $order_dtl;
    }

    /**
     * vendor_order_item_table function to get the html of item table of a vendor.
     * @access public
     * @param order id , vendor term id 
     */
    public function vendor_order_item_table($order, $vendor_id, $is_ship = false) {
        global $WCMb;
        $vendor_items = $this->get_vendor_items_from_order($order->get_id(), $vendor_id);
        foreach ($vendor_items as $item_id => $item) {
            $_product = apply_filters('wcmb_woocommerce_order_item_product', $order->get_product_from_item($item), $item);
            ?>
            <tr class="">
                <?php do_action('wcmb_before_vendor_order_item_table', $item, $order, $vendor_id, $is_ship); ?>
                <td scope="col" style="text-align:left; border: 1px solid #eee;" class="product-name">
                    <?php
                    if ($_product && !$_product->is_visible()) {
                        echo apply_filters('wcmb_woocommerce_order_item_name', $item['name'], $item);
                    } else {
                        echo apply_filters('woocommerce_order_item_name', sprintf('<a href="%s">%s</a>', get_permalink($item['product_id']), $item['name']), $item);
                    }
                    wc_display_item_meta($item);
                    ?>
                </td>
                <td scope="col" style="text-align:left; border: 1px solid #eee;">	
                    <?php
                    echo $item['qty'];
                    ?>
                </td>
                <td scope="col" style="text-align:left; border: 1px solid #eee;">
                    <?php
                    $variation_id = 0;
                    if (isset($item['variation_id']) && !empty($item['variation_id'])) {
                        $variation_id = $item['variation_id'];
                    }
                    $product_id = $item['product_id'];
                    $commission_amount = get_wcmb_vendor_order_amount(array('order_id' => $order->get_id(), 'product_id' => $product_id, 'variation_id' => $variation_id, 'order_item_id' => $item_id));
                    if ($is_ship) {
                        echo $order->get_formatted_line_subtotal($item);
                    } else {
                        echo wc_price($commission_amount['commission_amount']);
                    }
                    ?>
                </td>
                <?php do_action('wcmb_after_vendor_order_item_table', $item, $order, $vendor_id, $is_ship); ?>
            </tr>
            <?php
        }
    }

    /**
     * plain_vendor_order_item_table function to get the plain html of item table of a vendor.
     * @access public
     * @param order id , vendor term id 
     */
    public function plain_vendor_order_item_table($order, $vendor_id, $is_ship = false) {
        global $WCMb;
        $vendor_items = $this->get_vendor_items_from_order($order->get_id(), $vendor_id);
        foreach ($vendor_items as $item_id => $item) {
            $_product = apply_filters('woocommerce_order_item_product', $order->get_product_from_item($item), $item);

            // Title
            echo apply_filters('woocommerce_order_item_name', $item['name'], $item);


            // Variation
            wc_display_item_meta($item);

            // Quantity
            echo "\n" . sprintf(__('Quantity: %s', 'MB-multivendor'), $item['qty']);
            $variation_id = 0;
            if (isset($item['variation_id']) && !empty($item['variation_id'])) {
                $variation_id = $item['variation_id'];
            }
            $product_id = $item['product_id'];
            $commission_amount = get_wcmb_vendor_order_amount(array('order_id' => $order->get_id(), 'product_id' => $product_id, 'variation_id' => $variation_id, 'order_item_id' => $item_id));
            if ($is_ship)
                echo "\n" . sprintf(__('Total: %s', 'MB-multivendor'), $order->get_formatted_line_subtotal($item));
            else
                echo "\n" . sprintf(__('Commission: %s', 'MB-multivendor'), wc_price($commission_amount['commission_amount']));

            echo "\n\n";
        }
    }

    /**
     * wcmb_get_vendor_part_from_order function to get vendor due from an order.
     * @access public
     * @param order , vendor term id 
     */
    public function wcmb_get_vendor_part_from_order($order, $vendor_term_id) {
        global $WCMb;
        $order_id = $order->get_id();
        $vendor = get_wcmb_vendor_by_term($vendor_term_id);
        $vendor_part = get_wcmb_vendor_order_amount(array('order_id' => $order_id, 'vendor_id' => $vendor->id));
        $vendor_due = array(
            'commission' => $vendor_part['commission_amount'],
            'shipping' => $vendor_part['shipping_amount'],
            'tax' => $vendor_part['tax_amount'],
            'shipping_tax' => $vendor_part['shipping_tax_amount']
        );
        return apply_filters('vendor_due_per_order', $vendor_due, $order, $vendor_term_id);
    }

    /**
     * wcmb_vendor_get_total_amount_due function to get vendor due from an order.
     * @access public
     * @param order , vendor term id 
     */
    public function wcmb_vendor_get_total_amount_due() {
        global $WCMb;
        $vendor = get_wcmb_vendor_by_term($this->term_id);
        $vendor_orders = get_wcmb_vendor_order_amount(array('vendor_id' => $vendor->id, 'commission_status' => 'unpaid'));
        return (float) ($vendor_orders['commission_amount'] + $vendor_orders['shipping_amount'] + $vendor_orders['tax_amount'] + $vendor_orders['shipping_tax_amount']);
    }

    /**
     * wcmb_get_vendor_part_from_order function to get vendor due from an order.
     * @access public
     * @param order , vendor term id 
     */
    public function wcmb_vendor_transaction() {
        global $WCMb;
        $transactions = $paid_array = array();
        $vendor = get_wcmb_vendor_by_term($this->term_id);
        if ($this->term_id > 0) {
            $args = array(
                'post_type' => 'wcmb_transaction',
                'post_status' => array('publish', 'private'),
                'posts_per_page' => -1,
                'post_author' => $vendor->id
            );
            $transactions = get_posts($args);
        }

        if (!empty($transactions)) {
            foreach ($transactions as $transaction) {
                $paid_array[] = $transaction->ID;
            }
        }
        return $paid_array;
    }

    /**
     * wcmb_vendor_get_order_item_totals function to get order item table of a vendor.
     * @access public
     * @param order id , vendor term id 
     */
    public function wcmb_vendor_get_order_item_totals($order, $term_id) {
        global $WCMb;
        $vendor = get_wcmb_vendor_by_term($term_id);
        $vendor_totals = get_wcmb_vendor_order_amount(array('vendor_id' => $vendor->id, 'order_id' => $order));
        $vendor_shipping_method = get_wcmb_vendor_order_shipping_method($order->get_id(), $vendor->id);
        $order_item_totals = array();
        $order_item_totals['commission_subtotal'] = array(
            'label' => __('Commission Subtotal:', 'MB-multivendor'),
            'value' => wc_price($vendor_totals['commission_amount'])
        );
        $order_item_totals['tax_subtotal'] = array(
            'label' => __('Tax Subtotal:', 'MB-multivendor'),
            'value' => wc_price($vendor_totals['tax_amount'] + $vendor_totals['shipping_tax_amount'])
        );
        if ($vendor_shipping_method) {
            $order_item_totals['shipping_method'] = array(
                'label' => __('Shipping Method:', 'MB-multivendor'),
                'value' => $vendor_shipping_method->get_name()
            );
        }
        $order_item_totals['shipping_subtotal'] = array(
            'label' => __('Shipping Subtotal:', 'MB-multivendor'),
            'value' => wc_price($vendor_totals['shipping_amount'])
        );
        $order_item_totals['total'] = array(
            'label' => __('Total:', 'MB-multivendor'),
            'value' => wc_price($vendor_totals['commission_amount'] + $vendor_totals['tax_amount'] + $vendor_totals['shipping_tax_amount'] + $vendor_totals['shipping_amount'])
        );
        return $order_item_totals;
    }

    /**
     * @deprecated since version 2.6.6
     * @param object | id $order
     * @param object | id $product
     * @return array
     */
    public function get_vendor_total_tax_and_shipping($order, $product = false) {
        _deprecated_function('get_vendor_total_tax_and_shipping', '2.6.6', 'get_wcmb_vendor_order_amount');
        return get_wcmb_vendor_order_amount(array('vendor_id' => $this->id, 'order_id' => $order, 'product_id' => $product));
    }

    public function is_shipping_enable() {
        global $WCMb;
        $is_enable = false;
        // omitted from if condition -- $wcmb->vendor_caps->vendor_payment_settings('give_shipping') && !get_user_meta($this->id, '_vendor_give_shipping', true) and replace with get_wcmb_vendor_settings( 'is_vendor_shipping_on', 'general' )
        if ('Enable' === get_wcmb_vendor_settings( 'is_vendor_shipping_on', 'general' ) && wc_shipping_enabled()) {
            $is_enable = true;
        }
        return apply_filters('is_wcmb_vendor_shipping_enable', $is_enable, $this->id);
    }
    
    public function is_transfer_shipping_enable() {
        global $WCMb;
        $is_enable = false;
        
        if ($WCMb->vendor_caps->vendor_payment_settings('give_shipping') && !get_user_meta($this->id, '_vendor_give_shipping', true) && wc_shipping_enabled()) {
            $is_enable = true;
        }
        return apply_filters('is_wcmb_vendor_transfer_shipping_enable', $is_enable);
    }
    
    public function is_transfer_tax_enable() {
        global $WCMb;
        $is_enable = false;
        
        if ($WCMb->vendor_caps->vendor_payment_settings('give_tax') && !get_user_meta($this->id, '_vendor_give_tax', true) && wc_tax_enabled()) {
            $is_enable = true;
        }
        return apply_filters('is_wcmb_vendor_transfer_shipping_enable', $is_enable);
    }

    public function is_shipping_tab_enable() {
        $is_enable_flat_rate = false;
        $raw_zones = WC_Shipping_Zones::get_zones();
        $raw_zones[] = array('id' => 0);
        foreach ($raw_zones as $raw_zone) {
            $zone = new WC_Shipping_Zone($raw_zone['id']);
            $raw_methods = $zone->get_shipping_methods();
            foreach ($raw_methods as $raw_method) {
                if ($raw_method->id == 'flat_rate') {
                    $is_enable_flat_rate = true;
                }
            }
        }
        $is_shipping_flat_rate_enable = false;
        if ($this->is_shipping_enable() && $is_enable_flat_rate) {
            $is_shipping_flat_rate_enable = true;
        }
        return apply_filters('is_wcmb_vendor_shipping_tab_enable', $is_shipping_flat_rate_enable, $this->is_shipping_enable());
    }

    /**
     * format_order_details function
     * @access public
     * @param order id , product_id
     * @return array of order details
     */
    public function format_order_details($orders, $product_id) {
        $body = $items = array();
        $product = wc_get_product($product_id)->get_title();
        foreach (array_unique($orders) as $order) {
            $i = $order;
            $order = new WC_Order($i);
            $body[$i] = array(
                'order_number' => $order->get_order_number(),
                'product' => $product,
                'name' => $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(),
                'address' => $order->get_shipping_address_1(),
                'city' => $order->get_shipping_city(),
                'state' => $order->get_shipping_state(),
                'zip' => $order->get_shipping_postcode(),
                'email' => $order->get_billing_email(),
                'date' => $order->get_date_created(),
                'comments' => wptexturize($order->get_customer_note()),
            );

            $items[$i]['total_qty'] = 0;
            foreach ($order->get_items() as $line_id => $item) {
                if ($item['product_id'] != $product_id && $item['variation_id'] != $product_id) {
                    continue;
                }

                $items[$i]['items'][] = $item;
                $items[$i]['total_qty'] += $item['qty'];
            }
        }

        return array('body' => $body, 'items' => $items, 'product_id' => $product_id);
    }

    /**
     * get_vendor_orders_reports_of function
     * @access public
     * @param report_type string
     * @param args array()
     * @return array of order details
     */
    public function get_vendor_orders_reports_of($report_type = 'vendor_stats', $args = array()) {
        global $wpdb;
        $today = @date('Y-m-d 00:00:00', strtotime("+1 days"));
        $last_seven_day_date = date('Y-m-d 00:00:00', strtotime('-7 days'));
        $reports = array();
        switch ($report_type) {
            case 'vendor_stats':
                $defaults = array(
                    'vendor_id' => $this->id,
                    'end_date' => $today,
                    'start_date' => $last_seven_day_date,
                    'is_trashed' => ''
                );
                $args = apply_filters('get_vendor_orders_reports_of_vendor_stats_query_args', wp_parse_args($args, $defaults));
                $sale_results = $wpdb->get_results(
                        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wcmb_vendor_orders WHERE vendor_id=%d AND `created` BETWEEN %s AND %s AND `is_trashed`=%d", $args['vendor_id'], $args['start_date'], $args['end_date'], $args['is_trashed']
                        )
                );
                $item_total_week = 0;
                $comission_total_arr = array();
                $total_comission_week = 0;
                $shipping_total_week = 0;
                $tax_total_week = 0;
                $net_withdrawal_balance = 0;
                $discount_amount = 0;
                $com_orders = array();
                $sale_item_count = array();
                $vendor_sales_results = array('traffic_no' => 0, 'coupon_total' => 0, 'withdrawal' => 0, 'earning' => 0, 'sales_total' => 0, 'orders_no' => 0);
                if ($sale_results) {
                    foreach ($sale_results as $sale) {
                        if ($sale->commission_id != 0 && $sale->commission_id != '') {
                            $order_item_id_week = $sale->order_item_id;
                            $sale_item_count[] = $sale->quantity;
                            $item_total_week += get_metadata('order_item', $sale->order_item_id, '_line_total', true);
                            if (!in_array($sale->commission_id, $comission_total_arr)) {
                                $comission_total_arr[] = $sale->commission_id;
                                $order_id = get_post_meta($sale->commission_id, '_commission_order_id', true);
                                $com_orders[] = $order_id;
                                $order = wc_get_order($order_id);
                                if ($order) {
                                    $discount_amount += $order->get_total_discount();
                                }
                                $amount = get_wcmb_vendor_order_amount(array('commission_id' => $sale->commission_id), $this->id);
                                $total_comission_week += $amount['total'];
                                $shipping_total_week += $amount['shipping_amount'];
                                $tax_total_week += $amount['tax_amount'] + $amount['shipping_tax_amount'];
                                $paid_status_week = get_metadata('post', $sale->commission_id, '_paid_status', true);
                                if ($paid_status_week == "paid") {
                                    $net_withdrawal_balance += $amount['total'];
                                }
                            }
                        }
                    }
                    $item_total_week += ($shipping_total_week + $tax_total_week);
                    $vendor_sales_results['sales_total'] = $item_total_week;
                    $vendor_sales_results['earning'] = $total_comission_week;
                    $vendor_sales_results['withdrawal'] = $net_withdrawal_balance;
                    $where = "created BETWEEN '{$args['start_date']}' AND '{$args['end_date']}' AND ";
                    $visitor_data = wcmb_get_visitor_stats($this->id, $where);
                    $vendor_sales_results['traffic_no'] = count($visitor_data);
                    $vendor_sales_results['coupon_total'] = $discount_amount;
                    $vendor_sales_results['orders_no'] = count($com_orders);
                }

                $reports = $vendor_sales_results;
                break;

            case 'pending_shipping':
                $defaults = array(
                    'vendor_id' => $this->id,
                    'end_date' => $today,
                    'start_date' => $last_seven_day_date
                );
                $args = apply_filters('get_vendor_orders_reports_of_pending_shipping_query_args', wp_parse_args($args, $defaults));
                $pending_shippings = $wpdb->get_results(
                        $wpdb->prepare("SELECT order_id FROM {$wpdb->prefix}wcmb_vendor_orders WHERE commission_id > 0 AND vendor_id=%d AND `created` BETWEEN %s AND %s AND `is_trashed` != 1 AND `shipping_status` != 1 group by order_id order by order_id DESC", $args['vendor_id'], $args['start_date'], $args['end_date']
                        )
                );
                $reports = $pending_shippings;
                break;

            default:
                $defaults = array(
                    'vendor_id' => $this->id,
                    'end_date' => $today,
                    'start_date' => $last_seven_day_date,
                    'is_trashed' => ''
                );
                $args = apply_filters('get_vendor_orders_reports_of_default_query_args', wp_parse_args($args, $defaults));
                $vendor_orders = $wpdb->get_results(
                        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wcmb_vendor_orders WHERE vendor_id=%d AND `created` BETWEEN %s AND %s AND `is_trashed`=%d", $args['vendor_id'], $args['start_date'], $args['end_date'], $args['is_trashed']
                        )
                );
                $reports = $vendor_orders;
                break;
        }
        return apply_filters('wcmb_vendor_order_report_of_data', $reports, $report_type, $args);
    }

    /**
     * Mark as shipped vendor order 
     * @global object $wpdb
     * @param int $order_id
     * @param srting $tracking_id
     * @param string $tracking_url
     */
    public function set_order_shipped($order_id, $tracking_id = '', $tracking_url = '') {
        global $wpdb;
        $shippers = get_post_meta($order_id, 'dc_pv_shipped', true) ? get_post_meta($order_id, 'dc_pv_shipped', true) : array();
        if (!in_array($this->id, $shippers)) {
            $shippers[] = $this->id;
            $mails = WC()->mailer()->emails['WC_Email_Notify_Shipped'];
            if (!empty($mails)) {
                $customer_email = get_post_meta($order_id, '_billing_email', true);
                $mails->trigger($order_id, $customer_email, $this->term_id, array('tracking_id' => $tracking_id, 'tracking_url' => $tracking_url));
            }
            update_post_meta($order_id, 'dc_pv_shipped', $shippers);
        }
        $wpdb->query("UPDATE {$wpdb->prefix}wcmb_vendor_orders SET shipping_status = '1' WHERE order_id = $order_id and vendor_id = $this->id");
        do_action('wcmb_vendors_vendor_ship', $order_id, $this->term_id);
        $order = wc_get_order($order_id);
        $comment_id = $order->add_order_note(__('Vendor ', 'MB-multivendor') . $this->page_title . __(' has shipped his part of order to customer.', 'MB-multivendor') . '<br><span>' . __('Tracking Url : ', 'MB-multivendor') . '</span> <a target="_blank" href="' . $tracking_url . '">' . $tracking_url . '</a><br><span>' . __('Tracking Id : ', 'MB-multivendor') . '</span>' . $tracking_id, '1', true);
        // update comment author & email
        wp_update_comment(array('comment_ID' => $comment_id, 'comment_author' => $this->page_title, 'comment_author_email' => $this->user_data->user_email));
        add_comment_meta($comment_id, '_vendor_id', $this->id);
    }

    /**
     * Returns vendor image/banner.
     *
     * @param string $type (default: 'image')
     * @param string/array $size (default: 'full')
     * @param boolean $protocol (default: false)
     * @return string
     */
    public function get_image($type = 'image', $size = 'full', $protocol = false) {
        $image = false;
        $id = $this->__get($type);

        if (!is_numeric($id)) {
            $id = get_attachment_id_by_url($id);
        }
        if ($id == 0) { /* if no attachment id found from attachment url */
            $image = $this->__get($type);
        } else {
            $image_attributes = wp_get_attachment_image_src($id, $size, true);
            if (is_array($image_attributes) && count($image_attributes)) {
                $image = $image_attributes[0];
            }
        }
        $image = apply_filters('wcmb_vendor_get_image_src', $image);
        if(!$protocol)
            return str_replace( array( 'https://', 'http://' ), '//', $image );
        else
            return $image;
    }

    /**
     * Get Announcements.
     *
     * @param int $id (default: current vendor)
     * @param array $args
     * @return array
     */
    public function get_announcements($id = '', $args = array()) {
        $vendor_id = '';
        $announcements = array();
        if ($id) {
            $vendor_id = $id;
        } else {
            $vendor_id = $this->id;
        }
        $default = array(
            'posts_per_page' => -1,
            'post_type' => 'wcmb_vendor_notice',
            'post_status' => 'publish',
            'suppress_filters' => true
        );
        $args = wp_parse_args($args, $default);
        $posts_array = get_posts($args);
        $dismiss_notices_ids = get_user_meta($vendor_id, '_wcmb_vendor_message_deleted', true);
        if (!empty($dismiss_notices_ids)) {
            $dismiss_notices_ids_array = explode(',', $dismiss_notices_ids);
        } else {
            $dismiss_notices_ids_array = array();
        }
        $readed_notices_ids = get_user_meta($vendor_id, '_wcmb_vendor_message_readed', true);
        if (!empty($readed_notices_ids)) {
            $readed_notices_ids_array = explode(',', $readed_notices_ids);
        } else {
            $readed_notices_ids_array = array();
        }
        if ($posts_array) {
            foreach ($posts_array as $post) {
                // deleted by vendor
                if (!in_array($post->ID, $dismiss_notices_ids_array)) {
                    $announcements['all'][$post->ID] = $post;
                    // readed by vendor
                    if (in_array($post->ID, $readed_notices_ids_array)) {
                        $post->is_read = true;
                        $announcements['read'][$post->ID] = $post;
                    } else {
                        $post->is_read = false;
                        $announcements['unread'][$post->ID] = $post;
                    }
                } else {
                    $post->is_read = false;
                    $announcements['deleted'][$post->ID] = $post;
                }
            }
        }
        return $announcements;
    }

    /**
     * Clear vendor all transients.
     *
     * @param int $id (default: current vendor)
     * @return void
     */
    public function clear_all_transients($id = '') {
        $vendor_id = $this->id;
        $response = false;
        if ($id) {
            $vendor_id = $id;
        }
        $transients_to_clear = array();
        // Transient names that include a vendor ID
        $vendor_transient_names = apply_filters('wcmb_clear_all_transients_included_vendor_id', array(
            'wcmb_dashboard_reviews_for_vendor_',
            'wcmb_customer_qna_for_vendor_',
            'wcmb_visitor_stats_data_',
            'wcmb_stats_report_data_',
        ));
        if ($vendor_id > 0) {
            foreach ($vendor_transient_names as $transient) {
                $transients_to_clear[] = $transient . $vendor_id;
            }
        }
        $transients_to_clear = apply_filters('wcmb_vendor_before_transients_to_clear', $transients_to_clear, $vendor_id);
        // Delete transients
        foreach ($transients_to_clear as $transient) {
            $response = delete_transient($transient);
        }
        do_action('wcmb_vendor_clear_all_transients', $vendor_id);
        return $response;
    }

    /**
     * Get vendor address.
     *
     * @param  array $args Arguments to show in address.
     * @return string
     */
    public function get_formatted_address($args = array(), $sep = ', ') {
        $formatted_address = array(
            'address_1' => $this->__get('address_1'),
            'address_2' => $this->__get('address_2'),
            'city' => $this->__get('city'),
            'state' => $this->__get('state'),
            'country' => $this->__get('country'),
            'postcode' => $this->__get('postcode'),
        );

        if($args) :
            foreach ($formatted_address as $key => $value) {
                if(in_array($key, $args)) continue;
                unset($formatted_address[$key]);
            }
        endif;
        // check empty data
        $addresses = array();
        foreach ($formatted_address as $key => $value) {
            if(!empty($value)){
                $addresses[$key] = $value;
            }
        }
        $addresses = apply_filters( 'wcmb_vendor_before_get_formatted_address', $addresses );

        $formatted_address = implode($sep, $addresses);

        return $formatted_address;
    }
    
    /**
     * Get order totals for display on pages and in emails.
     *
     * since wcmb 3.2.3
     * @param integer $order_id Order id.
     * @param string $split_tax Tax to display.
     * @param string $html_price Price to display.
     * @return array
    */
    public function get_vendor_order_item_totals($order_id, $split_tax = false, $html_price = true) {
        if($order_id){
            $order = wc_get_order(absint($order_id));
            $order_total_arr = array();
            $vendor_items = get_wcmb_vendor_orders(array('order_id' => $order_id, 'vendor_id' => $this->id));
            $vendor_order_amount = get_wcmb_vendor_order_amount(array('order_id' => $order_id, 'vendor_id' => $this->id));
            $vendor_shipping_method = get_wcmb_vendor_order_shipping_method($order_id, $this->id);
            $total_rows  = array();
            // items subtotals
            if($vendor_items){
                $subtotal = 0;
                foreach ($vendor_items as $item) {
                    $item_obj = $order->get_item($item->order_item_id); 
                    $subtotal += $item_obj->get_subtotal();
                }
                $order_total_arr[] = $subtotal;
                $total_rows['order_subtotal'] = array(
                    'label' => __( 'Subtotal:', 'woocommerce' ),
                    'value' => ($html_price) ? wc_price($subtotal) : $subtotal,
                );
            }
            // shipping methods
            if ( $this->is_shipping_enable() && $vendor_shipping_method ) {
                $total_rows['shipping'] = array(
                    'label' => __( 'Shipping:', 'woocommerce' ),
                    'value' => $vendor_shipping_method->get_name(),
                );
            }
            // shipping cost
            if ( $this->is_shipping_enable() && $vendor_order_amount['shipping_amount'] ) {
                $order_total_arr[] = $vendor_order_amount['shipping_amount'];
                $total_rows['shipping_cost'] = array(
                    'label' => __( 'Shipping cost:', 'MB-multivendor' ),
                    'value' => ($html_price) ? wc_price($vendor_order_amount['shipping_amount']) : $vendor_order_amount['shipping_amount'],
                );
            }
            // tax
            if(!apply_filters('wcmb_get_vendor_order_item_totals_split_taxes', $split_tax, $order_id, $vendor_order_amount, $this->id)){
                $order_total_arr[] = $vendor_order_amount['tax_amount'] + $vendor_order_amount['shipping_tax_amount'];
                $total_rows['tax'] = array(
                    'label' => WC()->countries->tax_or_vat() . ':',
                    'value' => ($html_price) ? wc_price($vendor_order_amount['tax_amount'] + $vendor_order_amount['shipping_tax_amount']) : $vendor_order_amount['tax_amount'] + $vendor_order_amount['shipping_tax_amount'],
                );
            }else{
                $order_total_arr[] = $vendor_order_amount['shipping_tax_amount'];
                $total_rows['shipping_tax'] = array(
                    'label' => __( 'Shipping:', 'woocommerce' ).' '.WC()->countries->tax_or_vat() . ':',
                    'value' => ($html_price) ? wc_price($vendor_order_amount['shipping_tax_amount']) : $vendor_order_amount['shipping_tax_amount'],
                );
                $order_total_arr[] = $vendor_order_amount['tax_amount'];
                $total_rows['tax'] = array(
                    'label' => WC()->countries->tax_or_vat() . ':',
                    'value' => ($html_price) ? wc_price($vendor_order_amount['tax_amount']) : $vendor_order_amount['tax_amount'],
                );
            }
            // payment methods
            $total_rows['payment_method'] = array(
                'label' => __( 'Payment method:', 'woocommerce' ),
                'value' => $order->get_payment_method_title(),
            );
            // Order totals
            $total_rows['order_total'] = array(
                'label' => __( 'Total:', 'woocommerce' ),
                'value' => ($html_price) ? wc_price(array_sum($order_total_arr)) : array_sum($order_total_arr),
            );
            
            return apply_filters( 'wcmb_get_vendor_order_item_totals', $total_rows, $order_id, $this->id );
        }
        return false;
    }

}

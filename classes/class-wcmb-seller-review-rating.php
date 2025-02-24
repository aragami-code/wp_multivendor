<?php

/**

 */
class WCMb_Seller_Review_Rating {

    public function __construct() {
        global $WCMb;
        $rating_settings = get_option('wcmb_general_sellerreview_settings_name');
        if (get_wcmb_vendor_settings('is_sellerreview', 'general') == 'Enable') {
            //add_action('woocommerce_after_main_content', array($this, 'wcmb_seller_review_rating_form'), 5);
            add_action('woocommerce_after_shop_loop', array($this, 'wcmb_seller_review_rating_form'), 30);
            add_action('add_meta_boxes', array($this, 'add_wcmb_rating_meta_box'));
            add_action('comment_save_pre', array($this, 'save_wcmb_rating_meta_box'));
            add_filter('widget_comments_args', array($this, 'remove_vendor_rating_from_recent_comment'), 10);
            add_action('woocommerce_order_item_meta_end', array($this, 'wcmb_review_rating_link'), 10, 3);
        }
    }

    function wcmb_vendor_list_rating_rating_value($vendor_term_id, $vendor_id) {
        global $WCMb;
        $rating_info = wcmb_get_vendor_review_info($vendor_term_id);
        $WCMb->template->get_template('review/rating_vendor_lists.php', array('rating_val_array' => $rating_info));
    }

    function wcmb_review_rating_link($item_id, $item, $order) {
        global $WCMb;
        $rating_settings = get_option('wcmb_general_sellerreview_settings_name');
        $arr_values = array();
        $arr_status[] = 'completed';
        $arr_status[] = 'processing';
        $arr_status_final = apply_filters('wcmb_rating_review_order_status_filter', $arr_status);
        if (get_wcmb_vendor_settings('is_sellerreview_varified', 'general') == 'Enable') {
            if (is_array($arr_status_final) && in_array($order->get_status(), $arr_status_final)) {
                if ($item['product_id']) {
                    $product = get_post($item['product_id']);
                    if ($product) {
                        if (is_user_wcmb_vendor($product->post_author)) {
                            $vendor = new WCMb_Vendor($product->post_author);
                            $term_id = get_user_meta($vendor->id, '_vendor_term_id', true);
                            $term = get_term_by('id', $term_id, $WCMb->taxonomy->taxonomy_name);
                            $term_link = get_term_link($term, $WCMb->taxonomy->taxonomy_name);
                            $review_link = trailingslashit($term_link) . '#reviews';
                            $arr_values['vendor_review_link'] = $review_link;
                            $arr_values['shop_name'] = $vendor->page_title;
                            $arr_values['product_name'] = $product->post_title;
                            $WCMb->template->get_template('review/review-link.php', array('review_data' => $arr_values));
                        }
                    }
                }
            }
        } else {
            if ($item['product_id']) {
                $product = get_post($item['product_id']);
                if ($product) {
                    if (is_user_wcmb_vendor($product->post_author)) {
                        $vendor = new WCMb_Vendor($product->post_author);
                        $term_id = get_user_meta($vendor->id, '_vendor_term_id', true);
                        $term = get_term_by('id', $term_id, $WCMb->taxonomy->taxonomy_name);
                        $term_link = get_term_link($term, $WCMb->taxonomy->taxonomy_name);
                        $review_link = trailingslashit($term_link) . '#reviews';
                        $arr_values['vendor_review_link'] = $review_link;
                        $arr_values['shop_name'] = $vendor->page_title;
                        $arr_values['product_name'] = $product->post_title;
                        $WCMb->template->get_template('review/review-link.php', array('review_data' => $arr_values));
                    }
                }
            }
        }
    }

    function remove_vendor_rating_from_recent_comment($args) {
        $args['post__not_in'] = wcmb_vendor_dashboard_page_id();
        return $args;
    }

    function wcmb_seller_review_rating_form() {
        global $WCMb;
        if (is_tax($WCMb->taxonomy->taxonomy_name)) {
            $queried_object = get_queried_object();
            $WCMb->template->get_template('wcmb-vendor-review-form.php', array('queried_object' => $queried_object));
        }
    }

    function add_wcmb_rating_meta_box() {
        global $comment, $WCMb;
        if (!empty($comment)) {
            if ($comment->comment_type == 'wcmb_vendor_rating') {
                $screens = array('comment');
                foreach ($screens as $screen) {
                    add_meta_box(
                            'wcmb_vendor_rating', __('Vendor Rating', 'MB-multivendor'), array($this, 'wcmb_comment_vendor_rating_callback'), $screen, 'normal', 'high'
                    );
                }
            }
        }
    }

    function wcmb_comment_vendor_rating_callback($comment) {
        global $WCMb;
        $vendor_rating_id = get_comment_meta($comment->comment_ID, 'vendor_rating_id', true);
        $vendor = new WCMb_Vendor($vendor_rating_id);
        if($vendor){
            $name = $vendor->page_title;
        }else{
            $user = new WP_User($vendor_rating_id);
            $name = $user->display_name;
        }
        ?>
        <table class="form-table">
            <tbody>			
        <?php $WCMb->wcmb_wp_fields->dc_generate_form_field($this->get_wcmb_comment_rating_field($comment), array('in_table' => 1)); ?>
                <tr class="vendor_rating_author_wrapper">
                    <th class="vendor_rating_author_label_holder">
                        <p class="vendor_rating_author">
                            <strong><?php echo __('Vendor Name.', 'MB-multivendor'); ?></strong>
                        </p>
                        <label for="vendor_rating_author" class="screen-reader-text"><?php echo __('Vendor Name.', 'MB-multivendor'); ?></label>
                    </th>
                    <td>
        <?php echo $name; ?>
                    </td>
                </tr>
            </tbody>
        </table>
                <?php
            }

            function save_wcmb_rating_meta_box($comment_data) {
                if (isset($_POST['vendor_rating']) && !empty($_POST['vendor_rating'])) {
                    if (isset($_POST['comment_ID']) && !empty($_POST['comment_ID'])) {
                        update_comment_meta($_POST['comment_ID'], 'vendor_rating', $_POST['vendor_rating']);
                    }
                }
                return $comment_data;
            }

            function get_wcmb_comment_rating_field($comment) {
                global $WCMb;
                $vendor_rating = get_comment_meta($comment->comment_ID, 'vendor_rating', true);
                $fields = apply_filters('wcmb_vendor_rating_field_filter', array(
                    "vendor_rating" => array(
                        'label' => __('Vendor Rating.', 'MB-multivendor'),
                        'type' => 'select',
                        'desc' => __('Vendor Rating Star.', 'MB-multivendor'),
                        'options' => array('' => __('Please Select', 'MB-multivendor'), '1' => __('1 Star', 'MB-multivendor'), '2' => __('2 Star', 'MB-multivendor'), '3' => __('3 Star', 'MB-multivendor'), '4' => __('4 Star', 'MB-multivendor'), '5' => __('5 Star', 'MB-multivendor')),
                        'value' => $vendor_rating ? $vendor_rating : '',
                        'class' => 'user-profile-fields'
                    )
                ));
                return $fields;
            }

        }
        
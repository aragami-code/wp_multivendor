<?php

class WCMb_Settings_To_Do_List {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;

    /**
     * Start up
     */
    public function __construct($tab) {
        $this->tab = $tab;
        $this->options = get_option("wcmb_{$this->tab}_settings_name");
        $this->settings_page_init();
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMb;
        do_action('before_wcmb_to_do_list');
        //pending vendor
        $get_pending_vendors = get_users('role=dc_pending_vendor');
        if (!empty($get_pending_vendors)) {
            ?>
            <h3><?php echo apply_filters('to_do_pending_vendor_text', __('Pending Vendor Approval', 'MB-multivendor')); ?></h3>
            <table class="form-table" id="to_do_list">
                <tbody>
                    <tr>
                        <th style="width:50%" ><?php _e('Pending User', 'MB-multivendor'); ?> </th>
                        <?php do_action('wcmb_todo_pending_vendor_approval_table_header'); ?>
                        <th><?php _e('', 'MB-multivendor'); ?></th>
                        <th><?php _e('', 'MB-multivendor'); ?></th>
                        <th><?php _e('options', 'MB-multivendor'); ?></th>
                        <th><?php _e('', 'MB-multivendor'); ?></th>
                    </tr>
                    <?php
                    foreach ($get_pending_vendors as $pending_vendor) {
                        $dismiss = get_user_meta($pending_vendor->ID, '_dismiss_to_do_list', true);
                        if ($dismiss)
                            continue;
                        ?>
                        <tr>
                            <td style="width:50%" class="username column-username"><?php echo get_avatar($pending_vendor->ID, 32); ?><?php echo $pending_vendor->user_login; ?></td>
                            <?php do_action('wcmb_todo_pending_vendor_approval_table_row_data', $pending_vendor); ?>
                           

                              
                            <td class="activate"></td>
                            <td class="reject"></td>
                             
                            <td class="reject"><a target="_blank" href="<?php echo apply_filters( 'wcmb_todo_pending_user_list_edit_action_url', admin_url('user-edit.php?user_id='.$pending_vendor->ID) ); ?>"><input type="button" class="vendor_edit_button" value="Edit" /> </a> </td>
                            <td class="edit"> </td>
                            
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php
        }
        $vendor_ids = array();
        $vendors = get_wcmb_vendors();
        if (!empty($vendors) && is_array($vendors)) {
            foreach ($vendors as $vendor) {
                $vendor_ids[] = $vendor->id;
            }
        }
        //coupon
        $args = array(
            'posts_per_page' => -1,
            'author__in' => $vendor_ids,
            'post_type' => 'shop_coupon',
            'post_status' => 'pending',
        );
        $get_pending_coupons = new WP_Query($args);
        $get_pending_coupons = $get_pending_coupons->get_posts();
        if (!empty($get_pending_coupons)) {
            ?>
            <h3><?php _e('Pending Coupons Approval', 'MB-multivendor'); ?></h3>
            <table class="form-table" id="to_do_list">
                <tbody>
                    <tr>
                        <?php
                        $table_headers = apply_filters('wcmb_todo_pending_coupon_approval_table_headers', array(
                            'vendor' => __('Vendor Name', 'MB-multivendor'),
                            'coupon' => __('Coupon Name', 'MB-multivendor'),
                            'edit' => __('', 'MB-multivendor'),
                            'dismiss' => __('options', 'MB-multivendor'),
                        ));
                        if ($table_headers) :
                            foreach ($table_headers as $key => $label) {
                                ?>
                                <th><?php echo $label; ?> </th>
                            <?php
                            }
                        endif;
                        ?>
                    </tr>
                    <?php
                    foreach ($get_pending_coupons as $get_pending_coupon) {
                        $dismiss = get_post_meta($get_pending_coupon->ID, '_dismiss_to_do_list', true);
                        if ($dismiss)
                            continue;
                        ?>
                        <tr>
                            <?php
                            $currentvendor = get_wcmb_vendor($get_pending_coupon->post_author);
                            $vendor_term = get_term($currentvendor->term_id);
                            if ($table_headers) :
                                foreach ($table_headers as $key => $label) {
                                    switch ($key) {
                                        case 'vendor':
                                            ?>
                                            <td class="coupon column-coupon"><a href="user-edit.php?user_id=<?php echo $get_pending_coupon->post_author; ?>&amp;wp_http_referer=%2Fwordpress%2Fdc_vendor%2Fwp-admin%2Fusers.php%3Frole%3Ddc_vendor" target="_blank"><?php echo $vendor_term->name; ?></a></td>
                                            <?php break;
                                        case 'coupon':
                                            ?>
                                            <td class="coupon column-coupon"><?php echo $get_pending_coupon->post_title; ?></td>
                                            <?php break;
                                        case 'edit':
                                            ?>
                                            <td class="edit"> </td>
                                            <?php break;
                                        case 'dismiss':
                                            ?>
                                            <td class="dismiss">
                                                <a target="_blank" href="post.php?post=<?php echo $get_pending_coupon->ID; ?>&action=edit"><input type="button" class="vendor_edit_button" value="Edit" /></a></td>
                                            <?php
                                            break;
                                        default:
                                            do_action('wcmb_todo_pending_coupon_approval_table_row_data', $key, $get_pending_coupon);
                                            break;
                                    }
                                }
                            endif;
                            ?>
                        </tr>
            <?php } ?>
                </tbody>
            </table>
            <?php
        }

        //produ
        $args = array(
            'posts_per_page' => -1,
            'author__in' => $vendor_ids,
            'post_type' => 'product',
            'post_status' => 'pending',
        );
        $get_pending_products = new WP_Query($args);
        $get_pending_products = $get_pending_products->get_posts();
        if (!empty($get_pending_products)) {
            ?>
            <h3><?php _e('Pending Products Approval', 'MB-multivendor'); ?></h3>
            <table class="form-table" id="to_do_list">
                <tbody>
                    <tr>
                        <?php
                        $table_headers = apply_filters('wcmb_todo_pending_product_approval_table_headers', array(
                            'vendor' => __('Vendor Name', 'MB-multivendor'),
                            'product' => __('Product Name', 'MB-multivendor'),
                            'edit' => __('', 'MB-multivendor'),
                            'dismiss' => __('options', 'MB-multivendor'),
                        ));
                        if ($table_headers) :
                            foreach ($table_headers as $key => $label) {
                                ?>
                                <th><?php echo $label; ?> </th>
                        <?php
                        }
                    endif;
                    ?>
                    </tr>
                        <?php
                        foreach ($get_pending_products as $get_pending_product) {
                            $dismiss = get_post_meta($get_pending_product->ID, '_dismiss_to_do_list', true);
                            if ($dismiss)
                                continue;
                            ?>
                        <tr>
                            <?php
                            $currentvendor = get_wcmb_vendor($get_pending_product->post_author);
                            $vendor_term = get_term($currentvendor->term_id);
                            if ($table_headers) :
                                foreach ($table_headers as $key => $label) {
                                    switch ($key) {
                                        case 'vendor':
                                            ?>
                                            <td class="vendor column-coupon"><a href="user-edit.php?user_id=<?php echo $get_pending_product->post_author; ?>&amp;wp_http_referer=%2Fwordpress%2Fdc_vendor%2Fwp-admin%2Fusers.php%3Frole%3Ddc_vendor" target="_blank"><?php echo $vendor_term->name; ?></a></td>
                                            <?php break;
                                        case 'product':
                                            ?>
                                            <td class="coupon column-coupon"><?php echo $get_pending_product->post_title; ?></td>
                                            <?php break;
                                        case 'edit':
                                            ?>
                                            <td class="edit"></td>
                                <?php break;
                            case 'dismiss':
                                ?>
                                            <td class="dismiss">
                                                <a target="_blank" href="post.php?post=<?php echo $get_pending_product->ID; ?>&action=edit"><input type="button" class="vendor_edit_button" value="Edit" /> </a> </td>
                                <?php
                                break;
                            default:
                                do_action('wcmb_todo_pending_product_approval_table_row_data', $key, $get_pending_product);
                                break;
                        }
                    }
                endif;
                ?>
                        </tr>
            <?php } ?>
                </tbody>
            </table>
            <?php
        }


        //commission
        $args = array(
            'post_type' => 'wcmb_transaction',
            'post_status' => 'wcmb_processing',
            'meta_key' => 'transaction_mode',
            'meta_value' => 'direct_bank',
            'posts_per_page' => -1
        );
        $transactions = get_posts($args);

        if (!empty($transactions)) {
            ?>
            <h3><?php _e('Pending Bank Transfer', 'MB-multivendor'); ?></h3>
            <table class="form-table" id="to_do_list">
                <tbody>
                    <tr>
                    <?php
                    $table_headers = apply_filters('wcmb_todo_pending_bank_transfer_table_headers', array(
                        'vendor' => __('Vendor Name', 'MB-multivendor'),
                        'commission' => __('Commission', 'MB-multivendor'),
                        'amount' => __('Amount', 'MB-multivendor'),
                        'account_details' => __('Account Detail', 'MB-multivendor'),
                        'notify_vendor' => __('Notify the Vendor', 'MB-multivendor'),
                        'dismiss' => __('Dismiss', 'MB-multivendor'),
                    ));
                    if ($table_headers) :
                        foreach ($table_headers as $key => $label) {
                            ?>
                                <th><?php echo $label; ?> </th>
                        <?php
                        }
                    endif;
                    ?>
                    </tr>
                        <?php
                        foreach ($transactions as $transaction) {
                            $dismiss = get_post_meta($transaction->ID, '_dismiss_to_do_list', true);
                            $vendor_term_id = $transaction->post_author;
                            $currentvendor = get_wcmb_vendor_by_term($vendor_term_id);
                            $vendor_term = get_term($vendor_term_id);
                            if ($dismiss || !$currentvendor) {
                                continue;
                            }
                            $account_name = get_user_meta($currentvendor->id, '_vendor_account_holder_name', true);
                            $account_no = get_user_meta($currentvendor->id, '_vendor_bank_account_number', true);
                            $bank_name = get_user_meta($currentvendor->id, '_vendor_bank_name', true);
                            $iban = get_user_meta($currentvendor->id, '_vendor_iban', true);
                            $amount = get_post_meta($transaction->ID, 'amount', true) - get_post_meta($transaction->ID, 'transfer_charge', true) - get_post_meta($transaction->ID, 'gateway_charge', true);
                            ?>
                        <tr>
                            <?php
                            if ($table_headers) :
                                foreach ($table_headers as $key => $label) {
                                    switch ($key) {
                                        case 'vendor':
                                            ?>
                                            <td class="vendor column-coupon"><a href="user-edit.php?user_id=<?php echo $currentvendor->id; ?>&amp;wp_http_referer=%2Fwordpress%2Fdc_vendor%2Fwp-admin%2Fusers.php%3Frole%3Ddc_vendor" target="_blank"><?php echo $vendor_term->name; ?></a></td>
                                            <?php break;
                                        case 'commission':
                                            ?>
                                            <td class="commission column-coupon"><?php echo $transaction->post_title; ?></td>
                                                <?php break;
                                            case 'amount':
                                                ?>
                                            <td class="commission_val column-coupon"><?php echo wc_price($amount); ?></td>
                                                <?php
                                                break;
                                            case 'account_details':
                                                $address_array = apply_filters('wcmb_todo_pending_bank_transfer_row_account_details_data', array(
                                                    __('Account Name-', 'MB-multivendor') . ' ' . $account_name,
                                                    __('Account No -', 'MB-multivendor') . ' ' . $account_no,
                                                    __('Bank Name -', 'MB-multivendor') . ' ' . $bank_name,
                                                    __('IBAN -', 'MB-multivendor') . ' ' . $iban,
                                                        ), $currentvendor, $transaction);
                                                ?>
                                            <td class="account_detail"><?php echo implode('<br/>', $address_array); ?></td>
                                <?php break;
                            case 'notify_vendor':
                                ?>
                                            <td class="done"><input class="vendor_transaction_done_button" data-transid="<?php echo $transaction->ID; ?>" data-vendorid="<?php echo $vendor_term_id; ?>" type="button" id="done_request" name="done_request" value="Done"></td>
                                <?php break;
                            case 'dismiss':
                                ?>
                                            <td class="dismiss"><input class="vendor_dismiss_button" data-type="dc_commission" data-id="<?php echo $transaction->ID; ?>" type="button" id="dismiss_request" name="dismiss_request" value="Dismiss"></td>
                                <?php
                                break;
                            default:
                                do_action('wcmb_todo_pending_bank_transfer_table_row_data', $key, $get_pending_coupon);
                                break;
                        }
                    }
                endif;
                ?>   
                        </tr>
            <?php } ?>
                </tbody>
            </table>
            <?php
        }
        do_action('after_wcmb_to_do_list');
    }

}

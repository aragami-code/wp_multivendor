<?php

/**
 
 */
defined( 'ABSPATH' ) || exit;
?>
<div role="tabpanel" class="tab-pane fade" id="usage_limit_coupon_data">
    <div class="row-padding">
        <?php do_action( 'wcmb_afm_before_usage_limit_coupon_data', $post->ID, $coupon ); ?>
        <div class="form-group-row"> 
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3" for="usage_limit">
                    <?php esc_html_e( 'Usage limit per coupon', 'woocommerce' ); ?>
                    <span class="img_tip" data-desc="<?php esc_html_e( 'How many times this coupon can be used before it is void.', 'woocommerce' ); ?>"></span>
                </label>
                <div class="col-md-6 col-sm-9">
                    <input type="number" id="usage_limit" name="usage_limit" class="form-control" value="<?php esc_attr_e( $coupon->get_usage_limit( 'edit' ) ? $coupon->get_usage_limit( 'edit' ) : '' ); ?>" placeholder="<?php esc_attr_e( 'Unlimited usage', 'woocommerce' ); ?>" step="1" min="0">
                </div>
            </div> 
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3" for="limit_usage_to_x_items">
                    <?php esc_html_e( 'Limit usage to X items', 'woocommerce' ); ?>
                    <span class="img_tip" data-desc="<?php esc_html_e( 'The maximum number of individual items this coupon can apply to when using product discounts. Leave blank to apply to all qualifying items in cart.', 'woocommerce' ); ?>"></span>
                </label>
                <div class="col-md-6 col-sm-9">
                    <input type="number" id="limit_usage_to_x_items" name="limit_usage_to_x_items" class="form-control" value="<?php esc_attr_e( $coupon->get_limit_usage_to_x_items( 'edit' ) ? $coupon->get_limit_usage_to_x_items( 'edit' ) : '' ); ?>" placeholder="<?php esc_attr_e( 'Apply to all qualifying items in cart', 'woocommerce' ); ?>" step="1" min="0">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3" for="usage_limit_per_user">
                    <?php esc_html_e( 'Usage limit per user', 'woocommerce' ); ?>
                    <span class="img_tip" data-desc="<?php esc_html_e( 'How many times this coupon can be used by an individual user. Uses billing email for guests, and user ID for logged in users.', 'woocommerce' ); ?>"></span>
                </label>
                <div class="col-md-6 col-sm-9">
                    <input type="number" id="usage_limit_per_user" name="usage_limit_per_user" class="form-control" value="<?php esc_attr_e( $coupon->get_usage_limit_per_user( 'edit' ) ? $coupon->get_usage_limit_per_user( 'edit' ) : '' ); ?>" placeholder="<?php esc_attr_e( 'Unlimited usage', 'woocommerce' ); ?>" step="1" min="0">
                </div>
            </div>
        </div>
        <?php do_action( 'wcmb_afm_after_usage_limit_coupon_data', $post->ID, $coupon ); ?>
    </div>
</div>
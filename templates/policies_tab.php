<?php
/**

 */
global $product, $WCMb, $post;
$policies = get_wcmb_product_policies($product->get_id());
?>
<div class="wcmb-product-policies">
    <?php if(isset($policies['shipping_policy']) && !empty($policies['shipping_policy'])){ ?>
    <div class="wcmb-shipping-policies policy">
        <h2 class="wcmb_policies_heading heading"><?php echo apply_filters('wcmb_shipping_policies_heading', __('Shipping Policy', 'MB-multivendor')); ?></h2>
        <div class="wcmb_policies_description description" ><?php echo $policies['shipping_policy']; ?></div>
    </div>
    <?php } if(isset($policies['refund_policy']) && !empty($policies['refund_policy'])){ ?>
    <div class="wcmb-refund-policies policy">
        <h2 class="wcmb_policies_heading heading heading"><?php echo apply_filters('wcmb_refund_policies_heading', __('Refund Policy', 'MB-multivendor')); ?></h2>
        <div class="wcmb_policies_description description" ><?php echo $policies['refund_policy']; ?></div>
    </div>
    <?php } if(isset($policies['cancellation_policy']) && !empty($policies['cancellation_policy'])){ ?>
    <div class="wcmb-cancellation-policies policy">
        <h2 class="wcmb_policies_heading heading"><?php echo apply_filters('wcmb_cancellation_policies_heading', __('Cancellation / Return / Exchange Policy', 'MB-multivendor')); ?></h2>
        <div class="wcmb_policies_description description" ><?php echo $policies['cancellation_policy']; ?></div>
    </div>
    <?php } ?>
</div>
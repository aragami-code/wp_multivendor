<?php
/**

 */
global $WCMb, $product, $wpdb; 

//$have_parent = $product->get_parent_id();
//$parent_product = $product->get_id();
//if($have_parent != 0){
//    $parent_product = $product->get_parent_id();
//}
//$mapped_products = wc_get_products(array('post_parent' => $parent_product, 'posts_per_page' => -1));
//$mapped_products[] = wc_get_product($parent_product);
//if($mapped_products && count($mapped_products) > 1){
//    $button_text = apply_filters('wcmb_more_vendors', __('More Vendors','MB-multivendor'));
//    echo '<a  href="#" class="goto_more_offer_tab button">'.$button_text.'</a>';
//}

$has_product_map_id = get_post_meta($product->get_id(), '_wcmb_spmv_map_id', true);
if($has_product_map_id){
    $products_map_data_ids = get_wcmb_spmv_products_map_data($has_product_map_id);
    $mapped_products = array_diff($products_map_data_ids, array($product->get_id()));
    if(count($mapped_products) >= 1){
        $button_text = apply_filters('wcmb_more_vendors', __('More Vendors','MB-multivendor'));
        $button_text = apply_filters('wcmb_single_product_more_vendors_text', $button_text, $product);
        echo '<a  href="#" class="goto_more_offer_tab button">'.$button_text.'</a>';
    }
}


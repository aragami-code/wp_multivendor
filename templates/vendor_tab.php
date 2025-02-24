<?php

/**

 */
global $WCMb, $product;
$html = '';
$vendor = get_wcmb_product_vendors($product->get_id());
if ($vendor) {
    $html .= '<div class="product-vendor">';
    $html .= apply_filters('wcmb_before_seller_info_tab', '');
    $html .= '<h2>' . $vendor->page_title . '</h2>';
    echo $html;
    $term_vendor = wp_get_post_terms($product->get_id(), $WCMb->taxonomy->taxonomy_name);
    if (!is_wp_error($term_vendor) && !empty($term_vendor)) {
        $rating_result_array = wcmb_get_vendor_review_info($term_vendor[0]->term_id);
        if (get_wcmb_vendor_settings('is_sellerreview_varified', 'general') == 'Enable') {
            $term_link = get_term_link($term_vendor[0]);
            $rating_result_array['shop_link'] = $term_link;
            echo '<div style="text-align:left; float:left;">';
            $WCMb->template->get_template('review/rating-vendor-tab.php', array('rating_val_array' => $rating_result_array));
            echo "</div>";
            echo '<div style="clear:both; width:100%;"></div>';
        }
    }
    $html = '';
    if ('' != $vendor->description) {
        $html .= apply_filters('the_content', $vendor->description );
    }
    $html .= '<p><a href="' . $vendor->permalink . '">' . sprintf(__('More Products from %1$s', 'MB-multivendor'), $vendor->page_title) . '</a></p>';
    $html .= apply_filters('wcmb_after_seller_info_tab', '');
    $html .= '</div>';
    echo $html;
}
?>
<?php
/*
 
 */
global $WCMb, $wc_product_attributes;

$current_vendor_id = apply_filters('wcmb_current_loggedin_vendor_id', get_current_user_id());

// If vendor does not have product submission cap then show message
if (is_user_logged_in() && is_user_wcmb_vendor($current_vendor_id) && !current_user_can('edit_products')) {
    ?>
    <div class="col-md-12">
        <div class="panel panel-default">
            <?php _e('You do not have enough permission to submit a new product. Please contact site administrator.', 'MB-multivendor'); ?>
        </div>
    </div>
    <?php
    return;
}

?>
<div class="col-md-12 add-product-outer-wrapper">
    <div class="select-product-cat-wrapper">
        <?php $is_new_listing = isset($_REQUEST['new_listing']) ? true : false;
        $is_cats_hier = isset($_REQUEST['cats_hier']) ? true : false;
        if( ( $is_new_listing && $is_cats_hier ) || !get_wcmb_vendor_settings('is_singleproductmultiseller', 'general') == 'Enable' ) {
        ?>
        <!-- New product list categories hierarchically -->
        <div class="select-cat-step-wrapper">
            <div class="cat-step1" >
                <div class="panel panel-default pannel-outer-heading mt-0">
                    <div class="panel-heading">
                        <h1><span class="primary-color"><span><?php _e( 'Step 1 of', 'MB-multivendor' );?></span> <?php _e( '2:', 'MB-multivendor' );?></span> <?php _e('Select a product category', 'MB-multivendor'); ?></h1>
                        <h3><?php _e('Once a category is assigned to a product, it cannot be altered.', 'MB-multivendor'); ?></h3>
                    </div>
                    <div class="panel-body panel-content-padding form-horizontal breadcrumb-panel">
                        <div class="product-search-wrapper categories-search-wrapper">
                            <div class="form-text"><?php _e('Search category', 'MB-multivendor'); ?></div>
                            <div class="form-input">
                                <input id="search-categories-keyword" type="text" placeholder="<?php _e('Example: tshirt, music, album etc...', 'MB-multivendor'); ?>">
                                <ul id="searched-categories-results" class="list-group">
                                    
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default pannel-outer-heading wcmb-categories-level-panel has-scroller"> 
                        <div class="cat-column-scroller cat-left-scroller"><i class="wcmb-font ico-left-arrow-icon"></i></div>
                    <div class="form-horizontal cat-list-holder">
                        <div class="wcmb-product-categories-wrap cat-column-wrapper">
                            <div class="wcmb-product-cat-level 1-level-cat cat-column" data-level="1"  data-mcs-theme="dark">
                                <ul class="wcmb-product-categories 1-level" data-cat-level="1">
                                    <?php echo wcmb_list_categories( apply_filters( 'wcmb_vendor_product_classify_1_level_categories', array(
                                    'taxonomy' => 'product_cat', 
                                    'hide_empty' => false, 
                                    'html_list' => true,
                                    'cat_link'  => 'javascript:void(0)',
                                    ) ) ); ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                        <div class="cat-column-scroller cat-right-scroller"><i class="wcmb-font ico-right-arrow-icon"></i></div>
                </div>
            </div>
        </div>
        <?php }else{ ?>
        <!-- List a product by name or gtin -->
        <div class="cat-intro">
            <div class="panel panel-default pannel-outer-heading mt-0"> 
                <div class="panel-body panel-content-padding form-horizontal text-center">
                    <img src="<?php echo $WCMb->plugin_url.'assets/images/add-product-graphic.png'; ?>" alt="">
                    <h1 class="heading-underline"><?php _e('List a New Product', 'MB-multivendor'); ?></h1>
                    <div class="serach-product-cat-wrapper">
                        <h2><?php _e('Search from our existing Product Catalog', 'MB-multivendor'); ?></h2>
                        <form class="search-pro-by-name-gtin">
                            <input type="text" placeholder="<?php _e('Product name, UPC, ISBN ...', 'MB-multivendor'); ?>" class="form-control inline-input search-product-name-gtin-keyword" required>
                            <button type="button" class="btn btn-default search-product-name-gtin-btn"><?php echo strtoupper(__('Search', 'MB-multivendor')); ?></button> 
                        </form>
                        <?php $url = ( get_wcmb_vendor_settings('is_disable_marketplace_plisting', 'general') == 'Enable' ) ? esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_edit_product_endpoint', 'vendor', 'general', 'edit-product'))) : '?new_listing=1&cats_hier=1'; ?>
                        <p><?php _e('Not in the catalog?', 'MB-multivendor'); ?> <a href="<?php echo $url; ?>" class="cat-step-btn"><?php _e('Create a new product', 'MB-multivendor'); ?> <i class="wcmb-font ico-right-arrow-icon"></i></a></p>
                    </div>
                </div>
            </div>
            <div class="panel panel-custom mt-15 product-search-panel searched-products-name-gtin-panel">
                <div class="panel-heading"><?php _e('Your search results:', 'MB-multivendor'); ?></div>
                <div class="panel-body search-result-holder p-0 searched-result-products-name-gtin"></div>
            </div>          
        </div>
        <!-- End List a product by name or gtin -->
        <?php } ?>
        <div class="clearfix"></div>
    </div>
</div>
<?php
do_action('wcmb-frontend-product-manager_template');
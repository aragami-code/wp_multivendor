<?php
/**
 
 */
if (!defined('ABSPATH')) {
    exit;
}
global $WCMb, $post, $wpdb;
if (count($more_product_array) > 0) {
    $i = 0;
    ?>
    <div class="ajax_loader_class_msg"><img src="<?php echo $WCMb->plugin_url ?>assets/images/ajax-loader.gif" alt="ajax-loader" /></div>
    <div class="container">		
        <div class="row rowhead">
            <div class="rowsub "><?php echo __('Vendor', 'MB-multivendor'); ?></div>
            <div class="rowsub"><?php echo __('Price', 'MB-multivendor'); ?></div>
            <div class="rowsub">
                <select name="wcmb_multiple_product_sorting" id="wcmb_multiple_product_sorting" class="wcmb_multiple_product_sorting" attrid="<?php echo $post->ID; ?>" >
                    <option value="price"><?php echo __('Price Low To High', 'MB-multivendor'); ?></option>
                    <option value="price_high"><?php echo __('Price High To Low', 'MB-multivendor'); ?></option>
                    <option value="rating"><?php echo __('Rating High To Low', 'MB-multivendor'); ?></option>
                    <option value="rating_low"><?php echo __('Rating Low To High', 'MB-multivendor'); ?></option>
                </select>
            </div>
            <div style="clear:both;"></div>
        </div>			
        <?php
        $WCMb->template->get_template('single-product/multiple_vendors_products_body.php', array('more_product_array' => $more_product_array, 'sorting' => 'price'));
        ?>		
    </div>		
    <?php
} else {
    ?>
    <div class="container">
        <div class="row">
    <?php echo __('Sorry no more offers available', 'MB-multivendor'); ?>
        </div>
    </div>	
<?php }
?>


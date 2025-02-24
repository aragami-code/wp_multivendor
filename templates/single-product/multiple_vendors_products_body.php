<?php
/**

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $WCMb;
if(isset($more_product_array) && is_array($more_product_array) && count($more_product_array) > 0) {
	if(isset($sorting) && !empty($sorting)) {	
		/*function wcmb_sort_by_price($a, $b) {
			return $a['price_val'] - $b['price_val'];
		}*/		
		if($sorting == 'price') {		
			usort($more_product_array, function($a, $b){return $a['price_val'] - $b['price_val'];});
		}
		elseif($sorting == 'price_high') {		
			usort($more_product_array, function($a, $b){return $a['price_val'] - $b['price_val'];});
			$more_product_array = array_reverse (  $more_product_array);
		}
		elseif($sorting == 'rating') {
			$more_product_array = wcmb_sort_by_rating_multiple_product($more_product_array);			
		}
		elseif($sorting == 'rating_low') {
			$more_product_array = wcmb_sort_by_rating_multiple_product($more_product_array);
			$more_product_array = array_reverse (  $more_product_array);			
		}
	}
	foreach ($more_product_array as $more_product ) {	
            $_product = wc_get_product($more_product['product_id']);
		?>
		<div class="row rowbody">						
			<div class="rowsub ">
				<div class="vendor_name">
					<a href="<?php echo $more_product['shop_link']; ?>" class="wcmb_seller_name"><?php echo $more_product['seller_name']; ?></a>
					<?php do_action('after_wcmb_singleproductmultivendor_vendor_name', $more_product['product_id'], $more_product);?>
				</div>
				<?php 
				if(isset($more_product['rating_data']) && is_array($more_product['rating_data']) && isset($more_product['rating_data']['avg_rating']) && $more_product['rating_data']['avg_rating']!=0 && $more_product['rating_data']['avg_rating']!=''){ 
					echo wc_get_rating_html( $more_product['rating_data']['avg_rating'] );	
				}else {
					echo "<div class='star-rating'></div>";
				}
				?>
			</div>
			<div class="rowsub">
                <?php echo $_product->get_price_html(); ?>
			</div>
			<div class="rowsub">
				<?php if($more_product['product_type'] == 'simple') {?>
					<a href="<?php echo '?add-to-cart='.$more_product['product_id']; ?>" class="buttongap button" ><?php echo apply_filters('add_to_cart_text', __('Add to Cart','MB-multivendor')); ?></a>
				<?php } ?>
				<a href="<?php echo get_permalink($more_product['product_id']); ?>" class="buttongap button" ><?php echo __('Details','MB-multivendor'); ?></a>
			</div>
			<div style="clear:both;"></div>							
		</div>
		
		
	<?php
	}
}
?>
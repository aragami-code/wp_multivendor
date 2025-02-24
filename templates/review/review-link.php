<?php
/**
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $WCMb;
if(isset($review_data) && is_array($review_data)) {
$rating = 0;	
$review_data_final = apply_filters('wcmb_review_link_final_filter',$review_data);
?>
<div class="review_link_data_wappers">
<a target="_blank" class="button" href="<?php echo $review_data_final['vendor_review_link']; ?>"><?php echo __('Leave Vendor feedback','MB-multivendor'); ?></a> 
<a href="<?php echo $review_data_final['vendor_review_link']; ?>" target="_blank"><div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="<?php echo sprintf( __( 'Leave Vendor feedback', 'MB-multivendor' ) ) ?>">
		<span style="width:<?php echo ( $rating / 5 ) * 100; ?>%"><strong itemprop="ratingValue"><?php echo $rating; ?></strong> <?php _e( 'out of 5', 'MB-multivendor' ); ?></span>
	</div></a>
</div>
<?php }?>



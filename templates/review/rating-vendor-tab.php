<?php
/**
 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $WCMb;
$rating   = round( $rating_val_array['avg_rating'],2 );
$count = intval( $rating_val_array['total_rating'] );
$shop_link = $rating_val_array['shop_link'];

?>
<div style="width:100%; height:50px; margin-bottom:5px;">
	<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" style="float:left;" title="<?php echo sprintf( __( 'Rated %s out of 5', 'MB-multivendor' ), $rating ) ?>">
		<span style="width:<?php echo ( $rating_val_array['avg_rating'] / 5 ) * 100; ?>%"><strong itemprop="ratingValue"><?php echo $rating; ?></strong> <?php _e( 'out of 5', 'MB-multivendor' ); ?></span>
	</div>
	<div style="clear:both; height:5px; width:100%;"></div>	
	<a href="<?php echo $shop_link; ?>#reviews" target="_blank">
		<?php 
		if($count > 0 ) {?>	
		<?php echo sprintf( __(' %s Stars out of 5 based on %s Reviews','MB-multivendor'), $rating, $count);	 ?>			
		<?php 
		}
		else {
		?>
		<span style="float:right"><?php echo __(' No Reviews Yet','MB-multivendor');  ?></span>
		<?php }?>
	</a>
</div>

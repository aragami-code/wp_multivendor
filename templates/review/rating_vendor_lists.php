<?php
/**
 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $WCMb;
$rating   = round( $rating_val_array['avg_rating'],2 );
$count = intval( $rating_val_array['total_rating'] );

?>
<div class="wcmb_rating_wrap">
<?php if($count > 0) {?>
	<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" style="float:none;"  title="<?php echo sprintf( __( 'Rated %s out of 5', 'MB-multivendor' ), $rating ) ?>">
		<span style="width:<?php echo ( $rating_val_array['avg_rating'] / 5 ) * 100; ?>%"><strong itemprop="ratingValue"><?php echo $rating; ?></strong> <?php _e( 'out of 5', 'MB-multivendor' ); ?></span>
	</div>
<?php }else {?>
	<div><?php echo __('No Rating Yet','MB-multivendor'); ?></div>
<?php }?>
</div>

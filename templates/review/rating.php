<?php
/**

 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $WCMb;
$rating = round($rating_val_array['avg_rating'], 1);
$count = intval($rating_val_array['total_rating']);
$review_text = $count > 1 ? __('Reviews', 'MB-multivendor') : __('Review', 'MB-multivendor');
?> 
<div style="clear:both; width:100%;"></div> 
<?php if ($count > 0) { ?>
    <span class="wcmb_total_rating_number"><?php echo __(sprintf(' %s ', $rating)); ?></span>
<?php } ?>
<a href="#reviews">
<?php if ($count > 0) { ?>	
        <span itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="<?php echo sprintf(__('Rated %s out of 5', 'MB-multivendor'), $rating) ?>">
            <span style="width:<?php echo ( round($rating_val_array['avg_rating']) / 5 ) * 100; ?>%"><strong itemprop="ratingValue"><?php echo $rating; ?></strong> <?php _e('out of 5', 'MB-multivendor'); ?></span>
        </span>
        <?php echo __(sprintf(' %s %s', $count, $review_text)); ?>

    <?php
} else {
    ?>
        <?php echo __(' No Review Yet ', 'MB-multivendor'); ?>
    <?php } ?>
</a>

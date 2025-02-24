<?php
/**
 
 */
global $WCMb, $wpdb;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (isset($queried_object->term_id) && !empty($queried_object)) {
    $vendor = get_wcmb_vendor_by_term($queried_object->term_id);
    $shop_name = $vendor->page_title;
    $vendor_id = $vendor->id;
    $count = $vendor->get_review_count();
    $is_enable = wcmb_seller_review_enable($queried_object->term_id);
    $current_user = wp_get_current_user();
    $reviews_lists = $vendor->get_reviews_and_rating(0);
}
?>
<div class="wocommerce" >
    <div id="reviews" >
        <div id="wcmb_vendor_reviews">
            <?php if (isset($is_enable) && $is_enable) { ?>
                <div id="review_form_wrapper">
                    <div id="review_form">
                        <div id="respond" class="comment-respond">
                            <?php if ($vendor->id != get_current_vendor_id()) : ?>
                                <h3 id="reply-title" class="comment-reply-title"><?php
                                    if ($count == 0) {
                                        echo sprintf(__('Be the first to review “%s”', 'MB-multivendor'), $shop_name);
                                    } else {
                                        echo sprintf(__('Add a review to “%s”', 'MB-multivendor'), $shop_name);
                                    }
                                    ?> </h3>				
                                <form action="" method="post" id="commentform" class="comment-form" novalidate="">
                                    <p id="wcmb_seller_review_rating"></p>
                                    <p class="comment-form-rating"><label for="rating"><?php echo __('Your Rating', 'MB-multivendor'); ?></label>					
                                        <select name="rating" id="rating">
                                            <option value=""><?php echo __('Rate...', 'MB-multivendor'); ?></option>
                                            <option value="5"><?php echo __('Perfect', 'MB-multivendor'); ?></option>
                                            <option value="4"><?php echo __('Good', 'MB-multivendor'); ?></option>
                                            <option value="3"><?php echo __('Average', 'MB-multivendor'); ?></option>
                                            <option value="2"><?php echo __('Not that bad', 'MB-multivendor'); ?></option>
                                            <option value="1"><?php echo __('Very Poor', 'MB-multivendor'); ?></option>
                                        </select></p>
                                    <p class="comment-form-comment">
                                        <label for="comment"><?php echo __('Your Review', 'MB-multivendor'); ?> </label>
                                        <textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea>
                                    </p>					
                                    <p class="form-submit">
                                        <input id="wcmb_vendor_for_rating" name="wcmb_vendor_for_rating" type="hidden" value="<?php echo $vendor_id; ?>"  >
                                        <input id="author" name="author" type="hidden" value="<?php echo $current_user->display_name; ?>" size="30" aria-required="true">					 
                                        <input id="email" name="email" type="hidden" value="<?php echo $current_user->user_email; ?>" size="30" aria-required="true">
                                        <input name="submit" type="button" id="submit" class="submit" value="<?php _e('Submit', 'MB-multivendor') ?>">

                                    </p>				
                                </form>
                                <?php endif; ?>
                        </div><!-- #respond -->
                    </div>
                </div>
                <?php } ?>
            <div id="comments">
                <?php
                if ($count > 0) {
                    $start = 0;
                    $posts_per_page = get_option('posts_per_page');
                    $total_pages = ceil($count / $posts_per_page);
                    ?>
                    <h2><?php printf(_n('%s review for %s', '%s reviews for %s', $count, 'MB-multivendor'), $count, $shop_name); ?>	</h2>
                    <form id="vendor_review_rating_pagi_form" >
                        <input type="hidden" name="pageno" id="wcmb_review_rating_pageno" value="1" >
                        <input type="hidden" name="postperpage" id="wcmb_review_rating_postperpage" value="<?php echo $posts_per_page; ?>" >
                        <input type="hidden" name="totalpage" id="wcmb_review_rating_totalpage" value="<?php echo $total_pages; ?>" >
                        <input type="hidden" name="totalreview" id="wcmb_review_rating_totalreview" value="<?php echo $count; ?>" >	
                        <input type="hidden" name="term_id" id="wcmb_review_rating_term_id" value = "<?php echo $queried_object->term_id; ?>">
                    </form>
                    <?php
                    if (isset($reviews_lists) && count($reviews_lists) > 0) {
                        echo '<ol class="commentlist vendor_comment_list">';
                        $WCMb->template->get_template('review/wcmb-vendor-review.php', array('reviews_lists' => $reviews_lists, 'vendor_term_id' => $queried_object->term_id));
                        echo '</ol>';
                        if ($total_pages > 1) {
                            echo '<div class="wcmb_review_loader"><img src="' . $WCMb->plugin_url . 'assets/images/ajax-loader.gif" alt="ajax-loader" /></div>';
                            echo '<input name="loadmore" type="button" id="wcmb_review_load_more" class="submit wcmb_load_more" style="float:right;" value="'.__('Load More', 'MB-multivendor').'">';
                        }
                    }
                } elseif ($count == 0) {
                    ?>
                    <p class="woocommerce-noreviews"><?php echo __('There are no reviews yet.', 'MB-multivendor'); ?> </p>
                <?php } ?>
            </div>	
            <div class="clear"></div>

        </div>
    </div>
</div>

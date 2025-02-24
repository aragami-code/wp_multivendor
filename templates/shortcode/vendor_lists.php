<?php
/**
 
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
global $WCMb;
?>

<div id="wcmb-store-conatiner">
    <!-- Map Start -->
    <div class="wcmb-store-locator-wrap">
        <?php if(apply_filters('wcmb_vendor_list_enable_store_locator_map', true)) : ?>
        <div id="wcmb-vendor-list-map" class="wcmb-store-map-wrapper"></div>
        <form name="vendor_list_sort" method="post">
            <input type="hidden" id="wcmb_vlist_center_lat" name="wcmb_vlist_center_lat" value=""/>
            <input type="hidden" id="wcmb_vlist_center_lng" name="wcmb_vlist_center_lng" value=""/>
            <div class="wcmb-store-map-filter">
                <div class="wcmb-inp-wrap">
                    <input type="text" name="locationText" id="locationText" placeholder="<?php _e('Enter Address', 'MB-multivendor'); ?>" value="<?php echo isset($request['locationText']) ? $request['locationText'] : ''; ?>">
                </div>
                <div class="wcmb-inp-wrap">
                    <select name="radiusSelect" id="radiusSelect">
                        <option value=""><?php _e('Within', 'MB-multivendor'); ?></option>
                        <?php if($radius) :
                        $selected_radius = isset($request['radiusSelect']) ? $request['radiusSelect'] : '';
                        foreach ($radius as $value) {
                            echo '<option value="'.$value.'" '.selected( esc_attr( $selected_radius ), $value, false ).'>'.$value.'</option>';
                        }
                        endif;
                        ?>
                    </select>
                </div>
                <div class="wcmb-inp-wrap">
                    <select name="distanceSelect" id="distanceSelect">
                        <?php $selected_distance = isset($request['distanceSelect']) ? $request['distanceSelect'] : ''; ?>
                        <option value="M" <?php echo selected( $selected_distance, "M", false ); ?>><?php _e('Miles', 'MB-multivendor'); ?></option>
                        <option value="K" <?php echo selected( $selected_distance, "K", false ); ?>><?php _e('Kilometers', 'MB-multivendor'); ?></option>
                        <option value="N" <?php echo selected( $selected_distance, "N", false ); ?>><?php _e('Nautical miles', 'MB-multivendor'); ?></option>
                        <?php do_action('wcmb_vendor_list_sort_distanceSelect_extra_options'); ?>
                    </select>
                </div>
                <?php do_action( 'wcmb_vendor_list_vendor_sort_map_extra_filters', $request ); ?>
                <input type="submit" name="vendorListFilter" value="<?php _e('Submit', 'MB-multivendor'); ?>">
            </div>
        </form>
        <?php endif; ?>
        <div class="wcmb-store-map-pagination">
            <p class="wcmb-pagination-count wcmb-pull-right">
                <?php
                if ( $vendor_total <= $per_page || -1 === $per_page ) {
                        /* translators: %d: total results */
                        printf( _n( 'Viewing the single vendor', 'Viewing all %d vendors', $vendor_total, 'MB-multivendor' ), $vendor_total );
                } else {
                        $first = ( $per_page * $current ) - $per_page + 1;
                        if(!apply_filters('wcmb_vendor_list_ignore_pagination', false)) {
                            $last  = min( $vendor_total, $per_page * $current );
                        }else{
                            $last  = $vendor_total;
                        }
                        /* translators: 1: first result 2: last result 3: total results */
                        printf( _nx( 'Viewing the single vendor', 'Viewing %1$d&ndash;%2$d of %3$d vendors', $vendor_total, 'with first and last result', 'MB-multivendor' ), $first, $last, $vendor_total );
                }
                ?>
            </p>
            
            <form name="vendor_sort" method="post" >
                <div class="vendor_sort">
                    <select class="select short" id="vendor_sort_type" name="vendor_sort_type">
                        <?php
                        $vendor_sort_type = apply_filters('wcmb_vendor_list_vendor_sort_type', array(
                            'registered' => __('By date', 'MB-multivendor'),
                            'name' => __('By Alphabetically', 'MB-multivendor'),
                            'category' => __('By Category', 'MB-multivendor'),
                        ));
                        if ($vendor_sort_type && is_array($vendor_sort_type)) {
                            foreach ($vendor_sort_type as $key => $label) {
                                $selected = '';
                                if (isset($request['vendor_sort_type']) && $request['vendor_sort_type'] == $key) {
                                    $selected = 'selected="selected"';
                                }
                                echo '<option value="' . $key . '" ' . $selected . '>' . $label . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <?php
                    $product_category = get_terms('product_cat');
                    $options_html = '';
                    $sort_category = isset($request['vendor_sort_category']) ? $request['vendor_sort_category'] : '';
                    foreach ($product_category as $category) {
                        if ($category->term_id == $sort_category) {
                            $options_html .= '<option value="' . esc_attr($category->term_id) . '" selected="selected">' . esc_html($category->name) . '</option>';
                        } else {
                            $options_html .= '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
                        }
                    }
                    ?>
                    <select name="vendor_sort_category" id="vendor_sort_category" class="select"><?php echo $options_html; ?></select>
                    <?php do_action( 'wcmb_vendor_list_vendor_sort_extra_attributes', $request ); ?>
                    <input value="<?php echo __('Sort', 'MB-multivendor'); ?>" type="submit">
                </div>
            </form>

        </div>
    </div>
    <!-- Map End -->

    <div class="wcmb-store-list-wrap">
        <?php
        if ($vendors && is_array($vendors)) {
            foreach ($vendors as $vendor_id) {
                $vendor = get_wcmb_vendor($vendor_id);
                $image = $vendor->get_image() ? $vendor->get_image('image', array(125, 125)) : $WCMb->plugin_url . 'assets/images/WP-stdavatar.png';
                $banner = $vendor->get_image('banner') ? $vendor->get_image('banner') : '';
                ?>

                <div class="wcmb-store-list">
                    <?php do_action('wcmb_vendor_lists_single_before_image', $vendor->term_id, $vendor->id); ?>
                    <div class="wcmb-profile-wrap">
                        <div class="wcmb-cover-picture" style="background-image: url('<?php if($banner) echo $banner; ?>');"></div>
                        <div class="store-badge-wrap">
                            <?php do_action('wcmb_vendor_lists_vendor_store_badges', $vendor); ?>
                        </div>
                        <div class="wcmb-store-info">
                            <div class="wcmb-store-picture">
                                <img class="vendor_img" src="<?php echo $image; ?>" id="vendor_image_display">
                            </div>
                            <?php
                                $rating_info = wcmb_get_vendor_review_info($vendor->term_id);
                                $WCMb->template->get_template('review/rating_vendor_lists.php', array('rating_val_array' => $rating_info));
                            ?>
                        </div>
                    </div>
                    <?php do_action('wcmb_vendor_lists_single_after_image', $vendor->term_id, $vendor->id); ?>
                    <div class="wcmb-store-detail-wrap">
                        <?php do_action('wcmb_vendor_lists_vendor_before_store_details', $vendor); ?>
                        <ul class="wcmb-store-detail-list">
                            <li>
                                <i class="wcmb-font ico-store-icon"></i>
                                <?php $button_text = apply_filters('wcmb_vendor_lists_single_button_text', $vendor->page_title); ?>
                                <a href="<?php echo $vendor->get_permalink(); ?>" class="store-name"><?php echo $button_text; ?></a>
                                <?php do_action('wcmb_vendor_lists_single_after_button', $vendor->term_id, $vendor->id); ?>
                                <?php do_action('wcmb_vendor_lists_vendor_after_title', $vendor); ?>
                            </li>
                            <?php if($vendor->get_formatted_address()) : ?>
                            <li>
                                <i class="wcmb-font ico-location-icon2"></i>
                                <p><?php echo $vendor->get_formatted_address(); ?></p>
                            </li>
                            <?php endif; ?>
                        </ul>
                        <?php do_action('wcmb_vendor_lists_vendor_after_store_details', $vendor); ?>
                    </div>
                </div>
                <?php
            }
        } else {
            _e('No vendor found!', 'MB-multivendor');
        }
        ?>
    </div>
    <!-- pagination --> 
    <?php if(!apply_filters('wcmb_vendor_list_ignore_pagination', false)) : ?>
    <div class="wcmb-pagination">
        <?php
            echo paginate_links( apply_filters( 'wcmb_vendor_list_pagination_args', array( 
                    'base'         => $base,
                    'format'       => $format,
                    'add_args'     => false,
                    'current'      => max( 1, $current ),
                    'total'        => $total,
                    'prev_text'    => 'Prev',
                    'next_text'    => 'Next',
                    'type'         => 'list',
                    'end_size'     => 3,
                    'mid_size'     => 3,
            ) ) );
	?>
    </div>
    <?php endif; ?>
</div> 
<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WCMb_Shortcode_Vendor_List')) {

    class WCMb_Shortcode_Vendor_List {
        
        /**
         * Filter vendor list
         * @global object $wcmb
         * @param string $orderby
         * @param string $order
         * @param string $product_category
         * @return array
         */
        public static function get_vendors_query($args, $request = array(), $ignore_pagi = false) {
            $block_vendors = wp_list_pluck(wcmb_get_all_blocked_vendors(), 'id');
            $include_vendors = array();
            $default = array (
                'role'      => 'dc_vendor',
                'fields'    => 'ids',
                'exclude'   => $block_vendors,
                'orderby'   => $args['orderby'],
                'order'     => $args['order'],
                'number'    => 12,
            );
            if (isset($request['vendor_sort_type']) && $request['vendor_sort_type'] == 'category' && isset($request['vendor_sort_category'])) {
                $pro_args = array(
                    'posts_per_page' => -1,
                    'post_type' => 'product',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'product_cat',
                            'field' => 'term_id',
                            'terms' => absint($request['vendor_sort_category'])
                        )
                    )
                );
                $products = get_posts($pro_args);
                $product_ids = wp_list_pluck($products, 'ID');
                foreach ($product_ids as $product_id) {
                    $vendor = get_wcmb_product_vendors($product_id);
                    if ($vendor && !in_array($vendor->id, $block_vendors)) {
                        $include_vendors[] = $vendor->id;
                    }
                }
                //
                $args['include'] = $include_vendors;
                $vendor_args = wp_parse_args($args, $default);
            } else {
                $vendor_args = wp_parse_args($args, $default);
            }
            if(get_query_var('paged')) :
                $current_page = max( 1, get_query_var('paged') );
                $offset = ($current_page - 1) * $vendor_args['number'];
                $vendor_args['offset'] = $offset;
            endif;
            
            if(!$ignore_pagi){
                $vendors_query = new WP_User_Query( $vendor_args );
            }else{
                unset($vendor_args['offset']);
                $vendor_args['number'] = -1;
                $vendors_query = new WP_User_Query( $vendor_args );
            }
            
            return $vendors_query;
            
        }

        /**
         * Output vendor list shortcode
         * @global object $wcmb
         * @param array $atts
         */
        public static function output($atts) {
            global $WCMb;
            $frontend_assets_path = $WCMb->plugin_url . 'assets/frontend/';
            $frontend_assets_path = str_replace(array('http:', 'https:'), '', $frontend_assets_path);
            $suffix = defined('WCMB_SCRIPT_DEBUG') && WCMB_SCRIPT_DEBUG ? '' : '.min';
            $WCMb->library->load_gmap_api();
            wp_register_style('wcmb_vendor_list', $frontend_assets_path . 'css/vendor-list.css', array(), $WCMb->version);
            wp_register_script('wcmb_vendor_list', $frontend_assets_path . 'js/vendor-list.js', array('jquery','wcmb-gmaps-api'), $WCMb->version, true);
            
            wp_enqueue_script('frontend_js');
            wp_enqueue_script('wcmb_vendor_list');
            
            wp_enqueue_style('wcmb_vendor_list');
            extract(shortcode_atts(array('orderby' => 'registered', 'order' => 'ASC'), $atts));
            $order_by = isset($_REQUEST['vendor_sort_type']) ? $_REQUEST['vendor_sort_type'] : $orderby;
            
            $query = apply_filters('wcmb_vendor_list_vendors_query_args', array(
                'number' => 12,
                'orderby' => $order_by, 
                'order' => $order,
            ));
            if($order_by == 'name'){
                $query['meta_key'] = '_vendor_page_title';
                $query['orderby'] = 'meta_value';
            }
            // backward supports
            $query = apply_filters('wcmb_vendor_list_get_wcmb_vendors_args', $query, $order_by, $_REQUEST, $atts);

            $vendors_query = self::get_vendors_query($query, $_REQUEST, apply_filters('wcmb_vendor_list_ignore_pagination', false));
            $vendors = $vendors_query->get_results();
            $vendors_total = $vendors_query->get_total();
            if(isset($_REQUEST['wcmb_vlist_center_lat']) && isset($_REQUEST['wcmb_vlist_center_lng'])){
                $vendors_query_all = self::get_vendors_query($query, $_REQUEST, true);
                $vendors = $vendors_query_all->get_results();
            }
            // map data
            $listed_stores = wcmb_get_vendor_list_map_store_data($vendors, $_REQUEST);

            if(isset($_REQUEST['wcmb_vlist_center_lat']) && isset($_REQUEST['wcmb_vlist_center_lng'])){
                $vendors = $listed_stores['vendors'];
                $vendors_total = count($listed_stores['vendors']);
            }

            $script_param = array(
                'stores'    => $listed_stores['stores'],
                'lang'      => array(
                    'geolocation_service_failed' => __('Error: The Geolocation service failed.', 'MB-multivendor'),
                    'geolocation_permission_denied' => __('Error: User denied the request for Geolocation.', 'MB-multivendor'),
                    'geolocation_position_unavailable' => __('Error: Location information is unavailable.', 'MB-multivendor'),
                    'geolocation_timeout' => __('Error: The request to get user location timed out.', 'MB-multivendor'),
                    'geolocation_doesnt_support' => __('Error: Your browser does not support geolocation.', 'MB-multivendor'),
                ),
                'map_data' => array(
                    'map_options' => array('zoom' => 10, 'mapTypeControlOptions' => array('mapTypeIds' => array('roadmap', 'satellite'))),
                    'marker_icon' => $WCMb->plugin_url . 'assets/images/store-marker.png',
                    'map_style' => true,
                    'map_style_data'  => array(
                        array(
                            'stylers' => array(
                                array('saturation' => -100),
                                array('gamma' => 1),
                            ),
                        ),
                        array(
                            'featureType' => 'water',
                            'stylers' => array(
                                array('lightness' => -12)
                            )
                        ),
                    ),
                    'map_style_title' => __('Styled Map', 'MB-multivendor'),
                ),
                'autocomplete' => true,
            );
            $WCMb->localize_script('wcmb_vendor_list', apply_filters('wcmb_vendor_list_script_data_params',$script_param, $_REQUEST));
            $radius = apply_filters('wcmb_vendor_list_filter_radius_data', array(5,10,20,30,50));
            $data = apply_filters('wcmb_vendor_list_data', array(
                'total'   => ceil($vendors_total/$query['number']),
                'current' => max( 1, get_query_var('paged') ),
                'per_page' => $query['number'],
                'base'    => get_pagenum_link(1) . '%_%',
                'format'  => 'page/%#%/',
                'vendors'   => $vendors,
                'vendor_total' => $vendors_total,
                'radius' => $radius,
                'request' => $_REQUEST,
            ));
            $WCMb->template->get_template('shortcode/vendor_lists.php', $data);
        }
    }

}
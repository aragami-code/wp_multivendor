<?php

if (!defined('ABSPATH'))
    exit;

/**

 */
class WCMb_Taxonomy {

    public $taxonomy_name;
    public $taxonomy_slug;
    public $wcmb_spmv_taxonomy;
    public $wcmb_gtin_taxonomy;

    public function __construct() {
        $permalinks = get_option('dc_vendors_permalinks');
        $this->taxonomy_name = 'dc_vendor_shop';
        $this->taxonomy_slug = empty($permalinks['vendor_shop_base']) ? _x('vendor', 'slug', 'MB-multivendor') : $permalinks['vendor_shop_base'];
        $this->register_post_taxonomy();
        //add_action('created_term', array($this, 'created_term'), 10, 3);
        add_filter('get_the_terms', array(&$this, 'wcmb_get_the_terms'), 10, 3);
        // register wcmb single product multiple vendors (SPMV) taxonomy
        $this->init_wcmb_spmv_taxonomy();
        // register wcmb GTIN
        if (get_wcmb_vendor_settings('is_gtin_enable', 'general') == 'Enable') {
            $this->init_wcmb_gtin_taxonomy();
        }
    }

    /**
     * Register wcmb taxonomy
     *
     * @author WC Marketplace
     * @access private
     * @package wcmb
     */
    public function register_post_taxonomy() {
        $labels = array(
            'name' => apply_filters('wcmb_vendor_taxonomy_name', __('Vendor', 'MB-multivendor')),
            'singular_name' => __('Vendor', 'MB-multivendor'),
            'menu_name' => __('Vendors', 'MB-multivendor'),
            'search_items' => __('Search Vendors', 'MB-multivendor'),
            'all_items' => __('All Vendors', 'MB-multivendor'),
            'parent_item' => __('Parent Vendor', 'MB-multivendor'),
            'parent_item_colon' => __('Parent Vendor:', 'MB-multivendor'),
            'view_item' => __('View Vendor', 'MB-multivendor'),
            'edit_item' => __('Edit Vendor', 'MB-multivendor'),
            'update_item' => __('Update Vendor', 'MB-multivendor'),
            'add_new_item' => __('Add New Vendor', 'MB-multivendor'),
            'new_item_name' => __('New Vendor Name', 'MB-multivendor'),
            'popular_items' => __('Popular Vendors', 'MB-multivendor'),
            'separate_items_with_commas' => __('Separate vendors with commas', 'MB-multivendor'),
            'add_or_remove_items' => __('Add or remove vendors', 'MB-multivendor'),
            'choose_from_most_used' => __('Choose from most used vendors', 'MB-multivendor'),
            'not_found' => __('No vendors found', 'MB-multivendor'),
        );

        $vendor_slug = apply_filters('wcmb_vendor_slug', $this->taxonomy_slug);

        $args = array(
            'public' => true,
            'hierarchical' => false,
            'rewrite' => array('slug' => $vendor_slug),
            'show_admin_column' => true,
            'show_ui' => false,
            'labels' => $labels
        );
        register_taxonomy($this->taxonomy_name, 'product', $args);
    }

    /**
     * Function created_term
     */
    function created_term($term_id, $tt_id, $taxonomy) {
        if ($taxonomy == $this->taxonomy_name) {
            $term = get_term_by('id', $term_id, $this->taxonomy_name, 'ARRAY_A');
            $random_password = wp_generate_password(12);
            $unique_username = $this->generate_unique_username($term['name']);
            $user_id = wp_create_user($unique_username, $random_password);
            if (!is_wp_error($user_id)) {
                $user = new WP_User($user_id);
                $user->set_role('dc_vendor');
            }
        }
    }

    /**
     * Function generate_unique_username
     */
    function generate_unique_username($term_name, $count = '') {
        if (!username_exists($term_name . $count)) {
            return $term_name . $count;
        }

        $count = ( $count == '' ) ? 1 : absint($count) + 1;

        $this->generate_unique_username($term_name, $count);
    }
    /**
     * Prevent term display in woocommerce product page if not vendor
     * 
     * @param array of WP_Term $terms
     * @param int $post_id
     * @param string $taxonomy
     * @return array of WP_Term
     */
    public function wcmb_get_the_terms($terms, $post_id, $taxonomy) {
        if ($taxonomy == $this->taxonomy_name && get_post_type($post_id) == 'product' && $terms) {
            foreach ($terms as $index => $term) {
                $term_id = $term->term_id;
                $vendor = get_wcmb_vendor_by_term($term_id);
                if (!$vendor) {
                    unset($terms[$index]);
                }
            }
        }
        return $terms;
    }
    
    public function init_wcmb_spmv_taxonomy(){
        // register wcmb single product multiple vendors (SPMV) taxonomy
        $this->wcmb_spmv_taxonomy = apply_filters('wcmb_spmv_taxonomy_slug', 'wcmb_spmv');
        register_taxonomy(
            $this->wcmb_spmv_taxonomy,
            'product',
            array(
                'label' => __( 'WCMb SPMV', 'MB-multivendor' ),
                'public' => false,
                'rewrite' => false,
                'hierarchical' => false,
                'show_admin_column' => false,
                'show_ui' => false,
            )
        );
        
        // Add default spmv terms
        $wcmb_spmv_default_terms = apply_filters('wcmb_spmv_default_terms', array(
            'min-price' => array(
                'label'=> __('Min Price', 'MB-multivendor'), 
                'description' => __('Used for minimum price products under all single product multi vendor concept.', 'MB-multivendor'),
            ),
            'max-price' => array(
                'label'=> __('Max Price', 'MB-multivendor'), 
                'description' => __('Used for maximum price products under all single product multi vendor concept.', 'MB-multivendor'),
            ),
            'top-rated-vendor' => array(
                'label'=> __('Top rated vendor', 'MB-multivendor'), 
                'description' => __('Used for top rated vendor products under all single product multi vendor concept.', 'MB-multivendor'),
            ),
        ));
        
        if( $wcmb_spmv_default_terms ) :
            foreach ($wcmb_spmv_default_terms as $slug => $term_data) {
                $name = isset($term_data['label']) ? $term_data['label'] : $slug;
                $desc = isset($term_data['description']) ? $term_data['description'] : '';
                $term = term_exists( $name, $this->wcmb_spmv_taxonomy );
                
                if ( 0 === $term || NULL === $term ) { 
                    wp_insert_term(
                        $name, // the term 
                        $this->wcmb_spmv_taxonomy, // the taxonomy
                        array(
                            'description'=> $desc,
                            'slug' => $slug,
                            'parent'=> $term 
                        )
                    );
                }
            }
        endif;
    }
    
    public function init_wcmb_gtin_taxonomy(){
        // register GTIN taxonomy
        $this->wcmb_gtin_taxonomy = apply_filters('wcmb_gtin_taxonomy_slug', 'wcmb_gtin');
        register_taxonomy(
            $this->wcmb_gtin_taxonomy,
            'product',
            array(
                'label' => apply_filters('wcmb_taxonomy_gtin_label_text',__( 'GTIN', 'MB-multivendor' )),
                'public' => false,
                'rewrite' => false,
                'hierarchical' => false,
                'show_admin_column' => false,
                'show_ui' => false,
            )
        );
        
        // Add default spmv terms
        $wcmb_gtin_default_terms = apply_filters('wcmb_gtin_default_terms', array(
            'upc'   => __( 'UPC', 'MB-multivendor' ),
            'ean'   => __( 'EAN', 'MB-multivendor' ),
            'isbn'  => __( 'ISBN', 'MB-multivendor' ),
            'mpuin' => apply_filters( 'wcmb_gtin_default_marketplace_unique_item_number_label', __( 'MPUIN', 'MB-multivendor' )),
            
        ));
        
        if( $wcmb_gtin_default_terms ) :
            foreach ($wcmb_gtin_default_terms as $slug => $label) {
                $name = isset($label) ? $label : $slug;
                $term = term_exists( $name, $this->wcmb_gtin_taxonomy );
                
                if ( 0 === $term || NULL === $term ) { 
                    wp_insert_term(
                        $name, // the term 
                        $this->wcmb_gtin_taxonomy, // the taxonomy
                        array(
                            'slug' => $slug,
                            'parent'=> $term 
                        )
                    );
                }
            }
        endif;
    }
    
    /**
     * Get SPMV terms
     * 
     * @param array $args
     * @return array of WP_Term
     */
    public function get_wcmb_spmv_terms($args = array()) {
        $default = array(
            'taxonomy' => $this->wcmb_spmv_taxonomy,
            'hide_empty' => false,
        );
        $args = wp_parse_args($args, $default);
        if( isset($args['taxonomy']) && $args['taxonomy'] != $this->wcmb_spmv_taxonomy )
            $args['taxonomy'] = $this->wcmb_spmv_taxonomy;
        $terms = get_terms( $args );
        return $terms;
    }
    
    /**
     * Get GTIN terms
     * 
     * @param array $args
     * @return array of WP_Term
     */
    public function get_wcmb_gtin_terms($args = array()) {
        $default = array(
            'taxonomy' => $this->wcmb_gtin_taxonomy,
            'hide_empty' => false,
        );
        $args = wp_parse_args($args, $default);
        if( isset($args['taxonomy']) && $args['taxonomy'] != $this->wcmb_gtin_taxonomy )
            $args['taxonomy'] = $this->wcmb_gtin_taxonomy;
        $terms = get_terms( $args );
        return $terms;
    }

}

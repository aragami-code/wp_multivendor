<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 
 */
class WCMb_Admin {

    public $settings;

    public function __construct() {
        // Admin script and style
        add_action('admin_enqueue_scripts', array(&$this, 'enqueue_admin_script'), 30);
        add_action('admin_bar_menu', array(&$this, 'add_toolbar_items'), 100);
        add_action('admin_head', array(&$this, 'admin_header'));
        add_action('current_screen', array($this, 'conditonal_includes'));
        add_action('delete_post', array($this, 'remove_commission_from_sales_report'), 10);
        add_action('trashed_post', array($this, 'remove_commission_from_sales_report'), 10);
        add_action('untrashed_post', array($this, 'restore_commission_from_sales_report'), 10);
        add_action('woocommerce_order_status_changed', array($this, 'change_commission_status'), 20, 3);
        if (get_wcmb_vendor_settings('is_singleproductmultiseller', 'general') == 'Enable') {
            add_action('admin_enqueue_scripts', array($this, 'wcmb_kill_auto_save'));
        }
        $this->load_class('settings');
        $this->settings = new WCMb_Settings();
        add_filter('woocommerce_hidden_order_itemmeta', array(&$this, 'add_hidden_order_items'));

        add_action('admin_menu', array(&$this, 'wcmb_admin_menu'));
        add_action('admin_head', array($this, 'menu_commission_count'));
        //if (!get_option('_is_dismiss_service_notice', false) && current_user_can('manage_options')) {
            //add_action('admin_notices', array(&$this, 'wcmb_service_page_notice'));
        //}
        add_action('wp_dashboard_setup', array(&$this, 'wcmb_remove_wp_dashboard_widget'));
        add_filter('woocommerce_order_actions', array(&$this, 'woocommerce_order_actions'));
        add_action('woocommerce_order_action_regenerate_order_commissions', array(&$this, 'regenerate_order_commissions'));
        add_filter('woocommerce_screen_ids', array(&$this, 'add_wcmb_screen_ids'));
        // Admin notice for advance frontend modules (Temp)
        add_action('admin_notices', array(&$this, 'advance_frontend_manager_notice'));
    }

    function add_hidden_order_items($order_items) {
        $order_items[] = '_give_tax_to_vendor';
        $order_items[] = '_give_shipping_to_vendor';
        // and so on...
        return $order_items;
    }

    public function change_commission_status($order_id, $old_status, $new_status) {
        global $wpdb;
        $myorder = get_post($order_id);
        $post_type = $myorder->post_type;
        if ($old_status == 'on-hold' || $old_status == 'pending' || $old_status == 'cancelled' || $old_status == 'refunded' || $old_status == 'failed') {
            if ($new_status == 'processing' || $new_status == 'completed') {
                if ($post_type == 'shop_order') {
                    $args = array(
                        'posts_per_page' => -1,
                        'offset' => 0,
                        'meta_key' => '_commission_order_id',
                        'meta_value' => $order_id,
                        'post_type' => 'dc_commission',
                        'post_status' => 'trash',
                        'suppress_filters' => true
                    );
                    $commission_array = get_posts($args);
                    foreach ($commission_array as $commission) {
                        $to_be_restore_commission = array();
                        $to_be_restore_commission['ID'] = $commission->ID;
                        $to_be_restore_commission['post_status'] = 'private';
                        wp_update_post($to_be_restore_commission);
                    }
                    $order_query = "update " . $wpdb->prefix . "wcmb_vendor_orders set 	is_trashed = '' where `order_id` = " . $order_id;
                    $wpdb->query($order_query);
                }
            }
        } elseif ($old_status == 'processing' || $old_status == 'completed') {
            if ($new_status == 'on-hold' || $new_status == 'pending' || $new_status == 'cancelled' || $new_status == 'refunded' || $new_status == 'failed') {
                if ($post_type == 'shop_order') {
                    $args = array(
                        'posts_per_page' => -1,
                        'offset' => 0,
                        'meta_key' => '_commission_order_id',
                        'meta_value' => $order_id,
                        'post_type' => 'dc_commission',
                        'post_status' => array('publish', 'private'),
                        'suppress_filters' => true
                    );
                    $commission_array = get_posts($args);
                    foreach ($commission_array as $commission) {
                        $to_be_deleted_commission = array();
                        $to_be_deleted_commission['ID'] = $commission->ID;
                        $to_be_deleted_commission['post_status'] = 'trash';
                        wp_update_post($to_be_deleted_commission);
                    }
                    $order_query = "update " . $wpdb->prefix . "wcmb_vendor_orders set 	is_trashed = '1' where `order_id` = " . $order_id;
                    $wpdb->query($order_query);
                }
            }
        }
    }

    public function remove_commission_from_sales_report($order_id) {
        global $wpdb;
        $order = get_post($order_id);
        $post_type = $order->post_type;
        if ($post_type == 'shop_order') {
            $args = array(
                'posts_per_page' => -1,
                'offset' => 0,
                'meta_key' => '_commission_order_id',
                'meta_value' => $order_id,
                'post_type' => 'dc_commission',
                'post_status' => array('publish', 'private'),
                'suppress_filters' => true
            );
            $commission_array = get_posts($args);
            foreach ($commission_array as $commission) {
                $to_be_deleted_commission = array();
                $to_be_deleted_commission['ID'] = $commission->ID;
                $to_be_deleted_commission['post_status'] = 'trash';
                wp_update_post($to_be_deleted_commission);
            }
            $order_query = "update " . $wpdb->prefix . "wcmb_vendor_orders set 	is_trashed = '1' where `order_id` = " . $order_id;
            $wpdb->query($order_query);
        }
    }

    public function restore_commission_from_sales_report($order_id) {
        global $wpdb;
        $myorder = get_post($order_id);
        $post_type = $myorder->post_type;
        if ($post_type == 'shop_order') {
            $args = array(
                'posts_per_page' => -1,
                'offset' => 0,
                'meta_key' => '_commission_order_id',
                'meta_value' => $order_id,
                'post_type' => 'dc_commission',
                'post_status' => 'trash',
                'suppress_filters' => true
            );
            $commission_array = get_posts($args);
            foreach ($commission_array as $commission) {
                $to_be_restore_commission = array();
                $to_be_restore_commission['ID'] = $commission->ID;
                $to_be_restore_commission['post_status'] = 'private';
                wp_update_post($to_be_restore_commission);
            }
            $order_query = "update " . $wpdb->prefix . "wcmb_vendor_orders set 	is_trashed = '' where `order_id` = " . $order_id;
            $wpdb->query($order_query);
        }
    }

    function conditonal_includes() {
        $screen = get_current_screen();

        if (in_array($screen->id, array('options-permalink'))) {
            $this->permalink_settings_init();
            $this->permalink_settings_save();
        }
    }

    function permalink_settings_init() {
        // Add our settings
        add_settings_field(
                'dc_product_vendor_taxonomy_slug', // id
                __('Vendor Shop Base', 'MB-multivendor'), // setting title
                array(&$this, 'wcmb_taxonomy_slug_input'), // display callback
                'permalink', // settings page
                'optional'                                      // settings section
        );
    }

    function wcmb_taxonomy_slug_input() {
        $permalinks = get_option('dc_vendors_permalinks');
        ?>
        <input name="dc_product_vendor_taxonomy_slug" type="text" class="regular-text code" value="<?php if (isset($permalinks['vendor_shop_base'])) echo esc_attr($permalinks['vendor_shop_base']); ?>" placeholder="<?php echo _x('vendor', 'slug', 'MB-multivendor') ?>" />
        <?php
    }

    function permalink_settings_save() {
        if (!is_admin()) {
            return;
        }
        // We need to save the options ourselves; settings api does not trigger save for the permalinks page
        if (isset($_POST['permalink_structure']) || isset($_POST['dc_product_vendor_taxonomy_slug'])) {

            // Cat and tag bases
            $dc_product_vendor_taxonomy_slug = wc_clean($_POST['dc_product_vendor_taxonomy_slug']);
            $permalinks = get_option('dc_vendors_permalinks');

            if (!$permalinks) {
                $permalinks = array();
            }

            $permalinks['vendor_shop_base'] = untrailingslashit($dc_product_vendor_taxonomy_slug);
            update_option('dc_vendors_permalinks', $permalinks);
        }
    }

    /**
     * Add Toolbar for vendor user 
     *
     * @access public
     * @param admin bar
     * @return void
     */
    function add_toolbar_items($admin_bar) {
        $user = wp_get_current_user();
        if (is_user_wcmb_vendor($user)) {
            $admin_bar->add_menu(
                    array(
                        'id' => 'vendor_dashboard',
                        'title' => __('Frontend  Dashboard', 'MB-multivendor'),
                        'href' => get_permalink(wcmb_vendor_dashboard_page_id()),
                        'meta' => array(
                            'title' => __('Frontend Dashboard', 'MB-multivendor'),
                            'target' => '_blank',
                            'class' => 'shop-settings'
                        ),
                    )
            );
            $admin_bar->add_menu(
                    array(
                        'id' => 'shop_settings',
                        'title' => __('Storefront', 'MB-multivendor'),
                        'href' => wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_store_settings_endpoint', 'vendor', 'general', 'storefront')),
                        'meta' => array(
                            'title' => __('Storefront', 'MB-multivendor'),
                            'target' => '_blank',
                            'class' => 'shop-settings'
                        ),
                    )
            );
        }
    }

    function load_class($class_name = '') {
        global $WCMb;
        if ('' != $class_name) {
            require_once ($WCMb->plugin_path . 'admin/class-' . esc_attr($WCMb->token) . '-' . esc_attr($class_name) . '.php');
        } // End If Statement
    }

// End load_class()

    /**
     * Add dualcube footer text on plugin settings page
     *
     * @access public
     * @param admin bar
     * @return void
     */
    function dualcube_admin_footer_for_wcmb() {
        global $WCMb;
        ?>
        <div style="clear: both"></div>
    
        <?php
    }

    /**
     * Add css on admin header
     *
     * @access public
     * @return void
     */
    function admin_header() {
        $screen = get_current_screen();
        if (is_user_logged_in()) {
            if (isset($screen->id) && in_array($screen->id, array('edit-dc_commission', 'edit-wcmb_university', 'edit-wcmb_vendor_notice'))) {
                ?>
                <script>
                    jQuery(document).ready(function ($) {
                        var target_ele = $(".wrap .wp-header-end");
                        var targethtml = target_ele.html();
                        //targethtml = targethtml + '<a href="<?php echo trailingslashit(get_admin_url()) . 'admin.php?page=wcmb-setting-admin'; ?>" class="page-title-action">Back To wcmb Settings</a>';
                        //target_ele.html(targethtml);
                <?php if (in_array($screen->id, array('edit-wcmb_university'))) { ?>
                            target_ele.before('<p><b><?php echo __('"Knowledgebase" section is visible only to vendors through the vendor dashboard. You may use this section to onboard your vendors. Share tutorials, best practices, "how to" guides or whatever you feel is appropriate with your vendors.', 'MB-multivendor'); ?></b></p>');
                <?php } ?>
                <?php if (in_array($screen->id, array('edit-wcmb_vendor_notice'))) { ?>
                            target_ele.before('<p><b><?php echo __('Announcements are visible only to vendors through the vendor dashboard(message section). You may use this section to broadcast your announcements.', 'MB-multivendor'); ?></b></p>');
                <?php } ?>
                    });

                </script>
                <?php
            }
        }
    }

    public function wcmb_admin_menu() {
        if (is_user_wcmb_vendor(get_current_vendor_id())) {
            remove_menu_page('edit.php');
            remove_menu_page('edit-comments.php');
            remove_menu_page('tools.php');
        }
    }

    public function menu_commission_count() {
        global $submenu;
        if (isset($submenu['wcmb'])) {
            if (apply_filters('wcmb_include_unpaid_commission_count_in_menu', true) && current_user_can('manage_woocommerce') && ( $order_count = wcmb_count_commission()->unpaid )) {
                foreach ($submenu['wcmb'] as $key => $menu_item) {
                    if (0 === strpos($menu_item[0], _x('Commissions', 'Admin menu name', 'wcmb'))) {
                        $submenu['wcmb'][$key][0] .= ' <span class="awaiting-mod update-plugins count-' . $order_count . '"><span class="processing-count">' . number_format_i18n($order_count) . '</span></span>';
                        break;
                    }
                }
            }
        }
    }

    /**
     * Admin Scripts
     */
    public function enqueue_admin_script() {
        global $WCMb;
        $screen = get_current_screen();
        $suffix = defined('WCMB_SCRIPT_DEBUG') && WCMB_SCRIPT_DEBUG ? '' : '.min';
        
        $wcmb_admin_screens = apply_filters('wcmb_enable_admin_script_screen_ids', array(
            'wcmb_page_wcmb-setting-admin',
            'wcmb_page_wcmb-to-do',
            'edit-wcmb_vendorrequest',
            'dc_commission',
            'woocommerce_page_wc-reports',
            'toplevel_page_wc-reports',
            'product',
            'edit-product',
            'user-edit',
            'profile',
            'users',
            'wcmb_page_wcmb-extensions',
            'wcmb_page_vendors',
            'toplevel_page_dc-vendor-shipping',
	));
        
        // Register scripts.
        wp_register_style('wcmb_admin_css', $WCMb->plugin_url . 'assets/admin/css/admin' . $suffix . '.css', array(), $WCMb->version);
        wp_register_script('wcmb_admin_js', $WCMb->plugin_url . 'assets/admin/js/admin' . $suffix . '.js', apply_filters('wcmb_admin_script_add_dependencies', array('jquery', 'jquery-ui-core', 'jquery-ui-tabs', 'wc-backbone-modal')), $WCMb->version, true);
        wp_register_script('dc_to_do_list_js', $WCMb->plugin_url . 'assets/admin/js/to_do_list' . $suffix . '.js', array('jquery'), $WCMb->version, true);
        wp_register_script('WCMb_chosen', $WCMb->plugin_url . 'assets/admin/js/chosen.jquery' . $suffix . '.js', array('jquery'), $WCMb->version, true);
        wp_register_script('WCMb_ajax-chosen', $WCMb->plugin_url . 'assets/admin/js/ajax-chosen.jquery' . $suffix . '.js', array('jquery', 'WCMb_chosen'), $WCMb->version, true);
        wp_register_script('wcmb-admin-commission-js', $WCMb->plugin_url . 'assets/admin/js/commission' . $suffix . '.js', array('jquery'), $WCMb->version, true);
        wp_register_script('wcmb-admin-product-js', $WCMb->plugin_url . 'assets/admin/js/product' . $suffix . '.js', array('jquery'), $WCMb->version, true);
        wp_register_script('edit_user_js', $WCMb->plugin_url . 'assets/admin/js/edit_user' . $suffix . '.js', array('jquery'), $WCMb->version, true);
        wp_register_script('dc_users_js', $WCMb->plugin_url . 'assets/admin/js/to_do_list' . $suffix . '.js', array('jquery'), $WCMb->version, true);
        wp_register_script('wcmb_admin_product_auto_search_js', $WCMb->plugin_url . 'assets/admin/js/admin-product-auto-search' . $suffix . '.js', array('jquery'), $WCMb->version, true);
        wp_register_script('wcmb_report_js', $WCMb->plugin_url . 'assets/admin/js/report' . $suffix . '.js', array('jquery'), $WCMb->version, true);
        wp_register_script('wcmb_vendor_js', $WCMb->plugin_url . 'assets/admin/js/vendor' . $suffix . '.js', array('jquery', 'woocommerce_admin'), $WCMb->version, true);
        wp_register_script('wcmb_vendor_shipping',$WCMb->plugin_url . 'assets/admin/js/vendor-shipping' . $suffix . '.js', array( 'jquery', 'wp-util', 'underscore', 'backbone', 'jquery-ui-sortable', 'wc-backbone-modal' ), $WCMb->version );

        $WCMb->localize_script('wcmb_admin_js', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'vendors_nonce' => wp_create_nonce('wcmb-vendors'),
            'lang'  => array(
                'in_percentage' => __('In Percentage', 'MB-multivendor'),
                'in_fixed' => __('In Fixed', 'MB-multivendor'),
            )
        ));
        if (in_array($screen->id, $wcmb_admin_screens)) :
            wp_enqueue_style( 'wcmb_admin_css' );
            wp_enqueue_script( 'wcmb_admin_js' );
        endif;
        // hide media list view access for vendor
        $user = wp_get_current_user();
        if(in_array('dc_vendor', $user->roles)){
            $custom_css = "
            .view-switch .view-list{
                    display: none;
            }";
            wp_add_inline_style( 'media-views', $custom_css );
        }
        // wcmb library
        if (in_array($screen->id, array('wcmb_page_wcmb-setting-admin', 'wcmb_page_wcmb-to-do'))) :
            $WCMb->library->load_qtip_lib();
            $WCMb->library->load_upload_lib();
            $WCMb->library->load_colorpicker_lib();
            $WCMb->library->load_datepicker_lib();
            wp_enqueue_script('wcmb_admin_js', $WCMb->plugin_url . 'assets/admin/js/admin' . $suffix . '.js', array('jquery', 'jquery-ui-core', 'jquery-ui-tabs'), $WCMb->version, true);
            wp_enqueue_style('wcmb_admin_css', $WCMb->plugin_url . 'assets/admin/css/admin' . $suffix . '.css', array(), $WCMb->version);
        endif;
        if (in_array($screen->id, array('wcmb_page_wcmb-to-do', 'edit-wcmb_vendorrequest'))) {
            wp_enqueue_script( 'dc_to_do_list_js' );
        }
        if (in_array($screen->id, array('wcmb_page_vendors'))) :
        	$WCMb->library->load_upload_lib();
	        wp_enqueue_script('wcmb_admin_js');
                wp_register_script('wc-country-select', WC()->plugin_url() . '/assets/js/frontend/country-select' . $suffix . '.js', array('jquery'), WC_VERSION);
                $params = array(
                        'countries'                 => wp_json_encode( array_merge( WC()->countries->get_allowed_country_states(), WC()->countries->get_shipping_country_states() ) ),
                        'i18n_select_state_text'    => esc_attr__( 'Select an option&hellip;', 'woocommerce' ),
                        'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'woocommerce' ),
                        'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'woocommerce' ),
                        'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce' ),
                        'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce' ),
                        'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce' ),
                        'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce' ),
                        'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce' ),
                        'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce' ),
                        'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce' ),
                        'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce' ),
                );
                wp_localize_script( 'wc-country-select', 'wc_country_select_params', $params );
                wp_enqueue_script( 'wc-country-select' );
                wp_register_script('wcmb_country_state_js', $WCMb->plugin_url . 'assets/frontend/js/wcmb-country-state.js', array('jquery', 'wc-country-select'), $WCMb->version, true);
                wp_enqueue_script( 'wcmb_country_state_js' );
            
        endif;

        if (in_array($screen->id, array('dc_commission', 'woocommerce_page_wc-reports', 'toplevel_page_wc-reports', 'product', 'edit-product'))) :
            $WCMb->library->load_qtip_lib();
            if (!wp_style_is('woocommerce_chosen_styles', 'queue')) {
                wp_enqueue_style('woocommerce_chosen_styles', $WCMb->plugin_url . '/assets/admin/css/chosen' . $suffix . '.css');
            }
            wp_enqueue_script('WCMb_chosen');
            wp_enqueue_script('WCMb_ajax-chosen');
            wp_enqueue_script('wcmb-admin-commission-js');
            wp_localize_script('wcmb-admin-commission-js', 'dc_vendor_object', array('security' => wp_create_nonce("search-products")));
            wp_enqueue_script('wcmb-admin-product-js');
            wp_localize_script('wcmb-admin-product-js', 'dc_vendor_object', array('security' => wp_create_nonce("search-products")));
            if (get_wcmb_vendor_settings('is_singleproductmultiseller', 'general') == 'Enable' && in_array($screen->id, array('product'))) {
                wp_enqueue_script('wcmb_admin_product_auto_search_js');
                wp_localize_script('wcmb_admin_product_auto_search_js', 'wcmb_admin_product_auto_search_js_params', array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'search_products_nonce' => wp_create_nonce('search-products'),
                ));
            }
        endif;

        if (in_array($screen->id, array('user-edit', 'profile'))) :
            $WCMb->library->load_qtip_lib();
            $WCMb->library->load_upload_lib();
            wp_enqueue_script('edit_user_js');
        endif;

        if (in_array($screen->id, array('users'))) :
            wp_enqueue_script('dc_users_js');
        endif;

        if (in_array($screen->id, array('woocommerce_page_wc-reports', 'toplevel_page_wc-reports'))) :
            wp_enqueue_script('WCMb_chosen');
            wp_enqueue_script('WCMb_ajax-chosen');
            wp_enqueue_script('wcmb-admin-product-js');
            wp_localize_script('wcmb-admin-product-js', 'dc_vendor_object', array('security' => wp_create_nonce("search-products")));
        endif;

        if (in_array($screen->id, array('woocommerce_page_wc-reports', 'toplevel_page_wc-reports'))) :
            wp_enqueue_script('wcmb_report_js');
        endif;

        if (is_user_wcmb_vendor(get_current_vendor_id())) {
            wp_enqueue_script('wcmb_vendor_js');
        }
        
        // hide coupon allow free shipping option for vendor
        if (is_user_wcmb_vendor(get_current_vendor_id())) {
            $custom_css = "
            #general_coupon_data .free_shipping_field{
                    display: none;
            }";
            wp_add_inline_style( 'woocommerce_admin_styles', $custom_css );
            wp_enqueue_script('wcmb_vendor_js');
        }
        
        // hide product cat from quick & bulk edit
        if(is_user_wcmb_vendor(get_current_vendor_id()) && in_array($screen->id, array('edit-product'))){
            $custom_css = "
            .inline-edit-product .inline-edit-categories, .bulk-edit-product .inline-edit-categories{
                display: none;
            }";
            wp_add_inline_style( 'woocommerce_admin_styles', $custom_css );
        }
    }

    function wcmb_kill_auto_save() {
        if ('product' == get_post_type()) {
            wp_dequeue_script('autosave');
        }
    }

    /**
     * Display wcmb service notice in admin panel
     */
    public function wcmb_service_page_notice() {
        ?>
        <div class="updated wcmb_admin_new_banner">
            <div class="round"></div>
            <div class="round1"></div>
            <div class="round2"></div>
            <div class="round3"></div>
            <div class="round4"></div>
            <div class="wcmb_banner-content">
               
               

            </div>
        </div>
        <style type="text/css">.clearfix{clear:both}.wcmb_admin_new_banner.updated{border-left:0}.wcmb_admin_new_banner{box-shadow:0 3px 1px 1px rgba(0,0,0,.2);padding:10px 30px;background:#fff;position:relative;overflow:hidden;clear:both;border-top:2px solid #8abee5;text-align:left;background-size:contain}.wcmb_admin_new_banner .round{width:200px;height:200px;position:absolute;border-radius:100%;border:30px solid rgba(157,42,255,.05);top:-150px;left:73px;z-index:1}.wcmb_admin_new_banner .round1{position:absolute;border-radius:100%;border:45px solid rgba(194,108,144,.05);bottom:-82px;right:-58px;width:180px;height:180px;z-index:1}.wcmb_admin_new_banner .round2,.wcmb_admin_new_banner .round3{border-radius:100%;width:180px;height:180px;position:absolute;z-index:1}.wcmb_admin_new_banner .round2{border:18px solid rgba(194,108,144,.05);top:35px;left:249px}.wcmb_admin_new_banner .round3{border:45px solid rgba(31,194,255,.05);top:2px;right:40%}.wcmb_admin_new_banner .round4{position:absolute;border-radius:100%;border:31px solid rgba(31,194,255,.05);top:11px;left:-49px;width:100px;height:100px;z-index:1}.wcmb_banner-content{display: -webkit-box;display: -moz-box;display: -ms-flexbox;display: -webkit-flex;display: flex;align-items:center}.wcmb_admin_new_banner .txt{color:#333;font-size:18px;line-height:1.4;width:calc(100% - 330px);position:relative;z-index:2;display:inline-block;font-weight:400;float:left;padding-left:8px}.wcmb_admin_new_banner .link,.wcmb_admin_new_banner .wcmb_btn_service_claim_now{font-weight:400;display:inline-block;z-index:2;padding:0 20px;position:relative}.wcmb_admin_new_banner .rightside{float:right;width:500px}.wcmb_admin_new_banner .wcmb_btn_service_claim_now{cursor:pointer;background:#8abee5;height:40px;color:#fff;font-size:20px;text-align:center;border:none;margin:5px 13px;border-radius:5px;text-decoration:none;line-height:40px}.wcmb_admin_new_banner button:hover{opacity:.8;transition:.5s}.wcmb_admin_new_banner .link{font-size:18px;line-height:49px;background:0 0;height:50px}.wcmb_admin_new_banner .link a{color:#333;text-decoration:none}@media (max-width:990px){.wcmb_admin_new_banner::before{left:-4%;top:-12%}}@media (max-width:767px){.wcmb_admin_new_banner::before{left:0;top:0;transform:rotate(0);width:10px}.wcmb_admin_new_banner .txt{width:400px;max-width:100%;text-align:center;padding:0;margin:0 auto 5px;float:none;display:block;font-size:17px;line-height:1.6}.wcmb_admin_new_banner .rightside{width:100%;padding-left:10px;text-align:center;box-sizing:border-box}.wcmb_admin_new_banner .wcmb_btn_service_claim_now{margin:10px 0}.wcmb_banner-content{display:block}}.wcmb_admin_new_banner button.notice-dismiss{z-index:1;position:absolute;top:50%;transform:translateY(-50%)}</style>
        <script type="text/javascript">
            function dismiss_servive_notice(e, i) {
                jQuery.post(ajaxurl, {action: "dismiss_wcmb_servive_notice"}, function (e) {
                    e && (jQuery(".wcmb_admin_new_banner").addClass("hidden"), void 0 !== i && (window.open(i, '_blank')))
                })
            }
        </script>
        <?php
    }

    /**
     * Remove wp dashboard widget for vendor
     * @global array $wp_meta_boxes
     */
    public function wcmb_remove_wp_dashboard_widget() {
        global $wp_meta_boxes;
        if (is_user_wcmb_vendor(get_current_vendor_id())) {
            unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
            unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
            unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
            unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
        }
    }

    public function woocommerce_order_actions($actions) {
        $actions['regenerate_order_commissions'] = __('Regenerate order commissions', 'MB-multivendor');
        return $actions;
    }

    /**
     * Regenerate order commissions
     * @param Object $order
     * @since 3.0.2
     */
    public function regenerate_order_commissions($order) {
        global $wpdb, $WCMb;
        if (!in_array($order->get_status(), $WCMb->commission->completed_statuses)) {
            return;
        }
        $table_name = $wpdb->prefix . 'wcmb_vendor_orders';
        delete_post_meta($order->get_id(), '_commissions_processed');
        delete_post_meta($order->get_id(), '_wcmb_order_processed');
        $commission_ids = get_post_meta($order->get_id(), '_commission_ids', true) ? get_post_meta($order->get_id(), '_commission_ids', true) : array();
        if ($commission_ids && is_array($commission_ids)) {
            foreach ($commission_ids as $commission_id) {
                wp_delete_post($commission_id, true);
            }
        }
        delete_post_meta($order->get_id(), '_commission_ids');
        $wpdb->delete($table_name, array('order_id' => $order->get_id()), array('%d'));
        $WCMb->commission->wcmb_process_commissions($order->get_id());
    }
    
    public function add_wcmb_screen_ids($screen_ids){
        $screen_ids[] = 'toplevel_page_dc-vendor-shipping';
        return $screen_ids;
    }
    
    public function advance_frontend_manager_notice(){
        if(!class_exists('WCMb_AFM') && WC_Dependencies_Product_Vendor::is_advance_frontend_manager_active()) :
        ?>
        <div id="message" class="error settings-error notice is-dismissible">
            <p><?php printf(__('%sAdvance Frontend Manager%s will not work with latest WCMb (v%s), so please update Advance Frontend Manager with latest one (v3.0.0).', 'MB-multivendor'
), '<strong>', '</strong>', WCMb_PLUGIN_VERSION); ?></p>
        </div>
        <?php 
        endif;
    }

}

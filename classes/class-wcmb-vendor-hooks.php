<?php

/**
 * 
 */
class WCMb_Vendor_Hooks {

    function __construct() {
        add_action( 'wcmb_vendor_dashboard_navigation', array( &$this, 'wcmb_create_vendor_dashboard_navigation' ) );
        add_action( 'wcmb_vendor_dashboard_content', array( &$this, 'wcmb_create_vendor_dashboard_content' ) );
        add_action( 'before_wcmb_vendor_dashboard', array( &$this, 'save_vendor_dashboard_data' ) );

        add_action( 'wcmb_vendor_dashboard_vendor-announcements_endpoint', array( &$this, 'wcmb_vendor_dashboard_vendor_announcements_endpoint' ) );
        add_action( 'wcmb_vendor_dashboard_vendor-orders_endpoint', array( &$this, 'wcmb_vendor_dashboard_vendor_orders_endpoint' ) );
        add_action( 'wcmb_vendor_dashboard_storefront_endpoint', array( &$this, 'wcmb_vendor_dashboard_storefront_endpoint' ) );
        add_action( 'wcmb_vendor_dashboard_profile_endpoint', array( &$this, 'wcmb_vendor_dashboard_profile_endpoint' ) );
        add_action( 'wcmb_vendor_dashboard_vendor-policies_endpoint', array( &$this, 'wcmb_vendor_dashboard_vendor_policies_endpoint' ) );
        add_action( 'wcmb_vendor_dashboard_vendor-billing_endpoint', array( &$this, 'wcmb_vendor_dashboard_vendor_billing_endpoint' ) );
        add_action( 'wcmb_vendor_dashboard_vendor-shipping_endpoint', array( &$this, 'wcmb_vendor_dashboard_vendor_shipping_endpoint' ) );
        add_action( 'wcmb_vendor_dashboard_vendor-report_endpoint', array( &$this, 'wcmb_vendor_dashboard_vendor_report_endpoint' ) );

        add_action( 'wcmb_vendor_dashboard_add-product_endpoint', array( &$this, 'wcmb_vendor_dashboard_add_product_endpoint' ) );
        add_action( 'wcmb_vendor_dashboard_edit-product_endpoint', array( &$this, 'wcmb_vendor_dashboard_edit_product_endpoint' ) );
        add_action( 'wcmb_vendor_dashboard_products_endpoint', array( &$this, 'wcmb_vendor_dashboard_products_endpoint' ) );
        add_action( 'wcmb_vendor_dashboard_add-coupon_endpoint', array( &$this, 'wcmb_vendor_dashboard_add_coupon_endpoint' ) );
        add_action( 'wcmb_vendor_dashboard_coupons_endpoint', array( &$this, 'wcmb_vendor_dashboard_coupons_endpoint' ) );

        add_action( 'wcmb_vendor_dashboard_vendor-withdrawal_endpoint', array( &$this, 'wcmb_vendor_dashboard_vendor_withdrawal_endpoint' ) );
        add_action( 'wcmb_vendor_dashboard_transaction-details_endpoint', array( &$this, 'wcmb_vendor_dashboard_transaction_details_endpoint' ) );
        //add_action( 'wcmb_vendor_dashboard_vendor-knowledgebase_endpoint', array( &$this, 'wcmb_vendor_dashboard_vendor_knowledgebase_endpoint' ) );
        add_action( 'wcmb_vendor_dashboard_vendor-tools_endpoint', array( &$this, 'wcmb_vendor_dashboard_vendor_tools_endpoint' ) );
        add_action( 'wcmb_vendor_dashboard_products-qna_endpoint', array( &$this, 'wcmb_vendor_dashboard_products_qna_endpoint' ) );

        add_filter( 'the_title', array( &$this, 'wcmb_vendor_dashboard_endpoint_title' ) );
        add_filter( 'wcmb_vendor_dashboard_menu_vendor_policies_capability', array( &$this, 'wcmb_vendor_dashboard_menu_vendor_policies_capability' ) );
        add_filter( 'wcmb_vendor_dashboard_menu_vendor_withdrawal_capability', array( &$this, 'wcmb_vendor_dashboard_menu_vendor_withdrawal_capability' ) );
        add_filter( 'wcmb_vendor_dashboard_menu_vendor_shipping_capability', array( &$this, 'wcmb_vendor_dashboard_menu_vendor_shipping_capability' ) );
        add_action( 'before_wcmb_vendor_dashboard_content', array( &$this, 'before_wcmb_vendor_dashboard_content' ) );
        add_action( 'wp', array( &$this, 'wcmb_add_theme_support' ), 15 );
        
        // Rejected vendor dashboard content
        add_action( 'wcmb_rejected_vendor_dashboard_content', array( &$this, 'rejected_vendor_dashboard_content' ) );
        add_action( 'before_wcmb_rejected_vendor_dashboard', array( &$this, 'save_rejected_vendor_reapply_data' ) );
    }

    /**
     * Create vendor dashboard menu
     * array $args
     */
    public function wcmb_create_vendor_dashboard_navigation( $args = array() ) {
        global $WCMb;
        $WCMb->template->get_template( 'vendor-dashboard/navigation.php', array( 'nav_items' => $this->wcmb_get_vendor_dashboard_navigation(), 'args' => $args ) );
    }

    public function wcmb_get_vendor_dashboard_navigation() {
        $vendor_nav = array(
        /*    'dashboard'            => array(
                'label'       => __( 'Dashboard', 'MB-multivendor' )
                , 'url'         => wcmb_get_vendor_dashboard_endpoint_url( 'dashboard' )
                , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_dashboard_capability', true )
                , 'position'    => 0
                , 'submenu'     => array()
                , 'link_target' => '_self'
                , 'nav_icon'    => 'wcmb-font ico-dashboard-icon'
            ),
            'store-settings'       => array(
                'label'       => __( 'Store Settings', 'MB-multivendor' )
                , 'url'         => '#'
                , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_store_settings_capability', true )
                , 'position'    => 10
                , 'submenu'     => array(
                    'storefront'      => array(
                        'label'       => __( 'Storefront', 'MB-multivendor' )
                        , 'url'         => wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_store_settings_endpoint', 'vendor', 'general', 'storefront' ) )
                        , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_shop_front_capability', true )
                        , 'position'    => 10
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'wcmb-font ico-storefront-icon'
                    ),
                    'vendor-policies' => array(
                        'label'       => __( 'Policies', 'MB-multivendor' )
                        , 'url'         => wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_vendor_policies_endpoint', 'vendor', 'general', 'vendor-policies' ) )
                        , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_vendor_policies_capability', false )
                        , 'position'    => 20
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'wcmb-font ico-policies-icon'
                    ),
                    'vendor-billing'  => array(
                        'label'       => __( 'Billing', 'MB-multivendor' )
                        , 'url'         => wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_vendor_billing_endpoint', 'vendor', 'general', 'vendor-billing' ) )
                        , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_vendor_billing_capability', true )
                        , 'position'    => 30
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'wcmb-font ico-billing-icon'
                    ),
                    'vendor-shipping' => array(
                        'label'       => __( 'Shipping', 'MB-multivendor' )
                        , 'url'         => wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_vendor_shipping_endpoint', 'vendor', 'general', 'vendor-shipping' ) )
                        , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_vendor_shipping_capability', wc_shipping_enabled() )
                        , 'position'    => 40
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'wcmb-font ico-shippingnew-icon'
                    )
                )
                , 'link_target' => '_self'
                , 'nav_icon'    => 'wcmb-font ico-store-settings-icon'
            ),
            'vendor-products'      => array(
                'label'       => __( 'Product Manager', 'MB-multivendor' )
                , 'url'         => '#'
                , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_vendor_products_capability', 'edit_products' )
                , 'position'    => 20
                , 'submenu'     => array(
                    'products'    => array(
                        'label'       => __( 'All Products', 'MB-multivendor' )
                        , 'url'         => apply_filters( 'wcmb_vendor_products', wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_products_endpoint', 'vendor', 'general', 'products' ) ) )
                        , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_products_capability', 'edit_products' )
                        , 'position'    => 10
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'wcmb-font ico-product-manager-icon'
                    ),
                    'add-product' => array(
                        'label'       => __( 'Add Product', 'MB-multivendor' )
                        , 'url'         => apply_filters( 'wcmb_vendor_dashboard_add_product_url', wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_add_product_endpoint', 'vendor', 'general', 'add-product' ) ) )
                        , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_add_product_capability', 'edit_products' )
                        , 'position'    => 20
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'wcmb-font ico-add-product-icon'
                    )
                )
                , 'link_target' => '_self'
                , 'nav_icon'    => 'wcmb-font ico-product-manager-icon'
            ),
            'vendor-promte'        => array(
                'label'       => __( 'Coupons', 'MB-multivendor' )
                , 'url'         => '#'
                , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_vendor_promte_capability', 'edit_shop_coupons' )
                , 'position'    => 30
                , 'submenu'     => array(
                    'coupons'    => array(
                        'label'       => __( 'All Coupons', 'MB-multivendor' )
                        , 'url'         => apply_filters( 'wcmb_vendor_coupons', wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_coupons_endpoint', 'vendor', 'general', 'coupons' ) ) )
                        , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_vendor_coupons_capability', 'edit_shop_coupons' )
                        , 'position'    => 10
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'wcmb-font ico-coupons-icon'
                    ),
                    'add-coupon' => array(
                        'label'       => __( 'Add Coupon', 'MB-multivendor' )
                        , 'url'         => apply_filters( 'wcmb_vendor_submit_coupon', wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_add_coupon_endpoint', 'vendor', 'general', 'add-coupon' ) ) )
                        , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_add_coupon_capability', 'edit_shop_coupons' )
                        , 'position'    => 20
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'wcmb-font ico-addcoupon-icon'
                    )
                )
                , 'link_target' => '_self'
                , 'nav_icon'    => 'wcmb-font ico-coupons-icon'
            ),
            'vendor-report'        => array(
                'label'       => __( 'Stats / Reports', 'MB-multivendor' )
                , 'url'         => '#'
                , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_vendor_report_capability', true )
                , 'position'    => 40
                , 'submenu'     => array(
                    'vendor-report' => array(
                        'label'       => __( 'Overview', 'MB-multivendor' )
                        , 'url'         => wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_vendor_report_endpoint', 'vendor', 'general', 'vendor-report' ) )
                        , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_vendor_report_capability', true )
                        , 'position'    => 10
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'wcmb-font ico-reports-icon'
                    )
                )
                , 'link_target' => '_self'
                , 'nav_icon'    => 'wcmb-font ico-reports-icon'
            ),
            'vendor-orders'        => array(
                'label'       => __( 'Orders', 'MB-multivendor' )
                , 'url'         => wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_vendor_orders_endpoint', 'vendor', 'general', 'vendor-orders' ) )
                , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_vendor_orders_capability', true )
                , 'position'    => 50
                , 'submenu'     => array()
                , 'link_target' => '_self'
                , 'nav_icon'    => 'wcmb-font ico-orders-icon'
            ),
            'vendor-payments'      => array(
                'label'       => __( 'Payments', 'MB-multivendor' )
                , 'url'         => '#'
                , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_vendor_payments_capability', true )
                , 'position'    => 60
                , 'submenu'     => array(
                    'vendor-withdrawal'   => array(
                        'label'       => __( 'Withdrawal', 'MB-multivendor' )
                        , 'url'         => wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_vendor_withdrawal_endpoint', 'vendor', 'general', 'vendor-withdrawal' ) )
                        , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_vendor_withdrawal_capability', false )
                        , 'position'    => 10
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'wcmb-font ico-revenue-icon'
                    ),
                    'transaction-details' => array(
                        'label'       => __( 'History', 'MB-multivendor' )
                        , 'url'         => wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_transaction_details_endpoint', 'vendor', 'general', 'transaction-details' ) )
                        , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_transaction_details_capability', true )
                        , 'position'    => 20
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'wcmb-font ico-history-icon'
                    )
                )
                , 'link_target' => '_self'
                , 'nav_icon'    => 'wcmb-font ico-payments-icon'
            ),
            //'vendor-knowledgebase' => array(
              //  'label'       => __( 'Knowledgebase', 'MB-multivendor' )
               // , 'url'         => wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_vendor_knowledgebase_endpoint', 'vendor', 'general', 'vendor-knowledgebase' ) )
               // , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_vendor_knowledgebase_capability', true )
               // , 'position'    => 70
              //  , 'submenu'     => array()
                //, 'link_target' => '_self'
                //, 'nav_icon'    => 'wcmb-font ico-knowledgebase-icon'
            //),
            'vendor-tools'         => array(
                'label'       => __( 'Tools', 'MB-multivendor' )
                , 'url'         => wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_vendor_tools_endpoint', 'vendor', 'general', 'vendor-tools' ) )
                , 'capability'  => apply_filters( 'wcmb_vendor_dashboard_menu_vendor_tools_capability', true )
                , 'position'    => 80
                , 'submenu'     => array()
                , 'link_target' => '_self'
                , 'nav_icon'    => 'wcmb-font ico-tools-icon'
            )
       */ );
        return apply_filters( 'wcmb_vendor_dashboard_nav', $vendor_nav );
    }

    /**
     * Display Vendor dashboard Content
     * @global object $wp
     * @global object $wcmb
     * @return null
     */
    public function wcmb_create_vendor_dashboard_content() {
        global $wp, $WCMb;
        foreach ( $wp->query_vars as $key => $value ) {
            // Ignore pagename and page param.
            if ( in_array( $key, array( 'page', 'pagename' ) ) ) {
                continue;
            }
            do_action( 'before_wcmb_vendor_dashboard_content', $key );
            if ( has_action( 'wcmb_vendor_dashboard_' . $key . '_endpoint' ) ) {
                if ( $this->current_vendor_can_view( $WCMb->endpoints->get_current_endpoint() ) ) {
                    do_action( 'wcmb_vendor_dashboard_' . $key . '_endpoint', $value );
                }
                return;
            }
            do_action( 'after_wcmb_vendor_dashboard_content' );
        }
        $WCMb->library->load_dataTable_lib();
        $WCMb->template->get_template( 'vendor-dashboard/dashboard.php' );
    }

    public function wcmb_create_vendor_dashboard_breadcrumbs( $current_endpoint, $nav = array(), $firstLevel = true ) {
        global $WCMb;
        $nav = ! empty( $nav ) ? $nav : $this->wcmb_get_vendor_dashboard_navigation();
        $resultArray = array();
        $current_endpoint = $current_endpoint ? $current_endpoint : 'dashboard';
        $breadcrumb = false;
        $curent_menu = array();
        if ( array_key_exists( $current_endpoint, $nav ) ) {
            $menu = $nav[$current_endpoint];
            $icon = isset($menu['nav_icon']) ? '<i class="' . $menu['nav_icon'] . '"></i>' : '';
            $breadcrumb = $icon . '<span> ' . $menu['label'] . '</span>';
            $curent_menu = $menu;
        } else {
            $submenus = wp_list_pluck( $nav, 'submenu' );
            foreach ( $submenus as $key => $submenu ) {
                if ( $submenu && array_key_exists( $current_endpoint, $submenu ) ) {
                    if ( ! $firstLevel ) {
                        $menu = $nav[$key];
                        $icon = isset($menu['nav_icon']) ? '<i class="' . $menu['nav_icon'] . '"></i>' : '';
                        $breadcrumb = $icon . '<span> ' . $menu['label'] . '</span>';
                        $subm = $submenu[$current_endpoint];
                        $subicon = isset($subm['nav_icon']) ? '<i class="' . $subm['nav_icon'] . '"></i>' : '';
                        $breadcrumb .= '&nbsp;<span class="bread-sepa"> ' . apply_filters( 'wcmb_vendor_dashboard_breadcrumbs_separator', '>' ) . ' </span>&nbsp;';
                        $breadcrumb .= $subicon . '<span> ' . $subm['label'] . '</span>';
                        $curent_menu = $subm;
                    } else {
                        $menu = $submenu[$current_endpoint];
                        $icon = isset($menu['nav_icon']) ? '<i class="' . $menu['nav_icon'] . '"></i>' : '';
                        $breadcrumb = $icon . '<span> ' . $menu['label'] . '</span>';
                        $curent_menu = $menu;
                    }
                    break;
                } else {
                    $current_endpoint_arr = isset($WCMb->endpoints->wcmb_query_vars[$current_endpoint]) ? $WCMb->endpoints->wcmb_query_vars[$current_endpoint] : array();
                    $icon = isset($current_endpoint_arr['icon']) ? '<i class="' . $current_endpoint_arr['icon'] . '"></i>' : '';
                    $breadcrumb = $icon . '<span> ' . $current_endpoint_arr['label'] . '</span>';
                    $curent_menu = $current_endpoint_arr;
                }
            }
        }
        return apply_filters( 'wcmb_create_vendor_dashboard_breadcrumbs', $breadcrumb, $curent_menu );
    }

    public function current_vendor_can_view( $current_endpoint = 'dashboard' ) {
        $nav = $this->wcmb_get_vendor_dashboard_navigation();
        foreach ( $nav as $endpoint => $menu ) {
            if ( $endpoint == $current_endpoint ) {
                return current_user_can( $menu['capability'] ) || true === $menu['capability'];
            } else if ( ! empty( $menu['submenu'] ) && array_key_exists( $current_endpoint, $menu['submenu'] ) && isset( $menu['submenu'][$current_endpoint]['capability'] ) ) {
                return current_user_can( $menu['submenu'][$current_endpoint]['capability'] ) || true === $menu['submenu'][$current_endpoint]['capability'];
            }
        }
        return true;
    }

    /**
     * Display Vendor Announcements content
     * @global object $WCMb
     */
    public function wcmb_vendor_dashboard_vendor_announcements_endpoint() {
        global $WCMb;
        $frontend_style_path = $WCMb->plugin_url . 'assets/frontend/css/';
        $frontend_style_path = str_replace( array( 'http:', 'https:' ), '', $frontend_style_path );
        $frontend_script_path = $WCMb->plugin_url . 'assets/frontend/js/';
        $frontend_script_path = str_replace( array( 'http:', 'https:' ), '', $frontend_script_path );
        $suffix = defined( 'WCMB_SCRIPT_DEBUG' ) && WCMB_SCRIPT_DEBUG ? '' : '.min';
        //wp_enqueue_style('font-vendor_announcements', '//fonts.googleapis.com/css?family=Lato:400,100,100italic,300,300italic,400italic,700,700italic,900,900italic', array(), $WCMb->version);
        //wp_enqueue_style('ui_vendor_announcements', '//code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css', array(), $WCMb->version);
        wp_enqueue_script( 'jquery-ui-accordion' );
        wp_enqueue_script( 'wcmb_new_vandor_announcements_js', $frontend_script_path . 'wcmb_vendor_announcements' . $suffix . '.js', array( 'jquery' ), $WCMb->version, true );
        $WCMb->localize_script( 'wcmb_new_vandor_announcements_js' );
        //wp_enqueue_script('jquery');
        //wp_enqueue_script('wcmb_new_vandor_announcements_js_lib_ui', '//code.jquery.com/ui/1.10.4/jquery-ui.js', array('jquery'), $WCMb->version, true);
        $vendor = get_wcmb_vendor( get_current_vendor_id() );
        $WCMb->template->get_template( 'vendor-dashboard/vendor-announcements.php', array( 'vendor_announcements' => $vendor->get_announcements() ) );
    }

    /**
     * Display vendor dashboard shop front content
     * @global object $wcmb
     */
    public function wcmb_vendor_dashboard_storefront_endpoint() {
        global $WCMb;
        $vendor = get_wcmb_vendor( get_current_vendor_id() );
        $user_array = $WCMb->user->get_vendor_fields( $vendor->id );
        $WCMb->library->load_dashboard_upload_lib();
        $WCMb->library->load_gmap_api();
        $WCMb->template->get_template( 'vendor-dashboard/shop-front.php', $user_array );
    }
    
    /**
     * Display vendor profile management content
     * @global object $wcmb
     */
    public function wcmb_vendor_dashboard_profile_endpoint() {
        global $WCMb;
        $user = wp_get_current_user();
        $WCMb->library->load_dashboard_upload_lib();
        $WCMb->template->get_template( 'vendor-dashboard/profile.php', array( 'user' => $user ) );
    }

    /**
     * display vendor policies content
     * @global object $wcmb
     */
    public function wcmb_vendor_dashboard_vendor_policies_endpoint() {
        global $WCMb;
        $vendor = get_wcmb_vendor( get_current_vendor_id() );
        $user_array = $WCMb->user->get_vendor_fields( $vendor->id );
        if ( ! wp_script_is( 'tiny_mce', 'enqueued' ) ) {
            wp_enqueue_editor();
        }
        $WCMb->template->get_template( 'vendor-dashboard/vendor-policy.php', $user_array );
    }

    /**
     * Display Vendor billing settings content
     * @global object $wcmb
     */
    public function wcmb_vendor_dashboard_vendor_billing_endpoint() {
        global $WCMb;
        $vendor = get_wcmb_vendor( get_current_vendor_id() );
        $user_array = $WCMb->user->get_vendor_fields( $vendor->id );
        $WCMb->template->get_template( 'vendor-dashboard/vendor-billing.php', $user_array );
    }

    /**
     * Display vendor shipping content
     * @global object $wcmb
     */
    public function wcmb_vendor_dashboard_vendor_shipping_endpoint() {
        global $WCMb;
        $wcmb_payment_settings_name = get_option( 'wcmb_payment_settings_name' );
        $_vendor_give_shipping = get_user_meta( get_current_vendor_id(), '_vendor_give_shipping', true );
        if ( isset( $wcmb_payment_settings_name['give_shipping'] ) && empty( $_vendor_give_shipping ) ) {
            if (wp_script_is('wcmb-vendor-shipping', 'registered') &&
                !wp_script_is('wcmb-vendor-shipping', 'enqueued')) {
                wp_enqueue_script('wcmb-vendor-shipping');
            }

            $WCMb->template->get_template('vendor-dashboard/vendor-shipping.php');
        } else {
            echo '<p class="wcmb_headding3">' . __( 'Sorry you are not authorized for this pages. Please contact with admin.', 'MB-multivendor' ) . '</p>';
        }
    }

    /**
     * Display vendor report content
     * @global object $wcmb
     */
    public function wcmb_vendor_dashboard_vendor_report_endpoint() {
        global $WCMb;
        if ( isset( $_POST['wcmb_stat_start_dt'] ) ) {
            $start_date = $_POST['wcmb_stat_start_dt'];
        } else {
            // hard-coded '01' for first day     
            $start_date = date( 'Y-m-01' );
        }

        if ( isset( $_POST['wcmb_stat_end_dt'] ) ) {
            $end_date = $_POST['wcmb_stat_end_dt'];
        } else {
            // hard-coded '01' for first day
            $end_date = date( 'Y-m-d' );
        }
        $vendor = get_wcmb_vendor( get_current_vendor_id() );
        $WCMb_Plugin_Post_Reports = new WCMb_Report();
        $array_report = $WCMb_Plugin_Post_Reports->vendor_sales_stat_overview( $vendor, $start_date, $end_date );
        $WCMb->template->get_template( 'vendor-dashboard/vendor-report.php', $array_report );
    }

    public function wcmb_vendor_dashboard_add_product_endpoint() {
        global $WCMb, $wp;
        $WCMb->library->load_colorpicker_lib();
        $WCMb->library->load_datepicker_lib();
        $WCMb->library->load_frontend_upload_lib();
        $WCMb->library->load_accordian_lib();
        $WCMb->library->load_select2_lib();

        $suffix = defined( 'WCMB_SCRIPT_DEBUG' ) && WCMB_SCRIPT_DEBUG ? '' : '.min';

        if ( get_wcmb_vendor_settings( 'is_singleproductmultiseller', 'general' ) == 'Enable' ) {
            wp_enqueue_script( 'wcmb_admin_product_auto_search_js', $WCMb->plugin_url . 'assets/admin/js/admin-product-auto-search' . $suffix . '.js', array( 'jquery' ), $WCMb->version, true );
            wp_localize_script( 'wcmb_admin_product_auto_search_js', 'wcmb_admin_product_auto_search_js_params', array(
                'ajax_url'              => admin_url( 'admin-ajax.php' ),
                'search_products_nonce' => wp_create_nonce( 'search-products' ),
            ) );
        }

        if ( ! wp_script_is( 'tiny_mce', 'enqueued' ) ) {
            wp_enqueue_editor();
        }
        // Enqueue jQuery UI and autocomplete
        wp_enqueue_script( 'jquery-ui-autocomplete' );
        wp_enqueue_script( 'wp-a11y' );
        wp_enqueue_script( 'suggest' );
        
        wp_register_script( 'wcmb_product_classify', $WCMb->plugin_url . 'assets/frontend/js/product-classify.js', array( 'jquery', 'jquery-blockui' ), $WCMb->version, true );
        $script_param = array(
            'ajax_url' => $WCMb->ajax_url(),
            'initial_graphic_url' => $WCMb->plugin_url.'assets/images/select-category-graphic.png',
            'i18n' => array(
                'select_cat_list' => __( 'Select a category from the list', 'MB-multivendor' )
            )
        );
        wp_enqueue_script( 'wcmb_product_classify' );
        $WCMb->localize_script( 'wcmb_product_classify', apply_filters( 'wcmb_product_classify_script_data_params', $script_param ) );

        $WCMb->template->get_template( 'vendor-dashboard/product-manager/add-product.php' );
    }
    
    public function wcmb_vendor_dashboard_edit_product_endpoint(){
        global $WCMb;
        // load scripts & styles
        $suffix = defined( 'WCMb_SCRIPT_DEBUG' ) && WCMB_SCRIPT_DEBUG ? '' : '.min';
        $WCMb->library->load_select2_lib();
        $WCMb->library->load_datepicker_lib();
        $WCMb->library->load_jquery_serializejson_library();
        $WCMb->library->load_tabs_library();
        wp_enqueue_media();
        wp_enqueue_script( 'selectWoo' );
        wp_enqueue_style('advance-product-manager');
        wp_register_script( 'wcmb-advance-product', $WCMb->plugin_url . 'assets/frontend/js/product.js', array( 'jquery', 'jquery-ui-sortable', 'select2_js', 'jquery-ui-datepicker', 'selectWoo', 'wcmb-serializejson', 'wcmb-tabs' ), $WCMb->version );
        wp_enqueue_script( 'wcmb-meta-boxes' );
        $WCMb->localize_script( 'wcmb-meta-boxes');
        // load classes
        $WCMb->load_class( 'edit-product', 'products' );
        $edit_product = new WCMb_Products_Edit_Product();
        $edit_product->output();
    }

    public function wcmb_vendor_dashboard_products_endpoint() {
        global $WCMb;
        if ( is_user_logged_in() && is_user_wcmb_vendor( get_current_vendor_id() ) ) {
            $WCMb->library->load_dataTable_lib();
            $products_table_headers = array(
                'select_product' => '',
                'image'      => '<i class="wcmb-font ico-image-icon"></i>',
                'name'       => __( 'Product', 'MB-multivendor' ),
                'price'      => __( 'Price', 'MB-multivendor' ),
                'stock'      => __( 'Stock', 'MB-multivendor' ),
                'categories' => __( 'Categories', 'MB-multivendor' ),
                'date'       => __( 'Date', 'MB-multivendor' ),
                'status'     => __( 'Status', 'MB-multivendor' ),
                'actions'     => __( 'Actions', 'MB-multivendor' ),
            );
            $products_table_headers = apply_filters( 'wcmb_vendor_dashboard_product_list_table_headers', $products_table_headers );
            $table_init = apply_filters( 'wcmb_vendor_dashboard_product_list_table_init', array(
                'ordering'    => 'true',
                'searching'   => 'false',
                'emptyTable'  => __( 'No products found!', 'MB-multivendor' ),
                'processing'  => __( 'Processing...', 'MB-multivendor' ),
                'info'        => __( 'Showing _START_ to _END_ of _TOTAL_ products', 'MB-multivendor' ),
                'infoEmpty'   => __( 'Showing 0 to 0 of 0 products', 'MB-multivendor' ),
                'lengthMenu'  => __( 'Number of rows _MENU_', 'MB-multivendor' ),
                'zeroRecords' => __( 'No matching products found', 'MB-multivendor' ),
                'search'      => __( 'Search:', 'MB-multivendor' ),
                'next'        => __( 'Next', 'MB-multivendor' ),
                'previous'    => __( 'Previous', 'MB-multivendor' ),
            ) );

            $WCMb->template->get_template( 'vendor-dashboard/product-manager/products.php', array( 'products_table_headers' => $products_table_headers, 'table_init' => $table_init ) );
        }
    }

    public function wcmb_vendor_dashboard_add_coupon_endpoint() {
        global $WCMb, $wp;
              
        $WCMb->library->load_select2_lib();
        $WCMb->library->load_datepicker_lib();
        wp_enqueue_script( 'selectWoo' );
        wp_register_script( 'wcmb-advance-coupon', $WCMb->plugin_url . 'assets/frontend/js/coupon.js', array( 'jquery', 'select2_js', 'jquery-ui-datepicker', 'selectWoo' ), $WCMb->version );
        wp_enqueue_script( 'wcmb-meta-boxes' );
        $WCMb->localize_script( 'wcmb-meta-boxes');
        // load classes
        $WCMb->load_class( 'add-coupon', 'coupons' );
        $add_coupon = new WCMb_Coupons_Add_Coupon();
        $add_coupon->output();
        
    }

    public function wcmb_vendor_dashboard_coupons_endpoint() {
        global $WCMb;
        if ( is_user_logged_in() && is_user_wcmb_vendor( get_current_vendor_id() ) ) {
            $WCMb->library->load_dataTable_lib();
            $WCMb->template->get_template( 'vendor-dashboard/coupon-manager/coupons.php' );
        }
    }

    /**
     * Dashboard order endpoint contect
     * @global object $wcmb
     */
    public function wcmb_vendor_dashboard_vendor_orders_endpoint() {
        global $WCMb, $wp;
        $vendor = get_current_vendor();
        if ( isset( $_POST['wcmb-submit-mark-as-ship'] ) ) {
            $order_id = $_POST['order_id'];
            $tracking_id = $_POST['tracking_id'];
            $tracking_url = $_POST['tracking_url'];
            $vendor->set_order_shipped( $order_id, $tracking_id, $tracking_url );
        }
        $vendor_order = $wp->query_vars[get_wcmb_vendor_settings( 'wcmb_vendor_orders_endpoint', 'vendor', 'general', 'vendor-orders' )];
        if ( ! empty( $vendor_order ) ) {
            $WCMb->template->get_template( 'vendor-dashboard/vendor-orders/vendor-order-details.php', array( 'order_id' => $vendor_order ) );
        } else {
            $WCMb->library->load_dataTable_lib();

            if ( ! empty( $_POST['wcmb_start_date_order'] ) ) {
                $start_date = $_POST['wcmb_start_date_order'];
            } else {
                $start_date = date( 'Y-m-01' );
            }

            if ( ! empty( $_POST['wcmb_end_date_order'] ) ) {
                $end_date = $_POST['wcmb_end_date_order'];
            } else {
                $end_date = date( 'Y-m-d' );
            }
            //wp_localize_script('vendor_orders_js', 'vendor_orders_args', array('start_date' => strtotime($start_date), 'end_date' => strtotime($end_date . ' +1 day')));
            $WCMb->template->get_template( 'vendor-dashboard/vendor-orders.php', array( 'vendor' => $vendor, 'start_date' => strtotime( $start_date ), 'end_date' => strtotime( $end_date . ' +1 day' ) ) );
        }
    }

    /**
     * Display Vendor Withdrawal Content
     * @global object $wcmb
     */
    public function wcmb_vendor_dashboard_vendor_withdrawal_endpoint() {
        global $WCMb;
        $vendor = get_wcmb_vendor( get_current_vendor_id() );
        if ( $vendor ) {
            $WCMb->library->load_dataTable_lib();
            $meta_query['meta_query'] = array(
                array(
                    'key'     => '_paid_status',
                    'value'   => 'unpaid',
                    'compare' => '='
                ),
                array(
                    'key'     => '_commission_vendor',
                    'value'   => absint( $vendor->term_id ),
                    'compare' => '='
                )
            );
            $vendor_unpaid_orders = $vendor->get_orders( false, false, $meta_query );
            // withdrawal table init
            $table_init = apply_filters( 'wcmb_vendor_dashboard_payment_withdrawal_table_init', array(
                'ordering'    => 'false',
                'searching'   => 'false',
                'emptyTable'  => __( 'No orders found!', 'MB-multivendor' ),
                'processing'  => __( 'Processing...', 'MB-multivendor' ),
                'info'        => __( 'Showing _START_ to _END_ of _TOTAL_ orders', 'MB-multivendor' ),
                'infoEmpty'   => __( 'Showing 0 to 0 of 0 orders', 'MB-multivendor' ),
                'lengthMenu'  => __( 'Number of rows _MENU_', 'MB-multivendor' ),
                'zeroRecords' => __( 'No matching orders found', 'MB-multivendor' ),
                'search'      => __( 'Search:', 'MB-multivendor' ),
                'next'        => __( 'Next', 'MB-multivendor' ),
                'previous'    => __( 'Previous', 'MB-multivendor' ),
            ) );

            $WCMb->template->get_template( 'vendor-dashboard/vendor-withdrawal.php', array( 'vendor' => $vendor, 'vendor_unpaid_orders' => $vendor_unpaid_orders, 'table_init' => $table_init ) );
        }
    }

    /**
     * Display transaction details content
     * @global object $wcmb
     */
    public function wcmb_vendor_dashboard_transaction_details_endpoint() {
        global $WCMb, $wp;
        $user_id = get_current_vendor_id();
        if ( is_user_wcmb_vendor( $user_id ) ) {
            $transaction_id = $wp->query_vars[get_wcmb_vendor_settings( 'wcmb_transaction_details_endpoint', 'vendor', 'general', 'transaction-details' )];
            if ( ! empty( $transaction_id ) ) {
                $WCMb->template->get_template( 'vendor-dashboard/vendor-withdrawal/vendor-withdrawal-request.php', array( 'transaction_id' => $transaction_id ) );
            } else {
                $WCMb->library->load_dataTable_lib();
                $WCMb->template->get_template( 'vendor-dashboard/vendor-transactions.php' );
            }
        }
    }

    /**
     * Display Vendor university content
     * @global object $wcmb
     */
    public function wcmb_vendor_dashboard_vendor_knowledgebase_endpoint() {
        global $WCMb;
        wp_enqueue_style( 'jquery-ui-style' );
        wp_enqueue_script( 'jquery-ui-accordion' );
        $WCMb->template->get_template( 'vendor-dashboard/vendor-university.php' );
    }

    /**
     * Display Vendor Tools purging content
     * @global object $wcmb
     */
    public function wcmb_vendor_dashboard_vendor_tools_endpoint() {
        global $WCMb;
        $WCMb->template->get_template( 'vendor-dashboard/vendor-tools.php' );
    }

    /**
     * Display Vendor Products Q&As content
     * @global object $wcmb
     */
    public function wcmb_vendor_dashboard_products_qna_endpoint() {
        global $WCMb;
        if ( is_user_logged_in() && is_user_wcmb_vendor( get_current_vendor_id() ) ) {
            $WCMb->library->load_dataTable_lib();
            $WCMb->library->load_select2_lib();
            $WCMb->template->get_template( 'vendor-dashboard/vendor-products-qna.php' );
        }
    }

    public function save_vendor_dashboard_data() {
        global $WCMb;
        $vendor = get_wcmb_vendor( get_current_vendor_id() );
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
            switch ( $WCMb->endpoints->get_current_endpoint() ) {
                case 'storefront':
                case 'vendor-policies':
                case 'vendor-billing':
                    $error = $WCMb->vendor_dashboard->save_store_settings( $vendor->id, $_POST );
                    if ( empty( $error ) ) {
                        wc_add_notice( __( 'All Options Saved', 'MB-multivendor' ), 'success' );
                    } else {
                        wc_add_notice( $error, 'error' );
                    }
                    break;
                case 'vendor-shipping':
                    $WCMb->vendor_dashboard->save_vendor_shipping( $vendor->id, $_POST );
                    break;
                case 'profile':
                    $WCMb->vendor_dashboard->save_vendor_profile( $vendor->id, $_POST );
                    break;
                default :
                    break;
            }
        }
        // FPM add product messages
        if ( get_transient( 'wcmb_fpm_product_added_msg' ) ) {
            wc_add_notice( get_transient( 'wcmb_fpm_product_added_msg' ), 'success' );
            delete_transient( 'wcmb_fpm_product_added_msg' );
        }
    }

    /**
     * Change endpoint page title
     * @global object $wp_query
     * @global object $wcmb
     * @param string $title
     * @return string
     */
    public function wcmb_vendor_dashboard_endpoint_title( $title ) {
        global $wp_query, $WCMb;
        if ( ! is_null( $wp_query ) && ! is_admin() && is_main_query() && in_the_loop() && is_page() && is_wcmb_endpoint_url() ) {
            $endpoint = $WCMb->endpoints->get_current_endpoint();

            if ( isset( $WCMb->endpoints->wcmb_query_vars[$endpoint]['label'] ) && $endpoint_title = $WCMb->endpoints->wcmb_query_vars[$endpoint]['label'] ) {
                $title = $endpoint_title;
            }

            remove_filter( 'the_title', array( &$this, 'wcmb_vendor_dashboard_endpoint_title' ) );
        }

        return $title;
    }

    /**
     * set policies tab cap
     * @param Boolean $cap
     * @return Boolean
     */
    public function wcmb_vendor_dashboard_menu_vendor_policies_capability( $cap ) {
        if ( ('Enable' === get_wcmb_vendor_settings( 'is_policy_on', 'general' ) && apply_filters( 'wcmb_vendor_can_overwrite_policies', true )) || ('Enable' === get_wcmb_vendor_settings( 'is_customer_support_details', 'general' ) && apply_filters( 'wcmb_vendor_can_overwrite_customer_support', true )) ) {
            $cap = true;
        }
        return $cap;
    }

    public function wcmb_vendor_dashboard_menu_vendor_withdrawal_capability( $cap ) {
        if ( get_wcmb_vendor_settings( 'wcmb_disbursal_mode_vendor', 'payment' ) ) {
            $cap = true;
        }
        return $cap;
    }

    public function wcmb_vendor_dashboard_menu_vendor_shipping_capability( $cap ) {
        $vendor = get_wcmb_vendor( get_current_vendor_id() );
        if ( $vendor ) {
            return $vendor->is_shipping_enable();
        } else {
            return false;
        }
    }

    /**
     * Generate Vendor Progress
     * @global object $wcmb
     */
    public function before_wcmb_vendor_dashboard_content( $key ) {
        global $WCMb;
        if ( $key !== $WCMb->endpoints->get_current_endpoint() ) {
            return;
        }
        $vendor = get_wcmb_vendor( get_current_vendor_id() );
        if ( $vendor && apply_filters( 'wcmb_vendor_dashboard_show_progress_bar', true, $vendor ) ) {
            $vendor_progress = wcmb_get_vendor_profile_completion( $vendor->id );
            if ( $vendor_progress['progress'] < 100 ) {
                echo '<div class="col-md-12">';
                echo '<div class="panel">';
                if ( $vendor_progress['todo'] && is_array( $vendor_progress['todo'] ) ) {
                    $todo_link = isset( $vendor_progress['todo']['link'] ) ? esc_url( $vendor_progress['todo']['link'] ) : '';
                    $todo_label = isset( $vendor_progress['todo']['label'] ) ? $vendor_progress['todo']['label'] : '';
                    echo '<div style="margin:17px 20px 12px 20px;">' . __( 'To boost up your profile progress add', 'MB-multivendor' ) . ' <a href="' . $todo_link . '">' . $todo_label . '</a></div>';
                }
                echo '<div class="progress" style="margin:0 20px 20px;">';
                echo '<div class="progress-bar" role="progressbar" style="width: ' . $vendor_progress['progress'] . '%;" aria-valuenow="' . $vendor_progress['progress'] . '" aria-valuemin="0" aria-valuemax="100">' . $vendor_progress['progress'] . '%</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        }
    }

    /**
     * wcmb theme supported function
     */
    public function wcmb_add_theme_support() {
        if ( is_vendor_dashboard() && is_user_logged_in() && is_user_wcmb_vendor( get_current_user_id() ) ) {
            global $wp_filter;
            //Flatsome mobile menu support
            remove_action( 'wp_footer', 'flatsome_mobile_menu', 7 );
            // Remove demo store notice
            remove_action( 'wp_footer', 'woocommerce_demo_store' );
            // Remove custom css
            $wp_head_hooks = $wp_filter['wp_head']->callbacks;
            foreach ( $wp_head_hooks as $priority => $wp_head_hook ) {
                foreach ( array_keys( $wp_head_hook ) as $hook ) {
                    if ( strpos( $hook, 'custom_css' ) ) {
                        remove_action( 'wp_head', $hook, $priority );
                    }
                }
            }
        }
    }

    /**
     * wcmb rejected vendor dashboard function
     */
    public function rejected_vendor_dashboard_content() {
    	global $WCMb, $wp;
    	
    	if(isset($wp->query_vars['rejected-vendor-reapply'])) {
    		$WCMb->template->get_template('non-vendor/rejected-vendor-reapply.php');
    	} else {
    		$WCMb->template->get_template('non-vendor/rejected-vendor-dashboard.php');
		}
    }
    
    /**
     *  Update rejected vendor data and make the status pending
     */
    public function save_rejected_vendor_reapply_data() {
    	global $WCMb;
        $user = wp_get_current_user();
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' && is_user_wcmb_rejected_vendor($user->ID) && $WCMb->endpoints->get_current_endpoint() == 'rejected-vendor-reapply') {
        	if(isset($_POST['reapply_vendor_application']) && isset($_POST['wcmb_vendor_fields'])) {
        		if (isset($_FILES['wcmb_vendor_fields'])) {
					$attacment_files = $_FILES['wcmb_vendor_fields'];
					$files = array();
					$count = 0;
					if (!empty($attacment_files) && is_array($attacment_files)) {
						foreach ($attacment_files['name'] as $key => $attacment) {
							foreach ($attacment as $key_attacment => $value_attacment) {
								$files[$count]['name'] = $value_attacment;
								$files[$count]['type'] = $attacment_files['type'][$key][$key_attacment];
								$files[$count]['tmp_name'] = $attacment_files['tmp_name'][$key][$key_attacment];
								$files[$count]['error'] = $attacment_files['error'][$key][$key_attacment];
								$files[$count]['size'] = $attacment_files['size'][$key][$key_attacment];
								$files[$count]['field_key'] = $key;
								$count++;
							}
						}
					}
					$upload_dir = wp_upload_dir();
					require_once(ABSPATH . 'wp-admin/includes/image.php');
					if (!function_exists('wp_handle_upload')) {
						require_once( ABSPATH . 'wp-admin/includes/file.php' );
					}
					foreach ($files as $file) {
						$uploadedfile = $file;
						$upload_overrides = array('test_form' => false);
						$movefile = wp_handle_upload($uploadedfile, $upload_overrides);
						if ($movefile && !isset($movefile['error'])) {
							$filename = $movefile['file'];
							$filetype = wp_check_filetype($filename, null);
							$attachment = array(
								'post_mime_type' => $filetype['type'],
								'post_title' => $file['name'],
								'post_content' => '',
								'post_status' => 'inherit',
								'guid' => $movefile['url']
							);
							$attach_id = wp_insert_attachment($attachment, $movefile['file']);
							$attach_data = wp_generate_attachment_metadata($attach_id, $filename);
							wp_update_attachment_metadata($attach_id, $attach_data);
							$_POST['wcmb_vendor_fields'][$file['field_key']]['value'][] = $attach_id;
						}
					}
				}
        		update_user_meta( $user->ID, 'wcmb_vendor_fields', $_POST['wcmb_vendor_fields']);
        		$user->remove_cap( 'dc_rejected_vendor' );
        		$user->add_cap( 'dc_pending_vendor' );
        		
        		$wcmb_vendor_rejection_notes = unserialize( get_user_meta( $user->ID, 'wcmb_vendor_rejection_notes', true ) );
				$wcmb_vendor_rejection_notes[time()] = array(
						'note_by' => $user->ID,
						'note' => __( 'Re applied to become a vendor', 'MB-multivendor' ));
				update_user_meta( $user->ID, 'wcmb_vendor_rejection_notes', serialize( $wcmb_vendor_rejection_notes ) );
        	}
    	}
    }
}

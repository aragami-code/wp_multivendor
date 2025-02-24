<?php

class WCMb_Settings {

    private $tabs = array();
    private $options = array();
    private $tabsection_general = array();
    private $tabsection_payment = array();
    private $tabsection_vendor = array();
    private $tabsection_capabilities = array();
    private $vendor_class_obj;

    /**
     * Start up
     */
    public function __construct() {
        // Admin menu
        add_action( 'admin_menu', array( $this, 'add_settings_page' ), 100 );
        add_action( 'admin_init', array( $this, 'settings_page_init' ) );

        add_action( 'in_admin_header', array( &$this, 'wcmb_settings_admin_header' ), 100 );

        // Settings tabs general
        add_action( 'settings_page_general_tab_init', array( &$this, 'general_tab_init' ), 10, 1 );
        add_action( 'settings_page_general_policies_tab_init', array( &$this, 'general_policies_tab_init' ), 10, 2 );
       // add_action( 'settings_page_general_customer_support_details_tab_init', array( &$this, 'general_customer_support_details_tab_init' ), 10, 2 );
        // Settings tabs vendor
        add_action( 'settings_page_vendor_general_tab_init', array( &$this, 'vendor_general_tab_init' ), 10, 2 );
        add_action( 'settings_page_vendor_registration_tab_init', array( &$this, 'vendor_registration_tab_init' ), 10, 2 );
        add_action( 'settings_page_vendor_dashboard_tab_init', array( &$this, 'vendor_dashboard_tab_init' ), 10, 2 );
        // Settings tabs frontend
//        add_action('settings_page_frontend_tab_init', array(&$this, 'frontend_tab_init'), 10, 1);
        // Settings tabs payment
        add_action( 'settings_page_payment_tab_init', array( &$this, 'payment_tab_init' ), 10, 1 );
        add_action( 'settings_page_payment_paypal_masspay_tab_init', array( &$this, 'payment_paypal_masspay_init' ), 10, 2 );
        add_action( 'settings_page_payment_paypal_payout_tab_init', array( &$this, 'payment_paypal_payout_init' ), 10, 2 );
        add_action( 'settings_page_payment_stripe_gateway_tab_init', array(&$this, 'payment_stripe_gateway_tab_init'), 10, 2);
        // Settings tabs capability
        add_action( 'settings_page_capabilities_product_tab_init', array( &$this, 'capabilites_product_tab_init' ), 10, 2 );
        add_action('settings_page_capabilities_order_tab_init', array(&$this, 'capabilites_order_tab_init'), 10, 2);
//        add_action('settings_page_capabilities_miscellaneous_tab_init', array(&$this, 'capabilites_miscellaneous_tab_init'), 10, 2);
        // Settings tabs others
       // add_action( 'settings_page_wcmb-addons_tab_init', array( &$this, 'wcmb_addons_tab_init' ), 10, 2 );
        add_action( 'settings_page_to_do_list_tab_init', array( &$this, 'to_do_list_tab_init' ), 10, 1 );
        add_action( 'settings_page_notices_tab_init', array( &$this, 'notices_tab_init' ), 10, 1 );
        add_action( 'settings_page_vendors_tab_init', array( &$this, 'vendors_tab_init' ), 10, 1 );

        add_action( 'update_option_wcmb_vendor_general_settings_name', array( &$this, 'wcmb_update_option_wcmb_vendor_general_settings_name' ) );
        
        // Save screen options
        add_filter('set-screen-option', array( &$this, 'vendors_set_option'), 10, 3);
    }
    
    public function wcmb_settings_admin_header() {
        $screen = get_current_screen();
        if ( empty( $screen->id ) || strpos( $screen->id, 'wcmb_page_wcmb-setting-admin' ) === false ) {
            return;
        }
        echo '<div class="wcmb-settings-admin-header"></div>';
    }

    /**
     * flush rewrite rules after endpoints change
     */
    public function wcmb_update_option_wcmb_vendor_general_settings_name() {
        global $WCMb;
        $WCMb->endpoints->init_wcmb_query_vars();
        $WCMb->endpoints->add_wcmb_endpoints();
        flush_rewrite_rules();
    }

    /**
     * Add options page   
     */
    public function add_settings_page() {
        global $WCMb, $submenu;

        add_menu_page(
            __( 'MB Multivendor', 'MB-multivendor' )
            , __( 'MB multivendor', 'MB-multivendor' )
            , 'manage_woocommerce'
            , 'wcmb'
            , 'dashicons-admin-site'
            , null
            , 100
        );
        //add_submenu_page( 'wcmb', __( 'Reports', 'MB-multivendor' ), __( 'Reports', 'MB-multivendor' ), 'manage_woocommerce', 'wc-reports&tab=wcmb_vendors', '__return_false' );
        $wcmb_vendors_page = add_submenu_page( 'wcmb', __( 'Vendors', 'MB-multivendor' ), __( 'Vendors', 'MB-multivendor' ), 'manage_woocommerce', 'vendors', array( $this, 'wcmb_vendors' ) );
        $wcmb_settings_page = add_submenu_page( 'wcmb', __( 'Settings', 'MB-multivendor' ), __( 'Settings', 'MB-multivendor' ), 'manage_woocommerce', 'wcmb-setting-admin', array( $this, 'create_wcmb_settings' ) );

        $wcmb_todo_list = add_submenu_page( 'wcmb', __( 'To-do List', 'MB-multivendor' ), __( 'tasks List', 'MB-multivendor' ), 'manage_woocommerce', 'wcmb-to-do', array( $this, 'wcmb_to_do' ) );
        //$wcmb_extension_page = add_submenu_page( 'wcmb', __( 'Extensions', 'MB-multivendor' ), __( 'Extensions', 'MB-multivendor' ), 'manage_woocommerce', 'wcmb-extensions', array( $this, 'wcmb_extensions' ) );
        // transaction details page
        // $wcmb_extension_page = add_submenu_page( 'wcmb', __( 'Transaction Details', 'MB-multivendor' ), __( 'Transaction Details', 'MB-multivendor' ), 'manage_woocommerce', 'wcmb-transaction-details', array( $this, 'wcmb_transaction_details' ) );

        // Assign priority incrmented by 1
        $wcmb_submenu_priority = array(
        	'wc-reports&tab=wcmb_vendors' => 4,
        	'edit.php?post_type=dc_commission' => 3,
        	//'edit.php?post_type=wcmb_vendor_notice' => 2,
        	//'edit.php?post_type=wcmb_university' => 3,
        	'vendors' => 1,
        	'wcmb-setting-admin' => 2,
        	'wcmb-to-do' => 5,
        	//'wcmb-extensions' => 7,
		);
        
		$this->tabs = $this->get_wcmb_settings_tabs();
        $this->tabsection_general = $this->get_wcmb_settings_tabsections_general();
        $this->tabsection_payment = $this->get_wcmb_settings_tabsections_payment();
        $this->tabsection_vendor = $this->get_wcmb_settings_tabsections_vendor();
        $this->tabsection_capabilities = $this->get_wcmb_settings_tabsections_capabilities();
        // Add wcmb Help Tab
      //  add_action( 'load-' . $wcmb_settings_page, array( &$this, 'wcmb_settings_add_help_tab' ) );
        //add_action( 'load-' . $wcmb_extension_page, array( &$this, 'wcmb_settings_add_help_tab' ) );
        //add_action( 'load-' . $wcmb_todo_list, array( &$this, 'wcmb_settings_add_help_tab' ) );
        add_action( 'load-' . $wcmb_vendors_page, array( &$this, 'wcmb_vendors_add_help_tab' ) );
        
        /* sort wcmb submenu */
        if ( isset( $submenu['wcmb'] ) ) {
        	$wcmb_submenu_priority = apply_filters( 'wcmb_submenu_items', $wcmb_submenu_priority, $submenu['wcmb'] );
        	$submenu_wcmb_sort = array();
        	$submenu_wcmb_sort_duplicates = array();
        	foreach($submenu['wcmb'] as $menu_items) {
        		if(isset($wcmb_submenu_priority[$menu_items[2]]) && ($wcmb_submenu_priority[$menu_items[2]] >= 0) && !isset($submenu_wcmb_sort[$wcmb_submenu_priority[$menu_items[2]]])) $submenu_wcmb_sort[$wcmb_submenu_priority[$menu_items[2]]] = $menu_items;
				else $submenu_wcmb_sort_duplicates[] = $menu_items;
        	}
        	
        	ksort($submenu_wcmb_sort);
        	
        	$submenu_wcmb_sort = array_merge($submenu_wcmb_sort, $submenu_wcmb_sort_duplicates);
        	
        	$submenu['wcmb'] = $submenu_wcmb_sort;
        }
    }

    public function wcmb_transaction_details() {
        global $WCMb;
        ?>
        <div class="wrap blank-wrap"><h3><?php _e( 'Transaction Details', 'MB-multivendor' ); ?></h3></div>
        <div class="wrap wcmb-settings-wrap panel-body">
            <?php
            $_is_trans_details_page = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '';
            $trans_id = isset( $_REQUEST['trans_id'] ) ? absint( $_REQUEST['trans_id'] ) : 0;
            if ( $_is_trans_details_page == 'wcmb-transaction-details' && $trans_id != 0 ) {
                $transaction = get_post( $trans_id );
                if ( isset( $transaction->post_type ) && $transaction->post_type == 'wcmb_transaction' ) {
                    $vendor = get_wcmb_vendor_by_term( $transaction->post_author ) ? get_wcmb_vendor_by_term( $transaction->post_author ) : get_wcmb_vendor( $transaction->post_author );
                    $commission_details = $WCMb->transaction->get_transaction_item_details( $trans_id );
                    ?>
                    <table class="widefat fixed striped">
                        <?php
                        if ( ! empty( $commission_details['header'] ) ) {
                            echo '<thead><tr>';
                            foreach ( $commission_details['header'] as $header_val ) {
                                echo '<th>' . $header_val . '</th>';
                            }
                            echo '</tr></thead>';
                        }
                        echo '<tbody>';
                        if ( ! empty( $commission_details['body'] ) ) {

                            foreach ( $commission_details['body'] as $commission_detail ) {
                                echo '<tr>';
                                foreach ( $commission_detail as $details ) {
                                    foreach ( $details as $detail_key => $detail ) {
                                        echo '<td>' . $detail . '</td>';
                                    }
                                }
                                echo '</tr>';
                            }
                        }
                        if ( $totals = $WCMb->transaction->get_transaction_item_totals( $trans_id, $vendor ) ) {
                            foreach ( $totals as $total ) {
                                echo '<tr><td colspan="3" >' . $total['label'] . '</td><td>' . $total['value'] . '</td></tr>';
                            }
                        }
                        echo '</tbody>';
                        ?>
                    </table>
                <?php } else { ?>
                    <p class="wcmb_headding3"><?php echo __( 'Unfortunately transaction details are not found. You may try again later.', 'MB-multivendor' ); ?></p>
                <?php }
            } else {
                ?>
                <p class="wcmb_headding3"><?php echo __( 'Unfortunately transaction details are not found. You may try again later.', 'MB-multivendor' ); ?></p> 
            <?php } ?>
        </div>
        <?php
    }
    
    public function wcmb_vendors_add_help_tab() {
        global $WCMb;
        $tab = 'vendors';
        
        $screen = get_current_screen();
        
        $option = 'per_page';
		$args   = [
			'label'   => __('Number of vendors per page:', 'MB-multivendor'),
			'default' => 5,
			'option'  => 'vendors_per_page'
		];
	
		add_screen_option( $option, $args );
		
		$WCMb->admin->load_class( "settings-{$tab}", $WCMb->plugin_path, $WCMb->token );
		$this->vendor_class_obj = new WCMb_Settings_WCMb_Vendors( $tab );
    }
    
    function vendors_set_option($status, $option, $value) {
		if ( 'vendors_per_page' == $option ) return $value;
		return $status;
	}
    
    public function wcmb_settings_add_help_tab() {
        global $WCMb;
        $screen = get_current_screen();

        $screen->add_help_tab();
        $screen->add_help_tab();
        $screen->add_help_tab();
        $screen->add_help_tab();
        $screen->add_help_tab();
        $screen->set_help_sidebar();
    }

    public function get_wcmb_settings_tabs() {
        $tabs = apply_filters( 'wcmb_tabs', array(
            'general'      => __( 'General', 'MB-multivendor' ),
            'vendor'       => __( 'Vendor', 'MB-multivendor' ),
//            'frontend' => __('Frontend', 'MB-multivendor'),
            'payment'      => __( 'Payment', 'MB-multivendor' ),
            'capabilities' => __( 'Capabilities', 'MB-multivendor' )
        ) );
        return $tabs;
    }

    public function get_wcmb_settings_tabsections_general() {
        $tabsection_general = apply_filters( 'wcmb_tabsection_general', array(
            'general' => array( 'title' => __( 'General', 'MB-multivendor' ), 'icon' => 'dashicons-admin-site' ),
        ) );
        if ( 'Enable' === get_wcmb_vendor_settings( 'is_policy_on', 'general', '' ) ) {
            $tabsection_general['policies'] = array( 'title' => __( 'Policies', 'MB-multivendor' ), 'icon' => 'dashicons-lock' );
        }
        if ( 'Enable' === get_wcmb_vendor_settings( 'is_customer_support_details', 'general', '' ) ) {
            $tabsection_general['customer_support_details'] = array( 'title' => __( 'Customer Support', 'MB-multivendor' ), 'icon' => 'dashicons-universal-access' );
        }

        return $tabsection_general;
    }

    public function get_wcmb_settings_tabsections_payment() {
        $tabsection_payment = apply_filters( 'wcmb_tabsection_payment', array(
            'payment' => array( 'title' => __( 'Payment Settings', 'MB-multivendor' ), 'icon' => 'dashicons-share-alt' )
        ) );
        if ( 'Enable' === get_wcmb_vendor_settings( 'payment_method_paypal_masspay', 'payment' ) ) {
            $tabsection_payment['paypal_masspay'] = array( 'title' => __( 'Paypal Masspay', 'MB-multivendor' ), 'icon' => 'dashicons-tickets-alt' );
        }
        if ( 'Enable' === get_wcmb_vendor_settings( 'payment_method_paypal_payout', 'payment' ) ) {
            $tabsection_payment['paypal_payout'] = array( 'title' => __( 'Paypal Payout', 'MB-multivendor' ), 'icon' => 'dashicons-randomize' );
        }
        if ( 'Enable' === get_wcmb_vendor_settings( 'payment_method_stripe_masspay', 'payment' ) ) {
            $tabsection_payment['stripe_gateway'] = array( 'title' => __( 'Stripe Gateway', 'MB-multivendor' ), 'icon' => 'dashicons-tickets-alt' );
        }
        return $tabsection_payment;
    }

    public function get_wcmb_settings_tabsections_vendor() {
        $tabsection_vendor = apply_filters( 'wcmb_tabsection_vendor', array(
            'registration' => array( 'title' => __( 'Vendor Registration', 'MB-multivendor' ), 'icon' => 'dashicons-media-document' ),
            'general'      => array( 'title' => __( 'Vendor Pages', 'MB-multivendor' ), 'icon' => 'dashicons-admin-page' ),
            'dashboard'    => array( 'title' => __( 'Vendor Frontend', 'MB-multivendor' ), 'icon' => 'dashicons-admin-appearance' )
        ) );
        return $tabsection_vendor;
    }

    public function get_wcmb_settings_tabsections_capabilities() {
        $tabsection_capabilities = apply_filters( 'wcmb_tabsection_capabilities', array(
            'product' => array( 'title' => __( 'Product', 'MB-multivendor' ), 'icon' => 'dashicons-cart' ),
'order' => __('Order', 'MB-multivendor'),
//            'miscellaneous' => __('Miscellaneous', 'MB-multivendor')
        ) );
        return $tabsection_capabilities;
    }

    public function get_settings_tab_desc() {
        $tab_desc = apply_filters( 'wcmb_tabs_desc', array(
            'product'  => __( 'Configure the "Product Add" page for vendors. Choose the features you want to show to your vendors.', 'MB-multivendor' ),
            'frontend' => __( 'Configure which vendor details you want to reveal to your users', 'MB-multivendor' ),
        ) );
        return $tab_desc;
    }

    public function wcmb_settings_tabs() {
        $current = isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ? $_GET['tab'] : 'general';
        $sublinks = array();
        foreach ( $this->tabs as $tab_id => $tab ) {
            if ( $current != $tab_id || ! $this->is_wcmb_tab_has_subtab( $tab_id ) ) {
                continue;
            }
            $current_section = isset( $_GET['tab_section'] ) && ! empty( $_GET['tab_section'] ) ? $_GET['tab_section'] : current( array_keys( $this->get_wcmb_subtabs( $tab_id ) ) );

            foreach ( $this->get_wcmb_subtabs( $tab_id ) as $subtab_id => $subtab ) {
                $sublink = '';
                if ( is_array( $subtab ) ) {
                    $icon = isset( $subtab['icon'] ) && ! empty( $subtab['icon'] ) ? '<span class="dashicons ' . $subtab['icon'] . '"></span> ' : '';
                    $sublink = $icon . '<label>' . $subtab['title'] . '</label>';
                } else {
                    $sublink = '<label>' . $subtab . '</label>';
                }

                if ( $subtab_id === $current_section ) {
                    $sublinks[] = "<li><a class='current wcmb_sub_sction' href='?page=wcmb-setting-admin&tab=$tab_id&tab_section=$subtab_id'>$sublink</a></li>";
                } else {
                    $sublinks[] = "<li><a class='wcmb_sub_sction' href='?page=wcmb-setting-admin&tab=$tab_id&tab_section=$subtab_id'>$sublink</a></li>";
                }
            }
        }

        $links = array();
        foreach ( $this->tabs as $tab => $name ) :
            if ( $tab == $current ) :
                $links[] = "<a class='nav-tab nav-tab-active' href='?page=wcmb-setting-admin&tab=$tab'>$name</a>";
            else :
                $links[] = "<a class='nav-tab' href='?page=wcmb-setting-admin&tab=$tab'>$name</a>";
            endif;
        endforeach;


        echo '<h2 class="nav-tab-wrapper">';
        foreach ( $links as $link ) {
            echo $link;
        }
        echo '</h2>';

        $display_sublink = apply_filters( 'display_wcmb_sublink', $this->is_wcmb_tab_has_subtab( $current ), $current );
        $sublinks = apply_filters( 'wcmb_subtab', $sublinks, $current );
        if ( $display_sublink ) {
            echo '<div class="wcmb_subtab_container">';
            echo '<ul class="subsubsub wcmbsubtabadmin">';
            foreach ( $sublinks as $sublink ) {
                echo $sublink;
            }
            echo '</ul>';
        }
    }

    /**
     * Options page callback
     */
    public function create_wcmb_settings() {
        global $WCMb;
        ?>
        <div class="wrap blank-wrap"><h2></h2></div>
        <div class="wrap wcmb-settings-wrap">
            <?php $this->wcmb_settings_tabs(); ?>
            <?php
            $tab = isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ? $_GET['tab'] : current( array_keys( $this->tabs ) );

            foreach ( $this->tabs as $tab_id => $_tab ) {
                if ( $tab_id != $tab ) {
                    continue;
                }
                $tab_section = isset( $_GET['tab_section'] ) && ! empty( $_GET['tab_section'] ) ? $_GET['tab_section'] : current( array_keys( $this->get_wcmb_subtabs( $tab_id ) ) );
            }

            foreach ( $this->tabs as $tab_id => $tab_name ) {
                $this->options = array_merge( $this->options, (array) get_option( "wcmb_{$tab_id}_settings_name", array() ) );
                if ( $this->is_wcmb_tab_has_subtab( $tab_id ) ) {
                    foreach ( $this->get_wcmb_subtabs( $tab_id ) as $subtab_id => $subtab_name ) {
                        $this->options = array_merge( $this->options, get_option( "wcmb_{$tab_id}_{$subtab_id}_settings_name", array() ) );
                    }
                }
            }

            foreach ( $this->tabs as $tab_id => $tab_name ) {
                settings_errors( "wcmb_{$tab_id}_settings_name" );
                if ( $this->is_wcmb_tab_has_subtab( $tab_id ) ) {
                    foreach ( $this->get_wcmb_subtabs( $tab_id ) as $subtab_id => $subtab_name ) {
                        settings_errors( "wcmb_{$tab_id}_{$subtab_id}_settings_name" );
                    }
                }
            }
            ?>
            <form class='wcmb_vendors_settings <?php echo $this->is_wcmb_tab_has_subtab( $tab ) ? 'wcmb_subtab_content' : 'wcmb_tab_content'; ?> wcmb_<?php echo $tab; ?>_<?php echo $tab_section; ?>_settings_group' method="post" action="options.php">
                <?php
                $tab_desc = $this->get_settings_tab_desc();
                if ( ! empty( $tab_desc[$tab] ) ) {
                    echo '<h4 class="wcmb-tab-description">' . $tab_desc[$tab] . '</h4>';
                }
                ?>
                <?php
                // This prints out all hidden setting fields
                if ( $tab == 'general' && isset( $_GET['tab_section'] ) && $_GET['tab_section'] != 'general' ) {
                    settings_fields( "wcmb_{$tab}_{$tab_section}_settings_group" );
                    do_action( "wcmb_{$tab}_{$tab_section}_settings_before_submit" );
                    do_settings_sections( "wcmb-{$tab}-{$tab_section}-settings-admin" );
                    submit_button();
                } else if ( $tab == 'payment' && isset( $_GET['tab_section'] ) && $_GET['tab_section'] != 'payment' ) {
                    settings_fields( "wcmb_{$tab}_{$tab_section}_settings_group" );
                    do_action( "wcmb_{$tab}_{$tab_section}_settings_before_submit" );
                    do_settings_sections( "wcmb-{$tab}-{$tab_section}-settings-admin" );
                    submit_button();
                } else if ( $tab == 'vendor' ) {
                    settings_fields( "wcmb_{$tab}_{$tab_section}_settings_group" );
                    do_action( "wcmb_{$tab}_{$tab_section}_settings_before_submit" );
                    do_settings_sections( "wcmb-{$tab}-{$tab_section}-settings-admin" );
                    if ( $tab_section == 'registration' ) {
                        do_action( "settings_page_{$tab}_{$tab_section}_tab_init", $tab, $tab_section );
                        wp_enqueue_script( 'wcmb_angular', $WCMb->plugin_url . 'assets/admin/js/angular.min.js', array(), $WCMb->version );
                        wp_enqueue_script( 'wcmb_angular-ui', $WCMb->plugin_url . 'assets/admin/js/sortable.js', array( 'wcmb_angular' ), $WCMb->version );
                        wp_enqueue_script( 'wcmb_vendor_registration', $WCMb->plugin_url . 'assets/admin/js/vendor_registration_app.js', array( 'wcmb_angular', 'wcmb_angular-ui' ), $WCMb->version );
                        $wcmb_vendor_registration_form_data = get_option( 'wcmb_vendor_registration_form_data' );
                        wp_localize_script( 'wcmb_vendor_registration', 'vendor_registration_param', array( 'partials' => $WCMb->plugin_url . 'assets/admin/partials/', 'ajax_url' => admin_url( 'admin-ajax.php' ), 'lang' => array('need_country_dependancy' => __('Please add country field first.', 'MB-multivendor')), 'form_data' => $wcmb_vendor_registration_form_data ) );
                    } else {
                        submit_button();
                    }
                } else if ( $tab == 'capabilities' ) {
                    if ( isset( $_GET['tab_section'] ) ) {
                        $tab_section = $_GET['tab_section'];
                    } else {
                        $tab_section = 'product';
                    }
                    settings_fields( "wcmb_{$tab}_{$tab_section}_settings_group" );
                    do_action( "wcmb_{$tab}_{$tab_section}_settings_before_submit" );
                    do_settings_sections( "wcmb-{$tab}-{$tab_section}-settings-admin" );
                    submit_button();
                } else if ( $tab == 'wcmb-addons' ) {
                    do_action( "settings_page_{$tab}_tab_init", $tab );
                } else if ( isset( $_GET['tab_section'] ) && $_GET['tab_section'] && $_GET['tab_section'] != 'general' && $tab != 'general' && $tab != 'payment' ) {
                    $tab_section = $_GET['tab_section'];
                    settings_fields( "wcmb_{$tab}_{$tab_section}_settings_group" );
                    do_action( "wcmb_{$tab}_{$tab_section}_settings_before_submit" );
                    do_settings_sections( "wcmb-{$tab}-{$tab_section}-settings-admin" );
                    submit_button();
                } else {
                    settings_fields( "wcmb_{$tab}_settings_group" );
                    do_action( "wcmb_{$tab}_settings_before_submit" );
                    do_settings_sections( "wcmb-{$tab}-settings-admin" );
                    submit_button();
                }
                ?>
            </form>
            <?php echo $this->is_wcmb_tab_has_subtab( $tab ) ? '</div>' : ''; ?>
        </div>
        <?php
        //do_action( 'dualcube_admin_footer' );
    }

    public function wcmb_extensions() {
        ?>  
        <div class="wrap">
            <h1><?php _e( 'WCMb Extensions', 'MB-multivendor' ) ?></h1>
            <?php do_action( "settings_page_wcmb-addons_tab_init", 'wcmb-addons' ); ?>
            
        </div>
        <?php
    }

    public function wcmb_to_do() {
        ?>  
        <div class="wrap wcmb_vendors_settings">
            <h1><?php _e( 'To-do', 'MB-multivendor' ) ?></h1>
            <?php do_action( "settings_page_to_do_list_tab_init", 'to_do_list' ); ?>
        
        </div>
        <?php
    }
    
    public function wcmb_vendors() {
        ?>  
        <div class="wrap">
        	<?php do_action( "settings_page_vendors_tab_init", 'vendors' ); ?>
        
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        do_action( 'befor_settings_page_init' );
        foreach ( $this->tabs as $tab_id => $tab ) {
            do_action( "settings_page_{$tab_id}_tab_init", $tab_id );
            $exclude_list = apply_filters( 'wcmb_subtab_init_exclude_list', array( 'payment', 'registration' ), $tab_id );
            if ( $this->is_wcmb_tab_has_subtab( $tab_id ) ) {
                foreach ( $this->get_wcmb_subtabs( $tab_id ) as $subtab_id => $subtab ) {
                    if ( ! in_array( $subtab_id, $exclude_list ) ) {
                        do_action( "settings_page_{$tab_id}_{$subtab_id}_tab_init", $tab_id, $subtab_id );
                    }
                }
            }
        }
        do_action( 'after_settings_page_init' );
    }

    /**
     * Register and add settings fields
     */
    public function settings_field_init( $tab_options ) {
        if ( ! empty( $tab_options ) && isset( $tab_options['tab'] ) && isset( $tab_options['ref'] ) && isset( $tab_options['sections'] ) ) {
            // Register tab options
            register_setting(
                "wcmb_{$tab_options['tab']}_settings_group", // Option group
                "wcmb_{$tab_options['tab']}_settings_name", // Option name
                array( $tab_options['ref'], "wcmb_{$tab_options['tab']}_settings_sanitize" ) // Sanitize
            );

            foreach ( $tab_options['sections'] as $sectionID => $section ) {
                // Register section
                if ( method_exists( $tab_options['ref'], "{$sectionID}_info" ) ) {
                    add_settings_section(
                        $sectionID, // ID
                        $section['title'], // Title
                        array( $tab_options['ref'], "{$sectionID}_info" ), // Callback
                        "wcmb-{$tab_options['tab']}-settings-admin" // Page
                    );
                } else {
                    $callback = isset( $section['ref'] ) && method_exists( $section['ref'], "{$sectionID}_info" ) ? array( $section['ref'], "{$sectionID}_info" ) : __return_false();
                    add_settings_section(
                        $sectionID, // ID
                        $section['title'], // Title
                        $callback, // Callback
                        "wcmb-{$tab_options['tab']}-settings-admin" // Page
                    );
                }

                // Register fields
                if ( isset( $section['fields'] ) ) {
                    foreach ( $section['fields'] as $fieldID => $field ) {
                        if ( isset( $field['type'] ) ) {
                            $field['title'] = isset( $field['title'] ) ? $field['title'] : '';
                            $field['tab'] = $tab_options['tab'];
                            $callbak = $this->get_field_callback_type( $field['type'] );
                            if ( ! empty( $callbak ) ) {
                                add_settings_field(
                                    $fieldID, $field['title'], array( $this, $callbak ), "wcmb-{$tab_options['tab']}-settings-admin", $sectionID, $this->process_fields_args( $field, $fieldID )
                                );
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Register and add settings fields
     */
    public function settings_field_withsubtab_init( $tab_options ) {
        if ( ! empty( $tab_options ) && isset( $tab_options['tab'] ) && isset( $tab_options['ref'] ) && isset( $tab_options['sections'] ) && isset( $tab_options['subsection'] ) ) {
            // Register tab options
            register_setting(
                "wcmb_{$tab_options['tab']}_{$tab_options['subsection']}_settings_group", // Option group
                "wcmb_{$tab_options['tab']}_{$tab_options['subsection']}_settings_name", // Option name
                array( $tab_options['ref'], "wcmb_{$tab_options['tab']}_{$tab_options['subsection']}_settings_sanitize" ) // Sanitize
            );

            foreach ( $tab_options['sections'] as $sectionID => $section ) {
                // Register section
                if ( apply_filters( "{$tab_options['tab']}_{$sectionID}_info_display", method_exists( $tab_options['ref'], "{$sectionID}_info" ) ) ) {
                    add_settings_section(
                        $sectionID, // ID
                        $section['title'], // Title
                        array( $tab_options['ref'], "{$sectionID}_info" ), // Callback
                        "wcmb-{$tab_options['tab']}-{$tab_options['subsection']}-settings-admin" // Page
                    );
                } else {
                    $callback = isset( $section['ref'] ) && method_exists( $section['ref'], "{$sectionID}_info" ) ? array( $section['ref'], "{$sectionID}_info" ) : __return_false();
                    add_settings_section(
                        $sectionID, // ID
                        $section['title'], // Title
                        $callback, // Callback
                        "wcmb-{$tab_options['tab']}-{$tab_options['subsection']}-settings-admin" // Page
                    );
                }

                // Register fields
                if ( isset( $section['fields'] ) ) {
                    foreach ( $section['fields'] as $fieldID => $field ) {
                        if ( isset( $field['type'] ) ) {
                            $field['tab'] = $tab_options['tab'] . '_' . $tab_options['subsection'];
                            $callbak = $this->get_field_callback_type( $field['type'] );
                            if ( ! empty( $callbak ) ) {
                                add_settings_field(
                                    $fieldID, $field['title'], array( $this, $callbak ), "wcmb-{$tab_options['tab']}-{$tab_options['subsection']}-settings-admin", $sectionID, $this->process_fields_args( $field, $fieldID )
                                );
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * function process_fields_args
     * @param $fields
     * @param $fieldId
     * @return Array
     */
    public function process_fields_args( $field, $fieldID ) {
        if ( ! isset( $field['id'] ) ) {
            $field['id'] = $fieldID;
        }
        if ( ! isset( $field['label_for'] ) ) {
            $field['label_for'] = $fieldID;
        }
        if ( ! isset( $field['name'] ) ) {
            $field['name'] = $fieldID;
        }
        return $field;
    }

    public function general_tab_init( $tab ) {
        global $WCMb;
        $WCMb->admin->load_class( "settings-{$tab}", $WCMb->plugin_path, $WCMb->token );
        new WCMb_Settings_General( $tab );
    }

    public function general_policies_tab_init( $tab, $subsection ) {
        global $WCMb;
        $WCMb->admin->load_class( "settings-{$tab}-{$subsection}", $WCMb->plugin_path, $WCMb->token );
        new WCMb_Settings_General_Policies( $tab, $subsection );
    }

    public function general_customer_support_details_tab_init( $tab, $subsection ) {
        global $WCMb;
        $WCMb->admin->load_class( "settings-{$tab}-{$subsection}", $WCMb->plugin_path, $WCMb->token );
        new WCMb_Settings_General_Customer_support_Details( $tab, $subsection );
    }

    public function capabilites_product_tab_init( $tab, $subsection ) {
        global $WCMb;
        $WCMb->admin->load_class( "settings-{$tab}-{$subsection}", $WCMb->plugin_path, $WCMb->token );
        new WCMb_Settings_Capabilities_Product( $tab, $subsection );
    }

    public function capabilites_order_tab_init($tab, $subsection) {
        global $WCMb;
        $WCMb->admin->load_class("settings-{$tab}-{$subsection}", $WCMb->plugin_path, $WCMb->token);
        new WCMb_Settings_Capabilities_Order($tab, $subsection);
 }
//
//    public function capabilites_miscellaneous_tab_init($tab, $subsection) {
//        global $WCMp;
//        $WCMp->admin->load_class("settings-{$tab}-{$subsection}", $WCMp->plugin_path, $WCMp->token);
//        new WCMp_Settings_Capabilities_Miscellaneous($tab, $subsection);
//    }

    public function notices_tab_init( $tab ) {
        global $WCMb;
        $WCMb->admin->load_class( "settings-{$tab}", $WCMb->plugin_path, $WCMb->token );
        new WCMb_Settings_Notices( $tab );
    }

    public function payment_tab_init( $tab ) {
        global $WCMb;
        $WCMb->admin->load_class( "settings-{$tab}", $WCMb->plugin_path, $WCMb->token );
        new WCMb_Settings_Payment( $tab );
    }

    public function payment_paypal_masspay_init( $tab, $subsection ) {
        global $WCMb;
        $WCMb->admin->load_class( "settings-{$tab}-{$subsection}", $WCMb->plugin_path, $WCMb->token );
        new WCMb_Settings_Payment_Paypal_Masspay( $tab, $subsection );
    }

    public function payment_paypal_payout_init( $tab, $subsection ) {
        global $WCMb;
        $WCMb->admin->load_class( "settings-{$tab}-{$subsection}", $WCMb->plugin_path, $WCMb->token );
        new WCMb_Settings_Payment_Paypal_Payout( $tab, $subsection );
    }
    
    public function payment_stripe_gateway_tab_init( $tab, $subsection ) {
        global $WCMb;
        $WCMb->admin->load_class( "settings-{$tab}-{$subsection}", $WCMb->plugin_path, $WCMb->token );
        new WCMb_Settings_Payment_Stripe_Connect( $tab, $subsection );
    }

//    public function frontend_tab_init($tab) {
//        global $WCMp;
//        $WCMp->admin->load_class("settings-{$tab}", $WCMp->plugin_path, $WCMp->token);
//        new WCMp_Settings_Frontend($tab);
//    }

    public function to_do_list_tab_init( $tab ) {
        global $WCMb;
        $WCMb->admin->load_class( "settings-{$tab}", $WCMb->plugin_path, $WCMb->token );
        new WCMb_Settings_To_Do_List( $tab );
    }

    public function vendor_registration_tab_init( $tab, $subsection ) {
        global $WCMb;
        $WCMb->admin->load_class( "settings-{$tab}-{$subsection}", $WCMb->plugin_path, $WCMb->token );
        new WCMb_Settings_Vendor_Registration( $tab, $subsection );
    }

    public function vendor_dashboard_tab_init( $tab, $subsection ) {
        global $WCMb;
        $WCMb->admin->load_class( "settings-{$tab}-{$subsection}", $WCMb->plugin_path, $WCMb->token );
        new WCMb_Settings_Vendor_Dashboard( $tab, $subsection );
    }

    public function vendor_general_tab_init( $tab, $subsection ) {
        global $WCMb;
        $WCMb->admin->load_class( "settings-{$tab}-{$subsection}", $WCMb->plugin_path, $WCMb->token );
        new WCMb_Settings_Vendor_General( $tab, $subsection );
    }

    public function wcmb_addons_tab_init( $tab ) {
        global $WCMb;
        $WCMb->admin->load_class( "settings-{$tab}", $WCMb->plugin_path, $WCMb->token );
        new WCMb_Settings_WCMb_Addons( $tab );
    }
    
    public function vendors_tab_init( $tab ) {
        //global $WCMb;
        //$WCMp->admin->load_class( "settings-{$tab}", $WCMp->plugin_path, $WCMp->token );
        //new WCMp_Settings_WCMp_Vendors( $tab );
        $this->vendor_class_obj->settings_page_init();
    }


    public function is_wcmb_tab_has_subtab( $tab = 'general' ) {
        return in_array( $tab, apply_filters( 'is_wcmb_tab_has_subtab', array( 'general', 'payment', 'vendor', 'capabilities' ), $tab ) );
    }

    public function get_wcmb_subtabs( $tab = 'general' ) {
        $subtabs = array();
        switch ( $tab ) {
            case 'payment':
                $subtabs = $this->tabsection_payment;
                break;
            case 'vendor':
                $subtabs = $this->tabsection_vendor;
                break;
            case 'capabilities':
                $subtabs = $this->tabsection_capabilities;
                break;
            default :
                $subtabs = $this->tabsection_general;
                break;
        }
        return apply_filters( 'wcmb_get_subtabs', $subtabs, $tab );
    }

    public function get_field_callback_type( $fieldType ) {
        $callBack = '';
        switch ( $fieldType ) {
            case 'input':
            case 'number':
            case 'text':
            case 'email':
            case 'password':
            case 'url':
                $callBack = 'text_field_callback';
                break;

            case 'hidden':
                $callBack = 'hidden_field_callback';
                break;

            case 'textarea':
                $callBack = 'textarea_field_callback';
                break;

            case 'wpeditor':
                $callBack = 'wpeditor_field_callback';
                break;

            case 'checkbox':
                $callBack = 'checkbox_field_callback';
                break;

            case 'radio':
                $callBack = 'radio_field_callback';
                break;
            case 'radio_select':
                $callBack = 'radio_select_field_callback';
                break;
            case 'color_scheme_picker':
                $callBack = 'color_scheme_picker_callback';
                break;

            case 'select':
                $callBack = 'select_field_callback';
                break;

            case 'upload':
                $callBack = 'upload_field_callback';
                break;

            case 'colorpicker':
                $callBack = 'colorpicker_field_callback';
                break;

            case 'datepicker':
                $callBack = 'datepicker_field_callback';
                break;

            case 'multiinput':
                $callBack = 'multiinput_callback';
                break;

            case 'label':
                $callBack = 'label_callback';
                break;

            default:
                $callBack = '';
                break;
        }

        return $callBack;
    }

    /**
     * Get the hidden field display
     */
    public function hidden_field_callback( $field ) {
        global $WCMb;
        $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
        $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
        $field['name'] = "wcmb_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMb->wcmb_wp_fields->hidden_input( $field );
    }

    /**
     * Get the text field display
     */
    public function text_field_callback( $field ) {
        global $WCMb;
        $field['dfvalue'] = isset( $field['dfvalue'] ) ? esc_attr( $field['dfvalue'] ) : '';
        $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : $field['dfvalue'];
        $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
        $field['name'] = "wcmb_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMb->wcmb_wp_fields->text_input( $field );
    }

    /**
     * Get the label field display
     */
    public function label_callback( $field ) {
        global $WCMb;
        $field['dfvalue'] = isset( $field['dfvalue'] ) ? esc_attr( $field['dfvalue'] ) : '';
        $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : $field['dfvalue'];
        $WCMb->wcmb_wp_fields->label_input( $field );
    }

    /**
     * Get the text area display
     */
    public function textarea_field_callback( $field ) {
        global $WCMb;
        $field['value'] = isset( $field['value'] ) ? esc_textarea( $field['value'] ) : '';
        $field['value'] = isset( $this->options[$field['name']] ) ? esc_textarea( $this->options[$field['name']] ) : $field['value'];
        $field['name'] = "wcmb_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMb->wcmb_wp_fields->textarea_input( $field );
    }

    /**
     * Get the wpeditor display
     */
    public function wpeditor_field_callback( $field ) {
        global $WCMb;
        $field['value'] = isset( $field['value'] ) ? ( $field['value'] ) : '';
        $field['value'] = isset( $this->options[$field['name']] ) ? ( $this->options[$field['name']] ) : $field['value'];
        $field['name'] = "wcmb_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMb->wcmb_wp_fields->wpeditor_input( $field );
    }

    /**
     * Get the checkbox field display
     */
    public function checkbox_field_callback( $field ) {
        global $WCMb;
        $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
        $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
        $field['dfvalue'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : '';
        $field['name'] = "wcmb_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMb->wcmb_wp_fields->checkbox_input( $field );
    }

    /**
     * Get the checkbox field display
     */
    public function radio_field_callback( $field ) {
        global $WCMb;
        $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
        $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
        $field['name'] = "wcmb_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMb->wcmb_wp_fields->radio_input( $field );
    }

    /**
     * Get the checkbox field display
     */
    public function radio_select_field_callback( $field ) {
        global $WCMb;
        $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
        $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
        $field['name'] = "wcmb_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMb->wcmb_wp_fields->radio_select_input( $field );
    }

    public function color_scheme_picker_callback( $field ) {
        global $WCMb;
        $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
        $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
        $field['name'] = "wcmb_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMb->wcmb_wp_fields->color_scheme_picker_input( $field );
    }

    /**
     * Get the select field display
     */
    public function select_field_callback( $field ) {
        global $WCMb;
        $field['value'] = isset( $field['value'] ) ? esc_textarea( $field['value'] ) : '';
        $field['value'] = isset( $this->options[$field['name']] ) ? esc_textarea( $this->options[$field['name']] ) : $field['value'];
        $field['name'] = "wcmb_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMb->wcmb_wp_fields->select_input( $field );
    }

    /**
     * Get the upload field display
     */
    public function upload_field_callback( $field ) {
        global $WCMb;
        $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
        $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
        $field['name'] = "wcmb_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMb->wcmb_wp_fields->upload_input( $field );
    }

    /**
     * Get the multiinput field display
     */
    public function multiinput_callback( $field ) {
        global $WCMb;
        $field['value'] = isset( $field['value'] ) ? $field['value'] : array();
        $field['value'] = isset( $this->options[$field['name']] ) ? $this->options[$field['name']] : $field['value'];
        $field['name'] = "wcmb_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMb->wcmb_wp_fields->multi_input( $field );
    }

    /**
     * Get the colorpicker field display
     */
    public function colorpicker_field_callback( $field ) {
        global $WCMb;
        $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
        $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
        $field['name'] = "wcmb_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMb->wcmb_wp_fields->colorpicker_input( $field );
    }

    /**
     * Get the datepicker field display
     */
    public function datepicker_field_callback( $field ) {
        global $WCMb;
        $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
        $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
        $field['name'] = "wcmb_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMb->wcmb_wp_fields->datepicker_input( $field );
    }

}

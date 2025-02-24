<?php

/**
 * wcmb Main Class
 *
 * @version		2.2.0
 * @package		wcmb
 * @author 		WC Marketplace
 */
if (!defined('ABSPATH')) {
    exit;
}

final class WCMb {

    public $plugin_url;
    public $plugin_path;
    public $version;
    public $token;
    public $text_domain;
    public $library;
    public $shortcode;
    public $admin;
    public $endpoints;
    public $frontend;
    public $vendor_hooks;
    public $template;
    public $ajax;
    public $taxonomy;
    public $product;
    private $file;
    public $settings;
    public $wcmb_wp_fields;
    public $user;
    public $vendor_caps;
    public $vendor_dashboard;
    public $transaction;
    public $email;
    public $review_rating;
    public $coupon;
    public $more_product_array = array();
    public $payment_gateway;
    public $wcmb_frontend_lib;
    public $cron_job;
    public $product_qna;
    public $commission;
    public $shipping_gateway;

    /**
     * Class construct
     * @param object $file
     */
    public function __construct($file) {
        $this->file = $file;
        $this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
        $this->plugin_path = trailingslashit(dirname($file));
        $this->token = WCMb_PLUGIN_TOKEN;
        $this->text_domain = WCMb_TEXT_DOMAIN;
        $this->version = WCMb_PLUGIN_VERSION;

        // Intialize wcmb Widgets
        $this->init_custom_widgets();
        // Intialize Stripe library
        $this->init_stripe_library();
        // Init payment gateways
        $this->init_payment_gateway();

        // Intialize Crons
        $this->init_cron_job();
        // Load Woo helper
        $this->load_woo_helper();

        // Intialize wcmb
        add_action('init', array(&$this, 'init'));

        add_action('admin_init', array(&$this, 'wcmb_admin_init'));
        
        // wcmb Update Notice
        add_action('in_plugin_update_message-MB-multivendor/dc_product_vendor.php', array(&$this, 'wcmb_plugin_update_message'));

        // Secure commission notes
        add_filter('comments_clauses', array(&$this, 'exclude_order_comments'), 10, 1);
        add_filter('comment_feed_where', array(&$this, 'exclude_order_comments_from_feed_where'));
        
        // Add wcmb namespace support along with WooCommerce.
        add_filter( 'woocommerce_rest_is_request_to_rest_api', 'wcmb_namespace_approve', 10, 1 );
        // Load Vendor Shipping
        if( !defined('WP_ALLOW_MULTISITE')){
            add_action( 'woocommerce_loaded', array( &$this, 'load_vendor_shipping' ) );
        }else{
            $this->load_vendor_shipping();
        }
    }
    
    public function exclude_order_comments($clauses) {
        $clauses['where'] .= ( $clauses['where'] ? ' AND ' : '' ) . " comment_type != 'commission_note' ";
        return $clauses;
    }

    public function exclude_order_comments_from_feed_where($where) {
        return $where . ( $where ? ' AND ' : '' ) . " comment_type != 'commission_note' ";
    }

    /**
     * Initialize plugin on WP init
     */
    function init() {
        if (is_user_wcmb_pending_vendor(get_current_vendor_id()) || is_user_wcmb_rejected_vendor(get_current_vendor_id()) || is_user_wcmb_vendor(get_current_vendor_id())) {
            show_admin_bar(apply_filters('wcmb_show_admin_bar', false));
        }
        // Init Text Domain
        $this->load_plugin_textdomain();
        // Init library
        $this->load_class('library');
        $this->library = new WCMb_Library();

        $this->wcmb_frontend_fields = $this->library->load_wcmb_frontend_fields();

        //Init endpoints
        $this->load_class('endpoints');
        $this->endpoints = new WCMb_Endpoints();
        // Init custom capabilities
        $this->init_custom_capabilities();

        // Init product vendor custom post types
        $this->init_custom_post();

        $this->load_class('payment-gateways');
        $this->payment_gateway = new WCMb_Payment_Gateways();

        $this->load_class('seller-review-rating');
        $this->review_rating = new WCMb_Seller_Review_Rating();
        // Init ajax
        if (defined('DOING_AJAX')) {
            $this->load_class('ajax');
            $this->ajax = new WCMb_Ajax();
        }
        // Init main admin action class 
        if (is_admin()) {
            $this->load_class('admin');
            $this->admin = new WCMb_Admin();
        }
        if (!is_admin() || defined('DOING_AJAX')) {
            // Init main frontend action class
            $this->load_class('frontend');
            $this->frontend = new WCMb_Frontend();
            // Init shortcode
            $this->load_class('shortcode');
            $this->shortcode = new WCMb_Shortcode();
            //Vendor Dashboard Hooks
            $this->load_class('vendor-hooks');
            $this->vendor_hooks = new WCMb_Vendor_Hooks();
        }
        // Init templates
        $this->load_class('template');
        $this->template = new WCMb_Template();
        add_filter('template_include', array($this, 'template_loader'), 15);
        // Init vendor action class
        $this->load_class('vendor-details');
        // Init Calculate commission class
        $this->load_class('calculate-commission');
        $this->commission = new WCMb_Calculate_Commission();
        // Init product vendor taxonomies
        $this->init_taxonomy();
        // Init product action class 
        $this->load_class('product');
        $this->product = new WCMb_Product();
        // Init Product QNA
        $this->load_class('product-qna');
        $this->product_qna = new WCMb_Product_QNA();
        // Init email activity action class 
        $this->load_class('email');
        $this->email = new WCMb_Email();
        // wcmb Fields Lib
        $this->wcmb_wp_fields = $this->library->load_wp_fields();
        // Load Jquery style
        $this->library->load_jquery_style_lib();
        // Init user roles
        $this->init_user_roles();

        // Init custom reports
        $this->init_custom_reports();

        // Init vendor dashboard
        $this->init_vendor_dashboard();
        // Init vendor coupon
        $this->init_vendor_coupon();
        
        // Init wcmb API
        $this->init_wcmb_rest_api();
        
        if (!wp_next_scheduled('migrate_spmv_multivendor_table') && !get_option('spmv_multivendor_table_migrated', false)) {
            wp_schedule_event(time(), 'every_5minute', 'migrate_spmv_multivendor_table');
        }
        do_action('wcmb_init');
    }
    
    // Initializing Rest API
    function init_wcmb_rest_api() {
		include_once ($this->plugin_path . "/api/class-wcmb-rest-controller.php" );
		new WCMb_REST_API();
	}

    /**
     * plugin admin init callback
     */
    function wcmb_admin_init() {
        $previous_plugin_version = get_option('dc_product_vendor_plugin_db_version');
        /* Migrate wcmb data */
        do_wcmb_data_migrate($previous_plugin_version, $this->version);
    }

    /**
     * Load vendor shop page template
     * @param type $template
     * @return type
     */
    function template_loader($template) {
        global $WCMb;
        if (is_tax($WCMb->taxonomy->taxonomy_name)) {
            $template = $this->template->locate_template('taxonomy-dc_vendor_shop.php');
        }
        return $template;
    }

    /**
     * Load Localisation files.
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present
     *
     * @access public
     * @return void
     */
    public function load_plugin_textdomain() {
        $locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
        $locale = apply_filters('plugin_locale', $locale, 'MB-multivendor');
        load_textdomain('MB-multivendor', WP_LANG_DIR . '/MB-multivendor/MB-multivendor-' . $locale . '.mo');
        load_plugin_textdomain('MB-multivendor', false, plugin_basename(dirname(dirname(__FILE__))) . '/languages');
    }

    /**
     * Helper method to load other class
     * @param type $class_name
     * @param type $dir
     */
    public function load_class($class_name = '', $dir = '') {
        if ('' != $class_name && '' != $this->token) {
            if(!$dir)
                require_once ( 'class-' . esc_attr($this->token) . '-' . esc_attr($class_name) . '.php' );
            else
                require_once ( trailingslashit( $dir ) . 'class-' . esc_attr($this->token) . '-' . strtolower($dir) . '-' . esc_attr($class_name) . '.php' );
        }
    }

    /**
     * Sets a constant preventing some caching plugins from caching a page. Used on dynamic pages
     *
     * @access public
     * @return void
     */
    function nocache() {
        if (!defined('DONOTCACHEPAGE')) {
            // WP Super Cache constant
            define("DONOTCACHEPAGE", "true");
        }
    }

    /**
     * Get Ajax URL.
     *
     * @return string
     */
    public function ajax_url() {
        return admin_url('admin-ajax.php', 'relative');
    }

    /**
     * Init wcmb User and define users roles
     *
     * @access public
     * @return void
     */
    function init_user_roles() {
        $this->load_class('user');
        $this->user = new WCMb_User();
    }

    /**
     * Init wcmb product vendor taxonomy.
     *
     * @access public
     * @return void
     */
    function init_taxonomy() {
        $this->load_class('taxonomy');
        $this->taxonomy = new WCMb_Taxonomy();
        register_activation_hook(__FILE__, 'flush_rewrite_rules');
    }

    /**
     * Init wcmbwcmb product vendor post type.
     *
     * @access public
     * @return void
     */
    function init_custom_post() {
        /* Commission post type */
        $this->load_class('post-commission');

        new WCMb_Commission();
        /* transaction post type */
        $this->load_class('post-transaction');
        $this->transaction = new WCMb_Transaction();
        /* wcmb notice post type */
        //$this->load_class('post-notices');
        //new WCMb_Notices();
        /* University post type */
        //$this->load_class('post-university');
        //new WCMb_University();
        /* Flush wp rewrite rule and update permalink structure */
        register_activation_hook(__FILE__, 'flush_rewrite_rules');
    }

    /**
     * Init wcmb vendor reports.
     *
     * @access public
     * @return void
     */
    function init_custom_reports() {
        // Init custom report
        $this->load_class('report');
        new WCMb_Report();
    }

    /**
     * Init wcmb vendor widgets.
     *
     * @access public
     * @return void
     */
    function init_custom_widgets() {
        $this->load_class('widget-init');
        new WCMb_Widget_Init();
    }

    /**
     * Init wcmb vendor capabilities.
     *
     * @access public
     * @return void
     */
    function init_custom_capabilities() {
        $this->load_class('capabilities');
        $this->vendor_caps = new WCMb_Capabilities();
    }

    /**
     * Init wcmb Dashboard Function
     *
     * @access public
     * @return void
     */
    function init_vendor_dashboard() {
        $this->load_class('vendor-dashboard');
        $this->vendor_dashboard = new WCMb_Admin_Dashboard();
    }

    /**
     * Init Cron Job
     * 
     * @access public
     * @return void
     */
    function init_cron_job() {
        add_filter('cron_schedules', array($this, 'add_wcmb_corn_schedule'));
        $this->load_class('cron-job');
        $this->cron_job = new WCMb_Cron_Job();
    }

    private function init_payment_gateway() {
        $this->load_class('payment-gateway');
    }
    
    /**
     * wcmb Shipping
     * 
     * Load vendor shipping
     * @since  3.2.2 
     * @access public
     * @package wcmb/Classes/Shipping
    */
    public function load_vendor_shipping() {
        $this->load_class( 'shipping-gateway' );
        $this->shipping_gateway = new WCMb_Shipping_Gateway();
        WCMb_Shipping_Gateway::load_class( 'shipping-zone', 'helpers' );
    }
    
    /**
    
    */
    public function load_woo_helper(){
        //common woo methods
        if ( ! class_exists( 'WCMb_Woo_Helper' ) ) {
            require_once ( $this->plugin_path . 'includes/class-wcmb-woo-helper.php' );
        }
    }

    /**
     * Init Vendor Coupon
     *
     * @access public
     * @return void
     */
    function init_vendor_coupon() {
        $this->load_class('coupon');
        $this->coupon = new WCMb_Coupon();
    }

    /**
     * Add wcmb weekly and monthly corn schedule
     *
     * @access public
     * @param schedules array
     * @return schedules array
     */
    function add_wcmb_corn_schedule($schedules) {
        $schedules['weekly'] = array(
            'interval' => 604800,
            'display' => __('Every 7 Days', $this->text_domain)
        );
        $schedules['monthly'] = array(
            'interval' => 2592000,
            'display' => __('Every 1 Month', $this->text_domain)
        );
        $schedules['fortnightly'] = array(
            'interval' => 1296000,
            'display' => __('Every 15 Days', $this->text_domain)
        );
        $schedules['every_5minute'] = array(
                'interval' => 5*60, // in seconds
                'display'  => __( 'Every 5 minute', $this->text_domain )
        );
        
        return $schedules;
    }

    /**
     * Return data for script handles.
     * @since  3.0.6 
     * @param  string $handle
     * @param  array $default params
     * @return array|bool
     */
    public function wcmb_get_script_data($handle, $default) {
        global $WCMb;

        switch ($handle) {
            case 'frontend_js' :
                $params = array(
                    'ajax_url' => $this->ajax_url(),
                    'messages' => array('confirm_dlt_pro' => __("Are you sure and want to delete this Product?\nYou can't undo this action ...", 'MB-multivendor')),
                );
                break;
            
            case 'wcmb_frontend_vdashboard_js' :
            case 'wcmb_single_product_multiple_vendors' :
            case 'wcmb_customer_qna_js' :
            case 'wcmb_new_vandor_announcements_js' :
                $params = array(
                    'ajax_url' => $this->ajax_url(),
                );
                break;
            
            case 'wcmb_seller_review_rating_js' :
                $params = array(
                    'ajax_url' => $this->ajax_url(),
                    'messages' => array(
                        'rating_error_msg_txt' => __('Please rate the vendor', 'MB-multivendor'),
                        'review_error_msg_txt' => __('Please review your vendor and minimum 10 Character required', 'MB-multivendor'),
                        'review_success_msg_txt' => __('Your review submitted successfully', 'MB-multivendor'),
                        'review_failed_msg_txt' => __('Error in system please try again later', 'MB-multivendor'),
                    ),
                );
                break;
            
            case 'wcmb-vendor-shipping' :
            case 'wcmb_vendor_shipping' :    
                $params = array(
                    'ajaxurl'	=> $this->ajax_url(),
                    'i18n' 	=> array(
			'deleteShippingMethodConfirmation'	=> __( 'Are you absolutely sure to delete this shipping method?', 'MB-multivendor' ),
                    ),
                );
                break;

            default:
                $params = array('ajax_url' => $this->ajax_url());
        }
        if($default && is_array($default)) $params = array_merge($default,$params);
        return apply_filters('wcmb_get_script_data', $params, $handle);
    }

    /**
     
     */
    public function localize_script($handle, $params = array(), $object = '') {
        if ( $data = $this->wcmb_get_script_data($handle, $params) ) {
            $name = str_replace('-', '_', $handle) . '_script_data';
            if($object){
                $name = str_replace('-', '_', $object) . '_script_data';
            }
            wp_localize_script($handle, $name, apply_filters($name, $data));
        }
    }
    
    /**
     * init Stripe library.
     *
     * @access public
     */
    public function init_stripe_library(){
        global $WCMb;
        $load_library = (get_wcmb_vendor_settings('payment_method_stripe_masspay', 'payment') == 'Enable') ? true : false;
        if(apply_filters('wcmb_load_stripe_library', $load_library)){
            $stripe_dependencies = WC_Dependencies_Product_Vendor::stripe_dependencies();
            if($stripe_dependencies['status']){
                $load_library = (get_wcmb_vendor_settings('payment_method_stripe_masspay', 'payment') == 'Enable') ? true : false;
                if(!class_exists("Stripe\Stripe")) {
                    require_once( $this->plugin_path . 'lib/Stripe/init.php' );
                }
            }else{
                switch ($stripe_dependencies['module']) {
                    case 'phpversion':
                        add_action('admin_notices', array($this, 'wcmb_stripe_phpversion_required_notice'));
                        break;
                    case 'curl':
                        add_action('admin_notices', array($this, 'wcmb_stripe_curl_required_notice'));
                        break;
                    case 'mbstring':
                        add_action('admin_notices', array($this, 'wcmb_stripe_mbstring_required_notice'));
                        break;
                    case 'json':
                        add_action('admin_notices', array($this, 'wcmb_stripe_json_required_notice'));
                        break;
                    default:
                        break;
                }
            }
        }
    }
    
    public function wcmb_stripe_phpversion_required_notice() {
        ?>
        <div id="message" class="error">
            <p><?php printf(__("%sWCMb Stripe Gateway%s requires PHP 5.3.29 or greater. We recommend upgrading to PHP %s or greater.", 'MB-multivendor' ), '<strong>', '</strong>', '5.6' ); ?></p>
        </div>
        <?php
    }
    
    public function wcmb_stripe_curl_required_notice() {
        ?>
        <div id="message" class="error">
            <p><?php printf(__("%sWCMb Stripe gateway depends on the %s PHP extension. Please enable it, or ask your hosting provider to enable it.", 'MB-multivendor' ), '<strong>', '</strong>', 'curl' ); ?></p>
        </div>
        <?php
    }
    
    public function wcmb_stripe_mbstring_required_notice() {
        ?>
        <div id="message" class="error">
            <p><?php printf(__("%sWCMb Stripe gateway depends on the %s PHP extension. Please enable it, or ask your hosting provider to enable it.", 'MB-multivendor' ), '<strong>', '</strong>', 'mbstring' ); ?></p>
        </div>
        <?php
    }
    
    public function wcmb_stripe_json_required_notice() {
        ?>
        <div id="message" class="error">
            <p><?php printf(__("%sWCMb Vendor Membership Stripe gateway depends on the %s PHP extension. Please enable it, or ask your hosting provider to enable it.", 'MB-multivendor' ), '<strong>', '</strong>', 'json' ); ?></p>
        </div>
        <?php
    }

    /**
     * Show plugin changes. Code adapted from W3 Total Cache and Woocommerce.
     */
    public static function wcmb_plugin_update_message($args) {
        $transient_name = 'wcmb_upgrade_notice_' . $args['Version'];
        if (false === ( $upgrade_notice = get_transient($transient_name) )) {
            $response = wp_safe_remote_get('https://plugins.svn.wordpress.org/MB-multivendor/trunk/readme.txt');
            if (!is_wp_error($response) && !empty($response['body'])) {
                $upgrade_notice = self::parse_update_notice($response['body'], $args['new_version']);
                set_transient($transient_name, $upgrade_notice, DAY_IN_SECONDS);
            }
        }
        echo '<style type="text/css">.wcmb_plugin_upgrade_notice{background-color:#ec4e2a;padding:10px;color:#fff;}.wcmb_plugin_upgrade_notice:before{content: "\f534";padding-right:5px;}</style>';
        echo wp_kses_post($upgrade_notice);
    }

    /**
     * Parse update notice from readme file.
     * Code adapted from W3 Total Cache and Woocommerce
     * 
     * @param  string $content
     * @param  string $new_version
     * @return string
     */
    private static function parse_update_notice($content, $new_version) {
        // Output Upgrade Notice.
        $matches = null;
        $regexp = '~==\s*Upgrade Notice\s*==\s*=\s*(.*)\s*=(.*)(=\s*' . preg_quote(WCMb_PLUGIN_VERSION) . '\s*=|$)~Uis';
        $upgrade_notice = '';

        if (preg_match($regexp, $content, $matches)) {
            $notices = (array) preg_split('~[\r\n]+~', trim($matches[2]));

            // Convert the full version strings to minor versions.
            $notice_version_parts = explode('.', trim($matches[1]));
            $current_version_parts = explode('.', WCMb_PLUGIN_VERSION);

            if (3 !== sizeof($notice_version_parts)) {
                return;
            }

            $notice_version = $notice_version_parts[0] . '.' . $notice_version_parts[1];
            $current_version = $current_version_parts[0] . '.' . $current_version_parts[1];

            // Check the latest stable version and ignore trunk.
            if (version_compare($current_version, $notice_version, '<')) {

                $upgrade_notice .= '<div class="wcmb_plugin_upgrade_notice dashicons-before">';

                foreach ($notices as $index => $line) {
                    $upgrade_notice .= preg_replace('~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $line);
                }

                $upgrade_notice .= '</div> ';
            }
        }

        return wp_kses_post($upgrade_notice);
    }

}

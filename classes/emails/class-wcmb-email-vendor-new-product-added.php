<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!class_exists('WC_Email_Vendor_New_Product_Added')) :

    /**
     
     */
    class WC_Email_Vendor_New_Product_Added extends WC_Email {

        var $user_login;
        var $user_email;
        var $user_pass;

        /**
         * Constructor
         *
         * @access public
         * @return void
         */
        function __construct() {
            global $WCMb;

            $this->id = 'admin_new_vendor_product';

            $this->title = __('New Vendor Product', 'MB-multivendor');
            $this->description = __('Notification emails are sent when a new product is submitted by a vendor.', 'MB-multivendor');

            //$this->heading = __('New product submitted: {product_name}', 'MB-multivendor');
            //$this->subject = __('[{blogname}] New product submitted by {vendor_name} - {product_name}', 'MB-multivendor');

            $this->template_base = $WCMb->plugin_path . 'templates/';
            $this->template_html = 'emails/new-product.php';
            $this->template_plain = 'emails/plain/new-product.php';


            // Call parent constuctor
            parent::__construct();
            // Other settings
            $this->recipient = $this->get_option('recipient');

            if (!$this->recipient)
                $this->recipient = get_option('admin_email');
        }

        /**
         * trigger function.
         *
         * @access public
         * @return void
         *
         * @param unknown $order_id
         */
        function trigger($post_id, $post, $vendor) {
            global $WCMb;

            if (!$this->is_enabled())
                return;
            
            $this->object = $post;
            $this->find[] = '{product_name}';
            $this->product_name = $post->post_title;
            $this->replace[] = $this->product_name;

            $this->find[] = '{vendor_name}';
            $this->vendor_name = $vendor->page_title;
            $this->replace[] = $this->vendor_name;

            $this->post_id = $post->ID;

            $post_type = get_post_type($this->post_id);
            $this->post_type = $post_type;
            if ($post_type == 'shop_coupon') {
                $this->title = __('New Vendor Coupon', 'MB-multivendor');
                $this->description = __('Notification emails are sent when a new coupon is submitted by a vendor.', 'MB-multivendor');

                $this->heading = apply_filters('wcmb_admin_new_vendor_coupon_email_heading',__('New coupon submitted: {product_name}', 'MB-multivendor'));
                $this->subject = apply_filters('wcmb_admin_new_vendor_coupon_email_subject',__('[{blogname}] New coupon submitted by {vendor_name} - {product_name}', 'MB-multivendor'));
            }

            $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
        }
        
        /**
         * Get email subject.
         *
         * @access  public
         * @return string
         */
        public function get_default_subject() {
            return apply_filters('wcmb_admin_new_vendor_product_email_subject', __('[{blogname}] New product submitted by {vendor_name} - {product_name}', 'MB-multivendor'), $this->object);
        }

        /**
         * Get email heading.
         *
         * @access  public
         * @return string
         */
        public function get_default_heading() {
            return apply_filters('wcmb_admin_new_vendor_product_email_heading', __('New product submitted: {product_name}', 'MB-multivendor'), $this->object);
        }

        /**
         * get_content_html function.
         *
         * @access public
         * @return string
         */
        function get_content_html() {
            ob_start();
            wc_get_template($this->template_html, array(
                'product_name' => $this->product_name,
                'vendor_name' => $this->vendor_name,
                'post_id' => $this->post_id,
                'post_type' => $this->post_type,
                'email_heading' => $this->get_heading(),
                'email'         => $this,
                    ), 'dc-product-vendor/', $this->template_base);

            return ob_get_clean();
        }

        /**
         * get_content_plain function.
         *
         * @access public
         * @return string
         */
        function get_content_plain() {
            ob_start();
            wc_get_template($this->template_plain, array(
                'product_name' => $this->product_name,
                'vendor_name' => $this->vendor_name,
                'post_id' => $this->post_id,
                'post_type' => $this->post_type,
                'email_heading' => $this->get_heading(),
                'email'         => $this,
                    ), 'dc-product-vendor/', $this->template_base);

            return ob_get_clean();
        }

        /**
         * Initialise Settings Form Fields
         *
         * @access public
         * @return void
         */
        function init_form_fields() {
            global $WCMb;
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'MB-multivendor'),
                    'type' => 'checkbox',
                    'label' => __('Enable this email notification.', 'MB-multivendor'),
                    'default' => 'yes'
                ),
                'recipient' => array(
                    'title' => __('Recipient(s)', 'MB-multivendor'),
                    'type' => 'text',
                    'description' => sprintf(__('Enter recipient(s) (comma separated) for this email. Defaults to <code>%s</code>.', 'MB-multivendor'), esc_attr(get_option('admin_email'))),
                    'placeholder' => '',
                    'default' => ''
                ),
                'subject' => array(
                    'title' => __('Subject', 'MB-multivendor'),
                    'type' => 'text',
                    'description' => sprintf(__('This controls the email subject line. Leave it blank to use the default subject: <code>%s</code>.', 'MB-multivendor'), $this->get_default_subject()),
                    'placeholder' => '',
                    'default' => ''
                ),
                'heading' => array(
                    'title' => __('Email Heading', 'MB-multivendor'),
                    'type' => 'text',
                    'description' => sprintf(__('This controls the main heading contained within the email notification. Leave it blank to use the default heading: <code>%s</code>.', 'MB-multivendor'), $this->get_default_heading()),
                    'placeholder' => '',
                    'default' => ''
                ),
                'email_type' => array(
                    'title' => __('Email Type', 'MB-multivendor'),
                    'type' => 'select',
                    'description' => __('Choose which format of email to be sent.', 'MB-multivendor'),
                    'default' => 'html',
                    'class' => 'email_type',
                    'options' => array(
                        'plain' => __('Plain Text', 'MB-multivendor'),
                        'html' => __('HTML', 'MB-multivendor'),
                        'multipart' => __('Multipart', 'MB-multivendor'),
                    )
                )
            );
        }

    }

    

endif;

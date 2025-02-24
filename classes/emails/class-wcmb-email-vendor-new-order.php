<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!class_exists('WC_Email_Vendor_New_Order')) :

    /**
     * New Order Email
     *
     * An email sent to the admin when a new order is received/paid for.
     *
     * @class 		WC_Email_New_Order
     * @version		2.0.0
     * @package		WooCommerce/Classes/Emails
     * @author 		WooThemes
     * @extends 	WC_Email
     */
    class WC_Email_Vendor_New_Order extends WC_Email {
        public $order;
        /**
         * Constructor
         */
        function __construct() {
            global $WCMb;
            $this->id = 'vendor_new_order';
            $this->title = __('Vendor New order', 'MB-multivendor');
            $this->description = __('New order notification emails are sent when order is processing.', 'MB-multivendor');

            //$this->heading = __('New Vendor Order', 'MB-multivendor');
            //$this->subject = __('[{site_title}] New vendor order ({order_number}) - {order_date}', 'MB-multivendor');

            $this->template_html = 'emails/vendor-new-order.php';
            $this->template_plain = 'emails/plain/vendor-new-order.php';
            $this->template_base = $WCMb->plugin_path . 'templates/';

            // Call parent constructor
            parent::__construct();
        }

        /**
         * Get email subject.
         *
         * @since  3.1.0
         * @return string
         */
        public function get_default_subject() {
            return apply_filters('wcmb_vendor_new_order_email_subject', __('[{site_title}] New vendor order ({order_number}) - {order_date}', 'MB-multivendor'), $this->object);
        }

        /**
         * Get email heading.
         *
         * @since  3.1.0
         * @return string
         */
        public function get_default_heading() {
            return apply_filters('wcmb_vendor_new_order_email_heading', __('New vendor order', 'MB-multivendor'), $this->object);
        }

        /**
         * trigger function.
         *
         * @access public
         * @return void
         */
        function trigger($order_id) {
            $vendors = get_vendor_from_an_order($order_id);

            if ($vendors) {
                foreach ($vendors as $vendor) {

                    $vendor_obj = get_wcmb_vendor_by_term($vendor);
                    $vendor_email = $vendor_obj->user_data->user_email;
                    $vendor_id = $vendor_obj->id;

                    if ($order_id && $vendor_email) {
                        $this->object = $this->order = wc_get_order($order_id);

                        $this->find[] = '{order_date}';
                        $this->replace[] = date_i18n(wc_date_format(), strtotime($this->order->get_date_created()));

                        $this->find[] = '{order_number}';
                        $this->replace[] = $this->order->get_order_number();
                        $this->vendor_email = $vendor_email;
                        $this->vendor_id = $vendor_id;
                        $this->recipient = $vendor_email;
                    }
                    
                    if (!$this->is_enabled() || !$this->get_recipient()) {
                        return;
                    }

                    $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
                }
            }
        }

        /**
         * get_content_html function.
         *
         * @access public
         * @return string
         */
        function get_content_html() {
            return wc_get_template_html($this->template_html, array(
                'email_heading' => $this->get_heading(),
                'vendor_id' => $this->vendor_id,
                'order' => $this->order,
                'blogname' => $this->get_blogname(),
                'sent_to_admin' => false,
                'plain_text' => false,
                'email'         => $this,
                    ), 'dc-product-vendor/', $this->template_base);
        }

        /**
         * get_content_plain function.
         *
         * @access public
         * @return string
         */
        function get_content_plain() {
            return wc_get_template_html($this->template_plain, array(
                'email_heading' => $this->get_heading(),
                'vendor_id' => $this->vendor_id,
                'order' => $this->order,
                'blogname' => $this->get_blogname(),
                'sent_to_admin' => false,
                'plain_text' => true,
                'email'         => $this,
                    ), 'dc-product-vendor/', $this->template_base);
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

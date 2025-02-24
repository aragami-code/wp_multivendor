<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WC_Email_Vendor_Commission_Transactions')) :

    /**

     */
    class WC_Email_Vendor_Commission_Transactions extends WC_Email {

        /**
         * Constructor
         */
        function __construct() {
            global $WCMb;
            $this->id = 'vendor_commissions_transaction';
            $this->title = __('Transactions (for Vendor)', 'MB-multivendor');
            $this->description = __('New commissions have been credited to vendor.', 'MB-multivendor');

            //$this->heading = __(' Transaction Details', 'MB-multivendor');
            //$this->subject = __('[{site_title}] The transaction you requested was successfully completed.', 'MB-multivendor');

            $this->template_base = $WCMb->plugin_path . 'templates/';
            $this->template_html = 'emails/vendor-commissions-transaction.php';
            $this->template_plain = 'emails/plain/vendor-commissions-transaction.php';


            // Call parent constructor
            parent::__construct();
        }

        /**
         * trigger function.
         *
         * @access public
         *
         * @param Commission $commission Commission paid
         */
        function trigger($trans_id, $vendor_term_id) {

            if (!isset($trans_id) && !isset($vendor_term_id))
                return;
            
            $this->object = get_post($trans_id);
            $this->vendor = get_wcmb_vendor_by_term($vendor_term_id);
            $commissions = get_post_meta($trans_id, 'commission_detail', true);
            $this->commissions = $commissions;
            $this->transaction_id = $trans_id;
            $this->recipient = $this->vendor->user_data->user_email;
            if ( $this->is_enabled() && $this->get_recipient() ) {
                $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
            }
        }
        
        /**
         * Get email subject.
         *
         * @access  public
         * @return string
         */
        public function get_default_subject() {
            return apply_filters('wcmb_vendor_commissions_transaction_email_subject', __('[{site_title}] The transaction you requested was successfully completed.', 'MB-multivendor'), $this->object);
        }

        /**
         * Get email heading.
         *
         * @access  public
         * @return string
         */
        public function get_default_heading() {
            return apply_filters('wcmb_vendor_commissions_transaction_email_heading', __(' Transaction Details', 'MB-multivendor'), $this->object);
        }

        /**
         * get_content_html function.
         *
         * @access public
         * @return string
         */
        function get_content_html() {
            global $WCMb;
            ob_start();
            wc_get_template($this->template_html, array(
                'commissions' => $this->commissions,
                'email_heading' => $this->get_heading(),
                'vendor' => $this->vendor,
                'transaction_id' => $this->transaction_id,
                'sent_to_admin' => false,
                'plain_text' => false,
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
                'commissions' => $this->commissions,
                'email_heading' => $this->get_heading(),
                'vendor' => $this->vendor,
                'transaction_id' => $this->transaction_id,
                'sent_to_admin' => false,
                'plain_text' => true,
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
                    'label' => __('Enable notification for this email', 'MB-multivendor'),
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
                    'description' => sprintf(__('This controls the main heading contained in the email notification. Leave it blank to use the default heading: <code>%s</code>.', 'MB-multivendor'), $this->get_default_heading()),
                    'placeholder' => '',
                    'default' => ''
                ),
                'email_type' => array(
                    'title' => __('Email Type', 'MB-multivendor'),
                    'type' => 'select',
                    'description' => __('Choose format for the email that will be sent.', 'MB-multivendor'),
                    'default' => 'html',
                    'class' => 'email_type wc-enhanced-select',
                    'options' => $this->get_email_type_options()
                )
            );
        }

    }

    endif;


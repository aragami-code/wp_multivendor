<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!class_exists('WC_Email_Vendor_New_Account')) :

    /**
     * C
     */
    class WC_Email_Vendor_New_Account extends WC_Email {

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
            $this->id = 'vendor_new_account';
            $this->title = __('New Vendor Account', 'MB-multivendor');
            $this->description = __('Vendor new account emails are sent when a customer signs up via the checkout or My Account page.', 'MB-multivendor');

            $this->template_html = 'emails/vendor-new-account.php';
            $this->template_plain = 'emails/plain/vendor-new-account.php';

            //$this->subject = __('Your account on {site_title}', 'MB-multivendor');
            //$this->heading = __('Welcome to {site_title}', 'MB-multivendor');
            $this->template_base = $WCMb->plugin_path . 'templates/';
            // Call parent constuctor
            parent::__construct();
        }

        /**
         * trigger function.
         *
         * @access public
         * @return void
         */
        function trigger($user_id, $user_pass = '', $password_generated = false) {

            if ($user_id) {
                $this->object = new WP_User($user_id);

                $this->user_pass = $user_pass;
                $this->user_login = stripslashes($this->object->user_login);
                $this->user_email = stripslashes($this->object->user_email);
                $this->recipient = $this->user_email;
                $this->password_generated = $password_generated;
            }

            if (!$this->is_enabled() || !$this->get_recipient())
                return;

            $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
        }
        
        /**
         * Get email subject.
         *
         * @access  public
         * @return string
         */
        public function get_default_subject() {
            return apply_filters('wcmb_vendor_new_account_email_subject', __('Your account on {site_title}', 'MB-multivendor'), $this->object);
        }

        /**
         * Get email heading.
         *
         * @access  public
         * @return string
         */
        public function get_default_heading() {
            return apply_filters('wcmb_vendor_new_account_email_heading', __('Welcome to {site_title}', 'MB-multivendor'), $this->object);
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
                'email_heading' => $this->get_heading(),
                'user_login' => $this->user_login,
                'user_pass' => $this->user_pass,
                'blogname' => $this->get_blogname(),
                'password_generated' => $this->password_generated,
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
                'email_heading' => $this->get_heading(),
                'user_login' => $this->user_login,
                'user_pass' => $this->user_pass,
                'blogname' => $this->get_blogname(),
                'password_generated' => $this->password_generated,
                'sent_to_admin' => false,
                'plain_text' => true,
                'email'         => $this,
                    ), 'dc-product-vendor/', $this->template_base);
            return ob_get_clean();
        }

    }

endif;


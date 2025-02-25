<?php
/**

 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global  $WCMb;

echo $email_heading . "\n\n";

echo sprintf( __( "Thanks for creating an account with %s. We have received your application for vendor registration. We will verify the information provided by you and inform you via email. Your username is <strong>%s</strong>.",  'MB-multivendor' ), $blogname, $user_login ) . "\n\n";

if ( get_option( 'woocommerce_registration_generate_password' ) === 'yes' && $password_generated )
	echo sprintf( __( "Your password is <strong>%s</strong>.",  'MB-multivendor' ), $user_pass ) . "\n\n";

echo sprintf( __( 'You can access your account area here: %s.',  'MB-multivendor' ), get_permalink( wc_get_page_id( 'myaccount' ) ) ) . "\n\n";

echo "\n****************************************************\n\n";

echo apply_filters( 'wcmb_email_footer_text', get_option( 'wcmb_email_footer_text' ) );
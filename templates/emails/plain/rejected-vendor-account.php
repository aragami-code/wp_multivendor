<?php
/**
 
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $WCMb;

echo $email_heading . "\n\n";

echo sprintf( __( "Thanks for creating an account as Pending Vendor on %s. But your request has been rejected due to some reason.",  'MB-multivendor' ), $blogname ) . "\n\n";

echo "\n****************************************************\n\n";

echo apply_filters( 'wcmb_email_footer_text', get_option( 'wcmb_email_footer_text' ) );
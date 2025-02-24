<?php
/**

 */
 
global $WCMb;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

$vendor_application_admin_url = apply_filters('wcmb_admin_new_vendor_email_vendor_application_url', admin_url( 'admin.php?page=vendors&s='.$user_object->user_login ));

echo "= " . $email_heading . " =\n\n";

echo sprintf( __( "A new user has applied to be a vendor on %s. His/her email is <strong>%s</strong>.", 'MB-multivendor' ), esc_html( $blogname ), esc_html( $user_object->user_email ) );

echo sprintf( __( 'You can access vendor application here: %s.',  'MB-multivendor' ), esc_url( $vendor_application_admin_url ) ) . "\n\n";

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'wcmb_email_footer_text', get_option( 'wcmb_email_footer_text' ) );
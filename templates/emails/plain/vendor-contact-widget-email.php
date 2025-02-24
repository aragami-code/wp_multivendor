<?php
/**

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $WCMb;
$name = isset( $object['name'] ) ? $object['name'] : '';
$message = isset( $object['message'] ) ? $object['message'] : '';
echo $email_heading . "\n\n"; 
printf(__( "Hello %s,\n\nA customer is trying to contact you. Details are as follows:", 'MB-multivendor' ),  $vendor->page_title); 
echo "****************************************************\n\n";
echo __( 'Name', 'MB-multivendor' ).' : '.$name;
echo "\n";
echo __( 'Message', 'MB-multivendor' ).' : '.$message;

echo apply_filters( 'wcmb_email_footer_text', get_option( 'wcmb_email_footer_text' ) );
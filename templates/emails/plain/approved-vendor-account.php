<?php
/**

 */
 
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $WCMb;

echo "= " . $email_heading . " =\n\n";
echo sprintf( __("Congratulations! Your vendor application on %s has been approved!", 'MB-multivendor' ), get_option( 'blogname' ) );
echo '\n';
echo sprintf( __( "Application Status: %s", 'MB-multivendor' ), 'Approved' );
echo '\n';
echo sprintf( __( "Applicant Username: %s", 'MB-multivendor' ), $user_login ); 
echo '\n';
echo _e('You have been cleared for landing! Congratulations and welcome aboard!', 'MB-multivendor');

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo apply_filters( 'wcmb_email_footer_text', get_option( 'wcmb_email_footer_text' ) );

?>
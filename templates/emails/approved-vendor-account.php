<?php
/**
 
 */
 
global $WCMb;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
?>
<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<p><?php printf( __( "Congratulations! Your vendor application on %s has been approved!", 'MB-multivendor' ), get_option( 'blogname' ) ); ?></p>
<p>
	<?php _e( "Application status: Approved",  'MB-multivendor' ); ?><br/>
	<?php printf( __( "Applicant Username: %s",  'MB-multivendor' ), $user_login ); ?>
</p>
<p><?php _e('You have been cleared for landing! Congratulations and welcome aboard!', 'MB-multivendor') ?> <p>
<?php do_action( 'wcmb_email_footer' );?>
<?php
/**
 
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
global $WCMb;
?>
<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php printf( __( "Thanks for creating an account with us on %s. Unfortunately your request has been rejected.",  'MB-multivendor' ), esc_html( $blogname )); ?></p>
<p><?php printf( __( "You may contact the site admin at %s.",  'MB-multivendor' ), get_option('admin_email')); ?></p>

<?php do_action( 'wcmb_email_footer' ); ?>
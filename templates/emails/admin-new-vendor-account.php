<?php
/**

 */
 
global $WCMb;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
$vendor_application_admin_url = apply_filters('wcmb_admin_new_vendor_email_vendor_application_url', admin_url( 'admin.php?page=vendors&s='.$user_object->user_login ));
?>
<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php printf( __( "A new user has applied to be a vendor on %s. His/her email is <strong>%s</strong>.", 'MB-multivendor' ), esc_html( $blogname ), esc_html( $user_object->user_email ) ); ?></p>

<p><?php printf( __( "You can access vendor application here: %s.", 'MB-multivendor' ), esc_url( $vendor_application_admin_url ) ); ?></p>

<?php do_action( 'wcmb_email_footer' ); ?>
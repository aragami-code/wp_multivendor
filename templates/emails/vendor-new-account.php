<?php
/**
 
 */


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
global  $WCMb;
?>
<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php printf( __( "Thanks for creating an account on %s. We will process your application and revert shortly.",  'MB-multivendor' ), esc_html( $blogname ), esc_html( $user_login ) ); ?></p>
<?php if ( get_option( 'woocommerce_registration_generate_password' ) == 'yes' && $password_generated ) : ?>
<p><?php printf( __( "Your password has been automatically generated: <strong>%s</strong>",  'MB-multivendor' ), esc_html( $user_pass ) ); ?></p>
<?php endif; ?>
<p><?php printf( __( 'You can access your account area here: %s.',  'MB-multivendor' ), get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?></p>

<?php do_action( 'wcmb_email_footer' ); ?>
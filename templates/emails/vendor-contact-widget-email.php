<?php
/**

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $WCMb;
$text_align = is_rtl() ? 'right' : 'left';
$name = isset( $object['name'] ) ? $object['name'] : '';
$message = isset( $object['message'] ) ? $object['message'] : '';
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<p style="text-align:<?php echo $text_align; ?>;" ><?php printf(__( 'Hello %s,<br>A customer is trying to contact you. Details are as follows:', 'MB-multivendor' ),  $vendor->page_title ); ?></p>
<div style="font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; margin-bottom: 40px;">
        <h2><?php _e( 'Customer details', 'woocommerce' ); ?></h2>
        <ul>
            <li><strong><?php _e( 'Name', 'MB-multivendor' ); ?>:</strong> <span class="text"><?php echo $name; ?></span></li>
            <li><strong><?php _e( 'Message', 'MB-multivendor' ); ?>:</strong> <span class="text"><?php echo $message; ?></span></li>
        </ul>
</div>

<?php do_action( 'wcmb_email_footer' ); ?>

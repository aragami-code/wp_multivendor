<?php
/**

 */


if ( !defined( 'ABSPATH' ) ) exit; 
global $WCMb;
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

	<p><?php printf( __( "Hi there! This is to notify that a new product has been submitted in %s.",  'MB-multivendor' ), get_option( 'blogname' ) ); ?></p>

	<p>
		<?php printf( __( "Product title: %s",  'MB-multivendor' ), $product_name ); ?><br/>
		<?php printf( __( "Submitted by: %s",  'MB-multivendor' ), 'Site Administrator' ); ?><br/>
		<?php 
                    $product_link = apply_filters( 'wcmb_email_admin_new_product_link', esc_url(wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_edit_product_endpoint', 'vendor', 'general', 'edit-product'), $post_id)));
			if($submit_product) {
				printf( __( "Edit product: %s",  'MB-multivendor' ), $product_link ); 
			} else {
				printf( __( "View product: %s",  'MB-multivendor' ), get_permalink($post_id)); 
			}
		?>
		<br/>
	</p>

<?php do_action( 'wcmb_email_footer' ); ?>
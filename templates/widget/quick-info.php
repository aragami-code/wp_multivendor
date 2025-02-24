<?php
/**

 */

global $WCMb;
$submit_label = isset( $instance['submit_label'] ) ? $instance['submit_label'] : __( 'Submit', 'MB-multivendor' );
extract( $instance );

?>
<div class="wcmb-quick-info-wrapper">
    <?php
    if( isset( $_GET['message'] ) ) {
                    $message = sanitize_text_field( $_GET['message'] );
                    echo "<div class='woocommerce-{$widget->response[ $message ]['class']}'>" . $widget->response[ $message ]['message'] . "</div>";
    }

    else {
                    echo '<p>' . $description . '</p>';
    }?>

    <form action="" method="post" id="respond" style=" padding: 0;">
                    <input type="text" class="input-text " name="quick_info[name]" value="<?php echo $current_user->display_name ?>" placeholder="<?php _e( 'Name', 'MB-multivendor' ) ?>" required/>
                    <input type="text" class="input-text " name="quick_info[subject]" value="" placeholder="<?php _e( 'Subject', 'MB-multivendor' ) ?>" required/>
                    <input type="email" class="input-text " name="quick_info[email]" value="<?php echo $current_user->user_email  ?>" placeholder="<?php _e( 'Email', 'MB-multivendor' ) ?>" required/>
                    <textarea name="quick_info[message]" rows="5" placeholder="<?php _e( 'Message', 'MB-multivendor' ) ?>" required></textarea>
                    <input type="submit" class="submit" id="submit" name="quick_info[submit]" value="<?php echo $submit_label ?>" />
                    <input type="hidden" name="quick_info[spam]" value="" />
                    <input type="hidden" name="quick_info[vendor_id]" value="<?php echo $vendor->id ?>" />
                    <?php wp_nonce_field( 'dc_vendor_quick_info_submitted', 'dc_vendor_quick_info_submitted' ); ?>
    </form>
</div>
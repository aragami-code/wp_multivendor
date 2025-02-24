<?php
/*
 
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $WCMb;
$user_id = get_current_user_id();
$payment_admin_settings = get_option('wcmb_payment_settings_name');
$payment_mode = array('' => __('Payment Mode', 'MB-multivendor'));
if (isset($payment_admin_settings['payment_method_paypal_masspay']) && $payment_admin_settings['payment_method_paypal_masspay'] = 'Enable') {
    $payment_mode['paypal_masspay'] = __('PayPal Masspay', 'MB-multivendor');
}
if (isset($payment_admin_settings['payment_method_paypal_payout']) && $payment_admin_settings['payment_method_paypal_payout'] = 'Enable') {
    $payment_mode['paypal_payout'] = __('PayPal Payout', 'MB-multivendor');
}
if (isset($payment_admin_settings['payment_method_stripe_masspay']) && $payment_admin_settings['payment_method_stripe_masspay'] = 'Enable') {
    $payment_mode['stripe_masspay'] = __('Stripe Connect', 'MB-multivendor');
}
if (isset($payment_admin_settings['payment_method_direct_bank']) && $payment_admin_settings['payment_method_direct_bank'] = 'Enable') {
    $payment_mode['direct_bank'] = __('Direct Bank', 'MB-multivendor');
}
$vendor_payment_mode_select = apply_filters('wcmb_vendor_payment_mode', $payment_mode);
?>
<div class="col-md-12">
    <form method="post" name="shop_settings_form" class="wcmb_billing_form">
        <div class="panel panel-default pannel-outer-heading">
            <div class="panel-heading">
                <h3><?php _e('Payment Method', 'MB-multivendor'); ?></h3>
            </div>                     
            <div class="panel-body panel-content-padding">
                <div class="form-group">
                    <label for="vendor_payment_mode" class="control-label col-sm-3 col-md-3"><?php _e('Choose Payment Method', 'MB-multivendor'); ?></label>
                    <div class="col-md-6 col-sm-9">
                        <select class="form-control" name="vendor_payment_mode" id="vendor_payment_mode">
                            <?php foreach ($vendor_payment_mode_select as $key => $value) : ?>
                                <option <?php if ($vendor_payment_mode['value'] == $key) echo 'selected' ?>  value="<?php echo $key; ?>"><?php echo $value; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="payment-gateway payment-gateway-paypal_masspay payment-gateway-paypal_payout <?php echo apply_filters('wcmb_vendor_paypal_email_container_class', ''); ?>">
                    <div class="form-group">
                        <label for="vendor_paypal_email" class="control-label col-sm-3 col-md-3"><?php _e('Paypal Email', 'MB-multivendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input  class="form-control" type="text" name="vendor_paypal_email" value="<?php echo isset($vendor_paypal_email['value']) ? $vendor_paypal_email['value'] : ''; ?>"  placeholder="<?php _e('Paypal Email', 'MB-multivendor'); ?>">
                        </div>
                    </div>
                </div>
                <div class="payment-gateway payment-gateway-direct_bank">
                    <div class="form-group">
                        <label for="vendor_bank_account_type" class="control-label col-sm-3 col-md-3"><?php _e('Account type', 'MB-multivendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <select id="vendor_bank_account_type" name="vendor_bank_account_type" class="form-control">
                                <option <?php if ($vendor_bank_account_type['value'] == 'current') echo 'selected' ?> value="current"><?php _e('Current', 'MB-multivendor'); ?></option>
                                <option <?php if ($vendor_bank_account_type['value'] == 'savings') echo 'selected' ?>  value="savings"><?php _e('Savings', 'MB-multivendor'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vendor_bank_name" class="control-label col-sm-3 col-md-3"><?php _e('Bank Name', 'MB-multivendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control" type="text" id="vendor_bank_name" name="vendor_bank_name" class="user-profile-fields" value="<?php echo isset($vendor_bank_name['value']) ? $vendor_bank_name['value'] : ''; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vendor_aba_routing_number" class="control-label col-sm-3 col-md-3"><?php _e('ABA Routing Number', 'MB-multivendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control" type="text" id="vendor_aba_routing_number" name="vendor_aba_routing_number" class="user-profile-fields" value="<?php echo isset($vendor_aba_routing_number['value']) ? $vendor_aba_routing_number['value'] : ''; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vendor_destination_currency" class="control-label col-sm-3 col-md-3"><?php _e('Destination Currency', 'MB-multivendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control" type="text" name="vendor_destination_currency" value="<?php echo isset($vendor_destination_currency['value']) ? $vendor_destination_currency['value'] : ''; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vendor_bank_address" class="control-label col-sm-3 col-md-3"><?php _e('Bank Address', 'MB-multivendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <textarea class="form-control" name="vendor_bank_address" cols="" rows=""><?php echo isset($vendor_bank_address['value']) ? $vendor_bank_address['value'] : ''; ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vendor_iban" class="control-label col-sm-3 col-md-3"><?php _e('IBAN', 'MB-multivendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control" type="text"  name="vendor_iban" value="<?php echo isset($vendor_iban['value']) ? $vendor_iban['value'] : ''; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vendor_account_holder_name" class="control-label col-sm-3 col-md-3"><?php _e('Account Holder Name', 'MB-multivendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control" type="text" placeholder=""  name="vendor_account_holder_name" value="<?php echo isset($vendor_account_holder_name['value']) ? $vendor_account_holder_name['value'] : ''; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vendor_bank_account_number" class="control-label col-sm-3 col-md-3"><?php _e('Account Number', 'MB-multivendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control" type="text" placeholder=""  name="vendor_bank_account_number" value="<?php echo isset($vendor_bank_account_number['value']) ? $vendor_bank_account_number['value'] : ''; ?>">
                        </div>
                    </div>
                </div>
                <?php 
                echo '<div class="payment-gateway payment-gateway-stripe_masspay">';
                if (isset($payment_admin_settings['payment_method_stripe_masspay']) && $payment_admin_settings['payment_method_stripe_masspay'] = 'Enable') {
                	$account_type = apply_filters('wcmb_vendor_stripe_connect_account_type', 'standard', $payment_admin_settings, $user_id);
                    
                	if( $account_type == 'standard' || $account_type == 'express' ) {
						$testmode = get_wcmb_vendor_settings('testmode', 'payment', 'stripe_gateway') === "Enable" ? true : false;
						$client_id = $testmode ? get_wcmb_vendor_settings('test_client_id', 'payment', 'stripe_gateway') : get_wcmb_vendor_settings('live_client_id', 'payment', 'stripe_gateway');
						$secret_key = $testmode ? get_wcmb_vendor_settings('test_secret_key', 'payment', 'stripe_gateway') : get_wcmb_vendor_settings('live_secret_key', 'payment', 'stripe_gateway');
						if (isset($client_id) && isset($secret_key)) {
							if (isset($_GET['code'])) {
								$code = $_GET['code'];
								if (!is_user_logged_in()) {
									if (isset($_GET['state'])) {
										$user_id = $_GET['state'];
									}
								}
								if (isset($resp['access_token']) || get_user_meta($user_id, 'vendor_connected', true) == 1) {
									update_user_meta($user_id, 'vendor_connected', 1);
									?>
									<div class="form-group">
										<label class="control-label col-sm-3 col-md-3"><?php _e('Stripe connect', 'MB-multivendor'); ?></label>
										<div class="col-md-6 col-sm-9">
											<input type="submit" class="btn btn-default" name="disconnect_stripe" value="<?php _e('Disconnect Stripe Account', 'MB-multivendor'); ?>" />
										</div>
									</div>
									<?php
								} else {
									update_user_meta($user_id, 'vendor_connected', 0);
									?>
									<div class="form-group">
										<label class="control-label col-sm-3 col-md-3"><?php _e('Stripe connect', 'MB-multivendor'); ?></label>
										<div class="col-md-6 col-sm-9">
											<b><?php _e('Please Retry!!!', 'MB-multivendor'); ?></b>
										</div>
									</div>
									<?php
								}
							} else if (isset($_GET['error'])) { // Error
								update_user_meta($user_id, 'vendor_connected', 0);
								?>
								<div class="form-group">
									<label class="control-label col-sm-3 col-md-3"><?php _e('Stripe connect', 'MB-multivendor'); ?></label>
									<div class="col-md-6 col-sm-9">
										<b><?php _e('Please Retry!!!', 'MB-multivendor'); ?></b>
									</div>
								</div>
								<?php
							} else {
								$vendor_connected = get_user_meta($user_id, 'vendor_connected', true);
								$connected = true;
	
								if (isset($vendor_connected) && $vendor_connected == 1) {
									$admin_client_id = get_user_meta($user_id, 'admin_client_id', true);
	
									if ($admin_client_id == $client_id) {
										?>
										<div class="form-group">
											<label class="control-label col-sm-3 col-md-3"><?php _e('Stripe connect', 'MB-multivendor'); ?></label>
											<div class="col-md-6 col-sm-9">
												<input type="submit" class="btn btn-default" name="disconnect_stripe" value="<?php _e('Disconnect Stripe Account', 'MB-multivendor'); ?>" />
											</div>
										</div>
										<?php
									} else {
										$connected = false;
									}
								} else {
									$connected = false;
								}
								if (!$connected) {
	
									$status = delete_user_meta($user_id, 'vendor_connected');
									$status = delete_user_meta($user_id, 'admin_client_id');
	
									// Show OAuth link
									$authorize_request_body = array(
										'response_type' => 'code',
										'scope' => 'read_write',
										'client_id' => $client_id,
										'redirect_uri' => admin_url('admin-ajax.php') . "?action=marketplace_stripe_authorize",
										'state' => $user_id
									);
									$url = apply_filters( 'wcmb_vendor_stripe_connect_account_type_request_url', 'https://connect.stripe.com/oauth/authorize', $account_type ) . '?' . http_build_query( apply_filters( 'wcmb_vendor_stripe_connect_account_type_request_params' , $authorize_request_body, $account_type ) );
									$stripe_connect_url = $WCMb->plugin_url . 'assets/images/blue-on-light.png';
	
									if (!$status) {
										?>
										<div class="form-group">
											<label class="control-label col-sm-3 col-md-3"><?php _e('Stripe connect', 'MB-multivendor'); ?></label>
											<div class="col-md-6 col-sm-9">
												<a href=<?php echo $url; ?> target="_self"><img src="<?php echo $stripe_connect_url; ?>" /></a>
											</div>
										</div>
										<?php
									} else {
										?>
										<div class="form-group">
											<label class="control-label col-sm-3 col-md-3"><?php _e('Stripe connect', 'MB-multivendor'); ?></label>
											<div class="col-md-6 col-sm-9">
												<a href=<?php echo $url; ?> target="_self"><img src="<?php echo $stripe_connect_url; ?>" /></a>
											</div>
										</div>
										<?php
									}
								}
							}
						}
					} else {
						do_action('wcmb_vendor_stripe_connect_account_fields', $payment_admin_settings, $user_id, $account_type);
					}
                }
                echo '</div>';
                ?>
                <?php do_action('wcmb_after_vendor_billing'); ?>
            </div>
        </div>

        <div class="wcmb-action-container">
            <button class="btn btn-default" name="store_save_billing" ><?php _e('Save Options', 'MB-multivendor'); ?></button>
            <div class="clear"></div>
        </div>
    </form>
</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('#vendor_payment_mode').on('change', function () {
            $('.payment-gateway').hide();
            $('.payment-gateway-' + $(this).val()).show();
        }).change();
    });
</script>
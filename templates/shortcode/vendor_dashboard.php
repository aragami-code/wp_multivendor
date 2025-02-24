<?php
/**
 
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $WCMb;

do_action('before_wcmb_vendor_dashboard');

//wc_print_notices();
$WCMb->template->get_template('vendor-dashboard/dashboard-header.php');

do_action('wcmb_vendor_dashboard_navigation', array());
$is_single = !is_null($WCMb->endpoints->get_current_endpoint_var()) ? '-single' : '';
?>
<div id="page-wrapper" class="side-collapse-container">
    <div id="current-endpoint-title-wrapper" class="current-endpoint-title-wrapper">
        <div class="current-endpoint">
            <?php echo $WCMb->vendor_hooks->wcmb_create_vendor_dashboard_breadcrumbs($WCMb->endpoints->get_current_endpoint()); ?>
        </div>
    </div>
    <!-- /.row -->
    <div class="content-padding gray-bkg <?php echo $WCMb->endpoints->get_current_endpoint() ? $WCMb->endpoints->get_current_endpoint().$is_single : 'dashboard'; ?>">
        <div class="notice-wrapper">
            <?php wc_print_notices(); ?>
        </div>
        <div class="row">
            <?php 
            $is_block = get_user_meta(get_current_vendor_id(), '_vendor_turn_off', true);
            if($is_block) {
				?>
				<div class="col-md-12 text-center">
					<div class="panel wcmb-suspended-vendor-notice content-padding">
					    <?php echo apply_filters( 'wcmb_suspended_vendor_dashboard_message', sprintf( __('Your account has been suspended by the admin due to some suspicious activity. Please contact your <a href="mailto:%s">admin</a> for further information.', 'MB-multivendor'), get_option('admin_email')) ); ?>
					</div>
				</div>
			<?php } else {
				do_action('wcmb_vendor_dashboard_content');
			}?>
        </div>
    </div>
</div>

<?php
$WCMb->template->get_template('vendor-dashboard/dashboard-footer.php');

do_action('after_wcmb_vendor_dashboard');

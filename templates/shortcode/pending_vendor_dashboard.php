<?php
/**

 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $WCMb, $wp;

echo '<div class="pending-vendor-dashboard">';
do_action('before_wcmb_pending_vendor_dashboard');

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
            <div class="col-md-12 text-center">
				<div class="panel wcmb-pending-vendor-notice">
                    <?php echo apply_filters( 'wcmb_pending_vendor_dashboard_message', __('Congratulations! You have successfully applied as a Vendor. Please wait for further notifications from the admin.', 'MB-multivendor') ); ?>
                </div>
			</div>
        </div>
    </div>
</div>

<?php
$WCMb->template->get_template('vendor-dashboard/dashboard-footer.php');

do_action('after_wcmb_pending_vendor_dashboard');
echo '</div>';

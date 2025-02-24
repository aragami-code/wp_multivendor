<?php
/**
 
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $WCMb;

echo '<div class="rejected-vendor-dashboard">';
do_action('before_wcmb_rejected_vendor_dashboard');

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
        	<?php do_action('wcmb_rejected_vendor_dashboard_content'); ?>
        </div>
    </div>
</div>

<?php
$WCMb->template->get_template('vendor-dashboard/dashboard-footer.php');

do_action('after_wcmb_rejected_vendor_dashboard');
echo '</div>';

<?php

/*
 
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $WCMb;
$vendor = get_current_vendor();
do_action('before_wcmb_vendor_pending_shipping');
?>
<table id="widget_vendor_pending_shipping" class="table table-striped table-bordered wcmb-widget-dt <?php //echo $pending_shippings ? 'responsive-table' : 'blank-responsive-table'; ?>" width="100%">
<?php if($default_headers){ ?>
    <thead>
        <tr>
            <?php 
                foreach ($default_headers as $key => $value) {
                    echo '<th>'.$value.'</th>';
                }
            ?>
        </tr>
    </thead>
    <tbody>
    </tbody>
<?php } ?>
</table>
 <!-- Modal -->
<div id="marke-as-ship-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <form method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php _e('Shipment Tracking Details', 'MB-multivendor'); ?></h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="tracking_url"><?php _e('Enter Tracking Url', 'MB-multivendor'); ?> *</label>
                        <input type="url" class="form-control" id="email" name="tracking_url" required="">
                    </div>
                    <div class="form-group">
                        <label for="tracking_id"><?php _e('Enter Tracking ID', 'MB-multivendor'); ?> *</label>
                        <input type="text" class="form-control" id="pwd" name="tracking_id" required="">
                    </div>
                </div>
                <input type="hidden" name="order_id" id="wcmb-marke-ship-order-id" />
                <?php if (isset($_POST['wcmb_start_date_order'])) : ?>
                    <input type="hidden" name="wcmb_start_date_order" value="<?php echo $_POST['wcmb_start_date_order']; ?>" />
                <?php endif; ?>
                <?php if (isset($_POST['wcmb_end_date_order'])) : ?>
                    <input type="hidden" name="wcmb_end_date_order" value="<?php echo $_POST['wcmb_end_date_order']; ?>" />
                <?php endif; ?>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" name="wcmb-submit-mark-as-ship"><?php _e('Submit', 'MB-multivendor'); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
jQuery(document).ready(function($) {
    var pending_shipping_wgt;
    var columns = [];
    <?php if($default_headers) {
     foreach ($default_headers as $key => $header) { ?>
        obj = {};
        obj['data'] = '<?php echo esc_js($key); ?>';
        obj['className'] = '<?php if(isset($header['class'])) echo esc_js($header['class']); ?>';
        columns.push(obj);
     <?php }
        } ?>
    pending_shipping_wgt = $('#widget_vendor_pending_shipping').DataTable({
        ordering  : false,
        paging: false,
        info: false,
        searching  : false,
        processing: false,
        serverSide: true,
        responsive: true,
        language: {
            "emptyTable": "<?php echo trim(__('You have no pending shipping!','MB-multivendor')); ?>",
            "zeroRecords": "<?php echo trim(__('You have no pending shipping!','MB-multivendor')); ?>",
            
        },
        ajax:{
            url : '<?php echo add_query_arg( 'action', 'wcmb_widget_vendor_pending_shipping', $WCMb->ajax_url() ); ?>', 
            type: "post",
            error: function(xhr, status, error) {
                $("#widget_vendor_pending_shipping tbody").append('<tr class="odd"><td valign="top" colspan="<?php if(is_array($default_headers)) count($default_headers); ?>" class="dataTables_empty" style="text-align:center;">'+error+' - <a href="javascript:window.location.reload();"><?php _e('Reload', 'MB-multivendor'); ?></a></td></tr>');
                $("#widget_vendor_pending_shipping").css("display","none");
            }
        },
        columns: columns
    });
    new $.fn.dataTable.FixedHeader( pending_shipping_wgt );
});
function wcmbMarkeAsShip(self, order_id) {
    jQuery('#wcmb-marke-ship-order-id').val(order_id);
    jQuery('#marke-as-ship-modal').modal('show');
}
</script>
<?php 
do_action('after_wcmb_vendor_pending_shipping');
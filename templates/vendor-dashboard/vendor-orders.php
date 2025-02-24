<?php
/**
 
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $woocommerce, $WCMb;

$orders_list_table_headers = apply_filters('wcmb_datatable_order_list_table_headers', array(
    'select_order'  => array('label' => '', 'class' => 'text-center'),
    'order_id'      => array('label' => __( 'Order ID', 'MB-multivendor' )),
    'order_date'    => array('label' => __( 'Date', 'MB-multivendor' )),
    'vendor_earning'=> array('label' => __( 'Earnings', 'MB-multivendor' )),
    'order_status'  => array('label' => __( 'Status', 'MB-multivendor' )),
    'action'        => array('label' => __( 'Action', 'MB-multivendor' )),
), get_current_user_id());
?>
<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-body">
            <form name="wcmb_vendor_dashboard_orders" method="POST" class="form-inline">
                <div class="form-group">
                    <span class="date-inp-wrap">
                        <input type="text" name="wcmb_start_date_order" class="pickdate gap1 wcmb_start_date_order form-control" placeholder="<?php _e('from', 'MB-multivendor'); ?>" value="<?php echo isset($_POST['wcmb_start_date_order']) ? $_POST['wcmb_start_date_order'] : date('Y-m-01'); ?>" />
                    </span> 
                    <!-- <span class="between">&dash;</span> -->
                </div>
                <div class="form-group">
                    <span class="date-inp-wrap">
                        <input type="text" name="wcmb_end_date_order" class="pickdate wcmb_end_date_order form-control" placeholder="<?php _e('to', 'MB-multivendor'); ?>" value="<?php echo isset($_POST['wcmb_end_date_order']) ? $_POST['wcmb_end_date_order'] : date('Y-m-d'); ?>" />
                    </span>
                </div>
                <button class="wcmb_black_btn btn btn-default" type="submit" name="wcmb_order_submit"><?php _e('Show', 'MB-multivendor'); ?></button>
            </form>
            <form method="post" name="wcmb_vendor_dashboard_completed_stat_export">
                <table class="table table-striped table-bordered" id="wcmb-vendor-orders" style="width:100%;">
                    <thead>
                        <tr>
                        <?php 
                            if($orders_list_table_headers) :
                                foreach ($orders_list_table_headers as $key => $header) {
                                    if($key == 'select_order'){ ?>
                            <th class="<?php if(isset($header['class'])) echo $header['class']; ?>"><input type="checkbox" class="select_all_all" onchange="toggleAllCheckBox(this, 'wcmb-vendor-orders');" /></th>
                                <?php }else{ ?>
                            <th class="<?php if(isset($header['class'])) echo $header['class']; ?>"><?php if(isset($header['label'])) echo $header['label']; ?></th>         
                                <?php }
                                }
                            endif;
                        ?>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            <?php if(apply_filters('can_wcmb_vendor_export_orders_csv', true, get_current_vendor_id())) : ?>
            <div class="wcmb-action-container">
                <input class="btn btn-default" type="submit" name="wcmb_download_vendor_order_csv" value="<?php _e('Download CSV', 'MB-multivendor') ?>" />
            </div>
            <?php endif; ?>
            <?php if (isset($_POST['wcmb_start_date_order'])) : ?>
                <input type="hidden" name="wcmb_start_date_order" value="<?php echo $_POST['wcmb_start_date_order']; ?>" />
            <?php endif; ?>
            <?php if (isset($_POST['wcmb_end_date_order'])) : ?>
                <input type="hidden" name="wcmb_end_date_order" value="<?php echo $_POST['wcmb_end_date_order']; ?>" />
            <?php endif; ?>    
            </form>
        </div>
    </div>

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
</div>


<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var orders_table;
        var statuses = [];
        var columns = [];
        <?php if($orders_list_table_headers) {
     foreach ($orders_list_table_headers as $key => $header) { ?>
        obj = {};
        obj['data'] = '<?php echo esc_js($key); ?>';
        obj['className'] = '<?php if(isset($header['class'])) echo esc_js($header['class']); ?>';
        columns.push(obj);
     <?php }
        }
        $filter_by_status = apply_filters('wcmb_vendor_dashboard_order_filter_status_arr',array(
            'all' => __('All', 'MB-multivendor'),
            'processing' => __('Processing', 'MB-multivendor'),
            'completed' => __('Completed', 'MB-multivendor')
        ));
        foreach ($filter_by_status as $key => $label) { ?>
            obj = {};
            obj['key'] = "<?php echo trim($key); ?>";
            obj['label'] = "<?php echo addslashes($label); ?>";
            statuses.push(obj);
        <?php } ?>
        orders_table = $('#wcmb-vendor-orders').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ordering: false,
            responsive: true,
            drawCallback: function (settings) {
                $( "#filter_by_order_status" ).detach();
                var order_status_sel = $('<select id="filter_by_order_status" class="wcmb-filter-dtdd wcmb_filter_order_status form-control">').appendTo("#wcmb-vendor-orders_length");
                $(statuses).each(function () {
                    order_status_sel.append($("<option>").attr('value', this.key).text(this.label));
                });
                if(settings.oAjaxData.order_status){
                    order_status_sel.val(settings.oAjaxData.order_status);
                }
            },
            language: {
                emptyTable: "<?php echo trim(__('No orders found!', 'MB-multivendor')); ?>",
                processing: "<?php echo trim(__('Processing...', 'MB-multivendor')); ?>",
                info: "<?php echo trim(__('Showing _START_ to _END_ of _TOTAL_ orders', 'MB-multivendor')); ?>",
                infoEmpty: "<?php echo trim(__('Showing 0 to 0 of 0 orders', 'MB-multivendor')); ?>",
                lengthMenu: "<?php echo trim(__('Number of rows _MENU_', 'MB-multivendor')); ?>",
                zeroRecords: "<?php echo trim(__('No matching orders found', 'MB-multivendor')); ?>",
                paginate: {
                    next: "<?php echo trim(__('Next', 'MB-multivendor')); ?>",
                    previous: "<?php echo trim(__('Previous', 'MB-multivendor')); ?>"
                }
            },
            ajax: {
                url: '<?php echo add_query_arg( 'action', 'wcmb_datatable_get_vendor_orders', $WCMb->ajax_url() ); ?>',
                type: "post",
                data: function (data) {
                    data.start_date = '<?php echo $start_date; ?>';
                    data.end_date = '<?php echo $end_date; ?>';
                    data.order_status = $('#filter_by_order_status').val();
                },
                error: function(xhr, status, error) {
                    $("#wcmb-vendor-orders tbody").append('<tr class="odd"><td valign="top" colspan="6" class="dataTables_empty" style="text-align:center;">'+error+' - <a href="javascript:window.location.reload();"><?php _e('Reload', 'MB-multivendor'); ?></a></td></tr>');
                    $("#wcmb-vendor-orders_processing").css("display","none");
                }
            },
            columns: columns
        });
        new $.fn.dataTable.FixedHeader( orders_table );
        $(document).on('change', '#filter_by_order_status', function () {
            orders_table.ajax.reload();
        });
    });

    function wcmbMarkeAsShip(self, order_id) {
        jQuery('#wcmb-marke-ship-order-id').val(order_id);
        jQuery('#marke-as-ship-modal').modal('show');
    }
</script>
<?php
/**
 
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $WCMb;
$transactions_list_table_headers = apply_filters('wcmb_datatable_vendor_transactions_list_table_headers', array(
    'select_transaction'  => array('label' => '', 'class' => 'text-center'),
    'date'      => array('label' => __( 'Date', 'MB-multivendor' )),
    'transaction_id'    => array('label' => __( 'Transc.ID', 'MB-multivendor' )),
    'commission_ids'=> array('label' => __( 'Commission IDs', 'MB-multivendor' )),
    'fees'  => array('label' => __( 'Fee', 'MB-multivendor' )),
    'net_earning'        => array('label' => __( 'Net Earnings', 'MB-multivendor' )),
), get_current_user_id());
?>
<div class="col-md-12">
    
    <div class="panel panel-default">
        <div class="panel-body">
            <div id="vendor_transactions_date_filter" class="form-inline datatable-date-filder">
                <div class="form-group">
                    <span class="date-inp-wrap">
                        <input id="wcmb_from_date" class="form-control" name="from_date" class="pickdate gap1" placeholder="From" value ="<?php echo date('Y-m-01'); ?>"/>
                    </span>
                </div>
                <div class="form-group">
                    <span class="date-inp-wrap">
                        <input id="wcmb_to_date" class="form-control" name="to_date" class="pickdate" placeholder="To" value ="<?php echo   date('Y-m-d'); ?>"/>
                    </span>
                </div>
                <button type="button" name="order_export_submit" id="do_filter"  class="btn btn-default" ><?php _e('Show', 'MB-multivendor') ?></button>
            </div>  
            <form method="post" name="export_transaction">
                <div class="wcmb_table_holder">
                    <table id="vendor_transactions" class="get_wcmb_transactions table table-striped table-bordered" width="100%">
                        <thead>
                            <tr>
                            <?php 
                                if($transactions_list_table_headers) :
                                    foreach ($transactions_list_table_headers as $key => $header) {
                                        if($key == 'select_transaction'){ ?>
                                <th class="<?php if(isset($header['class'])) echo $header['class']; ?>"><input type="checkbox" class="select_all_transaction" onchange="toggleAllCheckBox(this, 'vendor_transactions');" /></th>
                                    <?php }else{ ?>
                                <th class="<?php if(isset($header['class'])) echo $header['class']; ?>"><?php if(isset($header['label'])) echo $header['label']; ?></th>         
                                    <?php }
                                    }
                                endif;
                            ?>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
                <div id="export_transaction_wrap" class="wcmb-action-container wcmb_table_loader" style="display: none;">
                    <input type="hidden" id="export_transaction_start_date" name="from_date" value="<?php echo date('Y-m-01'); ?>" />
                    <input id="export_transaction_end_date" type="hidden" name="to_date" value="<?php echo date('Y-m-d'); ?>" />
                    <button type="submit" name="export_transaction" class="btn btn-default"><?php _e('Download CSV', 'MB-multivendor'); ?></button>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
    </div>  
</div>
<script>
jQuery(document).ready(function($) {
    $( "#wcmb_from_date" ).datepicker({ 
        dateFormat: 'yy-mm-dd',
        onClose: function (selectedDate) {
            $("#wcmb_to_date").datepicker("option", "minDate", selectedDate);
        }
    });
    $( "#wcmb_to_date" ).datepicker({ 
        dateFormat: 'yy-mm-dd',
        onClose: function (selectedDate) {
            $("#wcmb_from_date").datepicker("option", "maxDate", selectedDate);
        }
    });
    var vendor_transactions;
    var columns = [];
    <?php if($transactions_list_table_headers) {
     foreach ($transactions_list_table_headers as $key => $header) { ?>
        obj = {};
        obj['data'] = '<?php echo esc_js($key); ?>';
        obj['className'] = '<?php if(isset($header['class'])) echo esc_js($header['class']); ?>';
        columns.push(obj);
     <?php }
        } ?>
    vendor_transactions = $('#vendor_transactions').DataTable({
        ordering  : false,
        searching  : false,
        processing: true,
        serverSide: true,
        responsive: true,
        language: {
            "emptyTable": "<?php echo trim(__('Sorry. No transactions are available.','MB-multivendor')); ?>",
            "processing": "<?php echo trim(__('Processing...', 'MB-multivendor')); ?>",
            "info": "<?php echo trim(__('Showing _START_ to _END_ of _TOTAL_ transactions','MB-multivendor')); ?>",
            "infoEmpty": "<?php echo trim(__('Showing 0 to 0 of 0 transactions','MB-multivendor')); ?>",
            "lengthMenu": "<?php echo trim(__('Number of rows _MENU_','MB-multivendor')); ?>",
            "zeroRecords": "<?php echo trim(__('No matching transactions found','MB-multivendor')); ?>",
            "search": "<?php echo trim(__('Search:','MB-multivendor')); ?>",
            "paginate": {
                "next":  "<?php echo trim(__('Next','MB-multivendor')); ?>",
                "previous":  "<?php echo trim(__('Previous','MB-multivendor')); ?>"
            }
        },
        initComplete: function (settings, json) {
            var info = this.api().page.info();
            if (info.recordsTotal > 0) {
                $('#export_transaction_wrap').show();
            }
            $('#display_trans_from_dt').text($('#wcmb_from_date').val());
            $('#export_transaction_start_date').val($('#wcmb_from_date').val());
            $('#display_trans_to_dt').text($('#wcmb_to_date').val());
            $('#export_transaction_end_date').val($('#wcmb_to_date').val());
        },
        ajax:{
            url : '<?php echo add_query_arg( 'action', 'wcmb_vendor_transactions_list', $WCMb->ajax_url() ); ?>', 
            type: "post",
            data: function (data) {
                data.from_date = $('#wcmb_from_date').val();
                data.to_date = $('#wcmb_to_date').val();
            },
            error: function(xhr, status, error) {
                $("#vendor_transactions tbody").append('<tr class="odd"><td valign="top" colspan="6" class="dataTables_empty" style="text-align:center;">'+error+' - <a href="javascript:window.location.reload();"><?php _e('Reload', 'MB-multivendor'); ?></a></td></tr>');
                $("#vendor_transactions_processing").css("display","none");
            }
        },
        columns: columns
    });
    new $.fn.dataTable.FixedHeader( vendor_transactions );
    $(document).on('click', '#vendor_transactions_date_filter #do_filter', function () {
        $('#display_trans_from_dt').text($('#wcmb_from_date').val());
        $('#export_transaction_start_date').val($('#wcmb_from_date').val());
        $('#display_trans_to_dt').text($('#wcmb_to_date').val());
        $('#export_transaction_end_date').val($('#wcmb_to_date').val());
        vendor_transactions.ajax.reload();
    });
});
</script>
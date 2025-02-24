<?php
/**

 */
if (!defined('ABSPATH')) {
// Exit if accessed directly
exit;
}
global $woocommerce, $WCMb;
$get_vendor_thresold = 0;
if (isset($WCMb->vendor_caps->payment_cap['commission_threshold']) && $WCMb->vendor_caps->payment_cap['commission_threshold']) {
$get_vendor_thresold = $WCMb->vendor_caps->payment_cap['commission_threshold'];
}
$withdrawal_list_table_headers = apply_filters('wcmb_datatable_vendor_withdrawal_list_table_headers', array(
    'select_withdrawal'  => array('label' => '', 'class' => 'text-center', 'orderable' => false),
    'order_id'      => array('label' => __( 'Order ID', 'MB-multivendor' ), 'orderable' => false),
    'commission_amount'    => array('label' => __( 'Commission Amount', 'MB-multivendor' ), 'orderable' => false),
    'shipping_amount'=> array('label' => __( 'Shipping Amount', 'MB-multivendor' ), 'orderable' => false),
    'tax_amount'  => array('label' => __( 'Tax Amount', 'MB-multivendor' ), 'orderable' => false),
    'total'        => array('label' => __( 'Total', 'MB-multivendor' ), 'orderable' => false),
), get_current_user_id());
?>
<?php if($get_vendor_thresold) : ?>
<div class="col-md-12">
    <blockquote>
        <span><?php _e('Your Threshold value for withdrawals is :', 'MB-multivendor'); ?> <?php echo wc_price($get_vendor_thresold); ?></span>
    </blockquote>
</div>
<?php endif; ?>
<div class="col-md-12">
    <div class="panel panel-default">
        <h3 class="panel-heading"><?php _e('Completed Orders', 'MB-multivendor'); ?></h3>
        <div class="panel-body">
            <form method="post" name="get_paid_form">
                <table id="vendor_withdrawal" class="table table-striped table-bordered" width="100%">
                    <thead>
                        <tr>
                        <?php 
                            if($withdrawal_list_table_headers) :
                                foreach ($withdrawal_list_table_headers as $key => $header) {
                                    if($key == 'select_withdrawal'){ ?>
                            <th class="<?php if(isset($header['class'])) echo $header['class']; ?>"><input type="checkbox" class="select_all_withdrawal" onchange="toggleAllCheckBox(this, 'vendor_withdrawal');" /></th>
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
                <div class="wcmb_table_loader">
                    <input type="hidden" id="total_orders_count" value = "<?php echo count($vendor_unpaid_orders); ?>" />
                    <?php if (count($vendor_unpaid_orders) > 0) { 
                        if (isset($WCMb->vendor_caps->payment_cap['wcmb_disbursal_mode_vendor']) && $WCMb->vendor_caps->payment_cap['wcmb_disbursal_mode_vendor'] == 'Enable') {
                            $total_vendor_due = $vendor->wcmb_vendor_get_total_amount_due();
                            if ($total_vendor_due > $get_vendor_thresold) { ?>
                            <div class="wcmb-action-container">
                                <button name="vendor_get_paid" type="submit" class="btn btn-default"><?php _e('Request Withdrawals', 'MB-multivendor'); ?></button>
                            </div>
                    <?php
                            }
                        }
                    }
                    ?>
                    <div class="clear"></div>
                </div>
            </form>
            <?php $vendor_payment_mode = get_user_meta($vendor->id, '_vendor_payment_mode', true);
            if ($vendor_payment_mode == 'paypal_masspay' && wp_next_scheduled('masspay_cron_start')) { ?>
            <div class="wcmb_admin_massege">
                <div class="wcmb_mixed_msg"><?php _e('Your next scheduled payment date is on:', 'MB-multivendor'); ?>	<span><?php echo date('d/m/Y g:i:s A', wp_next_scheduled('masspay_cron_start')); ?></span> </div>
            </div>
        <?php } ?> 
        </div>
    </div>
</div>
<script>
jQuery(document).ready(function($) {
    var vendor_withdrawal;
    var columns = [];
    <?php if($withdrawal_list_table_headers) {
     foreach ($withdrawal_list_table_headers as $key => $header) { ?>
        obj = {};
        obj['data'] = '<?php echo esc_js($key); ?>';
        obj['className'] = '<?php if(isset($header['class'])) echo esc_js($header['class']); ?>';
        obj['orderable'] = '<?php if(isset($header['orderable'])) echo esc_js($header['orderable']); ?>';
        columns.push(obj);
     <?php }
        } ?>
    vendor_withdrawal = $('#vendor_withdrawal').DataTable({
        ordering  : <?php echo isset($table_init['ordering']) ? trim($table_init['ordering']) : 'false'; ?>,
        searching  : <?php echo isset($table_init['searching']) ? trim($table_init['searching']) : 'false'; ?>,
        processing: true,
        serverSide: true,
        responsive: true,
        language: {
            "emptyTable": "<?php echo isset($table_init['emptyTable']) ? trim($table_init['emptyTable']) : __('No orders found!','MB-multivendor'); ?>",
            "processing": "<?php echo isset($table_init['processing']) ? trim($table_init['processing']) : __('Processing...', 'MB-multivendor'); ?>",
            "info": "<?php echo isset($table_init['info']) ? trim($table_init['info']) : __('Showing _START_ to _END_ of _TOTAL_ orders','MB-multivendor'); ?>",
            "infoEmpty": "<?php echo isset($table_init['infoEmpty']) ? trim($table_init['infoEmpty']) : __('Showing 0 to 0 of 0 orders','MB-multivendor'); ?>",
            "lengthMenu": "<?php echo isset($table_init['lengthMenu']) ? trim($table_init['lengthMenu']) : __('Number of rows _MENU_','MB-multivendor'); ?>",
            "zeroRecords": "<?php echo isset($table_init['zeroRecords']) ? trim($table_init['zeroRecords']) : __('No matching orders found','MB-multivendor'); ?>",
            "search": "<?php echo isset($table_init['search']) ? trim($table_init['search']) : __('Search:','MB-multivendor'); ?>",
            "paginate": {
                "next":  "<?php echo isset($table_init['next']) ? trim($table_init['next']) : __('Next','MB-multivendor'); ?>",
                "previous":  "<?php echo isset($table_init['previous']) ? trim($table_init['previous']) : __('Previous','MB-multivendor'); ?>"
            }
        },
        drawCallback: function () {
            $('table.dataTable tr [type="checkbox"]').each(function(){
                if($(this).prop('disabled')){
                    $(this).css('cursor', 'not-allowed');
                    $(this).parents('tr[role="row"]').css('background-color', '#edf0f1');
                }
            })
        },
        ajax:{
            url : '<?php echo add_query_arg( 'action', 'wcmb_vendor_unpaid_order_vendor_withdrawal_list', $WCMb->ajax_url() ); ?>', 
            type: "post",
            error: function(xhr, status, error) {
                $("#vendor_withdrawal tbody").append('<tr class="odd"><td valign="top" colspan="6" class="dataTables_empty" style="text-align:center;">'+error+' - <a href="javascript:window.location.reload();"><?php _e('Reload', 'MB-multivendor'); ?></a></td></tr>');
                $("#vendor_withdrawal_processing").css("display","none");
            }
        },
        columns: columns
    });
    new $.fn.dataTable.FixedHeader( vendor_withdrawal );
});
</script>
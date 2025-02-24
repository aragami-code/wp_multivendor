<?php
/*
 
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $WCMb;
do_action('before_wcmb_vendor_dashboard_coupon_list_table');
$coupon_list_table_headers = apply_filters('wcmb_datatable_coupon_list_table_headers', array(
    'coupons'      => array('label' => __( 'Coupon(s)', 'MB-multivendor' ), 'class' => 'name'),
    'type'    => array('label' => __( 'Coupon type', 'MB-multivendor' )),
    'amount'    => array('label' => __( 'Coupon Amount', 'MB-multivendor' )),
    'uses_limit'=> array('label' => __( 'Usage / Limit', 'MB-multivendor' )),
    'expiry_date'  => array('label' => __( 'Expiry Date', 'MB-multivendor' )),
    'actions'  => array('label' => __( 'Actions', 'MB-multivendor' )),
), get_current_user_id());
?>
<div class="col-md-12">
    <div class="panel panel-default panel-pading">
        <table id="coupons_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <?php 
                        if($coupon_list_table_headers) :
                            foreach ($coupon_list_table_headers as $key => $header) { ?>
                        <th class="<?php if(isset($header['class'])) echo $header['class']; ?>"><?php if(isset($header['label'])) echo $header['label']; ?></th>         
                            <?php }
                        endif;
                    ?>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
        <div class="wcmb-action-container">
            <a href="<?php echo wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_add_coupon_endpoint', 'vendor', 'general', 'add-coupon'));?>" class="btn btn-default"><?php echo __('Add Coupon', 'MB-multivendor');?></a>
        </div>
    </div>
</div>
<style>
    .vendor-coupon .row-actions{ visibility: hidden;}
    .vendor-coupon:hover .row-actions{ visibility: visible;}
    span.delete a{color: #a00;}
</style>
<script>
jQuery(document).ready(function($) {
    var vendor_coupons;
    var columns = [];
    <?php if($coupon_list_table_headers) {
     foreach ($coupon_list_table_headers as $key => $header) { ?>
        obj = {};
        obj['data'] = '<?php echo esc_js($key); ?>';
        obj['className'] = '<?php if(isset($header['class'])) echo esc_js($header['class']); ?>';
        columns.push(obj);
     <?php }
        } ?>
    vendor_coupons = $('#coupons_table').DataTable({
        columnDefs: [
            { width: 80, targets: 5 }
        ],
        ordering  : false,
        searching  : false,
        processing: true,
        serverSide: true,
        responsive: true,
        language: {
            emptyTable: "<?php echo trim(__('No coupons found!','MB-multivendor')); ?>",
            processing: "<?php echo trim(__('Processing...', 'MB-multivendor')); ?>",
            info: "<?php echo trim(__('Showing _START_ to _END_ of _TOTAL_ coupons','MB-multivendor')); ?>",
            infoEmpty: "<?php echo trim(__('Showing 0 to 0 of 0 coupons','MB-multivendor')); ?>",
            lengthMenu: "<?php echo trim(__('Number of rows _MENU_','MB-multivendor')); ?>",
            zeroRecords: "<?php echo trim(__('No matching coupons found','MB-multivendor')); ?>",
            paginate: {
                next: "<?php echo trim(__('Next', 'MB-multivendor')); ?>",
                previous: "<?php echo trim(__('Previous', 'MB-multivendor')); ?>"
            }
        },
        ajax:{
            url : '<?php echo add_query_arg( 'action', 'wcmb_vendor_coupon_list', $wcmb->ajax_url() ); ?>', 
            type: "post", 
            error: function(xhr, status, error) {
                $("#coupons_table tbody").append('<tr class="odd"><td valign="top" colspan="4" class="dataTables_empty" style="text-align:center;">'+error+' - <a href="javascript:window.location.reload();"><?php _e('Reload', 'MB-multivendor'); ?></a></td></tr>');
                $("#coupons_table_processing").css("display","none");
            }
        },
        createdRow: function (row, data, index) {
            $(row).addClass('vendor-coupon');
        },
        columns: columns
    });
    new $.fn.dataTable.FixedHeader( vendor_coupons );
});
</script>
<?php do_action('after_wcmb_vendor_dashboard_coupon_list_table'); 
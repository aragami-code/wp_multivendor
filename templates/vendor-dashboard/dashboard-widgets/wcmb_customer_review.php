<?php
/*
 
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $WCMb;
$vendor = get_wcmb_vendor();
if (!$vendor) {
    return;
}
$ratings = wcmb_get_vendor_review_info($vendor->term_id);
?>
<div class="row">
    <!-- <div class="col-md-12">
        <?php //echo wc_get_rating_html($ratings['avg_rating']); ?>
    </div> -->
    <div class="col-md-12 wcmb-comments dash-widget-dt">
        <table id="vendor_reviews" class="wcmb-widget-dt table" width="100%">
            <thead>
                <tr><th></th></tr>
            </thead>
            <tbody class="media-list">
              
            </tbody>
        </table>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var vendor_reviews;
    vendor_reviews = $('#vendor_reviews').DataTable({
        ordering  : false,
        lengthChange : false,
        pageLength : 5,
        info:     false,
        searching  : false,
        processing: false,
        serverSide: true,
        responsive: true,
        pagingType: 'numbers',
        language: {
            emptyTable: '<div><?php echo trim(__('No reviews found.', 'MB-multivendor')); ?></div>'
        },
        preDrawCallback: function( settings ) {
            $('#vendor_reviews thead').hide();
            $('.dataTables_paginate').parent().removeClass('col-sm-7').addClass('col-sm-12').siblings('div').hide();
            var info = this.api().page.info();
            if (info.recordsTotal <= 5) {
                $('.dataTables_paginate').parent().parent().hide();
            }else{
                $('.dataTables_paginate').parent().parent().show();
            }
        },
        ajax:{
            url : '<?php echo add_query_arg( 'action', 'wcmb_vendor_dashboard_reviews_data', $WCMb->ajax_url() ); ?>', 
            type: "post",
            error: function(xhr, status, error) {
                $("#vendor_reviews tbody").append('<tr class="odd"><td valign="top" colspan="1" class="dataTables_empty"><div>'+error+' - <a href="javascript:window.location.reload();"><?php _e('Reload', 'MB-multivendor'); ?></div></td></tr>');
                $("#vendor_reviews_processing").css("display","none");
            }
        },
        columns: [{ className: "media" }]

    });
});
</script>
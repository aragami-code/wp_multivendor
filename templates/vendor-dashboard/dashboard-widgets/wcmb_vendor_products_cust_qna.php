<?php

/*
 
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $WCMb;

do_action('before_wcmb_vendor_dashboard_products_cust_qna');
?>
<div class="customer-questions-panel dash-widget-dt">
    <table id="customer_questions" class="wcmb-widget-dt table" width="100%">
        <thead>
            <tr><th></th></tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<script>
jQuery(document).ready(function($) {
    var customer_questions;
    customer_questions = $('#customer_questions').DataTable({
        ordering  : false,
        lengthChange : false,
        pageLength : 5,
        info:     false,
        searching  : false,
        processing: false,
        serverSide: true,
        pagingType: 'numbers',
        language: {
            emptyTable: '<article class="reply-item" style="border-bottom:none;"><div class="col-md-12 col-md-12 col-sm-12 col-xs-12" style="text-align:center;"><?php echo trim(__('No unanswered questions found.', 'MB-multivendor')); ?></div></article>',
        },
        preDrawCallback: function( settings ) {
            $('#customer_questions thead').hide();
            $('.dataTables_paginate').parent().removeClass('col-sm-7').addClass('col-sm-12').siblings('div').hide();
            var info = this.api().page.info();
            if (info.recordsTotal <= 5) {
                $('.dataTables_paginate').parent().parent().hide();
            }else{
                $('.dataTables_paginate').parent().parent().show();
            }
        },
        ajax:{
            url : '<?php echo add_query_arg( 'action', 'wcmb_vendor_dashboard_customer_questions_data', $WCMb->ajax_url() ); ?>', 
            type: "post",
            'error': function(xhr, status, error) {
                $("#customer_questions tbody").append('<tr class="odd"><td valign="top" colspan="1" class="dataTables_empty"><article class="reply-item" style="border-bottom:none;"><div class="col-md-12 col-md-12 col-sm-12 col-xs-12" style="text-align:center;">'+error+' - <a href="javascript:window.location.reload();"><?php _e('Reload', 'MB-multivendor'); ?></a></div></article></td></tr>');
                $("#customer_questions_processing").css("display","none");
            }
        }
    });
});
</script>
<?php
do_action('after_wcmb_vendor_dashboard_products_cust_qna');

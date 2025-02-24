<?php
/*
 
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $WCMb;
$vendor = get_wcmb_vendor(get_current_vendor_id());
do_action('before_wcmb_vendor_dashboard_product_list_table');
?>
<div class="col-md-12 all-products-wrapper">
    <div class="panel panel-default panel-pading">
        <div class="product-list-filter-wrap">
            <div class="form-group">
                <div class="product_filters pull-left">
                    <?php
                    $statuses = apply_filters('wcmb_vendor_dashboard_product_list_filters_status', array(
                        'all' => __('All', 'MB-multivendor'),
                        'publish' => __('Published', 'MB-multivendor'),
                        'pending' => __('Pending', 'MB-multivendor'),
                        'draft' => __('Draft', 'MB-multivendor'),
                        'trash' => __('Trash', 'MB-multivendor')
                    ));
                    $current_status = isset($_GET['post_status']) ? $_GET['post_status'] : 'all';
                    echo '<ul class="subsubsub by_status nav nav-pills category-filter-nav">';
                    //$array_keys = array_keys($statuses);
                    foreach ($statuses as $key => $label) {
                        if($key == 'all'){
                            $count_pros = count($vendor->get_products(array('post_status'=> array('publish', 'pending','draft'))));
                        }else{
                            $count_pros = count($vendor->get_products(array('post_status'=> $key)));
                        }
                        if($count_pros){
                            echo '<li><a href="' . add_query_arg(array('post_status' => sanitize_title($key)), wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_products_endpoint', 'vendor', 'general', 'products'))) . '" class="' . ( $current_status == $key ? 'current' : '' ) . '">' . $label .' ( <span id="count-'.$key.'" data-status="'.$key.'" data-count="'.$count_pros.'">'. $count_pros .'</span> ) </a></li>';
                        }
                    }
                    echo '</ul><br class="clear" />';
                    ?>
                </div>
                <div class="product_search pull-right">
                    <input type="text" class="pro_search_key no_input form-control inline-input" id="pro_search_key" name="search_keyword" />
                    <button class="wcmb_black_btn btn btn-secondary" type="button" id="pro_search_btn"><?php _e('Search', 'MB-multivendor'); ?></button>
                </div>
            </div>
        </div>
        <form method="post" name="wcmb_product_list_form" id="wcmb_product_list_form">
        <div class="product-filter-actions">
            <div class="alignleft actions">
                <?php $pro_bulk_actions = apply_filters( 'wcmb_product_list_bulk_actions', array(
                    'trash' => __('Move to trash', 'MB-multivendor'),
                    'untrash' => __('Restore', 'MB-multivendor'),
                    'delete' => __('Delete Permanently', 'MB-multivendor'),
                ));
                // Filter bulk actions according to post status
                if(isset($_REQUEST['post_status']) && $_REQUEST['post_status'] == 'trash'){
                    if(isset($pro_bulk_actions['trash'])) unset($pro_bulk_actions['trash']);
                }else{
                    if(isset($pro_bulk_actions['untrash'])) { 
                        unset($pro_bulk_actions['untrash']);
                        unset($pro_bulk_actions['delete']);
                    }
                }
                ?>
                <select id="product_bulk_actions" name="bulk_action" class="wcmb-filter-dtdd wcmb_product_bulk_actions form-control inline-input">
                    <option value=""><?php _e('Bulk Actions', 'MB-multivendor'); ?></option>
                    <?php 
                    if($pro_bulk_actions) :
                        foreach ($pro_bulk_actions as $key => $label) {
                            echo '<option value="'.$key.'">'.$label.'</option>';
                        }
                    endif;
                    ?>
                </select>
                <button class="wcmb_black_btn btn btn-secondary" type="button" id="product_list_do_bulk_action"><?php _e('Apply', 'MB-multivendor'); ?></button>
                <select id="product_cat" name="product_cat" class="wcmb-filter-dtdd wcmb_filter_product_cat form-control inline-input">
                    <option value=""><?php _e('Select a Category', 'MB-multivendor'); ?></option>
                    <?php 
                    $product_taxonomy_terms = get_terms('product_cat', 'orderby=name&hide_empty=0&parent=0');
                    if ($product_taxonomy_terms) {
                        WCMbGenerateTaxonomyHTML('product_cat', $product_taxonomy_terms, array());
                    }
                    ?>
                </select>
                <select id="product_types" name="product_type" class="wcmb-filter-dtdd wcmb_filter_product_types form-control inline-input">
                    <option value=""><?php _e('Filter by product type', 'MB-multivendor'); ?></option>
                    <?php 
                    $product_types = wcmb_get_available_product_types();
                    if($product_types) :
                        foreach ($product_types as $key => $label) {
                            if(in_array($key, array( 'virtual', 'downloadable'))) continue;
                            echo '<option value="'.$key.'">'.$label.'</option>';
                            if ( 'simple' === $key ) {
                                if(array_key_exists('downloadable', $product_types))
                                        echo '<option value="downloadable">' . ( is_rtl() ? '&larr;' : '&rarr;' ) . ' ' . $product_types['downloadable'].'</option>';
                                if(array_key_exists('virtual', $product_types))
                                        echo '<option value="virtual">' . ( is_rtl() ? '&larr;' : '&rarr;' ) . ' ' . $product_types['virtual'].'</option>';
                            }
                        }
                    endif;
                    ?>
                </select>
                <?php do_action( 'wcmb_products_list_add_extra_filters' ); ?>
                <button class="wcmb_black_btn btn btn-secondary" type="button" id="product_list_do_filter"><?php _e('Filter', 'MB-multivendor'); ?></button>
            </div>
        </div>
            
        <table id="product_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead><tr>
            <?php
            if ($products_table_headers) {
                foreach ($products_table_headers as $key => $value) {
                    if($key == 'select_product'){ ?>
                        <th class="text-center" data-lable="<?php echo $key ?>"><input type="checkbox" class="select_all_all" onchange="toggleAllCheckBox(this, 'product_table');" /></th>
                    <?php }else{ ?>
                        <th data-lable="<?php echo $key ?>"><?php echo $value ?></th>
                    <?php }
                }
            }
            ?>
            </tr></thead>
        </table>
        <div class="wcmb-action-container">
            <?php do_action('before_wcmb_vendor_dash_product_list_page_header_action_btn'); ?>
            <a href="<?php echo wcmb_get_vendor_dashboard_endpoint_url(get_wcmb_vendor_settings('wcmb_add_product_endpoint', 'vendor', 'general', 'add-product'));?>" class="btn btn-default"><i class="wcmb-font ico-add-booking"></i><?php echo __('Add Product', 'MB-multivendor');?></a>
            <?php do_action('after_wcmb_vendor_dash_product_list_page_header_action_btn'); ?>
        </div>
        </form>
    </div>
</div>
<?php do_action('after_wcmb_vendor_dashboard_product_list_table'); ?>
<script>
    jQuery(document).ready(function ($) { 
        var product_table;
        var columns = [];
        //var filter_by_category_list = [];
<?php
if ($products_table_headers) {
    $enable_ordering = apply_filters('wcmb_vendor_dashboard_product_list_table_orderable_columns', array('name', 'date'));
    foreach ($products_table_headers as $key => $value) {
        $orderable = 'false';
        if (in_array($key, $enable_ordering)) {
            $orderable = 'true';
        }
        if($key == 'select_product') $orderable = 'false';
        ?>
                obj = {};
                obj['data'] = '<?php echo $key; ?>';
                obj['className'] = '<?php echo $key; ?>';
                obj['orderable'] = <?php echo $orderable; ?>;
                columns.push(obj);
    <?php
    }
}

?>
        product_table = $('#product_table').DataTable({
            'ordering': <?php echo isset($table_init['ordering']) ? trim($table_init['ordering']) : 'true'; ?>,
            'searching': <?php echo isset($table_init['searching']) ? trim($table_init['searching']) : 'true'; ?>,
            "processing": true,
            "serverSide": true,
            "lengthChange": false,
            "responsive": true,
            "language": {
                "emptyTable": "<?php echo isset($table_init['emptyTable']) ? trim($table_init['emptyTable']) : __('No products found!', 'MB-multivendor'); ?>",
                "processing": "<?php echo isset($table_init['processing']) ? trim($table_init['processing']) : __('Processing...', 'MB-multivendor'); ?>",
                "info": "<?php echo isset($table_init['info']) ? trim($table_init['info']) : __('Showing _START_ to _END_ of _TOTAL_ products', 'MB-multivendor'); ?>",
                "infoEmpty": "<?php echo isset($table_init['infoEmpty']) ? trim($table_init['infoEmpty']) : __('Showing 0 to 0 of 0 products', 'MB-multivendor'); ?>",
                "lengthMenu": "<?php echo isset($table_init['lengthMenu']) ? trim($table_init['lengthMenu']) : __('Number of rows _MENU_', 'MB-multivendor'); ?>",
                "zeroRecords": "<?php echo isset($table_init['zeroRecords']) ? trim($table_init['zeroRecords']) : __('No matching products found', 'MB-multivendor'); ?>",
                "search": "<?php echo isset($table_init['search']) ? trim($table_init['search']) : __('Search:', 'MB-multivendor'); ?>",
                "paginate": {
                    "next": "<?php echo isset($table_init['next']) ? trim($table_init['next']) : __('Next', 'MB-multivendor'); ?>",
                    "previous": "<?php echo isset($table_init['previous']) ? trim($table_init['previous']) : __('Previous', 'MB-multivendor'); ?>"
                },
            },
            "drawCallback": function(settings){
                //$( "#product_cat" ).detach();
                $('thead tr th.select_product').removeClass('sorting_asc');
                $('thead tr th.image').removeClass('sorting_asc');
//                var product_cat_sel = $('<select id="product_cat" class="wcmb-filter-dtdd wcmb_filter_product_cat form-control">').appendTo("#product_table_length");
//                product_cat_sel.append($("<option>").attr('value', '').text('<?php echo trim(__('Select a Category', 'MB-multivendor')); ?>'));
//                $(filter_by_category_list).each(function () {
//                    product_cat_sel.append($("<option>").attr('value', this.key).text(this.label));
//                });
//                if(settings.oAjaxData.product_cat){
//                    product_cat_sel.val(settings.oAjaxData.product_cat);
//                }
                if(settings.json.notices.length > 0 ){
                    $('.wcmb-wrapper .notice-wrapper').html('');
                    $.each(settings.json.notices, function( index, notice ) {
                        if(notice.type == 'success'){
                            $('.wcmb-wrapper .notice-wrapper').append('<div class="woocommerce-message" role="alert">'+notice.message+'</div>');
                        }else{
                            $('.wcmb-wrapper .notice-wrapper').append('<div class="woocommerce-error" role="alert">'+notice.message+'</div>');
                        }
                    });
                }
            },
            "ajax": {
                url: '<?php echo add_query_arg( 'action', 'wcmb_vendor_product_list', $WCMb->ajax_url() ); ?>',
                type: "post",
                data: function (data) {
                    data.products_filter_action = $('form#wcmb_product_list_form').serialize();
                    data.post_status = "<?php echo isset($_GET['post_status']) ? trim($_GET['post_status']) : 'all' ?>";
                    data.product_cat = $('#product_cat').val();
                    data.bulk_action = $('#product_bulk_actions').val();
                    data.search_keyword = $('#pro_search_key').val();
                },
                error: function(xhr, status, error) {
                    $("#product_table tbody").append('<tr class="odd"><td valign="top" colspan="<?php echo count($products_table_headers); ?>" class="dataTables_empty" style="text-align:center;">'+error+' - <a href="javascript:window.location.reload();"><?php _e('Reload', 'MB-multivendor'); ?></a></td></tr>');
                    $("#product_table_processing").css("display","none");
                }
            },
            "columns": columns,
            "createdRow": function (row, data, index) {
                $(row).addClass('vendor-product');
            }
        });
        new $.fn.dataTable.FixedHeader( product_table );
//        $(document).on('change', '#product_cat', function () {
//            product_table.ajax.reload();
//        });
        $(document).on('click', '#pro_search_btn', function () {
            product_table.ajax.reload();
        });
        $(document).on('click', '#product_list_do_filter', function (e) {
            product_table.ajax.reload();
        });
        $(document).on('click', '#product_list_do_bulk_action', function (e) {
            product_table.ajax.reload();
        });
    });
</script>
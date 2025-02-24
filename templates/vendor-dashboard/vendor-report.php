<?php
/**

 */
global $WCMb;
?>
<div class="col-md-12">
    
    <div class="panel panel-default panel-pading">
        <form name="wcmb_vendor_dashboard_stat_report" method="POST" class="stat-date-range form-inline">
            <div class="wcmb_form1 ">
                <div class="panel-heading">
                    <h3><?php _e('Select Date Range :', 'MB-multivendor'); ?></h3> 
                    <div class="form-group">
                        <span class="date-inp-wrap">
                            <input type="text" name="wcmb_stat_start_dt" value="<?php echo isset($_POST['wcmb_stat_start_dt']) ? $_POST['wcmb_stat_start_dt'] : date('Y-m-01'); ?>" class="pickdate gap1 wcmb_stat_start_dt form-control">
                        </span> 
                        <!-- <span class="to-text">-</span> -->
                    </div>
                    <div class="form-group">
                        <span class="date-inp-wrap">
                        <input type="text" name="wcmb_stat_end_dt" value="<?php echo isset($_POST['wcmb_stat_end_dt']) ? $_POST['wcmb_stat_end_dt'] : date('Y-m-d'); ?>" class="pickdate wcmb_stat_end_dt form-control">
                        </span>
                    </div>
                    <div class="form-group">
                        <button name="submit_button" type="submit" value="Show" class="wcmb_black_btn btn btn-default"><?php _e('Show', 'MB-multivendor'); ?></button>
                    </div> 
                    <?php if (apply_filters('can_wcmb_vendor_export_orders_csv', true, get_current_vendor_id())) : ?>
                    <div class="form-group">
                        <button type="submit" class="wcmb_black_btn btn btn-default" name="wcmb_stat_export" value="export"><?php _e('Download CSV', 'MB-multivendor'); ?></button>
                    </div> 
                    <?php endif; ?>
                </div>
                <div class="panel-body">
                    <div class="wcmb_ass_holder_box">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="wcmb_displaybox2 text-center">
                                    <h4><?php _e('Total Sales', 'MB-multivendor'); ?></h4>
                                    <h3><?php echo wc_price($total_vendor_sales); ?></h3>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="wcmb_displaybox2 text-center">
                                    <h4><?php _e('My Earnings', 'MB-multivendor'); ?></h4>
                                    <h3><?php echo wc_price($total_vendor_earning); ?></h3>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="wcmb_displaybox2 text-center">
                                    <h4><?php _e('Total number of Order placed', 'MB-multivendor'); ?></h4>
                                    <h3><?php echo $total_order_count; ?></h3>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="wcmb_displaybox2 text-center">
                                    <h4><?php _e('Purchased Products', 'MB-multivendor'); ?></h4>
                                    <h3><?php echo $total_purchased_products; ?></h3>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="wcmb_displaybox2 text-center">
                                    <h4><?php _e('Number of Coupons used', 'MB-multivendor'); ?></h4>
                                    <h3><?php echo $total_coupon_used; ?></h3>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="wcmb_displaybox2 text-center">
                                    <h4><?php _e('Total Coupon Discount', 'MB-multivendor'); ?></h4>
                                    <h3><?php echo wc_price($total_coupon_discount_value); ?></h3>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="wcmb_displaybox2 text-center">
                                    <h4><?php _e('Number of Unique Customers', 'MB-multivendor'); ?></h4>
                                    <h3><?php echo count($total_customers); ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

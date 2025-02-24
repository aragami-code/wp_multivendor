<?php

/*

 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $WCMb;

do_action('before_wcmb_vendor_stats_reports'); 
?>
<div class="pannel panel-default pannel-outer-heading staticstics-panel-wrap">
    <div class="panel-body">
        <h2><i class="wcmb-font ico-report-icon"></i> <?php printf( __( 'Your Store Report - %s', 'MB-multivendor' ), '<span class="_wcmb_stats_period"></span>' );?></h2>
        <div class="row">
            <div class="col-md-4 key-perfomence-indicator">
                <h2><?php _e('Key Performance Indicators', 'MB-multivendor'); ?></h2>
                <ul class="short-stat-info-list">
                    <li>
                        <span class="stat-icon" title="<?php _e('Traffic', 'MB-multivendor'); ?>"><i class="wcmb-font ico-visit-icon"></i></span>
                        <span class="_wcmb_stats_table current_traffic_no current-stat-report"></span>
                        <span class="_wcmb_stats_table previous_traffic_no prev-stat-report"></span>
                    </li>
                    <li>
                        <span class="stat-icon" title="<?php _e('Order No', 'MB-multivendor'); ?>"><i class="wcmb-font ico-cart-icon"></i></span>
                        <span class="_wcmb_stats_table current_orders_no current-stat-report"></span>
                        <span class="_wcmb_stats_table previous_orders_no prev-stat-report"></span>
                    </li>
                    <li>
                        <span class="stat-icon" title="<?php _e('Sales', 'MB-multivendor'); ?>"><i class="wcmb-font ico-price2-icon"></i></span>
                        <span class="_wcmb_stats_table current_sales_total current-stat-report"></span>
                        <span class="_wcmb_stats_table previous_sales_total prev-stat-report"></span>
                    </li>
                </ul>
                <ul class="short-stat-info-list">
                    <li>
                        <span class="stat-icon" title="<?php _e('Earning', 'MB-multivendor'); ?>"><i class="wcmb-font ico-earning-icon"></i></span>
                        <span class="_wcmb_stats_table current_earning current-stat-report"></span>
                        <span class="_wcmb_stats_table previous_earning prev-stat-report"></span>
                    </li>
                    <li>
                        <span class="stat-icon" title="<?php _e('Withdrawal', 'MB-multivendor'); ?>"><i class="wcmb-font ico-revenue-icon"></i></span>
                        <span class="_wcmb_stats_table current_withdrawal current-stat-report"></span>
                        <span class="_wcmb_stats_table previous_withdrawal prev-stat-report"></span>
                    </li>
                </ul>
            </div>
            <div class="col-md-8">
                <h2><?php _e('Store Insights', 'MB-multivendor'); ?></h2>
                <p class="stat-detail-info"><span><i class="wcmb-font ico-avarage-order-value-icon"></i></span> <?php printf( __( 'Your average order value %1$s for this span was %2$s', 'MB-multivendor' ), '<strong>(AOV)</strong>', '<span class="_wcmb_stats_aov stats-aov"></span>'); ?> </p>
                <p class="stat-detail-info"><span><i class="wcmb-font ico-revenue-icon"></i></span> <?php printf( __( 'During this span, %1$s has been credited to your %2$s account, as commission.', 'MB-multivendor' ), '<mark class="_wcmb_stats_table current_withdrawal withdrawal-label mark-green"></mark>', $payment_mode); ?></p>
                <div class="compare-stat-info">
                    <span><b><?php _e('Compare your store performance against', 'MB-multivendor'); ?></b></span>
                    <select name="" id="wcmb_vendor_stats_report_filter" class="form-control" data-stats="<?php echo htmlspecialchars(wp_json_encode($vendor_report_data)); ?>">
                        <?php 
                        if($stats_reports_periods){
                            foreach ($stats_reports_periods as $key => $value) {
                                echo '<option value="'.$key.'">'.$value.'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <ul class="wcmb-website-stat-list">
                    <li>
                        <span><i class="wcmb-font ico-visit-icon"></i></span>
                        <span><?php _e('Store traffic', 'MB-multivendor'); ?> <mark id="stats-diff-traffic" class="_wcmb_diff_traffic_no "></mark></span>
                    </li>
                    <li>
                        <span><i class="wcmb-font ico-cart-icon"></i></span>
                        <span><?php _e('Received orders', 'MB-multivendor'); ?> <mark id="stats-diff-order-no" class="_wcmb_diff_orders_no "></mark></span>
                    </li> 
                    <li>
                        <span><i class="wcmb-font ico-price2-icon"></i></span>
                        <span><?php _e('Total sales', 'MB-multivendor'); ?> <mark id="stats-diff-sales-total" class="_wcmb_diff_sales_total "></mark></span>
                    </li>
                    
                    <li>
                        <span><i class="wcmb-font ico-earning-icon"></i></span>
                        <span><?php _e('Your earning', 'MB-multivendor'); ?> <mark id="stats-diff-earning" class="_wcmb_diff_earning "></mark></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php
do_action('after_wcmb_vendor_stats_reports');
